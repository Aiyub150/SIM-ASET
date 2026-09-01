@extends('layouts.app')

@section('title', 'Input Mutasi Stok - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Input Mutasi Stok Permanen</h3>
    <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark fw-bold">
                ⚠️ Perhatian: Data yang disimpan tidak dapat dihapus atau diedit.
            </div>
            <div class="card-body p-4">
                <form action="{{ route('stocks.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Referensi (Surat/BAST/Faktur)</label>
                        <input type="text" name="reference_code" class="form-control @error('reference_code') is-invalid @enderror" value="{{ old('reference_code') }}" placeholder="Contoh: BAST/2026/08/001" required>
                        @error('reference_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Barang</label>
                        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->sku }} - {{ $item->name }} (Fisik: {{ $item->total_qty }}, Tersedia: {{ $item->available_qty }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tipe Mutasi</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk / Pengadaan (+)</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar / Hibah (-)</option>
                                <option value="broken" {{ old('type') == 'broken' ? 'selected' : '' }}>Barang Rusak Total (-)</option>
                                <option value="lost" {{ old('type') == 'lost' ? 'selected' : '' }}>Barang Hilang (-)</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah (Qty)</label>
                            <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" min="1" value="{{ old('qty') }}" required>
                            @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Simpan Mutasi Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection