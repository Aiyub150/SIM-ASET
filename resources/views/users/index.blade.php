@extends('layouts.app')

@section('title', 'Manajemen Pengguna — SIM-ASET')
@section('page-title', 'Manajemen Pengguna')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-1">Pengguna Sistem</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola seluruh akun, role, dan pemantauan aktivitas pengguna SIM-ASET</p>
    </div>
    @can('users.create')
    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Pengguna</span>
    </button>
    @endcan
</div>

{{-- Bar Filter & Pencarian Pengguna --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Cari berdasarkan nama atau email pengguna..." 
                           value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role" id="user_role_filter" class="form-select">
                    <option value="">Semua Peran / Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" data-color="{{ $r->color ?? '#2563eb' }}" {{ ($roleFilter ?? '') === $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" id="user_status_filter" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ ($statusFilter ?? '') === 'active' ? 'selected' : '' }}>Sedang Aktif</option>
                    <option value="inactive" {{ ($statusFilter ?? '') === 'inactive' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Cari
                </button>
                @if(!empty($search) || !empty($roleFilter) || !empty($statusFilter))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nama Pengguna</th>
                    <th>Email</th>
                    <th>Peran / Role</th>
                    <th>Status Sesi</th>
                    <th>Aktivitas Terakhir</th>
                    <th>Terdaftar</th>
                    @can('users.edit')
                    <th class="text-center pe-4">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle shadow-sm"
                                         style="width:36px; height:36px; object-fit:cover; flex-shrink:0; border:1px solid #e2e8f0;">
                                @else
                                    <div style="width:36px; height:36px; border-radius:50%; background:#2563eb; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.8rem; font-weight:700; flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="fw-semibold text-dark d-block">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:.7rem;">Akun Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                @php
                                    $roleColor = $role->color ?? '#2563eb';
                                @endphp
                                <span class="badge rounded-pill px-2.5 py-1 font-monospace" 
                                      style="background-color: {{ $roleColor }}18; color: {{ $roleColor }}; border: 1px solid {{ $roleColor }}45; font-size: 0.78rem; font-weight: 600;">
                                    <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background-color: {{ $roleColor }};"></span>
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_session_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1 px-2.5 py-1" style="font-size: 0.75rem;">
                                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 6px; height: 6px;" role="status"></span>
                                    Sedang Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-moon me-1 opacity-50"></i> Offline
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_session_active)
                                <div class="text-success fw-semibold" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Mulai: {{ $user->last_login_at ? \App\Services\TimezoneHelper::format($user->last_login_at, 'H:i') : ($user->last_activity ? \App\Services\TimezoneHelper::format(\Carbon\Carbon::createFromTimestamp($user->last_activity), 'H:i') : '-') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Sesi aktif saat ini' }}
                                </div>
                            @elseif($user->last_activity || $user->last_login_at)
                                @php
                                    $recentTime = $user->last_activity 
                                        ? \Carbon\Carbon::createFromTimestamp($user->last_activity) 
                                        : $user->last_login_at;
                                @endphp
                                <div class="fw-medium text-dark" style="font-size: 0.8rem;">
                                    {{ $recentTime->diffForHumans() }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ \App\Services\TimezoneHelper::format($recentTime, 'd M Y, H:i') }}
                                </div>
                            @else
                                <span class="text-muted small">Belum Pernah Login</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        @can('users.edit')
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-user"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->roles->pluck('name')->first() ?? '' }}"
                                    data-avatar="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}">
                                Edit
                            </button>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('users.edit') ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-users-slash fs-2 opacity-25 mb-2 d-block"></i>
                            @if(!empty($search) || !empty($roleFilter) || !empty($statusFilter))
                                Tidak ada data pengguna yang cocok dengan filter pencarian.
                            @else
                                Belum ada data pengguna terdaftar.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white py-3 px-4 border-top">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- MODAL TAMBAH PENGGUNA BARU (POPUP MODAL TANPA REDIRECT) --}}
@can('users.create')
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createUserModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="form-create-user">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap staf / operator" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Resmi <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="nama@instansi.go.id" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="8" required placeholder="Minimal 8 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" required placeholder="Ulangi kata sandi">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peran / Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">— Pilih Role Akses —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FOTO PROFIL DENGAN LIVE PREVIEW --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Foto Avatar Pengguna <span class="text-muted fw-normal">(Opsional)</span></label>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div style="width: 70px; height: 70px; border: 2px dashed #cbd5e1; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc;">
                                    <img id="create_user_avatar_preview" src="" alt="Avatar" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    <div id="create_user_avatar_placeholder" class="text-muted text-center" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-user fa-lg d-block mb-1 text-secondary"></i>
                                        Foto
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <input type="file" name="avatar" id="create_user_avatar_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                    Format: JPG, PNG, WEBP (maks 2MB). Live preview tampil instan saat file dipilih.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- MODAL EDIT PENGGUNA (POPUP MODAL TANPA REDIRECT) --}}
@can('users.edit')
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="editUserModalLabel">Edit Data Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="form-edit-user">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_user_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Resmi <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_user_email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ubah Kata Sandi <span class="text-muted fw-normal">(Kosongkan jika tidak diganti)</span></label>
                            <input type="password" name="password" class="form-control" minlength="8" placeholder="Biarkan kosong jika tidak diubah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="Ulangi jika mengubah kata sandi">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peran / Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" id="edit_user_role" class="form-select" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- LIVE PREVIEW EDIT AVATAR PENGGUNA --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Foto Avatar Pengguna</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div style="width: 70px; height: 70px; border: 2px dashed #cbd5e1; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc;">
                                    <img id="edit_user_avatar_preview" src="" alt="Avatar" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    <div id="edit_user_avatar_placeholder" class="text-muted text-center" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-user fa-lg d-block mb-1 text-secondary"></i>
                                        Foto
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <input type="file" name="avatar" id="edit_user_avatar_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                    Pilih gambar baru untuk mengganti foto avatar saat ini. Format JPG, PNG, WEBP (maks 2MB).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleFilter = document.getElementById('user_role_filter');
        if (roleFilter && typeof TomSelect !== 'undefined') {
            let roleInit = false;
            new TomSelect(roleFilter, {
                create: false,
                placeholder: 'Semua Peran / Role',
                allowEmptyOption: true,
                onInitialize: function() {
                    roleInit = true;
                },
                onChange: function() {
                    if (!roleInit) return;
                    roleFilter.closest('form').submit();
                },
                render: {
                    option: function(data, escape) {
                        const color = data.$option ? data.$option.getAttribute('data-color') : '#2563eb';
                        if (!data.value) {
                            return `<div>${escape(data.text)}</div>`;
                        }
                        return `<div>
                            <span class="d-inline-block rounded-circle me-2" style="width: 8px; height: 8px; background-color: ${escape(color)};"></span>
                            ${escape(data.text)}
                        </div>`;
                    },
                    item: function(data, escape) {
                        const color = data.$option ? data.$option.getAttribute('data-color') : '#2563eb';
                        if (!data.value) {
                            return `<div>${escape(data.text)}</div>`;
                        }
                        return `<div>
                            <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background-color: ${escape(color)};"></span>
                            ${escape(data.text)}
                        </div>`;
                    }
                }
            });
        }

        const statusFilter = document.getElementById('user_status_filter');
        if (statusFilter && typeof TomSelect !== 'undefined') {
            let statusInit = false;
            new TomSelect(statusFilter, {
                create: false,
                controlInput: null,
                placeholder: 'Semua Status',
                allowEmptyOption: true,
                onInitialize: function() {
                    statusInit = true;
                },
                onChange: function() {
                    if (!statusInit) return;
                    statusFilter.closest('form').submit();
                }
            });
        }
        // TomSelect untuk Pilihan Role di Modal Tambah & Edit Pengguna
        ['create_user_role', 'edit_user_role'].forEach(id => {
            const el = document.getElementById(id);
            if (el && typeof TomSelect !== 'undefined') {
                new TomSelect(el, {
                    create: false,
                    placeholder: '— Pilih Peran Pengguna —'
                });
            }
        });

        // --- Modal Edit Pengguna & Live Avatar Preview ---
        function setupAvatarPreview(fileInputId, previewImgId, placeholderId) {
            const input = document.getElementById(fileInputId);
            const preview = document.getElementById(previewImgId);
            const placeholder = document.getElementById(placeholderId);

            if (!input || !preview) return;

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    const orig = preview.getAttribute('data-original-src');
                    if (orig) {
                        preview.src = orig;
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    } else {
                        preview.src = '';
                        preview.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'block';
                    }
                }
            });
        }

        setupAvatarPreview('create_user_avatar_input', 'create_user_avatar_preview', 'create_user_avatar_placeholder');
        setupAvatarPreview('edit_user_avatar_input', 'edit_user_avatar_preview', 'edit_user_avatar_placeholder');

        // Handler klik tombol Edit Pengguna
        document.querySelectorAll('.btn-edit-user').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const avatar = this.getAttribute('data-avatar');

                const form = document.getElementById('form-edit-user');
                form.action = `/users/${id}`;
                document.getElementById('edit_user_name').value = name;
                document.getElementById('edit_user_email').value = email;
                document.getElementById('edit_user_role').value = role;

                const editInput = document.getElementById('edit_user_avatar_input');
                const editPreview = document.getElementById('edit_user_avatar_preview');
                const editHolder = document.getElementById('edit_user_avatar_placeholder');
                if (editInput) editInput.value = '';

                if (avatar && avatar.trim() !== '') {
                    editPreview.src = avatar;
                    editPreview.setAttribute('data-original-src', avatar);
                    editPreview.style.display = 'block';
                    if (editHolder) editHolder.style.display = 'none';
                } else {
                    editPreview.src = '';
                    editPreview.removeAttribute('data-original-src');
                    editPreview.style.display = 'none';
                    if (editHolder) editHolder.style.display = 'block';
                }

                const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            });
        });
    });
</script>
@endpush

@endsection
