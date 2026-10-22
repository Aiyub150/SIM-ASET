<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman pratinjau Laporan Bulanan
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->can('reports.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk melihat laporan.');
        }

        // Terima format baru: ?period=YYYY-MM (dari <input type="month">)
        // Juga tetap kompatibel dengan format lama: ?month=MM&year=YYYY
        [$month, $year] = $this->parsePeriod($request);

        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        $loanQuery = Loan::with(['borrower'])
            ->whereMonth('borrow_date', $month)
            ->whereYear('borrow_date', $year)
            ->orderBy('borrow_date', 'asc');

        if ($isStaff) {
            $loanQuery->where('user_id', $user->id);
        }
        $loans = $loanQuery->get();

        if ($isStaff) {
            $movements = collect();
        } else {
            $movements = StockMovement::with(['item'])
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('reports.index', compact('loans', 'movements', 'month', 'year'));
    }

    /**
     * Ekspor Laporan ke format PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->can('reports.export') && !$user->can('reports.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk mengekspor laporan.');
        }

        [$month, $year] = $this->parsePeriod($request);

        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        $loanQuery = Loan::with(['borrower'])->whereMonth('borrow_date', $month)->whereYear('borrow_date', $year);
        if ($isStaff) {
            $loanQuery->where('user_id', $user->id);
        }
        $loans = $loanQuery->get();

        if ($isStaff) {
            $movements = collect();
        } else {
            $movements = StockMovement::with(['item'])->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        }

        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        $pdf = Pdf::loadView('reports.pdf', compact('loans', 'movements', 'monthName', 'year'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream("Laporan_Inventaris_{$monthName}_{$year}.pdf");
    }

    /**
     * Ekspor Laporan ke format Excel (.xls)
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->can('reports.export') && !$user->can('reports.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk mengekspor laporan.');
        }

        [$month, $year] = $this->parsePeriod($request);
        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        $loanQuery = Loan::with(['borrower', 'loanItems.item'])->whereMonth('borrow_date', $month)->whereYear('borrow_date', $year);
        if ($isStaff) {
            $loanQuery->where('user_id', $user->id);
        }
        $loans = $loanQuery->get();

        $movements = collect();
        if (!$isStaff) {
            $movements = StockMovement::with(['item'])->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        }

        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');
        $fileName = "Laporan_SIM-ASET_{$monthName}_{$year}.xls";

        return response()->streamDownload(function () use ($loans, $movements, $monthName, $year) {
            echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style>table { border-collapse: collapse; width: 100%; font-family: sans-serif; } th, td { border: 1px solid #94a3b8; padding: 6px 10px; font-size: 12px; } th { background-color: #f1f5f9; font-weight: bold; }</style></head><body>";
            echo "<h2 style='font-family: sans-serif; margin-bottom: 4px;'>LAPORAN INVENTARIS SIM-ASET</h2>";
            echo "<p style='font-family: sans-serif; color: #475569; margin-top: 0;'>Periode: " . strtoupper($monthName) . " {$year}</p>";

            echo "<h4 style='font-family: sans-serif; margin-top: 20px; margin-bottom: 8px;'>1. Data Peminjaman Barang</h4>";
            echo "<table><thead><tr><th>No</th><th>Kode Peminjaman</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Status</th><th>Daftar Barang & Qty</th></tr></thead><tbody>";
            $no = 1;
            foreach ($loans as $l) {
                $statusText = match($l->computed_status) {
                    'completed' => 'Selesai',
                    'completed_late' => 'Selesai Terlambat',
                    'overdue' => 'Terlambat',
                    default => 'Aktif',
                };
                $itemsStr = htmlspecialchars($l->loanItems->map(fn($li) => ($li->item->name ?? 'Barang') . ' (' . $li->qty . ')')->join(', '));
                echo "<tr><td align='center'>{$no}</td><td>{$l->loan_code}</td><td>" . htmlspecialchars($l->borrower->institution_name ?? $l->borrower->name ?? '-') . "</td><td>{$l->borrow_date}</td><td>{$l->due_date}</td><td>{$statusText}</td><td>{$itemsStr}</td></tr>";
                $no++;
            }
            if ($loans->isEmpty()) {
                echo "<tr><td colspan='7' align='center' style='color:#64748b;'>Tidak ada transaksi peminjaman pada periode ini.</td></tr>";
            }
            echo "</tbody></table>";

            if ($movements->isNotEmpty()) {
                echo "<h4 style='font-family: sans-serif; margin-top: 25px; margin-bottom: 8px;'>2. Data Mutasi Stok</h4>";
                echo "<table><thead><tr><th>No</th><th>Tanggal</th><th>No Referensi</th><th>Barang</th><th>SKU</th><th>Tipe Mutasi</th><th>Jumlah</th><th>Catatan</th></tr></thead><tbody>";
                $noM = 1;
                foreach ($movements as $m) {
                    echo "<tr><td align='center'>{$noM}</td><td>{$m->created_at}</td><td>" . htmlspecialchars($m->reference_code ?? '-') . "</td><td>" . htmlspecialchars($m->item->name ?? '-') . "</td><td>" . htmlspecialchars($m->item->sku ?? '-') . "</td><td>{$m->type}</td><td align='center'>{$m->qty}</td><td>" . htmlspecialchars($m->notes ?? '-') . "</td></tr>";
                    $noM++;
                }
                echo "</tbody></table>";
            }

            echo "</body></html>";
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Ekspor Laporan ke format CSV (.csv)
     */
    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->can('reports.export') && !$user->can('reports.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk mengekspor laporan.');
        }

        [$month, $year] = $this->parsePeriod($request);
        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        $loanQuery = Loan::with(['borrower', 'loanItems.item'])->whereMonth('borrow_date', $month)->whereYear('borrow_date', $year);
        if ($isStaff) {
            $loanQuery->where('user_id', $user->id);
        }
        $loans = $loanQuery->get();

        $movements = collect();
        if (!$isStaff) {
            $movements = StockMovement::with(['item'])->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
        }

        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');
        $fileName = "Laporan_SIM-ASET_{$monthName}_{$year}.csv";

        return response()->streamDownload(function () use ($loans, $movements, $monthName, $year) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM agar dibuka sempurna di Microsoft Excel Windows
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ["LAPORAN INVENTARIS SIM-ASET - PERIODE {$monthName} {$year}"]);
            fputcsv($handle, []);

            // 1. Data Peminjaman
            fputcsv($handle, ['DATA PEMINJAMAN BARANG']);
            fputcsv($handle, ['No', 'Kode Peminjaman', 'Peminjam', 'Tanggal Pinjam', 'Jatuh Tempo', 'Status', 'Daftar Barang & Qty']);
            $no = 1;
            foreach ($loans as $l) {
                $statusText = match($l->computed_status) {
                    'completed' => 'Selesai',
                    'completed_late' => 'Selesai Terlambat',
                    'overdue' => 'Terlambat',
                    default => 'Aktif',
                };
                $itemsStr = $l->loanItems->map(fn($li) => ($li->item->name ?? 'Barang') . ' (' . $li->qty . ')')->join(', ');
                fputcsv($handle, [
                    $no++,
                    $l->loan_code,
                    $l->borrower->institution_name ?? $l->borrower->name ?? '-',
                    $l->borrow_date,
                    $l->due_date,
                    $statusText,
                    $itemsStr
                ]);
            }

            fputcsv($handle, []);

            // 2. Data Mutasi Stok
            if ($movements->isNotEmpty()) {
                fputcsv($handle, ['DATA MUTASI STOK']);
                fputcsv($handle, ['No', 'Tanggal', 'No Referensi', 'Barang', 'SKU', 'Tipe Mutasi', 'Jumlah', 'Catatan / Alasan']);
                $noM = 1;
                foreach ($movements as $m) {
                    fputcsv($handle, [
                        $noM++,
                        $m->created_at->format('Y-m-d H:i:s'),
                        $m->reference_code ?? '-',
                        $m->item->name ?? '-',
                        $m->item->sku ?? '-',
                        $m->type,
                        $m->qty,
                        $m->notes ?? '-'
                    ]);
                }
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    // ── Helper: urai periode dari request ─────────────────────────────────
    private function parsePeriod(Request $request): array
    {
        // Format baru dari <input type="month">: period=YYYY-MM
        if ($request->filled('period') && str_contains($request->input('period'), '-')) {
            [$y, $m] = explode('-', $request->input('period'), 2);
            return [str_pad($m, 2, '0', STR_PAD_LEFT), $y];
        }

        // Format lama: ?month=MM&year=YYYY (untuk kompatibilitas link ekspor PDF)
        $month = $request->input('month', now()->format('m'));
        $year  = $request->input('year',  now()->format('Y'));
        return [str_pad($month, 2, '0', STR_PAD_LEFT), $year];
    }
}
