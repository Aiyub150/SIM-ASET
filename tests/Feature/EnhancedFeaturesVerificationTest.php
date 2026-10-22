<?php

namespace Tests\Feature;

use App\Models\Borrower;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnhancedFeaturesVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Borrower $borrower;
    protected Category $category;
    protected Location $location;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@example.com',
        ]);
        $this->superAdmin->assignRole($role);

        $this->borrower = Borrower::create([
            'institution_name' => 'Dinas Kominfo Provinsi',
            'pic_name' => 'Budi Santoso',
            'contact_number' => '081234567890',
            'address' => 'Jl. Pahlawan No. 10',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik & Multimedia',
            'sku_prefix' => 'ELK',
        ]);

        $this->location = Location::create([
            'name' => 'Gudang Pusat Logistik',
            'address' => 'Jl. Pahlawan No. 10',
        ]);

        $this->item = Item::create([
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'name' => 'Proyektor Epson EB-X500',
            'sku' => 'ELK-001',
            'ownership_status' => 'internal',
            'total_qty' => 10,
            'available_qty' => 10,
        ]);
    }

    protected function createFakeJpg(string $filename = 'proof.jpg'): UploadedFile
    {
        $jpgBytes = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB\x00C\x00" . str_repeat("\x00", 64) . "\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\xFF\xC4\x00\x1F\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0B\xFF\xDA\x00\x08\x01\x01\x00\x00?\x00\xBF\x00\xFF\xD9";
        return UploadedFile::fake()->createWithContent($filename, $jpgBytes);
    }

    /**
     * Uji 1: Verifikasi 4 Status Peminjaman di Aksesor Model Loan
     */
    public function test_loan_four_status_accessor_logic()
    {
        $today = Carbon::today();

        // 1. Status Aktif (masih berjalan, belum jatuh tempo)
        $activeLoan = Loan::create([
            'loan_code' => 'PINJ-TEST-001',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(2),
            'due_date' => $today->copy()->addDays(3),
            'status' => 'active',
        ]);
        $this->assertEquals('active', $activeLoan->computed_status);

        // KASUS KRITIS: Data lama berstatus 'completed' di DB tapi ada barang yang belum dikembalikan fisik
        $unreturnedLoan = Loan::create([
            'loan_code' => 'PINJ-TEST-UNRETURNED',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(2),
            'due_date' => $today->copy()->addDays(3),
            'status' => 'completed',
        ]);
        \App\Models\LoanItem::create([
            'loan_id' => $unreturnedLoan->id,
            'item_id' => $this->item->id,
            'qty' => 5,
            'returned_qty' => 0, // Belum ada yang kembali
        ]);
        // Status harus menolak completed dan jatuh ke 'active'
        $this->assertEquals('active', $unreturnedLoan->fresh()->computed_status);

        // 2. Status Terlambat (belum selesai, sudah melewati jatuh tempo)
        $overdueLoan = Loan::create([
            'loan_code' => 'PINJ-TEST-002',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(10),
            'due_date' => $today->copy()->subDays(3),
            'status' => 'active',
        ]);
        $this->assertEquals('overdue', $overdueLoan->computed_status);

        // 3. Status Selesai Tepat Waktu (selesai & return_date <= due_date)
        $completedLoan = Loan::create([
            'loan_code' => 'PINJ-TEST-003',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(7),
            'due_date' => $today->copy()->subDays(2),
            'return_date' => $today->copy()->subDays(3),
            'status' => 'completed',
        ]);
        $this->assertEquals('completed', $completedLoan->computed_status);

        // 4. Status Selesai Terlambat (selesai & return_date > due_date)
        $completedLateLoan = Loan::create([
            'loan_code' => 'PINJ-TEST-004',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(15),
            'due_date' => $today->copy()->subDays(5),
            'return_date' => $today->copy()->subDays(1),
            'status' => 'completed',
        ]);
        $this->assertEquals('completed_late', $completedLateLoan->computed_status);
    }

    /**
     * Uji 2: Warisan Foto Barang Baru ke Kartu Stok Mutasi Masuk Awal (INIT)
     */
    public function test_new_item_creation_inherits_image_to_initial_stock_movement()
    {
        $file = $this->createFakeJpg('laptop_baru.jpg');

        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'ownership_status' => 'internal',
            'name' => 'Laptop Dell Latitude 7420',
            'total_qty' => 5,
            'image' => $file,
        ]);

        $response->assertRedirect(route('items.index'));

        $newItem = Item::where('name', 'Laptop Dell Latitude 7420')->first();
        $this->assertNotNull($newItem);
        $this->assertNotNull($newItem->image);
        Storage::disk('public')->assertExists($newItem->image);

        // Pastikan mutasi stok awal mewarisi foto bukti yang sama persis
        $initMovement = $newItem->stockMovements()->where('type', 'in')->first();
        $this->assertNotNull($initMovement);
        $this->assertEquals($newItem->image, $initMovement->image);
    }

    /**
     * Uji 3: Upload Bukti Awal Peminjaman (borrow_proof_image)
     */
    public function test_upload_borrow_proof_image_endpoint()
    {
        $loan = Loan::create([
            'loan_code' => 'PINJ-PROOF-01',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(5),
            'status' => 'active',
        ]);

        $proofFile = $this->createFakeJpg('bast_awal.jpg');

        $response = $this->actingAs($this->superAdmin)->post(route('loans.borrow-proof', $loan->id), [
            'borrow_proof_image' => $proofFile,
        ]);

        $response->assertSessionHas('success');
        $loan->refresh();
        $this->assertNotNull($loan->borrow_proof_image);
        Storage::disk('public')->assertExists($loan->borrow_proof_image);
    }

    /**
     * Uji 4: Upload Bukti Pengembalian (return_proof_image) Ditolak jika Belum Selesai Lunas
     */
    public function test_upload_return_proof_image_rejected_if_loan_not_fully_returned()
    {
        $loan = Loan::create([
            'loan_code' => 'PINJ-PROOF-02',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => Carbon::today()->subDays(2),
            'due_date' => Carbon::today()->addDays(3),
            'status' => 'active', // Masih belum selesai
        ]);

        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $this->item->id,
            'qty' => 5,
            'returned_qty' => 2, // Masih sisa 3
        ]);

        $proofFile = $this->createFakeJpg('bast_kembali.jpg');

        $response = $this->actingAs($this->superAdmin)->post(route('loans.return-proof', $loan->id), [
            'return_proof_image' => $proofFile,
        ]);

        // Harus ditolak dengan pesan peringatan SweetAlert2
        $response->assertSessionHas('error', 'semua barang harus dikembalikan jika ingin meng-upload gambar pengembalian, silahkan periksa kembali');
        $loan->refresh();
        $this->assertNull($loan->return_proof_image);
    }

    /**
     * Uji 5: Upload Bukti Pengembalian Berhasil jika Pinjaman Sudah Selesai Penuh
     */
    public function test_upload_return_proof_image_succeeds_when_loan_is_completed()
    {
        $loan = Loan::create([
            'loan_code' => 'PINJ-PROOF-03',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => Carbon::today()->subDays(5),
            'due_date' => Carbon::today()->subDays(1),
            'return_date' => Carbon::today(),
            'status' => 'completed',
        ]);

        LoanItem::create([
            'loan_id' => $loan->id,
            'item_id' => $this->item->id,
            'qty' => 5,
            'returned_qty' => 5, // Lunas
        ]);

        $proofFile = $this->createFakeJpg('bast_kembali_lunas.jpg');

        $response = $this->actingAs($this->superAdmin)->post(route('loans.return-proof', $loan->id), [
            'return_proof_image' => $proofFile,
        ]);

        $response->assertSessionHas('success');
        $loan->refresh();
        $this->assertNotNull($loan->return_proof_image);
        Storage::disk('public')->assertExists($loan->return_proof_image);
    }

    /**
     * Uji 6: Matriks Hak Akses Memiliki Sticky Header CSS
     */
    public function test_admin_permissions_page_contains_sticky_header_styles()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin-settings.permissions.index'));
        $response->assertOk();
        $response->assertSee('position: sticky');
        $response->assertSee('max-height: 72vh');
    }

    /**
     * Uji 7: Laporan Rekapitulasi Menampilkan 4 Status Peminjaman
     */
    public function test_reports_index_and_exports_display_four_statuses()
    {
        $today = Carbon::today();

        // 1. Aktif
        Loan::create([
            'loan_code' => 'PINJ-REP-01',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today,
            'due_date' => $today->copy()->addDays(5),
            'status' => 'active',
        ]);

        // 2. Terlambat
        Loan::create([
            'loan_code' => 'PINJ-REP-02',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(10),
            'due_date' => $today->copy()->subDays(2),
            'status' => 'active',
        ]);

        // 3. Selesai Terlambat
        Loan::create([
            'loan_code' => 'PINJ-REP-03',
            'borrower_id' => $this->borrower->id,
            'user_id' => $this->superAdmin->id,
            'borrow_date' => $today->copy()->subDays(12),
            'due_date' => $today->copy()->subDays(5),
            'return_date' => $today->copy()->subDays(1),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('reports.index', [
            'period' => $today->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('Aktif');
        $response->assertSee('Terlambat');
        $response->assertSee('Selesai Terlambat');
    }
}
