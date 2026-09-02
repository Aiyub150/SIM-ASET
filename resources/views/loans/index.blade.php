@extends('layouts.app')

@section('title', 'Daftar Peminjaman — SIM-ASET')
@section('page-title', 'Daftar Peminjaman')

@section('content')

@php $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'Admin']); @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Transaksi Peminjaman</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            @if($isAdmin)
                Daftar seluruh transaksi peminjaman aset daerah
            @else
                Daftar transaksi peminjaman yang Anda catat
            @endif
        </p>
    </div>
    <a href="{{ route('loans.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Buat Peminjaman
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Kode Transaksi</th>
                    <th>Instansi Peminjam</th>
                    @if($isAdmin)
                    <th>Pencatat</th>
                    @endif
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    @php $isOverdue = $loan->status === 'active' && $loan->due_date->isPast(); @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-600" style="font-weight:600; font-family:monospace; font-size:.82rem; color:#2563eb;">
                                {{ $loan->loan_code }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-500" style="font-weight:500;">{{ $loan->borrower->institution_name }}</span>
                        </td>
                        @if($isAdmin)
                        <td class="text-muted">{{ $loan->user->name }}</td>
                        @endif
                        <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                        <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                            {{ $loan->due_date->format('d/m/Y') }}
                            @if($isOverdue)
                                <br><small style="font-size:.72rem;">Terlambat!</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($loan->status === 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($isOverdue)
                                <span class="badge bg-danger">Terlambat</span>
                            @else
                                <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                            </svg>
                            Belum ada data peminjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $loans->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
