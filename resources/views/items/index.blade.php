@extends('layouts.app')

@section('title', 'Master Data Barang - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Master Data Barang</h3>
    <a href="{{ route('items.create') }}" class="btn btn-primary fw-bold">+ Tambah Barang Baru</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">Kode (SKU)</th>
                        <th>Nama Barang</th>
                        <th width="15%" class="text-center">Total Fisik</th>
                        <th width="15%" class="text-center">Tersedia</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $items->firstItem() + $index }}</td>
                            <td class="fw-bold">{{ $item->sku }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-center fw-bold">{{ $item->total_qty }}</td>
                            <td class="text-center fw-bold text-success">{{ $item->available_qty }}</td>
                            <td class="text-center">
                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-warning fw-bold">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $items->links('pagination::bootstrap-5') }}</div>
@endsection