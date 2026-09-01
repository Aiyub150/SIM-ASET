@extends('layouts.app')

@section('title', 'Edit Instansi - SIM ASET')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Edit Data Instansi</h3>
    <a href="{{ route('borrowers.index') }}" class="btn btn-outline-secondary fw-bold">← Batal & Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                Pembaruan Informasi Peminjam
            </div>
            <div class="card-body p-4">
                <form action="{{ route('borrowers.update', $borrower->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Instansi / Organisasi</label>
                        <input type="text" name="institution_name" class="form-control @error('institution_name') is-invalid @enderror" value="{{ old('institution_name', $borrower->institution_name) }}" required>
                        @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Penanggung Jawab (PIC)</label>
                            <input type="text" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror" value="{{ old('pic_name', $borrower->pic_name) }}" required>
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Kontak (HP/Telp)</label>
                            <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $borrower->contact_number) }}" required>
                            @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat Lengkap (Opsional)</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $borrower->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Simpan Perubahan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection