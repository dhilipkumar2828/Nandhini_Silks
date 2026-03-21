<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add applicable_on to coupons
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'applicable_on')) {
                $table->enum('applicable_on', ['all', 'category', 'product'])->default('all')->after('max_discount');
            }
        });

        // Add order_number to orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable()->unique()->after('id');
            }
        });

        // Fix related_products column type in products (text -> json)
        // Only if it's currently a text column
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'related_products')) {
                // Change from text to json - safe since values are either null or valid JSON arrays
                $table->json('related_products')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('applicable_on');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('related_products')->nullable()->change();
        });
    }
};
