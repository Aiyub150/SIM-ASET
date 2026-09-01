<?php
use App\Http\Controllers\LoanController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

// Halaman utama langsung diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Lapisan Pertahanan 1: Wajib Login
Route::middleware(['auth'])->group(function () {

    // (Opsional) Route bawaan Breeze setelah login sukses
    Route::get('/dashboard', function () {
        return redirect()->route('loans.index');
    })->name('dashboard');

    // MODUL PEMINJAMAN
    // Dapat diakses oleh semua user yang login (Staff maupun Super Admin)
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/create', [LoanController::class, 'create'])->name('create');
        Route::get('/{loan}', [LoanController::class, 'show'])->name('show');
        
        // Route untuk Cetak PDF
        Route::get('/{loan}/print', [LoanController::class, 'printPdf'])->name('print');
        
        Route::post('/', [LoanController::class, 'store'])->name('store');
        Route::post('/{loan}/return', [LoanController::class, 'returnItems'])->name('return');
    });
    // Lapisan Pertahanan 2: Wajib memiliki Role 'Super Admin'
    Route::middleware(['role:Super Admin'])->group(function () {
        
        // Route Mutasi Stok...
        Route::prefix('stocks')->name('stocks.')->group(function () {
            // ... (kode stocks sebelumnya)
        });

        // Route Laporan Bulanan
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        });

    });

    // MODUL MUTASI STOK / LEDGER
    Route::middleware(['role:Super Admin'])->prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/create', [StockMovementController::class, 'create'])->name('create');
        Route::post('/', [StockMovementController::class, 'store'])->name('store'); 
    });

});

// Jangan hapus baris ini, ini milik Laravel Breeze untuk memuat route login, register, dll
require __DIR__.'/auth.php';