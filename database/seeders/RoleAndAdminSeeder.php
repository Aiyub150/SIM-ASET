<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Roles (3 level)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'Admin']);
        $roleStaff      = Role::firstOrCreate(['name' => 'Staff Logistik']);

        // 2. Super Admin — akses penuh termasuk user management
        $superAdminPassword = env('SEEDER_SUPERADMIN_PASSWORD', env('SEEDER_DEFAULT_PASSWORD', 'password123'));
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@simaset.com'],
            [
                'name'     => 'Seeder Super Admin',
                'password' => Hash::make($superAdminPassword),
            ]
        );
        $superAdmin->syncRoles($roleSuperAdmin);

        // 3. Admin — akses semua fitur operasional, kecuali user management
        $adminPassword = env('SEEDER_ADMIN_PASSWORD', env('SEEDER_DEFAULT_PASSWORD', 'password123'));
        $admin = User::firstOrCreate(
            ['email' => 'admin@simaset.com'],
            [
                'name'     => 'Seeder Admin',
                'password' => Hash::make($adminPassword),
            ]
        );
        $admin->syncRoles($roleAdmin);

        // 4. Staff Logistik — hanya bisa input & lihat peminjaman miliknya sendiri
        $staffPassword = env('SEEDER_STAFF_PASSWORD', env('SEEDER_DEFAULT_PASSWORD', 'password123'));
        $staff = User::firstOrCreate(
            ['email' => 'staff@simaset.com'],
            [
                'name'     => 'Seeder Staff',
                'password' => Hash::make($staffPassword),
            ]
        );
        $staff->syncRoles($roleStaff);
    }
}
