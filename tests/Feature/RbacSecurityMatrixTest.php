<?php

namespace Tests\Feature;

use App\Models\Borrower;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacSecurityMatrixTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin;
    private User $staff;
    private Item $testItem;
    private Borrower $testBorrower;
    private Category $testCategory;
    private Loan $testLoan;

    protected function setUp(): void
    {
        parent::setUp();

        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'Admin']);
        $roleStaff      = Role::firstOrCreate(['name' => 'Staff Logistik']);

        $this->superAdmin = User::factory()->create(['name' => 'Super Admin Sec', 'email' => 'superadmin.sec@simaset.com']);
        $this->superAdmin->assignRole($roleSuperAdmin);

        $this->admin = User::factory()->create(['name' => 'Admin Sec', 'email' => 'admin.sec@simaset.com']);
        $this->admin->assignRole($roleAdmin);

        $this->staff = User::factory()->create(['name' => 'Staff Sec', 'email' => 'staff.sec@simaset.com']);
        $this->staff->assignRole($roleStaff);

        $this->testCategory = Category::create([
            'name'        => 'Elektronik Sec',
            'code'        => 'ELC',
            'is_active'   => true,
            'description' => 'Test Kategori',
        ]);

        $this->testBorrower = Borrower::create([
            'institution_name' => 'Dinas Pendidikan Sec',
            'pic_name'         => 'Budi Sec',
            'contact_number'   => '081234567890',
            'address'          => 'Jl. Test No. 12',
        ]);

        $testLocation = Location::create([
            'name'    => 'Gudang Utama Sec',
            'address' => 'Jl. Merdeka Sec No. 1',
        ]);

        $this->testItem = Item::create([
            'category_id'       => $this->testCategory->id,
            'location_id'       => $testLocation->id,
            'name'              => 'Laptop ThinkPad Sec',
            'sku'               => 'ELEC-SEC-001',
            'total_qty'         => 10,
            'available_qty'     => 10,
        ]);

        $this->testLoan = Loan::create([
            'loan_code'     => 'LOAN-2026-TEST',
            'borrower_id'   => $this->testBorrower->id,
            'user_id'       => $this->staff->id,
            'borrow_date'   => now()->toDateString(),
            'due_date'      => now()->addDays(3)->toDateString(),
            'status'        => 'active',
            'notes'         => 'Uji keamanan RBAC',
        ]);
    }

    public function test_superadmin_has_full_access_to_all_matrix_endpoints(): void
    {
        $endpoints = [
            '/users',
            '/superadmin/log-server',
            '/superadmin/log-history',
            '/categories',
            '/stocks',
            '/items/create',
            '/items',
            '/loans',
            '/loans/create',
            '/borrowers',
            '/reports',
            '/items/' . $this->testItem->id . '/edit',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($this->superAdmin)->get($endpoint);
            $this->assertEquals(200, $response->getStatusCode(), "Superadmin failed on endpoint: {$endpoint}");
        }
    }

    public function test_admin_has_operational_access_but_forbidden_from_superadmin_modules(): void
    {
        // Akses diperbolehkan (200 OK)
        $allowedEndpoints = [
            '/categories',
            '/stocks',
            '/items/create',
            '/items',
            '/loans',
            '/loans/create',
            '/borrowers',
            '/reports',
            '/items/' . $this->testItem->id . '/edit',
        ];

        foreach ($allowedEndpoints as $endpoint) {
            $response = $this->actingAs($this->admin)->get($endpoint);
            $this->assertEquals(200, $response->getStatusCode(), "Admin should have access to: {$endpoint}");
        }

        // Akses dilarang (403 Forbidden)
        $forbiddenEndpoints = [
            '/users',
            '/superadmin/log-server',
            '/superadmin/log-history',
        ];

        foreach ($forbiddenEndpoints as $endpoint) {
            $response = $this->actingAs($this->admin)->get($endpoint);
            $this->assertEquals(403, $response->getStatusCode(), "Admin must be forbidden from: {$endpoint}");
        }
    }

    public function test_staff_has_limited_access_and_forbidden_from_admin_modules(): void
    {
        // Akses diperbolehkan untuk Staff (200 OK)
        $allowedEndpoints = [
            '/items',
            '/loans',
            '/loans/create',
            '/reports',
        ];

        foreach ($allowedEndpoints as $endpoint) {
            $response = $this->actingAs($this->staff)->get($endpoint);
            $this->assertEquals(200, $response->getStatusCode(), "Staff should have access to: {$endpoint}");
        }

        // Akses dilarang untuk Staff (403 Forbidden)
        $forbiddenEndpoints = [
            '/users',
            '/superadmin/log-server',
            '/superadmin/log-history',
            '/categories',
            '/stocks',
            '/borrowers',
            '/items/create',
            '/items/' . $this->testItem->id . '/edit',
        ];

        foreach ($forbiddenEndpoints as $endpoint) {
            $response = $this->actingAs($this->staff)->get($endpoint);
            $this->assertEquals(403, $response->getStatusCode(), "Staff must be forbidden from: {$endpoint}");
        }
    }
}
