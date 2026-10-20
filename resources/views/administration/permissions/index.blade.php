@extends('layouts.app')

@section('title', 'Manajemen Hak Akses & Izin — SIM-ASET')
@section('page-title', 'Manajemen Hak Akses & Permissions')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Matriks Hak Akses & Izin Pengguna</h5>
                <p class="text-muted small mb-0">Kelola dan tinjau hak akses fungsional sistem, baik ringkasan per peran maupun pemberian izin khusus langsung kepada pengguna tertentu.</p>
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
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Matriks Hak Akses Antar Peran</h6>
                            <small class="text-muted">Tinjau seluruh izin modul yang aktif untuk tiap tingkatan peran dalam sistem.</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="min-width: 260px;">Modul & Nama Hak Akses</th>
                                    @foreach($roles as $r)
                                    <th class="text-center" style="min-width: 140px;">
                                        <div class="fw-bold">{{ $r->name }}</div>
                                        <a href="{{ route('admin-settings.roles.permissions.edit', $r) }}" class="badge bg-primary-subtle text-primary text-decoration-none border mt-1">
                                            <i class="fa-solid fa-key me-1"></i> Edit Izin
                                        </a>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedPermissions as $moduleName => $perms)
                                <tr class="table-light fw-bold">
                                    <td colspan="{{ count($roles) + 1 }}" class="ps-4 text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                        <i class="fa-solid fa-folder me-1"></i> Modul {{ $moduleName }}
                                    </td>
                                </tr>
                                @foreach($perms as $perm)
                                <tr>
                                    <td class="ps-4">
                                        <code>{{ $perm->name }}</code>
                                    </td>
                                    @foreach($roles as $r)
                                    <td class="text-center">
                                        @if($r->name === 'Super Admin' || $r->hasPermissionTo($perm->name))
                                            <span class="badge bg-success-subtle text-success p-1 px-2 rounded-circle" title="Aktif">
                                                <i class="fa-solid fa-check"></i>
                                            </span>
                                        @else
                                            <span class="text-muted opacity-25">&bull;</span>
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
                            <p class="text-muted small mb-3">Cari nama atau email pengguna. Anda dapat memberikan hak akses tambahan langsung (*Direct Permission*) khusus untuk akun yang dipilih.</p>
                            
                            <form method="GET" action="{{ route('admin-settings.permissions.index') }}" id="formSelectUser" class="row g-2 align-items-center">
                                <div class="col-md-7 col-lg-5">
                                    <select name="user_id" id="selectUserTom" class="form-select rounded-3" placeholder="Ketik nama atau email pengguna...">
                                        <option value="">-- Ketik untuk mencari pengguna --</option>
                                        @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ optional($selectedUser)->id === $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }}) — [{{ $u->roles->pluck('name')->first() ?? 'Tanpa Role' }}]
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">
                                        <i class="fa-solid fa-user-check me-1"></i> Buka Pengguna
                                    </button>
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
                                    <small class="text-muted">{{ $selectedUser->email }} &bull; Peran: <span class="badge bg-primary-subtle text-primary">{{ $selectedUser->roles->pluck('name')->first() ?? 'User' }}</span></small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="fa-solid fa-lock me-1"></i> Izin Peran Bawaan Terkunci
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
                                @foreach($groupedPermissions as $moduleName => $perms)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border rounded-4 shadow-none bg-light-subtle">
                                        <div class="card-header bg-transparent py-2 px-3 border-bottom">
                                            <span class="fw-bold text-primary small text-uppercase">
                                                <i class="fa-solid fa-folder me-1"></i> Modul {{ $moduleName }}
                                            </span>
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column gap-2">
                                            @foreach($perms as $perm)
                                            @php
                                                $hasViaRole = $selectedUser->hasPermissionTo($perm->name) && !$selectedUser->hasDirectPermission($perm->name);
                                                $hasDirect  = $selectedUser->hasDirectPermission($perm->name);
                                            @endphp
                                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-1">
                                                <div>
                                                    <label class="form-check-label small mb-0 text-dark" for="user_{{ $selectedUser->id }}_{{ str_replace('.', '_', $perm->name) }}">
                                                        <code>{{ $perm->name }}</code>
                                                    </label>
                                                    @if($hasViaRole)
                                                        <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size:0.62rem;">dari Peran</span>
                                                    @elseif($hasDirect)
                                                        <span class="badge bg-success-subtle text-success ms-1" style="font-size:0.62rem;">Khusus</span>
                                                    @endif
                                                </div>
                                                <input class="form-check-input ms-auto mt-0" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->name }}" 
                                                       id="user_{{ $selectedUser->id }}_{{ str_replace('.', '_', $perm->name) }}" 
                                                       {{ $hasDirect || $hasViaRole ? 'checked' : '' }}
                                                       {{ $hasViaRole ? 'disabled' : '' }}>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Izin yang ditandai <code>dari Peran</code> otomatis berlaku dari role pengguna. Centang izin lainnya untuk memberikan izin khusus tambahan.</span>
                                <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hak Akses {{ $selectedUser->name }}
                                </button>
                            </div>
                        </form>
                        @else
                        <div class="text-center py-5 text-muted border rounded-4 bg-light-subtle">
                            <i class="fa-solid fa-user-shield fs-1 opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Pengguna yang Dipilih</h6>
                            <p class="small text-muted mb-0">Gunakan kotak pencarian di atas untuk memilih akun pengguna yang ingin dikonfigurasikan hak aksesnya.</p>
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
            new TomSelect(selectEl, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Ketik nama atau email pengguna...',
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
