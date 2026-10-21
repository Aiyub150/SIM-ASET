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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'failed_login_count')) {
                $table->unsignedInteger('failed_login_count')->default(0)->after('password');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'locked_until')) {
                $table->dropColumn('locked_until');
            }
            if (Schema::hasColumn('users', 'failed_login_count')) {
                $table->dropColumn('failed_login_count');
            }
        });
    }
};
