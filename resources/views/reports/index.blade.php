@extends('layouts.app')

@section('title', 'Laporan Inventaris — SIM-ASET')
@section('page-title', 'Laporan Rekapitulasi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Laporan Rekapitulasi Bulanan</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Pratinjau dan ekspor laporan inventaris per periode</p>
    </div>
    <a href="{{ route('reports.export-pdf', ['month' => $month, 'year' => $year]) }}"
       target="_blank"
       class="btn btn-outline-danger d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
            <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .471.236c.09.112.145.256.145.41 0 .23-.05.474-.15.727-.2.505-.5.986-.816 1.402a10.85 10.85 0 0 1 1.77 1.34 8.794 8.794 0 0 1 2.296.883c.311.191.564.444.69.75.122.295.12.593-.01.884-.13.284-.363.504-.633.63a.85.85 0 0 1-.448.067c-.23-.028-.507-.156-.816-.382z"/>
        </svg>
        Ekspor PDF
    </a>
</div>

{{-- Filter Panel --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('reports.index') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <span class="fw-500 text-muted" style="font-weight:500; font-size:.85rem;">Filter Periode:</span>

            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0" style="font-size:.85rem; font-weight:500;">Bulan</label>
                <select name="month" class="form-select form-select-sm" style="width:auto; min-width:110px;">
                    @for($i = 1; $i <= 12; $i++)
                        @php $val = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0" style="font-size:.85rem; font-weight:500;">Tahun</label>
                <select name="year" class="form-select form-select-sm" style="width:auto; min-width:85px;">
                    @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn btn-sm btn-primary px-3">Tampilkan</button>
        </form>
    </div>
</div>

{{-- Tabel Peminjaman --}}
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width:3px; height:18px; background:#2563eb; border-radius:2px;"></div>
        <h6 class="fw-bold mb-0">Transaksi Peminjaman Aset</h6>
        <span class="badge bg-primary ms-1">{{ $loans->count() }}</span>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:12%;">Tanggal</th>
                        <th style="width:22%;">Kode Transaksi</th>
                        <th>Instansi Peminjam</th>
                        <th style="width:15%;">Jatuh Tempo</th>
                        <th class="text-center pe-4" style="width:12%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td class="ps-4" style="font-size:.85rem;">{{ $loan->borrow_date->format('d/m/Y') }}</td>
                            <td>
                                <span style="font-family:monospace; font-size:.82rem; font-weight:600; color:#2563eb;">
                                    {{ $loan->loan_code }}
                                </span>
                            </td>
                            <td style="font-weight:500;">{{ $loan->borrower->institution_name }}</td>
                            <td style="font-size:.85rem;">{{ $loan->due_date->format('d/m/Y') }}</td>
                            <td class="text-center pe-4">
                                @if($loan->status === 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($loan->status === 'active')
                                    <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ strtoupper($loan->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted" style="font-size:.85rem;">
                                Tidak ada transaksi peminjaman di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tabel Mutasi Stok --}}
<div class="mb-2">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div style="width:3px; height:18px; background:#10b981; border-radius:2px;"></div>
        <h6 class="fw-bold mb-0">Mutasi Stok Fisik</h6>
        <span class="badge bg-success ms-1">{{ $movements->count() }}</span>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:12%;">Tanggal</th>
                        <th style="width:25%;">No. Referensi</th>
                        <th>Barang Terkait</th>
                        <th class="text-center" style="width:12%;">Tipe</th>
                        <th class="text-center pe-4" style="width:10%;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mov)
                        <tr>
                            <td class="ps-4" style="font-size:.85rem;">{{ $mov->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span style="font-family:monospace; font-size:.82rem; font-weight:600;">
                                    {{ $mov->reference_code }}
                                </span>
                            </td>
                            <td style="font-weight:500;">{{ $mov->item->name }}</td>
                            <td class="text-center">
                                @if($mov->type === 'in')
                                    <span class="badge bg-success">Masuk</span>
                                @elseif($mov->type === 'out')
                                    <span class="badge bg-secondary">Keluar</span>
                                @elseif($mov->type === 'broken')
                                    <span class="badge bg-danger">Rusak</span>
                                @else
                                    <span class="badge" style="background:#1e293b; color:#fff;">Hilang</span>
                                @endif
                            </td>
                            <td class="text-center pe-4 fw-bold">{{ $mov->qty }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted" style="font-size:.85rem;">
                                Tidak ada pergerakan stok di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
