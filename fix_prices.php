<?php

use App\Models\Product;
use App\Models\ProductVariant;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Price Cleanup...\n";

// 1. Fix Products
$products = Product::all();
$fixedProducts = 0;
foreach ($products as $product) {
    $priceUpdated = false;
    
    // If price is 0, it's definitely wrong
    if (floatval($product->price) <= 0) {
        $product->price = (floatval($product->sale_price) > 0) ? $product->sale_price : ($product->regular_price ?: 0);
        $priceUpdated = true;
    }
    
    // If sale_price is 0 but price is still 0 (fallback failed)
    if (floatval($product->sale_price) <= 0 && $product->sale_price !== null) {
        $product->sale_price = null;
        // Re-check price
        $product->price = $product->regular_price ?: $product->price;
        $priceUpdated = true;
    }

    if ($priceUpdated) {
        $product->save();
        $fixedProducts++;
    }
}

echo "Fixed $fixedProducts Products.\n";

// 2. Fix Variants
$variants = ProductVariant::all();
$fixedVariants = 0;
foreach ($variants as $variant) {
    $variantUpdated = false;
    
    // If sale_price is 0, make it null
    if (floatval($variant->sale_price) <= 0 && $variant->sale_price !== null) {
        $variant->sale_price = null;
        $variantUpdated = true;
    }
    
    // If variant price is somehow 0 (shouldn't be, but let's check)
    // Actually, variants usually have price (which is regular) and sale_price.
    // Let's ensure if they both are 0, we can't do much, but we mainly want to fix the "0 sale price" bug.

    if ($variantUpdated) {
        $variant->save();
        $fixedVariants++;
    }
}

echo "Fixed $fixedVariants Variants.\n";

echo "Done.\n";
