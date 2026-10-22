<?php

namespace App\Providers;

use App\Services\TimezoneHelper;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Implicitly grant Super Admin role all permissions (flexible naming: "Super Admin", "Superadmin", etc.)
        Gate::before(function ($user, $ability) {
            $isSuperAdmin = $user->roles->contains(function ($r) {
                $normalized = strtolower(str_replace([' ', '-', '_'], '', $r->name));
                return in_array($normalized, ['superadmin', 'superadministrator']) || $r->id === 1;
            });
            return $isSuperAdmin ? true : null;
        });

        // Carbon Macros for Localized Timezone Formatting (WIB / WITA / WIT)
        Carbon::macro('toUserTz', function () {
            /** @var Carbon $this */
            return TimezoneHelper::convert($this);
        });

        Carbon::macro('formatUserTz', function (string $format = 'd/m/Y H:i:s', bool $withLabel = true) {
            /** @var Carbon $this */
            return TimezoneHelper::format($this, $format, $withLabel);
        });

        Carbon::macro('userTzTime', function (string $format = 'H:i:s', bool $withLabel = true) {
            /** @var Carbon $this */
            return TimezoneHelper::formatTime($this, $format, $withLabel);
        });

        Carbon::macro('userTzDate', function (string $format = 'd/m/Y') {
            /** @var Carbon $this */
            return TimezoneHelper::formatDate($this, $format);
        });

        // Blade Directives for views
        Blade::directive('userTime', function ($expression) {
            return "<?php echo \App\Services\TimezoneHelper::formatTime($expression); ?>";
        });

        Blade::directive('userDate', function ($expression) {
            return "<?php echo \App\Services\TimezoneHelper::formatDate($expression); ?>";
        });

        Blade::directive('userDateTime', function ($expression) {
            return "<?php echo \App\Services\TimezoneHelper::format($expression); ?>";
        });

        // Global Rate Limiter per IP (120 requests/minute, cannot be bypassed or reset)
        RateLimiter::for('global-ip', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // ── RATE LIMITERS ───────────────────────────────────────────────
        // 1. Login (Dinonaktifkan sementara sesuai permintaan)
        RateLimiter::for('auth.login', function (Request $request) {
            return Limit::none();
        });

        // 2. Forgot Password Protection (IP & Device)
        RateLimiter::for('auth.forgot-password', function (Request $request) {
            $clientIp = \App\Services\ActivityLogger::resolveClientIp($request);
            $deviceId = $request->cookie('simaset_did') 
                ?? $request->header('X-Device-Id') 
                ?? sha1($request->header('User-Agent') ?? 'unknown-device');
            $deviceKey = 'dev_' . substr(sha1($deviceId), 0, 16);

            $forgotPasswordResponse = function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan reset password dari perangkat Anda. Silakan coba lagi nanti.',
                    ], 429, $headers);
                }

                return response()->view('errors.429', [
                    'message' => 'Terlalu banyak permintaan reset password dari perangkat Anda. Silakan coba lagi nanti.',
                ], 429, $headers);
            };

            $limits = [
                Limit::perMinute(5)->by($deviceKey)->response($forgotPasswordResponse),
            ];

            if (!\App\Services\ActivityLogger::isInternalOrLoopbackIp($clientIp)) {
                $limits[] = Limit::perMinute(5)->by($clientIp)->response($forgotPasswordResponse);
            }

            return $limits;
        });

        // 3. Document Exports Protection (PDF, Excel, CSV) - IP & Device
        RateLimiter::for('reports.export', function (Request $request) {
            $userKey = $request->user()?->id ?? 'guest';
            $deviceKey = sha1($request->ip() . '|' . ($request->header('User-Agent') ?? 'unknown-device'));

            $exportResponse = function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Batas ekspor dokumen tercapai (maksimal 10 ekspor/menit). Silakan tunggu sejenak sebelum mengunduh kembali.',
                    ], 429, $headers);
                }

                return response()->view('errors.429', [
                    'message' => 'Batas ekspor dokumen tercapai (maksimal 10 ekspor/menit). Silakan tunggu sejenak sebelum mengunduh kembali.',
                ], 429, $headers);
            };

            return [
                Limit::perMinute(10)->by($userKey . '|' . $deviceKey)->response($exportResponse),
                Limit::perMinute(15)->by($request->ip())->response($exportResponse),
            ];
        });
    }
}
