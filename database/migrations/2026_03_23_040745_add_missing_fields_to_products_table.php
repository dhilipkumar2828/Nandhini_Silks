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
            if (!Schema::hasColumn('products', 'isbn')) {
                $table->string('isbn')->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'primary_image')) {
                $table->string('primary_image')->nullable()->after('images');
            }
            if (!Schema::hasColumn('products', 'display_order')) {
                $table->integer('display_order')->default(0)->after('status');
            }
            if (!Schema::hasColumn('products', 'tax_class_id')) {
                $table->unsignedBigInteger('tax_class_id')->nullable()->after('tax_class');
            }
            // Ensure status is string and supports Draft/Archived
            $table->string('status')->default('published')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['isbn', 'primary_image', 'display_order', 'tax_class_id']);
            $table->boolean('status')->default(true)->change();
        });
    }
};
