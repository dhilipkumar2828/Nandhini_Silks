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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_city')->nullable()->after('delivery_address');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_pincode')->nullable()->after('shipping_state');
            $table->string('shipping_country')->default('India')->after('shipping_pincode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country']);
        });
    }
};
