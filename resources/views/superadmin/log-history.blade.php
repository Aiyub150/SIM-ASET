@extends('layouts.app')

@section('title', 'Log History (Audit Trail) — SIM-ASET')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1" style="font-size: 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock-history me-2 text-primary" viewBox="0 0 16 16" style="vertical-align: -3px;">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.376.862c.324.23.633.486.924.764l.698-.716a8.01 8.01 0 0 0-1.07-1.025l-.552.977zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                </svg>
                Log History Aktivitas (Audit Trail)
            </h1>
            <p class="text-muted small mb-0">
                Catatan kronologis seluruh interaksi pengguna, modifikasi data, dan transaksi operasional sistem secara transparan
            </p>
        </div>
        <div>
            <span class="badge bg-light text-dark border p-2 font-monospace">
                IP Anda: <strong class="text-success">{{ $currentUserIp }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('superadmin.log-history') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Cari keterangan, user, atau IP...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="module" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" placeholder="Dari Tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" placeholder="Sampai Tanggal">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">Filter</button>
                    @if(request()->anyFilled(['q', 'module', 'action', 'start_date', 'end_date']))
                        <a href="{{ route('superadmin.log-history') }}" class="btn btn-light btn-sm text-secondary" title="Reset">✕</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- History Table Container -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">Daftar Audit Trail & Riwayat Operasi</h6>
            <span class="badge bg-primary rounded-pill px-3 py-2 font-monospace" style="font-size: 0.75rem;">
                Total: {{ $logs->total() }} Catatan
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4" style="width: 160px;">Waktu & Tanggal</th>
                        <th style="width: 220px;">Pengguna</th>
                        <th style="width: 120px;">Modul</th>
                        <th style="width: 130px;">Aksi</th>
                        <th>Keterangan Lengkap</th>
                        <th style="width: 140px;">IP Pengguna</th>
                        <th class="text-center" style="width: 80px;">Rincian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $actionBadge = match($log->action) {
                                'CREATE', 'LOAN_CREATE' => 'bg-success text-white',
                                'UPDATE', 'STOCK_ADJUST' => 'bg-info text-white',
                                'DELETE' => 'bg-danger text-white',
                                'LOGIN' => 'bg-primary text-white',
                                'LOGOUT' => 'bg-secondary text-white',
                                'LOAN_RETURN' => 'bg-warning text-dark',
                                default => 'bg-light text-dark border',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace text-muted" style="font-size: 0.78rem;">
                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="small fw-semibold text-dark">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($log->user && $log->user->avatar)
                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" 
                                             alt="{{ $log->user_name }}" 
                                             class="rounded-circle shadow-sm"
                                             style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                    @else
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #2563eb; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .75rem; font-weight: 700; flex-shrink: 0;">
                                            {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="text-truncate" style="max-width: 160px;">
                                        <div class="fw-semibold text-dark">{{ $log->user_name }}</div>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">{{ $log->user_role }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $actionBadge }} px-2 py-1 rounded" style="font-size: 0.7rem; font-weight: 600;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark fw-normal" style="font-size: 0.82rem; line-height: 1.4;">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.78rem; font-weight: 500;">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(!empty($log->properties))
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm py-0 px-2" 
                                            style="font-size: 0.75rem;"
                                            onclick='showPayloadDetails(@json($log->properties), "{{ addslashes($log->description) }}")'>
                                        Detail
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-calendar-x text-muted opacity-50 mb-2" viewBox="0 0 16 16">
                                    <path d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z"/>
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                <div>Belum ada riwayat aktivitas yang tercatat sesuai kriteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function showPayloadDetails(properties, description) {
    const formattedJson = JSON.stringify(properties, null, 2);
    Swal.fire({
        title: 'Detail Keterangan & Payload',
        html: `
            <div class="text-start mb-2 small text-secondary"><strong>Aktivitas:</strong> ${description}</div>
            <pre class="text-start p-3 bg-light border rounded font-monospace" style="max-height: 250px; overflow-y: auto; font-size: 0.8rem; color: #1e293b;">${formattedJson}</pre>
        `,
        icon: 'info',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#2563eb',
        customClass: {
            container: 'rounded-4'
        }
    });
}
</script>
@endpush
@endsection
