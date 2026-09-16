@extends('layouts.app')

@section('title', 'Dashboard — SIM-ASET')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Aset Barang</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($totalItems) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Stok Fisik</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($totalStock) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <h6 class="card-title text-black-50">Peminjaman Aktif</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($activeLoans) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <h6 class="card-title text-white-50">Peminjaman Terlambat</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($overdueLoans) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Peminjaman Terbaru</h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentLoans as $loan)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $loan->borrower->institution_name }}</strong><br>
                            <small class="text-muted">{{ $loan->borrow_date->format('d M Y') }} - {{ $loan->due_date->format('d M Y') }}</small>
                        </div>
                        <span class="badge {{ $loan->status === 'active' ? 'bg-warning' : 'bg-success' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">Belum ada peminjaman.</div>
                @endforelse
            </div>
            <div class="card-footer bg-white border-top-0 text-center">
                <a href="{{ route('loans.index') }}" class="text-decoration-none small">Lihat Semua Peminjaman &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Mutasi Stok Terbaru</h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentMovements as $movement)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $movement->item->name }}</strong><br>
                            <small class="text-muted">{{ $movement->created_at->format('d M Y H:i') }} oleh {{ $movement->user->name }}</small>
                        </div>
                        <span class="badge {{ $movement->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">Belum ada mutasi stok.</div>
                @endforelse
            </div>
            <div class="card-footer bg-white border-top-0 text-center">
                <a href="{{ route('stocks.index') }}" class="text-decoration-none small">Lihat Semua Mutasi &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Statistik Peminjaman & Pengembalian (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <canvas id="loanChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('loanChart').getContext('2d');
        const loanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Peminjaman Baru',
                        data: {!! json_encode($chartLoans) !!},
                        backgroundColor: 'rgba(236, 72, 153, 0.8)', // pink-500
                        borderColor: 'rgba(236, 72, 153, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengembalian Selesai',
                        data: {!! json_encode($chartReturns) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.8)', // green-500
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
@endpush
