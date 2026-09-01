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
        // Ambil filter dari URL, jika kosong gunakan bulan dan tahun saat ini
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        // Tarik data dengan Eager Loading dan Filter Waktu
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
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $loans = Loan::with(['borrower'])->whereMonth('borrow_date', $month)->whereYear('borrow_date', $year)->get();
        $movements = StockMovement::with(['item'])->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

        $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        $pdf = Pdf::loadView('reports.pdf', compact('loans', 'movements', 'monthName', 'year'));
        $pdf->setPaper('A4', 'landscape'); // Gunakan Landscape karena kolom tabel laporan biasanya banyak

        return $pdf->stream("Laporan_Inventaris_{$monthName}_{$year}.pdf");
    }
}