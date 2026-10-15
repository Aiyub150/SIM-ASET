<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply OWASP-recommended security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Clickjacking Protection (set only if not already present from reverse proxy)
        if (! $response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // 2. MIME Type Sniffing Prevention
        if (! $response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // 3. Referrer Policy
        if (! $response->headers->has('Referrer-Policy')) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // 4. Permissions / Feature Policy
        if (! $response->headers->has('Permissions-Policy')) {
            $response->headers->set(
                'Permissions-Policy',
                'camera=(self), microphone=(), geolocation=(), payment=()'
            );
        }

        // 5. Strict Transport Security (HSTS) when on HTTPS
        if ($request->isSecure() && ! $response->headers->has('Strict-Transport-Security')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 6. Content Security Policy (CSP)
        // Hardened: remove 'unsafe-eval' to eliminate runtime eval() risks while preserving required CDNs
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com; "
            . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com; "
            . "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com; "
            . "font-src 'self' data: https://cdn.jsdelivr.net; "
            . "connect-src 'self' blob:; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        // 8. Anti-Cache Headers for Authenticated / Private Requests (Prevent BFCache & History Data Leakage)
        if ($request->user() || $request->is('dashboard*', 'items*', 'loans*', 'borrowers*', 'stocks*', 'reports*', 'users*', 'superadmin*', 'settings*', 'profile*')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        // Remove server exposure headers
        header_remove('X-Powered-By');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
