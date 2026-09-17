@extends('layouts.app')

@section('title', 'Edit Profil — SIM-ASET')
@section('page-title', 'Pengaturan Profil')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Informasi Profil</h5>
                <p class="text-muted small mb-0">Perbarui informasi profil dan alamat email Anda.</p>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" id="avatar-preview" class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div id="avatar-preview-placeholder" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <img src="" alt="Avatar" id="avatar-preview" class="rounded-circle border d-none" style="width: 100px; height: 100px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="mt-3">
                            <label for="avatar" class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="fa-solid fa-camera me-1"></i> Ganti Foto
                            </label>
                            <input type="file" id="avatar" name="avatar" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewAvatar(event)">
                            <div class="form-text mt-1" style="font-size: 0.75rem;">JPG, PNG, WEBP. Maks 2MB.</div>
                            @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Role Akses</label>
                        <input type="text" class="form-control bg-light" value="{{ strtoupper(auth()->user()->roles->pluck('name')->first() ?? 'USER') }}" readonly>
                        <div class="form-text small">Hak akses tidak dapat diubah sendiri. Hubungi Super Admin.</div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        @if (session('status') === 'profile-updated' || session('success'))
                            <span class="text-success small fw-medium">
                                <i class="fa-solid fa-check-circle me-1"></i>Tersimpan
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Ubah Password</h5>
                <p class="text-muted small mb-0">Pastikan akun menggunakan password panjang & acak agar tetap aman.</p>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="update_password_password" class="form-label">Password Baru</label>
                        <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                        @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-secondary px-4">Simpan Password</button>
                        @if (session('status') === 'password-updated')
                            <span class="text-success small fw-medium">
                                <i class="fa-solid fa-check-circle me-1"></i>Password diubah
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-preview-placeholder');
            output.src = reader.result;
            output.classList.remove('d-none');
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
