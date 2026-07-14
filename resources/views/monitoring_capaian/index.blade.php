@extends('layouts.dashboard')

@section('title', 'Monitoring Capaian Kinerja')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Monitoring Pengisian Capaian Kinerja</h4>
            <div class="text-muted small">Pantau kelengkapan pengisian data capaian untuk setiap indikator.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-tasks me-2"></i>Status Kelengkapan Data</h6>
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('monitoring-capaian.index') }}" method="GET" class="d-flex gap-2">
                    <input type="number" name="tahun" class="form-control form-control-sm rounded-pill px-3 shadow-sm border-light-subtle" value="{{ $tahun }}" style="width: 100px;">
                    <select name="triwulan" class="form-select form-select-sm rounded-pill px-3 shadow-sm border-light-subtle" onchange="this.form.submit()">
                        <option value="1" {{ $triwulan == 1 ? 'selected' : '' }}>Triwulan I</option>
                        <option value="2" {{ $triwulan == 2 ? 'selected' : '' }}>Triwulan II</option>
                        <option value="3" {{ $triwulan == 3 ? 'selected' : '' }}>Triwulan III</option>
                        <option value="4" {{ $triwulan == 4 ? 'selected' : '' }}>Triwulan IV</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="monitoringTable" style="font-size: 0.85rem;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2" width="40">No</th>
                            <th rowspan="2" style="min-width: 250px;">Indikator</th>
                            <th rowspan="2">Realisasi TW</th>
                            <th colspan="2">Rumus Target</th>
                            <th colspan="3">Narasi & Argumen</th>
                            <th colspan="4">Analisis & Tindak Lanjut</th>
                            <th colspan="2">Bukti Dukung</th>
                        </tr>
                        <tr>
                            <th class="fw-normal">Nilai X</th>
                            <th class="fw-normal">Nilai Y</th>
                            <th class="fw-normal">Dasar Hitung</th>
                            <th class="fw-normal">Argumen Logis</th>
                            <th class="fw-normal">Penjelasan</th>
                            
                            <th class="fw-normal">Kendala</th>
                            <th class="fw-normal">Solusi</th>
                            <th class="fw-normal">RTL</th>
                            <th class="fw-normal">PIC & Batas</th>
                            
                            <th class="fw-normal">Kinerja</th>
                            <th class="fw-normal">RTL (TW Sblm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indikators as $ind)
                            @php 
                                $capaian = $capaians->get($ind->id);
                                $realisasi = $ind->realisasis->first();
                                $analisis = $ind->analisis->first();
                                $hasTindakLanjut = $analisis && $analisis->tindakLanjuts->isNotEmpty();
                                $firstTl = $hasTindakLanjut ? $analisis->tindakLanjuts->first() : null;
                                
                                $isComplete = function($val) {
                                    return ($val !== null && $val !== '' && $val !== false) 
                                        ? '<i class="fas fa-check-circle text-success fs-5"></i>' 
                                        : '<i class="fas fa-times-circle text-danger opacity-50"></i>';
                                };
                                
                                $hasRumus = $ind->definisi_x || $ind->definisi_y;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2 mb-1">{{ $ind->kode }}</span>
                                    <div class="fw-bold text-dark">{{ $ind->indikator_kinerja }}</div>
                                </td>
                                
                                <td class="text-center">{!! $isComplete($realisasi->realisasi_kumulatif ?? null) !!}</td>
                                
                                @if($hasRumus)
                                    <td class="text-center">{!! $isComplete($realisasi->realisasi_x ?? null) !!}</td>
                                    <td class="text-center">{!! $isComplete($realisasi->realisasi_y ?? null) !!}</td>
                                @else
                                    <td class="text-center text-muted bg-light">-</td>
                                    <td class="text-center text-muted bg-light">-</td>
                                @endif
                                
                                <td class="text-center">{!! $isComplete($capaian->dasar_hitung ?? null) !!}</td>
                                <td class="text-center">{!! $isComplete($capaian->argumen_logis ?? null) !!}</td>
                                <td class="text-center">{!! $isComplete($capaian->penjelasan_lainnya ?? null) !!}</td>
                                
                                <td class="text-center">{!! $isComplete($firstTl->kendala ?? null) !!}</td>
                                <td class="text-center">{!! $isComplete($firstTl->solusi ?? null) !!}</td>
                                <td class="text-center">{!! $isComplete($firstTl->rtl ?? null) !!}</td>
                                <td class="text-center">
                                    @php
                                        $picBatas = ($firstTl->pic ?? null) && ($firstTl->batas_waktu ?? null);
                                    @endphp
                                    {!! $isComplete($picBatas) !!}
                                </td>
                                
                                <td class="text-center">{!! $isComplete($capaian->link_bukti_kinerja ?? null) !!}</td>
                                <td class="text-center">{!! $isComplete($capaian->link_bukti_tindak_lanjut ?? null) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada indikator kinerja.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
