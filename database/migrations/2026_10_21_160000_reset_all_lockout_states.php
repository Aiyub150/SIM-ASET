<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reset all users failed_login_count and locked_until in database
        try {
            DB::table('users')->update([
                'failed_login_count' => 0,
                'locked_until' => null,
            ]);
        } catch (\Throwable $e) {
            // Ignore if columns not yet ready
        }

        // 2. Clear all cache stores
        try {
            Cache::flush();
        } catch (\Throwable $e) {}

        // 3. Clean physical cache files in storage
        $cachePath = storage_path('framework/cache/data');
        if (is_dir($cachePath)) {
            try {
                File::cleanDirectory($cachePath);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
