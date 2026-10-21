<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $ip = $request->ip();
        $deviceKey = sha1($ip . '|' . ($request->header('User-Agent') ?? 'unknown-device'));
        $ipKey = 'login_ip|' . $ip;

        // Cek semua kemungkinan key lockout (IP limiter middleware, device fingerprint, atau IP key LoginRequest)
        $isLocked = \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($ip, LoginRequest::MAX_ATTEMPTS)
                 || \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($deviceKey, LoginRequest::MAX_ATTEMPTS)
                 || \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($ipKey, LoginRequest::MAX_ATTEMPTS);

        $lockoutSeconds = 0;
        if ($isLocked) {
            $lockoutSeconds = max(
                (int) \Illuminate\Support\Facades\RateLimiter::availableIn($ip),
                (int) \Illuminate\Support\Facades\RateLimiter::availableIn($deviceKey),
                (int) \Illuminate\Support\Facades\RateLimiter::availableIn($ipKey)
            );
            if ($lockoutSeconds <= 0) {
                $lockoutSeconds = LoginRequest::LOCKOUT_SECONDS;
            }
        } elseif (session()->has('lockout_seconds')) {
            $lockoutSeconds = (int) session('lockout_seconds');
        }

        return view('auth.login', [
            'lockoutSeconds' => $lockoutSeconds,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $user->update(['last_login_at' => now()]);
        }

        \App\Services\ActivityLogger::log(
            'LOGIN',
            'Autentikasi',
            'Pengguna berhasil masuk (login) ke sistem',
            ['email' => $request->email],
            $request
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            \App\Services\ActivityLogger::log(
                'LOGOUT',
                'Autentikasi',
                'Pengguna keluar (logout) dari sistem',
                ['email' => Auth::user()->email],
                $request
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->withHeaders([
                'Clear-Site-Data' => '"cache", "storage"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0, private',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ]);
    }
}
