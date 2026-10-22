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
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Rate limiter dinonaktifkan sementara
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
