<?php

namespace App\Http\Middleware;

use App\Services\TimezoneHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezoneMiddleware
{
    /**
     * Handle an incoming request and bind the user's localized timezone.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = $request->header('X-User-Timezone') 
            ?: $request->cookie('user_timezone');

        if ($timezone && TimezoneHelper::isValidTimezone($timezone)) {
            Config::set('app.user_timezone', TimezoneHelper::normalizeTimezone($timezone));
        } else {
            Config::set('app.user_timezone', TimezoneHelper::DEFAULT_TIMEZONE);
        }

        return $next($request);
    }
}
