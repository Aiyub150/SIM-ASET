<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminLogTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'Admin']);
        $roleStaff      = Role::firstOrCreate(['name' => 'Staff Logistik']);

        $this->superAdmin = User::factory()->create(['name' => 'Super Admin Test', 'email' => 'superadmin.test@simaset.com']);
        $this->superAdmin->assignRole($roleSuperAdmin);

        $this->admin = User::factory()->create(['name' => 'Admin Test', 'email' => 'admin.test@simaset.com']);
        $this->admin->assignRole($roleAdmin);

        $this->staff = User::factory()->create(['name' => 'Staff Test', 'email' => 'staff.test@simaset.com']);
        $this->staff->assignRole($roleStaff);
    }

    public function test_superadmin_can_access_log_history_page(): void
    {
        ActivityLog::create([
            'user_id'     => $this->superAdmin->id,
            'user_name'   => $this->superAdmin->name,
            'user_role'   => 'Super Admin',
            'action'      => 'LOGIN',
            'module'      => 'Autentikasi',
            'description' => 'Super Admin masuk ke sistem',
            'properties'  => ['test' => true],
            'ip_address'  => '203.0.113.195',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.log-history'));
        $response->assertStatus(200);
        $response->assertSee('Log History Aktivitas (Audit Trail)');
        $response->assertSee('Super Admin masuk ke sistem');
        $response->assertSee('203.0.113.195');
    }

    public function test_non_superadmin_roles_are_forbidden_from_log_pages(): void
    {
        // Admin
        $response = $this->actingAs($this->admin)->get(route('superadmin.log-history'));
        $response->assertStatus(403);

        // Staff
        $response = $this->actingAs($this->staff)->get(route('superadmin.log-history'));
        $response->assertStatus(403);
    }

    public function test_activity_logger_resolves_real_client_ip_from_headers(): void
    {
        // Emulate request with X-Forwarded-For header from real client
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_FOR' => '198.51.100.42, 10.0.0.1',
            'REMOTE_ADDR'          => '127.0.0.1', // server loopback
        ]);

        $resolvedIp = ActivityLogger::resolveClientIp($request);
        $this->assertEquals('198.51.100.42', $resolvedIp);
    }

    public function test_superadmin_log_history_renders_bootstrap_five_pagination(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            ActivityLog::create([
                'user_id'     => $this->superAdmin->id,
                'user_name'   => $this->superAdmin->name,
                'user_role'   => 'Super Admin',
                'action'      => 'LOGIN',
                'module'      => 'Autentikasi',
                'description' => "Aktivitas uji #{$i}",
                'ip_address'  => '127.0.0.1',
            ]);
        }

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.log-history'));
        $response->assertStatus(200);
        $response->assertSee('pagination');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
        $response->assertDontSee('w-5 h-5'); // Tailwind unconstrained svg classes must not be present
    }
}
