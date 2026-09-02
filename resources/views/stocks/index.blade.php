@extends('layouts.app')

@section('title', 'Kartu Stok — SIM-ASET')
@section('page-title', 'Kartu Stok (Buku Besar)')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Kartu Stok Aset</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Riwayat seluruh pergerakan fisik inventaris aset daerah</p>
    </div>
    <a href="{{ route('stocks.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Input Mutasi Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Waktu</th>
                    <th>No. Referensi</th>
                    <th>Barang</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Mutasi</th>
                    <th class="text-center">Saldo Awal</th>
                    <th class="text-center">Saldo Akhir</th>
                    <th class="pe-4">Pencatat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    @php $isIn = $movement->type === 'in'; @endphp
                    <tr>
                        <td class="ps-4" style="font-size:.8rem; color:#64748b;">
                            {{ $movement->created_at->format('d/m/Y') }}<br>
                            <span style="font-size:.73rem;">{{ $movement->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600;">
                                {{ $movement->reference_code }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:500;">{{ $movement->item->name }}</span><br>
                            <span style="font-family:monospace; font-size:.75rem; color:#64748b;">{{ $movement->item->sku }}</span>
                        </td>
                        <td class="text-center">
                            @if($isIn)
                                <span class="badge bg-success">Masuk</span>
                            @elseif($movement->type === 'out')
                                <span class="badge bg-secondary">Keluar</span>
                            @elseif($movement->type === 'broken')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge" style="background:#1e293b; color:#fff;">Hilang</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                            {{ $isIn ? '+' : '−' }}{{ $movement->qty }}
                        </td>
                        <td class="text-center text-muted">{{ $movement->balance_before }}</td>
                        <td class="text-center fw-bold">{{ $movement->balance_after }}</td>
                        <td class="pe-4 text-muted" style="font-size:.83rem;">{{ $movement->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                            Belum ada riwayat mutasi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
