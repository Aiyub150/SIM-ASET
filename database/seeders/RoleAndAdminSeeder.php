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

        // 1. Buat Granular Permissions
        $permissions = [
            // Items
            'items.view', 'items.create', 'items.edit', 'items.delete',
            // Borrowers
            'borrowers.view', 'borrowers.create', 'borrowers.edit', 'borrowers.delete',
            // Loans
            'loans.view', 'loans.create', 'loans.return',
            // Stocks
            'stocks.view', 'stocks.adjust',
            // Categories
            'categories.view', 'categories.create', 'categories.edit', 'categories.toggle',
            // Reports
            'reports.view', 'reports.export',
            // User Management
            'users.view', 'users.create', 'users.edit',
            // Audit & Roles
            'roles.manage', 'permissions.manage', 'audit.view', 'sku.manage',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Buat Roles (3 level bawaan)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web'], ['color' => '#d97706']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'], ['color' => '#2563eb']);
        $roleStaff      = Role::firstOrCreate(['name' => 'Staff Logistik', 'guard_name' => 'web'], ['color' => '#059669']);

        // 3. Beri Permissions Default
        $roleSuperAdmin->syncPermissions(Permission::all());

        $roleAdmin->syncPermissions([
            'items.view', 'items.create', 'items.edit',
            'borrowers.view', 'borrowers.create', 'borrowers.edit',
            'loans.view', 'loans.create', 'loans.return',
            'stocks.view', 'stocks.adjust',
            'categories.view', 'categories.create', 'categories.edit', 'categories.toggle',
            'reports.view', 'reports.export',
        ]);

        $roleStaff->syncPermissions([
            'items.view',
            'loans.view', 'loans.create', 'loans.return',
            'reports.view',
        ]);

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
