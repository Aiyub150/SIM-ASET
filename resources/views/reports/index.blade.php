@extends('layouts.app')

@section('title', 'Laporan Inventaris - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Laporan Rekapitulasi Bulanan</h3>
</div>

<!-- Panel Filter Laporan -->
<div class="card mb-4 shadow-sm">
    <div class="card-body bg-light d-flex justify-content-between align-items-center">
        <form action="{{ route('reports.index') }}" method="GET" class="d-flex align-items-center m-0">
            <label class="fw-bold me-2">Bulan:</label>
            <select name="month" class="form-select form-select-sm me-3" style="width: auto;">
                @for($i = 1; $i <= 12; $i++)
                    @php $val = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>

            <label class="fw-bold me-2">Tahun:</label>
            <select name="year" class="form-select form-select-sm me-3" style="width: auto;">
                @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="btn btn-sm btn-primary">Tampilkan Data</button>
        </form>

        <a href="{{ route('reports.export-pdf', ['month' => $month, 'year' => $year]) }}" target="_blank" class="btn btn-danger fw-bold">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16" style="margin-top:-3px;">
                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .471.236c.09.112.145.256.145.41 0 .23-.05.474-.15.727-.2.505-.5.986-.816 1.402a10.85 10.85 0 0 1 1.77 1.34 8.794 8.794 0 0 1 2.296.883c.311.191.564.444.69.75.122.295.12.593-.01.884-.13.284-.363.504-.633.63a.85.85 0 0 1-.448.067c-.23-.028-.507-.156-.816-.382a3.84 3.84 0 0 1-1.24-1.182 11.232 11.232 0 0 1-3.238 1.064 9.176 9.176 0 0 1-1.636 1.13c-.302.164-.627.31-.963.388a.822.822 0 0 1-.433-.03z"/>
            </svg>
            Cetak PDF
        </a>
    </div>
</div>

<!-- Grid Laporan -->
<div class="row">
    <!-- Tabel Peminjaman Baru -->
    <div class="col-md-12 mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Pratinjau Peminjaman Aset Baru</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm m-0">
                <thead class="table-light">
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="20%">Kode Transaksi</th>
                        <th>Instansi Peminjam</th>
                        <th width="15%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                            <td>{{ $loan->loan_code }}</td>
                            <td>{{ $loan->borrower->institution_name }}</td>
                            <td class="text-center">{{ strtoupper($loan->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada transaksi peminjaman di bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Mutasi Fisik -->
    <div class="col-md-12">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Pratinjau Mutasi Stok Fisik</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm m-0">
                <thead class="table-light">
                    <tr>
                        <th width="15%">Tanggal Eksekusi</th>
                        <th width="25%">Ref/Surat</th>
                        <th>Barang Terkait</th>
                        <th width="15%" class="text-center">Tipe</th>
                        <th width="10%" class="text-center">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mov)
                        <tr>
                            <td>{{ $mov->created_at->format('d/m/Y') }}</td>
                            <td>{{ $mov->reference_code }}</td>
                            <td>{{ $mov->item->name }}</td>
                            <td class="text-center">{{ strtoupper($mov->type) }}</td>
                            <td class="text-center">{{ $mov->qty }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada pergerakan stok di bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection