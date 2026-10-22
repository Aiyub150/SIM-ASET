@extends('layouts.app')

@section('title', 'Master Data Barang — SIM-ASET')
@section('page-title', 'Master Data Barang')

@push('styles')
<style>
    /* Modern Ownership Selector Cards */
    .ownership-card {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
        transition: all 0.2s ease-in-out;
        background-color: #ffffff;
        position: relative;
    }
    .ownership-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .ownership-card.active-internal {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .ownership-card.active-vendor {
        border-color: #7c3aed !important;
        background-color: #f5f3ff !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    }
    .ownership-card.active-external {
        border-color: #d97706 !important;
        background-color: #fffbeb !important;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    }

    /* Live Image Preview Styles */
    .preview-box {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        background-color: #f8fafc;
        transition: border-color 0.2s;
    }
    .preview-box:hover {
        border-color: #94a3b8;
    }
    .preview-box img {
        max-height: 140px;
        max-width: 100%;
        border-radius: 6px;
        object-fit: contain;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Barang</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar seluruh inventaris aset yang terdaftar</p>
    </div>
    @can('items.create')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createItemModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Barang
    </button>
    @endcan
</div>

<div class="card">
    <div class="px-4 py-3 border-bottom bg-light">
        <form method="GET" action="{{ route('items.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, SKU, vendor..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" id="item_category_filter" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="ownership_status" id="item_ownership_filter" class="form-select form-select-sm">
                    <option value="">Semua Kepemilikan</option>
                    <option value="internal" {{ request('ownership_status') == 'internal' ? 'selected' : '' }}>Milik Sendiri</option>
                    <option value="vendor" {{ request('ownership_status') == 'vendor' ? 'selected' : '' }}>Milik Vendor</option>
                    <option value="external" {{ request('ownership_status') == 'external' ? 'selected' : '' }}>Milik Instansi Lain</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    @hasanyrole('Super Admin|Admin')
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom gap-3 flex-wrap">
        <label class="form-check mb-0 d-flex align-items-center gap-2">
            <input type="checkbox" id="select-all-items" class="form-check-input">
            <span>Pilih semua</span>
        </label>
        <div>
            <button type="button" id="bulk-label-button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2 2h2v2H2z"/><path d="M6 0v6H0V0zM1 1v4h4V1zm4 7H0v6h6V8zm-5 1h4v4H1zm11-9h-2v2h2zm4 0h-6v6h6zm-5 1h4v4h-4zm-1 9h-2v2h2zm4 0h-2v2h2zm-2 2h-2v2h2zm4 0h-2v2h2zm-2 2h-2v2h2zm-4-4h-2v2h2zm2-2h-2v2h2zm-2-2h-2v2h2z"/>
                </svg>
                Cetak Label Terpilih
            </button>
        </div>
    </div>
    @endhasanyrole
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    @hasanyrole('Super Admin|Admin')
                    <th class="ps-4" style="width:5%;"><input type="checkbox" id="select-all-header" class="form-check-input" aria-label="Pilih semua barang"></th>
                    @endhasanyrole
                    <th style="width:5%;">No</th>
                    <th style="width:14%;">Kode (SKU)</th>
                    <th style="width:14%;">Foto</th>
                    <th>Nama Barang</th>
                    <th style="width:13%;">Kategori</th>
                    <th class="text-center" style="width:10%;">Total Fisik</th>
                    <th class="text-center" style="width:10%;">Tersedia</th>
                    @if(auth()->user()?->hasAnyRole(['Super Admin', 'Admin']) || auth()->user()?->can('items.edit'))
                    <th class="text-center pe-4" style="width:15%;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php $utilization = $item->total_qty > 0 ? round((($item->total_qty - $item->available_qty) / $item->total_qty) * 100) : 0; @endphp
                    <tr>
                        @hasanyrole('Super Admin|Admin')
                        <td class="ps-4">
                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" aria-label="Pilih {{ $item->name }}">
                        </td>
                        @endhasanyrole
                        <td class="text-muted">{{ $items->firstItem() + $index }}</td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600; color:#2563eb;">
                                {{ $item->sku }}
                            </span>
                        </td>
                        <td>
                            @if($item->image)
                                <button type="button" 
                                        class="btn p-0 border-0 bg-transparent btn-preview-image position-relative" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#imagePreviewModal"
                                        data-item-name="{{ $item->name }}"
                                        data-item-sku="{{ $item->sku }}"
                                        data-image-src="{{ asset('storage/' . $item->image) }}"
                                        title="Klik untuk memperbesar gambar">
                                    <img src="{{ asset('storage/' . $item->image) }}" 
                                         alt="{{ $item->name }}" 
                                         style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; transition: transform 0.15s ease, box-shadow 0.15s ease;"
                                         class="item-thumbnail shadow-xs">
                                    <span class="position-absolute bottom-0 end-0 bg-dark bg-opacity-75 text-white p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                          style="width: 16px; height: 16px; font-size: 9px;" title="Perbesar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" 
                                     style="width: 44px; height: 44px; border-radius: 8px; border: 1px dashed #cbd5e1; font-size: 0.68rem; text-align: center; line-height: 1.1;"
                                     title="Belum ada foto">
                                    No Pic
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600; color:#1e293b;">{{ $item->name }}</div>
                            @if(($item->ownership_status ?? 'internal') === 'vendor')
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                                        <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h1a.5.5 0 0 1 0 1H0a.5.5 0 0 1 0-1h1V9a.5.5 0 0 1 .5-.5m2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5"/>
                                    </svg>Vendor: {{ $item->owner_name ?? 'Pihak Ketiga' }}
                                </span>
                            @elseif($item->ownership_status === 'external')
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                                        <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                        <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1z"/>
                                    </svg>Instansi: {{ $item->owner_name ?? 'Instansi Lain' }}
                                </span>
                            @else
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">
                                    Milik Sendiri
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:500; font-size:.75rem;">
                                {{ $item->category?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $item->total_qty }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold {{ $item->available_qty == 0 ? 'text-danger' : 'text-success' }}">
                                {{ $item->available_qty }}
                            </span>
                            @if($item->total_qty > 0)
                                <div class="progress mt-1" style="height:3px; width:60px; margin:0 auto;">
                                    <div class="progress-bar {{ $utilization > 80 ? 'bg-danger' : 'bg-success' }}"
                                         style="width:{{ $utilization }}%;"></div>
                                </div>
                            @endif
                        </td>
                        @if(auth()->user()?->hasAnyRole(['Super Admin', 'Admin']) || auth()->user()?->can('items.edit'))
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 btn-edit-item"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editItemModal"
                                        data-item-id="{{ $item->id }}"
                                        data-item-name="{{ $item->name }}"
                                        data-item-sku="{{ $item->sku }}"
                                        data-item-category-id="{{ $item->category_id }}"
                                        data-item-ownership="{{ $item->ownership_status }}"
                                        data-item-owner-name="{{ $item->owner_name }}"
                                        data-item-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}"
                                        title="Edit Data Barang">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                    Edit
                                </button>
                                <a href="{{ route('items.label', ['item' => $item->id, 'type' => '2d']) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Cetak Label Barcode Kotak">
                                    Label
                                </a>
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.922l6.5 2.6z"/>
                            </svg>
                            Belum ada data barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- MODAL PREVIEW FOTO BARANG (POPUP JELAS) --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="imagePreviewModalLabel">-</h5>
                    <span class="badge bg-white text-primary border font-monospace mt-1" id="imagePreviewModalSku">-</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 bg-white">
                <div class="d-flex align-items-center justify-content-center p-2 rounded" style="background: #f8fafc; min-height: 280px; border: 1px dashed #e2e8f0;">
                    <img id="imagePreviewModalImg" src="" alt="Pratinjau Foto Barang" class="img-fluid rounded shadow-sm" style="max-height: 65vh; max-width: 100%; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-between">
                <span class="text-muted small">Klik di luar area modal atau tombol Tutup untuk kembali.</span>
                <button type="button" class="btn btn-secondary btn-sm px-3 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH BARANG BARU (POPUP CREATE) --}}
@can('items.create')
<div class="modal fade" id="createItemModal" tabindex="-1" aria-labelledby="createItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createItemModalLabel">Tambah Barang Inventaris Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" id="form-create-item">
                @csrf
                <div class="modal-body p-4">
                    {{-- STATUS KEPEMILIKAN ASET --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kepemilikan Aset <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="ownership-card active-internal d-flex flex-row align-items-center gap-2.5 h-100" id="label-create-internal">
                                    <input type="radio" name="ownership_status" value="internal" class="form-check-input mt-0 create-ownership-radio" checked style="accent-color: #2563eb;">
                                    <div>
                                        <div class="fw-bold text-primary small d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-building-columns"></i> Milik Sendiri
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">SKU Otomatis Sistem</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="ownership-card d-flex flex-row align-items-center gap-2.5 h-100" id="label-create-vendor">
                                    <input type="radio" name="ownership_status" value="vendor" class="form-check-input mt-0 create-ownership-radio" style="accent-color: #7c3aed;">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1" id="text-create-vendor">
                                            <i class="fa-solid fa-truck-ramp-box"></i> Milik Vendor
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">SKU/Barcode Asli</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="ownership-card d-flex flex-row align-items-center gap-2.5 h-100" id="label-create-external">
                                    <input type="radio" name="ownership_status" value="external" class="form-check-input mt-0 create-ownership-radio" style="accent-color: #d97706;">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1" id="text-create-external">
                                            <i class="fa-solid fa-handshake-angle"></i> Instansi Lain
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">SKU Titipan Asli</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- NAMA PEMILIK / VENDOR (KONDISIONAL) --}}
                    <div class="mb-3" id="create_owner_field_group" style="display: none;">
                        <label class="form-label fw-semibold" id="create_owner_label">Nama Pemilik / Vendor <span class="text-danger">*</span></label>
                        <input type="text" name="owner_name" id="create_owner_name" class="form-control" placeholder="Contoh: PT Vendor Nusantara / Dinas Kesehatan">
                    </div>

                    <div class="row g-3">
                        {{-- KATEGORI --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="create_category_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categoriesWithSku as $cat)
                                    <option value="{{ $cat->id }}" 
                                            data-prefix="{{ $cat->sku_prefix }}" 
                                            data-next="{{ str_pad($cat->next_sku_number, 3, '0', STR_PAD_LEFT) }}">
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- KODE BARANG (SKU) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Kode Barang (SKU)</span>
                                <span id="create_sku_badge" class="badge" style="background:#eff6ff; color:#2563eb; font-size:.7rem; font-weight:500;">
                                    Auto-generate
                                </span>
                            </label>
                            <input type="text" name="sku" id="create_sku_preview" class="form-control" placeholder="Pilih kategori terlebih dahulu" readonly style="font-family:monospace; background:#f8fafc; color:#64748b;">
                            <div class="form-text text-muted" id="create_sku_helper" style="font-size:0.75rem;">
                                SKU otomatis. Contoh: <code>PREFIX-001</code>.
                            </div>
                        </div>
                    </div>

                    {{-- NAMA BARANG --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kursi Susun Chitose" required>
                    </div>

                    {{-- FOTO BARANG DENGAN LIVE PREVIEW --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Foto Barang</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="preview-box" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; padding: 2px;">
                                    <img id="create_image_preview" src="" alt="Pratinjau" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                    <div id="create_image_placeholder" class="text-muted text-center" style="font-size: 0.65rem; line-height: 1.2;">
                                        <i class="fa-solid fa-camera fa-lg d-block mb-1 text-secondary"></i>
                                        Pratinjau
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <input type="file" name="image" id="create_item_image_input" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text text-muted" style="font-size:0.75rem;">
                                    Format: JPG, PNG, WEBP (maks 2MB). Foto ini otomatis tersimpan ke bukti fisik mutasi stok awal.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- JUMLAH BARANG (TOTAL FISIK AWAL) --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">
                            Jumlah Barang <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="total_qty" id="create_total_qty" class="form-control" value="1" min="1" step="1" placeholder="Contoh: 10" required>
                        <div class="form-text text-muted" style="font-size:0.75rem;">
                            Jumlah fisik barang yang didaftarkan (minimal 1 unit).
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-submit-create-item" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- MODAL EDIT DATA BARANG (POPUP EDIT - TANPA TOTAL FISIK DEMI INTEGRITAS AUDIT) --}}
@if(auth()->user()?->hasAnyRole(['Super Admin', 'Admin']) || auth()->user()?->can('items.edit'))
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="editItemModalLabel">Edit Data Barang</h5>
                    <span class="badge bg-white text-primary border font-monospace mt-1" id="edit_modal_sku_badge">-</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="form-edit-item">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    {{-- STATUS KEPEMILIKAN ASET --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kepemilikan Aset <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="ownership-card d-flex flex-row align-items-center gap-2.5 h-100" id="label-edit-internal">
                                    <input type="radio" name="ownership_status" value="internal" class="form-check-input mt-0 edit-ownership-radio" style="accent-color: #2563eb;">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1" id="text-edit-internal">
                                            <i class="fa-solid fa-building-columns"></i> Milik Sendiri
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">Aset Internal</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="ownership-card d-flex flex-row align-items-center gap-2.5 h-100" id="label-edit-vendor">
                                    <input type="radio" name="ownership_status" value="vendor" class="form-check-input mt-0 edit-ownership-radio" style="accent-color: #7c3aed;">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1" id="text-edit-vendor">
                                            <i class="fa-solid fa-truck-ramp-box"></i> Milik Vendor
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">Sewa / Pihak Ke-3</div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="ownership-card d-flex flex-row align-items-center gap-2.5 h-100" id="label-edit-external">
                                    <input type="radio" name="ownership_status" value="external" class="form-check-input mt-0 edit-ownership-radio" style="accent-color: #d97706;">
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-1" id="text-edit-external">
                                            <i class="fa-solid fa-handshake-angle"></i> Instansi Lain
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">Titipan Pemerintah</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- NAMA PEMILIK / VENDOR (KONDISIONAL) --}}
                    <div class="mb-3" id="edit_owner_field_group" style="display: none;">
                        <label class="form-label fw-semibold" id="edit_owner_label">Nama Pemilik / Vendor <span class="text-danger">*</span></label>
                        <input type="text" name="owner_name" id="edit_owner_name" class="form-control" placeholder="Contoh: PT Vendor Nusantara">
                    </div>

                    <div class="row g-3">
                        {{-- KATEGORI --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- KODE BARANG (SKU - READONLY DI FORM EDIT) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Barang (SKU)</label>
                            <input type="text" id="edit_sku_preview" class="form-control" readonly style="font-family:monospace; background:#f8fafc; color:#64748b;">
                            <div class="form-text text-muted" style="font-size:0.75rem;">
                                Kode SKU terkunci permanen untuk konsistensi transaksi dan audit.
                            </div>
                        </div>
                    </div>

                    {{-- NAMA BARANG --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_item_name" class="form-control" required>
                    </div>

                    {{-- FOTO BARANG DENGAN LIVE PREVIEW --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Foto Barang</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="preview-box" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; padding: 2px;">
                                    <img id="edit_image_preview" src="" alt="Foto Barang" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                    <div id="edit_image_placeholder" class="text-muted text-center" style="font-size: 0.65rem; line-height: 1.2;">
                                        <i class="fa-solid fa-image fa-lg d-block mb-1 text-secondary"></i>
                                        Belum Ada
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <input type="file" name="image" id="edit_item_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text text-muted" style="font-size:0.75rem;">
                                    Format: JPG, PNG, WEBP (maks 2MB). Pilih file baru untuk mengganti gambar.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KETERANGAN TRANSPARANSI STOK FISIK --}}
                    <div class="alert alert-info py-2 px-3 mt-3 d-flex align-items-center gap-2 mb-0" style="font-size: 0.8rem; border-radius: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="flex-shrink-0" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <div>
                            <strong>Integritas Audit:</strong> Jumlah stok fisik tidak dapat diubah dari form ini. Segala penambahan atau pengurangan saldo fisik wajib melalui menu <a href="{{ route('stocks.create') }}" class="text-decoration-none fw-bold">Kartu Stok</a>.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllHeader = document.getElementById('select-all-header');
        const selectAllLabel = document.getElementById('select-all-items');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkButton = document.getElementById('bulk-label-button');

        const syncSelectAll = () => {
            const allChecked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(cb => cb.checked);
            selectAllHeader.checked = allChecked;
            selectAllLabel.checked = allChecked;
        };

        const toggleAll = (checked) => {
            checkboxes.forEach(cb => cb.checked = checked);
            syncSelectAll();
        };

        selectAllHeader?.addEventListener('change', (e) => toggleAll(e.target.checked));
        selectAllLabel?.addEventListener('change', (e) => toggleAll(e.target.checked));
        checkboxes.forEach(cb => cb.addEventListener('change', syncSelectAll));

        bulkButton?.addEventListener('click', function () {
            const selected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!selected.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih minimal satu barang terlebih dahulu.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            const url = new URL('{{ route('items.labels') }}', window.location.origin);
            url.searchParams.set('type', '2d');
            selected.forEach(id => url.searchParams.append('items[]', id));
            window.open(url.toString(), '_blank');
        });

        const categoryFilter = document.getElementById('item_category_filter');
        if (categoryFilter && typeof TomSelect !== 'undefined') {
            let catInit = false;
            new TomSelect(categoryFilter, {
                create: false,
                placeholder: 'Semua Kategori',
                allowEmptyOption: true,
                onInitialize: function() {
                    catInit = true;
                },
                onChange: function() {
                    if (!catInit) return;
                    categoryFilter.closest('form').submit();
                }
            });
        }

        const ownershipFilter = document.getElementById('item_ownership_filter');
        if (ownershipFilter && typeof TomSelect !== 'undefined') {
            let ownInit = false;
            new TomSelect(ownershipFilter, {
                create: false,
                placeholder: 'Semua Kepemilikan',
                allowEmptyOption: true,
                onInitialize: function() {
                    ownInit = true;
                },
                onChange: function() {
                    if (!ownInit) return;
                    ownershipFilter.closest('form').submit();
                }
            });
        }

        // Handler Modal Preview Gambar Jelas
        const previewModal = document.getElementById('imagePreviewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                const name = trigger.getAttribute('data-item-name') || '-';
                const sku = trigger.getAttribute('data-item-sku') || '-';
                const src = trigger.getAttribute('data-image-src') || '';

                document.getElementById('imagePreviewModalLabel').textContent = name;
                document.getElementById('imagePreviewModalSku').textContent = sku;
                document.getElementById('imagePreviewModalImg').src = src;
            });
        }

        // --- LOGIKA MODAL TAMBAH BARANG (CREATE) ---
        const createCategorySelect = document.getElementById('create_category_id');
        const createSkuPreview     = document.getElementById('create_sku_preview');
        const createSkuBadge       = document.getElementById('create_sku_badge');
        const createSkuHelper      = document.getElementById('create_sku_helper');
        const createOwnerGroup     = document.getElementById('create_owner_field_group');
        const createOwnerLabel     = document.getElementById('create_owner_label');
        const createOwnerInput     = document.getElementById('create_owner_name');

        // Interaktivitas Visual Card Selector untuk Status Kepemilikan (Create)
        function refreshOwnershipCardClasses(prefix) {
            const radios = document.querySelectorAll(`.${prefix}-ownership-radio`);
            radios.forEach(radio => {
                const label = document.getElementById(`label-${prefix}-${radio.value}`);
                const textElem = document.getElementById(`text-${prefix}-${radio.value}`);
                if (!label) return;

                label.classList.remove('active-internal', 'active-vendor', 'active-external');
                if (textElem) {
                    textElem.classList.remove('text-primary', 'text-purple', 'text-warning');
                    textElem.classList.add('text-dark');
                }

                if (radio.checked) {
                    label.classList.add(`active-${radio.value}`);
                    if (textElem) {
                        textElem.classList.remove('text-dark');
                        if (radio.value === 'internal') textElem.classList.add('text-primary');
                        if (radio.value === 'vendor') textElem.classList.add('text-purple');
                        if (radio.value === 'external') textElem.classList.add('text-warning');
                    }
                }
            });
        }

        // Live Image Preview Universal Handler
        function setupLivePreview(fileInputId, imgPreviewId, placeholderId) {
            const input = document.getElementById(fileInputId);
            const preview = document.getElementById(imgPreviewId);
            const placeholder = document.getElementById(placeholderId);

            if (!input || !preview) return;

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (preview.getAttribute('data-original-src')) {
                        preview.src = preview.getAttribute('data-original-src');
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    } else {
                        preview.src = '';
                        preview.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'block';
                    }
                }
            });
        }

        setupLivePreview('create_item_image_input', 'create_image_preview', 'create_image_placeholder');
        setupLivePreview('edit_item_image', 'edit_image_preview', 'edit_image_placeholder');

        function updateCreateSku() {
            refreshOwnershipCardClasses('create');
            const selectedRadio = document.querySelector('input[name="ownership_status"].create-ownership-radio:checked');
            const status = selectedRadio ? selectedRadio.value : 'internal';

            if (status === 'internal') {
                createOwnerGroup.style.display = 'none';
                createOwnerInput.required = false;

                createSkuPreview.readOnly = true;
                createSkuPreview.style.background = '#f8fafc';
                createSkuBadge.textContent = 'Auto-generate';
                createSkuBadge.className = 'badge bg-primary-subtle text-primary';

                const opt = createCategorySelect.options[createCategorySelect.selectedIndex];
                if (opt && opt.value) {
                    const prefix = opt.getAttribute('data-prefix');
                    const nextNum = opt.getAttribute('data-next');
                    createSkuPreview.value = prefix + '-' + nextNum;
                    createSkuHelper.innerHTML = 'SKU otomatis: <code>' + prefix + '-' + nextNum + '</code>';
                } else {
                    createSkuPreview.value = '';
                    createSkuHelper.textContent = 'Pilih kategori untuk melihat pratinjau SKU.';
                }
            } else if (status === 'vendor') {
                createOwnerGroup.style.display = 'block';
                createOwnerLabel.innerHTML = 'Nama Vendor / Perusahaan Pemilik <span class="text-danger">*</span>';
                createOwnerInput.placeholder = 'Contoh: PT Vendor Nusantara / CV Rental Jaya';
                createOwnerInput.required = true;

                createSkuPreview.readOnly = false;
                createSkuPreview.style.background = '#ffffff';
                createSkuBadge.textContent = 'SKU / Barcode Asli Vendor';
                createSkuBadge.className = 'badge bg-purple-subtle text-purple';
                createSkuHelper.innerHTML = '<span class="text-dark fw-semibold">Wajib input SKU/Barcode asli:</span> Barang sewa tidak menggunakan SKU otomatis.';
            } else if (status === 'external') {
                createOwnerGroup.style.display = 'block';
                createOwnerLabel.innerHTML = 'Nama Instansi Pemilik Aset <span class="text-danger">*</span>';
                createOwnerInput.placeholder = 'Contoh: Dinas Kesehatan / Bappeda';
                createOwnerInput.required = true;

                createSkuPreview.readOnly = false;
                createSkuPreview.style.background = '#ffffff';
                createSkuBadge.textContent = 'SKU / Kode Registrasi Instansi';
                createSkuBadge.className = 'badge bg-warning-subtle text-warning';
                createSkuHelper.innerHTML = '<span class="text-dark fw-semibold">Wajib input Kode Registrasi:</span> Barang titipan instansi menggunakan kode aset resminya.';
            }
        }

        document.querySelectorAll('.create-ownership-radio').forEach(r => r.addEventListener('change', updateCreateSku));
        createCategorySelect?.addEventListener('change', updateCreateSku);

        // Pencegatan SweetAlert2 untuk form create (total_qty minimal 1)
        const formCreateItem = document.getElementById('form-create-item');
        const inputCreateQty = document.getElementById('create_total_qty');
        if (formCreateItem && inputCreateQty) {
            formCreateItem.addEventListener('submit', function (e) {
                const qtyVal = parseInt(inputCreateQty.value, 10);
                if (isNaN(qtyVal) || qtyVal < 1) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
                        confirmButtonColor: '#2563eb'
                    });
                    inputCreateQty.focus();
                }
            });
        }

        // --- LOGIKA MODAL EDIT BARANG ---
        const editModal = document.getElementById('editItemModal');
        if (editModal) {
            const formEdit       = document.getElementById('form-edit-item');
            const editName       = document.getElementById('edit_item_name');
            const editSkuPreview = document.getElementById('edit_sku_preview');
            const editSkuBadge   = document.getElementById('edit_modal_sku_badge');
            const editCategory   = document.getElementById('edit_category_id');
            const editOwnerGroup = document.getElementById('edit_owner_field_group');
            const editOwnerLabel = document.getElementById('edit_owner_label');
            const editOwnerInput = document.getElementById('edit_owner_name');
            const editImgContainer = document.getElementById('edit_current_image_container');
            const editImgPreview   = document.getElementById('edit_current_image_preview');

            function syncEditOwnershipUI(status) {
                refreshOwnershipCardClasses('edit');
                if (status === 'internal') {
                    editOwnerGroup.style.display = 'none';
                    editOwnerInput.required = false;
                } else if (status === 'vendor') {
                    editOwnerGroup.style.display = 'block';
                    editOwnerLabel.innerHTML = 'Nama Vendor / Perusahaan Pemilik <span class="text-danger">*</span>';
                    editOwnerInput.placeholder = 'Contoh: PT Vendor Nusantara / CV Rental Jaya';
                    editOwnerInput.required = true;
                } else if (status === 'external') {
                    editOwnerGroup.style.display = 'block';
                    editOwnerLabel.innerHTML = 'Nama Instansi Pemilik Aset <span class="text-danger">*</span>';
                    editOwnerInput.placeholder = 'Contoh: Dinas Kesehatan / Bappeda';
                    editOwnerInput.required = true;
                }
            }

            document.querySelectorAll('.edit-ownership-radio').forEach(r => {
                r.addEventListener('change', function () {
                    syncEditOwnershipUI(this.value);
                });
            });

            editModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const itemId     = btn.getAttribute('data-item-id');
                const itemName   = btn.getAttribute('data-item-name');
                const itemSku    = btn.getAttribute('data-item-sku');
                const categoryId = btn.getAttribute('data-item-category-id');
                const ownership  = btn.getAttribute('data-item-ownership') || 'internal';
                const ownerName  = btn.getAttribute('data-item-owner-name') || '';
                const itemImage  = btn.getAttribute('data-item-image') || '';

                formEdit.action = `/items/${itemId}`;
                editName.value = itemName;
                editSkuPreview.value = itemSku;
                editSkuBadge.textContent = 'SKU: ' + itemSku;
                editCategory.value = categoryId;
                editOwnerInput.value = ownerName;

                // Set radio ownership
                document.querySelectorAll('.edit-ownership-radio').forEach(r => {
                    r.checked = (r.value === ownership);
                });
                syncEditOwnershipUI(ownership);

                // Set preview foto
                const editImgInput = document.getElementById('edit_item_image');
                const editPreview = document.getElementById('edit_image_preview');
                const editPlaceholder = document.getElementById('edit_image_placeholder');
                editImgInput.value = '';

                if (itemImage && itemImage.trim() !== '') {
                    editPreview.src = itemImage;
                    editPreview.setAttribute('data-original-src', itemImage);
                    editPreview.style.display = 'block';
                    if (editPlaceholder) editPlaceholder.style.display = 'none';
                } else {
                    editPreview.src = '';
                    editPreview.removeAttribute('data-original-src');
                    editPreview.style.display = 'none';
                    if (editPlaceholder) editPlaceholder.style.display = 'block';
                }
            });
        }
    });
</script>

@endsection
