<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    private array $systemRoles = ['Super Admin', 'Admin', 'Staff Logistik'];

    /**
     * Halaman Utama Manajemen Role
     */
    public function indexRoles(): View
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('id')->get();

        return view('administration.roles.index', [
            'roles'       => $roles,
            'systemRoles' => $this->systemRoles,
        ]);
    }

    /**
     * Simpan Role Baru
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:roles,name',
                'regex:/^[\pL\pN\s\-_]+$/u',
            ],
        ], [
            'name.required' => 'Nama peran/role wajib diisi.',
            'name.unique'   => 'Nama role sudah terdaftar.',
            'name.regex'    => 'Format nama role hanya boleh huruf, angka, spasi, tanda minus, dan garis bawah.',
        ]);

        $role = Role::create([
            'name'       => trim($validated['name']),
            'guard_name' => 'web',
        ]);

        ActivityLogger::log(
            'CREATE',
            'Manajemen Role',
            "Membuat peran baru: {$role->name}",
            ['role_id' => $role->id, 'name' => $role->name],
            $request
        );

        return redirect()->route('admin-settings.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil ditambahkan.");
    }

    /**
     * Update Nama Role
     */
    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', "Role bawaan sistem ({$role->name}) tidak boleh diubah namanya.");
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
                'regex:/^[\pL\pN\s\-_]+$/u',
            ],
        ]);

        $oldName = $role->name;
        $role->update(['name' => trim($validated['name'])]);

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Role',
            "Mengubah nama peran: dari {$oldName} menjadi {$role->name}",
            ['role_id' => $role->id, 'old_name' => $oldName, 'new_name' => $role->name],
            $request
        );

        return redirect()->route('admin-settings.roles.index')
            ->with('success', "Role \"{$oldName}\" berhasil diubah menjadi \"{$role->name}\".");
    }

    /**
     * Hapus Role
     */
    public function destroyRole(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->systemRoles)) {
            return back()->with('error', "Role inti sistem ({$role->name}) tidak dapat dihapus demi integritas sistem.");
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Role \"{$role->name}\" masih digunakan oleh {$role->users()->count()} pengguna. Silakan pindahkan pengguna terlebih dahulu.");
        }

        $roleName = $role->name;
        $role->syncPermissions([]);
        $role->delete();

        ActivityLogger::log(
            'DELETE',
            'Manajemen Role',
            "Menghapus peran kustom: {$roleName}",
            ['name' => $roleName],
            $request
        );

        return redirect()->route('admin-settings.roles.index')
            ->with('success', "Role \"{$roleName}\" berhasil dihapus.");
    }

    /**
     * Halaman Khusus Form Pengaturan Hak Akses Per Role
     */
    public function editRolePermissions(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();

        // Kelompokkan permission berdasarkan modul
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $module = ucfirst($parts[0] ?? 'Lainnya');
            $groupedPermissions[$module][] = $perm;
        }

        return view('administration.roles.permissions', [
            'role'               => $role,
            'groupedPermissions' => $groupedPermissions,
            'systemRoles'        => $this->systemRoles,
        ]);
    }

    /**
     * Halaman Matriks Hak Akses (Role & User Override)
     */
    public function indexPermissions(Request $request): View
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $permissions = Permission::orderBy('name')->get();

        // Kelompokkan permission berdasarkan modul
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $module = ucfirst($parts[0] ?? 'Lainnya');
            $groupedPermissions[$module][] = $perm;
        }

        // Ambil daftar pengguna untuk User Override
        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? User::with(['roles', 'permissions'])->find($selectedUserId) : null;
        $users = User::with('roles')->orderBy('name')->get();

        return view('administration.permissions.index', [
            'roles'              => $roles,
            'groupedPermissions'=> $groupedPermissions,
            'users'              => $users,
            'selectedUser'       => $selectedUser,
            'systemRoles'        => $this->systemRoles,
        ]);
    }

    /**
     * Simpan Pembaruan Matriks Hak Akses Role
     */
    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        $permissionNames = $request->input('permissions', []);
        $role->syncPermissions($permissionNames);

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Hak Akses',
            "Memperbarui hak akses matriks untuk peran: {$role->name}",
            ['role_id' => $role->id, 'permissions_count' => count($permissionNames)],
            $request
        );

        return back()->with('success', "Hak akses untuk role \"{$role->name}\" berhasil disimpan.");
    }

    /**
     * Simpan Hak Akses Khusus Pengguna (Direct User Override)
     */
    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        $permissionNames = $request->input('permissions', []);
        $user->syncPermissions($permissionNames);

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Hak Akses',
            "Memperbarui direct permissions (override) untuk pengguna: {$user->name} ({$user->email})",
            ['user_id' => $user->id, 'permissions' => $permissionNames],
            $request
        );

        return redirect()->route('admin-settings.permissions.index', ['user_id' => $user->id])
            ->with('success', "Hak akses khusus untuk \"{$user->name}\" berhasil diperbarui.");
    }
}
