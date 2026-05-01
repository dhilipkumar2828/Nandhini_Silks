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
        Schema::table('orders', function (Blueprint $col) {
            $col->decimal('package_length', 8, 2)->nullable();
            $col->decimal('package_breadth', 8, 2)->nullable();
            $col->decimal('package_height', 8, 2)->nullable();
            $col->decimal('package_weight', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $col) {
            $col->dropColumn(['package_length', 'package_breadth', 'package_height', 'package_weight']);
        });
    }
};
