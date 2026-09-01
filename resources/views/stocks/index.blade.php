@extends('layouts.app')

@section('title', 'Buku Besar / Kartu Stok Aset - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Kartu Stok Aset (Buku Besar)</h3>
    <a href="{{ route('stocks.create') }}" class="btn btn-primary">+ Input Mutasi Baru</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu Transaksi</th>
                        <th>No. Referensi</th>
                        <th>Barang (SKU)</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Mutasi (Qty)</th>
                        <th class="text-center">Saldo Awal</th>
                        <th class="text-center">Saldo Akhir</th>
                        <th>Admin Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        @php
                            $isIn = $movement->type === 'in';
                        @endphp
                        <tr>
                            <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $movement->reference_code }}</strong></td>
                            <td>{{ $movement->item->name }} <br> <small class="text-muted">{{ $movement->item->sku }}</small></td>
                            <td class="text-center">
                                @if($isIn)
                                    <span class="badge bg-success">Masuk</span>
                                @elseif($movement->type === 'out')
                                    <span class="badge bg-secondary">Keluar</span>
                                @elseif($movement->type === 'broken')
                                    <span class="badge bg-danger">Rusak</span>
                                @else
                                    <span class="badge bg-dark">Hilang</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                                {{ $isIn ? '+' : '-' }} {{ $movement->qty }}
                            </td>
                            <td class="text-center text-muted">{{ $movement->balance_before }}</td>
                            <td class="text-center fw-bold">{{ $movement->balance_after }}</td>
                            <td>{{ $movement->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $movements->links('pagination::bootstrap-5') }}
</div>
@endsection