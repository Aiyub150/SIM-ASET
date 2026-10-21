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
        $clientIp = \App\Services\ActivityLogger::resolveClientIp($request);
        $deviceId = $request->cookie('simaset_did') 
            ?? $request->header('X-Device-Id') 
            ?? sha1($request->header('User-Agent') ?? 'unknown-device');
        $deviceKey = 'dev_' . substr(sha1($deviceId), 0, 16);

        // Lockout timer HANYA aktif jika device ini telah melampaui batas percobaan
        $isLocked = false;
        $lockoutSeconds = 0;

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($deviceKey, LoginRequest::MAX_ATTEMPTS)) {
            $isLocked = true;
            $lockoutSeconds = (int) \Illuminate\Support\Facades\RateLimiter::availableIn($deviceKey);
        } elseif (!\App\Services\ActivityLogger::isInternalOrLoopbackIp($clientIp)) {
            $ipKey = 'login_ip|' . $clientIp;
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($ipKey, LoginRequest::MAX_ATTEMPTS)) {
                $isLocked = true;
                $lockoutSeconds = (int) \Illuminate\Support\Facades\RateLimiter::availableIn($ipKey);
            }
        }

        if ($isLocked) {
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
