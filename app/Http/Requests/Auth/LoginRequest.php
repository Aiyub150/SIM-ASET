<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public const MAX_ATTEMPTS = 5;
    public const LOCKOUT_SECONDS = 900; // 15 menit (900 detik)

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $email = strtolower((string) $this->input('email', ''));
        $user = \App\Models\User::where('email', $email)->first();

        // 1. Cek apakah user sedang terkunci di database (FR-AUTH-11 / konsep karbon-ikn)
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $seconds = (int) now()->diffInSeconds($user->locked_until);
            $seconds = $seconds > 0 ? $seconds : self::LOCKOUT_SECONDS;
            session()->flash('lockout_seconds', $seconds);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login yang gagal. Akun Anda dikunci sementara selama {$minutes} menit demi keamanan.",
            ]);
        }

        // 2. Cek rate limiter
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
            RateLimiter::hit($this->deviceKey(), self::LOCKOUT_SECONDS);

            $attempts = 0;
            if ($user) {
                $user->increment('failed_login_count');
                $attempts = $user->failed_login_count;
                if ($attempts >= self::MAX_ATTEMPTS) {
                    $user->update(['locked_until' => now()->addSeconds(self::LOCKOUT_SECONDS)]);
                }
            } else {
                $attempts = RateLimiter::attempts($this->throttleKey());
            }

            // User diberikan 5 kesempatan (percobaan 1 s.d. 4 sisa kesempatan)
            // Baru setelah 5 kali gagal penuh, sistem mengunci akun selama 15 menit sehingga percobaan ke-6 terkunci
            if ($attempts >= self::MAX_ATTEMPTS) {
                $seconds = self::LOCKOUT_SECONDS;
                session()->flash('lockout_seconds', $seconds);

                throw ValidationException::withMessages([
                    'email' => "Terlalu banyak percobaan login yang gagal. Akun Anda dikunci sementara selama 15 menit demi keamanan.",
                ]);
            }

            $remaining = self::MAX_ATTEMPTS - $attempts;

            throw ValidationException::withMessages([
                'email' => "Email atau kata sandi yang Anda masukkan salah. Sisa kesempatan login: {$remaining} kali sebelum akun dikunci.",
            ]);
        }

        // Login sukses -> Reset hitungan gagal (persis seperti karbon-ikn)
        if ($user) {
            $user->update([
                'failed_login_count' => 0,
                'locked_until'       => null,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->ipThrottleKey());
        RateLimiter::clear($this->deviceKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $primaryKey = $this->throttleKey();

        $isLimited = RateLimiter::tooManyAttempts($primaryKey, self::MAX_ATTEMPTS);

        if (! $isLimited) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($primaryKey);
        if ($seconds <= 0) {
            $seconds = self::LOCKOUT_SECONDS;
        }

        session()->flash('lockout_seconds', $seconds);
        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login yang gagal. Akun Anda dikunci sementara selama {$minutes} menit demi keamanan.",
        ]);
    }

    /**
     * Dapatkan unique device key dari cookie simaset_did atau header.
     */
    public function deviceKey(): string
    {
        $deviceId = $this->cookie('simaset_did') 
            ?? $this->header('X-Device-Id') 
            ?? sha1($this->header('User-Agent') ?? 'unknown-device');

        return 'dev_' . substr(sha1($deviceId), 0, 16);
    }

    /**
     * Get the rate limiting throttle key for the email account (seperti konsep karbon-ikn).
     */
    public function throttleKey(): string
    {
        return 'login_acc|' . Str::lower($this->string('email'));
    }

    /**
     * Get the IP-specific throttle key.
     */
    public function ipThrottleKey(): string
    {
        $clientIp = \App\Services\ActivityLogger::resolveClientIp($this);
        return 'login_ip|' . $clientIp;
    }
}
