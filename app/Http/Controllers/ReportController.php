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
        // Terima format baru: ?period=YYYY-MM (dari <input type="month">)
        // Juga tetap kompatibel dengan format lama: ?month=MM&year=YYYY
        [$month, $year] = $this->parsePeriod($request);

        $loans = Loan::with(['borrower'])
            ->whereMonth('borrow_date', $month)
            ->whereYear('borrow_date', $year)
            ->orderBy('borrow_date', 'asc')
            ->get();

        $movements = StockMovement::with(['item'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('reports.index', compact('loans', 'movements', 'month', 'year'));
    }

    /**
     * Ekspor Laporan ke format PDF
     */
    public function exportPdf(Request $request)
    {
        [$month, $year] = $this->parsePeriod($request);

        $loans     = Loan::with(['borrower'])->whereMonth('borrow_date', $month)->whereYear('borrow_date', $year)->get();
        $movements = StockMovement::with(['item'])->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        $pdf = Pdf::loadView('reports.pdf', compact('loans', 'movements', 'monthName', 'year'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream("Laporan_Inventaris_{$monthName}_{$year}.pdf");
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
