@extends('layouts.app')

@section('title', 'Master Data Instansi - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Master Data Instansi / Peminjam</h3>
    <a href="{{ route('borrowers.create') }}" class="btn btn-primary fw-bold">+ Tambah Instansi</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Instansi / Organisasi</th>
                        <th>Penanggung Jawab (PIC)</th>
                        <th>Nomor Kontak</th>
                        <th>Alamat</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowers as $index => $borrower)
                        <tr>
                            <td class="text-center">{{ $borrowers->firstItem() + $index }}</td>
                            <td class="fw-bold">{{ $borrower->institution_name }}</td>
                            <td>{{ $borrower->pic_name }}</td>
                            <td>{{ $borrower->contact_number }}</td>
                            <td class="text-muted">{{ $borrower->address ?: '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('borrowers.edit', $borrower->id) }}" class="btn btn-sm btn-warning fw-bold">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data instansi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $borrowers->links('pagination::bootstrap-5') }}
</div>
@endsection