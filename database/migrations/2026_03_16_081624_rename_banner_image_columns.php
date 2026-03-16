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
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'image_desktop')) {
                $table->renameColumn('image_desktop', 'image');
            }
            if (Schema::hasColumn('banners', 'image_mobile')) {
                $table->dropColumn('image_mobile');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'image')) {
                $table->renameColumn('image', 'image_desktop');
            }
            $table->string('image_mobile')->nullable()->after('image_desktop');
        });
    }
};
