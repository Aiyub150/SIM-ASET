<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DeviceIdentificationMiddleware
{
    public const COOKIE_NAME = 'simaset_did';

    /**
     * Handle incoming request and ensure a unique device identifier cookie is attached.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $deviceId = $request->cookie(self::COOKIE_NAME);
        $cookieMissing = empty($deviceId) || !is_string($deviceId) || strlen($deviceId) < 16;

        if ($cookieMissing) {
            $deviceId = (string) Str::uuid();
            $request->cookies->set(self::COOKIE_NAME, $deviceId);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($cookieMissing && method_exists($response, 'withCookie')) {
            $response->withCookie(cookie(
                name: self::COOKIE_NAME,
                value: $deviceId,
                minutes: 1051200, // 2 tahun
                path: '/',
                domain: null,
                secure: $request->isSecure(),
                httpOnly: false,
                raw: false,
                sameSite: 'lax'
            ));
        }

        return $response;
    }
}
