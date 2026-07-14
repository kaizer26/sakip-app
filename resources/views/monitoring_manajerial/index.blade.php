@extends('layouts.dashboard')

@section('title', 'Monitoring Manajerial')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Monitoring Manajerial</h4>
            <div class="text-muted small">Pemantauan kesehatan kinerja organisasi dan penyelesaian masalah.</div>
        </div>
    </div>

    <!-- Grafik dan Ringkasan -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    <h6 class="fw-bold mb-4 w-100 text-center">Status Keseluruhan RTL</h6>
                    <div style="width: 200px; height: 200px;">
                        <canvas id="rtlChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-check text-warning me-2"></i>Menunggu Verifikasi Atasan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="verifikasiTable" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>PIC & IKU</th>
                                    <th>RTL & Catatan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menungguVerifikasi as $rtl)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">{{ $rtl->pic->nama ?? '-' }}</div>
                                            <div class="text-primary extra-small text-truncate" style="max-width: 250px;" title="{{ $rtl->issue->indikator->indikator_kinerja ?? '-' }}">
                                                {{ $rtl->issue->indikator->indikator_kinerja ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">{{ $rtl->deskripsi_rtl }}</div>
                                            @php $lastExec = $rtl->executions->last(); @endphp
                                            <div class="text-muted extra-small fst-italic">"{{ $lastExec->catatan_progres ?? 'Tidak ada catatan' }}"</div>
                                            @if($lastExec && $lastExec->file_bukti_dukung)
                                                <a href="{{ asset('storage/' . $lastExec->file_bukti_dukung) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle text-decoration-none mt-2">
                                                    <i class="fas fa-paperclip me-1"></i> Bukti Dukung
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('monitoring-manajerial.verifikasi', $rtl->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 mb-1" title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('monitoring-manajerial.verifikasi', $rtl->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="revise">
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 mb-1" title="Revisi">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada RTL yang menunggu verifikasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kendala Kronis -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-danger bg-opacity-10 border-0 p-4">
            <h6 class="fw-bold text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Tabel Kendala Kronis (Overdue / Berulang)</h6>
            <div class="text-muted extra-small mt-1">Daftar IKU yang memiliki masalah belum tertangani dalam waktu yang ditentukan.</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="kronisTable" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th width="40" class="text-center">No</th>
                            <th>Indikator Kinerja</th>
                            <th>Total Overdue RTL</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kronisIndikators as $ind)
                            @php
                                $overdueCount = 0;
                                foreach($ind->issues as $issue) {
                                    $overdueCount += $issue->rtls->count();
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2 mb-1">{{ $ind->kode }}</span>
                                    <div class="fw-bold text-dark">{{ $ind->indikator_kinerja }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-danger rounded-pill px-3">{{ $overdueCount }} Overdue RTL</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="fas fa-search me-1"></i> Investigasi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <img src="https://illustrations.popsy.co/gray/success.svg" alt="empty" width="80" class="mb-3 opacity-50">
                                    <div>Kinerja organisasi sehat. Tidak ada kendala kronis.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Donut Chart
        const ctx = document.getElementById('rtlChart').getContext('2d');
        const rtlChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Berjalan', 'Terlambat', 'Menunggu'],
                datasets: [{
                    data: [{{ $selesai }}, {{ $berjalan }}, {{ $terlambat }}, {{ $menunggu }}],
                    backgroundColor: [
                        '#198754', // Success
                        '#0d6efd', // Primary
                        '#dc3545', // Danger
                        '#ffc107'  // Warning
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Initialize DataTables
        @if($menungguVerifikasi->count() > 0)
            $('#verifikasiTable').DataTable({
                language: window.DATATABLES_ID,
                pageLength: 5,
                lengthMenu: [5, 10, 25],
                ordering: false
            });
        @endif

        @if($kronisIndikators->count() > 0)
            $('#kronisTable').DataTable({
                language: window.DATATABLES_ID,
                pageLength: 10,
                ordering: false
            });
        @endif
    });
</script>
@endsection
