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
                <form action="{{ route('stocks.store') }}" 
                      method="POST"
                      data-confirm="Apakah Anda yakin ingin memproses transaksi mutasi stok ini?"
                      data-confirm-title="Simpan Mutasi Stok?">
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
                        <select name="item_id" id="stock_item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">— Pilih atau cari barang… —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-sku="{{ $item->sku }}"
                                        data-category="{{ $item->category->name ?? 'Tanpa Kategori' }}"
                                        data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}"
                                        data-available="{{ $item->available_qty }}"
                                        data-total="{{ $item->total_qty }}"
                                        {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    [{{ $item->sku }}] {{ $item->name }} — Fisik: {{ $item->total_qty }}, Tersedia: {{ $item->available_qty }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Tipe Mutasi</label>
                            <select name="type" id="stock_mutation_type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">— Pilih Tipe Mutasi —</option>
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
    const stockItemSelect = document.getElementById('stock_item_id');

    if (btnReset && input) {
        btnReset.addEventListener('click', function () {
            input.value = suggestedCode;
            input.focus();
        });
    }

    if (stockItemSelect) {
        new TomSelect(stockItemSelect, {
            create: false,
            placeholder: '— Pilih atau cari barang… —',
            render: {
                option: function(data, escape) {
                    const opt = data.$option;
                    if (!data.value || !opt) {
                        return `<div class="p-2 text-muted">${escape(data.text)}</div>`;
                    }
                    const name = opt.getAttribute('data-name') || data.text;
                    const sku = opt.getAttribute('data-sku') || '';
                    const category = opt.getAttribute('data-category') || '-';
                    const imgUrl = opt.getAttribute('data-image');
                    const available = opt.getAttribute('data-available') || '0';
                    const total = opt.getAttribute('data-total') || '0';

                    const imgHtml = imgUrl 
                        ? `<img src="${escape(imgUrl)}" alt="${escape(name)}" class="rounded-3 border shadow-sm" style="width: 44px; height: 44px; object-fit: cover; flex-shrink: 0;">`
                        : `<div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px; flex-shrink: 0;"><i class="fa-solid fa-box text-secondary" style="font-size: 1.2rem;"></i></div>`;

                    return `<div class="d-flex align-items-center justify-content-between p-2 border-bottom border-light">
                        <div class="d-flex align-items-center gap-3 text-truncate me-2">
                            ${imgHtml}
                            <div class="text-truncate">
                                <div class="d-flex align-items-center gap-1 text-truncate">
                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">${escape(name)}</span>
                                    <span class="badge bg-light text-dark border ms-1" style="font-size: 0.72rem; font-family: monospace;">${escape(sku)}</span>
                                </div>
                                <div class="text-muted small mt-1 text-truncate" style="font-size: 0.75rem;">
                                    Kategori: <span class="text-dark">${escape(category)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-success-subtle text-success border d-block mb-1" style="font-size: 0.72rem;">
                                Tersedia: ${escape(available)}
                            </span>
                            <small class="text-muted" style="font-size: 0.68rem;">Fisik: ${escape(total)}</small>
                        </div>
                    </div>`;
                },
                item: function(data, escape) {
                    const opt = data.$option;
                    if (!data.value || !opt) {
                        return `<div>${escape(data.text)}</div>`;
                    }
                    const name = opt.getAttribute('data-name') || data.text;
                    const sku = opt.getAttribute('data-sku') || '';
                    const imgUrl = opt.getAttribute('data-image');
                    const available = opt.getAttribute('data-available') || '0';

                    const imgHtml = imgUrl 
                        ? `<img src="${escape(imgUrl)}" alt="${escape(name)}" class="rounded me-2 border" style="width: 22px; height: 22px; object-fit: cover; flex-shrink: 0;">`
                        : `<i class="fa-solid fa-box text-muted me-2" style="font-size: 0.85rem;"></i>`;

                    return `<div class="d-flex align-items-center justify-content-between w-100 gap-2">
                        <div class="d-flex align-items-center text-truncate">
                            ${imgHtml}
                            <span class="fw-bold text-dark text-truncate">${escape(name)}</span>
                            <span class="badge bg-light text-dark border ms-2" style="font-size: 0.7rem; font-family: monospace;">${escape(sku)}</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border ms-2" style="font-size: 0.72rem; flex-shrink: 0;">
                            Tersedia: ${escape(available)}
                        </span>
                    </div>`;
                },
                no_results: function(data, escape) {
                    return '<div class="no-results" style="padding: 10px; color: #6c757d;">Barang tidak ditemukan.</div>';
                }
            }
        });
    }

    const mutationTypeSelect = document.getElementById('stock_mutation_type');
    if (mutationTypeSelect) {
        new TomSelect(mutationTypeSelect, {
            create: false,
            controlInput: null,
            placeholder: '— Pilih Tipe Mutasi —'
        });
    }
})();
</script>
@endpush
