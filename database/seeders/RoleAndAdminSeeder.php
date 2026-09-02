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
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@pemda.go.id'],
            [
                'name'     => 'Bapak Kepala Gudang',
                'password' => Hash::make('password123'),
            ]
        );
        $superAdmin->syncRoles($roleSuperAdmin);

        // 3. Admin — akses semua fitur operasional, kecuali user management
        $admin = User::firstOrCreate(
            ['email' => 'admin@pemda.go.id'],
            [
                'name'     => 'Admin Pemda',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->syncRoles($roleAdmin);

        // 4. Staff Logistik — hanya bisa input & lihat peminjaman miliknya sendiri
        $staff = User::firstOrCreate(
            ['email' => 'staff@pemda.go.id'],
            [
                'name'     => 'Petugas Frontdesk',
                'password' => Hash::make('password123'),
            ]
        );
        $staff->syncRoles($roleStaff);
    }
}
