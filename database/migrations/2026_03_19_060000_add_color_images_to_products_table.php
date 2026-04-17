<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add color_images JSON column to products table.
     * Stores: { "color_value_id": ["products/img1.jpg", "products/img2.jpg"], ... }
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('color_images')->nullable()->after('images');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('color_images');
        });
    }
};
