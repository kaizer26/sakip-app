@extends('layouts.dashboard')

@section('title', 'Edit Master RO')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="mb-0 fw-bold text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i> Edit Rincian Output
            </h4>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('tabel-ro.index') }}" class="btn btn-light border rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-circle me-2"></i>Terdapat kesalahan:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('tabel-ro.update', $tabel_ro) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Informasi Utama</h5>
                <div class="row g-4 mb-5">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Pilih Indikator Kinerja <span class="text-danger">*</span></label>
                        <select name="indikator_id" class="form-select select2" required>
                            <option value="">-- Cari dan Pilih Indikator --</option>
                            @foreach($indikators as $ind)
                                <option value="{{ $ind->id }}" {{ old('indikator_id', $tabel_ro->indikator_id) == $ind->id ? 'selected' : '' }}>
                                    [{{ $ind->kode }}] {{ $ind->indikator_kinerja }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $tabel_ro->tahun) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Triwulan <span class="text-danger">*</span></label>
                        <select name="triwulan" class="form-select" required>
                            <option value="1" {{ old('triwulan', $tabel_ro->triwulan) == '1' ? 'selected' : '' }}>Triwulan I</option>
                            <option value="2" {{ old('triwulan', $tabel_ro->triwulan) == '2' ? 'selected' : '' }}>Triwulan II</option>
                            <option value="3" {{ old('triwulan', $tabel_ro->triwulan) == '3' ? 'selected' : '' }}>Triwulan III</option>
                            <option value="4" {{ old('triwulan', $tabel_ro->triwulan) == '4' ? 'selected' : '' }}>Triwulan IV</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nama Rincian Output (RO) <span class="text-danger">*</span></label>
                        <input type="text" name="ro" class="form-control" value="{{ old('ro', $tabel_ro->ro) }}" placeholder="Masukkan nama Rincian Output" required>
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Target & Realisasi RO</h5>
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Realisasi Volume RO</label>
                        <input type="number" step="0.01" name="realisasi_volume_ro" class="form-control" value="{{ old('realisasi_volume_ro', $tabel_ro->realisasi_volume_ro) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Progres RO (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="progres_ro" class="form-control" value="{{ old('progres_ro', $tabel_ro->progres_ro) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Pagu Anggaran (Rp)</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pagu Awal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" name="pagu_awal" class="form-control" value="{{ old('pagu_awal', $tabel_ro->pagu_awal) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pagu Revisi</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" name="pagu_revisi" class="form-control" value="{{ old('pagu_revisi', $tabel_ro->pagu_revisi) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Realisasi Pagu</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" name="pagu_realisasi" class="form-control" value="{{ old('pagu_realisasi', $tabel_ro->pagu_realisasi) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sisa Pagu</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" name="pagu_sisa" class="form-control" value="{{ old('pagu_sisa', $tabel_ro->pagu_sisa) }}">
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
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
    });
</script>
@endsection
