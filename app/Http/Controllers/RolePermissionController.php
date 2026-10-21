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
     * Helper: metadata deskripsi ramah dan modul untuk permissions
     */
    public static function getPermissionDefinitions(): array
    {
        return [
            'Master Barang' => [
                'items.view'   => ['label' => 'Lihat Katalog Barang', 'desc' => 'Melihat daftar aset, detail spesifikasi, dan stok barang inventaris'],
                'items.create' => ['label' => 'Tambah Barang Baru', 'desc' => 'Mendaftarkan aset barang baru ke dalam sistem inventaris'],
                'items.edit'   => ['label' => 'Ubah Data Barang', 'desc' => 'Mengubah nama, foto, atau informasi barang inventaris'],
                'items.delete' => ['label' => 'Hapus Barang', 'desc' => 'Menghapus data aset barang dari sistem inventaris'],
            ],
            'Data Peminjam / Instansi' => [
                'borrowers.view'   => ['label' => 'Lihat Data Peminjam', 'desc' => 'Melihat direktori instansi dan data kontak pihak peminjam'],
                'borrowers.create' => ['label' => 'Tambah Peminjam Baru', 'desc' => 'Mendaftarkan nama instansi atau pihak peminjam baru'],
                'borrowers.edit'   => ['label' => 'Ubah Data Peminjam', 'desc' => 'Memperbarui informasi instansi peminjam'],
                'borrowers.delete' => ['label' => 'Hapus Data Peminjam', 'desc' => 'Menghapus instansi peminjam dari sistem'],
            ],
            'Transaksi Peminjaman' => [
                'loans.view'   => ['label' => 'Lihat Riwayat Pinjam', 'desc' => 'Melihat catatan transaksi peminjaman barang'],
                'loans.create' => ['label' => 'Catat Peminjaman Baru', 'desc' => 'Membuat transaksi peminjaman aset dan mengurangi stok barang'],
                'loans.return' => ['label' => 'Proses Pengembalian', 'desc' => 'Mencatat pengembalian aset dari peminjam dan memulihkan stok'],
            ],
            'Mutasi Stok & Kartu Stok' => [
                'stocks.view'   => ['label' => 'Lihat Buku Besar Stok', 'desc' => 'Melihat pergerakan mutasi fisik barang masuk, keluar, atau rusak'],
                'stocks.adjust' => ['label' => 'Input Mutasi Fisik', 'desc' => 'Menambah atau mengurangi stok barang secara langsung'],
            ],
            'Kategori Barang' => [
                'categories.view'   => ['label' => 'Lihat Kategori', 'desc' => 'Melihat daftar klasifikasi kategori barang'],
                'categories.create' => ['label' => 'Tambah Kategori', 'desc' => 'Menambahkan kategori inventaris baru'],
                'categories.edit'   => ['label' => 'Ubah Kategori', 'desc' => 'Memperbarui nama dan kode awalan (prefix) kategori'],
                'categories.toggle' => ['label' => 'Nonaktifkan / Aktifkan', 'desc' => 'Mengubah status aktif kategori barang'],
            ],
            'Laporan & Rekapitulasi' => [
                'reports.view'   => ['label' => 'Lihat Rekapitulasi', 'desc' => 'Melihat ringkasan transaksi peminjaman dan mutasi bulanan'],
                'reports.export' => ['label' => 'Ekspor Dokumen PDF', 'desc' => 'Mengunduh rekapitulasi inventaris ke dalam format PDF resmi'],
            ],
            'Manajemen Pengguna' => [
                'users.view'   => ['label' => 'Lihat Daftar Pengguna', 'desc' => 'Melihat seluruh akun staf dan hak aksesnya'],
                'users.create' => ['label' => 'Tambah Akun Pengguna', 'desc' => 'Mendaftarkan akun staf atau operator baru'],
                'users.edit'   => ['label' => 'Ubah Profil & Sandi', 'desc' => 'Memperbarui informasi atau mengatur ulang sandi staf'],
            ],
            'Administrasi & Audit' => [
                'roles.manage'       => ['label' => 'Kelola Peran (Role)', 'desc' => 'Menambah, mengubah, dan menghapus peran pengguna'],
                'permissions.manage' => ['label' => 'Atur Matriks Hak Akses', 'desc' => 'Mengatur perizinan modul untuk setiap peran atau user'],
                'audit.view'         => ['label' => 'Lihat Log Audit Trail', 'desc' => 'Melihat rekaman aktivitas dan riwayat keamanan sistem'],
                'sku.manage'         => ['label' => 'Pola Format Kode SKU', 'desc' => 'Mengonfigurasi pola penomoran SKU, Peminjaman, dan BAST'],
            ],
        ];
    }

    /**
     * Halaman Khusus Form Pengaturan Hak Akses Per Role
     */
    public function editRolePermissions(Role $role): View
    {
        $role->load('permissions');
        $permissionDefs = self::getPermissionDefinitions();
        $permissions = Permission::all()->keyBy('name');

        return view('administration.roles.permissions', [
            'role'           => $role,
            'permissionDefs' => $permissionDefs,
            'permissions'    => $permissions,
            'systemRoles'    => $this->systemRoles,
        ]);
    }

    /**
     * Halaman Matriks Hak Akses (Role & User Override)
     */
    public function indexPermissions(Request $request): View
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $permissionDefs = self::getPermissionDefinitions();
        $permissions = Permission::all()->keyBy('name');

        // Ambil daftar pengguna untuk User Override
        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? User::with(['roles', 'permissions'])->find($selectedUserId) : null;
        $users = User::with('roles')->orderBy('name')->get();

        return view('administration.permissions.index', [
            'roles'          => $roles,
            'permissionDefs' => $permissionDefs,
            'permissions'    => $permissions,
            'users'          => $users,
            'selectedUser'   => $selectedUser,
            'systemRoles'    => $this->systemRoles,
        ]);
    }

    /**
     * Helper: pemetaan kode permission ke label ramah pengguna
     */
    public static function getPermissionLabelMap(): array
    {
        $map = [];
        foreach (self::getPermissionDefinitions() as $group => $perms) {
            foreach ($perms as $code => $info) {
                $map[$code] = $info['label'];
            }
        }
        return $map;
    }

    /**
     * Simpan Pembaruan Matriks Hak Akses Role
     */
    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        $permMap = self::getPermissionLabelMap();
        $oldPermissions = $role->permissions->pluck('name')->toArray();
        $permissionNames = $request->input('permissions', []);
        
        $role->syncPermissions($permissionNames);

        $added = array_diff($permissionNames, $oldPermissions);
        $removed = array_diff($oldPermissions, $permissionNames);

        $addedLabels = array_map(fn($p) => $permMap[$p] ?? $p, $added);
        $removedLabels = array_map(fn($p) => $permMap[$p] ?? $p, $removed);

        $actor = auth()->user()->name ?? 'Administrator';

        if (!empty($added) && empty($removed)) {
            $desc = "{$actor} memberikan akses " . implode(', ', $addedLabels) . " kepada role {$role->name}";
        } elseif (empty($added) && !empty($removed)) {
            $desc = "{$actor} mencabut akses " . implode(', ', $removedLabels) . " dari role {$role->name}";
        } elseif (!empty($added) && !empty($removed)) {
            $desc = "{$actor} memberikan akses " . implode(', ', $addedLabels) . " dan mencabut " . implode(', ', $removedLabels) . " untuk role {$role->name}";
        } else {
            $desc = "{$actor} memperbarui konfigurasi hak akses role {$role->name}";
        }

        $oldLabels = array_values(array_map(fn($p) => $permMap[$p] ?? $p, $oldPermissions));
        $newLabels = array_values(array_map(fn($p) => $permMap[$p] ?? $p, $permissionNames));

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Hak Akses',
            $desc,
            [
                'role'   => $role->name,
                'before' => ['Daftar Izin' => !empty($oldLabels) ? implode(', ', $oldLabels) : 'Tidak ada izin'],
                'after'  => ['Daftar Izin' => !empty($newLabels) ? implode(', ', $newLabels) : 'Tidak ada izin'],
            ],
            $request
        );

        return back()->with('success', "Hak akses untuk role \"{$role->name}\" berhasil disimpan.");
    }

    /**
     * Simpan Hak Akses Khusus Pengguna (Direct User Override)
     */
    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        $permMap = self::getPermissionLabelMap();
        $oldPermissions = $user->permissions->pluck('name')->toArray();
        $permissionNames = $request->input('permissions', []);

        $user->syncPermissions($permissionNames);

        $added = array_diff($permissionNames, $oldPermissions);
        $removed = array_diff($oldPermissions, $permissionNames);

        $addedLabels = array_map(fn($p) => $permMap[$p] ?? $p, $added);
        $removedLabels = array_map(fn($p) => $permMap[$p] ?? $p, $removed);

        $actor = auth()->user()->name ?? 'Administrator';

        if (!empty($added) && empty($removed)) {
            $desc = "{$actor} memberikan akses " . implode(', ', $addedLabels) . " kepada user {$user->name}";
        } elseif (empty($added) && !empty($removed)) {
            $desc = "{$actor} mencabut akses " . implode(', ', $removedLabels) . " dari user {$user->name}";
        } elseif (!empty($added) && !empty($removed)) {
            $desc = "{$actor} memberikan akses " . implode(', ', $addedLabels) . " dan mencabut " . implode(', ', $removedLabels) . " untuk user {$user->name}";
        } else {
            $desc = "{$actor} memperbarui konfigurasi hak akses user {$user->name}";
        }

        $oldLabels = array_values(array_map(fn($p) => $permMap[$p] ?? $p, $oldPermissions));
        $newLabels = array_values(array_map(fn($p) => $permMap[$p] ?? $p, $permissionNames));

        ActivityLogger::log(
            'UPDATE',
            'Manajemen Hak Akses',
            $desc,
            [
                'user'   => $user->name,
                'email'  => $user->email,
                'before' => ['Izin Khusus' => !empty($oldLabels) ? implode(', ', $oldLabels) : 'Tidak ada izin khusus'],
                'after'  => ['Izin Khusus' => !empty($newLabels) ? implode(', ', $newLabels) : 'Tidak ada izin khusus'],
            ],
            $request
        );

        return redirect()->route('admin-settings.permissions.index', ['user_id' => $user->id])
            ->with('success', "Hak akses khusus untuk \"{$user->name}\" berhasil diperbarui.");
    }
}
