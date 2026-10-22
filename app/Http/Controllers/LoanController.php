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
            $data = $request->validated();

            if ($request->hasFile('borrow_proof_image')) {
                $data['borrow_proof_image'] = $request->file('borrow_proof_image')->store('images/loans/proofs', 'public');
            }

            $loan = $this->loanService->createLoan($data, Auth::id());

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
                ->route('loans.index')
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
        $items     = Item::with('category')->where('available_qty', '>', 0)->orderBy('name', 'asc')->get();

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

        $loan->load(['borrower', 'user', 'loanItems.item.category']);

        $pendingItems = $loan->loanItems->filter(function ($loanItem) {
            return $loanItem->qty > $loanItem->returned_qty;
        });

        return view('loans.show', compact('loan', 'pendingItems'));
    }

    /**
     * Upload Bukti Peminjaman Awal Susulan
     */
    public function uploadBorrowProof(\Illuminate\Http\Request $request, Loan $loan): RedirectResponse
    {
        $request->validate([
            'borrow_proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'borrow_proof_image.required' => 'Pilih file bukti peminjaman terlebih dahulu.',
            'borrow_proof_image.image'    => 'File yang dipilih harus berupa gambar.',
            'borrow_proof_image.mimes'    => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'borrow_proof_image.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $path = $request->file('borrow_proof_image')->store('images/loans/proofs', 'public');
        $loan->update(['borrow_proof_image' => $path]);

        // Hubungkan juga ke stock movements out terkait jika belum punya gambar
        \App\Models\StockMovement::where('reference_code', $loan->loan_code)
            ->where('type', 'out')
            ->whereNull('image')
            ->update(['image' => $path]);

        \App\Services\ActivityLogger::log(
            'UPDATE',
            'Peminjaman',
            "Mengunggah bukti foto awal peminjaman untuk transaksi {$loan->loan_code}",
            ['loan_id' => $loan->id, 'loan_code' => $loan->loan_code, 'image' => $path],
            $request
        );

        return redirect()->route('loans.index')->with('success', "Bukti foto peminjaman untuk {$loan->loan_code} berhasil diunggah.");
    }

    /**
     * Upload Bukti Pengembalian Susulan (HANYA JIKA SELURUH BARANG SUDAH SELESAI DIKEMBALIKAN)
     */
    public function uploadReturnProof(\Illuminate\Http\Request $request, Loan $loan): RedirectResponse
    {
        // Validasi Kritis: semua barang harus sudah dikembalikan
        $hasPending = $loan->loanItems()->whereColumn('returned_qty', '<', 'qty')->exists();
        if ($hasPending || $loan->status !== 'completed') {
            return redirect()->route('loans.index')->with('error', 'semua barang harus dikembalikan jika ingin meng-upload gambar pengembalian, silahkan periksa kembali');
        }

        $request->validate([
            'return_proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'return_proof_image.required' => 'Pilih file bukti pengembalian terlebih dahulu.',
            'return_proof_image.image'    => 'File yang dipilih harus berupa gambar.',
            'return_proof_image.mimes'    => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'return_proof_image.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $path = $request->file('return_proof_image')->store('images/loans/proofs', 'public');
        $loan->update(['return_proof_image' => $path]);

        // Hubungkan juga ke stock movements in terkait jika belum punya gambar
        \App\Models\StockMovement::where('reference_code', $loan->loan_code)
            ->where('type', 'in')
            ->whereNull('image')
            ->update(['image' => $path]);

        \App\Services\ActivityLogger::log(
            'UPDATE',
            'Peminjaman',
            "Mengunggah bukti foto pengembalian lengkap untuk transaksi {$loan->loan_code}",
            ['loan_id' => $loan->id, 'loan_code' => $loan->loan_code, 'image' => $path],
            $request
        );

        return redirect()->route('loans.index')->with('success', "Bukti foto pengembalian untuk {$loan->loan_code} berhasil diunggah.");
    }
}
