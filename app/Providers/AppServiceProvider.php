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

        // ── RATE LIMITERS: IP & DEVICE FINGERPRINTING ─────────────────────
        // 1. Login Protection (IP & Device)
        RateLimiter::for('auth.login', function (Request $request) {
            $deviceKey = sha1($request->ip() . '|' . ($request->header('User-Agent') ?? 'unknown-device'));
            $email = strtolower((string) $request->input('email', ''));

            return [
                Limit::perMinute(5)->by($deviceKey)->response(function () {
                    return back()->withInput()->with('error', 'Terlalu banyak percobaan login dari perangkat ini. Silakan tunggu 1 menit.');
                }),
                Limit::perMinute(10)->by($request->ip()),
                Limit::perMinute(5)->by($email . '|' . $request->ip()),
            ];
        });

        // 2. Forgot Password Protection (IP & Device)
        RateLimiter::for('auth.forgot-password', function (Request $request) {
            $deviceKey = sha1($request->ip() . '|' . ($request->header('User-Agent') ?? 'unknown-device'));

            return [
                Limit::perMinutes(15, 3)->by($deviceKey)->response(function () {
                    return back()->with('error', 'Terlalu banyak permintaan reset password dari perangkat Anda. Silakan coba lagi nanti.');
                }),
                Limit::perMinutes(15, 5)->by($request->ip()),
            ];
        });

        // 3. Document Exports Protection (PDF, Excel, CSV) - IP & Device
        RateLimiter::for('reports.export', function (Request $request) {
            $userKey = $request->user()?->id ?? 'guest';
            $deviceKey = sha1($request->ip() . '|' . ($request->header('User-Agent') ?? 'unknown-device'));

            return [
                Limit::perMinute(10)->by($userKey . '|' . $deviceKey)->response(function () {
                    return back()->with('error', 'Batas ekspor dokumen tercapai (maksimal 10 ekspor/menit). Silakan tunggu sejenak sebelum mengunduh kembali.');
                }),
                Limit::perMinute(15)->by($request->ip()),
            ];
        });
    }
}
