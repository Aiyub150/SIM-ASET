<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Resolve the real client IP address from request headers.
     * Prioritizes client headers (X-Forwarded-For, X-Real-IP, CF-Connecting-IP, etc.)
     * and guarantees not returning internal server IP when forwarded.
     */
    public static function resolveClientIp(?Request $request = null): string
    {
        $req = $request ?: request();
        if (!$req) {
            return '127.0.0.1';
        }

        // 1. Cloudflare header (prioritas tertinggi: langsung dari edge proxy Cloudflare)
        $cfIp = $req->header('CF-Connecting-IP');
        if ($cfIp && filter_var($cfIp, FILTER_VALIDATE_IP)) {
            return $cfIp;
        }

        // 2. True-Client-IP header (Akamai / Cloudflare Enterprise)
        $trueClientIp = $req->header('True-Client-IP');
        if ($trueClientIp && filter_var($trueClientIp, FILTER_VALIDATE_IP)) {
            return $trueClientIp;
        }

        // 3. X-Real-IP header (jika IP publik)
        $realIp = $req->header('X-Real-IP');
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $realIp;
        }

        // 4. X-Forwarded-For: cari IP publik pertama yang bukan internal proxy
        $forwardedFor = $req->header('X-Forwarded-For');
        if ($forwardedFor) {
            $ips = array_map('trim', explode(',', $forwardedFor));
            foreach ($ips as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
            foreach ($ips as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) {
            return $realIp;
        }

        // 5. Client-IP header
        $clientIp = $req->header('Client-IP');
        if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $clientIp;
        }

        // 6. Fallback standard getClientIp
        return $req->getClientIp() ?: '127.0.0.1';
    }

    /**
     * Mengecek apakah IP adalah internal loopback atau server lokal.
     */
    public static function isInternalOrLoopbackIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
            return true;
        }

        $serverAddr = $_SERVER['SERVER_ADDR'] ?? null;
        if ($serverAddr && $ip === $serverAddr) {
            return true;
        }

        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Record an activity log entry with full descriptions.
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?array $properties = null,
        ?Request $request = null
    ): ActivityLog {
        $req = $request ?: request();
        $user = Auth::user();

        $userId = $user ? $user->id : null;
        $userName = $user ? $user->name : 'Sistem / Anonim';
        $userRole = $user ? ($user->roles->pluck('name')->first() ?? 'User') : 'Guest';

        $ipAddress = self::resolveClientIp($req);
        $userAgent = $req ? substr((string) $req->userAgent(), 0, 500) : null;

        return ActivityLog::create([
            'user_id'     => $userId,
            'user_name'   => $userName,
            'user_role'   => $userRole,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'properties'  => $properties,
            'ip_address'  => $ipAddress,
            'user_agent'  => $userAgent,
        ]);
    }
}
