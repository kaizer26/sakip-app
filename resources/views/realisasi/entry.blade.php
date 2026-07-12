@extends('layouts.dashboard')

@section('title', 'Input Realisasi: ' . $indikator->kode)

@section('content')
{{-- Quill.js CSS --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<div class="row g-3">
    {{-- ========================== KOLOM KIRI: INFO + FORM ========================== --}}
    <div class="col-md-5">

        {{-- Info Indikator --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h6 class="text-muted small fw-bold text-uppercase mb-3">Informasi Indikator</h6>
                <div class="mb-2">
                    <label class="text-muted small d-block">Indikator Kinerja</label>
                    <div class="fw-bold">{{ $indikator->indikator_kinerja }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">
                        <label class="text-muted small d-block">Satuan</label>
                        <div class="fw-bold">{{ $indikator->satuan }}</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small d-block">Target Tahunan</label>
                        <div class="fw-bold text-primary">{{ $indikator->target_tahunan }}</div>
                    </div>
                </div>
                {{-- Definisi X/Y jika tersedia --}}
                @if($indikator->definisi_x || $indikator->definisi_y)
                <div class="mt-2 pt-2 border-top">
                    <div class="small text-muted fw-bold mb-1"><i class="fas fa-calculator me-1"></i> Formula Capaian</div>
                    @if($indikator->definisi_x)
                    <div class="extra-small mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle me-1">X</span>
                        {{ $indikator->definisi_x }}
                    </div>
                    @endif
                    @if($indikator->definisi_y)
                    <div class="extra-small">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle me-1">Y</span>
                        {{ $indikator->definisi_y }}
                    </div>
                    @endif
                </div>
                @endif
                <div class="mb-0 mt-2 pt-2 border-top">
                    <label class="text-muted small d-block">PIC</label>
                    <div class="d-flex align-items-center mt-1">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:30px;height:30px;font-size:0.8rem;">
                            {{ substr($indikator->pic->nama ?? 'A', 0, 1) }}
                        </div>
                        <div class="fw-bold">{{ $indikator->pic->nama ?? 'Admin' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Realisasi --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted small fw-bold text-uppercase mb-3">Form Realisasi</h6>
                <form action="{{ route('realisasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="indikator_id" value="{{ $indikator->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Triwulan</label>
                        <select name="triwulan" id="selectTriwulan" class="form-select" required>
                            <option value="1">Triwulan I</option>
                            <option value="2">Triwulan II</option>
                            <option value="3">Triwulan III</option>
                            <option value="4">Triwulan IV</option>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border border-dashed text-center" id="targetInfo">
                        <div class="text-muted small">Target Kumulatif Triwulan <span id="twLabel">I</span></div>
                        <div class="fs-4 fw-bold text-dark" id="targetValue">-</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nilai Realisasi Kumulatif</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="realisasi_kumulatif" id="inputRealisasi"
                                class="form-control form-control-lg" required {{ !$isPIC ? 'disabled' : '' }}>
                            <span class="input-group-text">{{ $indikator->satuan }}</span>
                        </div>
                        <div class="form-text text-info" id="prevInfo">Realisasi sebelumnya: <span id="prevValue">0</span></div>
                        @error('realisasi_kumulatif')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Input X/Y (tampil hanya jika definisi tersedia) --}}
                    @if($indikator->definisi_x || $indikator->definisi_y)
                    <div class="card border-0 bg-light rounded-3 p-3 mb-3" id="xySection">
                        <div class="small fw-bold text-muted mb-2"><i class="fas fa-calculator me-1"></i> Nilai X / Y (Dasar Hitung)</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="extra-small text-muted mb-1 d-block">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle me-1">X</span>
                                    {{ $indikator->definisi_x ?? 'Realisasi X' }}
                                </label>
                                <div class="extra-small text-primary mb-1 fw-bold" id="targetXContainer" style="display: none;">
                                    Target TW <span class="twLabelX"></span>: <span id="targetXValue">-</span>
                                </div>
                                <input type="number" step="0.01" name="realisasi_x" id="inputX"
                                    class="form-control form-control-sm" placeholder="0" {{ !$isPIC ? 'disabled' : '' }}>
                            </div>
                            <div class="col-6">
                                <label class="extra-small text-muted mb-1 d-block">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle me-1">Y</span>
                                    {{ $indikator->definisi_y ?? 'Total Y' }}
                                </label>
                                <div class="extra-small text-secondary mb-1 fw-bold" id="targetYContainer" style="display: none;">
                                    Target TW <span class="twLabelY"></span>: <span id="targetYValue">-</span>
                                </div>
                                <input type="number" step="0.01" name="realisasi_y" id="inputY"
                                    class="form-control form-control-sm" placeholder="0" {{ !$isPIC ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="mt-2 text-center extra-small text-muted" id="xyCalcResult"></div>
                    </div>
                    @endif

                    {{-- Rincian Output --}}
                    <div id="outputMonitoringSection" class="mb-3 d-none">
                        <h6 class="text-muted small fw-bold text-uppercase mb-2">
                            <i class="fas fa-list-check me-1"></i> Rincian Output (RO)
                        </h6>
                        <div id="outputContainer" class="bg-light p-3 rounded-3 border"></div>
                    </div>

                    @if($isPIC)
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-1"></i> Simpan Realisasi & Output
                        </button>
                    @else
                        <div class="alert alert-warning border-0 small text-center rounded-3">
                            <i class="fas fa-lock me-1"></i> Mode Lihat Saja (Hanya PIC/Admin yang dapat mengubah data)
                        </div>
                    @endif
                    <a href="{{ route('rekap.capaian') }}" class="btn btn-link w-100 text-muted mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================== KOLOM KANAN: TABS ========================== --}}
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="bg-light p-1 rounded-4 d-flex">
                    <ul class="nav nav-pills nav-fill w-100" id="contextTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold small py-2 rounded-3" id="kendala-tab"
                                data-bs-toggle="tab" data-bs-target="#kendala-pane" type="button" role="tab">
                                <i class="fas fa-exclamation-triangle me-1"></i> Kendala
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small py-2 rounded-3" id="aktivitas-tab"
                                data-bs-toggle="tab" data-bs-target="#aktivitas-pane" type="button" role="tab">
                                <i class="fas fa-tasks me-1"></i> Aktivitas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small py-2 rounded-3" id="basis-tab"
                                data-bs-toggle="tab" data-bs-target="#basis-pane" type="button" role="tab">
                                <i class="fas fa-database me-1"></i> Basis Data
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small py-2 rounded-3" id="dasar-tab"
                                data-bs-toggle="tab" data-bs-target="#dasar-pane" type="button" role="tab">
                                <i class="fas fa-square-root-alt me-1"></i> Dasar Hitung
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small py-2 rounded-3" id="bukti-tab"
                                data-bs-toggle="tab" data-bs-target="#bukti-pane" type="button" role="tab">
                                <i class="fas fa-folder-open me-1"></i> Bukti Dukung
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="tab-content" id="contextTabsContent">

                    {{-- TAB: KENDALA --}}
                    <div class="tab-pane fade show active py-3" id="kendala-pane" role="tabpanel">
                        <div id="kendalaContainer">
                            <div class="text-center py-4 opacity-50 small italic text-muted">Memuat data kendala...</div>
                        </div>
                    </div>

                    {{-- TAB: AKTIVITAS PEGAWAI --}}
                    <div class="tab-pane fade py-3" id="aktivitas-pane" role="tabpanel">
                        <div id="activityContainer">
                            <div class="text-center py-4 opacity-50 small italic text-muted">Memuat data aktivitas...</div>
                        </div>
                    </div>

                    {{-- TAB: BASIS DATA --}}
                    <div class="tab-pane fade py-3" id="basis-pane" role="tabpanel">
                        <div id="basisDataPanel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-muted">Basis Data Indikator</span>
                                @if($isPIC)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="basisUploadBtn">
                                        <i class="fas fa-image me-1"></i> Upload Foto
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" id="saveBasisBtn">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div id="basisDataEditor" class="quill-editor-container" style="min-height: 280px; border-radius: 8px;"></div>
                            <input type="file" id="basisFileInput" accept="image/*,application/pdf" style="display:none">
                            <div class="mt-2 extra-small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Gunakan editor untuk teks, <b>\$rumus\$</b> untuk formula matematika (LaTeX), dan tombol Upload untuk menyisipkan foto.
                            </div>
                        </div>
                    </div>

                    {{-- TAB: DASAR HITUNG --}}
                    <div class="tab-pane fade py-3" id="dasar-pane" role="tabpanel">
                        <div id="dasarHitungPanel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-muted">Dasar Hitung</span>
                                @if($isPIC)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="dasarUploadBtn">
                                        <i class="fas fa-image me-1"></i> Upload Foto
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" id="saveDasarBtn">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                                @endif
                            </div>
                            <div id="dasarHitungEditor" class="quill-editor-container" style="min-height: 280px; border-radius: 8px;"></div>
                            <input type="file" id="dasarFileInput" accept="image/*,application/pdf" style="display:none">
                            <div class="mt-2 extra-small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Contoh rumus: <code>\$\\frac{X}{Y} \\times 100\%\$</code> akan dirender sebagai formula.
                            </div>
                        </div>
                    </div>

                    {{-- TAB: BUKTI DUKUNG --}}
                    <div class="tab-pane fade py-3" id="bukti-pane" role="tabpanel">
                        <div id="buktiDukungContainer">
                            <div class="text-center py-4 opacity-50 small italic text-muted">Memuat bukti dukung...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- File input tersembunyi untuk upload evidence output --}}
<input type="file" id="outputFileInput" style="display:none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">

{{-- Modal Preview --}}
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="previewTitle">File Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="previewContent" style="min-height:400px;display:flex;align-items:center;justify-content:center;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Quill.js --}}
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
{{-- MathJax untuk preview rumus LaTeX --}}
<script>
window.MathJax = {
    tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
    svg: { fontCache: 'global' }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

<script>
const isPIC        = {{ $isPIC ? 'true' : 'false' }};
const indikatorKode = '{{ $indikator->kode }}';
const indikatorId   = {{ $indikator->id }};
const richContentUrl = '{{ route('indikator.rich-content', $indikator->kode) }}';
const mediaUploadUrl = '{{ route('indikator.media', $indikator->kode) }}';
const csrfToken      = '{{ csrf_token() }}';

// ============================================================
// QUILL EDITORS
// ============================================================
const toolbarOptions = [
    [{ 'header': [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    [{ 'align': [] }],
    ['blockquote', 'code-block'],
    ['image', 'link'],
    ['clean']
];

const basisEditor = new Quill('#basisDataEditor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions },
    readOnly: !isPIC,
    placeholder: 'Ketik basis data indikator di sini...\nGunakan $formula$ untuk rumus matematika.',
});

const dasarEditor = new Quill('#dasarHitungEditor', {
    theme: 'snow',
    modules: { toolbar: toolbarOptions },
    readOnly: !isPIC,
    placeholder: 'Ketik dasar hitung di sini...\nContoh: $\\frac{X}{Y} \\times 100\\%$',
});

// Load existing content
const existingBasis = @json($indikator->basis_data ?? '');
const existingDasar = @json($indikator->dasar_hitung ?? '');

if (existingBasis) {
    basisEditor.clipboard.dangerouslyPasteHTML(0, existingBasis);
    setTimeout(() => { if (window.MathJax) MathJax.typeset(['#basisDataEditor']); }, 500);
}
if (existingDasar) {
    dasarEditor.clipboard.dangerouslyPasteHTML(0, existingDasar);
    setTimeout(() => { if (window.MathJax) MathJax.typeset(['#dasarHitungEditor']); }, 500);
}

// Re-render math when switching to basis/dasar tabs
document.getElementById('basis-tab').addEventListener('shown.bs.tab', () => {
    if (window.MathJax) MathJax.typeset(['#basisDataEditor']);
});
document.getElementById('dasar-tab').addEventListener('shown.bs.tab', () => {
    if (window.MathJax) MathJax.typeset(['#dasarHitungEditor']);
});

// ============================================================
// SAVE Rich Content (Basis Data & Dasar Hitung)
// ============================================================
function saveRichContent(field, editor, btn) {
    const html    = editor.root.innerHTML;
    const origHtml = btn.innerHTML;
    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    $.ajax({
        url: richContentUrl,
        method: 'POST',
        data: { _token: csrfToken, [field]: html },
        success: (r) => { toastr.success('Konten berhasil disimpan'); },
        error:   ()  => { toastr.error('Gagal menyimpan konten'); },
        complete: () => { btn.disabled = false; btn.innerHTML = origHtml; }
    });
}

if (isPIC) {
    document.getElementById('saveBasisBtn')?.addEventListener('click', function() {
        saveRichContent('basis_data', basisEditor, this);
    });
    document.getElementById('saveDasarBtn')?.addEventListener('click', function() {
        saveRichContent('dasar_hitung', dasarEditor, this);
    });
}

// ============================================================
// UPLOAD MEDIA untuk Quill Editor
// ============================================================
function triggerMediaUpload(fileInput, editor, field) {
    fileInput.click();
    fileInput.onchange = function() {
        if (!this.files || !this.files[0]) return;
        const fd = new FormData();
        fd.append('file',  this.files[0]);
        fd.append('field', field);
        fd.append('_token', csrfToken);

        $.ajax({
            url: mediaUploadUrl,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: (r) => {
                // Insert image into editor at cursor position
                const range = editor.getSelection(true);
                editor.insertEmbed(range.index, 'image', r.url);
                toastr.success('Foto berhasil diunggah');
            },
            error: () => { toastr.error('Gagal mengunggah foto'); },
            complete: () => { this.value = ''; }
        });
    };
}

if (isPIC) {
    document.getElementById('basisUploadBtn')?.addEventListener('click', function() {
        triggerMediaUpload(document.getElementById('basisFileInput'), basisEditor, 'basis_data');
    });
    document.getElementById('dasarUploadBtn')?.addEventListener('click', function() {
        triggerMediaUpload(document.getElementById('dasarFileInput'), dasarEditor, 'dasar_hitung');
    });
}

// ============================================================
// PREVIEW FILE
// ============================================================
function showPreview(url, fileName) {
    const ext = fileName.split('.').pop().toLowerCase();
    let html = '';
    $('#previewTitle').text(fileName);
    $('#previewContent').html('<div class="spinner-border text-primary"></div>');
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
        html = `<img src="${url}" class="img-fluid rounded shadow-sm" style="max-height:80vh;">`;
    } else if (ext === 'pdf') {
        html = `<iframe src="${url}" width="100%" height="600px" style="border:none;border-radius:8px;"></iframe>`;
    } else {
        html = `<div class="text-center py-5"><i class="fas fa-file-alt fs-1 text-muted mb-3"></i><p>Format <b>.${ext}</b> tidak mendukung preview.<br><a href="${url}" target="_blank" class="btn btn-primary px-4 mt-2">Download / Buka File</a></p></div>`;
    }
    setTimeout(() => { $('#previewContent').html(html); }, 300);
    new bootstrap.Modal(document.getElementById('modalPreview')).show();
}

// ============================================================
// LOAD CONTEXT (Triwulan)
// ============================================================
function loadContext() {
    const triwulan = $('#selectTriwulan').val();
    $('#twLabel').text(['', 'I', 'II', 'III', 'IV'][triwulan]);
    $('#activityContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    $('#kendalaContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    $('#buktiDukungContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

    $.get(`{{ url('api/realisasi/context/' . $indikator->kode) }}/${triwulan}`, function(data) {
        $('#targetValue').text(data.target);
        $('#prevValue').text(data.previous_value);
        $('#inputRealisasi').val(data.current_value);

        // X/Y
        if (typeof data.current_x !== 'undefined') $('#inputX').val(data.current_x);
        if (typeof data.current_y !== 'undefined') $('#inputY').val(data.current_y);
        
        // Target X/Y
        $('.twLabelX, .twLabelY').text(['', 'I', 'II', 'III', 'IV'][triwulan]);
        if (data.target_x !== null) {
            $('#targetXValue').text(data.target_x);
            $('#targetXContainer').show();
        } else {
            $('#targetXContainer').hide();
        }
        
        if (data.target_y !== null) {
            $('#targetYValue').text(data.target_y);
            $('#targetYContainer').show();
        } else {
            $('#targetYContainer').hide();
        }

        updateXYCalc();

        renderKendala(data.analisis);
        renderAktivitas(data.aktivitas);
        renderBuktiDukung(data.aktivitas);
        renderOutputs(data.outputs);
    });
}

// ============================================================
// KALKULASI X/Y OTOMATIS
// ============================================================
function updateXYCalc() {
    const x = parseFloat($('#inputX').val());
    const y = parseFloat($('#inputY').val());
    if (!isNaN(x) && !isNaN(y) && y > 0) {
        const pct = ((x / y) * 100).toFixed(2);
        $('#xyCalcResult').html(`<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-chart-line me-1"></i> Capaian X/Y: <b>${pct}%</b> (${x}/${y})</span>`);
    } else {
        $('#xyCalcResult').html('');
    }
}
$('#inputX, #inputY').on('input', updateXYCalc);

// ============================================================
// RENDER FUNGSI
// ============================================================
function renderKendala(analisis) {
    if (!analisis || analisis.length === 0) {
        return $('#kendalaContainer').html('<div class="text-center py-4 opacity-50 small italic">Tidak ada kendala yang dilaporkan.</div>');
    }
    let html = '';
    analisis.forEach(an => {
        const sevClass = an.severity === 'High' ? 'danger' : (an.severity === 'Medium' ? 'warning' : 'info');
        html += `
            <div class="mb-3 pb-3 border-bottom last-child-no-border" style="border-left:4px solid var(--bs-${sevClass});padding-left:15px;">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-bold extra-small text-dark">${an.pegawai}
                        <span class="badge bg-${sevClass} text-white ms-1" style="font-size:0.55rem;">${an.severity} Issue</span>
                    </div>
                    <span class="badge bg-light text-muted fw-normal border extra-small px-2 py-1">${an.tanggal}</span>
                </div>
                <div class="text-dark small mb-2 lh-base fw-bold">${an.kendala}</div>
                ${an.solusi ? `<div class="bg-light p-2 rounded extra-small mt-2 border-start border-warning border-3"><i class="fas fa-lightbulb text-warning me-1"></i> <b>Solusi:</b> ${an.solusi}</div>` : ''}
            </div>`;
    });
    $('#kendalaContainer').html(html);
}

function renderAktivitas(aktivitas) {
    if (!aktivitas || aktivitas.length === 0) {
        return $('#activityContainer').html('<div class="text-center py-4 opacity-50 small italic">Belum ada aktivitas yang dicatat.</div>');
    }
    let html = '';
    aktivitas.forEach(a => {
        let lampHtml = '';
        if (a.lampirans && a.lampirans.length > 0) {
            a.lampirans.forEach(l => {
                const url = `{{ asset('storage') }}/${l}`;
                const fn  = l.split('/').pop();
                lampHtml += `<button type="button" onclick="showPreview('${url}','${fn}')" class="btn btn-light btn-sm border extra-small me-1 mt-1 px-2 py-0"><i class="fas fa-eye me-1"></i> ${fn}</button>`;
            });
        }
        html += `
            <div class="mb-4 pb-4 border-bottom last-child-no-border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold fs-6 text-dark">${a.pegawai}</div>
                    <span class="badge bg-light text-muted fw-normal border px-2 py-1">${a.tanggal}</span>
                </div>
                <div class="small text-primary mb-2 fw-medium"><i class="fas fa-tag me-1"></i> Tahap: ${a.tahapan}</div>
                <div class="text-muted small mb-2 lh-base">${a.uraian}</div>
                <div class="d-flex flex-wrap">${lampHtml}</div>
            </div>`;
    });
    $('#activityContainer').html(html);
}

function renderBuktiDukung(aktivitas) {
    if (!aktivitas || aktivitas.length === 0) {
        return $('#buktiDukungContainer').html('<div class="text-center py-4 opacity-50 small italic">Belum ada bukti dukung yang dicatat.</div>');
    }
    let html = '';
    aktivitas.forEach((a, idx) => {
        let lampHtml = '';
        if (a.lampirans && a.lampirans.length > 0) {
            a.lampirans.forEach(l => {
                const url = `{{ asset('storage') }}/${l}`;
                const fn  = l.split('/').pop();
                const ext = fn.split('.').pop().toLowerCase();
                if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                    lampHtml += `<div class="me-2 mb-2" style="display:inline-block;">
                        <img src="${url}" alt="${fn}" class="rounded border" style="height:80px;width:80px;object-fit:cover;cursor:pointer;"
                            onclick="showPreview('${url}','${fn}')">
                    </div>`;
                } else {
                    lampHtml += `<button type="button" onclick="showPreview('${url}','${fn}')" class="btn btn-light btn-sm border extra-small me-1 mb-1 px-2 py-0"><i class="fas fa-file me-1"></i> ${fn}</button>`;
                }
            });
        }

        html += `
            <div class="card border-0 bg-light rounded-3 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle me-1 extra-small">Kegiatan ${idx+1}</span>
                            <span class="fw-bold small text-dark">${a.pegawai}</span>
                        </div>
                        <span class="badge bg-light text-muted fw-normal border px-2 py-1 extra-small">${a.tanggal}</span>
                    </div>
                    <div class="small text-primary fw-medium mb-1"><i class="fas fa-tag me-1"></i> ${a.tahapan}</div>
                    <div class="small text-dark fw-bold mb-1">${a.uraian}</div>
                    ${a.penjelasan_kegiatan ? `
                        <div class="extra-small text-muted mb-1">
                            <i class="fas fa-align-left me-1 text-secondary"></i>
                            <span class="fw-bold">Penjelasan:</span> ${a.penjelasan_kegiatan}
                        </div>` : ''}
                    ${a.realisasi_kegiatan ? `
                        <div class="extra-small text-success mb-2">
                            <i class="fas fa-check-circle me-1"></i>
                            <span class="fw-bold">Realisasi:</span> ${a.realisasi_kegiatan}
                        </div>` : ''}
                    ${lampHtml ? `<div class="d-flex flex-wrap mt-2 pt-2 border-top">${lampHtml}</div>` : ''}
                </div>
            </div>`;
    });
    $('#buktiDukungContainer').html(html);
}

function renderOutputs(outputs) {
    if (!outputs || outputs.length === 0) {
        return $('#outputMonitoringSection').addClass('d-none');
    }
    $('#outputMonitoringSection').removeClass('d-none');
    let html = '';
    outputs.forEach(o => {
        const hasFile    = o.file_path != null;
        const progVal    = parseFloat(o.progres) || 0;
        const progColor  = progVal >= 100 ? 'success' : (progVal >= 60 ? 'warning' : 'danger');
        const targVol    = o.target_volume != null ? `<span class="text-muted">/ ${o.target_volume}</span>` : '';

        html += `
            <div class="mb-3 pb-3 border-bottom last-child-no-border">
                <div class="form-check mb-1">
                    <input class="form-check-input output-checkbox" type="checkbox"
                        id="output-${o.id}" data-id="${o.id}" ${o.is_achieved ? 'checked' : ''} ${!isPIC ? 'disabled' : ''}>
                    <label class="form-check-label text-dark fw-bold small" for="output-${o.id}">
                        ${o.nama_output} <span class="text-muted extra-small fw-normal">(${o.jenis_output})</span>
                    </label>
                </div>
                ${o.penjelasan_ro ? `<div class="extra-small text-muted mb-2 ps-4"><i class="fas fa-info-circle me-1"></i>${o.penjelasan_ro}</div>` : ''}

                <div class="row g-2 mb-2 ps-4">
                    <div class="col-5">
                        <label class="extra-small text-muted mb-1">Volume Capaian ${targVol}</label>
                        <input type="number" step="0.01" name="output_data[${o.id}][volume]"
                            class="form-control form-control-sm" value="${o.volume || ''}" placeholder="0" ${!isPIC ? 'disabled' : ''}>
                    </div>
                    <div class="col-4">
                        <label class="extra-small text-muted mb-1">Progres (%)</label>
                        <input type="number" step="0.01" name="output_data[${o.id}][progres]"
                            class="form-control form-control-sm" value="${o.progres || ''}" placeholder="0"
                            min="0" max="100" ${!isPIC ? 'disabled' : ''}
                            oninput="updateProgress(${o.id}, this.value)">
                    </div>
                    <div class="col-3 d-flex align-items-end pb-1">
                        <div class="w-100">
                            <div class="progress" style="height:8px;" title="${progVal}%">
                                <div class="progress-bar bg-${progColor}" id="pbar-${o.id}" style="width:${progVal}%"></div>
                            </div>
                            <div class="extra-small text-center text-muted" id="pct-${o.id}">${progVal}%</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 ps-4">
                    ${isPIC ? `
                    <button type="button" class="btn btn-sm btn-outline-secondary border-dashed upload-trigger" data-id="${o.id}">
                        <i class="fas ${hasFile ? 'fa-sync' : 'fa-upload'} me-1"></i>
                        ${hasFile ? 'Perbarui' : 'Unggah'}
                    </button>` : ''}
                    <span id="file-info-${o.id}" class="extra-small text-muted">
                        ${hasFile
                            ? `<a href="javascript:void(0)" onclick="showPreview('{{ asset('storage') }}/${o.file_path}','${o.file_path.split('/').pop()}')" class="text-primary text-decoration-none fw-bold"><i class="fas fa-eye me-1"></i> Lihat</a>`
                            : '<i class="fas fa-info-circle me-1"></i> Tidak ada file'}
                    </span>
                </div>
            </div>`;
    });
    $('#outputContainer').html(html);
}

function updateProgress(id, val) {
    const v = Math.min(100, Math.max(0, parseFloat(val) || 0));
    const color = v >= 100 ? 'success' : (v >= 60 ? 'warning' : 'danger');
    $(`#pbar-${id}`).css('width', v + '%').removeClass('bg-success bg-warning bg-danger').addClass(`bg-${color}`);
    $(`#pct-${id}`).text(v + '%');
}

// ============================================================
// OUTPUT EVENTS
// ============================================================
$(document).on('change', '.output-checkbox', function() {
    const id      = $(this).data('id');
    $.ajax({
        url:    "{{ route('output-master.toggle-status', ['output_master' => '__ID__']) }}".replace('__ID__', id),
        method: 'POST',
        data:   { _token: csrfToken },
        success: (r) => { toastr.success(r.message); },
        error:   ()  => { toastr.error('Gagal memperbarui status output.'); }
    });
});

let activeOutputId = null;
$(document).on('click', '.upload-trigger', function() {
    activeOutputId = $(this).data('id');
    $('#outputFileInput').click();
});

$('#outputFileInput').on('change', function() {
    if (!this.files || !this.files[0] || !activeOutputId) return;
    const fd = new FormData();
    fd.append('file', this.files[0]);
    fd.append('_token', csrfToken);
    const btn = $(`.upload-trigger[data-id="${activeOutputId}"]`);
    const orig = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    $.ajax({
        url:         "{{ route('output-master.upload', ['output_master' => ':id']) }}".replace(':id', activeOutputId),
        method:      'POST',
        data:        fd,
        processData: false,
        contentType: false,
        success: (r) => {
            toastr.success(r.message);
            btn.prop('disabled', false).html('<i class="fas fa-sync me-1"></i> Perbarui');
            $(`#file-info-${activeOutputId}`).html(`<a href="javascript:void(0)" onclick="showPreview('${r.file_url}','${r.file_name}')" class="text-primary text-decoration-none fw-bold"><i class="fas fa-eye me-1"></i> Lihat</a>`);
        },
        error: (xhr) => { btn.prop('disabled', false).html(orig); toastr.error(xhr.responseJSON?.message || 'Gagal mengunggah file.'); },
        complete: () => { $('#outputFileInput').val(''); }
    });
});

// ============================================================
// INIT
// ============================================================
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const twParam   = urlParams.get('triwulan');
    if (twParam && ['1','2','3','4'].includes(twParam)) {
        $('#selectTriwulan').val(twParam);
    }
    $('#selectTriwulan').on('change', loadContext);
    loadContext();

    // Load bukti dukung tab on click
    document.getElementById('bukti-tab').addEventListener('shown.bs.tab', function() {
        const triwulan = $('#selectTriwulan').val();
        $.get(`{{ url('api/realisasi/context/' . $indikator->kode) }}/${triwulan}`, function(data) {
            renderBuktiDukung(data.aktivitas);
        });
    });
});
</script>

<style>
.last-child-no-border:last-child { border-bottom: none !important; }
.extra-small { font-size: 0.75rem; }
.nav-pills .nav-link { color: #6c757d; border: none; transition: all 0.2s; font-size: 0.72rem; }
.nav-pills .nav-link.active { color: #fff; background-color: var(--bs-primary); box-shadow: 0 4px 10px rgba(67,97,238,0.2); }
.nav-pills .nav-link:not(.active):hover { background-color: rgba(0,0,0,0.05); color: var(--bs-primary); }
.quill-editor-container { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; }
.ql-toolbar { border-radius: 8px 8px 0 0 !important; }
.ql-container { border-radius: 0 0 8px 8px !important; font-size: 0.9rem; }
.border-dashed { border-style: dashed !important; }
</style>
@endsection
