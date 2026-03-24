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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('combination'); // Red|XL
            $table->json('attribute_values'); // [1, 5] (IDs)
            $table->string('sku')->nullable()->unique();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
