@extends('layouts.app')

@section('title', 'Manajemen Kode Referensi & SKU — SIM-ASET')
@section('page-title', 'Manajemen Format Kode & SKU')

@section('content')
<style>
    .anatomy-block {
        border-radius: 8px;
        padding: 0.4rem 0.6rem;
        font-weight: 700;
        font-family: monospace;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Format Penomoran Kode SKU & Dokumen Transaksi</h5>
                    <p class="text-secondary small mb-0">Atur pola penomoran aset dan registrasi transaksi dinas agar seragam dan mudah ditelusuri. Dilengkapi fitur <strong>Live Preview</strong> seketika dan opsi <strong>Sinkronisasi Riwayat</strong>.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border p-2 px-3 rounded-pill font-monospace" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generator Otomatis
                    </span>
                </div>
            </div>

            <!-- Anatomi Visual Barcode / SKU Explanation -->
            <div class="mt-3 pt-3 border-top">
                <small class="text-secondary fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-shapes text-primary me-1"></i> Anatomi Struktur Penomoran SIM-ASET:
                </small>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="anatomy-block bg-primary text-white" title="Inisial kategori barang atau identitas modul">
                        [AWALAN / PREFIX]
                    </span>
                    <span class="fw-bold text-secondary fs-5">+</span>
                    <span class="anatomy-block bg-secondary text-white" title="Karakter pemisah">- / _</span>
                    <span class="fw-bold text-secondary fs-5">+</span>
                    <span class="anatomy-block bg-info text-white" title="Format tanggal atau tahun pencatatan">
                        [TANGGAL / TAHUN]
                    </span>
                    <span class="fw-bold text-secondary fs-5">+</span>
                    <span class="anatomy-block bg-secondary text-white" title="Karakter pemisah">- / _</span>
                    <span class="fw-bold text-secondary fs-5">+</span>
                    <span class="anatomy-block bg-success text-white" title="Nomor urut registrasi atau kode acak unik">
                        [NOMOR URUT / SUFFIX]
                    </span>
                </div>
            </div>
        </div>
    </div>

    @foreach($settings as $setting)
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column" style="border: 1px solid #cbd5e1 !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle me-2">
                        <i class="fa-solid fa-barcode fs-6"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $setting->name }}</h6>
                        <small class="text-secondary font-monospace" style="font-size: 0.7rem;">Entitas: {{ $setting->entity }}</small>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('admin-settings.code-formats.update', $setting) }}" method="POST" class="d-flex flex-column flex-grow-1 form-code-format" id="form_{{ $setting->id }}">
                @csrf
                @method('PUT')
                <div class="card-body p-4 flex-grow-1">
                    <p class="text-secondary small mb-4" style="line-height: 1.4;">{{ $setting->description }}</p>

                    <!-- LIVE PREVIEW BOX DENGAN HIGH CONTRAST -->
                    <div class="p-3 rounded-4 border mb-4 text-center" style="background-color: #f8fafc; border-color: #94a3b8 !important;">
                        <small class="text-secondary text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fa-regular fa-eye text-primary me-1"></i> Live Preview Pola Kode
                        </small>
                        <div class="fw-bold font-monospace text-primary fs-4 preview-result" id="preview_{{ $setting->id }}">
                            {{ $setting->generatePreview() }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">1. Awalan Kode (Prefix)</label>
                        <input type="text" 
                               name="prefix" 
                               class="form-control rounded-3 font-monospace input-field" 
                               id="prefix_{{ $setting->id }}"
                               value="{{ $setting->prefix }}" 
                               placeholder="{{ $setting->entity === 'item_sku' ? 'Dinamis (Contoh: ELEK / MEUB)' : 'Contoh: PJM / BAST' }}"
                               data-setting-id="{{ $setting->id }}">
                        <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                            {{ $setting->entity === 'item_sku' ? 'Kosongkan jika ingin mengikuti inisial kategori barang secara otomatis (misal: Kursi -> FURN).' : 'Teks awalan baku di depan nomor transaksi.' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">2. Tanda Pemisah (Separator)</label>
                        <select name="separator" class="form-select rounded-3 input-field" id="separator_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="-" {{ $setting->separator === '-' ? 'selected' : '' }}>Tanda Hubung (-)</option>
                            <option value="/" {{ $setting->separator === '/' ? 'selected' : '' }}>Garis Miring (/)</option>
                            <option value="_" {{ $setting->separator === '_' ? 'selected' : '' }}>Garis Bawah (_)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">3. Unsur Tanggal / Periode</label>
                        <select name="date_format" class="form-select rounded-3 input-field" id="date_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="NONE" {{ empty($setting->date_format) ? 'selected' : '' }}>Tanpa Tanggal (Hanya Nomor Urut)</option>
                            <option value="YYYYMMDD" {{ $setting->date_format === 'YYYYMMDD' ? 'selected' : '' }}>TahunBulanHari Penuh (Contoh: {{ now()->format('Ymd') }})</option>
                            <option value="YYYY-MM" {{ $setting->date_format === 'YYYY-MM' ? 'selected' : '' }}>Tahun-Bulan (Contoh: {{ now()->format('Y-m') }})</option>
                            <option value="YYYY/MM" {{ $setting->date_format === 'YYYY/MM' ? 'selected' : '' }}>Tahun/Bulan (Contoh: {{ now()->format('Y/m') }})</option>
                            <option value="YYYY" {{ $setting->date_format === 'YYYY' ? 'selected' : '' }}>Tahun Saja (Contoh: {{ now()->format('Y') }})</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">4. Akhiran Kode (Suffix / Nomor Urut)</label>
                        <select name="suffix_type" class="form-select rounded-3 input-field suffix-type-select" id="suffix_type_{{ $setting->id }}" data-setting-id="{{ $setting->id }}">
                            <option value="INCREMENT_3_DIGIT" {{ $setting->suffix_type === 'INCREMENT_3_DIGIT' ? 'selected' : '' }}>Nomor Urut 3 Digit (001, 002, 003...)</option>
                            <option value="INCREMENT_4_DIGIT" {{ $setting->suffix_type === 'INCREMENT_4_DIGIT' ? 'selected' : '' }}>Nomor Urut 4 Digit (0001, 0002, 0003...)</option>
                            <option value="RANDOM_ALPHANUMERIC_5" {{ $setting->suffix_type === 'RANDOM_ALPHANUMERIC_5' ? 'selected' : '' }}>5 Karakter Acak Unik (Contoh: RRBQA)</option>
                            <option value="CUSTOM" {{ $setting->suffix_type === 'CUSTOM' ? 'selected' : '' }}>Teks Kustom Sendiri</option>
                        </select>
                    </div>

                    <div class="mb-3 custom-suffix-container" id="custom_container_{{ $setting->id }}" style="{{ $setting->suffix_type === 'CUSTOM' ? '' : 'display: none;' }}">
                        <label class="form-label small fw-bold text-dark mb-1">Teks Suffix Kustom</label>
                        <input type="text" 
                               name="custom_suffix" 
                               class="form-control rounded-3 font-monospace input-field" 
                               id="custom_suffix_{{ $setting->id }}"
                               value="{{ $setting->custom_suffix ?? '001' }}" 
                               data-setting-id="{{ $setting->id }}">
                    </div>

                    <!-- CHECKBOX SINKRONISASI DATA MASA LALU -->
                    <div class="p-3 rounded-3 border bg-light mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sync_legacy_data" value="1" id="sync_{{ $setting->id }}">
                            <label class="form-check-label small fw-bold text-dark cursor-pointer" for="sync_{{ $setting->id }}">
                                Terapkan ke Seluruh Data Riwayat
                            </label>
                        </div>
                        <div class="text-secondary small mt-1" style="font-size: 0.72rem; line-height: 1.3;">
                            Perbarui nomor kode pada data yang sudah ada sebelumnya agar formatnya seragam.
                        </div>
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

    document.querySelectorAll('.form-code-format').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const syncCheckbox = this.querySelector('input[name="sync_legacy_data"]');
            const isSync = syncCheckbox && syncCheckbox.checked;

            Swal.fire({
                title: isSync ? 'Simpan & Sinkronkan Data Lama?' : 'Simpan Format Kode?',
                text: isSync 
                    ? 'Perhatian: Seluruh data riwayat di database yang terkait dengan entitas ini akan diperbarui format nomornya.' 
                    : 'Konfigurasi format kode ini akan segera diterapkan pada pencatatan transaksi berikutnya.',
                icon: isSync ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: isSync ? 'Ya, Simpan & Sinkronkan' : 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isSync) {
                        Swal.fire({
                            title: 'Sedang Menyinkronkan Data...',
                            html: `
                                <div class="py-2 text-center">
                                    <p class="text-secondary small mb-3">Memperbarui seluruh data riwayat ke format kode baru secara aman. Mohon tunggu sejenak, sistem sedang memproses...</p>
                                    <div class="progress" style="height: 18px; border-radius: 9px; background-color: #e2e8f0;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                             role="progressbar" 
                                             style="width: 100%; font-size: 0.75rem; font-weight: 600;" 
                                             aria-valuenow="100" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            Memproses Sinkronisasi Database...
                                        </div>
                                    </div>
                                    <span class="text-muted d-block mt-2 font-monospace" style="font-size: 0.7rem;">Jangan menutup atau merefresh peramban ini</span>
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });
                    }
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
