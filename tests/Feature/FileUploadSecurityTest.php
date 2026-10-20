<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin']);
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->category = Category::create([
            'code' => 'ELK',
            'name' => 'Elektronik',
            'is_active' => true,
        ]);

        \App\Models\Location::firstOrCreate(
            ['id' => 1],
            ['name' => 'Gudang Utama', 'description' => 'Lokasi default']
        );

        Storage::fake('public');
    }

    public function test_rejects_non_image_or_malicious_script_upload(): void
    {
        $maliciousFile = UploadedFile::fake()->create('exploit.php', 10, 'text/x-php');

        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'name'        => 'Item Uji',
            'image'       => $maliciousFile,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('items', ['name' => 'Item Uji']);
    }

    public function test_accepts_valid_image_and_stores_with_hashed_filename(): void
    {
        // 1x1 transparent GIF bytes
        $gifBytes = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $validImage = UploadedFile::fake()->createWithContent('photo.gif', $gifBytes);

        // Or create jpg with binary content
        $jpgBytes = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB\x00C\x00\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x03\x01\x22\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x1F\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00?\x00\xBF\x00\xFF\xD9";
        $validJpg = UploadedFile::fake()->createWithContent('photo.jpg', $jpgBytes);

        $response = $this->actingAs($this->superAdmin)->post(route('items.store'), [
            'category_id' => $this->category->id,
            'name'        => 'Proyektor Epson',
            'total_qty'   => 5,
            'image'       => $validJpg,
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['name' => 'Proyektor Epson']);

        $item = \App\Models\Item::where('name', 'Proyektor Epson')->first();
        $this->assertNotNull($item->image);
        $this->assertStringStartsWith('images/items/', $item->image);
        Storage::disk('public')->assertExists($item->image);
    }
}
