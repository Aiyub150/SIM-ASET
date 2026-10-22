@extends('layouts.app')

@section('title', 'Master Data Instansi — SIM-ASET')
@section('page-title', 'Master Data Instansi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Instansi</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar instansi atau pihak yang dapat meminjam aset</p>
    </div>
    @can('borrowers.create')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createBorrowerModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Instansi
    </button>
    @endcan
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:5%;">#</th>
                    <th>Nama Instansi / Organisasi</th>
                    <th style="width:18%;">Penanggung Jawab</th>
                    <th style="width:15%;">Nomor Kontak</th>
                    <th style="width:18%;">Alamat</th>
                    <th class="text-center" style="width:12%;">Peta Lokasi</th>
                    @can('borrowers.edit')
                    <th class="text-center pe-4" style="width:9%;">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse($borrowers as $index => $borrower)
                    <tr>
                        <td class="ps-4 text-muted">{{ $borrowers->firstItem() + $index }}</td>
                        <td style="font-weight:500;">{{ $borrower->institution_name }}</td>
                        <td>{{ $borrower->pic_name }}</td>
                        <td>
                            <span style="font-family:monospace; font-size:.83rem;">{{ $borrower->contact_number }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.83rem;">{{ $borrower->address ?: '—' }}</td>
                        <td class="text-center">
                            @if($borrower->latitude && $borrower->longitude)
                                <button type="button" class="btn btn-sm btn-outline-info"
                                     data-name="{{ $borrower->institution_name }}"
                                     data-lat="{{ $borrower->latitude }}"
                                     data-lng="{{ $borrower->longitude }}"
                                     onclick="showMap(this)">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt me-1" viewBox="0 0 16 16">
                                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                     </svg>
                                     Lihat Peta
                                </button>
                            @else
                                <span class="text-muted" style="font-size: 0.8rem;">Tidak ada koordinat</span>
                            @endif
                        </td>
                        @can('borrowers.edit')
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-borrower"
                                    data-id="{{ $borrower->id }}"
                                    data-name="{{ $borrower->institution_name }}"
                                    data-pic="{{ $borrower->pic_name }}"
                                    data-contact="{{ $borrower->contact_number }}"
                                    data-address="{{ $borrower->address }}"
                                    data-lat="{{ $borrower->latitude }}"
                                    data-lng="{{ $borrower->longitude }}">
                                Edit
                            </button>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('borrowers.edit') ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                            Belum ada data instansi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($borrowers->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $borrowers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Map -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel">Lokasi Instansi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="previewMap" style="height: 400px; width: 100%;"></div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL TAMBAH INSTANSI BARU (POPUP MODAL) --}}
@can('borrowers.create')
<div class="modal fade" id="createBorrowerModal" tabindex="-1" aria-labelledby="createBorrowerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createBorrowerModalLabel">Tambah Instansi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('borrowers.store') }}" method="POST" id="form-create-borrower">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-building text-primary me-1.5"></i> Nama Instansi / Organisasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="institution_name" class="form-control" placeholder="Contoh: Dinas Pendidikan Kabupaten..." required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-user-tie text-primary me-1.5"></i> Nama Penanggung Jawab (PIC) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="pic_name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-phone text-primary me-1.5"></i> Nomor Kontak (HP/Telp) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="contact_number" class="form-control" placeholder="Contoh: 081234567890" required style="font-family:monospace;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-location-dot text-primary me-1.5"></i> Alamat Lengkap
                        </label>
                        <textarea name="address" id="create_borrower_address" rows="2" class="form-control" placeholder="Jalan, nomor gedung, kecamatan..."></textarea>
                    </div>

                    {{-- PETA INTERAKTIF LEAFLET DENGAN PENCARIAN ALAMAT --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-map-location-dot text-primary me-1.5"></i> Titik Lokasi Peta (Leaflet)</span>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">Geser pin / cari alamat</span>
                        </label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="create_map_search_input" class="form-control" placeholder="Ketik nama tempat/gedung/jalan untuk mencari di peta...">
                            <button class="btn btn-primary" type="button" id="btn_create_map_search">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                            </button>
                        </div>
                        <div id="create_borrower_map" style="height: 280px; width: 100%; border-radius: 8px; border: 1.5px solid #cbd5e1; z-index: 1;"></div>
                        <input type="hidden" name="latitude" id="create_borrower_lat" value="-6.2088">
                        <input type="hidden" name="longitude" id="create_borrower_lng" value="106.8456">
                        <div class="form-text text-muted" style="font-size:0.75rem;">
                            Klik pada peta atau geser pin marker. Alamat dan titik koordinat akan terisi otomatis.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Simpan Instansi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- MODAL EDIT INSTANSI (POPUP MODAL) --}}
@can('borrowers.edit')
<div class="modal fade" id="editBorrowerModal" tabindex="-1" aria-labelledby="editBorrowerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow border-0" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="editBorrowerModalLabel">Edit Data Instansi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="form-edit-borrower">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-building text-primary me-1.5"></i> Nama Instansi / Organisasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="institution_name" id="edit_borrower_name" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-user-tie text-primary me-1.5"></i> Nama Penanggung Jawab (PIC) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="pic_name" id="edit_borrower_pic" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-phone text-primary me-1.5"></i> Nomor Kontak (HP/Telp) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="contact_number" id="edit_borrower_contact" class="form-control" required style="font-family:monospace;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-location-dot text-primary me-1.5"></i> Alamat Lengkap
                        </label>
                        <textarea name="address" id="edit_borrower_address" rows="2" class="form-control"></textarea>
                    </div>

                    {{-- PETA INTERAKTIF LEAFLET UNTUK EDIT --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-map-location-dot text-primary me-1.5"></i> Titik Lokasi Peta (Leaflet)</span>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">Geser pin / cari alamat</span>
                        </label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="edit_map_search_input" class="form-control" placeholder="Ketik nama tempat/gedung/jalan untuk mencari di peta...">
                            <button class="btn btn-primary" type="button" id="btn_edit_map_search">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                            </button>
                        </div>
                        <div id="edit_borrower_map" style="height: 280px; width: 100%; border-radius: 8px; border: 1.5px solid #cbd5e1; z-index: 1;"></div>
                        <input type="hidden" name="latitude" id="edit_borrower_lat">
                        <input type="hidden" name="longitude" id="edit_borrower_lng">
                        <div class="form-text text-muted" style="font-size:0.75rem;">
                            Klik pada peta atau geser pin marker. Alamat dan titik koordinat akan terisi otomatis.
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
@endcan

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    let previewMap;
    let previewMarker;

    // Helper to prevent XSS
    function escapeHtml(unsafe) {
        return (unsafe || '').toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showMap(btn) {
        const name = btn.getAttribute('data-name');
        const lat = parseFloat(btn.getAttribute('data-lat'));
        const lng = parseFloat(btn.getAttribute('data-lng'));

        const modalEl = document.getElementById('mapModal');
        const mapModal = new bootstrap.Modal(modalEl);
        document.getElementById('mapModalLabel').innerText = 'Lokasi: ' + name;
        mapModal.show();

        modalEl.addEventListener('shown.bs.modal', function () {
            if (!previewMap) {
                previewMap = L.map('previewMap').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(previewMap);
                previewMarker = L.marker([lat, lng]).addTo(previewMap);
                previewMarker.bindPopup("<b>" + escapeHtml(name) + "</b>").openPopup();
            } else {
                previewMap.setView([lat, lng], 15);
                previewMarker.setLatLng([lat, lng]);
                previewMarker.getPopup().setContent("<b>" + escapeHtml(name) + "</b>");
                previewMap.invalidateSize();
            }
        }, { once: true });
    }

    // --- LEAFLET MAP LOGIC UNTUK MODAL TAMBAH & EDIT INSTANSI ---
    let createMapInstance = null;
    let createMarkerInstance = null;
    let editMapInstance = null;
    let editMarkerInstance = null;

    async function reverseGeocodeAddress(lat, lng, targetAddressInput) {
        try {
            const response = await fetch(`{{ route('geocoding.reverse') }}?lat=${lat}&lng=${lng}`);
            const res = await response.json();
            if (res && res.success && res.display_name && targetAddressInput) {
                targetAddressInput.value = res.display_name;
            }
        } catch (err) {
            console.error('Reverse geocode error:', err);
        }
    }

    async function forwardGeocodeSearch(query, btnEl, mapObj, markerObj, latInput, lngInput, addrInput) {
        if (!query.trim()) return;
        const originalHtml = btnEl.innerHTML;
        btnEl.disabled = true;
        btnEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mencari...';

        try {
            const response = await fetch(`{{ route('geocoding.search') }}?q=${encodeURIComponent(query)}`);
            const res = await response.json();
            if (res && res.success && res.data && res.data.length > 0) {
                const first = res.data[0];
                const lat = parseFloat(first.lat);
                const lng = parseFloat(first.lon);
                mapObj.setView([lat, lng], 16);
                markerObj.setLatLng([lat, lng]);
                latInput.value = lat;
                lngInput.value = lng;
                if (addrInput) addrInput.value = first.display_name;
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Pencarian Lokasi',
                    text: 'Lokasi tidak ditemukan. Coba gunakan nama kota atau kata kunci lain.',
                    confirmButtonColor: '#2563eb'
                });
            }
        } catch (err) {
            console.error('Forward geocode error:', err);
        } finally {
            btnEl.disabled = false;
            btnEl.innerHTML = originalHtml;
        }
    }

    // Modal Create Map Setup
    const createBorrowerModalEl = document.getElementById('createBorrowerModal');
    if (createBorrowerModalEl) {
        createBorrowerModalEl.addEventListener('shown.bs.modal', function() {
            const defaultLat = -6.2088;
            const defaultLng = 106.8456;
            const latInput = document.getElementById('create_borrower_lat');
            const lngInput = document.getElementById('create_borrower_lng');
            const addrInput = document.getElementById('create_borrower_address');

            if (!createMapInstance) {
                createMapInstance = L.map('create_borrower_map').setView([defaultLat, defaultLng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(createMapInstance);

                createMarkerInstance = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(createMapInstance);

                createMarkerInstance.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    latInput.value = pos.lat;
                    lngInput.value = pos.lng;
                    reverseGeocodeAddress(pos.lat, pos.lng, addrInput);
                });

                createMapInstance.on('click', function(e) {
                    createMarkerInstance.setLatLng(e.latlng);
                    latInput.value = e.latlng.lat;
                    lngInput.value = e.latlng.lng;
                    reverseGeocodeAddress(e.latlng.lat, e.latlng.lng, addrInput);
                });

                document.getElementById('btn_create_map_search')?.addEventListener('click', function() {
                    const q = document.getElementById('create_map_search_input').value;
                    forwardGeocodeSearch(q, this, createMapInstance, createMarkerInstance, latInput, lngInput, addrInput);
                });

                document.getElementById('create_map_search_input')?.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('btn_create_map_search').click();
                    }
                });
            } else {
                createMapInstance.invalidateSize();
            }
        });
    }

    // Modal Edit Map Setup
    const editBorrowerModalEl = document.getElementById('editBorrowerModal');
    if (editBorrowerModalEl) {
        editBorrowerModalEl.addEventListener('shown.bs.modal', function() {
            const latInput = document.getElementById('edit_borrower_lat');
            const lngInput = document.getElementById('edit_borrower_lng');
            const addrInput = document.getElementById('edit_borrower_address');

            const currentLat = parseFloat(latInput.value) || -6.2088;
            const currentLng = parseFloat(lngInput.value) || 106.8456;

            if (!editMapInstance) {
                editMapInstance = L.map('edit_borrower_map').setView([currentLat, currentLng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(editMapInstance);

                editMarkerInstance = L.marker([currentLat, currentLng], { draggable: true }).addTo(editMapInstance);

                editMarkerInstance.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    latInput.value = pos.lat;
                    lngInput.value = pos.lng;
                    reverseGeocodeAddress(pos.lat, pos.lng, addrInput);
                });

                editMapInstance.on('click', function(e) {
                    editMarkerInstance.setLatLng(e.latlng);
                    latInput.value = e.latlng.lat;
                    lngInput.value = e.latlng.lng;
                    reverseGeocodeAddress(e.latlng.lat, e.latlng.lng, addrInput);
                });

                document.getElementById('btn_edit_map_search')?.addEventListener('click', function() {
                    const q = document.getElementById('edit_map_search_input').value;
                    forwardGeocodeSearch(q, this, editMapInstance, editMarkerInstance, latInput, lngInput, addrInput);
                });

                document.getElementById('edit_map_search_input')?.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('btn_edit_map_search').click();
                    }
                });
            } else {
                editMapInstance.setView([currentLat, currentLng], 14);
                editMarkerInstance.setLatLng([currentLat, currentLng]);
                editMapInstance.invalidateSize();
            }
        });
    }

    // Handler Modal Edit Instansi Popup Trigger
    document.querySelectorAll('.btn-edit-borrower').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const pic = this.getAttribute('data-pic');
            const contact = this.getAttribute('data-contact');
            const address = this.getAttribute('data-address');
            const lat = this.getAttribute('data-lat');
            const lng = this.getAttribute('data-lng');

            const form = document.getElementById('form-edit-borrower');
            form.action = `/borrowers/${id}`;
            document.getElementById('edit_borrower_name').value = name;
            document.getElementById('edit_borrower_pic').value = pic;
            document.getElementById('edit_borrower_contact').value = contact;
            document.getElementById('edit_borrower_address').value = address || '';
            document.getElementById('edit_borrower_lat').value = lat || '-6.2088';
            document.getElementById('edit_borrower_lng').value = lng || '106.8456';

            const editModal = new bootstrap.Modal(document.getElementById('editBorrowerModal'));
            editModal.show();
        });
    });
</script>
@endpush
