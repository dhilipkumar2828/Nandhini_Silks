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
        // Change product status from boolean to string to support 'archived'
        Schema::table('products', function (Blueprint $table) {
            $table->string('status')->default('1')->change();
        });

        // Add meta_keywords to categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });

        // Add meta_keywords to sub_categories
        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });

        // Add meta_keywords to child_categories
        Schema::table('child_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('child_categories', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('meta_keywords');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('meta_keywords');
        });

        Schema::table('child_categories', function (Blueprint $table) {
            $table->dropColumn('meta_keywords');
        });
    }
};
