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
        // Banners
        if (Schema::hasColumn('banners', 'image') && !Schema::hasColumn('banners', 'image_desktop')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->renameColumn('image', 'image_desktop');
            });
        }

        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'image_mobile')) {
                $table->string('image_mobile')->nullable()->after(Schema::hasColumn('banners', 'image_desktop') ? 'image_desktop' : 'image');
            }
            if (!Schema::hasColumn('banners', 'display_order')) {
                $table->integer('display_order')->default(0)->after('link');
            }
        });

        // Ads
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ads', 'open_new_tab')) {
                $table->boolean('open_new_tab')->default(false)->after('link');
            }
        });

        // Testimonials
        if (Schema::hasColumn('testimonials', 'comment') && !Schema::hasColumn('testimonials', 'review')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->renameColumn('comment', 'review');
            });
        }

        Schema::table('testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('testimonials', 'photo')) {
                $table->string('photo')->nullable()->after('name');
            }
            if (!Schema::hasColumn('testimonials', 'display_homepage')) {
                $table->boolean('display_homepage')->default(true)->after('rating');
            }
            if (!Schema::hasColumn('testimonials', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'image_desktop')) {
                $table->renameColumn('image_desktop', 'image');
            }
            $table->dropColumn(['image_mobile', 'display_order']);
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['title', 'open_new_tab']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['photo', 'display_homepage', 'submitted_at']);
            if (Schema::hasColumn('testimonials', 'review')) {
                $table->renameColumn('review', 'comment');
            }
        });
    }
};
