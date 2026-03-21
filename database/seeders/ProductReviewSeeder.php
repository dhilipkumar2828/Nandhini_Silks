<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('email', 'dk@gmail.com')->first();
        if (!$user) {
            $user = \App\Models\User::first();
        }
        
        if (!$user) return;

        $products = \App\Models\Product::limit(5)->get();

        foreach($products as $index => $product) {
            \App\Models\ProductReview::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'stars' => rand(4, 5),
                'review' => "Great product! Highly recommend. Quality is excellent.",
                'status' => $index < 2 ? 1 : 0 // 2 published, rest pending
            ]);
        }
    }
}
