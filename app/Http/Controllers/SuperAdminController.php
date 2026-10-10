<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuperAdminController extends Controller
{
    /**
     * Tampilkan Log Server dari storage/logs/laravel.log
     */
    public function logServer(Request $request): View
    {
        $logFilePath = storage_path('logs/laravel.log');
        $rawLogs = [];
        $fileSize = 0;

        if (File::exists($logFilePath)) {
            $fileSize = File::size($logFilePath);
            // Baca baris terakhir hingga maksimal 500 baris log
            $content = File::get($logFilePath);
            $pattern = '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2}|$)/ms';
            
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            // Jika regex berhasil mengekstrak format log Laravel standar
            if (!empty($matches)) {
                foreach (array_reverse($matches) as $match) {
                    $timestamp = $match[1];
                    $env       = $match[2];
                    $level     = strtoupper($match[3]);
                    $message   = trim($match[4]);

                    // Ekstrak IP jika ada pola IP di dalam message/context
                    $ip = '-';
                    if (preg_match('/(?:ip|IP|client|client_ip)[\'":\s]+([0-9a-fA-F.:]+)/', $message, $ipMatches)) {
                        $ip = $ipMatches[1];
                    }

                    $rawLogs[] = [
                        'timestamp' => $timestamp,
                        'env'       => $env,
                        'level'     => $level,
                        'message'   => $message,
                        'ip'        => $ip !== '-' ? $ip : ActivityLogger::resolveClientIp($request),
                    ];

                    if (count($rawLogs) >= 150) {
                        break;
                    }
                }
            } else {
                // Fallback pembacaan baris demi baris jika format custom
                $lines = array_filter(array_map('trim', explode("\n", $content)));
                $lines = array_slice(array_reverse($lines), 0, 100);
                foreach ($lines as $line) {
                    $rawLogs[] = [
                        'timestamp' => date('Y-m-d H:i:s'),
                        'env'       => 'local',
                        'level'     => 'INFO',
                        'message'   => $line,
                        'ip'        => ActivityLogger::resolveClientIp($request),
                    ];
                }
            }
        }

        // Filter berdasarkan level jika ada input search/filter
        $filterLevel = strtoupper($request->get('level', 'ALL'));
        if ($filterLevel !== 'ALL' && !empty($filterLevel)) {
            $rawLogs = array_filter($rawLogs, fn($item) => $item['level'] === $filterLevel);
        }

        $search = $request->get('q');
        if (!empty($search)) {
            $rawLogs = array_filter($rawLogs, fn($item) => stripos($item['message'], $search) !== false);
        }

        // Info IP pengguna yang saat ini mengakses
        $currentUserIp = ActivityLogger::resolveClientIp($request);

        return view('superadmin.log-server', [
            'logs'          => array_values($rawLogs),
            'fileSize'      => round($fileSize / 1024, 2), // KB
            'filterLevel'   => $filterLevel,
            'search'        => $search,
            'currentUserIp' => $currentUserIp,
        ]);
    }

    /**
     * Bersihkan isi laravel.log
     */
    public function clearLogServer(Request $request)
    {
        $logFilePath = storage_path('logs/laravel.log');
        if (File::exists($logFilePath)) {
            File::put($logFilePath, '');
        }

        ActivityLogger::log(
            'DELETE',
            'Sistem',
            'Super Admin mengosongkan / membersihkan log server (laravel.log)',
            ['file' => 'laravel.log'],
            $request
        );

        return redirect()->route('superadmin.log-server')
            ->with('success', 'Log server berhasil dibersihkan.');
    }

    /**
     * Download file laravel.log
     */
    public function downloadLogServer(Request $request): BinaryFileResponse
    {
        $logFilePath = storage_path('logs/laravel.log');
        if (!File::exists($logFilePath)) {
            File::put($logFilePath, '');
        }

        ActivityLogger::log(
            'EXPORT',
            'Sistem',
            'Super Admin mengunduh berkas laravel.log',
            ['file' => 'laravel.log'],
            $request
        );

        return response()->download($logFilePath, 'laravel-' . date('Y-m-d_His') . '.log');
    }

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
