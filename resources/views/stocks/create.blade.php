@extends('layouts.app')

@section('title', 'Input Mutasi Stok — SIM-ASET')
@section('page-title', 'Input Mutasi Stok')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Input Mutasi Stok Permanen</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Catat penambahan atau pengurangan stok fisik aset</p>
    </div>
    <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">

        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="flex-shrink-0 mt-1" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <div>
                <strong>Perhatian:</strong> Data mutasi stok yang tersimpan <strong>tidak dapat diedit atau dihapus</strong>.
                Pastikan semua data sudah benar sebelum menekan tombol simpan.
            </div>
        </div>

        <div class="card">
            <div class="card-header">Formulir Mutasi Stok</div>
            <div class="card-body p-4">
                <form action="{{ route('stocks.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Nomor Referensi
                            <span class="badge ms-1" style="background:#eff6ff; color:#2563eb; font-size:.7rem; font-weight:500;">
                                Auto-generate
                            </span>
                        </label>
                        <div class="input-group">
                            <input type="text" name="reference_code"
                                   id="reference_code"
                                   class="form-control @error('reference_code') is-invalid @enderror"
                                   value="{{ old('reference_code', $suggestedCode) }}"
                                   required
                                   style="font-family:monospace;">
                            <button type="button" class="btn btn-outline-secondary" id="btn-reset-ref" title="Reset ke kode otomatis">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Dibuat otomatis dengan format <code>BAST/YYYY/MM/NNN</code>.
                            Anda dapat mengedit jika nomor surat berbeda.
                        </div>
                        @error('reference_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Barang</label>
                        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    [{{ $item->sku }}] {{ $item->name }}
                                    &nbsp;— Fisik: {{ $item->total_qty }}, Tersedia: {{ $item->available_qty }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Tipe Mutasi</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">— Pilih Tipe —</option>
                                <option value="in"     {{ old('type') == 'in'     ? 'selected' : '' }}>➕ Barang Masuk / Pengadaan</option>
                                <option value="out"    {{ old('type') == 'out'    ? 'selected' : '' }}>➖ Barang Keluar / Hibah</option>
                                <option value="broken" {{ old('type') == 'broken' ? 'selected' : '' }}>🔴 Barang Rusak Total</option>
                                <option value="lost"   {{ old('type') == 'lost'   ? 'selected' : '' }}>⚫ Barang Hilang</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jumlah (Qty)</label>
                            <input type="number" name="qty"
                                   class="form-control @error('qty') is-invalid @enderror"
                                   min="1" value="{{ old('qty') }}" required placeholder="0">
                            @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Catatan <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
                        <textarea name="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="Keterangan tambahan tentang mutasi ini…">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        Simpan Mutasi Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const suggestedCode = @json($suggestedCode);
    const input  = document.getElementById('reference_code');
    const btnReset = document.getElementById('btn-reset-ref');
    if (btnReset && input) {
        btnReset.addEventListener('click', function () {
            input.value = suggestedCode;
            input.focus();
        });
    }
})();
</script>
@endpush
