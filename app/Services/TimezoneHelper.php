<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class TimezoneHelper
{
    public const DEFAULT_TIMEZONE = 'Asia/Jakarta';

    /**
     * Normalize friendly or legacy Indonesian timezone aliases to official IANA identifiers.
     */
    public static function normalizeTimezone(?string $timezone): ?string
    {
        if (empty($timezone)) {
            return null;
        }

        $map = [
            'Asia/Denpasar'      => 'Asia/Makassar',
            'Asia/Ujung_Pandang' => 'Asia/Makassar',
            'WIB'                => 'Asia/Jakarta',
            'WITA'               => 'Asia/Makassar',
            'WIT'                => 'Asia/Jayapura',
        ];

        return $map[$timezone] ?? $timezone;
    }

    /**
     * Get the resolved user timezone.
     */
    public static function getUserTimezone(): string
    {
        // 1. Check if set in config / request during middleware
        $configured = self::normalizeTimezone(Config::get('app.user_timezone'));
        if (!empty($configured) && self::isValidTimezone($configured)) {
            return $configured;
        }

        // 2. Check cookie from request
        $cookieTz = self::normalizeTimezone(Request::cookie('user_timezone'));
        if (!empty($cookieTz) && self::isValidTimezone($cookieTz)) {
            return $cookieTz;
        }

        // 3. Fallback to default Indonesian Western Time (WIB)
        return self::DEFAULT_TIMEZONE;
    }

    /**
     * Validate whether a timezone identifier is valid.
     */
    public static function isValidTimezone(string $timezone): bool
    {
        $normalized = self::normalizeTimezone($timezone);
        return in_array($normalized, timezone_identifiers_list(), true);
    }

    /**
     * Get the localized Indonesian timezone label (WIB, WITA, WIT) or GMT offset.
     */
    public static function getTimezoneLabel(?string $timezone = null): string
    {
        $tz = self::normalizeTimezone($timezone) ?: self::getUserTimezone();

        try {
            $now = Carbon::now($tz);
            $offsetHours = $now->offsetHours;

            return match ($offsetHours) {
                7, 7.0 => 'WIB',
                8, 8.0 => 'WITA',
                9, 9.0 => 'WIT',
                default => ($offsetHours >= 0 ? "GMT+{$offsetHours}" : "GMT{$offsetHours}"),
            };
        } catch (\Throwable) {
            return 'WIB';
        }
    }

    /**
     * Convert any DateTime/Carbon/string into the user's localized timezone.
     */
    public static function convert(mixed $dateTime, ?string $timezone = null): ?Carbon
    {
        if (empty($dateTime)) {
            return null;
        }

        $targetTz = self::normalizeTimezone($timezone) ?: self::getUserTimezone();

        if ($dateTime instanceof Carbon) {
            return $dateTime->copy()->setTimezone($targetTz);
        }

        if ($dateTime instanceof DateTimeInterface) {
            return Carbon::instance($dateTime)->setTimezone($targetTz);
        }

        return Carbon::parse($dateTime)->setTimezone($targetTz);
    }

    /**
     * Get the current Carbon instance in the user's timezone.
     */
    public static function now(?string $timezone = null): Carbon
    {
        return Carbon::now(self::normalizeTimezone($timezone) ?: self::getUserTimezone());
    }

    /**
     * Format a datetime with user timezone and optional timezone suffix (WIB / WITA / WIT).
     */
    public static function format(mixed $dateTime, string $format = 'd/m/Y H:i:s', bool $withLabel = true, ?string $timezone = null): string
    {
        $converted = self::convert($dateTime, $timezone);
        if (!$converted) {
            return '-';
        }

        $formatted = $converted->format($format);
        if ($withLabel) {
            $label = self::getTimezoneLabel($timezone);
            return "{$formatted} {$label}";
        }

        return $formatted;
    }

    /**
     * Format only the time component with timezone suffix (e.g. 14:18:27 WITA).
     */
    public static function formatTime(mixed $dateTime, string $format = 'H:i:s', bool $withLabel = true, ?string $timezone = null): string
    {
        return self::format($dateTime, $format, $withLabel, $timezone);
    }

    /**
     * Format only the date component adjusted to the user's timezone (e.g. 12/10/2026).
     */
    public static function formatDate(mixed $dateTime, string $format = 'd/m/Y', ?string $timezone = null): string
    {
        return self::format($dateTime, $format, false, $timezone);
    }

    /**
     * Format translated date in Indonesian in user timezone (e.g. 12 Oktober 2026).
     */
    public static function formatTranslated(mixed $dateTime, string $format = 'd F Y', ?string $timezone = null): string
    {
        $converted = self::convert($dateTime, $timezone);
        return $converted ? $converted->translatedFormat($format) : '-';
    }
}
