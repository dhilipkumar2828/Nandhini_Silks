<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shiprocket_courier_id')) {
                $table->unsignedInteger('shiprocket_courier_id')->nullable()->after('shiprocket_awb');
            }
            if (!Schema::hasColumn('orders', 'shiprocket_courier_name')) {
                $table->string('shiprocket_courier_name')->nullable()->after('shiprocket_courier_id');
            }
            if (!Schema::hasColumn('orders', 'shiprocket_label_url')) {
                $table->string('shiprocket_label_url')->nullable()->after('shiprocket_courier_name');
            }
            if (!Schema::hasColumn('orders', 'shiprocket_manifest_url')) {
                $table->string('shiprocket_manifest_url')->nullable()->after('shiprocket_label_url');
            }
            if (!Schema::hasColumn('orders', 'shiprocket_invoice_url')) {
                $table->string('shiprocket_invoice_url')->nullable()->after('shiprocket_manifest_url');
            }
            if (!Schema::hasColumn('orders', 'pickup_scheduled_at')) {
                $table->timestamp('pickup_scheduled_at')->nullable()->after('shiprocket_invoice_url');
            }
            if (!Schema::hasColumn('orders', 'shiprocket_webhook_status')) {
                $table->string('shiprocket_webhook_status')->nullable()->after('pickup_scheduled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shiprocket_courier_id',
                'shiprocket_courier_name',
                'shiprocket_label_url',
                'shiprocket_manifest_url',
                'shiprocket_invoice_url',
                'pickup_scheduled_at',
                'shiprocket_webhook_status',
            ]);
        });
    }
};
