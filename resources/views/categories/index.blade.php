@extends('layouts.app')

@section('title', 'Manajemen Kategori — SIM-ASET')
@section('page-title', 'Manajemen Kategori')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card mb-4">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
        <span>Daftar Kategori Barang</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            + Tambah Kategori
        </button>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Nama Kategori</th>
                    <th>Prefix SKU</th>
                    <th>Total Barang</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $category->name }}</div>
                        <div class="small text-muted">{{ Str::limit($category->description, 50) }}</div>
                    </td>
                    <td>
                        @if($category->sku_prefix)
                            <span class="badge bg-secondary">{{ $category->sku_prefix }}</span>
                        @else
                            <span class="text-muted fst-italic">Tidak ada</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $category->items_count }} Barang</span>
                    </td>
                    <td>
                        @if($category->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Inaktif</span>
                        @endif
                    </td>
                    <td class="pe-4 text-end">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                            Edit
                        </button>
                        <form action="{{ route('categories.toggle', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" onclick="return confirm('Apakah Anda yakin ingin {{ $category->is_active ? 'menonaktifkan' : 'mengaktifkan' }} kategori ini?')">
                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Kategori: {{ $category->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Prefix SKU</label>
                                        <input type="text" name="sku_prefix" class="form-control" value="{{ $category->sku_prefix }}" placeholder="Misal: ELK">
                                        <div class="form-text">Maksimal 10 karakter.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Misal: Elektronik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prefix SKU</label>
                        <input type="text" name="sku_prefix" class="form-control" placeholder="Misal: ELK">
                        <div class="form-text">Maksimal 10 karakter. Digunakan untuk format kode barcode.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
