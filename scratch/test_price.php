<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Http\Controllers\CartController;

$product = Product::find(26); // saree 1
$controller = new CartController();

// Use reflection to call private productPrice
$reflection = new \ReflectionClass($controller);
$method = $reflection->getMethod('productPrice');
$method->setAccessible(true);
$price = $method->invoke($controller, $product);

echo "Product: {$product->name}\n";
echo "Base Price: {$product->price}\n";
echo "Calculated Inclusive Price: {$price}\n";
