<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Category relations
            $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
            $table->foreignId('child_category_id')->nullable()->after('sub_category_id')->constrained()->onDelete('set null');

            // Basic Info
            $table->string('sku')->nullable()->after('slug');
            $table->string('barcode')->nullable()->after('sku');
            $table->string('brand')->nullable()->after('barcode');
            $table->text('short_description')->nullable()->after('brand');
            $table->longText('full_description')->nullable()->after('short_description');
            
            // Media
            $table->json('images')->nullable()->after('full_description');
            $table->string('video_url')->nullable()->after('images');

            // Pricing
            $table->decimal('regular_price', 15, 2)->default(0)->after('video_url');
            $table->decimal('sale_price', 15, 2)->nullable()->after('regular_price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('sale_price');
            $table->string('tax_class')->nullable()->after('discount_percent');

            // Inventory
            $table->integer('stock_quantity')->default(0)->after('tax_class');
            $table->integer('low_stock_threshold')->default(10)->after('stock_quantity');
            $table->string('stock_status')->default('instock')->after('low_stock_threshold');

            // Shipping
            $table->decimal('weight', 10, 2)->nullable()->after('stock_status');
            $table->string('dimensions')->nullable()->after('weight'); // L x W x H
            $table->string('shipping_class')->nullable()->after('dimensions');

            // Variants
            $table->json('attributes')->nullable()->after('shipping_class');
            $table->json('variants')->nullable()->after('attributes');

            // Misc
            $table->text('related_products')->nullable()->after('variants');
            $table->text('tags')->nullable()->after('related_products');

            // SEO
            $table->string('meta_title')->nullable()->after('tags');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');

            // Status
            $table->boolean('status')->default(true)->after('is_featured');
            
            // Cleanup old fields if necessary, but I'll keep them for now to avoid breaking existing code
            // Actually, I'll drop 'description' and 'price' and 'image_path' if they are redundant
            // I'll keep them for safety or drop them in down()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
