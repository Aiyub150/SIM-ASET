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
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::LOCKOUT_SECONDS);
            RateLimiter::hit($this->ipThrottleKey(), self::LOCKOUT_SECONDS);

            $remaining = min(
                RateLimiter::remaining($this->throttleKey(), self::MAX_ATTEMPTS),
                RateLimiter::remaining($this->ipThrottleKey(), self::MAX_ATTEMPTS)
            );

            if ($remaining <= 0) {
                $seconds = max(
                    RateLimiter::availableIn($this->throttleKey()),
                    RateLimiter::availableIn($this->ipThrottleKey())
                );
                $lockSec = $seconds > 0 ? $seconds : self::LOCKOUT_SECONDS;
                session()->flash('lockout_seconds', $lockSec);

                throw ValidationException::withMessages([
                    'email' => "Terlalu banyak percobaan login yang gagal. Akun dan IP Anda dikunci sementara selama 15 menit demi keamanan.",
                ]);
            }

            throw ValidationException::withMessages([
                'email' => "Email atau kata sandi yang Anda masukkan salah. Sisa kesempatan login: {$remaining} kali sebelum akun dikunci.",
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->ipThrottleKey());
        RateLimiter::clear($this->ip());
        RateLimiter::clear(sha1($this->ip() . '|' . ($this->header('User-Agent') ?? 'unknown-device')));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $primaryKey = $this->throttleKey();
        $ipKey = $this->ipThrottleKey();

        $isLimited = RateLimiter::tooManyAttempts($primaryKey, self::MAX_ATTEMPTS)
                  || RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS);

        if (! $isLimited) {
            return;
        }

        event(new Lockout($this));

        $seconds = max(
            RateLimiter::availableIn($primaryKey),
            RateLimiter::availableIn($ipKey)
        );

        if ($seconds <= 0) {
            $seconds = self::LOCKOUT_SECONDS;
        }

        session()->flash('lockout_seconds', $seconds);
        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login yang gagal. Akun dan IP Anda dikunci sementara selama {$minutes} menit demi keamanan.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request (Email + IP + Device Fingerprint).
     */
    public function throttleKey(): string
    {
        $device = sha1($this->header('User-Agent') ?? 'unknown-device');
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip() . '|' . $device);
    }

    /**
     * Get the IP-specific throttle key.
     */
    public function ipThrottleKey(): string
    {
        return 'login_ip|' . $this->ip();
    }
}
