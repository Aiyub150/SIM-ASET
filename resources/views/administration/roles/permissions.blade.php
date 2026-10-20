@extends('layouts.app')

@section('title', 'Atur Hak Akses: ' . $role->name . ' — SIM-ASET')
@section('page-title', 'Pengaturan Hak Akses Peran')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <i class="fa-solid fa-shield-halved fs-6"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark">Hak Akses untuk Peran: <span class="text-primary">{{ $role->name }}</span></h5>
                </div>
                <p class="text-muted small mb-0">
                    Konfigurasikan izin granular apa saja yang dapat dilakukan oleh pengguna dengan peran <strong>{{ $role->name }}</strong>.
                    @if(in_array($role->name, $systemRoles))
                        <span class="badge bg-secondary-subtle text-secondary ms-1">Peran Inti Sistem</span>
                    @else
                        <span class="badge bg-success-subtle text-success ms-1">Peran Kustom</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('admin-settings.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Peran
            </a>
        </div>
    </div>

    @if($role->name === 'Super Admin')
    <div class="col-12">
        <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 d-flex align-items-start gap-3">
            <i class="fa-solid fa-circle-info fs-4 text-primary mt-1"></i>
            <div>
                <h6 class="fw-bold mb-1">Peran Super Admin Memiliki Akses Penuh (Bypass All)</h6>
                <p class="small text-muted mb-0">
                    Sesuai prinsip arsitektur keamanan sistem, peran <strong>Super Admin</strong> secara otomatis memiliki seluruh hak akses dan izin eksekutif dalam sistem SIM-ASET. Izin tidak dapat dikurangi untuk mencegah penguncian sistem (lockout).
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="col-12">
        <form action="{{ route('admin-settings.roles.permissions.update', $role) }}" method="POST" id="formRolePermissions">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-2 py-1">
                            <span id="checkedCount">{{ $role->permissions->count() }}</span> Izin Terpilih
                        </span>
                    </div>
                    @if($role->name !== 'Super Admin')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light border rounded-3" onclick="selectAllPermissions(true)">
                            <i class="fa-solid fa-check-double me-1 text-primary"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-light border rounded-3" onclick="selectAllPermissions(false)">
                            <i class="fa-solid fa-xmark me-1 text-danger"></i> Kosongkan
                        </button>
                    </div>
                    @endif
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        @foreach($groupedPermissions as $moduleName => $perms)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border rounded-4 shadow-none bg-light-subtle">
                                <div class="card-header bg-transparent py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small text-uppercase">
                                        <i class="fa-solid fa-layer-group me-1"></i> Modul {{ $moduleName }}
                                    </span>
                                    @if($role->name !== 'Super Admin')
                                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" 
                                            onclick="toggleModuleCheckboxes('module_{{ Str::slug($moduleName) }}')" 
                                            style="font-size: 0.72rem;">
                                        Toggle
                                    </button>
                                    @endif
                                </div>
                                <div class="card-body p-3 d-flex flex-column gap-2" id="module_{{ Str::slug($moduleName) }}">
                                    @foreach($perms as $perm)
                                    <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-1">
                                        <label class="form-check-label small mb-0 pe-2 text-dark cursor-pointer" for="perm_{{ $perm->id }}">
                                            <code>{{ $perm->name }}</code>
                                        </label>
                                        <input class="form-check-input perm-checkbox ms-auto mt-0" 
                                               type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $perm->name }}" 
                                               id="perm_{{ $perm->id }}"
                                               {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                               {{ $role->name === 'Super Admin' ? 'checked disabled' : '' }}
                                               onchange="updateCheckedCount()">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($role->name !== 'Super Admin')
                <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Perubahan akan langsung berlaku bagi pengguna yang memiliki peran <strong>{{ $role->name }}</strong>.</span>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hak Akses
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateCheckedCount() {
        const checked = document.querySelectorAll('.perm-checkbox:checked').length;
        const countEl = document.getElementById('checkedCount');
        if (countEl) countEl.innerText = checked;
    }

    function selectAllPermissions(select) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            if (!cb.disabled) cb.checked = select;
        });
        updateCheckedCount();
    }

    function toggleModuleCheckboxes(moduleId) {
        const container = document.getElementById(moduleId);
        if (!container) return;
        const cbs = container.querySelectorAll('.perm-checkbox');
        const anyUnchecked = Array.from(cbs).some(cb => !cb.checked);
        cbs.forEach(cb => {
            if (!cb.disabled) cb.checked = anyUnchecked;
        });
        updateCheckedCount();
    }

    document.getElementById('formRolePermissions')?.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Hak Akses?',
            text: 'Perubahan hak akses peran {{ $role->name }} akan langsung diperbarui.',
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
</script>
@endpush
