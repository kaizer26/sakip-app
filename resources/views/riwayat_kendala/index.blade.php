@extends('layouts.dashboard')

@section('title', 'Riwayat Kendala Saya')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Riwayat Kendala</h4>
        <div class="text-muted small">Kelola riwayat pelaporan kendala dan tindak lanjut.</div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-history me-2"></i>Daftar Kendala</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th width="50">No</th>
                        <th>Indikator & Kegiatan</th>
                        <th>Triwulan</th>
                        <th>Kendala</th>
                        <th>Solusi</th>
                        <th>RTL</th>
                        <th>PIC TL</th>
                        <th>Batas Waktu</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatKendala as $rk)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold small">{{ $rk->indikator->indikator_kinerja ?? '-' }}</div>
                                <div class="text-muted extra-small">
                                    <span class="badge bg-light text-dark border">{{ $rk->indikator->kode ?? '-' }}</span>
                                    @if(auth()->user()->isAdmin())
                                    <div class="mt-1"><i class="fas fa-user text-primary me-1"></i> {{ $rk->pegawai->nama ?? 'Tidak Ditemukan' }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">TW {{ $rk->triwulan }}</td>
                            <td>{{ $rk->kendala }}</td>
                            <td>{{ $rk->solusi ?? '-' }}</td>
                            <td>{{ $rk->rencana_tindak_lanjut ?? '-' }}</td>
                            <td>{{ $rk->pic_tindak_lanjut ?? '-' }}</td>
                            <td class="text-center">{{ $rk->batas_waktu ? \Carbon\Carbon::parse($rk->batas_waktu)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-edit" 
                                    data-id="{{ $rk->id }}"
                                    data-triwulan="{{ $rk->triwulan }}"
                                    data-kendala="{{ $rk->kendala }}"
                                    data-solusi="{{ $rk->solusi }}"
                                    data-rtl="{{ $rk->rencana_tindak_lanjut }}"
                                    data-pic="{{ $rk->pic_tindak_lanjut }}"
                                    data-batas="{{ $rk->batas_waktu }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editKendalaModal">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p class="mb-0">Belum ada riwayat kendala yang dilaporkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Kendala Modal -->
<div class="modal fade" id="editKendalaModal" tabindex="-1" aria-labelledby="editKendalaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editKendalaForm" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editKendalaModalLabel">Edit Riwayat Kendala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold mb-1">Triwulan</label>
                    <select name="triwulan" id="edit_triwulan" class="form-select rounded-3" required>
                        <option value="1">Triwulan I</option>
                        <option value="2">Triwulan II</option>
                        <option value="3">Triwulan III</option>
                        <option value="4">Triwulan IV</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold mb-1">Kendala yang Dihadapi</label>
                    <textarea name="kendala" id="edit_kendala" class="form-control rounded-3" rows="3" required></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">Solusi yang Dilakukan</label>
                        <textarea name="solusi" id="edit_solusi" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">Rencana Tindak Lanjut</label>
                        <textarea name="rencana_tindak_lanjut" id="edit_rtl" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">PIC Tindak Lanjut</label>
                        <input type="text" name="pic_tindak_lanjut" id="edit_pic" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">Batas Waktu</label>
                        <input type="date" name="batas_waktu" id="edit_batas" class="form-control rounded-3">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const editForm = document.getElementById('editKendalaForm');
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                editForm.action = `/riwayat-kendala/${id}`;
                
                document.getElementById('edit_triwulan').value = this.dataset.triwulan;
                document.getElementById('edit_kendala').value = this.dataset.kendala;
                document.getElementById('edit_solusi').value = this.dataset.solusi || '';
                document.getElementById('edit_rtl').value = this.dataset.rtl || '';
                document.getElementById('edit_pic').value = this.dataset.pic || '';
                
                // Format batas_waktu for input type date
                if (this.dataset.batas && this.dataset.batas.length >= 10) {
                    document.getElementById('edit_batas').value = this.dataset.batas.substring(0, 10);
                } else {
                    document.getElementById('edit_batas').value = '';
                }
            });
        });
    });
</script>
@endsection
