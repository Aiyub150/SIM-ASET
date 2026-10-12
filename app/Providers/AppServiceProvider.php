<?php

namespace App\Providers;

use App\Services\TimezoneHelper;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
    }
}
