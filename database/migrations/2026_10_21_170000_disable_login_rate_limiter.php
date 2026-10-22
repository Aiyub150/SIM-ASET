<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
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
        // 1. Reset user lockout fields
        try {
            DB::table('users')->update([
                'failed_login_count' => 0,
                'locked_until' => null,
            ]);
        } catch (\Throwable $e) {}

        // 2. Clear all cache stores
        try {
            Cache::flush();
        } catch (\Throwable $e) {}

        // 3. Clear compiled views and physical cache
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
        } catch (\Throwable $e) {}

        $cachePath = storage_path('framework/cache/data');
        if (is_dir($cachePath)) {
            try {
                File::cleanDirectory($cachePath);
            } catch (\Throwable $e) {}
        }

        $viewsPath = storage_path('framework/views');
        if (is_dir($viewsPath)) {
            try {
                File::cleanDirectory($viewsPath);
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
