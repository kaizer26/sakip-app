@extends('layouts.dashboard')

@section('title', 'Dashboard Tindak Lanjut')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Dashboard Tindak Lanjut</h4>
            <div class="text-muted small">To-Do List untuk pelaksanaan Rencana Tindak Lanjut (RTL).</div>
        </div>
    </div>

    <!-- Tab Filters -->
    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3">
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $tab == 'semua' ? 'active' : 'bg-light text-dark' }}" href="{{ route('monitoring-rtl.index', ['tab' => 'semua']) }}">
                Semua <span class="badge bg-secondary ms-1">{{ $counts['semua'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $tab == 'overdue' ? 'active bg-danger' : 'bg-light text-dark' }}" href="{{ route('monitoring-rtl.index', ['tab' => 'overdue']) }}">
                Terlambat (Overdue) <span class="badge bg-danger ms-1">{{ $counts['overdue'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $tab == 'berjalan' ? 'active bg-primary' : 'bg-light text-dark' }}" href="{{ route('monitoring-rtl.index', ['tab' => 'berjalan']) }}">
                Berjalan <span class="badge bg-primary ms-1">{{ $counts['berjalan'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $tab == 'menunggu' ? 'active bg-warning text-dark' : 'bg-light text-dark' }}" href="{{ route('monitoring-rtl.index', ['tab' => 'menunggu']) }}">
                Menunggu Verifikasi <span class="badge bg-warning text-dark ms-1">{{ $counts['menunggu'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill {{ $tab == 'selesai' ? 'active bg-success' : 'bg-light text-dark' }}" href="{{ route('monitoring-rtl.index', ['tab' => 'selesai']) }}">
                Selesai <span class="badge bg-success ms-1">{{ $counts['selesai'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Kanban / Card View -->
    <div class="row g-3">
        @forelse($rtls as $rtl)
            @php
                $isOverdue = $rtl->due_date < date('Y-m-d') && in_array($rtl->status_rtl, ['Open', 'In Progress']);
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 {{ $isOverdue ? 'border-start border-4 border-danger' : 'border-start border-4 border-primary' }}">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $isOverdue ? 'bg-danger' : ($rtl->status_rtl == 'Closed' ? 'bg-success' : ($rtl->status_rtl == 'Selesai' ? 'bg-warning text-dark' : 'bg-primary')) }} rounded-pill px-3 py-2">
                                {{ $isOverdue ? 'Overdue' : $rtl->status_rtl }}
                            </span>
                            <div class="text-muted extra-small fw-bold">
                                <i class="fas fa-calendar-alt me-1"></i> Due: <span class="{{ $isOverdue ? 'text-danger' : '' }}">{{ \Carbon\Carbon::parse($rtl->due_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mt-2 mb-3 text-dark">{{ $rtl->deskripsi_rtl }}</h6>
                        
                        <div class="bg-light p-3 rounded-3 mb-3 mt-auto">
                            <div class="extra-small text-muted mb-1"><i class="fas fa-exclamation-circle me-1"></i> Berasal dari Kendala:</div>
                            <div class="small fw-bold text-dark text-truncate" title="{{ $rtl->issue->deskripsi }}">{{ $rtl->issue->deskripsi }}</div>
                            <div class="extra-small text-muted mt-2"><i class="fas fa-award me-1"></i> IKU:</div>
                            <div class="small fw-bold text-primary text-truncate" title="{{ $rtl->issue->indikator->indikator_kinerja ?? '-' }}">
                                {{ $rtl->issue->indikator->indikator_kinerja ?? '-' }}
                            </div>
                        </div>
                        
                        @if(in_array($rtl->status_rtl, ['Open', 'In Progress']))
                            <button class="btn btn-outline-primary rounded-pill w-100 mt-2 btn-eksekusi" 
                                data-id="{{ $rtl->id }}"
                                data-deskripsi="{{ $rtl->deskripsi_rtl }}"
                                data-due="{{ \Carbon\Carbon::parse($rtl->due_date)->format('d M Y') }}">
                                <i class="fas fa-arrow-up-right-from-square me-1"></i> Update Progres
                            </button>
                        @elseif($rtl->status_rtl == 'Selesai')
                            <button class="btn btn-light text-muted rounded-pill w-100 mt-2" disabled>
                                <i class="fas fa-hourglass-half me-1"></i> Menunggu Verifikasi Atasan
                            </button>
                        @else
                            <button class="btn btn-light text-success rounded-pill w-100 mt-2" disabled>
                                <i class="fas fa-check-double me-1"></i> RTL Selesai (Closed)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm">
                    <img src="https://illustrations.popsy.co/gray/success.svg" alt="empty" width="120" class="mb-3 opacity-50">
                    <h5 class="fw-bold">Tidak ada RTL yang ditemukan</h5>
                    <p>Semua beres! Tidak ada Rencana Tindak Lanjut pada kategori ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal Eksekusi RTL -->
    <div class="modal fade" id="modalEksekusi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" id="formEksekusi" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Eksekusi Tindak Lanjut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary-subtle">
                        <div class="fw-bold text-primary mb-1">Tugas Anda:</div>
                        <div class="text-dark small fw-bold mb-2" id="e_deskripsi"></div>
                        <div class="text-danger extra-small fw-bold"><i class="fas fa-clock me-1"></i> Tenggat: <span id="e_due"></span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Pelaksanaan (Progres) <span class="text-danger">*</span></label>
                        <textarea name="catatan_progres" class="form-control rounded-3" rows="3" placeholder="Jelaskan apa yang sudah Anda kerjakan untuk menyelesaikan RTL ini..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Upload Dokumen Bukti Dukung (PDF/JPG)</label>
                        <input type="file" name="file_bukti_dukung" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text extra-small">Maksimal 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim untuk Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.btn-eksekusi').on('click', function() {
            const id = $(this).data('id');
            const deskripsi = $(this).data('deskripsi');
            const due = $(this).data('due');
            
            $('#e_deskripsi').text(deskripsi);
            $('#e_due').text(due);
            
            // Set form action dynamically
            $('#formEksekusi').attr('action', '{{ url("monitoring-rtl") }}/' + id + '/eksekusi');
            
            $('#modalEksekusi').modal('show');
        });
    });
</script>
@endsection
