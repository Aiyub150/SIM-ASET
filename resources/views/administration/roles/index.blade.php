@extends('layouts.app')

@section('title', 'Manajemen Role Pengguna — SIM-ASET')
@section('page-title', 'Manajemen Role Pengguna')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Daftar Peran & Hak Akses Dinamis</h5>
                <p class="text-muted small mb-0">Kelola daftar peran pengguna dalam sistem. Anda dapat menambahkan role kustom baru kapan saja.</p>
            </div>
            <button class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fa-solid fa-plus me-1"></i> Tambah Peran Baru
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Peran / Role</th>
                            <th>Tipe Role</th>
                            <th>Jumlah Pengguna</th>
                            <th>Jumlah Izin (Permissions)</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded-circle p-2 me-2 {{ in_array($role->name, $systemRoles) ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $role->name }}</span>
                                        <small class="text-muted">Guard: {{ $role->guard_name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(in_array($role->name, $systemRoles))
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
                                    <a href="{{ route('admin-settings.permissions.index', ['role_id' => $role->id]) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-3" 
                                       title="Atur Hak Akses">
                                        <i class="fa-solid fa-key me-1"></i> Atur Izin
                                    </a>
                                    @if(!in_array($role->name, $systemRoles))
                                        <button class="btn btn-sm btn-outline-secondary rounded-3" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editRoleModal{{ $role->id }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('admin-settings.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @if(!in_array($role->name, $systemRoles))
                                <!-- Modal Edit Role -->
                                <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <form action="{{ route('admin-settings.roles.update', $role) }}" method="POST" class="modal-content border-0 rounded-4 shadow">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-0 pb-0">
                                                <h6 class="modal-title fw-bold">Ubah Nama Peran</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Nama Peran Baru <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $role->name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm rounded-3">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada role terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Role -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin-settings.roles.store') }}" method="POST" class="modal-content border-0 rounded-4 shadow">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Tambah Peran / Role Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Peran (Role) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Auditor Eksternal, Pengawas, dll" required>
                    <div class="form-text small">Hanya boleh huruf, angka, spasi, tanda hubung (-), dan garis bawah (_).</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm rounded-3">Simpan Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
