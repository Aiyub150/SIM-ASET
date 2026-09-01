@extends('layouts.app')

@section('title', 'Daftar Peminjaman - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Daftar Transaksi Peminjaman</h3>
    <a href="{{ route('loans.create') }}" class="btn btn-primary">+ Buat Peminjaman</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Instansi Peminjam</th>
                        <th>Admin Pencatat</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        @php
                            $isOverdue = $loan->status === 'active' && $loan->due_date->isPast();
                        @endphp
                        <tr>
                            <td><strong>{{ $loan->loan_code }}</strong></td>
                            <td>{{ $loan->borrower->institution_name }}</td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                            <td class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                {{ $loan->due_date->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                @if($loan->status === 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($isOverdue)
                                    <span class="badge bg-danger">Terlambat</span>
                                @else
                                    <span class="badge bg-warning text-dark">Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-info text-white">
                                    Detail & Kembalikan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $loans->links('pagination::bootstrap-5') }}
</div>
@endsection