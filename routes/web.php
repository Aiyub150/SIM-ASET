<?php
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Halaman utama langsung diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Lapisan Pertahanan 1: Wajib Login
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::view('/settings/version', 'settings.version')->name('settings.version');
    // ── MODUL PEMINJAMAN ──────────────────────────────────────────────
    // Semua user login bisa akses, tapi Staff hanya lihat miliknya sendiri
    // (filter dilakukan di LoanController berdasarkan role)
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/',               [LoanController::class, 'index'])->name('index');
        Route::get('/create',         [LoanController::class, 'create'])->name('create');
        Route::post('/',              [LoanController::class, 'store'])->name('store');
        Route::get('/{loan}',         [LoanController::class, 'show'])->name('show');
        Route::get('/{loan}/print',   [LoanController::class, 'printPdf'])->name('print');
        Route::post('/{loan}/return', [LoanController::class, 'returnItems'])->name('return');
    });

    Route::get('/items/lookup', [ItemController::class, 'lookupBySku'])->name('items.lookup');
    Route::get('/items/{item}/label', [ItemController::class, 'printLabel'])->name('items.label');

    // MODUL MASTER BARANG (VIEW) - Accessible by all roles
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // ── MODUL OPERASIONAL (Admin & Super Admin) ───────────────────────
    Route::middleware(['role:Super Admin|Admin'])->group(function () {

        // MODUL MASTER BARANG (CREATE/EDIT)
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/create',      [ItemController::class, 'create'])->name('create');
            Route::post('/',           [ItemController::class, 'store'])->name('store');
            Route::get('/labels',      [ItemController::class, 'printLabels'])->name('labels');
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::get('/{item}/label', [ItemController::class, 'printLabel'])->name('label');
            Route::put('/{item}',      [ItemController::class, 'update'])->name('update');
        });

        // MODUL MASTER INSTANSI
        Route::prefix('borrowers')->name('borrowers.')->group(function () {
            Route::get('/',                [BorrowerController::class, 'index'])->name('index');
            Route::get('/create',          [BorrowerController::class, 'create'])->name('create');
            Route::post('/',               [BorrowerController::class, 'store'])->name('store');
            Route::get('/{borrower}/edit', [BorrowerController::class, 'edit'])->name('edit');
            Route::put('/{borrower}',      [BorrowerController::class, 'update'])->name('update');
        });

        // MODUL MUTASI STOK / LEDGER
        Route::prefix('stocks')->name('stocks.')->group(function () {
            Route::get('/',        [StockMovementController::class, 'index'])->name('index');
            Route::get('/create',  [StockMovementController::class, 'create'])->name('create');
            Route::post('/',       [StockMovementController::class, 'store'])->name('store');
        });
    });

    // MODUL LAPORAN (Accessible by Staff, Admin, Super Admin)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',            [ReportController::class, 'index'])->name('index');
        Route::get('/export-pdf',  [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // ── MODUL USER MANAGEMENT (Super Admin saja) ─────────────────────
    Route::middleware(['role:Super Admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/',            [UserController::class, 'index'])->name('index');
        Route::get('/create',      [UserController::class, 'create'])->name('create');
        Route::post('/',           [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',      [UserController::class, 'update'])->name('update');
    });

    // ── MODUL KATEGORI (Super Admin & Admin) ────────────────────────────────
    Route::middleware(['role:Super Admin|Admin'])->prefix('categories')->name('categories.')->group(function () {
        Route::get('/',            [CategoryController::class, 'index'])->name('index');
        Route::post('/',           [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}',  [CategoryController::class, 'update'])->name('update');
        Route::patch('/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('toggle');
    });

});

// Jangan hapus baris ini, ini milik Laravel Breeze untuk memuat route login, register, dll
require __DIR__.'/auth.php';
