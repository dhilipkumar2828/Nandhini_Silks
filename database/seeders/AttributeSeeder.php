<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Color Attribute
        $colorAttr = Attribute::updateOrCreate(
            ['slug' => 'color'],
            [
                'name' => 'Color',
                'group' => 'Visual',
                'status' => true
            ]
        );

        $colors = [
            ['name' => 'Red', 'swatch' => '#FF0000'],
            ['name' => 'Blue', 'swatch' => '#0000FF'],
            ['name' => 'Green', 'swatch' => '#008000'],
            ['name' => 'Black', 'swatch' => '#000000'],
            ['name' => 'Gold', 'swatch' => '#D4AF37'],
            ['name' => 'Maroon', 'swatch' => '#800000'],
        ];

        $colorValueIds = [];
        foreach ($colors as $index => $color) {
            $val = AttributeValue::updateOrCreate(
                ['attribute_id' => $colorAttr->id, 'slug' => Str::slug($color['name'])],
                [
                    'name' => $color['name'],
                    'swatch_value' => $color['swatch'],
                    'display_order' => $index,
                    'status' => true
                ]
            );
            $colorValueIds[] = $val->id;
        }

        // 2. Create Size Attribute
        $sizeAttr = Attribute::updateOrCreate(
            ['slug' => 'size'],
            [
                'name' => 'Size',
                'group' => 'Standard',
                'status' => true
            ]
        );

        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '6 Yards', '5.5 Yards + 0.8 Blouse'];

        $sizeValueIds = [];
        foreach ($sizes as $index => $size) {
            $val = AttributeValue::updateOrCreate(
                ['attribute_id' => $sizeAttr->id, 'slug' => Str::slug($size)],
                [
                    'name' => $size,
                    'display_order' => $index,
                    'status' => true
                ]
            );
            $sizeValueIds[] = $val->id;
        }

        // 3. Assign random attributes to existing products
        $products = Product::all();
        foreach ($products as $product) {
            // Pick a few random colors and a random size
            $randomColors = collect($colorValueIds)->random(rand(2, 4))->toArray();
            $randomSizes = collect($sizeValueIds)->random(rand(1, 3))->toArray();
            
            // Format: [attribute_id => [value_id, ...]]
            $productAttributes = [
                $colorAttr->id => $randomColors,
                $sizeAttr->id => $randomSizes
            ];
            
            $product->update([
                'attributes' => $productAttributes
            ]);
        }
    }
}
