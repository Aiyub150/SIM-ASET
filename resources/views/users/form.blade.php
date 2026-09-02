@extends('layouts.app')

@section('title', isset($user) ? 'Edit Pengguna — SIM-ASET' : 'Tambah Pengguna — SIM-ASET')
@section('page-title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">{{ isset($user) ? 'Perbarui data akun dan role pengguna' : 'Buat akun baru dan tentukan role-nya' }}</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                {{ isset($user) ? 'Form Edit Pengguna' : 'Form Pengguna Baru' }}
            </div>
            <div class="card-body p-4">
                <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name ?? '') }}"
                               placeholder="Contoh: Budi Santoso" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email ?? '') }}"
                               placeholder="nama@pemda.go.id" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label class="form-label">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">— Pilih Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}"
                                    {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Super Admin: akses penuh + user management · Admin: akses operasional · Staff Logistik: hanya peminjaman miliknya
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Password
                            @if(isset($user))
                                <span class="text-muted" style="font-weight:400; font-size:.8rem;">(kosongkan jika tidak diubah)</span>
                            @else
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ isset($user) ? '••••••••' : 'Minimal 8 karakter' }}"
                               {{ isset($user) ? '' : 'required' }}>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            Konfirmasi Password
                            @unless(isset($user))
                                <span class="text-danger">*</span>
                            @endunless
                        </label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="{{ isset($user) ? '••••••••' : 'Ulangi password' }}"
                               {{ isset($user) ? '' : 'required' }}>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            {{ isset($user) ? 'Simpan Perubahan' : 'Buat Pengguna' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
