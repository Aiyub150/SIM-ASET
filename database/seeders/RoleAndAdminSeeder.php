<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Roles
        $roleSuperAdmin = Role::create(['name' => 'Super Admin']);
        $roleStaff = Role::create(['name' => 'Staff Logistik']);

        // 2. Buat User Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@pemda.go.id'],
            [
                'name' => 'Bapak Kepala Gudang',
                'password' => Hash::make('password123'),
            ]
        );
        $superAdmin->assignRole($roleSuperAdmin);

        // 3. Buat User Staff Biasa
        $staff = User::firstOrCreate(
            ['email' => 'staff@pemda.go.id'],
            [
                'name' => 'Petugas Frontdesk',
                'password' => Hash::make('password123'),
            ]
        );
        $staff->assignRole($roleStaff);
    }
}