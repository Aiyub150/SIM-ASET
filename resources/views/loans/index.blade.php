@extends('layouts.app')

@section('title', 'Daftar Peminjaman — SIM-ASET')
@section('page-title', 'Daftar Peminjaman')

@section('content')

@php $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'Admin']); @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Transaksi Peminjaman</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            @if($isAdmin)
                Daftar seluruh transaksi peminjaman aset daerah
            @else
                Daftar transaksi peminjaman yang Anda catat
            @endif
        </p>
    </div>
    @can('loans.create')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createLoanModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Buat Peminjaman
    </button>
    @endcan
</div>

<div class="card">
    <div class="px-4 py-3 border-bottom bg-light">
        <form method="GET" action="{{ route('loans.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode, peminjam, barang, pencatat..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" id="loan_status_filter" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('loans.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Kode Transaksi</th>
                    <th>Instansi Peminjam</th>
                    @if($isAdmin)
                    <th>Pencatat</th>
                    @endif
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    @php 
                        $statusCalculated = $loan->computed_status; 
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-600" style="font-weight:600; font-family:monospace; font-size:.82rem; color:#2563eb;">
                                {{ $loan->loan_code }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-500" style="font-weight:500;">{{ $loan->borrower->institution_name }}</span>
                        </td>
                        @if($isAdmin)
                        <td class="text-muted">{{ $loan->user->name }}</td>
                        @endif
                        <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                        <td class="{{ ($statusCalculated === 'overdue' || $statusCalculated === 'completed_late') ? 'text-danger fw-bold' : '' }}">
                            {{ $loan->due_date->format('d/m/Y') }}
                            @if($statusCalculated === 'overdue')
                                <br><small style="font-size:.72rem;">Terlambat!</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($statusCalculated === 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($statusCalculated === 'completed_late')
                                <span class="badge bg-warning text-dark">Selesai Terlambat</span>
                            @elseif($statusCalculated === 'overdue')
                                <span class="badge bg-danger">Terlambat</span>
                            @else
                                <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary btn-view-loan-detail"
                                    data-bs-toggle="modal"
                                    data-bs-target="#loanDetailModal"
                                    data-loan="{{ json_encode([
                                        'id' => $loan->id,
                                        'loan_code' => $loan->loan_code,
                                        'borrower_institution' => $loan->borrower->institution_name,
                                        'borrower_pic' => $loan->borrower->pic_name,
                                        'borrower_contact' => $loan->borrower->contact_number,
                                        'borrower_address' => $loan->borrower->address ?? '-',
                                        'recorder_name' => $loan->user->name,
                                        'borrow_date' => $loan->borrow_date->format('d M Y'),
                                        'due_date' => $loan->due_date->format('d M Y'),
                                        'return_date' => $loan->return_date ? $loan->return_date->format('d M Y') : null,
                                        'status' => $loan->status,
                                        'computed_status' => $statusCalculated,
                                        'notes' => $loan->notes ?? '-',
                                        'borrow_proof' => $loan->borrow_proof_image ? asset('storage/' . $loan->borrow_proof_image) : null,
                                        'return_proof' => $loan->return_proof_image ? asset('storage/' . $loan->return_proof_image) : null,
                                        'print_url' => route('loans.print', $loan->id),
                                        'return_url' => route('loans.return', $loan->id),
                                        'borrow_proof_url' => route('loans.borrow-proof', $loan->id),
                                        'return_proof_url' => route('loans.return-proof', $loan->id),
                                        'is_all_returned' => $loan->loanItems->count() > 0 && $loan->loanItems->every(fn($li) => ($li->qty - $li->returned_qty) == 0),
                                        'can_return' => auth()->user()->can('loans.return') && !in_array($loan->computed_status, ['completed', 'completed_late']),
                                        'items' => $loan->loanItems->map(fn($li) => [
                                            'id' => $li->id,
                                            'sku' => $li->item->sku,
                                            'name' => $li->item->name,
                                            'qty' => $li->qty,
                                            'returned_qty' => $li->returned_qty,
                                            'remaining_qty' => $li->qty - $li->returned_qty,
                                            'image' => $li->item->image ? asset('storage/' . $li->item->image) : null,
                                        ]),
                                    ]) }}">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                            </svg>
                            Belum ada data peminjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($loans->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $loans->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

</div>

{{-- MODAL DETAIL PEMINJAMAN LENGKAP DENGAN UPLOAD BUKTI SUSULAN & RETURN POPUP --}}
<div class="modal fade" id="loanDetailModal" tabindex="-1" aria-labelledby="loanDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content shadow border-0" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="loanDetailModalLabel">Detail Peminjaman</h5>
                        <span class="badge bg-white text-primary border font-monospace mt-1 fs-6" id="detail_loan_code">-</span>
                    </div>
                    <span id="detail_status_badge" class="badge ms-2">-</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="detail_print_bast_btn" target="_blank" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                        </svg>
                        Cetak BAST (PDF)
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                {{-- Data Peminjam & Ringkasan Waktu --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header py-2 px-3 bg-light fw-semibold small text-muted">DATA INSTANSI PEMINJAM</div>
                            <div class="card-body p-3">
                                <table class="table table-sm table-borderless mb-0" style="font-size:0.85rem;">
                                    <tr>
                                        <td class="text-muted" style="width:35%;">Instansi</td>
                                        <td class="fw-bold text-dark" id="detail_borrower_institution">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Penanggung Jawab</td>
                                        <td id="detail_borrower_pic">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kontak</td>
                                        <td id="detail_borrower_contact">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Dicatat Oleh</td>
                                        <td class="text-primary fw-semibold" id="detail_recorder_name">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header py-2 px-3 bg-light fw-semibold small text-muted">JADWAL & CATATAN</div>
                            <div class="card-body p-3">
                                <table class="table table-sm table-borderless mb-0" style="font-size:0.85rem;">
                                    <tr>
                                        <td class="text-muted" style="width:35%;">Tanggal Pinjam</td>
                                        <td id="detail_borrow_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jatuh Tempo</td>
                                        <td class="fw-bold" id="detail_due_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tanggal Kembali</td>
                                        <td id="detail_return_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Catatan</td>
                                        <td id="detail_notes">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Rincian Barang --}}
                <div class="card mb-4 border">
                    <div class="card-header py-2 px-3 bg-light fw-semibold small text-muted">RINCIAN BARANG PINJAMAN</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size:0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">SKU</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jml Pinjam</th>
                                    <th class="text-center">Sudah Kembali</th>
                                    <th class="text-center">Sisa Pinjaman</th>
                                    <th class="text-center pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items_tbody">
                                {{-- Diinjeksi oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BUKTI DOKUMENTASI FISIK (AWAL & PENGEMBALIAN) --}}
                <div class="row g-3 mb-3">
                    {{-- Bukti Awal Peminjaman --}}
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header py-2 px-3 bg-light fw-semibold small text-muted d-flex justify-content-between align-items-center">
                                <span>BUKTI SERAH TERIMA AWAL</span>
                                <span id="detail_borrow_proof_badge" class="badge bg-secondary">Belum Ada</span>
                            </div>
                            <div class="card-body p-3 text-center">
                                <div id="detail_borrow_proof_view" class="mb-3" style="display:none;">
                                    <img id="detail_borrow_proof_img" src="" alt="Bukti Pinjam" style="max-height: 160px; max-width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                                </div>
                                <form id="form_upload_borrow_proof" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-2 align-items-center mb-2">
                                        <div class="col-auto">
                                            <div style="width: 55px; height: 55px; border: 2px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden;">
                                                <img id="borrow_proof_live_preview" src="" alt="Live Bukti Pinjam" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                                <i id="borrow_proof_live_icon" class="fa-solid fa-camera text-secondary"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <input type="file" name="borrow_proof_image" id="input_borrow_proof_file" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">Unggah Bukti Serah Terima</button>
                                    <div class="form-text text-muted small text-start mt-1" style="font-size:0.72rem;">
                                        Unggah foto penyerahan barang / BAST fisik (maks 2MB).
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Bukti Pengembalian Barang (Hanya jika semua barang kembali) --}}
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header py-2 px-3 bg-light fw-semibold small text-muted d-flex justify-content-between align-items-center">
                                <span>BUKTI PENGEMBALIAN LENGKAP</span>
                                <span id="detail_return_proof_badge" class="badge bg-secondary">Belum Ada</span>
                            </div>
                            <div class="card-body p-3 text-center">
                                <div id="detail_return_proof_view" class="mb-3" style="display:none;">
                                    <img id="detail_return_proof_img" src="" alt="Bukti Kembali" style="max-height: 160px; max-width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: contain;">
                                </div>
                                <form id="form_upload_return_proof" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-2 align-items-center mb-2">
                                        <div class="col-auto">
                                            <div style="width: 55px; height: 55px; border: 2px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden;">
                                                <img id="return_proof_live_preview" src="" alt="Live Bukti Kembali" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                                <i id="return_proof_live_icon" class="fa-solid fa-camera text-secondary"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <input type="file" name="return_proof_image" id="input_return_proof" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" required>
                                        </div>
                                    </div>
                                    <button type="submit" id="btn_submit_return_proof" class="btn btn-success btn-sm w-100 fw-semibold">Unggah Bukti Pengembalian</button>
                                    <div class="form-text text-muted small text-start mt-1" style="font-size:0.72rem;">
                                        Hanya dapat diunggah jika <strong>seluruh barang telah dikembalikan</strong>.
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM PENGEMBALIAN BARANG LANGSUNG DI MODAL JIKA BELUM SELESAI --}}
                <div id="detail_return_section" class="card border border-primary mt-3" style="display: none;">
                    <div class="card-header bg-primary text-white py-2 px-3 fw-semibold small d-flex justify-content-between align-items-center">
                        <span>PROSES PENGEMBALIAN BARANG</span>
                        <span class="badge bg-white text-primary">Operasional</span>
                    </div>
                    <div class="card-body p-3">
                        {{-- PEMINDAI BARCODE / SKU PENGEMBALIAN CEPAT --}}
                        <div class="p-2 mb-3 bg-light border rounded-3">
                            <label class="form-label fw-semibold small mb-1 d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-barcode text-primary me-1"></i> Scan Barcode / Input SKU Barang</span>
                                <span class="badge bg-primary-subtle text-primary" style="font-size:0.68rem;">Scanner Otomatis</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-qrcode text-muted"></i></span>
                                <input type="text" id="modal_scan_barcode_input" class="form-control font-monospace" placeholder="Scan barcode barang atau ketik SKU lalu Enter...">
                                <button type="button" class="btn btn-outline-primary" id="btn_toggle_modal_camera">
                                    <i class="fa-solid fa-camera"></i> Kamera
                                </button>
                            </div>
                            <div id="modal_camera_reader_box" class="mt-2 text-center" style="display: none;">
                                <div id="modal_reader" style="width: 100%; max-width: 320px; margin: 0 auto; border-radius: 8px; overflow: hidden;"></div>
                                <small class="text-muted d-block mt-1">Arahkan kamera ke QR Code/Barcode barang</small>
                            </div>
                            <div id="modal_scan_feedback" class="small mt-1 text-muted" style="font-size:0.75rem;">
                                Siap memindai barcode. Setiap scan akan otomatis mengisi/menaikkan jumlah kembali item terkait.
                            </div>
                        </div>

                        <form id="form_modal_return" method="POST">
                            @csrf
                            <div id="modal_return_items_container">
                                {{-- Input row dikonstruksi secara dinamis via JS --}}
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                                <button type="button" id="btn_modal_return_all" class="btn btn-sm btn-outline-success">
                                    Kembalikan Semua Sisa Sekaligus
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
                                    Eksekusi Pengembalian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@can('loans.create')
{{-- Modal Tambah Peminjaman Baru (Zero-Redirect) --}}
<div class="modal fade" id="createLoanModal" tabindex="-1" aria-labelledby="createLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-hand-holding-box text-primary fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="createLoanModalLabel">Buat Transaksi Peminjaman Baru</h5>
                        <p class="text-muted small mb-0">Catat serah terima peminjaman aset inventaris kepada instansi/peminjam</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('loans.store') }}" method="POST" id="form-create-loan" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    {{-- SCANNER SKU / BARCODE KOTAK INSTAN --}}
                    <div class="card border border-primary border-opacity-25 bg-light mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-2">
                                <div>
                                    <span class="fw-bold text-primary small d-flex align-items-center gap-1.5">
                                        <i class="fa-solid fa-qrcode fs-6"></i> Scan Barcode Kotak (QR Code) / Ketik SKU
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Arahkan scanner USB/kamera atau ketik SKU barang untuk menambahkannya langsung ke daftar</small>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-toggle-create-camera">
                                    <i class="fa-solid fa-camera me-1"></i> Buka Kamera Scanner
                                </button>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                <input type="text" id="modal-sku-scan-input" class="form-control font-monospace" placeholder="Contoh: ELEC-001 (lalu tekan Enter)..." autocomplete="off">
                            </div>
                            <div id="modal-sku-scan-feedback" class="small mt-1 text-muted" style="font-size: 0.72rem;">
                                Menunggu input keyboard/scanner…
                            </div>
                            <div id="modal-camera-preview-wrapper" class="mt-2 text-center" style="display: none;">
                                <div id="modal-create-camera-reader" class="rounded border" style="width: 100%; max-width: 320px; margin: 0 auto; min-height: 200px; background: #0f172a;"></div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Arahkan kamera ke QR Code/Barcode barang</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- KOLOM KIRI: INFORMASI PEMINJAMAN --}}
                        <div class="col-lg-5">
                            <div class="card h-100 border">
                                <div class="card-header bg-white py-2.5 px-3 fw-bold small text-dark border-bottom">
                                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Data Instansi & Jadwal
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Instansi Peminjam <span class="text-danger">*</span></label>
                                        <select name="borrower_id" id="modal_loan_borrower_id" class="form-select" required>
                                            <option value="">— Cari atau pilih instansi… —</option>
                                            @if(isset($borrowers))
                                                @foreach($borrowers as $b)
                                                    <option value="{{ $b->id }}"
                                                            data-inst="{{ $b->institution_name }}"
                                                            data-pic="{{ $b->pic_name }}"
                                                            data-phone="{{ $b->phone ?? '-' }}"
                                                            data-email="{{ $b->email ?? '-' }}">
                                                        {{ $b->institution_name }} (PIC: {{ $b->pic_name }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label fw-semibold small">Tanggal Pinjam <span class="text-danger">*</span></label>
                                            <input type="date" name="borrow_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-semibold small">Batas Kembali <span class="text-danger">*</span></label>
                                            <input type="date" name="due_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Bukti Serah Terima Awal <span class="text-muted fw-normal">(Opsional)</span></label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 50px; height: 50px; border: 1.5px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #f8fafc;">
                                                <img id="create_borrow_proof_preview" src="" alt="Bukti" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                                                <i class="fa-solid fa-file-image text-secondary" id="create_borrow_proof_icon"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="borrow_proof_image" id="create_borrow_proof_input" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                                                <div class="text-muted" style="font-size: 0.7rem;">Format JPG/PNG/WEBP maks 2MB</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label fw-semibold small">Catatan Peminjaman <span class="text-muted fw-normal">(Opsional)</span></label>
                                        <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Contoh: Keperluan dinas rapat koordinasi wilayah..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: DAFTAR BARANG YANG DIPINJAM --}}
                        <div class="col-lg-7">
                            <div class="card h-100 border">
                                <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center border-bottom">
                                    <span class="fw-bold small text-dark">
                                        <i class="fa-solid fa-boxes-stacked text-primary me-1"></i> Rincian Barang yang Dipinjam
                                    </span>
                                    <button type="button" id="btn-modal-add-row" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.78rem;">
                                        <i class="fa-solid fa-plus me-1"></i> Tambah Baris
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div id="modal-loan-items-container" style="max-height: 380px; overflow-y: auto; padding-right: 4px;">
                                        {{-- Row item digenerate via JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Proses & Simpan Transaksi Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script id="items-data" type="application/json">
    {!! json_encode($items ?? []) !!}
</script>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loanStatusFilter = document.getElementById('loan_status_filter');
        if (loanStatusFilter && typeof TomSelect !== 'undefined') {
            let loanInit = false;
            new TomSelect(loanStatusFilter, {
                create: false,
                controlInput: null,
                placeholder: 'Semua Status Peminjaman',
                allowEmptyOption: true,
                onInitialize: function() {
                    loanInit = true;
                },
                onChange: function() {
                    if (!loanInit) return;
                    loanStatusFilter.closest('form').submit();
                }
            });
        }

        // Setup live preview bukti di modal loan detail
        function setupMiniPreview(inputId, imgId, iconId) {
            const input = document.getElementById(inputId);
            const img = document.getElementById(imgId);
            const icon = document.getElementById(iconId);
            if (!input || !img) return;

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        img.src = e.target.result;
                        img.style.display = 'block';
                        if (icon) icon.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    img.src = '';
                    img.style.display = 'none';
                    if (icon) icon.style.display = 'block';
                }
            });
        }

        setupMiniPreview('input_borrow_proof_file', 'borrow_proof_live_preview', 'borrow_proof_live_icon');
        setupMiniPreview('input_return_proof', 'return_proof_live_preview', 'return_proof_live_icon');
        setupMiniPreview('create_borrow_proof_input', 'create_borrow_proof_preview', 'create_borrow_proof_icon');

        // --- HANDLER MODAL CREATE PEMINJAMAN (POPUP ZERO-REDIRECT) ---
        const createLoanModal = document.getElementById('createLoanModal');
        const modalBorrowerSelect = document.getElementById('modal_loan_borrower_id');
        let modalBorrowerTom = null;

        if (modalBorrowerSelect && typeof TomSelect !== 'undefined') {
            modalBorrowerTom = new TomSelect(modalBorrowerSelect, {
                create: false,
                plugins: ['dropdown_input'],
                placeholder: '— Cari atau pilih instansi peminjam —',
                render: {
                    option: function(data, escape) {
                        return `<div class="d-flex align-items-center py-1">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.8rem;">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark small">${escape(data.inst || data.text)}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    PIC: <strong>${escape(data.pic || '-')}</strong> | Telp: ${escape(data.phone || '-')}
                                </div>
                            </div>
                        </div>`;
                    },
                    item: function(data, escape) {
                        return `<div><i class="fa-solid fa-building text-primary me-1"></i> <strong>${escape(data.inst || data.text)}</strong> <span class="text-muted small">(PIC: ${escape(data.pic || '-')})</span></div>`;
                    }
                }
            });
        }

        // Kelola Baris Barang Dinamis di Modal Create Peminjaman
        const itemsDataEl = document.getElementById('items-data');
        const AVAILABLE_ITEMS = itemsDataEl ? JSON.parse(itemsDataEl.textContent || '[]') : [];
        const itemsContainer = document.getElementById('modal-loan-items-container');
        const btnAddRow = document.getElementById('btn-modal-add-row');
        let modalRowIndex = 0;
        let itemPickers = [];

        function renderLoanRow(initialItemId = null, initialQty = 1) {
            if (!itemsContainer) return;
            const idx = modalRowIndex++;
            const rowDiv = document.createElement('div');
            rowDiv.className = 'row g-2 align-items-center mb-2 pb-2 border-bottom modal-item-row';
            rowDiv.dataset.idx = idx;

            rowDiv.innerHTML = `
                <div class="col-7">
                    <label class="form-label small mb-1 fw-semibold">Pilih Barang</label>
                    <select name="items[${idx}][item_id]" class="form-select form-select-sm modal-item-select" required>
                        <option value="">— Cari atau pilih barang… —</option>
                        ${AVAILABLE_ITEMS.map(it => `
                            <option value="${it.id}"
                                    data-name="${it.name}"
                                    data-sku="${it.sku}"
                                    data-available="${it.available_qty}"
                                    data-category="${it.category ? it.category.name : 'Umum'}"
                                    data-image="${it.image ? '/storage/' + it.image : ''}"
                                    ${initialItemId == it.id ? 'selected' : ''}>
                                [${it.sku}] ${it.name} (Sisa: ${it.available_qty})
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-3">
                    <label class="form-label small mb-1 fw-semibold">Jumlah (Qty)</label>
                    <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm modal-item-qty" value="${initialQty}" min="1" max="999" required>
                    <div class="text-muted text-end small modal-qty-hint" style="font-size:0.68rem;">Maks: -</div>
                </div>
                <div class="col-2 text-end pt-3">
                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-remove-row" title="Hapus baris">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;

            itemsContainer.appendChild(rowDiv);

            const selectEl = rowDiv.querySelector('.modal-item-select');
            const qtyInput = rowDiv.querySelector('.modal-item-qty');
            const qtyHint  = rowDiv.querySelector('.modal-qty-hint');
            const removeBtn = rowDiv.querySelector('.btn-remove-row');

            let tom = null;
            if (typeof TomSelect !== 'undefined') {
                tom = new TomSelect(selectEl, {
                    create: false,
                    plugins: ['dropdown_input'],
                    placeholder: '— Cari nama / SKU barang —',
                    render: {
                        option: function(data, escape) {
                            const img = data.image ? `<img src="${escape(data.image)}" class="rounded border me-2" style="width: 28px; height: 28px; object-fit: cover;">` : `<div class="rounded border bg-light d-flex align-items-center justify-content-center me-2 text-muted" style="width: 28px; height: 28px; font-size: 0.65rem;"><i class="fa-solid fa-box"></i></div>`;
                            return `<div class="d-flex align-items-center py-1">
                                ${img}
                                <div>
                                    <div class="fw-semibold text-dark small">${escape(data.name || data.text)}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        SKU: <span class="font-monospace text-primary">${escape(data.sku || '')}</span> | Tersedia: <strong class="text-success">${escape(data.available || 0)}</strong>
                                    </div>
                                </div>
                            </div>`;
                        },
                        item: function(data, escape) {
                            return `<div><span class="font-monospace fw-bold text-primary">[${escape(data.sku || '')}]</span> ${escape(data.name || data.text)}</div>`;
                        }
                    },
                    onChange: function(val) {
                        syncRowMax(val, qtyInput, qtyHint);
                    }
                });
            } else {
                selectEl.addEventListener('change', function() {
                    syncRowMax(this.value, qtyInput, qtyHint);
                });
            }

            if (initialItemId) {
                syncRowMax(initialItemId, qtyInput, qtyHint);
            }

            removeBtn.addEventListener('click', function() {
                if (itemsContainer.querySelectorAll('.modal-item-row').length <= 1) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: 'Minimal harus ada 1 barang dalam transaksi peminjaman.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }
                if (tom) tom.destroy();
                rowDiv.remove();
            });

            itemPickers.push({ idx, row: rowDiv, tom, selectEl, qtyInput });
        }

        function syncRowMax(itemId, qtyInput, qtyHint) {
            const item = AVAILABLE_ITEMS.find(it => String(it.id) === String(itemId));
            if (item) {
                qtyInput.max = item.available_qty;
                qtyHint.textContent = `Maks: ${item.available_qty}`;
                if (parseInt(qtyInput.value, 10) > item.available_qty) {
                    qtyInput.value = item.available_qty;
                }
            } else {
                qtyHint.textContent = 'Maks: -';
            }
        }

        if (btnAddRow) {
            btnAddRow.addEventListener('click', function() {
                renderLoanRow();
            });
        }

        if (createLoanModal) {
            createLoanModal.addEventListener('shown.bs.modal', function() {
                if (itemsContainer && itemsContainer.children.length === 0) {
                    renderLoanRow();
                }
            });
        }

        // Scanner Barcode / SKU di Modal Tambah Peminjaman
        const modalSkuInput = document.getElementById('modal-sku-scan-input');
        const modalSkuFeedback = document.getElementById('modal-sku-scan-feedback');
        const btnToggleCreateCamera = document.getElementById('btn-toggle-create-camera');
        const createCameraBox = document.getElementById('modal-camera-preview-wrapper');
        let createHtml5QrCode = null;

        function handleScannedSku(scannedSku) {
            const clean = scannedSku.trim().toUpperCase();
            if (!clean) return;

            const foundItem = AVAILABLE_ITEMS.find(it => it.sku && it.sku.toUpperCase() === clean);
            if (!foundItem) {
                modalSkuFeedback.innerHTML = `<span class="text-danger fw-semibold"><i class="fa-solid fa-circle-xmark"></i> Barang dengan SKU "${scannedSku}" tidak ditemukan atau stok kosong.</span>`;
                return;
            }

            // Cek apakah barang sudah ada di salah satu baris
            let existingRow = null;
            itemPickers.forEach(picker => {
                const currentVal = picker.tom ? picker.tom.getValue() : picker.selectEl.value;
                if (String(currentVal) === String(foundItem.id)) {
                    existingRow = picker;
                }
            });

            if (existingRow) {
                const curQty = parseInt(existingRow.qtyInput.value || '1', 10);
                if (curQty < foundItem.available_qty) {
                    existingRow.qtyInput.value = curQty + 1;
                    modalSkuFeedback.innerHTML = `<span class="text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> Menambahkan qty untuk [${foundItem.sku}] ${foundItem.name} (Total: ${curQty + 1}).</span>`;
                } else {
                    modalSkuFeedback.innerHTML = `<span class="text-warning fw-semibold"><i class="fa-solid fa-triangle-exclamation"></i> Qty untuk [${foundItem.sku}] ${foundItem.name} telah mencapai batas stok (${foundItem.available_qty}).</span>`;
                }
            } else {
                // Cari baris kosong atau tambah baris baru
                let emptyPicker = itemPickers.find(p => {
                    const v = p.tom ? p.tom.getValue() : p.selectEl.value;
                    return !v;
                });
                if (emptyPicker) {
                    if (emptyPicker.tom) {
                        emptyPicker.tom.setValue(foundItem.id);
                    } else {
                        emptyPicker.selectEl.value = foundItem.id;
                    }
                    syncRowMax(foundItem.id, emptyPicker.qtyInput, emptyPicker.row.querySelector('.modal-qty-hint'));
                } else {
                    renderLoanRow(foundItem.id, 1);
                }
                modalSkuFeedback.innerHTML = `<span class="text-success fw-semibold"><i class="fa-solid fa-circle-check"></i> [${foundItem.sku}] ${foundItem.name} berhasil ditambahkan ke daftar!</span>`;
            }
        }

        if (modalSkuInput) {
            modalSkuInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleScannedSku(this.value);
                    this.value = '';
                }
            });
        }

        if (btnToggleCreateCamera && createCameraBox) {
            btnToggleCreateCamera.addEventListener('click', function() {
                if (createCameraBox.style.display === 'none') {
                    createCameraBox.style.display = 'block';
                    btnToggleCreateCamera.innerHTML = '<i class="fa-solid fa-video-slash me-1"></i> Tutup Kamera';
                    btnToggleCreateCamera.className = 'btn btn-danger btn-sm';

                    createHtml5QrCode = new Html5Qrcode("modal-create-camera-reader");
                    createHtml5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 220, height: 220 } },
                        (decodedText) => {
                            handleScannedSku(decodedText);
                        },
                        () => {}
                    ).catch(err => {
                        createCameraBox.style.display = 'none';
                        btnToggleCreateCamera.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Buka Kamera Scanner';
                        btnToggleCreateCamera.className = 'btn btn-outline-primary btn-sm';
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kamera Tidak Tersedia',
                            text: 'Gagal mengakses modul kamera. Gunakan keyboard scanner SKU.',
                            confirmButtonColor: '#2563eb'
                        });
                    });
                } else {
                    stopCreateCamera();
                }
            });

            function stopCreateCamera() {
                if (createHtml5QrCode) {
                    createHtml5QrCode.stop().then(() => {
                        createHtml5QrCode.clear();
                        createHtml5QrCode = null;
                    }).catch(() => {});
                }
                createCameraBox.style.display = 'none';
                btnToggleCreateCamera.innerHTML = '<i class="fa-solid fa-camera me-1"></i> Buka Kamera Scanner';
                btnToggleCreateCamera.className = 'btn btn-outline-primary btn-sm';
            }

            if (createLoanModal) {
                createLoanModal.addEventListener('hidden.bs.modal', function() {
                    stopCreateCamera();
                });
            }
        }

        // Validasi Submit Form Create Peminjaman dengan SweetAlert2
        const formCreateLoan = document.getElementById('form-create-loan');
        if (formCreateLoan) {
            formCreateLoan.addEventListener('submit', function(e) {
                const rows = itemsContainer ? itemsContainer.querySelectorAll('.modal-item-row') : [];
                if (rows.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silahkan tambahkan minimal satu barang untuk dipinjam.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                let hasEmptyItem = false;
                let hasInvalidQty = false;

                rows.forEach(r => {
                    const sel = r.querySelector('.modal-item-select');
                    const qty = r.querySelector('.modal-item-qty');
                    if (!sel || !sel.value) hasEmptyItem = true;
                    if (!qty || parseInt(qty.value, 10) < 1) hasInvalidQty = true;
                });

                if (hasEmptyItem) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Terdapat baris barang yang belum dipilih. Silahkan tentukan barang inventaris atau hapus baris kosong.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }

                if (hasInvalidQty) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }
            });
        }

        // --- HANDLER MODAL DETAIL PEMINJAMAN (POPUP) ---
        const loanDetailModal = document.getElementById('loanDetailModal');
        if (loanDetailModal) {
            loanDetailModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const loanData = JSON.parse(btn.getAttribute('data-loan'));

                document.getElementById('detail_loan_code').textContent = loanData.loan_code;
                document.getElementById('detail_borrower_institution').textContent = loanData.borrower_institution;
                document.getElementById('detail_borrower_pic').textContent = loanData.borrower_pic;
                document.getElementById('detail_borrower_contact').textContent = loanData.borrower_contact;
                document.getElementById('detail_recorder_name').textContent = loanData.recorder_name;
                document.getElementById('detail_borrow_date').textContent = loanData.borrow_date;
                document.getElementById('detail_due_date').textContent = loanData.due_date;
                document.getElementById('detail_return_date').textContent = loanData.return_date || '— (Belum Selesai)';
                document.getElementById('detail_notes').textContent = loanData.notes;
                document.getElementById('detail_print_bast_btn').href = loanData.print_url;

                // Status badge
                const statusBadge = document.getElementById('detail_status_badge');
                if (loanData.computed_status === 'completed') {
                    statusBadge.className = 'badge bg-success ms-2';
                    statusBadge.textContent = 'Selesai';
                } else if (loanData.computed_status === 'completed_late') {
                    statusBadge.className = 'badge bg-warning text-dark ms-2';
                    statusBadge.textContent = 'Selesai Terlambat';
                } else if (loanData.computed_status === 'overdue') {
                    statusBadge.className = 'badge bg-danger ms-2';
                    statusBadge.textContent = 'Terlambat';
                } else {
                    statusBadge.className = 'badge bg-warning-subtle text-warning-emphasis ms-2';
                    statusBadge.textContent = 'Aktif';
                }

                // Render Items Tbody
                const tbody = document.getElementById('detail_items_tbody');
                tbody.innerHTML = '';
                loanData.items.forEach(item => {
                    const tr = document.createElement('tr');
                    const isLunas = item.remaining_qty === 0;
                    const itemImageHtml = item.image 
                        ? `<img src="${item.image}" alt="${item.name}" class="rounded border me-2" style="width:34px; height:34px; object-fit:cover; vertical-align:middle;">` 
                        : `<span class="badge bg-light text-secondary border me-2" style="font-size:0.65rem; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle;">No Pic</span>`;

                    tr.innerHTML = `
                        <td class="ps-3"><span style="font-family:monospace; color:#2563eb; font-weight:600;">${item.sku}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${itemImageHtml}
                                <span class="fw-semibold text-dark">${item.name}</span>
                            </div>
                        </td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-center text-muted">${item.returned_qty}</td>
                        <td class="text-center fw-bold ${isLunas ? 'text-success' : 'text-danger'}">${item.remaining_qty}</td>
                        <td class="text-center pe-3">
                            <span class="badge ${isLunas ? 'bg-success' : 'bg-danger'}">${isLunas ? 'Lunas' : 'Belum Lunas'}</span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Bukti Awal Pinjam
                document.getElementById('form_upload_borrow_proof').action = loanData.borrow_proof_url;
                const borrowImgView = document.getElementById('detail_borrow_proof_view');
                const borrowImg = document.getElementById('detail_borrow_proof_img');
                const borrowBadge = document.getElementById('detail_borrow_proof_badge');
                if (loanData.borrow_proof) {
                    borrowImg.src = loanData.borrow_proof;
                    borrowImgView.style.display = 'block';
                    borrowBadge.className = 'badge bg-success';
                    borrowBadge.textContent = 'Terlampir';
                } else {
                    borrowImg.src = '';
                    borrowImgView.style.display = 'none';
                    borrowBadge.className = 'badge bg-secondary';
                    borrowBadge.textContent = 'Belum Ada';
                }

                // Bukti Kembali
                document.getElementById('form_upload_return_proof').action = loanData.return_proof_url;
                const returnImgView = document.getElementById('detail_return_proof_view');
                const returnImg = document.getElementById('detail_return_proof_img');
                const returnBadge = document.getElementById('detail_return_proof_badge');
                if (loanData.return_proof) {
                    returnImg.src = loanData.return_proof;
                    returnImgView.style.display = 'block';
                    returnBadge.className = 'badge bg-success';
                    returnBadge.textContent = 'Terlampir';
                } else {
                    returnImg.src = '';
                    returnImgView.style.display = 'none';
                    returnBadge.className = 'badge bg-secondary';
                    returnBadge.textContent = 'Belum Ada';
                }

                // Validasi Pencegatan SweetAlert2 untuk Bukti Pengembalian
                const formUploadReturnProof = document.getElementById('form_upload_return_proof');
                formUploadReturnProof.onsubmit = function (e) {
                    if (!loanData.is_all_returned || (loanData.computed_status !== 'completed' && loanData.computed_status !== 'completed_late')) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'semua barang harus dikembalikan jika ingin meng-upload gambar pengembalian, silahkan periksa kembali',
                            confirmButtonColor: '#2563eb'
                        });
                        return false;
                    }
                };

                // Section Pengembalian di dalam Modal
                const returnSection = document.getElementById('detail_return_section');
                const returnContainer = document.getElementById('modal_return_items_container');
                const formModalReturn = document.getElementById('form_modal_return');
                const pendingItems = loanData.items.filter(i => i.remaining_qty > 0);

                if (loanData.can_return && pendingItems.length > 0) {
                    formModalReturn.action = loanData.return_url;
                    returnContainer.innerHTML = '';
                    pendingItems.forEach((pi, idx) => {
                        const row = document.createElement('div');
                        row.className = 'row g-2 align-items-center mb-2 item-return-row';
                        row.setAttribute('data-sku', (pi.sku || '').toUpperCase());

                        const itemThumb = pi.image 
                            ? `<img src="${pi.image}" alt="${pi.name}" class="rounded border me-2" style="width:36px; height:36px; object-fit:cover;">` 
                            : `<span class="badge bg-light text-secondary border me-2" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; font-size:0.65rem;">No Pic</span>`;

                        row.innerHTML = `
                            <div class="col-md-7 d-flex align-items-center">
                                <input type="hidden" name="items[${idx}][loan_item_id]" value="${pi.id}">
                                ${itemThumb}
                                <div>
                                    <div class="fw-semibold text-dark small">${pi.name}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        SKU: <span class="font-monospace text-primary fw-semibold">${pi.sku}</span> | Sisa: <strong>${pi.remaining_qty}</strong> unit
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Qty</span>
                                    <input type="number" name="items[${idx}][return_qty]" class="form-control modal-return-qty-input" data-max="${pi.remaining_qty}" value="0" min="0" max="${pi.remaining_qty}">
                                    <button type="button" class="btn btn-outline-secondary btn-fill-max" data-max="${pi.remaining_qty}">Semua</button>
                                </div>
                            </div>
                        `;
                        returnContainer.appendChild(row);
                    });

                    // Scanner USB / Keyboard barcode handler di dalam modal
                    const scanInput = document.getElementById('modal_scan_barcode_input');
                    const scanFeedback = document.getElementById('modal_scan_feedback');
                    if (scanInput) {
                        scanInput.value = '';
                        scanInput.onkeydown = function (e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const code = this.value.trim().toUpperCase();
                                if (!code) return;

                                const matchedRow = returnContainer.querySelector(`.item-return-row[data-sku="${code}"]`);
                                if (matchedRow) {
                                    const inp = matchedRow.querySelector('.modal-return-qty-input');
                                    const maxVal = parseInt(inp.getAttribute('data-max'), 10);
                                    let currentVal = parseInt(inp.value, 10) || 0;

                                    if (currentVal < maxVal) {
                                        inp.value = currentVal + 1;
                                        scanFeedback.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-check"></i> Berhasil scan [${code}]: Jumlah kembali dinaikkan menjadi ${inp.value}.</span>`;
                                    } else {
                                        scanFeedback.innerHTML = `<span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> SKU [${code}] sudah mencapai batas maksimal sisa pinjaman (${maxVal}).</span>`;
                                    }
                                } else {
                                    scanFeedback.innerHTML = `<span class="text-danger fw-bold"><i class="fa-solid fa-xmark"></i> SKU [${code}] tidak ditemukan dalam daftar barang pinjaman ini.</span>`;
                                }
                                this.value = '';
                            }
                        };
                    }

                    // Tombol isi semua per row
                    returnContainer.querySelectorAll('.btn-fill-max').forEach(b => {
                        b.addEventListener('click', function() {
                            const inp = this.closest('.input-group').querySelector('.modal-return-qty-input');
                            inp.value = this.getAttribute('data-max');
                        });
                    });

                    // Tombol kembalikan semua sisa sekaligus
                    document.getElementById('btn_modal_return_all').onclick = function() {
                        returnContainer.querySelectorAll('.modal-return-qty-input').forEach(inp => {
                            inp.value = inp.getAttribute('data-max');
                        });
                    };

                    formModalReturn.onsubmit = function (e) {
                        let totalReturn = 0;
                        returnContainer.querySelectorAll('.modal-return-qty-input').forEach(inp => {
                            totalReturn += parseInt(inp.value || 0, 10);
                        });
                        if (totalReturn <= 0) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: 'Masukkan jumlah barang yang akan dikembalikan minimal 1 unit.',
                                confirmButtonColor: '#2563eb'
                            });
                            return false;
                        }
                    };

                    returnSection.style.display = 'block';
                } else {
                    returnSection.style.display = 'none';
                }
            });

            // Handler Kamera Barcode Scanner di dalam Modal
            let modalHtml5QrCode = null;
            const btnToggleCamera = document.getElementById('btn_toggle_modal_camera');
            const cameraBox = document.getElementById('modal_camera_reader_box');
            const scanFeedback = document.getElementById('modal_scan_feedback');

            if (btnToggleCamera) {
                btnToggleCamera.addEventListener('click', function () {
                    if (cameraBox.style.display === 'none') {
                        cameraBox.style.display = 'block';
                        btnToggleCamera.innerHTML = '<i class="fa-solid fa-stop"></i> Stop Kamera';
                        btnToggleCamera.className = 'btn btn-danger';

                        if (typeof Html5Qrcode !== 'undefined') {
                            modalHtml5QrCode = new Html5Qrcode("modal_reader");
                            modalHtml5QrCode.start(
                                { facingMode: "environment" },
                                { fps: 10, qrbox: { width: 220, height: 220 } },
                                (decodedText) => {
                                    const code = decodedText.trim().toUpperCase();
                                    const matchedRow = document.querySelector(`.item-return-row[data-sku="${code}"]`);
                                    if (matchedRow) {
                                        const inp = matchedRow.querySelector('.modal-return-qty-input');
                                        const maxVal = parseInt(inp.getAttribute('data-max'), 10);
                                        let currentVal = parseInt(inp.value, 10) || 0;

                                        if (currentVal < maxVal) {
                                            inp.value = currentVal + 1;
                                            scanFeedback.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-check"></i> [Kamera] Berhasil scan ${code}: Qty dinaikkan jadi ${inp.value}.</span>`;
                                        } else {
                                            scanFeedback.innerHTML = `<span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> [Kamera] SKU ${code} sudah mencapai max.</span>`;
                                        }
                                    } else {
                                        scanFeedback.innerHTML = `<span class="text-danger fw-bold"><i class="fa-solid fa-xmark"></i> [Kamera] SKU ${code} tidak ada di pinjaman ini.</span>`;
                                    }
                                },
                                (errorMessage) => {
                                    // Mengabaikan frame tanpa barcode
                                }
                            ).catch(err => {
                                scanFeedback.innerHTML = `<span class="text-danger">Gagal membuka kamera: ${err}</span>`;
                            });
                        }
                    } else {
                        stopModalCamera();
                    }
                });
            }

            function stopModalCamera() {
                if (modalHtml5QrCode) {
                    modalHtml5QrCode.stop().then(() => {
                        modalHtml5QrCode.clear();
                        modalHtml5QrCode = null;
                    }).catch(() => {});
                }
                cameraBox.style.display = 'none';
                btnToggleCamera.innerHTML = '<i class="fa-solid fa-camera"></i> Kamera';
                btnToggleCamera.className = 'btn btn-outline-primary';
            }

            loanDetailModal.addEventListener('hidden.bs.modal', function () {
                stopModalCamera();
            });
        }
    });
</script>
@endpush

@endsection
