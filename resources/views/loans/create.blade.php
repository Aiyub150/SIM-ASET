@extends('layouts.app')

@section('title', 'Buat Peminjaman Baru - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Buat Transaksi Peminjaman Baru</h3>
    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form action="{{ route('loans.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Kolom Kiri: Data Header Peminjaman -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white fw-bold">
                    Informasi Peminjam
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Peminjam (Instansi)</label>
                        <select name="borrower_id" class="form-select @error('borrower_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Peminjam --</option>
                            @foreach($borrowers as $borrower)
                                <option value="{{ $borrower->id }}" {{ old('borrower_id') == $borrower->id ? 'selected' : '' }}>
                                    {{ $borrower->institution_name }} ({{ $borrower->pic_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('borrower_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pinjam</label>
                        <input type="date" name="borrow_date" class="form-control @error('borrow_date') is-invalid @enderror" value="{{ old('borrow_date', date('Y-m-d')) }}" required>
                        @error('borrow_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Pengembalian</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Peminjaman</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Keranjang Barang -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Daftar Barang yang Dipinjam</span>
                    <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">+ Tambah Barang</button>
                </div>
                <div class="card-body">
                    @error('items') <div class="alert alert-danger p-2">{{ $message }}</div> @enderror
                    
                    <div id="items-container">
                        <!-- Baris Pertama -->
                        <div class="row align-items-end item-row mb-3" data-index="0">
                            <div class="col-md-8">
                                <label class="form-label">Pilih Barang</label>
                                <select name="items[0][item_id]" class="form-select @error('items.0.item_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }} (Sisa Stok: {{ $item->available_qty }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.0.item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="items[0][qty]" class="form-control @error('items.0.qty') is-invalid @enderror" min="1" required>
                                @error('items.0.qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-1">
                                <!-- Kosong untuk baris pertama agar sejajar -->
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Proses Peminjaman Aset</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template JS Sembunyi -->
<div id="item-template" class="d-none">
    <div class="row align-items-end item-row mb-3">
        <div class="col-md-8">
            <label class="form-label">Pilih Barang</label>
            <select class="form-select item-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} (Sisa Stok: {{ $item->available_qty }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jumlah</label>
            <input type="number" class="form-control qty-input" min="1" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-remove w-100">X</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;
        const container = document.getElementById('items-container');
        const template = document.getElementById('item-template').querySelector('.item-row');

        document.getElementById('btn-add-item').addEventListener('click', function () {
            const newRow = template.cloneNode(true);
            newRow.querySelector('.item-select').setAttribute('name', `items[${itemIndex}][item_id]`);
            newRow.querySelector('.qty-input').setAttribute('name', `items[${itemIndex}][qty]`);
            
            newRow.querySelector('.btn-remove').addEventListener('click', function () {
                newRow.remove();
            });

            container.appendChild(newRow);
            itemIndex++;
        });
    });
</script>
@endpush