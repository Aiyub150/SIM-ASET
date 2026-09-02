<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Services\LoanService;
use Exception;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ReturnLoanRequest;
use App\Models\Loan;
use App\Models\Borrower;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService)
    {
    }

    public function printPdf(Loan $loan)
    {
        // Staff hanya bisa cetak miliknya sendiri
        if (Auth::user()->hasRole('Staff Logistik') && $loan->user_id !== Auth::id()) {
            abort(403);
        }

        $loan->load(['borrower', 'user', 'loanItems.item']);
        $pdf = Pdf::loadView('loans.pdf', compact('loan'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream("BAST_{$loan->loan_code}.pdf");
    }

    /**
     * Daftar peminjaman:
     * - Super Admin & Admin : semua transaksi
     * - Staff Logistik      : hanya transaksi yang dia buat sendiri
     */
    public function index()
    {
        $user  = Auth::user();
        $query = Loan::with(['borrower', 'user'])->orderBy('created_at', 'desc');

        if ($user->hasRole('Staff Logistik')) {
            $query->where('user_id', $user->id);
        }

        $loans = $query->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Proses penyimpanan data peminjaman
     */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        try {
            $loan = $this->loanService->createLoan($request->validated(), Auth::id());

            return redirect()
                ->route('loans.index')
                ->with('success', "Peminjaman berhasil dicatat dengan kode: {$loan->loan_code}");

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Proses pengembalian barang
     */
    public function returnItems(ReturnLoanRequest $request, Loan $loan): RedirectResponse
    {
        // Staff tidak bisa memproses pengembalian atas transaksi orang lain
        if (Auth::user()->hasRole('Staff Logistik') && $loan->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $updatedLoan = $this->loanService->processReturn($loan->id, $request->validated(), Auth::id());

            $message = $updatedLoan->status === 'completed'
                ? "Pengembalian berhasil. Transaksi {$loan->loan_code} telah SELESAI sepenuhnya."
                : "Pengembalian parsial berhasil dicatat pada transaksi {$loan->loan_code}.";

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', $message);

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Form peminjaman baru
     */
    public function create()
    {
        $borrowers = Borrower::orderBy('institution_name', 'asc')->get();
        $items     = Item::where('available_qty', '>', 0)->orderBy('name', 'asc')->get();

        return view('loans.create', compact('borrowers', 'items'));
    }

    /**
     * Detail peminjaman — Staff hanya bisa lihat miliknya
     */
    public function show(Loan $loan)
    {
        if (Auth::user()->hasRole('Staff Logistik') && $loan->user_id !== Auth::id()) {
            abort(403);
        }

        $loan->load(['borrower', 'user', 'loanItems.item']);

        $pendingItems = $loan->loanItems->filter(function ($loanItem) {
            return $loanItem->qty > $loanItem->returned_qty;
        });

        return view('loans.show', compact('loan', 'pendingItems'));
    }
}
