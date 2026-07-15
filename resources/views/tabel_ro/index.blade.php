@extends('layouts.dashboard')

@section('title', 'Master RO')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <!-- <div class="col-md-6">
                        <h4 class="mb-0 fw-bold text-gray-800">
                            <i class="fas fa-list-check text-primary me-2"></i> Master Rincian Output (RO)
                        </h4>
                        <p class="text-muted small mb-0 mt-1">Kelola data RO, Realisasi, dan Pagu Anggaran.</p>
                    </div> -->
            <div class="col text-md-end mt-3 mt-md-0">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-md-end gap-2">
                    <a href="{{ route('tabel-ro.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah RO Baru
                    </a>
                    <a href="{{ route('tabel-ro.template') }}" class="btn btn-outline-success rounded-pill px-3 fw-bold">
                        <i class="fas fa-download me-1"></i> Template
                    </a>
                    <form action="{{ route('tabel-ro.import') }}" method="POST" enctype="multipart/form-data"
                        class="d-inline-flex align-items-center">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="file" name="file" class="form-control rounded-start-pill border-success"
                                style="width: 150px;" required>
                            <button type="submit" class="btn btn-success rounded-end-pill px-3">
                                <i class="fas fa-upload me-1"></i> Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <!-- Filter -->
                <form action="{{ route('tabel-ro.index') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="tahun" class="form-select">
                                <option value="">-- Semua Tahun --</option>
                                @php $currentYear = date('Y'); @endphp
                                @for($i = $currentYear - 2; $i <= $currentYear + 2; $i++)
                                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="triwulan" class="form-select">
                                <option value="">-- Semua Triwulan --</option>
                                <option value="1" {{ request('triwulan') == '1' ? 'selected' : '' }}>Triwulan I</option>
                                <option value="2" {{ request('triwulan') == '2' ? 'selected' : '' }}>Triwulan II</option>
                                <option value="3" {{ request('triwulan') == '3' ? 'selected' : '' }}>Triwulan III</option>
                                <option value="4" {{ request('triwulan') == '4' ? 'selected' : '' }}>Triwulan IV</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter"></i>
                                Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('tabel-ro.index') }}" class="btn btn-light border w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary small fw-bold text-uppercase">No</th>
                                <th class="text-secondary small fw-bold text-uppercase">Indikator</th>
                                <th class="text-secondary small fw-bold text-uppercase">Rincian Output (RO)</th>
                                <th class="text-secondary small fw-bold text-uppercase">Periode</th>
                                <th class="text-secondary small fw-bold text-uppercase">Realisasi/Progres</th>
                                <th class="text-secondary small fw-bold text-uppercase">Pagu (Awal/Realisasi)</th>
                                <th class="text-secondary small fw-bold text-uppercase text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ros as $key => $ro)
                                <tr>
                                    <td>{{ $ros->firstItem() + $key }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ro->indikator->kode }}</div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;"
                                            title="{{ $ro->indikator->indikator_kinerja }}">
                                            {{ $ro->indikator->indikator_kinerja }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $ro->ro }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">TW {{ $ro->triwulan }}</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $ro->tahun }}</span>
                                    </td>
                                    <td>
                                        <div>Vol: <span class="fw-bold">{{ $ro->realisasi_volume_ro }}</span></div>
                                        <div>Prog: <span class="fw-bold">{{ $ro->progres_ro }}%</span></div>
                                    </td>
                                    <td>
                                        <div>Awal: <span class="text-primary fw-bold">Rp
                                                {{ number_format($ro->pagu_awal, 0, ',', '.') }}</span></div>
                                        <div>Real: <span class="text-success fw-bold">Rp
                                                {{ number_format($ro->pagu_realisasi, 0, ',', '.') }}</span></div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('tabel-ro.edit', $ro) }}" class="btn btn-sm btn-outline-primary"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tabel-ro.destroy', $ro) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus RO ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-box-open fs-1 text-light mb-3"></i>
                                        <p class="mb-0">Belum ada data Rincian Output.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $ros->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection