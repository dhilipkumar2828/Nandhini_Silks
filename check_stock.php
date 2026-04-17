<?php
use App\Models\Product;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = Product::where('slug', 'kalini')->first();
if ($p) {
    echo "Product: {$p->name} Stock: {$p->stock_quantity}\n";
    foreach ($p->product_variants as $v) {
        echo "Variant SKU: {$v->sku} Color: {$v->color} Size: {$v->size} Stock: {$v->stock_quantity}\n";
    }
} else {
    echo "Product not found.\n";
}
