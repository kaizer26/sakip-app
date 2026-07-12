@extends('layouts.dashboard')

@section('title', 'Template Word')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="mb-0 fw-bold text-gray-800">
                <i class="fas fa-file-word text-primary me-2"></i> Ekspor Template Word
            </h4>
            <p class="text-muted small mb-0 mt-1">Pilih jenis dokumen yang ingin Anda cetak.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <label class="form-label fw-bold">Pilih Jenis Dokumen</label>
            <select id="documentType" class="form-select form-select-lg mb-3">
                <option value="">-- Silakan Pilih Dokumen --</option>
                <option value="notulen_capaian">Notulen Capaian Kinerja</option>
                <option value="notulen_pk">Notulen PK</option>
                <option value="dokumen_sumber">Dokumen Sumber</option>
                <option value="bukti_tindak_lanjut">Bukti Tindak Lanjut</option>
            </select>
        </div>
    </div>

    <!-- Form Notulen Capaian Kinerja -->
    <div id="form-notulen_capaian" class="card border-0 shadow-sm rounded-4 document-form" style="display: none;">
        <div class="card-header bg-white border-bottom pt-4 pb-3">
            <h5 class="card-title fw-bold text-primary mb-0">
                <i class="fas fa-file-alt me-2"></i> Form Notulen Capaian Kinerja
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 small rounded-3 mb-4">
                <i class="fas fa-info-circle me-2"></i> Dokumen ini menggunakan template <strong>notulen_capkin.docx</strong> yang ada di storage/app/templates.
            </div>
            
            <form action="{{ route('template.word.export.notulen') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Triwulan</label>
                        <select name="triwulan" class="form-select" required>
                            <option value="1">Triwulan I</option>
                            <option value="2">Triwulan II</option>
                            <option value="3">Triwulan III</option>
                            <option value="4">Triwulan IV</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Rapat</label>
                        <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Waktu Rapat</label>
                        <input type="text" name="waktu" class="form-control" placeholder="Contoh: 09.00 - Selesai" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Tempat</label>
                        <input type="text" name="tempat" class="form-control" placeholder="Contoh: Ruang Rapat Utama" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pimpinan Rapat</label>
                        <select name="pimpinan_id" class="form-select select2" required>
                            <option value="">-- Pilih Pimpinan Rapat --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->jabatan }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Notulis</label>
                        <select name="notulis_id" class="form-select select2" required>
                            <option value="">-- Pilih Notulis --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->jabatan }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-print me-1"></i> Cetak / Export
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Placeholder Segera Hadir -->
    <div id="form-coming_soon" class="card border-0 shadow-sm rounded-4 bg-light opacity-75 document-form" style="display: none;">
        <div class="card-body p-5 text-center d-flex flex-column justify-content-center">
            <i class="fas fa-tools fs-1 text-muted mb-3"></i>
            <h5 class="fw-bold text-muted">Segera Hadir</h5>
            <p class="text-muted mb-0">
                Fitur cetak untuk dokumen ini sedang dalam tahap pengembangan.
            </p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        $('#documentType').on('change', function() {
            $('.document-form').hide();
            let selected = $(this).val();
            
            if (selected === 'notulen_capaian') {
                $('#form-notulen_capaian').fadeIn();
            } else if (selected !== '') {
                $('#form-coming_soon').fadeIn();
            }
        });
    });
</script>
@endsection
