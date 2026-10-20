@extends('layouts.app')

@section('title', 'Manajemen Kode Referensi & SKU — SIM-ASET')
@section('page-title', 'Manajemen Format Kode & SKU')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Format Penomoran Kode SKU & Dokumen Transaksi</h5>
                <p class="text-muted small mb-0">Sesuaikan pola penomoran kode inventaris aset daerah dan dokumen agar fleksibel dengan standarisasi instansi Anda, lengkap dengan fitur <strong>Live Preview</strong> seketika.</p>
            </div>
        </div>
    </div>

    @foreach($settings as $setting)
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle me-2">
                        <i class="fa-solid fa-barcode"></i>
                    </span>
                    <h6 class="mb-0 fw-bold text-dark">{{ $setting->name }}</h6>
                </div>
            </div>
            
            <form action="{{ route('admin-settings.code-formats.update', $setting) }}" method="POST" class="d-flex flex-column flex-grow-1" id="form_{{ $setting->id }}">
                @csrf
                @method('PUT')
                <div class="card-body p-4 flex-grow-1">
                    <p class="text-muted small mb-4">{{ $setting->description }}</p>

                    <!-- LIVE PREVIEW BOX -->
                    <div class="p-3 rounded-4 bg-light border border-dashed mb-4 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            <i class="fa-regular fa-eye me-1"></i> Live Preview Hasil Kode
                        </small>
                        <div class="fw-bold font-monospace text-primary fs-5 preview-result" id="preview_{{ $setting->id }}">
                            {{ $setting->generatePreview() }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Awalan Kode (Prefix)</label>
                        <input type="text" 
                               name="prefix" 
                               class="form-control rounded-3 font-monospace input-field" 
                               id="prefix_{{ $setting->id }}"
                               value="{{ $setting->prefix }}" 
                               placeholder="{{ $setting->entity === 'item_sku' ? 'Dinamis (Contoh: ELEK / MEUB)' : 'Contoh: PJM / BAST' }}"
                               data-setting-id="{{ $setting->id }}">
                        <div class="form-text small" style="font-size: 0.75rem;">
                            {{ $setting->entity === 'item_sku' ? 'Kosongkan jika ingin mengikuti inisial kategori barang secara otomatis.' : 'Prefix teks di bagian depan kode.' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pemisah (Separator)</label>
                        <select name="separator" class="form-select rounded-3 input-field" id="separator_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="-" {{ $setting->separator === '-' ? 'selected' : '' }}>Tanda Hubung (-)</option>
                            <option value="/" {{ $setting->separator === '/' ? 'selected' : '' }}>Garis Miring (/)</option>
                            <option value="_" {{ $setting->separator === '_' ? 'selected' : '' }}>Garis Bawah (_)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Format Elemen Tanggal / Waktu</label>
                        <select name="date_format" class="form-select rounded-3 input-field" id="date_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="NONE" {{ empty($setting->date_format) ? 'selected' : '' }}>Tanpa Tanggal</option>
                            <option value="YYYYMMDD" {{ $setting->date_format === 'YYYYMMDD' ? 'selected' : '' }}>TahunBulanHari (Contoh: {{ now()->format('Ymd') }})</option>
                            <option value="YYYY-MM" {{ $setting->date_format === 'YYYY-MM' ? 'selected' : '' }}>Tahun-Bulan (Contoh: {{ now()->format('Y-m') }})</option>
                            <option value="YYYY/MM" {{ $setting->date_format === 'YYYY/MM' ? 'selected' : '' }}>Tahun/Bulan (Contoh: {{ now()->format('Y/m') }})</option>
                            <option value="YYYY" {{ $setting->date_format === 'YYYY' ? 'selected' : '' }}>Tahun Saja (Contoh: {{ now()->format('Y') }})</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Format Akhiran (Suffix / Bagian Belakang)</label>
                        <select name="suffix_type" class="form-select rounded-3 input-field suffix-type-select" id="suffix_type_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="INCREMENT_3_DIGIT" {{ $setting->suffix_type === 'INCREMENT_3_DIGIT' ? 'selected' : '' }}>Nomor Urut 3 Digit (001, 002, ...)</option>
                            <option value="INCREMENT_4_DIGIT" {{ $setting->suffix_type === 'INCREMENT_4_DIGIT' ? 'selected' : '' }}>Nomor Urut 4 Digit (0001, 0002, ...)</option>
                            <option value="RANDOM_ALPHANUMERIC_5" {{ $setting->suffix_type === 'RANDOM_ALPHANUMERIC_5' ? 'selected' : '' }}>5 Karakter Acak Unik (Contoh: RRBQA)</option>
                            <option value="CUSTOM" {{ $setting->suffix_type === 'CUSTOM' ? 'selected' : '' }}>Kustom Teks Sendiri</option>
                        </select>
                    </div>

                    <div class="mb-3 custom-suffix-container" id="custom_container_{{ $setting->id }}" style="{{ $setting->suffix_type === 'CUSTOM' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-semibold">Teks Suffix Kustom</label>
                        <input type="text" 
                               name="custom_suffix" 
                               class="form-control rounded-3 font-monospace input-field" 
                               id="custom_suffix_{{ $setting->id }}"
                               value="{{ $setting->custom_suffix ?? '001' }}" 
                               data-setting-id="{{ $setting->id }}">
                    </div>
                </div>

                <div class="card-footer bg-white py-3 border-top d-flex justify-content-end mt-auto">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Format
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    function updatePreview(id) {
        const prefixInput = document.getElementById('prefix_' + id);
        const sepSelect = document.getElementById('separator_' + id);
        const dateSelect = document.getElementById('date_' + id);
        const suffixSelect = document.getElementById('suffix_type_' + id);
        const customInput = document.getElementById('custom_suffix_' + id);
        const customContainer = document.getElementById('custom_container_' + id);
        const previewElement = document.getElementById('preview_' + id);

        if (!prefixInput || !previewElement) return;

        let prefix = prefixInput.value.trim();
        if (!prefix) {
            prefix = (id === '1' || prefixInput.placeholder.includes('Dinamis')) ? 'ELEK' : 'KODE';
        }

        const sep = sepSelect.value || '-';
        let datePart = '';
        const dateFormat = dateSelect.value;
        if (dateFormat === 'YYYYMMDD') {
            datePart = `${year}${month}${day}`;
        } else if (dateFormat === 'YYYY-MM') {
            datePart = `${year}-${month}`;
        } else if (dateFormat === 'YYYY/MM') {
            datePart = `${year}/${month}`;
        } else if (dateFormat === 'YYYY') {
            datePart = `${year}`;
        }

        let suffixPart = '001';
        const suffixType = suffixSelect.value;
        if (suffixType === 'INCREMENT_4_DIGIT') {
            suffixPart = '0001';
            customContainer.style.display = 'none';
        } else if (suffixType === 'RANDOM_ALPHANUMERIC_5') {
            suffixPart = 'RRBQA';
            customContainer.style.display = 'none';
        } else if (suffixType === 'CUSTOM') {
            customContainer.style.display = 'block';
            suffixPart = customInput.value.trim() || 'CUSTOM';
        } else {
            suffixPart = '001';
            customContainer.style.display = 'none';
        }

        const parts = [prefix];
        if (datePart) parts.push(datePart);
        if (suffixPart) parts.push(suffixPart);

        previewElement.textContent = parts.join(sep);
    }

    document.querySelectorAll('.input-field').forEach(function(el) {
        el.addEventListener('input', function() {
            const id = this.getAttribute('data-setting-id');
            updatePreview(id);
        });
        el.addEventListener('change', function() {
            const id = this.getAttribute('data-setting-id');
            updatePreview(id);
        });
    });
});
</script>
@endsection
