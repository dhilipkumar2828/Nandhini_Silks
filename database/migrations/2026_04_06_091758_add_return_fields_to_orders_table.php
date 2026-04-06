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
            $table->string('return_status')->nullable()->after('order_status');
            $table->text('return_reason')->nullable()->after('return_status');
            $table->text('return_admin_notes')->nullable()->after('return_reason');
            $table->string('reverse_awb')->nullable()->after('return_admin_notes');
            $table->string('shiprocket_return_order_id')->nullable()->after('reverse_awb');
            $table->string('shiprocket_return_shipment_id')->nullable()->after('shiprocket_return_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'return_status',
                'return_reason',
                'return_admin_notes',
                'reverse_awb',
                'shiprocket_return_order_id',
                'shiprocket_return_shipment_id'
            ]);
        });
    }
};
