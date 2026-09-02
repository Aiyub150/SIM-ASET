<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value) {
            if (! is_string($value)) {
                return;
            }

            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $decoded = preg_replace('/%3C|%3E|%26|(?:on[a-z]+\s*=|javascript:|vbscript:|data:text\/html)/i', '', $decoded);
            $decoded = strip_tags($decoded);
            $decoded = preg_replace('/\s+/', ' ', $decoded);

            $value = trim($decoded);
        });

        $request->merge($input);

        return $next($request);
    }
}
