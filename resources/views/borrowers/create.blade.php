@extends('layouts.app')

@section('title', 'Tambah Instansi — SIM-ASET')
@section('page-title', 'Tambah Instansi Baru')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Tambah Instansi Baru</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftarkan instansi atau pihak peminjam baru</p>
    </div>
    <a href="{{ route('borrowers.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">Formulir Data Instansi</div>
            <div class="card-body p-4">
                <form action="{{ route('borrowers.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Instansi / Organisasi</label>
                        <input type="text" name="institution_name"
                               class="form-control @error('institution_name') is-invalid @enderror"
                               value="{{ old('institution_name') }}"
                               placeholder="Contoh: Dinas Pendidikan Kabupaten..." required>
                        @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Penanggung Jawab (PIC)</label>
                            <input type="text" name="pic_name"
                                   class="form-control @error('pic_name') is-invalid @enderror"
                                   value="{{ old('pic_name') }}"
                                   placeholder="Contoh: Budi Santoso" required>
                            @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Kontak (HP/Telp)</label>
                            <input type="text" name="contact_number"
                                   class="form-control @error('contact_number') is-invalid @enderror"
                                   value="{{ old('contact_number') }}"
                                   placeholder="Contoh: 081234567890" required
                                   style="font-family:monospace;">
                            @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Alamat <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
                        <textarea name="address" rows="3"
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Masukkan alamat lengkap instansi…">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Simpan Instansi</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
