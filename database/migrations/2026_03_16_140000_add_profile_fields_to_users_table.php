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
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('phone');
            $table->date('dob')->nullable()->after('profile_picture');
            $table->string('gender', 20)->nullable()->after('dob');
            $table->string('account_status')->default('active')->after('gender');
            $table->string('role')->default('customer')->after('account_status');
            $table->timestamp('last_login_at')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_picture',
                'dob',
                'gender',
                'account_status',
                'role',
                'last_login_at',
            ]);
        });
    }
};
