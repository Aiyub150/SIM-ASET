<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Tampilkan Log History (Audit Trail)
     */
    public function logHistory(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        // Filter berdasarkan modul
        if ($request->filled('module')) {
            $query->where('module', $request->get('module'));
        }

        // Filter berdasarkan action
        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }

        // Filter pencarian teks
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        $logs = $query->paginate(20)->withQueryString();

        $modules = ActivityLog::distinct()->pluck('module')->filter()->values();
        $actions = ActivityLog::distinct()->pluck('action')->filter()->values();

        return view('superadmin.log-history', [
            'logs'    => $logs,
            'modules' => $modules,
            'actions' => $actions,
            'currentUserIp' => ActivityLogger::resolveClientIp($request),
        ]);
    }
}
