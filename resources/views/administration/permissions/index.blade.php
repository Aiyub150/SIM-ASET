@extends('layouts.app')

@section('title', 'Manajemen Hak Akses & Izin — SIM-ASET')
@section('page-title', 'Manajemen Hak Akses & Permissions')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Matriks Hak Akses Dinamis</h5>
                <p class="text-muted small mb-0">Atur izin akses fungsional sistem baik per peran (Role-level) maupun pengecualian khusus per pengguna (Direct User Override).</p>
            </div>
            <a href="{{ route('admin-settings.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Daftar Peran
            </a>
        </div>
    </div>

    <!-- Nav Tabs -->
    <div class="col-12">
        <ul class="nav nav-pills gap-2 p-1 bg-white rounded-4 shadow-sm border" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ empty($selectedUser) ? 'active' : '' }} rounded-3 px-3 fw-semibold small" 
                        id="pills-role-tab" data-bs-toggle="pill" data-bs-target="#pills-role" type="button" role="tab">
                    <i class="fa-solid fa-shield-halved me-1"></i> Hak Akses Berdasarkan Peran (Role Matrix)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ !empty($selectedUser) ? 'active' : '' }} rounded-3 px-3 fw-semibold small" 
                        id="pills-user-tab" data-bs-toggle="pill" data-bs-target="#pills-user" type="button" role="tab">
                    <i class="fa-solid fa-user-lock me-1"></i> Pengecualian Khusus Pengguna (User Override)
                </button>
            </li>
        </ul>
    </div>

    <div class="col-12">
        <div class="tab-content" id="pills-tabContent">
            <!-- TAB 1: ROLE PERMISSION MATRIX -->
            <div class="tab-pane fade {{ empty($selectedUser) ? 'show active' : '' }}" id="pills-role" role="tabpanel">
                @foreach($roles as $role)
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-circle me-2">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $role->name }}</h6>
                                <small class="text-muted">{{ in_array($role->name, $systemRoles) ? 'Peran Inti Sistem' : 'Peran Kustom' }} &bull; Total {{ $role->permissions->count() }} Izin Aktif</small>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('admin-settings.permissions.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @foreach($groupedPermissions as $moduleName => $perms)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                        <h6 class="fw-bold text-primary small text-uppercase mb-3 pb-2 border-bottom">
                                            <i class="fa-solid fa-folder me-1"></i> Modul {{ $moduleName }}
                                        </h6>
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($perms as $perm)
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->name }}" 
                                                       id="role_{{ $role->id }}_{{ str_replace('.', '_', $perm->name) }}"
                                                       {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                                       {{ $role->name === 'Super Admin' ? 'checked disabled' : '' }}>
                                                <label class="form-check-label small" for="role_{{ $role->id }}_{{ str_replace('.', '_', $perm->name) }}">
                                                    <code>{{ $perm->name }}</code>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if($role->name !== 'Super Admin')
                        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hak Akses {{ $role->name }}
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
                @endforeach
            </div>

            <!-- TAB 2: USER DIRECT PERMISSION OVERRIDE -->
            <div class="tab-pane fade {{ !empty($selectedUser) ? 'show active' : '' }}" id="pills-user" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">Pilih Pengguna untuk Diberikan Izin Khusus (Override)</h6>
                        <form method="GET" action="{{ route('admin-settings.permissions.index') }}" class="row g-2 align-items-center mb-4">
                            <div class="col-md-6 col-lg-4">
                                <select name="user_id" class="form-select rounded-3" onchange="this.form.submit()">
                                    <option value="">-- Pilih Pengguna --</option>
                                    @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ optional($selectedUser)->id === $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }}) — [{{ $u->roles->pluck('name')->first() ?? 'Tanpa Role' }}]
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-light btn-sm rounded-3">Buka Akses Pengguna</button>
                            </div>
                        </form>

                        @if($selectedUser)
                        <div class="alert alert-info border-0 rounded-3 small mb-4">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            <strong>Catatan Arsitektur:</strong> Izin yang dicentang di bawah ini diberikan langsung (*Direct Permission*) kepada <strong>{{ $selectedUser->name }}</strong>. Pengaturan ini <u>hanya berlaku untuk user ini saja</u> dan <strong>tidak akan mengubah</strong> hak akses pengguna lain yang memiliki role sama ({{ $selectedUser->roles->pluck('name')->first() ?? 'User' }}).
                        </div>

                        <form action="{{ route('admin-settings.permissions.users.update', $selectedUser) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                @foreach($groupedPermissions as $moduleName => $perms)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                        <h6 class="fw-bold text-primary small text-uppercase mb-3 pb-2 border-bottom">
                                            <i class="fa-solid fa-folder me-1"></i> Modul {{ $moduleName }}
                                        </h6>
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($perms as $perm)
                                            @php
                                                $hasViaRole = $selectedUser->hasPermissionViaRole($perm);
                                                $hasDirect = $selectedUser->hasDirectPermission($perm->name);
                                            @endphp
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $perm->name }}" 
                                                       id="user_{{ $selectedUser->id }}_{{ str_replace('.', '_', $perm->name) }}"
                                                       {{ $hasDirect || $hasViaRole ? 'checked' : '' }}
                                                       {{ $hasViaRole ? 'disabled' : '' }}>
                                                <label class="form-check-label small" for="user_{{ $selectedUser->id }}_{{ str_replace('.', '_', $perm->name) }}">
                                                    <code>{{ $perm->name }}</code>
                                                    @if($hasViaRole)
                                                        <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size:0.65rem;">dari Role</span>
                                                    @elseif($hasDirect)
                                                        <span class="badge bg-success-subtle text-success ms-1" style="font-size:0.65rem;">Khusus</span>
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4 shadow-sm">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengecualian Izin untuk {{ $selectedUser->name }}
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
