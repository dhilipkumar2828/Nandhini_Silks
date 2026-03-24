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
        $products = Product::with(['category', 'product_variants'])->latest()->paginate(10);
        return view('admin.stock.index', compact('products'));
    }

    public function showLogs(Product $product)
    {
        $logs = $product->stockMovements()->latest()->paginate(10);
        return view('admin.stock.logs', compact('product', 'logs'));
    }

    public function updateBulk(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Update Main Products
            foreach ($request->input('stock', []) as $productId => $stockData) {
                $product = Product::find($productId);
                if ($product) {
                    $oldQty = $product->stock_quantity;
                    $newQty = $stockData['quantity'];
                    if ($oldQty != $newQty) {
                        $product->update([
                            'stock_quantity' => $newQty,
                            'reserved_stock' => $stockData['reserved_stock'] ?? $product->reserved_stock,
                            'low_stock_threshold' => $stockData['low_stock_threshold'] ?? $product->low_stock_threshold,
                            'restock_quantity' => $stockData['restock_quantity'] ?? $product->restock_quantity,
                            'restock_date' => $stockData['restock_date'] ?? $product->restock_date,
                            'supplier' => $stockData['supplier'] ?? $product->supplier,
                            'stock_status' => $newQty > ($stockData['low_stock_threshold'] ?? $product->low_stock_threshold) ? 'instock' : ($newQty > 0 ? 'lowstock' : 'outofstock'),
                        ]);
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => $newQty > $oldQty ? 'in' : 'adjustment',
                            'quantity' => abs($newQty - $oldQty),
                            'balance_after' => $newQty,
                            'reason' => 'Admin Manual Bulk Update (Main)',
                        ]);
                    } else {
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

            // Update Variants
            foreach ($request->input('variants', []) as $variantId => $vStockData) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if ($variant) {
                    $oldVQty = $variant->stock_quantity;
                    $newVQty = $vStockData['quantity'];
                    if ($oldVQty != $newVQty) {
                        $variant->update(['stock_quantity' => $newVQty]);
                        StockMovement::create([
                            'product_id' => $variant->product_id,
                            'type' => $newVQty > $oldVQty ? 'in' : 'adjustment',
                            'quantity' => abs($newVQty - $oldVQty),
                            'balance_after' => $newVQty,
                            'reason' => 'Admin Manual Bulk Update (Variant ' . $variant->sku . ')',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.stock.index')->with('success', 'Inventory updated and movements logged.');
    }
}
