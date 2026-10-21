<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Flush all application & rate limiter caches to free any stuck server-IP lockouts
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // Ignore if cache driver does not support flush
        }

        $cacheDataPath = storage_path('framework/cache/data');
        if (is_dir($cacheDataPath)) {
            try {
                File::cleanDirectory($cacheDataPath);
            } catch (\Throwable $e) {
                // Ignore permission issues
            }
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
