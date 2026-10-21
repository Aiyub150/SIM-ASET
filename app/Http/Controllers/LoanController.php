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
        $user = Auth::user();
        if (!$user->can('loans.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang.');
        }

        $canViewAllLoans = $user->hasRole('Super Admin') || $user->hasRole('Admin') || (rescue(fn() => $user->can('reports.view'), false) && !$user->hasRole('Staff Logistik'));
        if (!$canViewAllLoans && $loan->user_id !== $user->id) {
            abort(403, 'Akses dilarang.');
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
    public function index(\Illuminate\Http\Request $request)
    {
        $user  = Auth::user();
        if (!$user->can('loans.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk melihat riwayat peminjaman.');
        }

        $search = $request->query('search');
        $status = $request->query('status');

        $query = Loan::with(['borrower', 'user', 'loanItems.item'])->orderBy('created_at', 'desc');

        $canViewAllLoans = $user->hasRole('Super Admin') || $user->hasRole('Admin') || (rescue(fn() => $user->can('reports.view'), false) && !$user->hasRole('Staff Logistik'));
        if (!$canViewAllLoans) {
            $query->where('user_id', $user->id);
        }

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('loan_code', 'like', "%{$search}%")
                     ->orWhere('borrow_date', 'like', "%{$search}%")
                     ->orWhere('due_date', 'like', "%{$search}%")
                     ->orWhereHas('borrower', function ($q2) use ($search) {
                         $q2->where('institution_name', 'like', "%{$search}%")
                            ->orWhere('pic_name', 'like', "%{$search}%");
                     })
                     ->orWhereHas('user', function ($q3) use ($search) {
                         $q3->where('name', 'like', "%{$search}%");
                     })
                     ->orWhereHas('loanItems.item', function ($q4) use ($search) {
                         $q4->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                     });
            });
        })->when($status, function ($q, $status) {
            return $q->where('status', $status);
        });

        $loans = $query->paginate(15)->withQueryString();

        return view('loans.index', compact('loans', 'search', 'status'));
    }

    /**
     * Proses penyimpanan data peminjaman
     */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        try {
            $loan = $this->loanService->createLoan($request->validated(), Auth::id());

            \App\Services\ActivityLogger::log(
                'LOAN_CREATE',
                'Peminjaman',
                "Mencatat peminjaman baru dengan nomor referensi {$loan->loan_code}",
                ['loan_id' => $loan->id, 'loan_code' => $loan->loan_code],
                $request
            );

            return redirect()
                ->route('loans.index')
                ->with('success', "Peminjaman berhasil dicatat dengan kode: {$loan->loan_code}");

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('LoanController@store error: ' . $e->getMessage(), ['exception' => $e]);
            $userMessage = ($e instanceof \Illuminate\Database\QueryException || str_contains($e->getMessage(), 'SQLSTATE'))
                ? 'Terjadi kesalahan sistem saat memproses peminjaman. Silakan coba lagi.'
                : $e->getMessage();

            return back()
                ->withInput()
                ->with('error', $userMessage);
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

            \App\Services\ActivityLogger::log(
                'LOAN_RETURN',
                'Peminjaman',
                "Memproses pengembalian peminjaman barang untuk kode {$loan->loan_code}",
                ['loan_id' => $loan->id, 'loan_code' => $loan->loan_code],
                $request
            );

            $message = $updatedLoan->status === 'completed'
                ? "Pengembalian berhasil. Transaksi {$loan->loan_code} telah SELESAI sepenuhnya."
                : "Pengembalian parsial berhasil dicatat pada transaksi {$loan->loan_code}.";

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', $message);

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('LoanController@returnItems error: ' . $e->getMessage(), ['exception' => $e]);
            $userMessage = ($e instanceof \Illuminate\Database\QueryException || str_contains($e->getMessage(), 'SQLSTATE'))
                ? 'Terjadi kesalahan sistem saat memproses pengembalian. Silakan coba lagi.'
                : $e->getMessage();

            return back()
                ->withInput()
                ->with('error', $userMessage);
        }
    }

    /**
     * Form peminjaman baru
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->can('loans.create') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk mencatat peminjaman baru.');
        }

        $borrowers = Borrower::orderBy('institution_name', 'asc')->get();
        $items     = Item::where('available_qty', '>', 0)->orderBy('name', 'asc')->get();

        return view('loans.create', compact('borrowers', 'items'));
    }

    /**
     * Detail peminjaman — Staff hanya bisa lihat miliknya
     */
    public function show(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->can('loans.view') && !$user->hasAnyRole(['Super Admin', 'Admin', 'Staff Logistik', 'Staff'])) {
            abort(403, 'Akses dilarang.');
        }

        $canViewAllLoans = $user->hasRole('Super Admin') || $user->hasRole('Admin') || (rescue(fn() => $user->can('reports.view'), false) && !$user->hasRole('Staff Logistik'));
        if (!$canViewAllLoans && $loan->user_id !== $user->id) {
            abort(403, 'Akses dilarang.');
        }

        $loan->load(['borrower', 'user', 'loanItems.item']);

        $pendingItems = $loan->loanItems->filter(function ($loanItem) {
            return $loanItem->qty > $loanItem->returned_qty;
        });

        return view('loans.show', compact('loan', 'pendingItems'));
    }
}
