<?php

namespace Tests\Feature;

use App\Models\Borrower;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Services\LoanService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SimAsetCoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $staff1;
    protected User $staff2;
    protected Category $category;
    protected Location $location;
    protected Item $item1;
    protected Item $item2;
    protected Borrower $borrower;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $roleSuperAdmin = Role::create(['name' => 'Super Admin']);
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleStaff = Role::create(['name' => 'Staff Logistik']);

        // Seed users
        $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $this->superAdmin->assignRole($roleSuperAdmin);

        $this->admin = User::factory()->create(['name' => 'Admin']);
        $this->admin->assignRole($roleAdmin);

        $this->staff1 = User::factory()->create(['name' => 'Staff 1']);
        $this->staff1->assignRole($roleStaff);

        $this->staff2 = User::factory()->create(['name' => 'Staff 2']);
        $this->staff2->assignRole($roleStaff);

        // Seed master data
        $this->category = Category::create([
            'name' => 'Elektronik',
            'sku_prefix' => 'ELEC',
            'is_active' => true,
        ]);

        $this->location = Location::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        $this->item1 = Item::create([
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'name' => 'Laptop Asus',
            'sku' => 'ELEC-001',
            'total_qty' => 10,
            'available_qty' => 10,
        ]);

        $this->item2 = Item::create([
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'name' => 'Proyektor BenQ',
            'sku' => 'ELEC-002',
            'total_qty' => 5,
            'available_qty' => 5,
        ]);

        $this->borrower = Borrower::create([
            'institution_name' => 'Dinas Pendidikan',
            'pic_name' => 'Ahmad',
            'contact_number' => '08123456789',
            'address' => 'Jl. Pendidikan No. 2',
        ]);
    }

    /**
     * Test 1: Public access rules (Redirections to login)
     */
    public function test_guest_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/register')->assertRedirect(route('login'));
        $this->get('/forgot-password')->assertRedirect(route('login'));
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    /**
     * Test 2: Role boundary - Staff cannot access user management
     */
    public function test_staff_cannot_access_user_management(): void
    {
        $this->actingAs($this->staff1)
            ->get('/users')
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get('/users')
            ->assertForbidden();

        $this->actingAs($this->superAdmin)
            ->get('/users')
            ->assertOk();
    }

    /**
     * Test 3: Role boundary - Staff cannot create or edit master items
     */
    public function test_staff_cannot_modify_items(): void
    {
        $this->actingAs($this->staff1)
            ->get('/items/create')
            ->assertForbidden();

        $this->actingAs($this->staff1)
            ->get("/items/{$this->item1->id}/edit")
            ->assertForbidden();

        // But staff CAN view items list (read-only as per Feedback-6)
        $this->actingAs($this->staff1)
            ->get('/items')
            ->assertOk();
    }

    /**
     * Test 4: Role boundary - Staff only sees their own loans
     */
    public function test_staff_cannot_view_or_return_another_staff_loan(): void
    {
        $loanService = app(LoanService::class);

        // Staff 1 creates a loan
        $loan = $loanService->createLoan([
            'borrower_id' => $this->borrower->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
            'items' => [
                ['item_id' => $this->item1->id, 'qty' => 2],
            ]
        ], $this->staff1->id);

        // Staff 1 can view
        $this->actingAs($this->staff1)
            ->get("/loans/{$loan->id}")
            ->assertOk();

        // Staff 2 cannot view (403 Forbidden)
        $this->actingAs($this->staff2)
            ->get("/loans/{$loan->id}")
            ->assertForbidden();

        // Staff 2 cannot print PDF (403 Forbidden)
        $this->actingAs($this->staff2)
            ->get("/loans/{$loan->id}/print")
            ->assertForbidden();

        // Admin CAN view
        $this->actingAs($this->admin)
            ->get("/loans/{$loan->id}")
            ->assertOk();
    }

    /**
     * Test 5: StockService adjustStock behavior
     */
    public function test_stock_service_adjust_stock(): void
    {
        $stockService = app(StockService::class);

        // Add 5 units
        $stockService->adjustStock([
            'item_id' => $this->item1->id,
            'type' => 'in',
            'qty' => 5,
            'reference_code' => 'BAST/2026/09/001',
            'notes' => 'Pengadaan unit tambahan',
        ], $this->admin->id);

        $this->item1->refresh();
        $this->assertEquals(15, $this->item1->total_qty);
        $this->assertEquals(15, $this->item1->available_qty);

        // Deduct 2 broken units
        $stockService->adjustStock([
            'item_id' => $this->item1->id,
            'type' => 'broken',
            'qty' => 2,
            'reference_code' => 'BAST/2026/09/002',
            'notes' => 'Unit rusak terbakar',
        ], $this->admin->id);

        $this->item1->refresh();
        $this->assertEquals(13, $this->item1->total_qty);
        $this->assertEquals(13, $this->item1->available_qty);
    }

    /**
     * Test 6: Multi-item loan creation & stock deduction
     */
    public function test_multi_item_loan_creation(): void
    {
        $loanService = app(LoanService::class);

        $loan = $loanService->createLoan([
            'borrower_id' => $this->borrower->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
            'items' => [
                ['item_id' => $this->item1->id, 'qty' => 3],
                ['item_id' => $this->item2->id, 'qty' => 2],
            ]
        ], $this->staff1->id);

        $this->assertNotNull($loan);
        $this->assertEquals('active', $loan->status);

        $this->item1->refresh();
        $this->item2->refresh();

        $this->assertEquals(7, $this->item1->available_qty);
        $this->assertEquals(3, $this->item2->available_qty);
    }

    /**
     * Test 7: Partial and complete return flow
     */
    public function test_partial_and_complete_loan_return(): void
    {
        $loanService = app(LoanService::class);

        $loan = $loanService->createLoan([
            'borrower_id' => $this->borrower->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
            'items' => [
                ['item_id' => $this->item1->id, 'qty' => 4],
            ]
        ], $this->staff1->id);

        $this->item1->refresh();
        $this->assertEquals(6, $this->item1->available_qty);

        $loanItem = $loan->loanItems->first();

        // 1. Return 2 units partially
        $updatedLoan = $loanService->processReturn($loan->id, [
            'items' => [
                [
                    'loan_item_id' => $loanItem->id,
                    'return_qty' => 2,
                ]
            ]
        ], $this->staff1->id);

        $this->assertEquals('active', $updatedLoan->status);
        $this->item1->refresh();
        $this->assertEquals(8, $this->item1->available_qty);

        // 2. Return remaining 2 units
        $completedLoan = $loanService->processReturn($loan->id, [
            'items' => [
                [
                    'loan_item_id' => $loanItem->id,
                    'return_qty' => 2,
                ]
            ]
        ], $this->staff1->id);

        $this->assertEquals('completed', $completedLoan->status);
        $this->assertNotNull($completedLoan->return_date);
        $this->item1->refresh();
        $this->assertEquals(10, $this->item1->available_qty);
    }

    /**
     * Test 8: Item search query respects category filter (nested closure verification)
     */
    public function test_item_search_does_not_bypass_category_filter(): void
    {
        $catFurniture = Category::create(['name' => 'Furniture', 'sku_prefix' => 'FURN']);
        $furnitureItem = Item::create([
            'category_id' => $catFurniture->id,
            'location_id' => $this->location->id,
            'name' => 'Meja Laptop Lipat',
            'sku' => 'FURN-001',
            'total_qty' => 5,
            'available_qty' => 5,
        ]);

        // When searching for "Laptop" in Elektronik category, only Asus Laptop should appear, NOT the furniture item
        $response = $this->actingAs($this->admin)
            ->get('/items?category_id=' . $this->category->id . '&search=Laptop');

        $response->assertOk();
        $response->assertSee('Laptop Asus');
        $response->assertDontSee('Meja Laptop Lipat');
    }

    /**
     * Test 9: XSS Sanitizer preserves special characters in passwords
     */
    public function test_xss_sanitizer_preserves_passwords(): void
    {
        $passwordWithSpecialChars = 'Sec<ret>&Pass 123!';

        $response = $this->actingAs($this->superAdmin)
            ->post('/users', [
                'name' => 'Test User',
                'email' => 'testspecial@example.com',
                'role' => 'Admin',
                'password' => $passwordWithSpecialChars,
                'password_confirmation' => $passwordWithSpecialChars,
            ]);

        $response->assertSessionHasNoErrors();
        $user = User::where('email', 'testspecial@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($passwordWithSpecialChars, $user->password));
    }

    /**
     * Test 10: Loan dynamic search by item name and item SKU
     */
    public function test_loan_search_by_item_name_and_sku(): void
    {
        $loanService = app(LoanService::class);
        $loan = $loanService->createLoan([
            'borrower_id' => $this->borrower->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(3)->toDateString(),
            'items' => [
                ['item_id' => $this->item1->id, 'qty' => 1],
            ]
        ], $this->admin->id);

        // Search by item SKU (ELEC-001)
        $responseSku = $this->actingAs($this->admin)->get('/loans?search=ELEC-001');
        $responseSku->assertOk();
        $responseSku->assertSee($loan->loan_code);

        // Search by item name (Laptop Asus)
        $responseName = $this->actingAs($this->admin)->get('/loans?search=Laptop');
        $responseName->assertOk();
        $responseName->assertSee($loan->loan_code);

        // Search for non-existent item
        $responseNone = $this->actingAs($this->admin)->get('/loans?search=NonExistentItemNameXYZ');
        $responseNone->assertOk();
        $responseNone->assertDontSee($loan->loan_code);
    }

    /**
     * Test 11: StockService strictly rejects invalid movement types
     */
    public function test_stock_service_rejects_invalid_movement_type(): void
    {
        $stockService = app(StockService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tipe mutasi tidak valid.');

        $stockService->adjustStock([
            'item_id' => $this->item1->id,
            'type' => 'invalid_type_here',
            'qty' => 1,
            'reference_code' => 'BAST/2026/09/999',
        ], $this->admin->id);
    }

    /**
     * Test 12: Staff report hides stock mutations
     */
    public function test_staff_report_does_not_contain_stock_mutations(): void
    {
        // Admin creates a stock movement
        $stockService = app(StockService::class);
        $stockService->adjustStock([
            'item_id' => $this->item1->id,
            'type' => 'in',
            'qty' => 2,
            'reference_code' => 'BAST/' . now()->format('Y/m') . '/001',
        ], $this->admin->id);

        // Staff visits report page
        $responseStaff = $this->actingAs($this->staff1)->get('/reports?period=' . now()->format('Y-m'));
        $responseStaff->assertOk();
        $responseStaff->assertDontSee('Mutasi Stok Fisik');

        // Admin visits report page - sees Mutasi Stok Fisik
        $responseAdmin = $this->actingAs($this->admin)->get('/reports?period=' . now()->format('Y-m'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Mutasi Stok Fisik');
    }
}
