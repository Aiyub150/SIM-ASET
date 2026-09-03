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
}
