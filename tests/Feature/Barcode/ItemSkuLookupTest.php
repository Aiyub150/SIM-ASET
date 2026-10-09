<?php

namespace Tests\Feature\Barcode;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSkuLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_lookup_item_by_sku(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Elektronik',
            'sku_prefix' => 'ELEC',
        ]);
        $location = Location::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Test',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Laptop',
            'sku' => 'ELEC-001',
            'total_qty' => 10,
            'available_qty' => 7,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/items/lookup?sku=' . urlencode($item->sku));

        $response->assertOk();
        $response->assertJsonPath('item.id', $item->id);
        $response->assertJsonPath('item.sku', $item->sku);
        $response->assertJsonPath('item.name', $item->name);
        $response->assertJsonPath('item.available_qty', 7);
    }

    public function test_print_label_defaults_to_square_qr_code_without_redundant_linear_barcode(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Elektronik',
            'sku_prefix' => 'ELEC',
        ]);
        $location = Location::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Test',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Monitor',
            'sku' => 'ELEC-002',
            'total_qty' => 5,
            'available_qty' => 5,
        ]);

        $response = $this->actingAs($user)->get(route('items.label', ['item' => $item->id]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_bulk_print_labels_defaults_to_square_qr_code(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Elektronik',
            'sku_prefix' => 'ELEC',
        ]);
        $location = Location::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Test',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => 'Mouse',
            'sku' => 'ELEC-003',
            'total_qty' => 10,
            'available_qty' => 10,
        ]);

        $response = $this->actingAs($user)->get(route('items.labels', ['items' => [$item->id]]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
}
