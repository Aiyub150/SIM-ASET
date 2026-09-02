<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->pluck('name');
        return view('users.form', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', Rule::in(Role::pluck('name'))],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['role']);

        return redirect()
            ->route('users.index')
            ->with('success', "Pengguna \"{$user->name}\" berhasil ditambahkan dengan role: {$data['role']}");
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->pluck('name');
        return view('users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', 'string', Rule::in(Role::pluck('name'))],
        ];

        // Password opsional saat edit — hanya divalidasi jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles($data['role']);

        return redirect()
            ->route('users.index')
            ->with('success', "Data pengguna \"{$user->name}\" berhasil diperbarui.");
    }
}
