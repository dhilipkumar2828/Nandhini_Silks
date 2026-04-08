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
        // 1. Drop the legacy shipping tables
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_classes');

        // 2. Remove shipping_class_id from products
        if (Schema::hasColumn('products', 'shipping_class_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('shipping_class_id');
            });
        }

        if (Schema::hasColumn('products', 'shipping_class')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('shipping_class');
            });
        }

        // 3. Remove shipping_class_id from product_variants
        if (Schema::hasTable('product_variants')) {
            if (Schema::hasColumn('product_variants', 'shipping_class_id')) {
                Schema::table('product_variants', function (Blueprint $table) {
                    $table->dropColumn('shipping_class_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal since we are purging legacy logic.
    }
};
