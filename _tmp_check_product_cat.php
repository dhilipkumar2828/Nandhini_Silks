<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \App\Models\Product::find(16);
if ($product) {
    echo "Product ID: " . $product->id . "\n";
    echo "Category ID: " . ($product->category_id ?? 'NULL') . "\n";
    echo "Sub Category ID: " . ($product->sub_category_id ?? 'NULL') . "\n";
    echo "Child Category ID: " . ($product->child_category_id ?? 'NULL') . "\n";
} else {
    echo "Product not found\n";
}
