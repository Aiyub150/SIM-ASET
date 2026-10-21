<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockProofAndImmutableLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Setup role & permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleStaff = Role::firstOrCreate(['name' => 'Staff']);

        $permStockView = Permission::firstOrCreate(['name' => 'stocks.view']);
        $permStockAdjust = Permission::firstOrCreate(['name' => 'stocks.adjust']);

        $roleAdmin->syncPermissions([$permStockView, $permStockAdjust]);
        $roleStaff->syncPermissions([$permStockView]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($roleAdmin);

        $this->operator = User::factory()->create();
        $this->operator->assignRole($roleStaff);

        $category = Category::create([
            'name' => 'Elektronik & IT',
            'code' => 'ELK',
        ]);

        $location = \App\Models\Location::create([
            'name' => 'Gudang Utama',
            'code' => 'GDG-01',
        ]);

        $this->item = Item::create([
            'name' => 'Laptop Lenovo ThinkPad',
            'sku' => 'SIM-ELK-001',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'unit' => 'Unit',
            'total_qty' => 10,
            'available_qty' => 10,
            'ownership_status' => 'internal',
        ]);
    }

    protected function createFakeJpg(string $filename = 'proof.jpg'): UploadedFile
    {
        $jpgBytes = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB\x00C\x00\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x03\x01\x22\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x1F\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00?\x00\xBF\x00\xFF\xD9";
        return UploadedFile::fake()->createWithContent($filename, $jpgBytes);
    }

    public function test_can_record_stock_movement_with_proof_image(): void
    {
        $file = $this->createFakeJpg('bast_serah_terima.jpg');

        $response = $this->actingAs($this->admin)->post(route('stocks.store'), [
            'reference_code' => 'BAST/2026/10/001',
            'item_id' => $this->item->id,
            'type' => 'in',
            'qty' => 5,
            'notes' => 'Penerimaan tambahan laptop dari pengadaan APBD',
            'image' => $file,
        ]);

        $response->assertRedirect(route('stocks.index'));
        $response->assertSessionHas('success');

        $movement = StockMovement::where('reference_code', 'BAST/2026/10/001')->first();
        $this->assertNotNull($movement);
        $this->assertEquals(10, $movement->balance_before);
        $this->assertEquals(15, $movement->balance_after);
        $this->assertNotNull($movement->image);
        Storage::disk('public')->assertExists($movement->image);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'STOCK_ADJUST',
            'module' => 'Mutasi Stok',
        ]);
    }

    public function test_can_record_stock_movement_without_image(): void
    {
        $response = $this->actingAs($this->admin)->post(route('stocks.store'), [
            'reference_code' => 'BAST/2026/10/002',
            'item_id' => $this->item->id,
            'type' => 'out',
            'qty' => 3,
            'notes' => 'Distribusi ke ruang rapat sekretariat',
        ]);

        $response->assertRedirect(route('stocks.index'));

        $movement = StockMovement::where('reference_code', 'BAST/2026/10/002')->first();
        $this->assertNotNull($movement);
        $this->assertEquals(10, $movement->balance_before);
        $this->assertEquals(7, $movement->balance_after);
        $this->assertNull($movement->image);
    }

    public function test_can_upload_late_proof_image_without_altering_immutable_ledger(): void
    {
        // 1. Catat mutasi tanpa foto
        $movement = StockMovement::create([
            'reference_code' => 'BAST/2026/10/003',
            'item_id' => $this->item->id,
            'user_id' => $this->admin->id,
            'type' => 'in',
            'qty' => 4,
            'balance_before' => 10,
            'balance_after' => 14,
            'notes' => 'Lupa upload foto saat input',
            'image' => null,
        ]);

        // 2. Upload susulan foto bukti
        $file = $this->createFakeJpg('bukti_susulan.jpg');

        $response = $this->actingAs($this->admin)->post(route('stocks.proof', $movement), [
            'image' => $file,
        ]);

        $response->assertSessionHas('success');

        // 3. Verifikasi gambar tersimpan
        $movement->refresh();
        $this->assertNotNull($movement->image);
        Storage::disk('public')->assertExists($movement->image);

        // 4. Verifikasi saldo & data kuantitas buku besar 100% UTUH / TIDAK TERUBAH
        $this->assertEquals(4, $movement->qty);
        $this->assertEquals(10, $movement->balance_before);
        $this->assertEquals(14, $movement->balance_after);
        $this->assertEquals('in', $movement->type);
        $this->assertEquals('BAST/2026/10/003', $movement->reference_code);

        // 5. Verifikasi log aktivitas tercatat
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'UPLOAD_PROOF',
            'module' => 'Mutasi Stok',
        ]);
    }

    public function test_late_upload_rejects_non_image_files(): void
    {
        $movement = StockMovement::create([
            'reference_code' => 'BAST/2026/10/004',
            'item_id' => $this->item->id,
            'user_id' => $this->admin->id,
            'type' => 'out',
            'qty' => 2,
            'balance_before' => 10,
            'balance_after' => 8,
            'notes' => 'Pengeluaran',
        ]);

        $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->admin)->post(route('stocks.proof', $movement), [
            'image' => $badFile,
        ]);

        $response->assertSessionHasErrors('image');
        $movement->refresh();
        $this->assertNull($movement->image);
    }

    public function test_unauthorized_user_cannot_upload_proof(): void
    {
        $movement = StockMovement::create([
            'reference_code' => 'BAST/2026/10/005',
            'item_id' => $this->item->id,
            'user_id' => $this->admin->id,
            'type' => 'out',
            'qty' => 1,
            'balance_before' => 10,
            'balance_after' => 9,
        ]);

        $file = $this->createFakeJpg('bukti.jpg');

        $response = $this->actingAs($this->operator)->post(route('stocks.proof', $movement), [
            'image' => $file,
        ]);

        $response->assertForbidden();
    }
}
