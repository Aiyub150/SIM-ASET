@extends('layouts.app')

@section('title', 'Manajemen Role Pengguna — SIM-ASET')
@section('page-title', 'Manajemen Role Pengguna')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Daftar Peran & Hak Akses Dinamis</h5>
                <p class="text-muted small mb-0">Kelola daftar peran pengguna dalam sistem. Anda dapat menyesuaikan nama peran, memilih warna identitas, dan menambahkan role kustom baru.</p>
            </div>
            <button class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fa-solid fa-plus me-1"></i> Tambah Peran Baru
            </button>
        </div>
    </div>

    <!-- Filter & Search Role -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin-settings.roles.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama peran / role..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">
                            <i class="fa-solid fa-filter me-1"></i> Cari
                        </button>
                        @if(!empty($search))
                        <a href="{{ route('admin-settings.roles.index') }}" class="btn btn-light btn-sm rounded-3 px-3 text-secondary border">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </a>
                        @endif
                    </div>
                    @if(!empty($search))
                    <div class="col-12 mt-2">
                        <span class="badge bg-light text-secondary border">Menampilkan hasil pencarian untuk: "<strong>{{ $search }}</strong>"</span>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Peran / Role</th>
                            <th>Warna & Tampilan</th>
                            <th>Tipe Role</th>
                            <th>Jumlah Pengguna</th>
                            <th>Jumlah Izin</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        @php
                            $roleColor = $role->color ?? '#2563eb';
                            $normalized = strtolower(str_replace([' ', '-', '_'], '', $role->name));
                            $isCoreRole = in_array($role->id, [1, 2, 3]) || in_array($normalized, ['superadmin', 'admin', 'stafflogistik']);
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px; background-color: {{ $roleColor }}20; color: {{ $roleColor }};">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $role->name }}</span>
                                        <small class="text-muted">Guard: {{ $role->guard_name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1 font-monospace" 
                                      style="background-color: {{ $roleColor }}18; color: {{ $roleColor }}; border: 1px solid {{ $roleColor }}45; font-size: 0.82rem; font-weight: 600;">
                                    <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $roleColor }};"></span>
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td>
                                @if($isCoreRole)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="fa-solid fa-lock me-1"></i> Bawaan Sistem
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fa-solid fa-user-gear me-1"></i> Peran Kustom
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $role->users_count }} Pengguna
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $role->permissions_count }} Permissions
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin-settings.roles.permissions.edit', $role) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-3" 
                                       title="Atur Hak Akses Peran Ini">
                                        <i class="fa-solid fa-key me-1"></i> Atur Izin
                                    </a>
                                    <!-- Tombol Ubah Nama & Warna (Tersedia untuk semua peran) -->
                                    <button class="btn btn-sm btn-outline-secondary rounded-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editRoleModal{{ $role->id }}"
                                            title="Ubah Nama & Warna Peran">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @if(!$isCoreRole)
                                        <form action="{{ route('admin-settings.roles.destroy', $role) }}" 
                                              method="POST" 
                                              class="d-inline form-delete-role"
                                              data-role-name="{{ $role->name }}"
                                              data-users-count="{{ $role->users_count }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" {{ $role->users_count > 0 ? 'disabled' : '' }} title="Hapus Peran">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal Edit Role -->
                                <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <form action="{{ route('admin-settings.roles.update', $role) }}" method="POST" class="modal-content border-0 rounded-4 shadow">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold">Ubah Nama & Warna Peran</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nama Peran <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $role->name }}" required>
                                                    @if($isCoreRole)
                                                        <div class="form-text small text-muted"><i class="fa-solid fa-circle-info me-1"></i> Ini adalah peran inti sistem. Anda bebas menyesuaikan namanya (misal: "Superadmin" tanpa spasi).</div>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Warna Badge Identitas Peran</label>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <input type="color" name="color" id="roleColorInput{{ $role->id }}" class="form-control form-control-color rounded-3 border" value="{{ $roleColor }}" style="width: 48px; height: 38px; cursor: pointer;">
                                                        <span class="font-monospace small text-muted" id="roleColorText{{ $role->id }}">{{ $roleColor }}</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 pt-1">
                                                        @foreach(['#2563eb' => 'Biru', '#059669' => 'Hijau', '#d97706' => 'Amber', '#dc2626' => 'Merah', '#7c3aed' => 'Ungu', '#0891b2' => 'Cyan', '#ea580c' => 'Oranye', '#475569' => 'Slate'] as $hex => $label)
                                                            <button type="button" 
                                                                    class="btn btn-sm rounded-circle p-0 border border-2 shadow-sm color-preset-btn" 
                                                                    style="width: 26px; height: 26px; background-color: {{ $hex }}; border-color: {{ $roleColor === $hex ? '#000' : 'transparent' }} !important;"
                                                                    title="{{ $label }}"
                                                                    onclick="selectRoleColor('{{ $role->id }}', '{{ $hex }}')"></button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-shield-halved fs-2 opacity-25 mb-2 d-block"></i>
                                @if(!empty($search))
                                    Tidak ada peran yang cocok dengan kata kunci "<strong>{{ $search }}</strong>".
                                @else
                                    Belum ada peran terdaftar dalam sistem.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
            <div class="card-footer bg-white py-3 px-4 border-top">
                {{ $roles->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create Role -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <form action="{{ route('admin-settings.roles.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Tambah Peran / Role Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Peran (Role) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Auditor Eksternal, Pengawas, Pemkab, dll" required>
                    <div class="form-text small">Hanya boleh huruf, angka, spasi, tanda minus (-), dan garis bawah (_).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Warna Badge Identitas Peran</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="color" name="color" id="createRoleColorInput" class="form-control form-control-color rounded-3 border" value="#2563eb" style="width: 48px; height: 38px; cursor: pointer;">
                        <span class="font-monospace small text-muted" id="createRoleColorText">#2563eb</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        @foreach(['#2563eb' => 'Biru', '#059669' => 'Hijau', '#d97706' => 'Amber', '#dc2626' => 'Merah', '#7c3aed' => 'Ungu', '#0891b2' => 'Cyan', '#ea580c' => 'Oranye', '#475569' => 'Slate'] as $hex => $label)
                            <button type="button" 
                                    class="btn btn-sm rounded-circle p-0 border border-2 shadow-sm" 
                                    style="width: 26px; height: 26px; background-color: {{ $hex }};"
                                    title="{{ $label }}"
                                    onclick="selectCreateRoleColor('{{ $hex }}')"></button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan Role</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function selectRoleColor(roleId, hex) {
        const input = document.getElementById('roleColorInput' + roleId);
        const text = document.getElementById('roleColorText' + roleId);
        if (input) input.value = hex;
        if (text) text.textContent = hex;
    }

    function selectCreateRoleColor(hex) {
        const input = document.getElementById('createRoleColorInput');
        const text = document.getElementById('createRoleColorText');
        if (input) input.value = hex;
        if (text) text.textContent = hex;
    }

    document.querySelectorAll('[id^="roleColorInput"]').forEach(function(input) {
        input.addEventListener('input', function() {
            const roleId = this.id.replace('roleColorInput', '');
            const text = document.getElementById('roleColorText' + roleId);
            if (text) text.textContent = this.value;
        });
    });

    document.getElementById('createRoleColorInput')?.addEventListener('input', function() {
        const text = document.getElementById('createRoleColorText');
        if (text) text.textContent = this.value;
    });

    // SweetAlert Konfirmasi Hapus Role
    document.querySelectorAll('.form-delete-role').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const roleName = this.dataset.roleName;
            Swal.fire({
                title: 'Hapus Peran?',
                text: `Apakah Anda yakin ingin menghapus peran "${roleName}"? Aksi ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
