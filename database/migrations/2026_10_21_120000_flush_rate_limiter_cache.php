<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Flush rate limiter & application cache on deploy to clear stuck lockout states from previous tests
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // Ignore if cache driver does not support flushing
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
