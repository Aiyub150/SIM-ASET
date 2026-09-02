@extends('layouts.app')

@section('title', 'Detail Peminjaman: ' . $loan->loan_code . ' — SIM-ASET')
@section('page-title', 'Detail Peminjaman')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">
            Peminjaman
            <span style="font-family:monospace; color:#2563eb; font-size:.95rem;">{{ $loan->loan_code }}</span>
        </h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Detail transaksi dan proses pengembalian barang</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('loans.print', $loan->id) }}" target="_blank" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak BAST
        </a>
        <a href="{{ route('loans.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    </div>
</div>

{{-- Info Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Data Peminjam</div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0" style="font-size:.875rem;">
                    <tr>
                        <td class="text-muted ps-4 py-2" style="width:38%;">Instansi</td>
                        <td class="fw-500 py-2" style="font-weight:500;">{{ $loan->borrower->institution_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-4 py-2">P. Jawab</td>
                        <td class="py-2">{{ $loan->borrower->pic_name }} · {{ $loan->borrower->contact_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-4 py-2 pb-3">Dicatat oleh</td>
                        <td class="py-2 pb-3">{{ $loan->user->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Status Transaksi</div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0" style="font-size:.875rem;">
                    <tr>
                        <td class="text-muted ps-4 py-2" style="width:38%;">Tgl Pinjam</td>
                        <td class="py-2">{{ $loan->borrow_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-4 py-2">Jatuh Tempo</td>
                        <td class="py-2 fw-bold">{{ $loan->due_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-4 py-2 pb-3">Status</td>
                        <td class="py-2 pb-3">
                            @if($loan->status === 'completed')
                                <span class="badge bg-success">Selesai · {{ $loan->return_date->format('d M Y') }}</span>
                            @elseif($loan->due_date->isPast())
                                <span class="badge bg-danger">Terlambat</span>
                            @else
                                <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Rincian Barang --}}
<div class="card mb-4">
    <div class="card-header">Rincian Barang yang Dipinjam</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">SKU</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Jml Pinjam</th>
                    <th class="text-center">Sudah Kembali</th>
                    <th class="text-center">Sisa Hutang</th>
                    <th class="text-center pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loan->loanItems as $detail)
                    @php $sisa = $detail->qty - $detail->returned_qty; @endphp
                    <tr>
                        <td class="ps-4">
                            <span style="font-family:monospace; font-size:.82rem; color:#64748b;">{{ $detail->item->sku }}</span>
                        </td>
                        <td style="font-weight:500;">{{ $detail->item->name }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-center text-muted">{{ $detail->returned_qty }}</td>
                        <td class="text-center fw-bold {{ $sisa > 0 ? 'text-danger' : 'text-success' }}">{{ $sisa }}</td>
                        <td class="text-center pe-4">
                            @if($sisa == 0)
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-danger">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Form Pengembalian --}}
@if($loan->status !== 'completed' && $pendingItems->count() > 0)
<div class="card" style="border-color:#2563eb;">
    <div class="card-header" style="background:#eff6ff; color:#1e40af; border-color:#2563eb;">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
        </svg>
        Proses Pengembalian Barang
    </div>
    <div class="card-body p-4">
        <form action="{{ route('loans.return', $loan->id) }}" method="POST">
            @csrf
            <div id="return-items-container">
                <div class="row g-2 align-items-end item-row mb-3" data-index="0">
                    <div class="col-md-7">
                        <label class="form-label">Pilih Barang yang Dikembalikan</label>
                        <select name="items[0][loan_item_id]" class="form-select loan-item-select" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($pendingItems as $pending)
                                <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                                    {{ $pending->item->name }} &nbsp;(Hutang: {{ $pending->qty - $pending->returned_qty }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah Kembali</label>
                        <input type="number" name="items[0][return_qty]" class="form-control return-qty-input" min="1" required>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                <button type="button" id="btn-add-return" class="btn btn-sm btn-outline-primary">
                    + Tambah Barang Lain
                </button>
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    Eksekusi Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Template tersembunyi --}}
<div id="return-template" class="d-none">
    <div class="row g-2 align-items-end item-row mb-3">
        <div class="col-md-7">
            <label class="form-label">Pilih Barang yang Dikembalikan</label>
            <select class="form-select loan-item-select" required>
                <option value="">— Pilih Barang —</option>
                @foreach($pendingItems as $pending)
                    <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                        {{ $pending->item->name }} &nbsp;(Hutang: {{ $pending->qty - $pending->returned_qty }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jumlah Kembali</label>
            <input type="number" class="form-control return-qty-input" min="1" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove w-100" style="padding:.45rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function attachMaxLogic(row) {
            const sel   = row.querySelector('.loan-item-select');
            const input = row.querySelector('.return-qty-input');
            if (!sel) return;
            sel.addEventListener('change', function () {
                const max = sel.options[sel.selectedIndex].getAttribute('data-max');
                if (max) { input.setAttribute('max', max); input.value = max; }
                else { input.removeAttribute('max'); input.value = ''; }
            });
        }

        const firstRow = document.querySelector('#return-items-container .item-row');
        if (firstRow) attachMaxLogic(firstRow);

        let idx = 1;
        const container = document.getElementById('return-items-container');
        const template  = document.getElementById('return-template')?.querySelector('.item-row');
        const btnAdd    = document.getElementById('btn-add-return');

        if (btnAdd && template) {
            btnAdd.addEventListener('click', function () {
                const newRow = template.cloneNode(true);
                newRow.querySelector('.loan-item-select').setAttribute('name', `items[${idx}][loan_item_id]`);
                newRow.querySelector('.return-qty-input').setAttribute('name', `items[${idx}][return_qty]`);
                newRow.querySelector('.btn-remove').addEventListener('click', () => newRow.remove());
                attachMaxLogic(newRow);
                container.appendChild(newRow);
                idx++;
            });
        }
    });
</script>
@endpush
