@extends('layouts.app')

@section('title', 'Manajemen Kategori — SIM-ASET')
@section('page-title', 'Manajemen Kategori')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-1">Manajemen Kategori Barang</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Kelola seluruh kelompok kategori barang, prefix SKU barcode, dan status keaktifan</p>
    </div>
    @can('categories.create')
    <button class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Kategori</span>
    </button>
    @endcan
</div>

{{-- Bar Filter & Pencarian Kategori --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Cari nama kategori, prefix SKU, atau deskripsi..." 
                           value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-8 col-md-3">
                <select name="status" id="category_status_filter" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Inaktif</option>
                </select>
            </div>
            <div class="col-4 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Cari
                </button>
                @if(!empty($search) || !empty($status))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 px-4 fw-bold border-bottom d-flex justify-content-between align-items-center">
        <span>Daftar Kategori Barang</span>
        <span class="badge bg-light text-dark border font-monospace">{{ $categories->total() }} Kategori</span>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nama Kategori</th>
                    <th>Prefix SKU</th>
                    <th>Total Barang</th>
                    <th>Status</th>
                    @if(auth()->user()->can('categories.edit') || auth()->user()->can('categories.toggle'))
                    <th class="pe-4 text-end">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $category->name }}</div>
                        <div class="small text-muted">{{ Str::limit($category->description, 60) ?: 'Tidak ada deskripsi.' }}</div>
                    </td>
                    <td>
                        @if($category->sku_prefix)
                            <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2.5 py-1">{{ $category->sku_prefix }}</span>
                        @else
                            <span class="text-muted fst-italic small">Tidak ada</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $category->items_count }} Barang</span>
                    </td>
                    <td>
                        @if($category->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                <span class="d-inline-block rounded-circle me-1" style="width: 6px; height: 6px; background-color: #16a34a;"></span>
                                Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1">
                                Inaktif
                            </span>
                        @endif
                    </td>
                    @if(auth()->user()->can('categories.edit') || auth()->user()->can('categories.toggle'))
                    <td class="pe-4 text-end">
                        @can('categories.edit')
                        <button class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}" title="Edit Kategori">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                        </button>
                        @endcan
                        @can('categories.toggle')
                        <form action="{{ route('categories.toggle', $category->id) }}" method="POST" class="d-inline" data-confirm="Apakah Anda yakin ingin {{ $category->is_active ? 'menonaktifkan' : 'mengaktifkan' }} kategori {{ $category->name }}?">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm rounded-3 {{ $category->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $category->is_active ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}">
                                <i class="fa-solid {{ $category->is_active ? 'fa-ban' : 'fa-check' }} me-1"></i>
                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @endcan
                    </td>
                    @endif
                </tr>

                {{-- Edit Modal --}}
                @can('categories.edit')
                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered text-start">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <form action="{{ route('categories.update', $category->id) }}" 
                                  method="POST"
                                  data-confirm="Apakah Anda yakin ingin menyimpan perubahan data kategori ini?"
                                  data-confirm-title="Simpan Perubahan Kategori?">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-bold">Edit Kategori: {{ $category->name }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control rounded-3" value="{{ $category->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Prefix SKU</label>
                                        <input type="text" name="sku_prefix" class="form-control rounded-3 font-monospace" value="{{ $category->sku_prefix }}" placeholder="Misal: ELK">
                                        <div class="form-text small">Maksimal 10 karakter. Digunakan untuk format kode barcode.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Deskripsi</label>
                                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Keterangan singkat kelompok kategori...">{{ $category->description }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endcan
                @empty
                <tr>
                    <td colspan="{{ (auth()->user()->can('categories.edit') || auth()->user()->can('categories.toggle')) ? 5 : 4 }}" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-tags fs-2 opacity-25 mb-2 d-block"></i>
                        @if(!empty($search) || !empty($status))
                            Tidak ada kategori yang cocok dengan filter pencarian.
                        @else
                            Belum ada data kategori terdaftar.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white py-3 px-4 border-top">
        {{ $categories->links() }}
    </div>
    @endif
</div>

{{-- Create Modal --}}
@can('categories.create')
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered text-start">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('categories.store') }}" 
                  method="POST"
                  data-confirm="Tambahkan kategori barang baru ini ke dalam katalog?"
                  data-confirm-title="Tambah Kategori Baru?">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Tambah Kategori Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required placeholder="Misal: Elektronik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Prefix SKU</label>
                        <input type="text" name="sku_prefix" class="form-control rounded-3 font-monospace" placeholder="Misal: ELK">
                        <div class="form-text small">Maksimal 10 karakter. Digunakan untuk format kode barcode.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Keterangan singkat kategori..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('category_status_filter');
        if (statusFilter && typeof TomSelect !== 'undefined') {
            let statusInit = false;
            new TomSelect(statusFilter, {
                create: false,
                controlInput: null,
                placeholder: 'Semua Status',
                allowEmptyOption: true,
                onInitialize: function() {
                    statusInit = true;
                },
                onChange: function() {
                    if (!statusInit) return;
                    statusFilter.closest('form').submit();
                }
            });
        }
    });
</script>
@endpush

@endsection
