@extends('layouts.dashboard')

@section('title', 'Tambah Indikator Kinerja')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('indikator.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Kode Route (Key)</label>
                            <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" placeholder="Contoh: 1.1.1.1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Kode Tujuan</label>
                            <input type="text" name="kode_tujuan" class="form-control" value="{{ old('kode_tujuan') }}" placeholder="Contoh: T1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Kode Sasaran</label>
                            <input type="text" name="kode_sasaran" class="form-control" value="{{ old('kode_sasaran') }}" placeholder="Contoh: 1.1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Kode Indikator Kinerja</label>
                            <input type="text" name="kode_indikator_kinerja" class="form-control" value="{{ old('kode_indikator_kinerja') }}" placeholder="Contoh: 1.1.1">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Tujuan</label>
                            <textarea name="tujuan" class="form-control" rows="2" required>{{ old('tujuan') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Sasaran</label>
                            <textarea name="sasaran" class="form-control" rows="2" required>{{ old('sasaran') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Indikator Kinerja</label>
                            <input type="text" name="indikator_kinerja" class="form-control" value="{{ old('indikator_kinerja') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Jenis Indikator</label>
                            <select name="jenis_indikator" class="form-select" required>
                                <option value="IKU" {{ old('jenis_indikator') == 'IKU' ? 'selected' : '' }}>IKU</option>
                                <option value="Proksi" {{ old('jenis_indikator') == 'Proksi' ? 'selected' : '' }}>Proksi</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Periode</label>
                            <select name="periode" class="form-select" required>
                                <option value="Triwulanan" {{ old('periode') == 'Triwulanan' ? 'selected' : '' }}>Triwulanan</option>
                                <option value="Tahunan" {{ old('periode') == 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tipe Indikator</label>
                            <select name="tipe" class="form-select">
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="%" {{ old('tipe') == '%' ? 'selected' : '' }}>%</option>
                                <option value="Non %" {{ old('tipe') == 'Non %' ? 'selected' : '' }}>Non %</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="%, Dokumen, Laporan, dll" value="{{ old('satuan') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Target Tahunan</label>
                            <input type="number" step="0.01" name="target_tahunan" class="form-control" value="{{ old('target_tahunan') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ old('tahun', 2026) }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Penanggung Jawab (PIC)</label>
                            <select name="pic_id" class="form-select select2">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawais as $p)
                                    <option value="{{ $p->id }}" {{ old('pic_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->nip ?? 'No NIP' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dasar Hitung & Basis Data Realisasi</label>
                        <textarea name="dasar_hitung" class="form-control tinymce-editor" rows="3" placeholder="Jelaskan dasar perhitungan dan basis data yang digunakan...">{{ old('dasar_hitung') }}</textarea>

                        <div class="form-text">Contoh: Laporan Realisasi Anggaran Divisi Keuangan per 31 Desember.</div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">Simpan Indikator</button>
                        <a href="{{ route('indikator.index') }}" class="btn btn-light ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        window.initTinyMCE('.tinymce-editor');
    });
</script>
@endsection

