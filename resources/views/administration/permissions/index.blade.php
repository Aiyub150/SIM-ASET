@extends('layouts.app')

@section('title', 'Manajemen Hak Akses & Izin — SIM-ASET')
@section('page-title', 'Manajemen Hak Akses & Permissions')

@section('content')
<style>
    .matrix-wrapper {
        position: relative;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }
    .matrix-table {
        min-width: 800px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .matrix-table th, 
    .matrix-table td {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .matrix-table th:last-child, 
    .matrix-table td:last-child {
        border-right: none;
    }
    .matrix-table tbody tr:last-child td {
        border-bottom: none;
    }
    .matrix-table thead th {
        border-bottom: 2px solid #cbd5e1;
        background-color: #f8fafc;
    }
    .matrix-sticky-col {
        position: sticky;
        left: 0;
        background-color: #ffffff;
        z-index: 5;
        border-right: 2px solid #cbd5e1 !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.03);
    }
    .matrix-table thead th.matrix-sticky-col {
        background-color: #f8fafc;
        z-index: 6;
        border-right: 2px solid #cbd5e1 !important;
    }
    .matrix-role-col {
        min-width: 140px;
        max-width: 180px;
    }
    .matrix-category-row td {
        border-top: 1px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        background-color: #f1f5f9 !important;
    }
    .ts-wrapper.form-select {
        padding: 0 !important;
        border: none !important;
    }
    .ts-wrapper .ts-control {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.5rem 0.85rem !important;
        min-height: 44px !important;
        box-shadow: none !important;
        background-color: #ffffff !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15) !important;
    }
    .ts-dropdown {
        border-radius: 10px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        z-index: 1050 !important;
        padding: 4px 0 !important;
    }
    .ts-dropdown .option {
        padding: 8px 12px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .ts-dropdown .option:last-child {
        border-bottom: none;
    }
    .ts-dropdown .active {
        background-color: #eff6ff !important;
        color: inherit !important;
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Matriks Hak Akses & Izin Pengguna</h5>
                <p class="text-secondary small mb-0">Kelola dan tinjau hak akses fungsional sistem secara transparan, baik perbandingan antar peran maupun izin khusus pengguna.</p>
            </div>
            <a href="{{ route('admin-settings.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="fa-solid fa-shield-halved me-1"></i> Kelola Daftar Peran
            </a>
        </div>
    </div>

    <!-- Nav Tabs -->
    <div class="col-12">
        <ul class="nav nav-pills gap-2 p-1 bg-white rounded-4 shadow-sm border" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($selectedUser) ? 'active' : '' }} rounded-3 px-3 fw-semibold small" 
                        id="pills-role-tab" data-bs-toggle="pill" data-bs-target="#pills-role" type="button" role="tab">
                    <i class="fa-solid fa-table-cells-large me-1"></i> Ringkasan Matriks Hak Akses Peran
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ !empty($selectedUser) ? 'active' : '' }} rounded-3 px-3 fw-semibold small" 
                        id="pills-user-tab" data-bs-toggle="pill" data-bs-target="#pills-user" type="button" role="tab">
                    <i class="fa-solid fa-user-gear me-1"></i> Hak Akses Khusus Pengguna (User Permissions)
                </button>
            </li>
        </ul>
    </div>

    <div class="col-12">
        <div class="tab-content" id="pills-tabContent">
            
            <!-- TAB 1: RINGKASAN MATRIKS ROLE -->
            <div class="tab-pane fade {{ empty($selectedUser) ? 'show active' : '' }}" id="pills-role" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Perbandingan Hak Akses Antar Peran</h6>
                            <small class="text-secondary">Tabel horizontal responsif dengan kolom nama izin terkunci (sticky) agar tetap mudah dibaca meskipun terdapat banyak peran.</small>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 font-monospace" style="font-size: 0.75rem;">
                            {{ $roles->count() }} Peran Terdaftar
                        </span>
                    </div>

                    <div class="matrix-wrapper">
                        <table class="table table-hover align-middle matrix-table" style="font-size: 0.85rem;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="px-4 py-3 align-middle matrix-sticky-col text-center" style="min-width: 320px;">
                                        <div class="text-uppercase text-secondary fw-bold" style="font-size: 0.88rem; letter-spacing: 0.5px;">Modul & Aksi Fungsional</div>
                                    </th>
                                    @foreach($roles as $r)
                                    <th class="text-center matrix-role-col px-3 py-3">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 160px;" title="{{ $r->name }}">{{ $r->name }}</div>
                                        <a href="{{ route('admin-settings.roles.permissions.edit', $r) }}" 
                                           class="btn btn-xs btn-outline-primary py-0 px-2 rounded-2 mt-1 text-decoration-none" 
                                           style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-sliders me-1"></i> Atur Izin
                                        </a>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionDefs as $moduleName => $perms)
                                <tr class="matrix-category-row">
                                    <td colspan="{{ count($roles) + 1 }}" class="text-start ps-3 ps-md-4 py-2 text-dark fw-bold text-uppercase" style="font-size: 0.90rem; letter-spacing: 0.8px;">
                                        <i class="fa-solid fa-folder-closed text-primary me-2"></i> Modul {{ $moduleName }}
                                    </td>
                                </tr>
                                @foreach($perms as $permName => $meta)
                                <tr>
                                    <td class="ps-4 py-2 matrix-sticky-col">
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $meta['label'] }}</div>
                                        <div class="text-secondary small" style="font-size: 0.75rem; line-height: 1.2;">{{ $meta['desc'] }}</div>
                                        <span class="badge bg-light text-dark font-monospace border mt-1" style="font-size: 0.65rem;">
                                            {{ $permName }}
                                        </span>
                                    </td>
                                    @foreach($roles as $r)
                                    <td class="text-center py-2">
                                        @if($r->name === 'Super Admin' || ($permissions->has($permName) && $r->hasPermissionTo($permName)))
                                            <span class="badge bg-success-subtle text-success p-2 rounded-circle" title="Diizinkan">
                                                <i class="fa-solid fa-check fs-6"></i>
                                            </span>
                                        @else
                                            <span class="text-secondary opacity-50">&bull;</span>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: HAK AKSES PENGGUNA (USER PERMISSIONS) -->
            <div class="tab-pane fade {{ !empty($selectedUser) ? 'show active' : '' }}" id="pills-user" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1">Pilih Pengguna untuk Mengatur Hak Akses Khusus</h6>
                            <p class="text-secondary small mb-3">Cari nama atau email pengguna. Anda dapat memberikan hak akses tambahan langsung (*Direct Permission*) khusus untuk akun yang dipilih tanpa harus mengubah peran dasarnya.</p>
                            
                            @php
                                $usersJson = $users->map(function($u) {
                                    return [
                                        'value' => (string) $u->id,
                                        'name' => $u->name,
                                        'email' => $u->email,
                                        'role' => $u->roles->pluck('name')->first() ?? 'Tanpa Role',
                                        'initial' => strtoupper(substr($u->name, 0, 1)),
                                        'avatar' => $u->avatar ? asset('storage/' . $u->avatar) : null,
                                        'avatar_file' => $u->avatar ? basename($u->avatar) : null,
                                    ];
                                })->values();
                            @endphp

                            <form method="GET" action="{{ route('admin-settings.permissions.index') }}" id="formSelectUser" class="row g-2 align-items-center">
                                <div class="col-md-9 col-lg-7">
                                    <select name="user_id" id="selectUserTom" class="form-select rounded-3" placeholder="Ketik nama atau email pengguna...">
                                        <option value="">-- Ketik untuk mencari pengguna --</option>
                                        @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ optional($selectedUser)->id === $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }}) — [{{ $u->roles->pluck('name')->first() ?? 'Tanpa Role' }}]
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>

                        @if($selectedUser)
                        <hr class="my-4 text-muted opacity-25">

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 bg-light rounded-4 mb-4 border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $selectedUser->name }}</h6>
                                    <small class="text-secondary">{{ $selectedUser->email }} &bull; Peran: <span class="badge bg-primary-subtle text-primary border">{{ $selectedUser->roles->pluck('name')->first() ?? 'User' }}</span></small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="fa-solid fa-lock me-1"></i> Izin Bawaan Peran
                                </span>
                                <span class="badge bg-success-subtle text-success border">
                                    <i class="fa-solid fa-key me-1"></i> Izin Khusus Langsung
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('admin-settings.permissions.users.update', $selectedUser) }}" method="POST" id="formUserPermissions">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4">
                                @foreach($permissionDefs as $moduleName => $perms)
                                <div class="col-md-6 col-lg-6">
                                    <div class="card h-100 border rounded-4 shadow-none" style="background-color: #f8fafc; border-color: #cbd5e1 !important;">
                                        <div class="card-header bg-white py-2 px-3 border-bottom text-center">
                                            <span class="fw-bold text-dark text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                                <i class="fa-solid fa-folder-closed text-primary me-2"></i> Modul {{ $moduleName }}
                                            </span>
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column gap-3">
                                            @foreach($perms as $permName => $meta)
                                            @php
                                                $hasViaRole = in_array($permName, $userRolePermissions ?? []);
                                                $hasDirect  = in_array($permName, $userDirectPermissions ?? []);
                                            @endphp
                                            <div class="p-2 rounded-3 bg-white border d-flex justify-content-between align-items-center" style="border-color: #e2e8f0 !important;">
                                                <div class="pe-3">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $meta['label'] }}</span>
                                                        @if($hasViaRole)
                                                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size:0.65rem;">Bawaan Peran</span>
                                                        @elseif($hasDirect)
                                                            <span class="badge bg-success-subtle text-success border" style="font-size:0.65rem;">Izin Khusus</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-secondary small" style="font-size: 0.76rem; line-height: 1.3;">{{ $meta['desc'] }}</div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.68rem; font-weight: 500;">
                                                            {{ $permName }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input cursor-pointer" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permName }}" 
                                                           id="user_{{ $selectedUser->id }}_{{ str_replace('.', '_', $permName) }}" 
                                                           style="width: 2.5em; height: 1.35em;"
                                                           {{ $hasDirect || $hasViaRole ? 'checked' : '' }}
                                                           {{ $hasViaRole ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <span class="text-secondary small">Izin dengan tanda <strong>Bawaan Peran</strong> otomatis aktif dari role pengguna. Centang izin lainnya untuk memberikan izin khusus tambahan kepada akun ini.</span>
                                <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hak Akses {{ $selectedUser->name }}
                                </button>
                            </div>
                        </form>
                        @else
                        <div class="text-center py-5 text-muted border rounded-4 bg-light-subtle">
                            <i class="fa-solid fa-user-shield fs-1 opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Pengguna yang Dipilih</h6>
                            <p class="small text-secondary mb-0">Gunakan kotak pencarian di atas untuk memilih akun pengguna yang ingin dikonfigurasikan hak aksesnya.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectEl = document.getElementById('selectUserTom');
        if (selectEl && typeof TomSelect !== 'undefined') {
            const userOptions = {!! json_encode($usersJson ?? []) !!};
            new TomSelect(selectEl, {
                options: userOptions,
                valueField: 'value',
                labelField: 'name',
                searchField: ['name', 'email', 'role'],
                create: false,
                placeholder: 'Ketik nama, email, atau peran pengguna...',
                render: {
                    option: function(data, escape) {
                        const avatarHtml = data.avatar 
                            ? `<img src="${escape(data.avatar)}" alt="Avatar" class="rounded-circle border shadow-sm" style="width: 36px; height: 36px; object-fit: cover; flex-shrink: 0;">`
                            : `<div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">${escape(data.initial)}</div>`;
                        
                        const fileBadge = data.avatar_file
                            ? `<span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem; font-weight: 500;" title="Nama file foto"><i class="fa-regular fa-image text-primary me-1"></i>${escape(data.avatar_file)}</span>`
                            : `<span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem; font-weight: 400;">Tanpa Foto</span>`;

                        return `<div class="d-flex align-items-center justify-content-between p-2 border-bottom border-light">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                ${avatarHtml}
                                <div class="text-truncate">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">${escape(data.name)}</div>
                                    <div class="d-flex align-items-center gap-1 text-secondary small text-truncate" style="font-size: 0.75rem;">
                                        <span>${escape(data.email)}</span>
                                        ${fileBadge}
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.72rem; flex-shrink: 0;">
                                ${escape(data.role)}
                            </span>
                        </div>`;
                    },
                    item: function(data, escape) {
                        const avatarHtml = data.avatar 
                            ? `<img src="${escape(data.avatar)}" alt="Avatar" class="rounded-circle me-2 border" style="width: 22px; height: 22px; object-fit: cover; flex-shrink: 0;">`
                            : `<span class="badge bg-primary-subtle text-primary rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px; font-size: 0.7rem; flex-shrink: 0;">${escape(data.initial)}</span>`;

                        const fileBadge = data.avatar_file
                            ? `<span class="badge bg-light text-secondary border ms-2" style="font-size: 0.7rem;"><i class="fa-regular fa-image text-primary me-1"></i>${escape(data.avatar_file)}</span>`
                            : '';

                        return `<div class="d-flex align-items-center justify-content-between w-100 gap-2">
                            <div class="d-flex align-items-center text-truncate">
                                ${avatarHtml}
                                <span class="fw-bold text-dark text-truncate">${escape(data.name)}</span>
                                <span class="text-secondary small ms-2 text-truncate">(${escape(data.email)})</span>
                                ${fileBadge}
                            </div>
                            <span class="badge bg-primary-subtle text-primary border ms-2" style="font-size: 0.72rem; flex-shrink: 0;">
                                ${escape(data.role)}
                            </span>
                        </div>`;
                    }
                },
                onChange: function(value) {
                    if (value) {
                        document.getElementById('formSelectUser').submit();
                    }
                }
            });
        }

        document.getElementById('formUserPermissions')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Perbarui Hak Akses?',
                text: 'Hak akses khusus untuk pengguna ini akan segera disimpan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
