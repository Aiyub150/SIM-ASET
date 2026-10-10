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

        // 1. Check HTTP header X-Forwarded-For (can be comma-separated list of client, proxy1, proxy2)
        $forwardedFor = $req->header('X-Forwarded-For');
        if ($forwardedFor) {
            $ips = array_map('trim', explode(',', $forwardedFor));
            foreach ($ips as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
                // If it's a valid IP (even private subnet like 192.168.x or 10.x in LAN office), return the first one
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        // 2. Cloudflare / Reverse Proxy headers
        $cfIp = $req->header('CF-Connecting-IP');
        if ($cfIp && filter_var($cfIp, FILTER_VALIDATE_IP)) {
            return $cfIp;
        }

        // 3. X-Real-IP header
        $realIp = $req->header('X-Real-IP');
        if ($realIp && filter_var($realIp, FILTER_VALIDATE_IP)) {
            return $realIp;
        }

        // 4. Client-IP header
        $clientIp = $req->header('Client-IP');
        if ($clientIp && filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $clientIp;
        }

        // 5. Fallback to standard request getClientIp
        return $req->getClientIp() ?: '127.0.0.1';
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
