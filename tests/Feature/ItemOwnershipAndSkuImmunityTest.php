<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CodeFormatSetting;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ItemOwnershipAndSkuImmunityTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($role);

        $this->category = Category::create([
            'name' => 'Elektronik & TI',
            'sku_prefix' => 'ELK',
            'is_active' => true,
        ]);

        Location::create([
            'name' => 'Gudang Utama',
            'building' => 'Gedung A',
        ]);
    }

    public function test_internal_item_auto_generates_sku(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'ownership_status' => 'internal',
            'name' => 'Laptop Dell Latitude',
        ]);

        $response->assertRedirect(route('items.index'));

        $item = Item::where('name', 'Laptop Dell Latitude')->first();
        $this->assertNotNull($item);
        $this->assertEquals('internal', $item->ownership_status);
        $this->assertNull($item->owner_name);
        $this->assertStringStartsWith('ELK-', $item->sku);
    }

    public function test_vendor_item_requires_owner_name_and_preserves_custom_sku(): void
    {
        // Gagal jika owner_name kosong
        $failResponse = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'ownership_status' => 'vendor',
            'name' => 'Mesin Fotokopi Fuji Xerox',
            'sku' => 'FX-RENT-9988',
            'owner_name' => '',
        ]);
        $failResponse->assertSessionHasErrors(['owner_name']);

        // Berhasil dengan data valid
        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'ownership_status' => 'vendor',
            'name' => 'Mesin Fotokopi Fuji Xerox',
            'sku' => 'FX-RENT-9988',
            'owner_name' => 'PT Astra Graphia Tbk',
        ]);

        $response->assertRedirect(route('items.index'));

        $item = Item::where('name', 'Mesin Fotokopi Fuji Xerox')->first();
        $this->assertNotNull($item);
        $this->assertEquals('vendor', $item->ownership_status);
        $this->assertEquals('PT Astra Graphia Tbk', $item->owner_name);
        $this->assertEquals('FX-RENT-9988', $item->sku);
    }

    public function test_external_agency_item_preserves_custom_sku(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'ownership_status' => 'external',
            'name' => 'Server Rack Titipan Diskominfo',
            'sku' => 'KIB-E-2026-042',
            'owner_name' => 'Dinas Komunikasi dan Informatika',
        ]);

        $response->assertRedirect(route('items.index'));

        $item = Item::where('name', 'Server Rack Titipan Diskominfo')->first();
        $this->assertNotNull($item);
        $this->assertEquals('external', $item->ownership_status);
        $this->assertEquals('Dinas Komunikasi dan Informatika', $item->owner_name);
        $this->assertEquals('KIB-E-2026-042', $item->sku);
    }

    public function test_legacy_sku_synchronization_has_immunity_for_vendor_and_external_items(): void
    {
        // 1. Buat barang internal
        $internalItem = Item::create([
            'category_id' => $this->category->id,
            'location_id' => 1,
            'name' => 'Meja Rapat Kayu Jati',
            'sku' => 'OLD-001',
            'ownership_status' => 'internal',
            'total_qty' => 1,
            'available_qty' => 1,
        ]);

        // 2. Buat barang vendor
        $vendorItem = Item::create([
            'category_id' => $this->category->id,
            'location_id' => 1,
            'name' => 'Dispenser Sewa Coway',
            'sku' => 'COWAY-SERIAL-7712',
            'ownership_status' => 'vendor',
            'owner_name' => 'PT Coway International',
            'total_qty' => 1,
            'available_qty' => 1,
        ]);

        // 3. Buat barang instansi lain
        $externalItem = Item::create([
            'category_id' => $this->category->id,
            'location_id' => 1,
            'name' => 'Kendaraan Operasional Titipan',
            'sku' => 'POL-B-9999-XYZ',
            'ownership_status' => 'external',
            'owner_name' => 'Sekretariat Daerah',
            'total_qty' => 1,
            'available_qty' => 1,
        ]);

        $setting = CodeFormatSetting::firstOrCreate(
            ['entity' => 'item_sku'],
            [
                'name' => 'Format SKU Barang',
                'prefix' => 'SIMASET',
                'separator' => '-',
                'suffix_type' => 'INCREMENT_3_DIGIT',
                'example_output' => 'SIMASET-001',
            ]
        );

        // 4. Update format SKU dan picu sinkronisasi data lama
        $response = $this->actingAs($this->superAdmin)->put(route('admin-settings.code-formats.update', $setting), [
            'prefix' => 'SIMASET',
            'separator' => '-',
            'date_format' => 'NONE',
            'suffix_type' => 'INCREMENT_3_DIGIT',
            'sync_legacy_data' => '1',
        ]);

        $response->assertRedirect();

        // 5. Verifikasi: Barang internal TERUBAH mengikuti format baru
        $internalItem->refresh();
        $this->assertEquals('SIMASET-001', $internalItem->sku);

        // 6. Verifikasi Proteksi Imunitas: Barang vendor dan instansi lain TETAP UTUH TIDAK BERUBAH
        $vendorItem->refresh();
        $this->assertEquals('COWAY-SERIAL-7712', $vendorItem->sku);

        $externalItem->refresh();
        $this->assertEquals('POL-B-9999-XYZ', $externalItem->sku);
    }

    public function test_items_index_filters_by_ownership_status(): void
    {
        Item::create([
            'category_id' => $this->category->id,
            'location_id' => 1,
            'name' => 'Monitor Samsung Internal',
            'sku' => 'MON-INT-001',
            'ownership_status' => 'internal',
        ]);

        Item::create([
            'category_id' => $this->category->id,
            'location_id' => 1,
            'name' => 'Proyektor Vendor Infocus',
            'sku' => 'PROJ-VEN-002',
            'ownership_status' => 'vendor',
            'owner_name' => 'PT Solusi Visual',
        ]);

        // Filter internal
        $responseInternal = $this->actingAs($this->superAdmin)->get(route('items.index', ['ownership_status' => 'internal']));
        $responseInternal->assertSee('Monitor Samsung Internal');
        $responseInternal->assertDontSee('Proyektor Vendor Infocus');

        // Filter vendor
        $responseVendor = $this->actingAs($this->superAdmin)->get(route('items.index', ['ownership_status' => 'vendor']));
        $responseVendor->assertSee('Proyektor Vendor Infocus');
        $responseVendor->assertDontSee('Monitor Samsung Internal');
    }
}
