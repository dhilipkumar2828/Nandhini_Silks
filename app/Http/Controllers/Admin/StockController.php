<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.stock.index', compact('products'));
    }

    public function showLogs(Product $product)
    {
        $logs = $product->stockMovements()->latest()->get();
        return view('admin.stock.logs', compact('product', 'logs'));
    }

    public function updateBulk(Request $request)
    {
        $stocks = $request->input('stock', []);
        
        DB::transaction(function () use ($stocks) {
            foreach ($stocks as $productId => $stockData) {
                $product = Product::find($productId);
                if ($product) {
                    $oldQuantity = $product->stock_quantity;
                    $newQuantity = $stockData['quantity'];
                    
                    if ($oldQuantity != $newQuantity) {
                        $product->update([
                            'stock_quantity' => $newQuantity,
                            'reserved_stock' => $stockData['reserved_stock'] ?? $product->reserved_stock,
                            'low_stock_threshold' => $stockData['low_stock_threshold'] ?? $product->low_stock_threshold,
                            'restock_quantity' => $stockData['restock_quantity'] ?? $product->restock_quantity,
                            'restock_date' => $stockData['restock_date'] ?? $product->restock_date,
                            'supplier' => $stockData['supplier'] ?? $product->supplier,
                            'stock_status' => $newQuantity > ($stockData['low_stock_threshold'] ?? $product->low_stock_threshold) ? 'instock' : ($newQuantity > 0 ? 'lowstock' : 'outofstock'),
                        ]);

                        // Log movement
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => $newQuantity > $oldQuantity ? 'in' : 'adjustment',
                            'quantity' => abs($newQuantity - $oldQuantity),
                            'balance_after' => $newQuantity,
                            'reason' => 'Admin Manual Bulk Update',
                        ]);
                    } else {
                        // Just update non-quantity fields if they changed
                         $product->update([
                            'reserved_stock' => $stockData['reserved_stock'] ?? $product->reserved_stock,
                            'low_stock_threshold' => $stockData['low_stock_threshold'] ?? $product->low_stock_threshold,
                            'restock_quantity' => $stockData['restock_quantity'] ?? $product->restock_quantity,
                            'restock_date' => $stockData['restock_date'] ?? $product->restock_date,
                            'supplier' => $stockData['supplier'] ?? $product->supplier,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.stock.index')->with('success', 'Inventory updated and movements logged.');
    }
}
