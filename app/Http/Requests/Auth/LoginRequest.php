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
            RateLimiter::hit($this->deviceKey(), self::LOCKOUT_SECONDS);

            $attempts = max(
                RateLimiter::attempts($this->throttleKey()),
                RateLimiter::attempts($this->ipThrottleKey()),
                RateLimiter::attempts($this->deviceKey())
            );

            // User diberikan 5 kesempatan (percobaan 1 s.d. 4 menampilkan sisa kesempatan)
            // Setelah 5 kali gagal, sistem mengunci selama 15 menit sehingga percobaan ke-6 terkunci penuh
            if ($attempts >= self::MAX_ATTEMPTS) {
                $seconds = max(
                    RateLimiter::availableIn($this->throttleKey()),
                    RateLimiter::availableIn($this->ipThrottleKey()),
                    RateLimiter::availableIn($this->deviceKey())
                );
                $lockSec = $seconds > 0 ? $seconds : self::LOCKOUT_SECONDS;
                session()->flash('lockout_seconds', $lockSec);

                throw ValidationException::withMessages([
                    'email' => "Terlalu banyak percobaan login yang gagal. Akun dan perangkat Anda dikunci sementara selama 15 menit demi keamanan.",
                ]);
            }

            $remaining = self::MAX_ATTEMPTS - $attempts;

            throw ValidationException::withMessages([
                'email' => "Email atau kata sandi yang Anda masukkan salah. Sisa kesempatan login: {$remaining} kali sebelum akun dikunci.",
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
        $ipKey = $this->ipThrottleKey();
        $devKey = $this->deviceKey();

        // Kunci HANYA jika gagal sudah mencapai atau melebihi MAX_ATTEMPTS (5 kali)
        $isLimited = RateLimiter::tooManyAttempts($primaryKey, self::MAX_ATTEMPTS)
                  || RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS)
                  || RateLimiter::tooManyAttempts($devKey, self::MAX_ATTEMPTS);

        if (! $isLimited) {
            return;
        }

        event(new Lockout($this));

        $seconds = max(
            RateLimiter::availableIn($primaryKey),
            RateLimiter::availableIn($ipKey),
            RateLimiter::availableIn($devKey)
        );

        if ($seconds <= 0) {
            $seconds = self::LOCKOUT_SECONDS;
        }

        session()->flash('lockout_seconds', $seconds);
        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login yang gagal. Akun dan perangkat Anda dikunci sementara selama {$minutes} menit demi keamanan.",
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
     * Get the rate limiting throttle key for the request (Email + Scope).
     */
    public function throttleKey(): string
    {
        $clientIp = \App\Services\ActivityLogger::resolveClientIp($this);
        $scope = \App\Services\ActivityLogger::isInternalOrLoopbackIp($clientIp) 
            ? $this->deviceKey() 
            : $clientIp . '|' . $this->deviceKey();

        return Str::transliterate(Str::lower($this->string('email')) . '|' . $scope);
    }

    /**
     * Get the IP-specific throttle key.
     */
    public function ipThrottleKey(): string
    {
        $clientIp = \App\Services\ActivityLogger::resolveClientIp($this);
        if (\App\Services\ActivityLogger::isInternalOrLoopbackIp($clientIp)) {
            return 'login_dev|' . $this->deviceKey();
        }

        return 'login_ip|' . $clientIp;
    }
}
