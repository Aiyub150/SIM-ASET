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

// Halaman Buku Panduan Penggunaan & UI/UX (Dapat diakses publik maupun oleh user login)
Route::view('/guide', 'guide')->name('guide');
Route::get('/guide/pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions([
        'isRemoteEnabled' => false,
        'chroot' => [public_path()],
    ])->loadView('guide_pdf');
    $pdf->setPaper('A4', 'portrait');
    return $pdf->stream('Buku_Panduan_SIM-ASET_2026.pdf');
})->middleware('throttle:reports.export')->name('guide.pdf');

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
        Route::get('/create',         [LoanController::class, 'create'])->middleware(['role_or_permission:Super Admin|Admin|Staff Logistik|loans.create'])->name('create');
        Route::post('/',              [LoanController::class, 'store'])->middleware(['role_or_permission:Super Admin|Admin|Staff Logistik|loans.create', 'throttle:30,1'])->name('store');
        Route::get('/{loan}',         [LoanController::class, 'show'])->name('show');
        Route::get('/{loan}/print',   [LoanController::class, 'printPdf'])->middleware('throttle:20,1')->name('print');
        Route::post('/{loan}/return', [LoanController::class, 'returnItems'])->middleware(['role_or_permission:Super Admin|Admin|Staff Logistik|loans.return', 'throttle:30,1'])->name('return');
        Route::post('/{loan}/borrow-proof', [LoanController::class, 'uploadBorrowProof'])->middleware('throttle:30,1')->name('borrow-proof');
        Route::post('/{loan}/return-proof', [LoanController::class, 'uploadReturnProof'])->middleware('throttle:30,1')->name('return-proof');
    });

    Route::get('/items/lookup', [ItemController::class, 'lookupBySku'])->middleware('throttle:60,1')->name('items.lookup');
    Route::get('/items/labels', [ItemController::class, 'printLabels'])->middleware('throttle:30,1')->name('items.labels');
    Route::get('/items/{item}/label', [ItemController::class, 'printLabel'])->middleware('throttle:30,1')->name('items.label');

    // MODUL MASTER BARANG (VIEW) - Accessible by all roles with items.view
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // MODUL MASTER BARANG (CREATE/EDIT)
    Route::prefix('items')->name('items.')->group(function () {
        Route::middleware(['role_or_permission:Super Admin|Admin|items.create'])->group(function () {
            Route::get('/create',      [ItemController::class, 'create'])->name('create');
            Route::post('/',           [ItemController::class, 'store'])->middleware('throttle:30,1')->name('store');
        });
        Route::middleware(['role_or_permission:Super Admin|Admin|items.edit'])->group(function () {
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}',      [ItemController::class, 'update'])->middleware('throttle:30,1')->name('update');
            Route::post('/{item}/quick-image', [ItemController::class, 'quickUpdateImage'])->name('quick-image');
        });
    });

    // MODUL MASTER INSTANSI
    Route::prefix('borrowers')->name('borrowers.')->group(function () {
        Route::get('/',                [BorrowerController::class, 'index'])->middleware(['role_or_permission:Super Admin|Admin|borrowers.view'])->name('index');
        Route::middleware(['role_or_permission:Super Admin|Admin|borrowers.create'])->group(function () {
            Route::get('/create',          [BorrowerController::class, 'create'])->name('create');
            Route::post('/',               [BorrowerController::class, 'store'])->middleware('throttle:30,1')->name('store');
        });
        Route::middleware(['role_or_permission:Super Admin|Admin|borrowers.edit'])->group(function () {
            Route::get('/{borrower}/edit', [BorrowerController::class, 'edit'])->name('edit');
            Route::put('/{borrower}',      [BorrowerController::class, 'update'])->middleware('throttle:30,1')->name('update');
        });
    });

    // GEOCODING PROXY (Internal OpenStreetMap Nominatim with caching & rate limit protection)
    Route::prefix('api/geocoding')->name('geocoding.')->group(function () {
        Route::get('/search',  [\App\Http\Controllers\GeocodingController::class, 'search'])->name('search');
        Route::get('/reverse', [\App\Http\Controllers\GeocodingController::class, 'reverse'])->name('reverse');
    });

    // MODUL MUTASI STOK / LEDGER
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/',        [StockMovementController::class, 'index'])->middleware(['role_or_permission:Super Admin|Admin|stocks.view'])->name('index');
        Route::middleware(['role_or_permission:Super Admin|Admin|stocks.adjust'])->group(function () {
            Route::get('/create',            [StockMovementController::class, 'create'])->name('create');
            Route::post('/',                 [StockMovementController::class, 'store'])->middleware('throttle:30,1')->name('store');
            Route::post('/{movement}/proof', [StockMovementController::class, 'uploadProof'])->middleware('throttle:30,1')->name('proof');
        });
    });

    // MODUL LAPORAN (Accessible by Staff, Admin, Super Admin)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',             [ReportController::class, 'index'])->name('index');
        Route::get('/export-pdf',   [ReportController::class, 'exportPdf'])->middleware('throttle:reports.export')->name('export-pdf');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->middleware('throttle:reports.export')->name('export-excel');
        Route::get('/export-csv',   [ReportController::class, 'exportCsv'])->middleware('throttle:reports.export')->name('export-csv');
    });

    // ── MODUL USER MANAGEMENT (Super Admin & Izin users.view) ────────
    Route::middleware(['role_or_permission:Super Admin|users.view'])->prefix('users')->name('users.')->group(function () {
        Route::get('/',            [UserController::class, 'index'])->name('index');
        Route::get('/create',      [UserController::class, 'create'])->name('create');
        Route::post('/',           [UserController::class, 'store'])->middleware('throttle:30,1')->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',      [UserController::class, 'update'])->middleware('throttle:30,1')->name('update');
    });

    // ── MODUL ADMINISTRATION SETTINGS (RBAC Dinamis & Granular) ──────
    Route::prefix('admin-settings')->name('admin-settings.')->group(function () {
        // 1. Log History
        Route::get('/log-history', [\App\Http\Controllers\SuperAdminController::class, 'logHistory'])
            ->middleware(['role_or_permission:Super Admin|audit.view'])
            ->name('log-history');

        // 2. Manajemen Role
        Route::middleware(['role_or_permission:Super Admin|roles.manage'])->group(function () {
            Route::get('/roles',                             [\App\Http\Controllers\RolePermissionController::class, 'indexRoles'])->name('roles.index');
            Route::post('/roles',                            [\App\Http\Controllers\RolePermissionController::class, 'storeRole'])->middleware('throttle:30,1')->name('roles.store');
            Route::get('/roles/{role}/permissions',          [\App\Http\Controllers\RolePermissionController::class, 'editRolePermissions'])->name('roles.permissions.edit');
            Route::put('/roles/{role}/permissions',          [\App\Http\Controllers\RolePermissionController::class, 'updateRolePermissions'])->middleware('throttle:30,1')->name('roles.permissions.update');
            Route::put('/roles/{role}',                      [\App\Http\Controllers\RolePermissionController::class, 'updateRole'])->middleware('throttle:30,1')->name('roles.update');
            Route::delete('/roles/{role}',                   [\App\Http\Controllers\RolePermissionController::class, 'destroyRole'])->middleware('throttle:30,1')->name('roles.destroy');
        });

        // 3. Manajemen Hak Akses (Permissions)
        Route::middleware(['role_or_permission:Super Admin|permissions.manage'])->group(function () {
            Route::get('/permissions',                   [\App\Http\Controllers\RolePermissionController::class, 'indexPermissions'])->name('permissions.index');
            Route::put('/permissions/roles/{role}',      [\App\Http\Controllers\RolePermissionController::class, 'updateRolePermissions'])->middleware('throttle:30,1')->name('permissions.roles.update');
            Route::put('/permissions/users/{user}',      [\App\Http\Controllers\RolePermissionController::class, 'updateUserPermissions'])->middleware('throttle:30,1')->name('permissions.users.update');
        });

        // 4. Manajemen Format Kode & SKU
        Route::middleware(['role_or_permission:Super Admin|sku.manage'])->group(function () {
            Route::get('/code-formats',                  [\App\Http\Controllers\CodeFormatSettingController::class, 'index'])->name('code-formats.index');
            Route::put('/code-formats/{setting}',        [\App\Http\Controllers\CodeFormatSettingController::class, 'update'])->middleware('throttle:30,1')->name('code-formats.update');
        });
    });

    // ── ALIAS UNTUK SUPERADMIN PREFIX (Backward Compatibility Route & Test) ──
    Route::middleware(['role_or_permission:Super Admin|audit.view'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/log-history', [\App\Http\Controllers\SuperAdminController::class, 'logHistory'])->name('log-history');
    });

    // ── MODUL KATEGORI ────────────────────────────────
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/',            [CategoryController::class, 'index'])->middleware(['role_or_permission:Super Admin|Admin|categories.view'])->name('index');
        Route::post('/',           [CategoryController::class, 'store'])->middleware(['role_or_permission:Super Admin|Admin|categories.create', 'throttle:30,1'])->name('store');
        Route::put('/{category}',  [CategoryController::class, 'update'])->middleware(['role_or_permission:Super Admin|Admin|categories.edit', 'throttle:30,1'])->name('update');
        Route::patch('/{category}/toggle', [CategoryController::class, 'toggleStatus'])->middleware(['role_or_permission:Super Admin|Admin|categories.toggle', 'throttle:30,1'])->name('toggle');
    });

});

// Jangan hapus baris ini, ini milik Laravel Breeze untuk memuat route login, register, dll
require __DIR__.'/auth.php';
