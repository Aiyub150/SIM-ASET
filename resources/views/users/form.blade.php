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
                <form id="userForm" action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}"
                      method="POST" enctype="multipart/form-data">
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
                        <label class="form-label fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" id="role_select" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">— Cari dan Pilih Role —</option>
                            @foreach($roles as $role)
                                @php
                                    $roleName = is_string($role) ? $role : $role->name;
                                    $roleColor = is_string($role) ? '#2563eb' : ($role->color ?? '#2563eb');
                                    $isSelected = old('role', isset($user) ? $user->roles->first()?->name : '') === $roleName;
                                @endphp
                                <option value="{{ $roleName }}" 
                                        data-color="{{ $roleColor }}"
                                        {{ $isSelected ? 'selected' : '' }}>
                                    {{ $roleName }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Ketik nama peran untuk mencari secara cepat. Setiap peran membawa hak akses spesifik sesuai matriks izin.
                        </div>
                    </div>

                    {{-- Foto Profil --}}
                    <div class="mb-3">
                        <label class="form-label">Foto Profil (Opsional)</label>
                        @if(isset($user) && $user->avatar)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Format yang didukung: JPG, PNG, WEBP. Maksimal 2MB.
                        </div>
                        @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role_select');
        if (roleSelect && typeof TomSelect !== 'undefined') {
            new TomSelect(roleSelect, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: '— Cari & Pilih Role —',
                render: {
                    option: function(data, escape) {
                        const color = data.$option ? data.$option.getAttribute('data-color') : '#2563eb';
                        return `<div>
                            <span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: ${escape(color)};"></span>
                            <span class="fw-semibold">${escape(data.text)}</span>
                        </div>`;
                    },
                    item: function(data, escape) {
                        const color = data.$option ? data.$option.getAttribute('data-color') : '#2563eb';
                        return `<div>
                            <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: ${escape(color)};"></span>
                            ${escape(data.text)}
                        </div>`;
                    },
                    no_results: function(data, escape) {
                        return '<div class="no-results" style="padding: 10px; color: #6c757d;">Tidak ada role yang cocok.</div>';
                    }
                }
            });
        }

        const userForm = document.getElementById('userForm');
        if (userForm) {
            userForm.addEventListener('submit', function (e) {
                if (userForm.dataset.confirmed === 'true') {
                    return;
                }
                e.preventDefault();

                if (!userForm.checkValidity()) {
                    userForm.reportValidity();
                    return;
                }

                const isEdit = {{ isset($user) ? 'true' : 'false' }};
                Swal.fire({
                    title: isEdit ? 'Simpan Perubahan Akun?' : 'Buat Pengguna Baru?',
                    text: isEdit 
                        ? 'Pastikan data dan role pengguna sudah sesuai sebelum disimpan.' 
                        : 'Akun baru akan dibuat dan dapat langsung digunakan untuk masuk sistem.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: isEdit ? 'Ya, Simpan' : 'Ya, Buat Pengguna',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        userForm.dataset.confirmed = 'true';
                        userForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush

@endsection
