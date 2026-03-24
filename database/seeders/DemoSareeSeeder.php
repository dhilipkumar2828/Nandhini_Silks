<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class DemoSareeSeeder extends Seeder
{
    public function run()
    {
        $p = Product::create([
            'name' => 'Nandhini Demo Saree',
            'slug' => 'nandhini-demo-saree',
            'sku' => 'DEMO' . time(),
            'category_id' => 2,
            'regular_price' => 2500,
            'sale_price' => 1999,
            'price' => 1999,
            'stock_quantity' => 50,
            'status' => '1',
            'attributes' => [1 => [1]],
            'image' => 'products/1773999321_69bd14d92b307.jpg',
            'primary_image' => 'products/1773999321_69bd14d92b307.jpg'
        ]);

        ProductVariant::create([
            'product_id' => $p->id,
            'combination' => ["1" => [1]],
            'attribute_values' => ['red' => 's'],
            'price' => 1999,
            'sale_price' => 1999,
            'stock_quantity' => 10,
            'sku' => 'DEMO-V1',
            'image' => 'products/1773999771_69bd169b2630a.jpg',
            'status' => 1
        ]);
    }
}
