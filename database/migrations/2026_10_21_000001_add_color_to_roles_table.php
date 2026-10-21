<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('roles', 'color')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('color', 30)->nullable()->default('#2563eb')->after('guard_name');
            });

            // Set warna default menarik untuk role bawaan
            DB::table('roles')->where('name', 'Super Admin')->update(['color' => '#d97706']); // Amber / Gold
            DB::table('roles')->where('name', 'Admin')->update(['color' => '#2563eb']);       // Blue
            DB::table('roles')->where('name', 'Staff Logistik')->update(['color' => '#059669']); // Emerald
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('roles', 'color')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }
};
