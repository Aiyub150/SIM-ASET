@extends('layouts.app')

@section('title', isset($item) ? 'Edit Barang — SIM-ASET' : 'Tambah Barang — SIM-ASET')
@section('page-title', isset($item) ? 'Edit Master Barang' : 'Tambah Master Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ isset($item) ? 'Edit Master Barang' : 'Tambah Barang Baru' }}</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ isset($item) ? 'Perbarui identitas barang yang ada' : 'Daftarkan aset baru ke dalam sistem' }}
        </p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-7 mx-auto">
        <div class="card">
            <div class="card-header">{{ isset($item) ? 'Identitas Barang' : 'Data Barang Baru' }}</div>
            <div class="card-body p-4">

                @if(!isset($item))
                    <div class="alert alert-info mb-4" style="font-size:.84rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <strong>Kode SKU</strong> akan di-generate otomatis berdasarkan kategori yang dipilih. Masukkan <strong>Jumlah Barang</strong> (minimal 1) untuk mendaftarkan aset.
                    </div>
                @endif

                <form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      data-confirm="{{ isset($item) ? 'Simpan seluruh pembaruan data barang ini ke dalam sistem?' : 'Tambahkan data barang baru ini ke dalam katalog aset?' }}"
                      data-confirm-title="{{ isset($item) ? 'Perbarui Data Barang?' : 'Tambah Barang Baru?' }}">
                    @csrf
                    @if(isset($item)) @method('PUT') @endif

                    {{-- STATUS KEPEMILIKAN ASET --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kepemilikan Barang</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="card h-100 p-2 text-center border ownership-card" id="label_ownership_internal" style="cursor:pointer; transition: all 0.2s;">
                                    <input type="radio" name="ownership_status" value="internal" class="d-none"
                                           {{ old('ownership_status', $item->ownership_status ?? 'internal') === 'internal' ? 'checked' : '' }}>
                                    <div class="fw-bold fs-7 mb-1 text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.464 9.99a11.8 11.8 0 0 0 4.048 3.125.7.7 0 0 0 .694 0 11.8 11.8 0 0 0 4.048-3.125c1.677-2.195 3.06-5.513 2.464-9.99a1.54 1.54 0 0 0-1.044-1.263 63 63 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793z"/>
                                        </svg>
                                        Milik Sendiri
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem; line-height:1.2;">Aset Daerah / Internal</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card h-100 p-2 text-center border ownership-card" id="label_ownership_vendor" style="cursor:pointer; transition: all 0.2s;">
                                    <input type="radio" name="ownership_status" value="vendor" class="d-none"
                                           {{ old('ownership_status', $item->ownership_status ?? '') === 'vendor' ? 'checked' : '' }}>
                                    <div class="fw-bold fs-7 mb-1" style="color:#7e22ce;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h1a.5.5 0 0 1 0 1H0a.5.5 0 0 1 0-1h1V9a.5.5 0 0 1 .5-.5m2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5"/>
                                        </svg>
                                        Milik Vendor
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem; line-height:1.2;">Sewa / Rekanan Pihak Ke-3</small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card h-100 p-2 text-center border ownership-card" id="label_ownership_external" style="cursor:pointer; transition: all 0.2s;">
                                    <input type="radio" name="ownership_status" value="external" class="d-none"
                                           {{ old('ownership_status', $item->ownership_status ?? '') === 'external' ? 'checked' : '' }}>
                                    <div class="fw-bold fs-7 mb-1" style="color:#b45309;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                            <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1z"/>
                                        </svg>
                                        Instansi Lain
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem; line-height:1.2;">Pinjam Pakai / Titipan</small>
                                </label>
                            </div>
                        </div>
                        @error('ownership_status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- NAMA VENDOR / INSTANSI PEMILIK (KONDISIONAL) --}}
                    <div class="mb-3" id="owner_name_container" style="display:none;">
                        <label class="form-label" id="owner_name_label">
                            Nama Vendor / Instansi Pemilik <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="owner_name" id="owner_name"
                               class="form-control @error('owner_name') is-invalid @enderror"
                               value="{{ old('owner_name', $item->owner_name ?? '') }}"
                               placeholder="Contoh: PT Astra Graphia / CV Mitra Solusi / Dinas Kominfo">
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Dokumentasikan nama perusahaan vendor rekanan atau dinas/instansi asal pemilik barang.
                        </div>
                        @error('owner_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori Barang</label>
                        <select name="category_id" id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                {{ isset($item) ? 'disabled' : 'required' }}>
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-prefix="{{ $category->sku_prefix }}"
                                        data-next="{{ $category->next_sku_number }}"
                                        {{ old('category_id', $item->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Saat edit, kirim value via hidden karena select disabled tidak dikirim --}}
                        @if(isset($item))
                            <input type="hidden" name="category_id" value="{{ $item->category_id }}">
                            <div class="form-text text-muted" style="font-size:.78rem;">
                                Kategori tidak dapat diubah setelah barang tersimpan.
                            </div>
                        @endif
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- SKU — readonly untuk auto-generate internal, teks input untuk vendor/external --}}
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span id="sku_label_title">Kode Barang (SKU)</span>
                            <span id="sku_badge" class="badge" style="background:#eff6ff; color:#2563eb; font-size:.7rem; font-weight:500;">
                                Auto-generate
                            </span>
                        </label>
                        <input type="text" name="sku" id="sku_preview"
                               class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku', $item->sku ?? '') }}"
                               placeholder="Pilih kategori untuk melihat pratinjau SKU"
                               {{ isset($item) ? 'readonly' : '' }}
                               style="font-family:monospace; background:#f8fafc; color:#64748b;">
                        <div class="form-text text-muted" id="sku_helper_text" style="font-size:.78rem;">
                            @if(isset($item))
                                SKU tidak dapat diubah setelah barang tersimpan.
                            @else
                                SKU ditentukan otomatis. Format: <code>PREFIX-NNN</code> (contoh: <code>FURN-003</code>).
                            @endif
                        </div>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- NAMA BARANG --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $item->name ?? '') }}"
                               placeholder="Contoh: Tenda Pleton 6x14 Meter" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- GAMBAR BARANG --}}
                    <div class="mb-3">
                        <label class="form-label">Foto Barang</label>
                        @if(isset($item) && $item->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="Foto Barang" style="max-height: 100px; border-radius: 8px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            Format yang didukung: JPG, PNG, WEBP. Maksimal 2MB.
                            @if(isset($item) && $item->image) <br><em>Upload gambar baru untuk mengganti gambar lama.</em> @endif
                        </div>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- JUMLAH BARANG (QTY) --}}
                    <div class="mb-4">
                        <label class="form-label">
                            Jumlah Barang <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="total_qty" id="total_qty"
                               class="form-control @error('total_qty') is-invalid @enderror"
                               value="{{ old('total_qty', isset($item) ? $item->total_qty : '1') }}"
                               min="1" step="1" placeholder="Contoh: 10" required
                               {{ isset($item) ? 'readonly style=background:#f8fafc;' : '' }}>
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            @if(isset($item))
                                Stok barang tersimpan tidak dapat diubah dari form ini demi transparansi audit. Penyesuaian stok fisik hanya dapat dilakukan melalui menu <a href="{{ route('stocks.create') }}" class="text-decoration-none fw-semibold">Kartu Stok</a>.
                            @else
                                Masukkan jumlah unit fisik barang yang didaftarkan (minimal 1 unit).
                            @endif
                        </div>
                        @error('total_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit"
                            id="btn-submit-item"
                            class="btn {{ isset($item) ? 'btn-warning' : 'btn-primary' }} w-100 fw-bold py-2">
                        {{ isset($item) ? 'Simpan Perubahan' : 'Simpan Barang Baru' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isEdit = {{ isset($item) ? 'true' : 'false' }};
    const radios = document.querySelectorAll('input[name="ownership_status"]');
    const ownerContainer = document.getElementById('owner_name_container');
    const ownerLabel = document.getElementById('owner_name_label');
    const ownerInput = document.getElementById('owner_name');
    const skuPreview = document.getElementById('sku_preview');
    const skuBadge = document.getElementById('sku_badge');
    const skuLabelTitle = document.getElementById('sku_label_title');
    const skuHelperText = document.getElementById('sku_helper_text');
    const sel = document.getElementById('category_id');

    function syncOwnershipUI(status) {
        // Highlight card active state
        document.querySelectorAll('.ownership-card').forEach(card => {
            card.classList.remove('border-success', 'border-primary', 'border-warning', 'shadow-sm', 'bg-light');
            card.style.borderColor = '#e2e8f0';
        });

        const activeCard = document.getElementById('label_ownership_' + status);
        if (activeCard) {
            activeCard.classList.add('shadow-sm');
            if (status === 'internal') {
                activeCard.style.borderColor = '#10b981';
                activeCard.style.backgroundColor = '#f0fdf4';
            } else if (status === 'vendor') {
                activeCard.style.borderColor = '#a855f7';
                activeCard.style.backgroundColor = '#faf5ff';
            } else if (status === 'external') {
                activeCard.style.borderColor = '#f59e0b';
                activeCard.style.backgroundColor = '#fffbeb';
            }
        }

        if (status === 'internal') {
            if (ownerContainer) ownerContainer.style.display = 'none';
            if (ownerInput) {
                ownerInput.removeAttribute('required');
            }
            if (!isEdit && skuPreview) {
                skuPreview.readOnly = true;
                skuPreview.style.backgroundColor = '#f8fafc';
                skuPreview.style.color = '#64748b';
                if (skuBadge) {
                    skuBadge.textContent = 'Auto-generate';
                    skuBadge.style.background = '#eff6ff';
                    skuBadge.style.color = '#2563eb';
                }
                if (skuLabelTitle) skuLabelTitle.textContent = 'Kode Barang (SKU)';
                if (skuHelperText) skuHelperText.innerHTML = 'SKU ditentukan otomatis oleh sistem sesuai kategori yang dipilih.';
                updatePreview();
            }
        } else if (status === 'vendor') {
            if (ownerContainer) ownerContainer.style.display = 'block';
            if (ownerLabel) ownerLabel.innerHTML = 'Nama Vendor Rekanan <span class="text-danger">*</span>';
            if (ownerInput) {
                ownerInput.setAttribute('required', 'required');
                ownerInput.placeholder = 'Contoh: PT Astra Graphia / CV Mitra Solusi';
            }
            if (!isEdit && skuPreview) {
                skuPreview.readOnly = false;
                skuPreview.style.backgroundColor = '#ffffff';
                skuPreview.style.color = '#1e293b';
                skuPreview.placeholder = 'Pindai barcode atau ketik SKU bawaan vendor...';
                if (skuBadge) {
                    skuBadge.textContent = 'Kode Bawaan Vendor';
                    skuBadge.style.background = '#f3e8ff';
                    skuBadge.style.color = '#7e22ce';
                }
                if (skuLabelTitle) skuLabelTitle.textContent = 'Kode Barcode / SKU Vendor';
                if (skuHelperText) skuHelperText.innerHTML = 'Ketik atau scan barcode bawaan vendor (kebal dari sinkronisasi otomatis SKU kantor).';
            }
        } else if (status === 'external') {
            if (ownerContainer) ownerContainer.style.display = 'block';
            if (ownerLabel) ownerLabel.innerHTML = 'Nama Instansi / Dinas Pemilik Asal <span class="text-danger">*</span>';
            if (ownerInput) {
                ownerInput.setAttribute('required', 'required');
                ownerInput.placeholder = 'Contoh: Dinas Kominfo / Balai Diklat / Kemenkeu RI';
            }
            if (!isEdit && skuPreview) {
                skuPreview.readOnly = false;
                skuPreview.style.backgroundColor = '#ffffff';
                skuPreview.style.color = '#1e293b';
                skuPreview.placeholder = 'Pindai barcode atau ketik nomor register instansi asal...';
                if (skuBadge) {
                    skuBadge.textContent = 'Kode Bawaan Instansi';
                    skuBadge.style.background = '#fef3c7';
                    skuBadge.style.color = '#b45309';
                }
                if (skuLabelTitle) skuLabelTitle.textContent = 'Nomor Register / SKU Instansi Asal';
                if (skuHelperText) skuHelperText.innerHTML = 'Ketik atau scan nomor register/KIB fisik dari instansi asal barang.';
            }
        }
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            syncOwnershipUI(this.value);
        });
    });

    const checkedRadio = document.querySelector('input[name="ownership_status"]:checked');
    if (checkedRadio) {
        syncOwnershipUI(checkedRadio.value);
    } else {
        syncOwnershipUI('internal');
    }

    function updatePreview() {
        if (!sel || !skuPreview) return;
        const currentStatus = document.querySelector('input[name="ownership_status"]:checked')?.value || 'internal';
        if (currentStatus !== 'internal') return; // Jangan timpa jika vendor/external

        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.dataset.prefix) {
            skuPreview.value = '';
            skuPreview.placeholder = 'Pilih kategori untuk melihat pratinjau SKU';
            return;
        }
        const prefix = opt.dataset.prefix;
        const num = parseInt(opt.dataset.next, 10);
        skuPreview.value = prefix + '-' + String(num).padStart(3, '0');
    }

    if (sel && !isEdit) {
        new TomSelect(sel, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: '— Pilih Kategori —',
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results" style="padding: 10px; color: #6c757d;">Tidak ada kategori yang cocok.</div>';
                }
            }
        });

        sel.addEventListener('change', updatePreview);
        if (sel.value) updatePreview();
    }

    // ── Validasi Logika Jumlah Barang Minimal 1 dengan SweetAlert2 ──
    const itemForm = document.querySelector('form[action*="items"]');
    const totalQtyInput = document.getElementById('total_qty');

    if (itemForm && totalQtyInput) {
        itemForm.addEventListener('submit', function (e) {
            const rawVal = totalQtyInput.value.trim();
            const qtyVal = parseInt(rawVal, 10);

            if (rawVal === '' || isNaN(qtyVal) || qtyVal <= 0) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    alert('jumlah barang minimal 1 silahkan cek kembali pada jumlah barang');
                }
                totalQtyInput.focus();
                return false;
            }
        }, true);
    }
});
</script>
@endpush
