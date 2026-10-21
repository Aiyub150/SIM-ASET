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
    <a href="{{ route('users.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Pengguna</span>
    </a>
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
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </a>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleFilter = document.getElementById('user_role_filter');
        if (roleFilter && typeof TomSelect !== 'undefined') {
            new TomSelect(roleFilter, {
                create: false,
                placeholder: 'Semua Peran / Role',
                allowEmptyOption: true,
                onChange: function() {
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
            new TomSelect(statusFilter, {
                create: false,
                controlInput: null,
                placeholder: 'Semua Status',
                allowEmptyOption: true,
                onChange: function() {
                    statusFilter.closest('form').submit();
                }
            });
        }
    });
</script>
@endpush

@endsection
