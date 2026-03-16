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
            $table->integer('reserved_stock')->default(0)->after('stock_quantity');
            $table->integer('restock_quantity')->nullable()->after('reserved_stock');
            $table->date('restock_date')->nullable()->after('restock_quantity');
            $table->string('supplier')->nullable()->after('restock_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['reserved_stock', 'restock_quantity', 'restock_date', 'supplier']);
        });
    }
};
