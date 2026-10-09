<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssSanitizer
{
    /**
     * The attributes that should never be sanitized.
     *
     * @var array<int, string>
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (! is_string($value) || in_array($key, $this->except, true)) {
                return;
            }

            // Strip script and style blocks completely, along with event attributes and dangerous protocols
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $sanitized = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $decoded);
            $sanitized = preg_replace('/%3C|%3E|(?:on[a-z]+\s*=|javascript:|vbscript:|data:text\/html)/i', '', $sanitized);
            $sanitized = strip_tags($sanitized);

            // Normalize line endings and collapse multiple horizontal spaces while preserving newlines
            $sanitized = str_replace(["\r\n", "\r"], "\n", $sanitized);
            $sanitized = preg_replace('/[^\S\n]+/', ' ', $sanitized);
            $sanitized = preg_replace('/\n{3,}/', "\n\n", $sanitized);

            $value = trim($sanitized);
        });

        $request->merge($input);

        return $next($request);
    }
}
