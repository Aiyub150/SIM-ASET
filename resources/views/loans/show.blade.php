@extends('layouts.app')

@section('title', 'Detail Peminjaman: ' . $loan->loan_code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Detail Peminjaman: <span class="text-primary">{{ $loan->loan_code }}</span></h3>
    <div>
        <a href="{{ route('loans.print', $loan->id) }}" target="_blank" class="btn btn-danger me-2 fw-bold">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-1" viewBox="0 0 16 16" style="margin-top:-3px;">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak BAST PDF
        </a>
        <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary fw-bold">← Kembali</a>
    </div>
</div>

<div class="row mb-4">
    <!-- Informasi Peminjam -->
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white fw-bold border-bottom">Data Peminjam</div>
            <div class="card-body p-0">
                <table class="table table-borderless m-0">
                    <tr><th width="35%" class="text-muted ps-3 pt-3">Instansi</th><td class="pt-3">{{ $loan->borrower->institution_name }}</td></tr>
                    <tr><th class="text-muted ps-3">P. Jawab</th><td>{{ $loan->borrower->pic_name }} ({{ $loan->borrower->contact_number }})</td></tr>
                    <tr><th class="text-muted ps-3 pb-3">Admin Pencatat</th><td class="pb-3">{{ $loan->user->name }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Informasi Waktu & Status -->
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white fw-bold border-bottom">Status Transaksi</div>
            <div class="card-body p-0">
                <table class="table table-borderless m-0">
                    <tr><th width="35%" class="text-muted ps-3 pt-3">Tgl Pinjam</th><td class="pt-3">{{ $loan->borrow_date->format('d M Y') }}</td></tr>
                    <tr><th class="text-muted ps-3">Jatuh Tempo</th><td class="fw-bold text-dark">{{ $loan->due_date->format('d M Y') }}</td></tr>
                    <tr><th class="text-muted ps-3 pb-3">Status</th>
                        <td class="pb-3">
                            @if($loan->status === 'completed')
                                <span class="badge bg-success px-3 py-2">Selesai ({{ $loan->return_date->format('d M Y') }})</span>
                            @elseif($loan->due_date->isPast())
                                <span class="badge bg-danger px-3 py-2">Terlambat</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2">Aktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white fw-bold border-bottom py-3">Rincian Barang yang Dipinjam</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Kode Barang (SKU)</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Jml Pinjam</th>
                        <th class="text-center">Sudah Kembali</th>
                        <th class="text-center">Sisa Hutang</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loan->loanItems as $detail)
                        @php $sisa = $detail->qty - $detail->returned_qty; @endphp
                        <tr>
                            <td class="ps-3">{{ $detail->item->sku }}</td>
                            <td>{{ $detail->item->name }}</td>
                            <td class="text-center">{{ $detail->qty }}</td>
                            <td class="text-center">{{ $detail->returned_qty }}</td>
                            <td class="text-center fw-bold {{ $sisa > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $sisa }}
                            </td>
                            <td class="text-center pe-3">
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
</div>

<!-- Tampilkan Form Pengembalian HANYA JIKA masih ada sisa hutang -->
@if($loan->status !== 'completed' && $pendingItems->count() > 0)
<div class="card border-primary mb-5 shadow-sm">
    <div class="card-header bg-primary text-white fw-bold py-3">
        Proses Pengembalian Barang
    </div>
    <div class="card-body bg-light p-4">
        <form action="{{ route('loans.return', $loan->id) }}" method="POST">
            @csrf
            <div id="return-items-container">
                <div class="row align-items-end item-row mb-3" data-index="0">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Pilih Barang</label>
                        <select name="items[0][loan_item_id]" class="form-select loan-item-select" required>
                            <option value="">-- Pilih Barang yang Dikembalikan --</option>
                            @foreach($pendingItems as $pending)
                                <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                                    {{ $pending->item->name }} (Hutang: {{ $pending->qty - $pending->returned_qty }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jml Kembali</label>
                        <input type="number" name="items[0][return_qty]" class="form-control return-qty-input" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <!-- Space kosong -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" id="btn-add-return" class="btn btn-outline-primary fw-bold">+ Tambah Barang Lain</button>
                <button type="submit" class="btn btn-primary fw-bold px-4">Eksekusi Pengembalian</button>
            </div>
        </form>
    </div>
</div>

<!-- Template JS Sembunyi -->
<div id="return-template" class="d-none">
    <div class="row align-items-end item-row mb-3">
        <div class="col-md-7">
            <label class="form-label fw-semibold">Pilih Barang</label>
            <select class="form-select loan-item-select" required>
                <option value="">-- Pilih Barang yang Dikembalikan --</option>
                @foreach($pendingItems as $pending)
                    <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                        {{ $pending->item->name }} (Hutang: {{ $pending->qty - $pending->returned_qty }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Jml Kembali</label>
            <input type="number" class="form-control return-qty-input" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove w-100 fw-bold">Batal</button>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function attachMaxLogic(row) {
            const select = row.querySelector('.loan-item-select');
            const input = row.querySelector('.return-qty-input');
            
            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const maxQty = selectedOption.getAttribute('data-max');
                if(maxQty) {
                    input.setAttribute('max', maxQty);
                    input.value = maxQty; 
                } else {
                    input.removeAttribute('max');
                    input.value = '';
                }
            });
        }

        const firstRow = document.querySelector('#return-items-container .item-row');
        if(firstRow) attachMaxLogic(firstRow);

        let index = 1;
        const container = document.getElementById('return-items-container');
        const template = document.getElementById('return-template').querySelector('.item-row');
        const btnAdd = document.getElementById('btn-add-return');

        if(btnAdd) {
            btnAdd.addEventListener('click', function () {
                const newRow = template.cloneNode(true);
                newRow.querySelector('.loan-item-select').setAttribute('name', `items[${index}][loan_item_id]`);
                newRow.querySelector('.return-qty-input').setAttribute('name', `items[${index}][return_qty]`);
                
                newRow.querySelector('.btn-remove').addEventListener('click', function () {
                    newRow.remove();
                });

                attachMaxLogic(newRow);
                container.appendChild(newRow);
                index++;
            });
        }
    });
</script>
@endpush