<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::all();
foreach($products as $product) {
    if($product->product_variants->count() > 0) {
        $totalStock = $product->product_variants->sum('stock_quantity');
        $product->update(['stock_quantity' => $totalStock]);
        echo "Product ID {$product->id} - Updated stock to $totalStock\n";
    }
}
echo "Sync complete.\n";
