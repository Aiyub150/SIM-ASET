@extends('layouts.app')

@section('title', 'Manajemen Pengguna — SIM-ASET')
@section('page-title', 'Manajemen Pengguna')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Pengguna Sistem</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola akun dan role pengguna SIM-ASET</p>
    </div>
    @can('users.create')
    <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Pengguna
    </a>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status Sesi</th>
                    <th>Terakhir Aktif</th>
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
                                <span style="font-weight:500;">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:.7rem;">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                @php
                                    $badgeStyle = match($role->name) {
                                        'Super Admin'    => 'background:#fef3c7; color:#92400e;',
                                        'Admin'          => 'background:#eff6ff; color:#1e40af;',
                                        default          => 'background:#f1f5f9; color:#475569;',
                                    };
                                @endphp
                                <span class="badge" style="{{ $badgeStyle }}">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_session_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 0.75rem;">
                                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 6px; height: 6px;" role="status"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_activity)
                                @php
                                    $lastActiveCarbon = \Carbon\Carbon::createFromTimestamp($user->last_activity);
                                @endphp
                                <div class="fw-medium text-dark" style="font-size: 0.8rem;">
                                    {{ $lastActiveCarbon->diffForHumans() }}
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ \App\Services\TimezoneHelper::format($lastActiveCarbon, 'd M Y, H:i') }}
                                </div>
                            @else
                                <span class="text-muted small">Belum Pernah Login</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        @can('users.edit')
                        <td class="text-center pe-4">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('users.edit') ? 7 : 6 }}" class="text-center py-5 text-muted">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
