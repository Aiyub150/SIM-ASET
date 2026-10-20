<?php

namespace Tests\Feature;

use App\Models\CodeFormatSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DynamicRbacAndAdministrationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndAdminSeeder::class);
        $this->seed(\Database\Seeders\CodeFormatSettingSeeder::class);

        $this->superAdmin = User::role('Super Admin')->first();
        $this->admin      = User::role('Admin')->first();
        $this->staff      = User::role('Staff Logistik')->first();
    }

    public function test_superadmin_can_access_roles_and_permissions_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin-settings.roles.index'));
        $response->assertOk();
        $response->assertSee('Daftar Peran', false);

        $responsePerm = $this->actingAs($this->superAdmin)->get(route('admin-settings.permissions.index'));
        $responsePerm->assertOk();
        $responsePerm->assertSee('Matriks Hak Akses', false);
    }

    public function test_superadmin_can_create_update_and_delete_custom_role(): void
    {
        // 1. Create Custom Role
        $response = $this->actingAs($this->superAdmin)->post(route('admin-settings.roles.store'), [
            'name' => 'Auditor BPK',
        ]);
        $response->assertRedirect(route('admin-settings.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Auditor BPK']);

        $role = Role::where('name', 'Auditor BPK')->first();

        // 2. Update Role Name
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin-settings.roles.update', $role), [
            'name' => 'Auditor Eksternal',
        ]);
        $responseUpdate->assertRedirect(route('admin-settings.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Auditor Eksternal']);

        // 3. Delete Role
        $role->refresh();
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin-settings.roles.destroy', $role));
        $responseDelete->assertRedirect(route('admin-settings.roles.index'));
        $this->assertDatabaseMissing('roles', ['name' => 'Auditor Eksternal']);
    }

    public function test_system_default_roles_cannot_be_deleted(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $response = $this->actingAs($this->superAdmin)->delete(route('admin-settings.roles.destroy', $superAdminRole));
        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
    }

    public function test_superadmin_can_update_role_permissions(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        $response = $this->actingAs($this->superAdmin)->put(route('admin-settings.permissions.roles.update', $adminRole), [
            'permissions' => ['items.view', 'reports.view', 'categories.view'],
        ]);

        $response->assertRedirect();
        $this->assertTrue($adminRole->fresh()->hasPermissionTo('items.view'));
        $this->assertTrue($adminRole->fresh()->hasPermissionTo('reports.view'));
    }

    public function test_superadmin_can_give_direct_permission_override_to_specific_user_without_affecting_others(): void
    {
        // Buat satu user staff tambahan untuk menguji isolasi
        $anotherStaff = User::factory()->create();
        $anotherStaff->assignRole('Staff Logistik');

        // Berikan direct permission override ke $this->staff saja (misal: reports.view dan audit.view)
        $response = $this->actingAs($this->superAdmin)->put(route('admin-settings.permissions.users.update', $this->staff), [
            'permissions' => ['reports.view', 'audit.view'],
        ]);

        $response->assertRedirect();

        // Verifikasi $this->staff memiliki direct permission
        $this->assertTrue($this->staff->fresh()->hasDirectPermission('reports.view'));
        $this->assertTrue($this->staff->fresh()->hasDirectPermission('audit.view'));

        // Verifikasi $anotherStaff TIDAK terpengaruh dan tidak memiliki direct permission ini
        $this->assertFalse($anotherStaff->fresh()->hasDirectPermission('reports.view'));
        $this->assertFalse($anotherStaff->fresh()->hasDirectPermission('audit.view'));
    }

    public function test_superadmin_can_configure_and_preview_code_format_settings(): void
    {
        $setting = CodeFormatSetting::where('entity', 'loan_code')->first();

        $response = $this->actingAs($this->superAdmin)->get(route('admin-settings.code-formats.index'));
        $response->assertOk();
        $response->assertSee('Format Penomoran Kode SKU', false);

        // Update format pinjaman menjadi: PINJAMAN-YYYY-INCREMENT_4_DIGIT
        $updateResp = $this->actingAs($this->superAdmin)->put(route('admin-settings.code-formats.update', $setting), [
            'prefix'        => 'PINJAMAN',
            'separator'     => '-',
            'date_format'   => 'YYYY',
            'suffix_type'   => 'INCREMENT_4_DIGIT',
        ]);

        $updateResp->assertRedirect();
        $setting->refresh();

        $this->assertEquals('PINJAMAN', $setting->prefix);
        $this->assertEquals('YYYY', $setting->date_format);
        $this->assertEquals('INCREMENT_4_DIGIT', $setting->suffix_type);

        $preview = $setting->generatePreview();
        $currentYear = date('Y');
        $this->assertEquals("PINJAMAN-{$currentYear}-0001", $preview);
    }

    public function test_non_superadmin_cannot_access_administration_settings(): void
    {
        $staffResponse = $this->actingAs($this->staff)->get(route('admin-settings.roles.index'));
        $staffResponse->assertForbidden();

        $adminResponse = $this->actingAs($this->admin)->get(route('admin-settings.code-formats.index'));
        $adminResponse->assertForbidden();
    }
}
