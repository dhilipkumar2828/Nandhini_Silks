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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('payment_method')->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('order_status', ['pending', 'processing', 'dispatched', 'delivered', 'cancelled'])->default('pending');
            $table->text('delivery_address');
            $table->text('admin_notes')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
