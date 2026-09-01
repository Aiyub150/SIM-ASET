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

class LoanController extends Controller
{
    // Inject Service ke dalam Controller
    public function __construct(private LoanService $loanService)
    {
    }

    public function printPdf(Loan $loan)
    {
        // Pastikan semua relasi termuat
        $loan->load(['borrower', 'user', 'loanItems.item']);

        // Load view khusus PDF (kita akan membuatnya setelah ini)
        $pdf = Pdf::loadView('loans.pdf', compact('loan'));
        
        // Format standar dokumen resmi instansi
        $pdf->setPaper('A4', 'portrait');

        // Gunakan stream() agar PDF terbuka di tab baru (preview), bukan langsung download
        return $pdf->stream("BAST_{$loan->loan_code}.pdf");
    }

    /**
     * Menampilkan daftar transaksi peminjaman
     */
    public function index()
    {
        // Eager Loading relasi borrower dan user untuk mencegah N+1 Query.
        // Pagination membatasi memori server hanya memuat 15 baris per halaman.
        $loans = Loan::with(['borrower', 'user'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(15);

        return view('loans.index', compact('loans'));
    }
    
    /**
     * Proses penyimpanan data peminjaman
     */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        try {
            // Karena kita belum membuat fitur Login, kita paksa pakai ID Admin dari Seeder
            // Nanti jika fitur Auth sudah siap, ganti dengan: auth()->id()
            $userId = auth()->id();

            // Kirim data yang SUDAH DIVALIDASI ke Service
            $loan = $this->loanService->createLoan($request->validated(), $userId);

            // Jika sukses, lempar user kembali dengan pesan sukses
            return redirect()
                ->route('loans.index') // Asumsi Anda punya route ini nantinya
                ->with('success', "Peminjaman berhasil dicatat dengan kode: {$loan->loan_code}");

        } catch (Exception $e) {
            // Ini adalah momen di mana Controller menangkap Exception dari Service.
            // Kita kembalikan user ke form sebelumnya (back), bawa input lamanya (withInput),
            // dan tampilkan pesan error dari Exception (with error).
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Proses pengembalian barang dari transaksi peminjaman
     */
    public function returnItems(ReturnLoanRequest $request, Loan $loan): RedirectResponse
    {
        try {
            // Hardcode ID Admin untuk sementara sampai fitur Login/Auth dibuat
            $userId = auth()->id();

            // Kirim ID Transaksi dan data yang tervalidasi ke Service
            $updatedLoan = $this->loanService->processReturn($loan->id, $request->validated(), $userId);

            // Berikan respons yang dinamis berdasarkan status akhir transaksi
            $message = $updatedLoan->status === 'completed' 
                ? "Pengembalian berhasil. Transaksi {$loan->loan_code} telah SELESAI sepenuhnya."
                : "Pengembalian parsial berhasil dicatat pada transaksi {$loan->loan_code}.";

            return redirect()
                ->route('loans.show', $loan->id) // Asumsi kita akan kembali ke halaman detail
                ->with('success', $message);

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman form peminjaman
     */
    public function create()
    {
        // Hanya ambil barang yang stok tersedianya lebih dari 0
        $borrowers = Borrower::orderBy('institution_name', 'asc')->get();
        $items = Item::where('available_qty', '>', 0)->orderBy('name', 'asc')->get();

        return view('loans.create', compact('borrowers', 'items'));
    }

    /**
     * Menampilkan detail peminjaman dan form pengembalian
     */
    public function show(Loan $loan)
    {
        // Wajib Eager Load relasi agar view tidak melakukan query berulang-ulang
        $loan->load(['borrower', 'user', 'loanItems.item']);

        // Filter hanya item yang masih punya sisa hutang (qty > returned_qty)
        $pendingItems = $loan->loanItems->filter(function ($loanItem) {
            return $loanItem->qty > $loanItem->returned_qty;
        });

        return view('loans.show', compact('loan', 'pendingItems'));
    }
}