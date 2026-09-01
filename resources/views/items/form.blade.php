@extends('layouts.app')

@section('title', isset($item) ? 'Edit Barang' : 'Tambah Barang Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">{{ isset($item) ? 'Edit Master Barang' : 'Tambah Master Barang Baru' }}</h3>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary fw-bold">← Batal</a>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                Identitas Barang
            </div>
            <div class="card-body p-4">
                @if(!isset($item))
                    <div class="alert alert-info mb-4">
                        <small>💡 Info: Stok barang baru secara otomatis akan diset ke 0. Untuk menambahkan stok, silakan ke menu <strong>Kartu Stok -> Mutasi Baru</strong> setelah barang ini disimpan.</small>
                    </div>
                @endif

                <form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" method="POST">
                    @csrf
                    @if(isset($item)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Barang (SKU)</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $item->sku ?? '') }}" placeholder="Contoh: TNDA-001" required>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name ?? '') }}" placeholder="Contoh: Tenda Pramuka Kapasitas 10 Orang" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @role('Super Admin')
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Jumlah (Qty)</label>
                            <input type="number" name="total_qty" class="form-control @error('total_qty') is-invalid @enderror" value="{{ old('total_qty', $item->total_qty ?? '') }}" min="0" step="1">
                            <div class="form-text">
                                Jika diisi untuk barang baru, stok awal akan langsung diset sesuai nilai ini. Untuk edit, perubahan akan menyesuaikan stok tersedia secara proporsional.
                            </div>
                            @error('total_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endrole

                    <button type="submit" class="btn btn-{{ isset($item) ? 'warning' : 'primary' }} w-100 fw-bold py-2">
                        {{ isset($item) ? 'Simpan Perubahan' : 'Simpan Barang Baru' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection