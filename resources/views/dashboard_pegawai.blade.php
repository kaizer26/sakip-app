@extends('layouts.dashboard')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden mb-0" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);">
            <div class="card-body p-4 p-md-5 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-2">Halo, {{ auth()->user()->name }}! 👋</h2>
                        <p class="mb-0 opacity-75">Selamat datang kembali di KinerjaApp. Pantau progres indikator dan laporkan aktivitas atau kendala Anda di sini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Reporting Forms Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-edit me-2"></i> Form Pelaporan Aktivitas & Kendala</h6>
            </div>
            <div class="card-body pt-0 d-flex flex-column" style="min-height: 320px;">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Tahun</label>
                        <select class="form-select form-select-sm bg-light filter-period" id="filterTahun">
                            <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                            <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                            <option value="2027" {{ $tahun == 2027 ? 'selected' : '' }}>2027</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Triwulan</label>
                        <select class="form-select form-select-sm bg-light filter-period" id="filterTriwulan">
                            <option value="1" {{ $triwulan == 1 ? 'selected' : '' }}>Triwulan I</option>
                            <option value="2" {{ $triwulan == 2 ? 'selected' : '' }}>Triwulan II</option>
                            <option value="3" {{ $triwulan == 3 ? 'selected' : '' }}>Triwulan III</option>
                            <option value="4" {{ $triwulan == 4 ? 'selected' : '' }}>Triwulan IV</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Wilayah</label>
                        <select class="form-select form-select-sm bg-light" disabled>
                            <option>[6305] Tapin</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted fw-bold mb-1">Unit Kerja</label>
                        <select class="form-select form-select-sm bg-light" disabled>
                            <option>[92800] BPS Kabupaten/Kota</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-1">Pilih Indikator Kinerja Utama (IKU)</label>
                        <select class="form-select select2-iku border-2" id="selectIKU">
                            <option value="" disabled selected>-- Pilih Indikator --</option>
                            @foreach($reportingIndikators as $i)
                                <option value="{{ $i->id }}">{{ $i->kode }} — {{ $i->indikator_kinerja }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold mb-1">Pilih Kegiatan</label>
                        <select class="form-select select2-kegiatan border-2" id="selectKegiatan" disabled>
                            <option value="" disabled selected>-- Pilih Indikator Terlebih Dahulu --</option>
                        </select>
                    </div>
                </div>

                <div class="flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="row g-3 d-none mt-0" id="actionCards">
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border-start border-4 border-success shadow-sm mb-0 bg-light bg-opacity-50 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-tasks text-success fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 small">Input Aktivitas</h6>
                                        <p class="text-muted extra-small mb-0">Catat tahapan kegiatan.</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm w-100 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAktivitas">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Aktivitas
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 p-3 border-start border-4 border-danger shadow-sm mb-0 bg-light bg-opacity-50 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-exclamation-triangle text-danger fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 small">Input Kendala</h6>
                                        <p class="text-muted extra-small mb-0">Laporkan hambatan.</p>
                                    </div>
                                </div>
                                <button class="btn btn-outline-danger btn-sm w-100 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKendala">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Kendala
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center py-2" id="placeHolder">
                        <img src="https://illustrations.popsy.co/gray/work-from-home.svg" alt="select" width="60" class="mb-2 opacity-50">
                        <h6 class="text-muted small">Silakan pilih IKU & Kegiatan untuk memulai pengisian laporan</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-4">
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                                <i class="fas fa-tasks fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold">Kontribusi Aktivitas</div>
                                <div class="fs-4 fw-bold text-dark">{{ $summary['personal_activities'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                                <i class="fas fa-bullseye fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold">Tanggung Jawab PIC</div>
                                <div class="fs-4 fw-bold text-dark">{{ $summary['total_pic'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="text-muted small fw-bold mb-2">Status Indikator Saya</div>
                        <div class="d-flex gap-2">
                            <div class="bg-success bg-opacity-10 text-success border border-success-subtle rounded-3 p-2 flex-grow-1 text-center">
                                <div class="fw-bold">{{ $summary['pic_hijau'] }}</div>
                                <div class="extra-small">Oke</div>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-3 p-2 flex-grow-1 text-center">
                                <div class="fw-bold">{{ $summary['pic_critical'] }}</div>
                                <div class="extra-small">Kritis</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">Daftar Indikator PIC Anda (Tahun {{ $tahun }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="myIndikatorTable">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Indikator Kinerja</th>
                        <th width="300">Progress Capaian Tahunan</th>
                        <th width="100" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikators as $i)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark mb-1">{{ $i->indikator_kinerja }}</div>
                            <div class="extra-small text-muted">
                                <span class="badge bg-light text-muted border fw-normal me-2">{{ $i->kode }}</span>
                                <i class="fas fa-crosshairs me-1"></i>{{ $i->sasaran }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 rounded-pill" style="height: 8px; background-color: rgba(0,0,0,0.05);">
                                    <div class="progress-bar bg-{{ $i->status_warna }} rounded-pill" role="progressbar" 
                                         style="width: {{ min(100, $i->capaian_tahunan) }}%"></div>
                                </div>
                                <span class="ms-3 small fw-bold text-{{ $i->status_warna }}">{{ number_format($i->capaian_tahunan, 1) }}%</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $i->status_warna }} bg-opacity-10 text-{{ $i->status_warna }} border border-{{ $i->status_warna }}-subtle rounded-pill px-3">
                                {{ $i->capaian_tahunan >= 100 ? 'Sesuai' : ($i->capaian_tahunan >= 80 ? 'Waspada' : 'Kritis') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fs-4 mb-2 d-block"></i>
                            <p class="mb-0">Anda belum ditugaskan sebagai PIC untuk indikator kinerja mana pun.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Aktivitas -->
<div class="modal fade" id="modalAktivitas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('public.aktivitas.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="indikator_id" class="hidden-iku">
            <input type="hidden" name="kegiatan_id" class="hidden-kegiatan">
            <input type="hidden" name="triwulan" value="{{ $triwulan }}">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah / Edit Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold mb-1">Pilih Pegawai (PIC)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary bg-opacity-10 text-primary border-primary-subtle"><i class="fas fa-user-check"></i></span>
                        <select name="pegawai_nip" class="form-select border-primary-subtle" required>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->nip ?? $p->email_bps }}" {{ (auth()->user()->pegawai->nip ?? '') == $p->nip ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3" id="tahapan_wrapper">
                    <label class="form-label small fw-bold mb-1">Tahap yang Sedang Dikerjakan</label>
                    <select name="tahapan" id="tahapan_select" class="form-select select2-modal" required>
                        <option value="" disabled selected>-- Pilih Tahapan --</option>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Uraian Aktivitas</label>
                    <textarea name="uraian" class="form-control rounded-3" rows="4" placeholder="Deskripsikan pekerjaan yang dilakukan..." required></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold small">Lampiran Bukti (Boleh lebih dari 1)</label>
                    <input type="file" name="lampiran[]" class="form-control rounded-3" multiple>
                    <small class="text-muted extra-small">Format: PDF, JPG, PNG, DOCX, XLSX. Maks 10MB per file.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Aktivitas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kendala -->
<div class="modal fade" id="modalKendala" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('public.kendala.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="indikator_id" class="hidden-iku">
            <input type="hidden" name="kegiatan_id" class="hidden-kegiatan">
            <input type="hidden" name="triwulan" value="{{ $triwulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Laporkan Kendala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold mb-1">Pilih Pegawai (PIC)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-danger bg-opacity-10 text-danger border-danger-subtle"><i class="fas fa-user-ninja"></i></span>
                        <select name="pegawai_nip" class="form-select border-danger-subtle" required>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->nip ?? $p->email_bps }}" {{ (auth()->user()->pegawai->nip ?? '') == $p->nip ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="kendala-container">
                    <div class="kendala-row border rounded-3 p-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-danger">Kendala #1</span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-kendala d-none"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-1">Kendala yang Dihadapi</label>
                            <textarea name="kendala[]" class="form-control rounded-3" rows="2" placeholder="Jelaskan hambatan secara spesifik..." required></textarea>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="$(this).closest('.kendala-row').find('.solusi-collapse').collapse('toggle')">
                                <i class="fas fa-plus"></i> Tambah Solusi yang Telah Dilakukan
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill" onclick="$(this).closest('.kendala-row').find('.rtl-collapse').collapse('toggle')">
                                <i class="fas fa-plus"></i> Buat Rencana Tindak Lanjut (RTL)
                            </button>
                        </div>

                        <div class="collapse solusi-collapse mb-3">
                            <div class="p-3 border rounded-3 bg-success bg-opacity-10 border-success-subtle">
                                <label class="form-label small fw-bold text-success mb-1">Solusi yang Telah Dilakukan</label>
                                <textarea name="solusi[]" class="form-control rounded-3 border-success-subtle" rows="2" placeholder="Apa yang sudah dilakukan?"></textarea>
                            </div>
                        </div>

                        <div class="collapse rtl-collapse">
                            <div class="p-3 border rounded-3 bg-warning bg-opacity-10 border-warning-subtle">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark mb-1">Rencana Tindak Lanjut (RTL)</label>
                                    <textarea name="rencana_tindak_lanjut[]" class="form-control rounded-3 border-warning-subtle" rows="2" placeholder="Apa rencana ke depan?"></textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 rtl-field">
                                        <label class="form-label small fw-bold text-dark mb-1">PIC Tindak Lanjut</label>
                                        <select name="pic_tindak_lanjut[]" class="form-select select2-modal pic_select_kendala border-warning-subtle">
                                            <option value="" disabled>-- Pilih PIC --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 rtl-field">
                                        <label class="form-label small fw-bold text-dark mb-1">Batas Waktu RTL</label>
                                        <input type="date" name="batas_waktu[]" class="form-control rounded-3 border-warning-subtle" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddKendala">
                        <i class="fas fa-plus me-1"></i> Tambah Kendala Lain
                    </button>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold mb-1 text-center d-block">Tingkat Keparahan (Severity)</label>
                    <div class="d-flex gap-2">
                        <div class="form-check border p-2 rounded-3 px-3 flex-grow-1 clickable-card severity-card text-center">
                            <input class="form-check-input d-none" type="radio" name="severity" id="sevLow" value="Low" checked>
                            <label class="form-check-label w-100" for="sevLow">Low</label>
                        </div>
                        <div class="form-check border p-2 rounded-3 px-3 flex-grow-1 clickable-card severity-card text-center">
                            <input class="form-check-input d-none" type="radio" name="severity" id="sevMed" value="Medium">
                            <label class="form-check-label w-100" for="sevMed">Medium</label>
                        </div>
                        <div class="form-check border p-2 rounded-3 px-3 flex-grow-1 clickable-card severity-card border-danger text-danger text-center">
                            <input class="form-check-input d-none" type="radio" name="severity" id="sevHigh" value="High">
                            <label class="form-check-label w-100 fw-bold" for="sevHigh">High</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('styles')
<style>
    .extra-small { font-size: 0.75rem; }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 10px;
        padding: 0.4rem;
        height: auto;
    }
    .clickable-card { cursor: pointer; transition: all 0.2s; }
    .clickable-card:hover { background-color: #f8f9fa; }
    .severity-card.active {
        background-color: #eef2ff;
        border-color: #4361ee !important;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.1);
    }
    .severity-card.border-danger.active {
        background-color: #fff1f2;
        border-color: #e71d36 !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2-iku, .select2-kegiatan').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Filter Period Change Logic
        $('.filter-period').on('change', function () {
            const tahun = $('#filterTahun').val();
            const triwulan = $('#filterTriwulan').val();
            window.location.href = `{{ route('dashboard') }}?tahun=${tahun}&triwulan=${triwulan}`;
        });

        const currentUserId = {{ auth()->user()->pegawai_id ?: 0 }};
        const currentUserName = "{{ $pegawai->nama ?? auth()->user()->name }}";

        // IKU Change Logic
        $('#selectIKU').on('change', function () {
            const ikuId = $(this).val();
            const $kegS = $('#selectKegiatan');
            
            $kegS.prop('disabled', true).html('<option value="" disabled selected>-- Memuat Kegiatan... --</option>');
            $('#actionCards').addClass('d-none');
            $('#placeHolder').removeClass('d-none');

            if (ikuId) {
                $.get(`/api/kegiatan/${ikuId}`, function (data) {
                    $kegS.html('<option value="" disabled selected>-- Pilih Kegiatan --</option>');
                    if (data.length > 0) {
                        data.forEach(function (k) {
                            $kegS.append($('<option></option>')
                                .attr('value', k.id)
                                .text(k.nama_kegiatan)
                                .attr('data-tahapan', JSON.stringify(k.tahapan_json))
                                .attr('data-ketua', k.ketua_tim_id)
                                .attr('data-anggotas', JSON.stringify(k.anggotas_list)));
                        });
                        $kegS.prop('disabled', false);
                    } else {
                        $kegS.html('<option value="" disabled selected>-- Tidak ada kegiatan --</option>');
                    }
                });
            }
        });

        // Kegiatan Change Logic
        $('#selectKegiatan').on('change', function () {
            if ($(this).val()) {
                $('#actionCards').removeClass('d-none');
                $('#placeHolder').addClass('d-none');
            } else {
                $('#actionCards').addClass('d-none');
                $('#placeHolder').removeClass('d-none');
            }
        });

        // Modal Data Preparation
        $('#modalAktivitas, #modalKendala').on('show.bs.modal', function () {
            const ikuId = $('#selectIKU').val();
            const kegId = $('#selectKegiatan').val();
            const $selectedKeg = $('#selectKegiatan').find(':selected');
            const isAktivitas = $(this).attr('id') === 'modalAktivitas';

            $('.hidden-iku').val(ikuId);
            $('.hidden-kegiatan').val(kegId);

            if (isAktivitas) {
                const $tahS = $('#tahapan_select');
                $tahS.html('<option value="">-- Pilih Tahapan --</option>');
                try {
                    const tahapan = JSON.parse($selectedKeg.attr('data-tahapan'));
                    tahapan.forEach(t => $tahS.append($('<option></option>').attr('value', t).text(t)));
                } catch(e) { console.error("Error parsing tahapan", e); }
                $tahS.select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#modalAktivitas') });
            } else {
                const ketuaId = $selectedKeg.attr('data-ketua');
                const isKetua = ketuaId == currentUserId;
                const $picS = $('.pic_select_kendala');
                const $rtlText = $('textarea[name="rencana_tindak_lanjut[]"]');
                const $batasDate = $('input[name="batas_waktu[]"]');
                
                $('.rtl-field').show();

                if (isKetua) {
                    $picS.prop('disabled', false).html('<option value="">-- Pilih PIC --</option>');
                    $rtlText.prop('readonly', false).attr('placeholder', 'Apa rencana ke depan?');
                    $batasDate.prop('readonly', false);
                    
                    try {
                        // Tambahkan nama Ketua Tim sendiri sebagai opsi
                        $picS.append($('<option></option>').attr('value', currentUserName).text(currentUserName + " (Ketua Tim)"));
                        
                        const anggotas = JSON.parse($selectedKeg.attr('data-anggotas'));
                        anggotas.forEach(a => $picS.append($('<option></option>').attr('value', a.nama).text(a.nama)));
                    } catch(e) { console.error("Error parsing anggotas", e); }
                } else {
                    $picS.html(`<option value="${currentUserName}" selected>${currentUserName}</option>`).prop('disabled', true);
                    $rtlText.prop('readonly', true).val('Akan diisi oleh Ketua Tim').attr('placeholder', '');
                    $batasDate.prop('readonly', true).val('');
                }
                $picS.select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#modalKendala') });
            }
        });

        // Handle Add Kendala Row
        let kendalaCount = 1;
        $('#btnAddKendala').on('click', function() {
            kendalaCount++;
            const $template = $('.kendala-row').first().clone();
            $template.find('input, textarea').val('');
            $template.find('.badge').text('Kendala #' + kendalaCount);
            $template.find('.btn-remove-kendala').removeClass('d-none');
            
            // Re-init select2
            $template.find('.select2-container').remove();
            $template.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('tabindex').removeAttr('aria-hidden');
            
            $('#kendala-container').append($template);
            $template.find('.pic_select_kendala').select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#modalKendala') });
            
            // Set default values if non-ketua
            const $selectedKeg = $('#selectKegiatan').find(':selected');
            const ketuaId = $selectedKeg.attr('data-ketua');
            if (ketuaId != currentUserId) {
                $template.find('.pic_select_kendala').html(`<option value="${currentUserName}" selected>${currentUserName}</option>`).prop('disabled', true);
                $template.find('textarea[name="rencana_tindak_lanjut[]"]').prop('readonly', true).val('Akan diisi oleh Ketua Tim');
                $template.find('input[name="batas_waktu[]"]').prop('readonly', true);
            }
        });

        $(document).on('click', '.btn-remove-kendala', function() {
            $(this).closest('.kendala-row').remove();
        });

        // Severity selection UI
        $('.severity-card').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
            $('.severity-card').removeClass('active');
            $(this).addClass('active');
        });

        // Initialize DataTable
        @if($indikators->count() > 0)
        $('#myIndikatorTable').DataTable({
            language: window.DATATABLES_ID,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50]
        });
        @endif
    });
</script>
@endsection
