<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::with('taxClass.rates')->get();

foreach ($products as $p) {
    echo "Product: {$p->name} (ID: {$p->id})\n";
    echo "  Price: {$p->price}\n";
    if ($p->taxClass) {
        echo "  Tax Class: {$p->taxClass->name}\n";
        foreach ($p->taxClass->rates as $r) {
            echo "    Rate: {$r->rate}% (Status: {$r->status})\n";
        }
    } else {
        echo "  No Tax Class\n";
    }
    echo "-------------------\n";
}
