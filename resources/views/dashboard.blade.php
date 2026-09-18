@extends('layouts.app')

@section('title', 'Dashboard — SIM-ASET')
@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);">
            <div class="card-body text-white d-flex align-items-center gap-3 py-4 position-relative">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
                @else
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-grow-1">
                    <h5 class="mb-1 fw-bold">Selamat datang, {{ auth()->user()->name }}!</h5>
                    <div class="d-flex align-items-center gap-3 text-white-50" style="font-size: 0.85rem;">
                        <span><i class="fa-regular fa-envelope me-1"></i> {{ auth()->user()->email }}</span>
                        <span class="badge bg-light text-dark"><i class="fa-solid fa-shield-halved me-1"></i> {{ strtoupper(auth()->user()->roles->pluck('name')->first() ?? 'User') }}</span>
                    </div>
                </div>
                <div class="d-none d-md-flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-user-pen me-1"></i> Edit Profil</a>
                    @hasanyrole('Super Admin|Admin')
                        <a href="{{ route('loans.create') }}" class="btn btn-sm btn-light text-primary"><i class="fa-solid fa-plus me-1"></i> Peminjaman Baru</a>
                    @else
                        <a href="{{ route('loans.index') }}" class="btn btn-sm btn-light text-primary"><i class="fa-solid fa-list me-1"></i> Peminjaman Saya</a>
                    @endhasanyrole
                </div>
            </div>
            {{-- Mobile Actions --}}
            <div class="card-footer bg-transparent border-top border-white-50 d-md-none d-flex gap-2 p-3">
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light flex-fill"><i class="fa-solid fa-user-pen me-1"></i> Profil</a>
                @hasanyrole('Super Admin|Admin')
                    <a href="{{ route('loans.create') }}" class="btn btn-sm btn-light text-primary flex-fill"><i class="fa-solid fa-plus me-1"></i> Peminjaman</a>
                @else
                    <a href="{{ route('loans.index') }}" class="btn btn-sm btn-light text-primary flex-fill"><i class="fa-solid fa-list me-1"></i> Peminjaman</a>
                @endhasanyrole
            </div>
        </div>
    </div>
</div>

@if($isStaff)
    {{-- STAFF LAYOUT --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card bg-white text-dark h-100 border-0 shadow-sm" style="border-left: 4px solid #2563eb !important;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1" style="font-size: 0.8rem;">Total Barang</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalItems) }}</h3>
                            </div>
                            <div style="font-size: 2rem; color: #2563eb; opacity: 0.8;"><i class="fa-solid fa-boxes-stacked"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-white text-dark h-100 border-0 shadow-sm" style="border-left: 4px solid #16a34a !important;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-1" style="font-size: 0.8rem;">Stok Fisik Keseluruhan</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalStock) }}</h3>
                            </div>
                            <div style="font-size: 2rem; color: #16a34a; opacity: 0.8;"><i class="fa-solid fa-cubes"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-black-50 mb-1">Peminjaman Aktif (Anda)</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($activeLoans) }}</h3>
                            </div>
                            <div style="font-size: 2rem; opacity: 0.2;"><i class="fa-solid fa-hand-holding-hand"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-danger text-white h-100 border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-1">Peminjaman Terlambat</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($overdueLoans) }}</h3>
                            </div>
                            <div style="font-size: 2rem; opacity: 0.3;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-handshake text-warning me-2"></i>Peminjaman Terbaru Anda</h6>
                    <a href="{{ route('loans.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentLoans as $loan)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $loan->item->name ?? 'Barang' }}</strong><br>
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
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Statistik Peminjaman Anda</h6>
                    <select class="form-select form-select-sm w-auto chart-filter" id="staffChartFilter">
                        <option value="hari">7 Hari Terakhir</option>
                        <option value="bulan" selected>6 Bulan Terakhir</option>
                        <option value="tahun">5 Tahun Terakhir</option>
                    </select>
                </div>
                <div class="card-body">
                    <div id="chartLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="loanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fa-regular fa-calendar text-primary me-2"></i>Kalender & Info</h6>
                </div>
                <div class="card-body text-center p-4">
                    <div class="mt-3 text-start">
                        @include('partials.calendar-widget')
                    </div>

                    <hr>
                    <div class="text-start mt-3">
                        <p class="mb-1 text-muted small fw-bold">AKTIVITAS MENDATANG</p>
                        @if($activeLoans > 0)
                            <div class="alert alert-info py-2 px-3 small border-0">Anda memiliki {{ $activeLoans }} peminjaman aktif.</div>
                        @else
                            <div class="text-muted small">Tidak ada aktivitas.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- ADMIN & SUPER ADMIN LAYOUT --}}
    <div class="row mb-4">
        @if($isSuperAdmin)
        <div class="col-lg col-md-4 col-6 mb-3 mb-lg-0">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1" style="font-size: 0.8rem;">Total Pengguna</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.3;"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-lg col-md-4 col-6 mb-3 mb-lg-0">
            <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1" style="font-size: 0.8rem;">Total Barang</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalItems) }}</h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.3;"><i class="fa-solid fa-boxes-stacked"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6 mb-3 mb-lg-0">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1" style="font-size: 0.8rem;">Total Stok Fisik</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalStock) }}</h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.3;"><i class="fa-solid fa-cubes"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-6 mb-3 mb-lg-0">
            <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-black-50 mb-1" style="font-size: 0.8rem;">Pinjam Aktif</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($activeLoans) }}</h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.2;"><i class="fa-solid fa-hand-holding-hand"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 col-12">
            <div class="card bg-danger text-white h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50 mb-1" style="font-size: 0.8rem;">Terlambat</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($overdueLoans) }}</h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.3;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Statistik Operasional</h6>
                    <select class="form-select form-select-sm w-auto chart-filter" id="adminChartFilter">
                        <option value="hari">7 Hari Terakhir</option>
                        <option value="bulan" selected>6 Bulan Terakhir</option>
                        <option value="tahun">5 Tahun Terakhir</option>
                    </select>
                </div>
                <div class="card-body position-relative">
                    <div id="chartLoading" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 10;">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="loanChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-handshake text-warning me-2"></i>Peminjaman Terbaru</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentLoans as $loan)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $loan->borrower->institution_name ?? $loan->user->name }}</strong><br>
                                        <small class="text-muted">{{ $loan->borrow_date->format('d M') }} - {{ $loan->due_date->format('d M Y') }}</small>
                                    </div>
                                    <span class="badge {{ $loan->status === 'active' ? 'bg-warning' : 'bg-success' }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">Belum ada peminjaman.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-arrow-right-arrow-left text-primary me-2"></i>Mutasi Stok Terbaru</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentMovements as $movement)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="text-truncate pe-2">
                                        <strong>{{ $movement->item->name }}</strong><br>
                                        <small class="text-muted">{{ $movement->created_at->format('d M H:i') }} oleh {{ $movement->user->name }}</small>
                                    </div>
                                    <span class="badge {{ $movement->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                    </span>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">Belum ada mutasi stok.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fa-regular fa-calendar text-primary me-2"></i>Tanggal & Aktivitas</h6>
                </div>
                <div class="card-body text-center p-4">
                    <div class="mt-3 text-start" id="admin-calendar-container">
                        @include('partials.calendar-widget')
                    </div>
                </div>
            </div>
            
            @if($lowStockItems->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Stok Menipis</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($lowStockItems as $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-truncate">{{ $item->name }}</span>
                            <span class="badge bg-danger rounded-pill">{{ $item->available_qty }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('loanChart').getContext('2d');
        let loanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Peminjaman',
                        data: {!! json_encode($chartLoans) !!},
                        backgroundColor: 'rgba(37, 99, 235, 0.8)', // blue-600
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengembalian',
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
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        const filterEl = document.querySelector('.chart-filter');
        const loadingEl = document.getElementById('chartLoading');
        
        if(filterEl) {
            filterEl.addEventListener('change', function(e) {
                const period = e.target.value;
                loadingEl.classList.remove('d-none');
                
                fetch(`{{ route('dashboard') }}?period=${period}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    loanChart.data.labels = data.labels;
                    loanChart.data.datasets[0].data = data.loans;
                    loanChart.data.datasets[1].data = data.returns;
                    loanChart.update();
                })
                .catch(err => {
                    alert('Gagal memuat statistik. Silakan coba lagi.');
                    console.error(err);
                })
                .finally(() => {
                    loadingEl.classList.add('d-none');
                });
            });
        }
    });

    window.loadCalendar = function(year, month) {
        document.querySelectorAll('#calendar-loading').forEach(el => el.classList.remove('d-none'));
        fetch(`{{ route('dashboard') }}?cal_year=${year}&cal_month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            document.querySelectorAll('.calendar-widget').forEach(el => {
                el.parentElement.innerHTML = html;
            });
            // Re-init tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        })
        .catch(err => {
            console.error('Error loading calendar:', err);
            document.querySelectorAll('#calendar-loading').forEach(el => el.classList.add('d-none'));
        });
    }
</script>
@endpush
