@extends('layouts.app')

@section('title', 'Buat Peminjaman Baru — SIM-ASET')
@section('page-title', 'Buat Peminjaman Baru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Transaksi Peminjaman Baru</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Isi data di bawah untuk mencatat peminjaman aset</p>
    </div>
    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

{{-- Data barang dari server — dipakai JS, tidak di-render sebagai <select> panjang --}}
@php
    $itemsJson = $items->map(function ($i) {
        return ['id' => $i->id, 'name' => $i->name, 'sku' => $i->sku ?? '', 'qty' => $i->available_qty];
    });
@endphp
<script id="items-data" type="application/json">
{!! json_encode($itemsJson) !!}
</script>

<style>
    /* ── Searchable Item Picker ──────────────────── */
    .item-picker-wrap { position: relative; }

    .item-picker-display {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        height: 38px;
        padding: .45rem .75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        font-size: .875rem;
        transition: border-color .2s, box-shadow .2s;
        user-select: none;
    }
    .item-picker-display:focus-within,
    .item-picker-display.open {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }
    .item-picker-display .placeholder { color: #94a3b8; }
    .item-picker-display .selected-text { color: #1e293b; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-picker-display .caret { flex-shrink: 0; color: #94a3b8; transition: transform .2s; }
    .item-picker-display.open .caret { transform: rotate(180deg); }

    .item-picker-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0; right: 0;
        z-index: 1050;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        overflow: hidden;
    }
    .item-picker-dropdown.open { display: block; }

    .item-picker-search-wrap {
        padding: .5rem .6rem;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
    }
    .item-picker-search {
        width: 100%;
        padding: .4rem .65rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 7px;
        font-size: .83rem;
        outline: none;
        background: #fff;
        transition: border-color .2s;
    }
    .item-picker-search:focus { border-color: #2563eb; }

    .item-picker-list {
        max-height: 220px;
        overflow-y: auto;
        padding: .35rem 0;
    }
    .item-picker-list::-webkit-scrollbar { width: 5px; }
    .item-picker-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }

    .item-picker-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .5rem .85rem;
        cursor: pointer;
        font-size: .85rem;
        transition: background .1s;
        gap: .5rem;
    }
    .item-picker-option:hover:not(.disabled) { background: #eff6ff; }
    .item-picker-option.selected { background: #eff6ff; color: #2563eb; font-weight: 600; }

    .item-picker-option.disabled {
        opacity: .35;
        cursor: not-allowed;
        pointer-events: none;
    }
    .item-picker-option.hidden { display: none; }

    .option-name { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .option-badge {
        flex-shrink: 0;
        font-size: .7rem;
        font-weight: 500;
        padding: .15em .5em;
        border-radius: 4px;
        background: #f0fdf4;
        color: #166534;
        white-space: nowrap;
    }
    .option-badge.zero { background: #fef2f2; color: #991b1b; }

    .item-picker-empty {
        padding: .85rem;
        text-align: center;
        font-size: .82rem;
        color: #94a3b8;
    }

    /* Error state */
    .item-picker-display.is-invalid {
        border-color: #dc3545;
    }
    .item-picker-display.is-invalid:focus-within {
        box-shadow: 0 0 0 3px rgba(220,53,69,.15);
    }
</style>

<form action="{{ route('loans.store') }}" method="POST" id="loan-form">
    @csrf

    <div class="row g-4">

        <div class="col-12 mb-3">
            <div class="card border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div>
                            <div class="fw-bold text-primary">Scan Barcode / Ketik SKU di Sini</div>
                            <small class="text-muted">Gunakan scanner USB atau kamera web untuk menambah barang secara cepat</small>
                        </div>
                        <div class="text-muted small">Fallback manual tetap tersedia</div>
                    </div>
                    <div class="mt-3 position-relative">
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Pilih Input Device</label>
                                <select id="scan-device-select" class="form-select form-select-sm">
                                    <option value="keyboard">Keyboard / Scanner USB</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Status</label>
                                <div class="form-control form-control-sm bg-light text-muted" id="scan-device-status">Menunggu input keyboard/scanner…</div>
                            </div>
                        </div>
                        <input type="text" id="sku-scan-input" class="form-control form-control-lg" placeholder="Contoh: ELEC-001" autocomplete="off" spellcheck="false" autofocus>
                        <div id="sku-scan-feedback" class="small mt-2 text-muted">Siap menerima input scanner…</div>
                    </div>
                    <div id="camera-preview-wrapper" class="mt-3 d-none">
                        <div class="small text-muted mb-2">Preview kamera aktif</div>
                        <video id="camera-preview" class="w-100 rounded border" autoplay playsinline muted style="max-height: 220px; background: #111827;"></video>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kiri: Info Peminjam --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Informasi Peminjaman</div>
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Peminjam (Instansi)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.02 1.02 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg></span>
                            <input type="text" class="form-control form-control-sm" id="borrower-search" placeholder="Cari instansi..." autocomplete="off">
                        </div>
                        <select name="borrower_id" id="borrower_id" class="form-select @error('borrower_id') is-invalid @enderror" required>
                            <option value="">— Pilih Instansi —</option>
                            @foreach($borrowers as $borrower)
                                <option value="{{ $borrower->id }}" {{ old('borrower_id') == $borrower->id ? 'selected' : '' }}>
                                    {{ $borrower->institution_name }} — {{ $borrower->pic_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('borrower_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" name="borrow_date"
                               class="form-control @error('borrow_date') is-invalid @enderror"
                               value="{{ old('borrow_date', date('Y-m-d')) }}" required>
                        @error('borrow_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Batas Pengembalian</label>
                        <input type="date" name="due_date"
                               class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date') }}" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Catatan <span class="text-muted">(opsional)</span></label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="3" placeholder="Contoh: Untuk kegiatan pelantikan…">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Barang --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Daftar Barang yang Dipinjam</span>
                    <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">
                        + Tambah Baris
                    </button>
                </div>
                <div class="card-body p-4">
                    @error('items')
                        <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
                    @enderror

                    <div id="items-container">
                        {{-- Baris pertama dirender oleh JS setelah DOMContentLoaded --}}
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        Proses & Simpan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    const borrowerSelect = document.getElementById('borrower_id');
    const borrowerSearch = document.getElementById('borrower-search');
    if (borrowerSelect && borrowerSearch) {
        borrowerSearch.addEventListener('input', function () {
            const value = this.value.trim().toLowerCase();
            Array.from(borrowerSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }
                option.hidden = value !== '' && !option.text.toLowerCase().includes(value);
            });
        });
    }

    // ── Data Sumber ──────────────────────────────────────
    const ITEMS = JSON.parse(document.getElementById('items-data').textContent);

    // Simpan semua picker yang aktif agar bisa saling dikecualikan
    let pickers = [];    // array of { row, hiddenInput, selectedId }
    let rowIndex = 0;

    // ── Fungsi Utama: Buat Satu Baris Picker ─────────────
    function createRow(isFirst = false) {
        const idx = rowIndex++;

        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end item-row mb-3';
        row.dataset.idx = idx;

        // Hidden input (yang dikirim ke server)
        const hiddenInput = document.createElement('input');
        hiddenInput.type  = 'hidden';
        hiddenInput.name  = `items[${idx}][item_id]`;
        hiddenInput.value = '';

        // Hidden qty validation wrapper
        const qtyName = `items[${idx}][qty]`;

        row.innerHTML = `
            <div class="col-8">
                <label class="form-label">Barang</label>
                <div class="item-picker-wrap">
                    <div class="item-picker-display" tabindex="0" role="combobox" aria-expanded="false">
                        <span class="display-text placeholder">— Cari & pilih barang —</span>
                        <svg class="caret" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                    <div class="item-picker-dropdown">
                        <div class="item-picker-search-wrap">
                            <input type="text" class="item-picker-search" placeholder="Ketik nama barang…" autocomplete="off" spellcheck="false">
                        </div>
                        <div class="item-picker-list"></div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <label class="form-label">Jumlah</label>
                <input type="number" name="${qtyName}" class="form-control qty-input" min="1" max="" placeholder="0" required>
            </div>
            <div class="col-1 d-flex align-items-end">
                ${isFirst ? '' : `<button type="button" class="btn btn-sm btn-outline-danger btn-remove w-100" style="padding:.45rem;" title="Hapus baris">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>`}
            </div>
        `;

        // Sisipkan hidden input ke dalam wrap
        row.querySelector('.item-picker-wrap').appendChild(hiddenInput);

        // State baris ini
        const state = { row, hiddenInput, selectedId: null };
        pickers.push(state);

        // Referensi elemen
        const display  = row.querySelector('.item-picker-display');
        const dropdown = row.querySelector('.item-picker-dropdown');
        const search   = row.querySelector('.item-picker-search');
        const list     = row.querySelector('.item-picker-list');
        const qtyInput = row.querySelector('.qty-input');

        // Render semua opsi ke list
        function renderOptions(query = '') {
            const q    = query.trim().toLowerCase();
            // ID yang sudah dipilih oleh baris LAIN
            const used = pickers
                .filter(p => p !== state && p.selectedId !== null)
                .map(p => p.selectedId);

            let hasVisible = false;
            list.innerHTML = '';

            ITEMS.forEach(item => {
                const matchText = item.name.toLowerCase().includes(q)
                               || item.sku.toLowerCase().includes(q);
                if (!matchText) return;

                const isUsed   = used.includes(item.id);
                const isZero   = item.qty === 0;
                const isSelf   = state.selectedId === item.id;
                const disabled = isUsed || (isZero && !isSelf);

                const opt = document.createElement('div');
                opt.className = 'item-picker-option' +
                    (disabled   ? ' disabled'  : '') +
                    (isSelf     ? ' selected'  : '');
                opt.dataset.id  = item.id;
                opt.dataset.max = item.qty;
                opt.innerHTML   = `
                    <span class="option-name">${item.name}${item.sku ? ` <small style="color:#94a3b8;">(${item.sku})</small>` : ''}</span>
                    <span class="option-badge ${item.qty === 0 ? 'zero' : ''}">
                        ${isUsed ? 'Dipilih' : 'Stok: ' + item.qty}
                    </span>`;

                if (!disabled) {
                    opt.addEventListener('click', () => selectItem(item));
                }
                list.appendChild(opt);
                if (!disabled) hasVisible = true;
            });

            if (!hasVisible) {
                list.innerHTML = `<div class="item-picker-empty">Tidak ada barang yang cocok${q ? ` untuk "<b>${q}</b>"` : ''}</div>`;
            }
        }

        // Pilih item
        function selectItem(item) {
            state.selectedId    = item.id;
            hiddenInput.value   = item.id;
            qtyInput.max        = item.qty;
            qtyInput.value      = Math.min(qtyInput.value || 1, item.qty) || 1;

            // Update tampilan display
            display.classList.remove('is-invalid');
            display.querySelector('.display-text').className = 'display-text selected-text';
            display.querySelector('.display-text').textContent =
                item.name + (item.sku ? ` (${item.sku})` : '') + ` — Stok: ${item.qty}`;

            closeDropdown();

            // Beritahu semua picker lain untuk refresh (exclude item ini)
            refreshAllOtherPickers(state);
        }

        // Buka/tutup dropdown
        function openDropdown() {
            // Tutup semua dropdown lain dulu
            pickers.forEach(p => {
                if (p !== state) closeDropdownOf(p);
            });

            dropdown.classList.add('open');
            display.classList.add('open');
            display.setAttribute('aria-expanded', 'true');
            renderOptions(search.value);
            search.value = '';
            search.focus();
        }

        function closeDropdown() {
            dropdown.classList.remove('open');
            display.classList.remove('open');
            display.setAttribute('aria-expanded', 'false');
        }

        state.close   = closeDropdown;
        state.refresh = () => renderOptions(search.value);

        // Events
        display.addEventListener('click', (e) => {
            if (dropdown.classList.contains('open')) closeDropdown();
            else openDropdown();
        });

        display.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openDropdown(); }
            if (e.key === 'Escape') closeDropdown();
        });

        search.addEventListener('input', () => renderOptions(search.value));

        search.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDropdown();
        });

        // Hapus baris
        const btnRemove = row.querySelector('.btn-remove');
        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                pickers = pickers.filter(p => p !== state);
                row.remove();
                refreshAllOtherPickers(null);
            });
        }

        // Form submit validation: pastikan item dipilih
        document.getElementById('loan-form').addEventListener('submit', function () {
            if (!state.selectedId) {
                display.classList.add('is-invalid');
            }
        });

        return row;
    }

    // Tutup dropdown milik picker tertentu
    function closeDropdownOf(pickerState) {
        pickerState.close?.();
    }

    // Refresh semua picker kecuali yang memicu perubahan
    function refreshAllOtherPickers(except) {
        pickers.forEach(p => {
            if (p !== except) p.refresh?.();
        });
    }

    // Tutup dropdown kalau klik di luar
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.item-picker-wrap')) {
            pickers.forEach(p => p.close?.());
        }
    });

    // ── Init: render baris pertama ───────────────────────
    const container = document.getElementById('items-container');
    container.appendChild(createRow(true));

    document.getElementById('btn-add-item').addEventListener('click', () => {
        if (ITEMS.length === 0) return;
        const usedCount = pickers.filter(p => p.selectedId !== null).length;
        if (usedCount >= ITEMS.length) {
            alert('Semua barang yang tersedia sudah ditambahkan ke daftar.');
            return;
        }
        container.appendChild(createRow(false));
    });

    const skuInput = document.getElementById('sku-scan-input');
    const skuFeedback = document.getElementById('sku-scan-feedback');
    const scanDeviceSelect = document.getElementById('scan-device-select');
    const scanDeviceStatus = document.getElementById('scan-device-status');
    const cameraPreviewWrapper = document.getElementById('camera-preview-wrapper');
    const cameraPreview = document.getElementById('camera-preview');
    let cameraStream = null;

    async function populateCameraDevices() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;

        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoInputs = devices.filter(device => device.kind === 'videoinput');

        if (!videoInputs.length) return;

        videoInputs.forEach((device, index) => {
            const option = document.createElement('option');
            option.value = `camera:${device.deviceId || index}`;
            option.textContent = device.label || `Kamera ${index + 1}`;
            scanDeviceSelect.appendChild(option);
        });
    }

    async function startCameraPreview(deviceId) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;

        try {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: { deviceId: deviceId ? { exact: deviceId } : undefined, facingMode: 'environment' }
            });

            cameraStream = stream;
            cameraPreview.srcObject = stream;
            cameraPreviewWrapper.classList.remove('d-none');
            scanDeviceStatus.textContent = 'Kamera aktif — siap memindai barcode.';
            scanDeviceStatus.className = 'form-control form-control-sm bg-light text-success';
        } catch (error) {
            cameraPreviewWrapper.classList.add('d-none');
            scanDeviceStatus.textContent = 'Kamera tidak tersedia; gunakan scanner USB/keyboard.';
            scanDeviceStatus.className = 'form-control form-control-sm bg-light text-warning';
        }
    }

    scanDeviceSelect.addEventListener('change', async function () {
        const value = this.value;
        if (value === 'keyboard') {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            cameraPreviewWrapper.classList.add('d-none');
            scanDeviceStatus.textContent = 'Menunggu input keyboard/scanner…';
            scanDeviceStatus.className = 'form-control form-control-sm bg-light text-muted';
            return;
        }

        const deviceId = value.replace('camera:', '');
        await startCameraPreview(deviceId);
    });

    populateCameraDevices();

    function setFeedback(message, tone = 'muted') {
        skuFeedback.textContent = message;
        skuFeedback.className = 'small mt-2 text-' + tone;
    }

    async function addItemFromSku(sku) {
        const normalized = (sku || '').trim();
        if (!normalized) return;

        try {
            setFeedback('Memvalidasi SKU di server…', 'primary');
            const response = await fetch(`/items/lookup?sku=${encodeURIComponent(normalized)}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                setFeedback(result.message || 'SKU tidak ditemukan. Coba cek kembali atau masukkan data manual.', 'danger');
                skuInput.value = '';
                return;
            }

            const item = result.item;
            const existing = pickers.find(p => p.selectedId === item.id);
            if (existing) {
                const qtyInput = existing.row.querySelector('.qty-input');
                const current = Number(qtyInput.value || 0);
                qtyInput.value = Math.min(current + 1, item.available_qty || current + 1);
                setFeedback(`Barang "${item.name}" sudah ada di daftar, qty ditambah 1.`, 'success');
                skuInput.value = '';
                return;
            }

            const row = createRow(false);
            const display = row.querySelector('.item-picker-display');
            const hidden = row.querySelector('input[type="hidden"]');
            const qty = row.querySelector('.qty-input');
            const itemState = pickers[pickers.length - 1];

            itemState.selectedId = item.id;
            hidden.value = item.id;
            qty.max = item.available_qty;
            qty.value = 1;

            display.querySelector('.display-text').className = 'display-text selected-text';
            display.querySelector('.display-text').textContent = item.name + (item.sku ? ` (${item.sku})` : '') + ` — Stok: ${item.available_qty}`;
            display.classList.remove('is-invalid');

            const list = row.querySelector('.item-picker-list');
            list.innerHTML = '<div class="item-picker-empty">Barang ditambahkan via scan SKU</div>';

            setFeedback(`Barang "${item.name}" berhasil ditambahkan dari SKU ${item.sku}.`, 'success');
            skuInput.value = '';
            skuInput.focus();
        } catch (error) {
            setFeedback('Gagal memvalidasi SKU. Pastikan koneksi dan data SKU benar.', 'danger');
            skuInput.value = '';
        }
    }

    skuInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        addItemFromSku(skuInput.value);
    });

    skuInput.addEventListener('input', function () {
        const value = skuInput.value.trim();
        if (!value) {
            setFeedback('Siap menerima input scanner…', 'muted');
            return;
        }

        setFeedback('Memproses SKU…', 'primary');
    });

})();
</script>
@endpush
