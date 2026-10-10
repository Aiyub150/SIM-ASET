@extends('layouts.app')

@section('title', 'Log Server — SIM-ASET')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1" style="font-size: 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-terminal me-2 text-primary" viewBox="0 0 16 16" style="vertical-align: -3px;">
                    <path d="M6 9a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 6 9zM3.854 4.146a.5.5 0 0 1 0 .708L1.707 7l2.147 2.146a.5.5 0 0 1-.708.708l-2.5-2.5a.5.5 0 0 1 0-.708l2.5-2.5a.5.5 0 0 1 .708 0z"/>
                    <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13zM1.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-13a.5.5 0 0 0-.5-.5h-13z"/>
                </svg>
                Log Server & Aplikasi
            </h1>
            <p class="text-muted small mb-0">
                Monitoring berkas log server (<code class="text-primary">storage/logs/laravel.log</code>) dengan pelacakan IP asli pengguna
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('superadmin.log-server.download') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Unduh Log
            </a>
            <form id="clearLogForm" method="POST" action="{{ route('superadmin.log-server.clear') }}" class="m-0">
                @csrf
                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="confirmClearLog()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    Bersihkan Log
                </button>
            </form>
        </div>
    </div>

    <!-- Info Banner Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-3" style="background: #ffffff; border-left: 4px solid #2563eb !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Ukuran File Log</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $fileSize }} KB</div>
                    </div>
                    <div class="rounded-circle bg-light p-2 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-3" style="background: #ffffff; border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">IP Asli Anda (Klien Saat Ini)</div>
                        <div class="fs-5 fw-bold text-success mt-1 font-monospace">{{ $currentUserIp }}</div>
                    </div>
                    <div class="rounded-circle bg-light p-2 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-3" style="background: #ffffff; border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Jumlah Entri Ditampilkan</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ count($logs) }} Baris</div>
                    </div>
                    <div class="rounded-circle bg-light p-2 text-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('superadmin.log-server') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari pesan log atau keyword...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="ALL" {{ $filterLevel === 'ALL' ? 'selected' : '' }}>Semua Level Log</option>
                        <option value="ERROR" {{ $filterLevel === 'ERROR' ? 'selected' : '' }}>ERROR</option>
                        <option value="WARNING" {{ $filterLevel === 'WARNING' ? 'selected' : '' }}>WARNING</option>
                        <option value="INFO" {{ $filterLevel === 'INFO' ? 'selected' : '' }}>INFO</option>
                        <option value="DEBUG" {{ $filterLevel === 'DEBUG' ? 'selected' : '' }}>DEBUG</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">Filter</button>
                    @if(!empty($search) || $filterLevel !== 'ALL')
                        <a href="{{ route('superadmin.log-server') }}" class="btn btn-light btn-sm text-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Log Table Container -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">Daftar Aktivitas Log Server</h6>
            <span class="badge bg-secondary font-monospace" style="font-size: 0.75rem;">Terurut dari Terbaru</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3" style="width: 170px;">Waktu</th>
                        <th style="width: 90px;">Level</th>
                        <th style="width: 140px;">IP Pengguna</th>
                        <th>Keterangan / Pesan Log</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $badgeClass = match($log['level']) {
                                'ERROR'   => 'bg-danger text-white',
                                'WARNING' => 'bg-warning text-dark',
                                'INFO'    => 'bg-info text-white',
                                'DEBUG'   => 'bg-secondary text-white',
                                default   => 'bg-light text-dark border',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 font-monospace text-muted" style="font-size: 0.78rem;">
                                {{ $log['timestamp'] }}
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} px-2 py-1 rounded" style="font-size: 0.7rem; font-weight: 600;">
                                    {{ $log['level'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.78rem; font-weight: 500;">
                                    {{ $log['ip'] }}
                                </span>
                            </td>
                            <td>
                                <div class="text-break font-monospace" style="white-space: pre-wrap; font-size: 0.8rem; color: #334155;">
                                    {{ Str::limit($log['message'], 400) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-inbox text-muted opacity-50 mb-2" viewBox="0 0 16 16">
                                    <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm-1.17-.437A1.5 1.5 0 0 1 4.98 3h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a1 1 0 0 1 .11.812l-1.5 6A1 1 0 0 1 13.52 16H2.48a1 1 0 0 1-.97-.812l-1.5-6a1 1 0 0 1 .11-.812l3.7-4.625zM1.125 9v4.5A.5.5 0 0 0 1.625 14h12.75a.5.5 0 0 0 .5-.5V9H10.5a2.5 2.5 0 0 1-5 0H1.125z"/>
                                </svg>
                                <div>Tidak ada catatan log server yang sesuai dengan filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmClearLog() {
    Swal.fire({
        title: 'Kosongkan Log Server?',
        text: "Semua riwayat catatan pada laravel.log akan dibersihkan secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Bersihkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('clearLogForm').submit();
        }
    });
}
</script>
@endpush
@endsection
