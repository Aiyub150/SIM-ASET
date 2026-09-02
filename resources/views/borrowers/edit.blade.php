@extends('layouts.app')

@section('title', 'Edit Instansi — SIM-ASET')
@section('page-title', 'Edit Data Instansi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Edit Data Instansi</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Perbarui informasi instansi: <strong>{{ $borrower->institution_name }}</strong></p>
    </div>
    <a href="{{ route('borrowers.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">Pembaruan Informasi Instansi</div>
            <div class="card-body p-4">
                <form action="{{ route('borrowers.update', $borrower->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Instansi / Organisasi</label>
                        <input type="text" name="institution_name"
                               class="form-control @error('institution_name') is-invalid @enderror"
                               value="{{ old('institution_name', $borrower->institution_name) }}" required>
                        @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Penanggung Jawab (PIC)</label>
                            <input type="text" name="pic_name"
                                   class="form-control @error('pic_name') is-invalid @enderror"
                                   value="{{ old('pic_name', $borrower->pic_name) }}" required>
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Kontak (HP/Telp)</label>
                            <input type="text" name="contact_number"
                                   class="form-control @error('contact_number') is-invalid @enderror"
                                   value="{{ old('contact_number', $borrower->contact_number) }}" required
                                   style="font-family:monospace;">
                            @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Alamat <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
                        <textarea name="address" rows="3"
                                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $borrower->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
