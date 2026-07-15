@extends('layouts.dashboard')

@section('title', 'Evaluasi Kinerja')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Evaluasi Kinerja - Triwulan {{ $triwulan }} - {{ $tahun }}</h4>
            <div class="text-muted small">Daftar Indikator Kinerja Utama dan Pelaporan Kendala.</div>
        </div>
        <form action="{{ route('evaluasi-kinerja.index') }}" method="GET" class="d-flex gap-1">
            <select name="tahun" class="form-select form-select-sm rounded-pill shadow-sm" onchange="this.form.submit()">
                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <select name="triwulan" class="form-select form-select-sm rounded-pill shadow-sm" onchange="this.form.submit()">
                <option value="1" {{ $triwulan == 1 ? 'selected' : '' }}>Triwulan 1</option>
                <option value="2" {{ $triwulan == 2 ? 'selected' : '' }}>Triwulan 2</option>
                <option value="3" {{ $triwulan == 3 ? 'selected' : '' }}>Triwulan 3</option>
                <option value="4" {{ $triwulan == 4 ? 'selected' : '' }}>Triwulan 4</option>
            </select>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 p-2" id="evaluasiTable" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Indikator</th>
                            <th class="text-end">Target</th>
                            <th class="text-end">Realisasi</th>
                            <th class="text-center">Capaian (%)</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($indikators as $ind)
                            @php
                                $realisasi = $ind->realisasis->first();
                                $targetField = 'target_tw' . $triwulan;
                                $targetVal = $ind->target ? $ind->target->$targetField : 0;
                                $realisasiVal = $realisasi ? $realisasi->realisasi_kumulatif : 0;
                                
                                $capaianPersen = 0;
                                if ($targetVal > 0) {
                                    $capaianPersen = ($realisasiVal / $targetVal) * 100;
                                } elseif ($targetVal == 0 && $realisasiVal > 0) {
                                    $capaianPersen = 100;
                                }
                                
                                $issueCount = $ind->issues->count();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mb-1">{{ $ind->kode }}</span>
                                    <div class="fw-bold text-dark">{{ $ind->indikator_kinerja }}</div>
                                </td>
                                <td class="text-end fw-bold">{{ $targetVal }}</td>
                                <td class="text-end fw-bold text-primary">{{ $realisasiVal ?? '-' }}</td>
                                <td class="text-center">
                                    @if($capaianPersen >= 100)
                                        <span class="badge bg-success rounded-pill px-3 py-2">{{ number_format($capaianPersen, 1) }}%</span>
                                    @elseif($capaianPersen > 0)
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">{{ number_format($capaianPersen, 1) }}%</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">0%</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($capaianPersen >= 100)
                                        <a href="{{ route('target.show', $ind->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill w-100 mb-1">
                                            <i class="fas fa-search me-1"></i> Lihat Detail
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-danger rounded-pill w-100 mb-1 btn-lapor-kendala"
                                            data-id="{{ $ind->id }}"
                                            data-kode="{{ $ind->kode }}"
                                            data-nama="{{ $ind->indikator_kinerja }}"
                                            data-target="{{ $targetVal }}"
                                            data-realisasi="{{ $realisasiVal }}">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Lapor Kendala
                                        </button>
                                    @endif
                                    
                                    @if($issueCount > 0)
                                        <a href="#" class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill text-decoration-none mt-1 d-inline-block">
                                            ⚠️ {{ $issueCount }} Kendala
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Tidak ada data IKU pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Lapor Kendala & RTL -->
    <div class="modal fade" id="modalKendala" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('issues.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4" x-data="kendalaForm()">
                @csrf
                <input type="hidden" name="indikator_id" id="kendala_indikator_id">
                <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Lapor Kendala & RTL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    <!-- Context Box -->
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary-subtle">
                        <div class="fw-bold text-primary mb-1" id="k_kode_nama"></div>
                        <div class="d-flex gap-3 text-dark small">
                            <div>Target: <strong id="k_target"></strong></div>
                            <div>Realisasi: <strong id="k_realisasi"></strong></div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-exclamation-circle text-danger me-2"></i>Detail Kendala</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi Kendala <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="2" placeholder="Apa yang menghambat pencapaian IKU ini?" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status Kendala <span class="text-danger">*</span></label>
                        <select name="status_kendala" class="form-select rounded-3" x-model="statusKendala" required>
                            <option value="" disabled>-- Pilih Status --</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Sebagian Selesai">Sebagian Selesai</option>
                            <option value="Belum Ditangani">Belum Ditangani</option>
                        </select>
                    </div>

                    <div class="mb-4" x-show="statusKendala !== '' && statusKendala !== 'Belum Ditangani'">
                        <label class="form-label small fw-bold">Solusi Sementara <span class="text-danger" x-show="statusKendala !== 'Belum Ditangani'">*</span></label>
                        <textarea name="solusi_sementara" class="form-control rounded-3" rows="2" placeholder="Apa tindakan atau solusi darurat yang sudah dilakukan?"></textarea>
                    </div>

                    <div x-show="statusKendala !== 'Selesai'">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h6 class="fw-bold mb-0"><i class="fas fa-tasks text-primary me-2"></i>Rencana Tindak Lanjut (RTL)</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="addRtl()">
                                <i class="fas fa-plus me-1"></i> Tambah RTL
                            </button>
                        </div>
                        
                        <div id="rtl-repeater">
                            <template x-for="(rtl, index) in rtls" :key="rtl.id">
                                <div class="bg-light p-3 rounded-3 border mb-3 position-relative">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle" @click="removeRtl(rtl.id)" x-show="rtls.length > 1" title="Hapus RTL" style="width: 28px; height: 28px; padding: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <div class="mb-3 mt-1">
                                        <label class="form-label small fw-bold">Tindakan yang akan dilakukan <span class="text-danger">*</span></label>
                                        <textarea :name="'rtl['+index+'][deskripsi_rtl]'" class="form-control rounded-3" rows="2" required></textarea>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">PIC (Penanggung Jawab) <span class="text-danger">*</span></label>
                                            <select :name="'rtl['+index+'][pic_nip]'" class="form-select rounded-3" required>
                                                <option value="">-- Pilih Pegawai --</option>
                                                @foreach($pegawais as $p)
                                                    <option value="{{ $p->nip ?? $p->email_bps }}">{{ $p->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Batas Waktu (Due Date) <span class="text-danger">*</span></label>
                                            <input type="date" :name="'rtl['+index+'][due_date]'" class="form-control rounded-3" required min="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<!-- Load Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kendalaForm', () => ({
            statusKendala: '',
            rtls: [{ id: Date.now() }],
            
            addRtl() {
                this.rtls.push({ id: Date.now() });
            },
            
            removeRtl(id) {
                if (this.rtls.length > 1) {
                    this.rtls = this.rtls.filter(rtl => rtl.id !== id);
                }
            }
        }));
    });

    $(document).ready(function() {
        $('.btn-lapor-kendala').on('click', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');
            const nama = $(this).data('nama');
            const target = $(this).data('target');
            const realisasi = $(this).data('realisasi');
            
            $('#kendala_indikator_id').val(id);
            $('#k_kode_nama').text(kode + ' - ' + nama);
            $('#k_target').text(target);
            $('#k_realisasi').text(realisasi);
            
            $('#modalKendala').modal('show');
        });
        
        @if($indikators->count() > 0)
        $('#evaluasiTable').DataTable({
            language: window.DATATABLES_ID,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            ordering: false
        });
        @endif
    });
</script>
@endsection
