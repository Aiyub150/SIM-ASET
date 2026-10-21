<?php

namespace Tests\Feature;

use App\Models\Borrower;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomRoleScopingAndSidebarGatingTest extends TestCase
{
    use RefreshDatabase;

    protected User $bupatiUser;
    protected Role $bupatiRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat permissions inti
        $permissions = [
            'items.view', 'items.create', 'items.edit', 'items.delete',
            'borrowers.view', 'borrowers.create', 'borrowers.edit', 'borrowers.delete',
            'loans.view', 'loans.create', 'loans.return',
            'stocks.view', 'stocks.adjust',
            'categories.view', 'categories.create', 'categories.edit', 'categories.toggle',
            'reports.view', 'reports.export',
            'users.view', 'roles.manage', 'permissions.manage', 'audit.view', 'sku.manage'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Staff Logistik', 'guard_name' => 'web']);

        // Buat role khusus "bupati" tanpa izin sama sekali (0 permission)
        $this->bupatiRole = Role::firstOrCreate(['name' => 'bupati', 'guard_name' => 'web']);

        $this->bupatiUser = User::factory()->create([
            'name' => 'Pawait Bupati',
            'email' => 'pawait@bupati.id',
        ]);
        $this->bupatiUser->assignRole($this->bupatiRole);

        // Buat data item dummy
        $cat = Category::create(['name' => 'Elektronik']);
        $loc = \App\Models\Location::create(['name' => 'Gudang Pusat']);
        Item::create([
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'name' => 'MacBook Pro M3 Max',
            'sku' => 'ITM-001',
            'total_qty' => 10,
            'available_qty' => 10,
        ]);
    }

    public function test_custom_role_with_zero_permissions_has_clean_minimal_sidebar_and_scoped_dashboard(): void
    {
        $response = $this->actingAs($this->bupatiUser)->get('/dashboard');

        $response->assertOk();

        // 1. Sidebar checks: Menu operasional & manajemen HARUS tersembunyi
        $response->assertDontSee(route('loans.index'));
        $response->assertDontSee(route('borrowers.index'));
        $response->assertDontSee(route('items.index'));
        $response->assertDontSee(route('stocks.index'));
        $response->assertDontSee(route('reports.index'));
        $response->assertDontSee(route('categories.index'));
        $response->assertDontSee(route('users.index'));
        $response->assertDontSee(route('admin-settings.roles.index'));
        $response->assertDontSee(route('admin-settings.permissions.index'));
        $response->assertDontSee(route('admin-settings.log-history'));
        $response->assertDontSee(route('admin-settings.code-formats.index'));
        $response->assertDontSee('id="adminSettingsCollapse"', false);

        // Menu personal & bantuan HARUS tetap ada
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('profile.edit'));
        $response->assertSee(route('settings.version'));
        $response->assertSee(route('guide'));
        $response->assertSee('Dashboard');
        $response->assertSee('Edit Profil');
        $response->assertSee('Informasi Versi');
        $response->assertSee('Panduan Penggunaan');

        // 2. Dashboard content checks:
        // Tidak boleh membocorkan total agregat inventaris instansi daerah
        $response->assertDontSee('Stok Fisik Keseluruhan');
        $response->assertSee('Peran Akses');
        $response->assertSee('bupati');
        $response->assertSee('Total Transaksi Saya');
        $response->assertSee('Belum ada transaksi peminjaman atas nama Anda.');

        // Banner button checks
        $response->assertDontSee('Peminjaman Baru');
        $response->assertDontSee('Peminjaman Saya');
    }

    public function test_custom_role_with_zero_permissions_is_forbidden_from_all_direct_module_urls(): void
    {
        $this->actingAs($this->bupatiUser)->get('/loans')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/loans/create')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/items')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/reports')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/reports/export-pdf')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/borrowers')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/stocks')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/categories')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/users')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/admin-settings/roles')->assertForbidden();
    }

    public function test_granting_items_view_permission_dynamically_opens_master_barang_for_custom_role(): void
    {
        // Berikan izin items.view kepada role bupati
        $this->bupatiRole->givePermissionTo('items.view');

        $dashResponse = $this->actingAs($this->bupatiUser)->get('/dashboard');
        $dashResponse->assertOk();

        // Sekarang sidebar menampilkan Master Data & Master Barang
        $dashResponse->assertSee('Master Data');
        $dashResponse->assertSee('Master Barang');

        // Di dashboard sekarang menampilkan kartu Total Barang dan Stok Fisik
        $dashResponse->assertSee('Total Barang');
        $dashResponse->assertSee('Stok Fisik Keseluruhan');

        // Menu Transaksi dan Laporan tetap tersembunyi
        $dashResponse->assertDontSee('Transaksi');
        $dashResponse->assertDontSee('Laporan & Analitik');

        // Route /items sekarang bisa diakses
        $itemsResponse = $this->actingAs($this->bupatiUser)->get('/items');
        $itemsResponse->assertOk();
        $itemsResponse->assertSee('MacBook Pro M3 Max');

        // Modul lain tetap 403 Forbidden
        $this->actingAs($this->bupatiUser)->get('/loans')->assertForbidden();
        $this->actingAs($this->bupatiUser)->get('/reports')->assertForbidden();
    }

    public function test_read_only_role_with_view_permissions_hides_all_create_and_mutation_buttons(): void
    {
        // Berikan HANYA izin view/baca kepada role bupati (simulasi audit read-only)
        $this->bupatiRole->givePermissionTo([
            'items.view',
            'borrowers.view',
            'loans.view',
            'stocks.view',
            'reports.view',
            'categories.view',
        ]);

        // 1. Modul Transaksi Peminjaman (/loans)
        $loansIndex = $this->actingAs($this->bupatiUser)->get('/loans');
        $loansIndex->assertOk();
        $loansIndex->assertDontSee('Buat Peminjaman');

        // Detail Peminjaman (/loans/{id})
        $borrower = Borrower::create([
            'institution_name' => 'Dinas Pendidikan',
            'pic_name' => 'Budi Santoso',
            'contact_number' => '081234567890',
        ]);
        $loan = Loan::create([
            'loan_code' => 'PINJ-202610-001',
            'user_id' => $this->bupatiUser->id,
            'borrower_id' => $borrower->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'active',
        ]);
        $item = Item::first();
        \App\Models\LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $item->id,
            'qty' => 2,
            'returned_qty' => 0,
        ]);

        $loanShow = $this->actingAs($this->bupatiUser)->get('/loans/' . $loan->id);
        $loanShow->assertOk();
        $loanShow->assertDontSee('Scan Barcode Pengembalian');
        $loanShow->assertDontSee('Proses Pengembalian Barang');
        $loanShow->assertDontSee('Eksekusi Pengembalian');
        $loanShow->assertDontSee('return-sku-input');
        // Namun tetap bisa melihat rincian barang & unduh PDF surat bukti
        $loanShow->assertSee('MacBook Pro M3 Max');
        $loanShow->assertSee('Cetak BAST');

        // 2. Modul Master Data Barang (/items)
        $itemsIndex = $this->actingAs($this->bupatiUser)->get('/items');
        $itemsIndex->assertOk();
        $itemsIndex->assertDontSee('Tambah Barang');

        // 3. Modul Data Instansi (/borrowers)
        $borrowersIndex = $this->actingAs($this->bupatiUser)->get('/borrowers');
        $borrowersIndex->assertOk();
        $borrowersIndex->assertDontSee('Tambah Instansi');
        $borrowersIndex->assertDontSee('>Edit<', false);

        // 4. Modul Kartu Stok (/stocks)
        $stocksIndex = $this->actingAs($this->bupatiUser)->get('/stocks');
        $stocksIndex->assertOk();
        $stocksIndex->assertDontSee('Input Mutasi Baru');

        // 5. Modul Manajemen Kategori (/categories)
        $categoriesIndex = $this->actingAs($this->bupatiUser)->get('/categories');
        $categoriesIndex->assertOk();
        $categoriesIndex->assertDontSee('+ Tambah Kategori');
        $categoriesIndex->assertDontSee('>Edit<', false);
        $categoriesIndex->assertDontSee('>Nonaktifkan<', false);

        // 6. Modul Laporan (/reports)
        $reportsIndex = $this->actingAs($this->bupatiUser)->get('/reports');
        $reportsIndex->assertOk();
        $reportsIndex->assertDontSee('Ekspor PDF');
    }
}
