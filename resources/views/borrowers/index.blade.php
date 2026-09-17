@extends('layouts.app')

@section('title', 'Master Data Instansi — SIM-ASET')
@section('page-title', 'Master Data Instansi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Instansi</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar instansi atau pihak yang dapat meminjam aset</p>
    </div>
    <a href="{{ route('borrowers.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Instansi
    </a>
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
                    <th class="text-center pe-4" style="width:9%;">Aksi</th>
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
                        <td class="text-center pe-4">
                            <a href="{{ route('borrowers.edit', $borrower->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
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

</script>
@endpush
