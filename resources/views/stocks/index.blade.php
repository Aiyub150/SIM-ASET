@extends('layouts.app')

@section('title', 'Kartu Stok — SIM-ASET')
@section('page-title', 'Kartu Stok (Buku Besar)')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Kartu Stok Aset</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Riwayat seluruh pergerakan fisik inventaris aset daerah</p>
    </div>
    @can('stocks.adjust')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createStockMovementModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Input Mutasi Baru
    </button>
    @endcan
</div>

<div class="card">
    <div class="px-4 py-3 border-bottom bg-light">
        <form method="GET" action="{{ route('stocks.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ref/nama/SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="type" id="stock_type_filter" class="form-select form-select-sm">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk (Penambahan)</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar (Pengurangan)</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Audit)</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Waktu</th>
                    <th>No. Referensi</th>
                    <th>Barang</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Mutasi</th>
                    <th class="text-center">Saldo Awal</th>
                    <th class="text-center">Saldo Akhir</th>
                    <th>Catatan</th>
                    <th>Pencatat</th>
                    <th class="pe-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    @php $isIn = $movement->type === 'in'; @endphp
                    <tr>
                        <td class="ps-4" style="font-size:.8rem; color:#64748b;">
                            {{ $movement->created_at->userTzDate('d/m/Y') }}<br>
                            <span style="font-size:.73rem;">{{ $movement->created_at->userTzTime('H:i') }}</span>
                        </td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600;">
                                {{ $movement->reference_code }}
                            </span>
                            @if($movement->image)
                                <br>
                                <a href="{{ asset('storage/' . $movement->image) }}" target="_blank" class="badge bg-primary-subtle text-primary border text-decoration-none mt-1" style="font-size: .68rem;">
                                    <i class="fa-solid fa-paperclip me-1"></i>Bukti Ada
                                </a>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight:500;">{{ $movement->item->name }}</span><br>
                            <span style="font-family:monospace; font-size:.75rem; color:#64748b;">{{ $movement->item->sku }}</span>
                        </td>
                        <td class="text-center">
                            @if($isIn)
                                <span class="badge bg-success">Masuk</span>
                            @elseif($movement->type === 'out')
                                <span class="badge bg-secondary">Keluar</span>
                            @elseif($movement->type === 'broken')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge" style="background:#1e293b; color:#fff;">Hilang</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                            {{ $isIn ? '+' : '−' }}{{ $movement->qty }}
                        </td>
                        <td class="text-center text-muted">{{ $movement->balance_before }}</td>
                        <td class="text-center fw-bold">{{ $movement->balance_after }}</td>
                        <td style="max-width: 180px;">
                            @if(!empty($movement->notes))
                                <span class="text-truncate d-inline-block text-secondary" style="max-width: 160px; font-size: .82rem;" title="{{ $movement->notes }}">
                                    {{ $movement->notes }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: .8rem; font-style: italic;">-</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.83rem;">{{ $movement->user->name ?? '-' }}</td>
                        <td class="pe-4 text-center">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary py-1 px-2 btn-detail-movement"
                                    data-ref="{{ $movement->reference_code }}"
                                    data-item="{{ $movement->item->name }}"
                                    data-sku="{{ $movement->item->sku }}"
                                    data-type="{{ $movement->type }}"
                                    data-type-label="{{ $isIn ? 'Masuk (Penambahan)' : ($movement->type === 'out' ? 'Keluar (Pengurangan)' : ($movement->type === 'broken' ? 'Rusak' : 'Hilang')) }}"
                                    data-qty="{{ ($isIn ? '+' : '−') . $movement->qty }}"
                                    data-before="{{ $movement->balance_before }}"
                                    data-after="{{ $movement->balance_after }}"
                                    data-user="{{ $movement->user->name ?? '-' }}"
                                    data-time="{{ $movement->created_at->userTzDate('d M Y') }} {{ $movement->created_at->userTzTime('H:i') }}"
                                    data-notes="{{ $movement->notes ?? '' }}"
                                    data-image="{{ $movement->image ? asset('storage/' . $movement->image) : '' }}"
                                    data-proof-url="{{ route('stocks.proof', $movement) }}"
                                    title="Lihat Detail & Catatan Mutasi">
                                <i class="fa-solid fa-eye me-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                            Belum ada riwayat mutasi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- MODAL TAMBAH MUTASI STOK BARU (POPUP MODAL TANPA REDIRECT) --}}
@can('stocks.adjust')
<div class="modal fade" id="createStockMovementModal" tabindex="-1" aria-labelledby="createStockMovementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="createStockMovementModalLabel">Input Mutasi Stok Permanen</h5>
                    <p class="text-muted small mb-0">Catat penambahan atau pengurangan saldo fisik aset barang</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stocks.store') }}" method="POST" enctype="multipart/form-data" id="form-create-stock-movement">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2.5 px-3 d-flex align-items-start gap-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;">
                        <i class="fa-solid fa-triangle-exclamation text-warning mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Peringatan Audit:</strong> Data mutasi fisik yang tersimpan <strong>tidak dapat diedit atau dihapus</strong> untuk menjamin integritas pembukuan buku besar aset.
                        </div>
                    </div>

                    {{-- REFERENSI BAST --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            <span>Nomor Referensi (BAST)</span>
                            <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:.7rem; font-weight:500;">
                                Auto-generate
                            </span>
                        </label>
                        <input type="text" name="reference_code" id="modal_reference_code" class="form-control"
                               value="{{ $suggestedCode ?? '' }}" readonly
                               style="font-family:monospace; background:#f8fafc; color:#1e293b; font-weight:600;">
                    </div>

                    {{-- PILIH BARANG DENGAN PREVIEW THUMBNAIL --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Barang Inventaris <span class="text-danger">*</span></label>
                        <select name="item_id" id="modal_stock_item_id" class="form-select" required>
                            <option value="">— Pilih atau cari barang… —</option>
                            @if(isset($items))
                                @foreach($items as $it)
                                    <option value="{{ $it->id }}"
                                            data-name="{{ $it->name }}"
                                            data-sku="{{ $it->sku }}"
                                            data-category="{{ $it->category->name ?? 'Tanpa Kategori' }}"
                                            data-image="{{ $it->image ? asset('storage/' . $it->image) : '' }}"
                                            data-available="{{ $it->available_qty }}"
                                            data-total="{{ $it->total_qty }}">
                                        [{{ $it->sku }}] {{ $it->name }} — Fisik: {{ $it->total_qty }}, Tersedia: {{ $it->available_qty }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        {{-- KARTU PREVIEW BARANG TERPILIH --}}
                        <div id="selected_item_card" class="mt-2.5 p-2.5 border rounded-3 bg-light d-flex align-items-center gap-3" style="display: none;">
                            <img id="selected_item_image" src="" alt="Thumbnail" class="rounded border" style="width: 50px; height: 50px; object-fit: cover; background: #fff;">
                            <div>
                                <div class="fw-bold text-dark small" id="selected_item_name">-</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    SKU: <span class="font-monospace fw-semibold text-primary" id="selected_item_sku">-</span> | 
                                    Kategori: <span id="selected_item_category">-</span> | 
                                    Stok Fisik: <strong id="selected_item_total">0</strong> (Tersedia: <strong id="selected_item_available" class="text-success">0</strong>)
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Tipe Mutasi <span class="text-danger">*</span></label>
                            <select name="type" id="modal_stock_mutation_type" class="form-select" required>
                                <option value="">— Pilih Tipe Mutasi —</option>
                                <option value="in">➕ Barang Masuk / Pengadaan</option>
                                <option value="out">➖ Barang Keluar / Hibah</option>
                                <option value="broken">🔴 Barang Rusak Total</option>
                                <option value="lost">⚫ Barang Hilang</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Jumlah (Qty) <span class="text-danger">*</span></label>
                            <input type="number" name="qty" id="modal_stock_qty" class="form-control" min="1" step="1" required placeholder="Minimal 1">
                        </div>
                    </div>

                    {{-- LIVE PREVIEW UNTUK FOTO BUKTI / DOKUMEN MUTASI --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Bukti Fisik / BAST <span class="text-muted fw-normal">(Opsional)</span></label>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div style="width: 75px; height: 75px; border: 2px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 2px;">
                                    <img id="stock_proof_preview" src="" alt="Bukti" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                    <div id="stock_proof_placeholder" class="text-muted text-center" style="font-size: 0.65rem; line-height: 1.2;">
                                        <i class="fa-solid fa-file-image fa-lg d-block mb-1 text-secondary"></i>
                                        Pratinjau
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <input type="file" name="image" id="modal_stock_image_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text text-muted" style="font-size: 0.75rem;">
                                    Jika memilih barang masuk, sistem otomatis menggunakan foto barang apabila tidak ada foto bukti baru yang diunggah.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold">Catatan Mutasi <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Keterangan penyerahan, hibah, berita acara, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Mutasi Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modal Detail Mutasi Stok --}}
<div class="modal fade" id="modalDetailMovement" tabindex="-1" aria-labelledby="modalDetailMovementLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold" id="modalDetailMovementLabel">
                    <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Rincian Mutasi Stok
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <div class="text-muted small">No. Referensi</div>
                        <div class="fw-bold font-monospace text-primary fs-6" id="detail-ref">-</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Waktu Pencatatan</div>
                        <div class="fw-medium small" id="detail-time">-</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Barang Inventaris</div>
                    <div class="fw-bold fs-6" id="detail-item">-</div>
                    <div class="small text-muted font-monospace" id="detail-sku">-</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Tipe Mutasi</div>
                            <span class="badge mt-1" id="detail-badge">-</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Perubahan Qty</div>
                            <div class="fw-bold mt-1 fs-6" id="detail-qty">-</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Saldo Sebelum</div>
                            <div class="fw-bold mt-1" id="detail-before">0</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Saldo Sesudah</div>
                            <div class="fw-bold mt-1 text-primary" id="detail-after">0</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1 fw-medium">
                        <i class="fa-solid fa-clipboard-list me-1 text-secondary"></i>Catatan / Alasan Mutasi:
                    </div>
                    <div class="p-3 border rounded bg-light-subtle text-dark" style="min-height: 75px; white-space: pre-wrap; font-size: .88rem;" id="detail-notes">-</div>
                </div>

                {{-- Bukti Dokumentasi Fisik / BAST --}}
                <div class="mb-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small fw-medium">
                            <i class="fa-solid fa-camera me-1 text-secondary"></i>Bukti Fisik / Dokumen BAST:
                        </div>
                        <span id="detail-image-status" class="badge" style="font-size: .7rem;">-</span>
                    </div>

                    {{-- Kontainer Gambar Jika Ada --}}
                    <div id="detail-image-box" class="text-center p-2 border rounded bg-light mb-2" style="display:none;">
                        <a id="detail-image-link" href="#" target="_blank" title="Klik untuk memperbesar">
                            <img id="detail-image-img" src="" alt="Bukti Mutasi" class="img-fluid rounded border shadow-sm" style="max-height: 160px; object-fit: contain;">
                        </a>
                        <div class="mt-1">
                            <a id="detail-image-btn" href="#" target="_blank" class="btn btn-sm btn-link text-decoration-none py-0" style="font-size:.78rem;">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Buka Gambar Penuh
                            </a>
                        </div>
                    </div>

                    {{-- Form Unggah Bukti Susulan --}}
                    @can('stocks.adjust')
                    <div class="p-3 border rounded bg-light-subtle">
                        <div class="small fw-semibold mb-1 text-dark" id="proof-form-title">
                            <i class="fa-solid fa-upload me-1 text-primary"></i>Unggah Bukti Dokumentasi Susulan
                        </div>
                        <p class="text-muted mb-2" style="font-size:.74rem;">
                            Lupa melampirkan foto saat input mutasi? Unggah bukti di sini. Saldo stok dan mutasi fisik tetap terkunci secara permanen.
                        </p>
                        <form id="form-upload-proof" method="POST" enctype="multipart/form-data" action="">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="file" name="image" id="proof_image_file" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                                <button class="btn btn-primary px-3" type="submit">
                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>Unggah
                                </button>
                            </div>
                            <div class="form-text text-muted" style="font-size:.72rem;">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                        </form>
                    </div>
                    @endcan
                </div>

                <div class="text-muted small pt-2 border-top d-flex align-items-center justify-content-between">
                    <span>Dicatat oleh:</span>
                    <strong class="text-dark" id="detail-user">-</strong>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal detail mutasi
        const detailModalEl = document.getElementById('modalDetailMovement');
        const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;
        const detailImageBox = document.getElementById('detail-image-box');
        const detailImageImg = document.getElementById('detail-image-img');
        const detailImageLink = document.getElementById('detail-image-link');
        const detailImageBtn = document.getElementById('detail-image-btn');
        const detailImageStatus = document.getElementById('detail-image-status');
        const proofForm = document.getElementById('form-upload-proof');
        const proofFormTitle = document.getElementById('proof-form-title');

        document.querySelectorAll('.btn-detail-movement').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('detail-ref').textContent = this.dataset.ref;
                document.getElementById('detail-time').textContent = this.dataset.time;
                document.getElementById('detail-item').textContent = this.dataset.item;
                document.getElementById('detail-sku').textContent = this.dataset.sku;
                document.getElementById('detail-qty').textContent = this.dataset.qty;
                document.getElementById('detail-before').textContent = this.dataset.before;
                document.getElementById('detail-after').textContent = this.dataset.after;
                document.getElementById('detail-user').textContent = this.dataset.user;
                
                const notes = (this.dataset.notes || '').trim();
                document.getElementById('detail-notes').textContent = notes ? notes : '(Tidak ada catatan tambahan)';

                const badge = document.getElementById('detail-badge');
                badge.textContent = this.dataset.typeLabel;
                badge.className = 'badge mt-1 ' + (this.dataset.type === 'in' ? 'bg-success' : (this.dataset.type === 'out' ? 'bg-secondary' : 'bg-danger'));

                const imageUrl = (this.dataset.image || '').trim();
                const proofUrl = this.dataset.proofUrl || '';

                if (imageUrl) {
                    detailImageStatus.textContent = 'Terlampir';
                    detailImageStatus.className = 'badge bg-success-subtle text-success border';
                    detailImageImg.src = imageUrl;
                    detailImageLink.href = imageUrl;
                    detailImageBtn.href = imageUrl;
                    detailImageBox.style.display = 'block';
                    if (proofFormTitle) proofFormTitle.innerHTML = '<i class="fa-solid fa-arrows-rotate me-1 text-primary"></i>Ganti / Perbarui Foto Bukti';
                } else {
                    detailImageStatus.textContent = 'Belum Ada Bukti';
                    detailImageStatus.className = 'badge bg-warning-subtle text-warning border';
                    detailImageImg.src = '';
                    detailImageBox.style.display = 'none';
                    if (proofFormTitle) proofFormTitle.innerHTML = '<i class="fa-solid fa-upload me-1 text-primary"></i>Unggah Bukti Dokumentasi Susulan';
                }

                if (proofForm) {
                    proofForm.action = proofUrl;
                    const fileInput = document.getElementById('proof_image_file');
                    if (fileInput) fileInput.value = '';
                }

                if (detailModal) detailModal.show();
            });
        });

        if (proofForm) {
            proofForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;
                const fileInput = document.getElementById('proof_image_file');
                if (!fileInput || !fileInput.files.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Berkas',
                        text: 'Silakan pilih gambar bukti dokumentasi terlebih dahulu.'
                    });
                    return;
                }
                Swal.fire({
                    title: 'Unggah Bukti Mutasi?',
                    text: 'Foto bukti fisik / BAST akan disimpan pada transaksi ini. Saldo stok dan mutasi fisik tetap terkunci secara permanen.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Unggah Bukti',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#2563eb'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
        // --- Interaktivitas Modal Input Mutasi Stok ---
        const modalStockItemSelect = document.getElementById('modal_stock_item_id');
        const selectedItemCard     = document.getElementById('selected_item_card');
        const selectedItemImage    = document.getElementById('selected_item_image');
        const selectedItemName     = document.getElementById('selected_item_name');
        const selectedItemSku      = document.getElementById('selected_item_sku');
        const selectedItemCategory = document.getElementById('selected_item_category');
        const selectedItemTotal    = document.getElementById('selected_item_total');
        const selectedItemAvail    = document.getElementById('selected_item_available');

        if (modalStockItemSelect) {
            modalStockItemSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    const name = opt.getAttribute('data-name');
                    const sku = opt.getAttribute('data-sku');
                    const cat = opt.getAttribute('data-category');
                    const img = opt.getAttribute('data-image');
                    const tot = opt.getAttribute('data-total');
                    const avl = opt.getAttribute('data-available');

                    selectedItemName.textContent = name;
                    selectedItemSku.textContent = sku;
                    selectedItemCategory.textContent = cat;
                    selectedItemTotal.textContent = tot;
                    selectedItemAvail.textContent = avl;

                    if (img && img.trim() !== '') {
                        selectedItemImage.src = img;
                        selectedItemImage.style.display = 'block';
                    } else {
                        selectedItemImage.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="%2394a3b8"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>';
                    }
                    selectedItemCard.style.display = 'flex';
                } else {
                    selectedItemCard.style.display = 'none';
                }
            });
        }

        // Live image preview untuk upload bukti mutasi
        const modalStockImgInput = document.getElementById('modal_stock_image_input');
        const stockProofPreview  = document.getElementById('stock_proof_preview');
        const stockProofHolder   = document.getElementById('stock_proof_placeholder');
        if (modalStockImgInput && stockProofPreview) {
            modalStockImgInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        stockProofPreview.src = e.target.result;
                        stockProofPreview.style.display = 'block';
                        if (stockProofHolder) stockProofHolder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    stockProofPreview.src = '';
                    stockProofPreview.style.display = 'none';
                    if (stockProofHolder) stockProofHolder.style.display = 'block';
                }
            });
        }

        // SweetAlert2 konfirmasi submit modal mutasi
        const formCreateStock = document.getElementById('form-create-stock-movement');
        if (formCreateStock) {
            formCreateStock.addEventListener('submit', function (e) {
                e.preventDefault();
                const form = this;
                const qtyInput = document.getElementById('modal_stock_qty');
                const qtyVal = parseInt(qtyInput.value, 10);
                if (isNaN(qtyVal) || qtyVal < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
                        confirmButtonColor: '#2563eb'
                    });
                    qtyInput.focus();
                    return;
                }

                Swal.fire({
                    title: 'Simpan Mutasi Stok?',
                    text: 'Apakah Anda yakin ingin memproses transaksi mutasi stok permanen ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Proses Mutasi',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#2563eb'
                }).then((res) => {
                    if (res.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // TomSelect untuk Input Mutasi Stok
        const modalStockItem = document.getElementById('modal_stock_item_id');
        if (modalStockItem && typeof TomSelect !== 'undefined') {
            new TomSelect(modalStockItem, {
                create: false,
                plugins: ['dropdown_input'],
                placeholder: 'Cari barang berdasarkan nama atau kode SKU...',
                render: {
                    option: function(data, escape) {
                        const img = data.image ? `<img src="${escape(data.image)}" class="rounded border me-2" style="width: 32px; height: 32px; object-fit: cover;">` : `<div class="rounded border bg-light d-flex align-items-center justify-content-center me-2 text-muted" style="width: 32px; height: 32px; font-size: 0.7rem;"><i class="fa-solid fa-box"></i></div>`;
                        return `<div class="d-flex align-items-center py-1">
                            ${img}
                            <div>
                                <div class="fw-semibold text-dark small">${escape(data.name || data.text)}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    SKU: <span class="font-monospace text-primary">${escape(data.sku || '')}</span> | Tersedia: <strong class="text-success">${escape(data.available || 0)}</strong>
                                </div>
                            </div>
                        </div>`;
                    },
                    item: function(data, escape) {
                        return `<div><span class="font-monospace fw-bold text-primary">[${escape(data.sku || '')}]</span> ${escape(data.name || data.text)}</div>`;
                    }
                }
            });
        }

        const modalStockType = document.getElementById('modal_stock_mutation_type');
        if (modalStockType && typeof TomSelect !== 'undefined') {
            new TomSelect(modalStockType, {
                create: false,
                placeholder: '— Pilih Tipe Mutasi —'
            });
        }

        const stockTypeFilter = document.getElementById('stock_type_filter');
        if (stockTypeFilter && typeof TomSelect !== 'undefined') {
            let typeInit = false;
            new TomSelect(stockTypeFilter, {
                plugins: ['dropdown_input'],
                create: false,
                placeholder: 'Semua Tipe Mutasi',
                allowEmptyOption: true,
                onInitialize: function() {
                    typeInit = true;
                },
                onChange: function() {
                    if (!typeInit) return;
                    stockTypeFilter.closest('form').submit();
                }
            });
        }
    });
</script>
@endpush

@endsection
