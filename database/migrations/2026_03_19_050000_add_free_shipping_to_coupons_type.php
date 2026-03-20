<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Extends the coupon type enum to include 'free_shipping' option.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'fixed', 'free_shipping') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'fixed') NOT NULL");
    }
};
