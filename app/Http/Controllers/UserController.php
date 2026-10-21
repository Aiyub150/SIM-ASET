<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $roleFilter = trim((string) $request->input('role', ''));
        $statusFilter = trim((string) $request->input('status', ''));

        $sessionLifetimeMinutes = (int) config('session.lifetime', 120);
        $validSessionThreshold = now()->subMinutes($sessionLifetimeMinutes)->timestamp;

        $query = User::with('roles');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== '') {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        if ($statusFilter === 'active') {
            $activeUserIds = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '>=', $validSessionThreshold)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();
            $query->whereIn('id', $activeUserIds);
        } elseif ($statusFilter === 'inactive') {
            $activeUserIds = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '>=', $validSessionThreshold)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();
            $query->whereNotIn('id', $activeUserIds);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        // Ambil data sesi pengguna dari database sessions untuk status aktif & terakhir kali aktif
        $userIds = $users->pluck('id');
        $sessionStats = \Illuminate\Support\Facades\DB::table('sessions')
            ->whereIn('user_id', $userIds)
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('MAX(last_activity) as last_active'))
            ->groupBy('user_id')
            ->pluck('last_active', 'user_id');

        $users->getCollection()->transform(function ($user) use ($sessionStats, $validSessionThreshold) {
            $user->last_activity = $sessionStats->get($user->id);
            // Sesi berstatus aktif jika rekaman sesi berada dalam batas masa berlaku session lifetime
            $user->is_session_active = $user->last_activity && ($user->last_activity >= $validSessionThreshold);
            return $user;
        });

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'search', 'roleFilter', 'statusFilter'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.form', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', Rule::in(Role::pluck('name'))],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('images/users', 'public');
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'avatar'   => $avatarPath,
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['role']);

        \App\Services\ActivityLogger::log(
            'CREATE',
            'Pengguna',
            "Menambahkan pengguna baru: {$user->name} ({$user->email}) dengan peran {$data['role']}",
            ['user_id' => $user->id, 'role' => $data['role']],
            $request
        );

        return redirect()
            ->route('users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil ditambahkan dengan role: {$data['role']}");
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', 'string', Rule::in(Role::pluck('name'))],
            'avatar'=> ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        // Password opsional saat edit — hanya divalidasi jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);

        $dataToUpdate = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $dataToUpdate['avatar'] = $request->file('avatar')->store('images/users', 'public');
        }

        $beforeUser = [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->roles->pluck('name')->first() ?? 'User',
        ];

        $user->update($dataToUpdate);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles($data['role']);

        $afterUser = [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $data['role'],
            'password_changed' => $request->filled('password') ? 'Ya (Kata sandi baru)' : 'Tidak berubah',
        ];

        \App\Services\ActivityLogger::log(
            'UPDATE',
            'Pengguna',
            "Memperbarui data pengguna: {$user->name} ({$user->email})",
            [
                'user_id' => $user->id,
                'before'  => $beforeUser,
                'after'   => $afterUser,
            ],
            $request
        );

        return redirect()
            ->route('users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil diperbarui.");
    }
}
