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

                    <div class="mb-4">
                        <label class="form-label">Titik Koordinat (Peta) <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
                        <div class="input-group mb-2">
                            <input type="text" id="map-search" class="form-control form-control-sm" placeholder="Ketik nama tempat/kota untuk mencari di peta...">
                            <button class="btn btn-secondary btn-sm" type="button" id="btn-map-search">Cari</button>
                        </div>
                        <div id="map" style="height: 300px; border-radius: 6px; border: 1px solid #dee2e6;" class="mb-2"></div>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $borrower->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $borrower->longitude) }}">
                        <div class="form-text">Geser pin, klik pada peta, atau gunakan fitur pencarian di atas untuk menentukan lokasi. Alamat akan terisi otomatis.</div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let lat = document.getElementById('latitude').value || -6.2088;
        let lng = document.getElementById('longitude').value || 106.8456;
        let zoom = document.getElementById('latitude').value ? 15 : 5;

        const map = L.map('map').setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        const addressInput = document.querySelector('textarea[name="address"]');
        const mapSearch = document.getElementById('map-search');
        const btnMapSearch = document.getElementById('btn-map-search');

        async function reverseGeocode(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                }
            } catch (error) {
                console.error("Reverse geocoding error:", error);
            }
        }

        async function forwardGeocode(query) {
            if(!query.trim()) return;
            btnMapSearch.textContent = 'Mencari...';
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`);
                const data = await response.json();
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    addressInput.value = data[0].display_name;
                } else {
                    alert('Lokasi tidak ditemukan.');
                }
            } catch (error) {
                console.error("Geocoding error:", error);
                alert('Terjadi kesalahan saat mencari lokasi.');
            }
            btnMapSearch.textContent = 'Cari';
        }

        map.on('click', function(e) {
            let latlng = e.latlng;
            marker.setLatLng(latlng);
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
            reverseGeocode(latlng.lat, latlng.lng);
        });

        marker.on('dragend', function(e) {
            let latlng = marker.getLatLng();
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
            reverseGeocode(latlng.lat, latlng.lng);
        });

        if (btnMapSearch) {
            btnMapSearch.addEventListener('click', () => forwardGeocode(mapSearch.value));
        }
        if (mapSearch) {
            mapSearch.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    forwardGeocode(mapSearch.value);
                }
            });
        }
    });
</script>
@endpush
