@extends('layouts.app')

@section('title', 'Manajemen Pengguna — SIM-ASET')
@section('page-title', 'Manajemen Pengguna')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Pengguna Sistem</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola akun dan role pengguna SIM-ASET</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:#2563eb; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem; font-weight:700; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
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
                        <td class="text-muted" style="font-size:.82rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center pe-4">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
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
