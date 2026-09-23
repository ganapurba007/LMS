@extends('layouts.be.master')
@section('header_title', 'Master Data — Tambah Bank Soal')

@section('content')

{{-- CDN Libraries for Client-side Document Extraction --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }
</script>

{{-- Page Header --}}
<div class="col-md-12">
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                <i class="ti ti-database-plus"></i>
            </div>
            <div>
                <h5 class="md-title">Tambah Bank Soal</h5>
                <div class="text-muted small">Buat bank butir soal kuis baru secara manual atau generate dari file Word/PDF.</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="md-btn-secondary qb-doc-upload-btn" data-bs-toggle="modal" data-bs-target="#modalDocUpload">
                <i class="ti ti-file-upload text-primary"></i> <span>Upload Word / PDF</span>
            </button>
            <button type="button" class="md-btn-secondary qb-quick-paste-btn" data-bs-toggle="modal" data-bs-target="#modalQuickPaste">
                <i class="ti ti-bolt text-warning"></i> <span>Input Cepat (Teks)</span>
            </button>
            <a href="{{ route('admin.question-banks.index') }}" class="md-btn-secondary">
                <i class="ti ti-arrow-left"></i> <span>Kembali</span>
            </a>
        </div>
    </div>
</div>

{{-- Flash Alert --}}
@if(session('error'))
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<form method="POST" action="{{ route('admin.question-banks.store') }}" id="batchQbForm">
    @csrf

    <div class="md-form-card mb-4">
        <div class="md-form-head">
            <div class="md-form-head-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                <i class="ti ti-list-details"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-grow-1">
                <h6 class="md-form-head-title mb-0">Daftar Butir Soal</h6>
                <span id="qbQuestionCounterBadge" class="md-badge qb-counter-badge">
                    <i class="ti ti-list-check"></i> 1 Butir Soal
                </span>
            </div>
        </div>

        <div class="md-form-body">
            <div class="alert border-0 rounded-3 d-flex align-items-start gap-2 mb-4 p-3 qb-info-alert">
                <i class="ti ti-info-circle fs-5 flex-shrink-0 mt-1" style="color:#0891b2;"></i>
                <div>
                    Pilih format soal untuk tiap butir. Anda dapat menyisipkan <strong>Gambar</strong> dan <strong>Tabel</strong> menggunakan tombol toolbar di atas teks pertanyaan. Klik <strong>Simpan Semua Soal</strong> setelah selesai.
                </div>
            </div>

            <!-- Container for question cards -->
            <div id="qbQuestionsContainer" class="d-flex flex-column gap-3 mb-3">
                <!-- Rendered by JavaScript -->
            </div>

            <!-- Add Button -->
            <button type="button" class="qb-btn-add-more" onclick="addNewQbQuestionCard()">
                <i class="ti ti-plus"></i>
                <span>Tambah Butir Soal Baru</span>
            </button>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.question-banks.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" id="submitQbAllBtn" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i>
                <span id="submitQbBtnText">Simpan Semua Soal (1 Butir)</span>
            </button>
        </div>
    </div>
</form>

<!-- Modal Upload & Generate Dokumen (Word & PDF) -->
<div class="modal fade" id="modalDocUpload" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="md-modal-content text-start p-3 p-sm-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;width:38px;height:38px;font-size:1.05rem;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-file-type-doc"></i>
                    </div>
                    <div>
                        <h6 class="md-title mb-0" style="font-size:1.05rem;">Generate Soal dari File Dokumen</h6>
                        <div class="text-muted small" style="font-size:.76rem;">Mendukung berkas Microsoft Word (.docx) &amp; PDF (.pdf)</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Upload Dropzone Area -->
            <div class="qb-doc-dropzone mb-3" id="docDropZone">
                <div class="qb-doc-dropzone-icon">
                    <i class="ti ti-cloud-upload"></i>
                </div>
                <h6 class="fw-bold mb-1 qb-doc-dropzone-title">Pilih atau Tarik File Dokumen ke Sini</h6>
                <p class="text-muted small mb-3">Format didukung: <strong class="text-primary">.docx (Word)</strong> &amp; <strong class="text-danger">.pdf</strong> &bull; Maks. 20MB</p>
                <input type="file" id="docFileInput" class="d-none" accept=".docx,.pdf,.doc">
                <button type="button" class="md-btn-secondary px-3 py-1.5" onclick="document.getElementById('docFileInput').click()">
                    <i class="ti ti-folder-open text-primary me-1"></i> Telusuri File Dokumen
                </button>
                <div id="selectedDocName" class="mt-2 fw-semibold text-primary small d-none"></div>
            </div>

            <!-- Loading Spinner -->
            <div id="docParseLoading" class="text-center py-4 d-none">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div class="fw-bold text-dark" id="docParseStatusText">Mengekstrak &amp; mem-parsing naskah soal...</div>
                <div class="text-muted small">Mohon tunggu sebentar, sistem sedang membaca butir-butir soal.</div>
            </div>

            <!-- Extracted Preview Section -->
            <div id="docExtractedSection" class="d-none mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small" id="docExtractedCountBadge" style="color:var(--tblr-heading-color,#0f172a);">
                        <i class="ti ti-circle-check text-success me-1"></i> 0 Soal Terdeteksi
                    </span>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="toggleDocRawText()" style="font-size:.75rem;">
                        <i class="ti ti-code"></i> Lihat Teks Mentah
                    </button>
                </div>
                <textarea id="docRawTextarea" class="form-control font-monospace mb-3 d-none" rows="5" style="font-size:.78rem;" placeholder="Teks hasil ekstraksi..."></textarea>
                <div id="docQuestionsPreviewList" class="d-flex flex-column gap-2" style="max-height: 260px; overflow-y: auto;">
                    <!-- Rendered cards -->
                </div>
            </div>

            <!-- Word Template Reference Card -->
            <div class="qb-doc-template-box mb-3">
                <div class="qb-doc-template-content">
                    <div class="qb-doc-template-icon">
                        <i class="ti ti-file-text"></i>
                    </div>
                    <div>
                        <div class="qb-doc-template-title">Template Acuan Soal Word</div>
                        <div class="qb-doc-template-sub">Unduh file acuan Microsoft Word (.docx) untuk panduan format penulisan siap upload</div>
                    </div>
                </div>
                <a href="{{ route('admin.question-banks.download-template', ['format' => 'docx']) }}" class="btn btn-sm btn-primary qb-doc-template-btn">
                    <i class="ti ti-download"></i> Unduh Template Word
                </a>
            </div>

            <!-- Format Guidelines Card -->
            <div class="qb-doc-tips-box mb-3">
                <div class="qb-doc-tips-header">
                    <i class="ti ti-bulb"></i>
                    <span>Panduan Format Penulisan Dokumen:</span>
                </div>
                <ul class="qb-doc-tips-list mb-0">
                    <li><strong>Nomor Soal:</strong> Awali butir pertanyaan dengan nomor urut jelas, misal: <code>1. Pertanyaan...</code></li>
                    <li><strong>Pilihan Ganda:</strong> Buat baris opsi <code>A. Pilihan 1</code>, <code>B. Pilihan 2</code>, dan sertakan <code>Kunci: A</code>.</li>
                    <li><strong>Benar / Salah:</strong> Tulis pernyataan dan akhiri dengan baris <code>Kunci: Benar</code> atau <code>Kunci: Salah</code>.</li>
                    <li><strong>Menjodohkan:</strong> Tulis premis pasangan dengan tanda sama dengan, contoh: <code>Indonesia = Jakarta</code>.</li>
                </ul>
            </div>

            <!-- Hidden form for direct server-side document save -->
            <form id="directDocUploadForm" action="{{ route('admin.question-banks.import-document') }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="file" name="document_file" id="hiddenDirectDocFileInput">
            </form>

            <div class="qb-doc-modal-actions">
                <button type="button" class="md-btn-light qb-doc-btn-cancel" data-bs-dismiss="modal">Batal</button>
                <div class="qb-doc-btn-group">
                    <button type="button" id="btnApplyExtractedDoc" class="md-btn-secondary" onclick="applyExtractedDocQuestions()" disabled>
                        <i class="ti ti-sparkles"></i> Review di Form
                    </button>
                    <button type="button" id="btnDirectSaveDoc" class="md-btn-submit qb-doc-btn-save" onclick="directSaveExtractedDoc()" disabled>
                        <i class="ti ti-device-floppy"></i> Simpan ke Database
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Paste (Teks) -->
<div class="modal fade" id="modalQuickPaste" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="md-modal-content text-start p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;width:34px;height:34px;font-size:.9rem;">
                        <i class="ti ti-bolt"></i>
                    </div>
                    <h6 class="md-title mb-0" style="font-size:1rem;">Input Cepat Soal dari Teks</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <p class="md-subtitle mb-3" style="font-size:.8rem;">
                Tempelkan naskah soal Anda. Sistem otomatis mendeteksi format Pilihan Ganda, Benar/Salah, dan Menjodohkan.
            </p>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="md-form-label mb-0" style="font-size:.78rem;">Area Teks Naskah Soal:</label>
                <button type="button" class="md-btn-secondary" style="padding:.2rem .6rem;font-size:.72rem;" onclick="loadSampleQbText()">
                    <i class="ti ti-file-text"></i> Muat Contoh Format
                </button>
            </div>

            <textarea id="quickPasteQbTextarea" class="form-control font-monospace mb-3" rows="9" style="font-size:.8rem;line-height:1.45;" placeholder="1. Apa ibukota negara Indonesia?&#10;A. Jakarta&#10;B. Bandung&#10;C. Surabaya&#10;D. Medan&#10;Kunci: A&#10;&#10;2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.&#10;Kunci: Benar&#10;&#10;3. Jodohkan bahasa pemrograman dengan ekstensinya:&#10;PHP = .php&#10;Python = .py&#10;JavaScript = .js"></textarea>

            <div class="p-3 rounded-3 mb-4" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);font-size:.75rem;">
                <div class="fw-bold mb-1" style="color:var(--tblr-heading-color,#0f172a);"><i class="ti ti-info-circle text-primary me-1"></i> Panduan Format Cepat:</div>
                <ul class="mb-0 ps-3 text-muted">
                    <li><strong>Pilihan Ganda:</strong> Diawali nomor, opsi <code>A.</code>, <code>B.</code>, dst, dan baris kunci <code>Kunci: A</code>.</li>
                    <li><strong>Benar / Salah:</strong> Pernyataan diikuti baris <code>Kunci: Benar</code> atau <code>Kunci: Salah</code>.</li>
                    <li><strong>Menjodohkan:</strong> Pernyataan diikuti baris pasangan dengan tanda <code>=</code> (contoh: <code>PHP = .php</code>).</li>
                </ul>
            </div>

            <div class="md-modal-actions">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="md-btn-submit" onclick="parseAndInsertQbQuestions()">
                    <i class="ti ti-sparkles"></i> Konversi &amp; Masukkan ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sisipkan Gambar -->
<div class="modal fade" id="modalInsertImage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="md-modal-content text-start p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="md-page-icon" style="background:rgba(59,130,246,.1);color:#2563eb;width:34px;height:34px;">
                        <i class="ti ti-photo"></i>
                    </div>
                    <div>
                        <h6 class="md-title mb-0" style="font-size:1rem;">Lampirkan Gambar ke Soal</h6>
                        <div class="text-muted small" style="font-size:.75rem;">Gambar akan otomatis terpasang rapi tanpa kode rumit.</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <ul class="nav nav-pills nav-fill mb-3" id="imageTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-1.5" style="font-size: .82rem;" id="tabUploadImg" data-bs-toggle="pill" data-bs-target="#paneUploadImg" type="button"><i class="ti ti-upload me-1"></i> Upload File</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-1.5" style="font-size: .82rem;" id="tabUrlImg" data-bs-toggle="pill" data-bs-target="#paneUrlImg" type="button"><i class="ti ti-link me-1"></i> URL Gambar</button>
                </li>
            </ul>

            <div class="tab-content mb-3">
                <!-- Tab Upload -->
                <div class="tab-pane fade show active" id="paneUploadImg">
                    <label class="md-form-label mb-1">Pilih File Gambar (JPG, PNG, GIF, WebP)</label>
                    <input type="file" id="modalImgFileInput" class="form-control form-control-sm mb-2" accept="image/*">
                    <div id="modalImgUploadProgress" class="d-none text-center py-2">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="small ms-1">Mengunggah gambar...</span>
                    </div>
                </div>
                <!-- Tab URL -->
                <div class="tab-pane fade" id="paneUrlImg">
                    <label class="md-form-label mb-1">Tautan / URL Gambar</label>
                    <input type="url" id="modalImgUrlInput" class="form-control form-control-sm mb-2" placeholder="https://example.com/gambar.png">
                </div>
            </div>

            <!-- Preview Image -->
            <div id="modalImgPreviewWrap" class="p-2 word-preview-box rounded-3 text-center mb-3 d-none">
                <div class="text-muted small mb-1">Pratinjau Gambar:</div>
                <img id="modalImgPreviewEl" src="" alt="Pratinjau" style="max-height: 160px; max-width: 100%; border-radius: 6px;">
            </div>

            <div class="md-modal-actions">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="md-btn-submit" id="btnConfirmInsertImage" onclick="confirmInsertImage()">
                    <i class="ti ti-check"></i> Pasang Gambar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Simbol Matematika / Sains -->
<div class="modal fade" id="modalInsertSymbol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="md-modal-content text-start p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;width:34px;height:34px;">
                        <i class="ti ti-math-function"></i>
                    </div>
                    <h6 class="md-title mb-0" style="font-size:1rem;">Simbol Matematika &amp; Sains</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="d-flex flex-wrap gap-1.5 mb-3" id="symbolPalette">
                @php
                    $symbols = ['²', '³', '⁴', '√', '∛', 'π', '±', '÷', '×', '≤', '≥', '≠', '≈', '≡', '°', 'α', 'β', 'γ', 'θ', 'λ', 'μ', 'Σ', 'Δ', 'Ω', '½', '¼', '¾', '⅓', '⅔', '∞', '∫', '∂', '→', '↔', '∈', '∉', '⊂', '⊆', '∩', '∪'];
                @endphp
                @foreach($symbols as $sym)
                    <button type="button" class="symbol-btn" onclick="insertSymbolToActiveCard('{{ $sym }}')">
                        {{ $sym }}
                    </button>
                @endforeach
            </div>

            <div class="md-modal-actions">
                <button type="button" class="md-btn-submit w-100" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sisipkan Tabel (Rapi, Clean, Intuitif) -->
<div class="modal fade" id="modalInsertTable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="md-modal-content text-start p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="md-page-icon" style="background:rgba(16, 185, 129, 0.12);color:#059669;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti ti-table fs-2"></i>
                    </div>
                    <div>
                        <h6 class="md-title mb-0" style="font-size:1.05rem;">Sisipkan Tabel Baru</h6>
                        <span class="text-muted small" style="font-size:0.75rem;">Atur jumlah kolom &amp; baris tabel</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Stepper Kolom & Baris -->
            <div class="d-flex align-items-center gap-2 p-3 word-stepper-box rounded-3 mb-3">
                <div class="flex-fill text-center">
                    <span class="small fw-bold text-muted d-block mb-1" style="font-size:0.7rem;letter-spacing:.04em;">JUMLAH KOLOM</span>
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInput('qb_table_cols_input', -1)">−</button>
                        <input type="number" id="qb_table_cols_input" value="3" min="1" max="15" class="form-control form-control-sm text-center fw-bold text-primary" style="width:52px;font-size:1.05rem;" oninput="updateWordBadgeFromInputs()">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInput('qb_table_cols_input', 1)">+</button>
                    </div>
                </div>
                <div style="width:1px;height:46px;background:#cbd5e1;"></div>
                <div class="flex-fill text-center">
                    <span class="small fw-bold text-muted d-block mb-1" style="font-size:0.7rem;letter-spacing:.04em;">JUMLAH BARIS</span>
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInput('qb_table_rows_input', -1)">−</button>
                        <input type="number" id="qb_table_rows_input" value="3" min="1" max="25" class="form-control form-control-sm text-center fw-bold text-primary" style="width:52px;font-size:1.05rem;" oninput="updateWordBadgeFromInputs()">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInput('qb_table_rows_input', 1)">+</button>
                    </div>
                </div>
            </div>

            <!-- Ukuran Cepat (Presets) -->
            <div class="d-flex align-items-center justify-content-between gap-1 mb-3">
                <span class="small text-muted" style="font-size:0.75rem;">Ukuran Cepat:</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensions(2,2)">2×2</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensions(3,3)">3×3</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensions(4,3)">4×3</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensions(5,4)">5×4</button>
                </div>
            </div>

            <!-- Header Checkbox -->
            <div class="form-check text-start mb-3 ms-1">
                <input class="form-check-input" type="checkbox" id="qb_table_has_header" checked onchange="updateWordBadgeFromInputs()">
                <label class="form-check-label small fw-semibold word-checkbox-label cursor-pointer" for="qb_table_has_header">
                    Jadikan baris pertama sebagai Judul (Header)
                </label>
            </div>

            <!-- Pratinjau Tabel -->
            <div class="p-2.5 mb-3 word-preview-box rounded-3">
                <div class="small fw-semibold text-muted text-center mb-1.5" style="font-size:0.72rem;">Pratinjau Bentuk Tabel:</div>
                <div id="qb_word_mini_preview"></div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary w-50 py-2 fw-bold" onclick="confirmInsertWordTable()">
                    <i class="ti ti-check me-1"></i> Buat Tabel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom Delete Card Confirmation -->
<div class="modal fade" id="modalConfirmDeleteCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Butir Soal Ini?</h6>
            <p class="md-modal-text">Butir soal ini akan dihapus dari form pembuatan.</p>
            <div class="md-modal-actions">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmDeleteCard" class="md-btn-danger">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom Alert Info -->
<div class="modal fade" id="modalAlertQb" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon warning" style="background:rgba(245,158,11,.1);color:#d97706;"><i class="ti ti-info-circle"></i></div>
            <h6 class="md-modal-title" id="modalAlertTitle">Pemberitahuan</h6>
            <p class="md-modal-text" id="modalAlertMsg"></p>
            <div class="md-modal-actions">
                <button type="button" class="md-btn-submit w-100" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')

<style>
/* Header Buttons & Badges */
.qb-doc-upload-btn {
    background: #ffffff !important;
    color: #0369a1 !important;
    border: 1.5px solid #0284c7 !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15) !important;
}
.qb-doc-upload-btn:hover {
    background: #f0f9ff !important;
    color: #0284c7 !important;
}
.qb-quick-paste-btn {
    background: #ffffff !important;
    color: #b45309 !important;
    border: 1.5px solid #d97706 !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.15) !important;
}
.qb-quick-paste-btn:hover {
    background: #fffbeb !important;
    color: #92400e !important;
    border-color: #b45309 !important;
}
.qb-counter-badge {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #7dd3fc !important;
    font-weight: 700 !important;
}
.qb-info-alert {
    background: #f0fdfa !important;
    border: 1px solid #ccfbf1 !important;
    color: #134e4a !important;
    font-size: .84rem;
}

/* Card Builder Item */
.qb-builder-item {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}
.qb-builder-item:hover {
    border-color: #0284c7;
    box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08);
}
.qb-builder-item .border-bottom {
    border-bottom: 1px solid #e2e8f0 !important;
}

/* Add More Button */
.qb-btn-add-more {
    width: 100%;
    padding: .8rem 1rem;
    background: #ffffff;
    border: 2px dashed #0284c7;
    border-radius: 10px;
    color: #0369a1;
    font-weight: 800;
    font-size: .84rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    cursor: pointer;
    transition: all .18s ease;
}
.qb-btn-add-more:hover {
    background: #f0f9ff;
    border-color: #0369a1;
    color: #0284c7;
    transform: translateY(-1px);
}

/* Format Pills */
.qb-format-pills {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
}
.qb-format-pill {
    flex: 1;
    min-width: 120px;
}
.qb-format-pill input {
    display: none;
}
.qb-format-pill label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    width: 100%;
    padding: .5rem .75rem;
    border-radius: 8px;
    border: 1.5px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
    margin: 0;
}
.qb-format-pill label:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.qb-format-pill input:checked + label {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0369a1;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
}

/* Options Container & Rows */
.qb-option-row {
    display: flex;
    align-items: center;
    gap: .55rem;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: .45rem .75rem;
    transition: all .15s ease;
}
.qb-option-row:hover {
    border-color: #94a3b8;
    background: #ffffff;
}
.qb-option-row:focus-within {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}
.qb-opt-radio-wrap {
    display: flex;
    align-items: center;
    gap: .35rem;
    font-size: .82rem;
    font-weight: 800;
    color: #475569;
    cursor: pointer;
    flex-shrink: 0;
}
.qb-opt-radio-wrap input:checked ~ span {
    color: #059669;
}
.qb-option-input {
    border: none !important;
    background: transparent !important;
    padding: .25rem .3rem !important;
    font-size: .84rem !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    box-shadow: none !important;
    outline: none !important;
    flex: 1;
}

/* Toolbar & Preview Styles */
.qb-toolbar-wrap {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
}
.qb-toolbar-btn {
    padding: .28rem .6rem;
    font-size: .75rem;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
    text-decoration: none;
    user-select: none;
}
.qb-toolbar-btn:hover {
    background: #f0f9ff;
    border-color: #0284c7;
    color: #0284c7;
}
.qb-toolbar-btn:active {
    transform: scale(0.98);
}
@media (max-width: 576px) {
    .qb-question-header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: .5rem !important;
    }
    .qb-question-header label {
        width: 100%;
        margin-bottom: 0;
    }
    .qb-toolbar-wrap {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        width: 100%;
        gap: .35rem;
        background: #f8fafc;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .qb-toolbar-btn {
        width: 100%;
        justify-content: center;
        padding: .44rem .2rem;
        font-size: .74rem;
        border-color: #cbd5e1;
        background: #ffffff;
    }
    .qb-toolbar-btn i {
        font-size: .9rem;
    }
}
@media (max-width: 375px) {
    .qb-toolbar-wrap {
        gap: .25rem;
        padding: 3px;
    }
    .qb-toolbar-btn {
        padding: .4rem .15rem;
        font-size: .69rem;
        letter-spacing: -0.2px;
        gap: .2rem;
    }
    .qb-toolbar-btn i {
        font-size: .84rem;
    }
}
.qb-live-preview-box {
    background: #f8fafc;
    border: 1.5px dashed #0284c7;
    border-radius: 8px;
    font-size: .88rem;
    line-height: 1.6;
}
.qb-live-preview-box img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.qb-live-preview-box table {
    width: 100%;
    margin-top: .5rem;
    margin-bottom: .5rem;
}
.symbol-btn {
    font-size: .95rem;
    min-width: 38px;
    font-family: serif;
}

/* ============================================================
   DARK MODE OVERRIDES
   ============================================================ */
[data-theme="dark"] .qb-doc-upload-btn {
    background: rgba(2, 132, 199, 0.15) !important;
    color: #38bdf8 !important;
    border-color: rgba(56, 189, 248, 0.4) !important;
}
[data-theme="dark"] .qb-quick-paste-btn {
    background: rgba(245, 158, 11, 0.15) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.4) !important;
}
[data-theme="dark"] .qb-builder-item {
    background: #151e32;
    border-color: #243049;
}
[data-theme="dark"] .qb-toolbar-wrap {
    background: rgba(15, 23, 42, 0.6) !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .qb-toolbar-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-toolbar-btn:hover {
    background-color: #334155 !important;
    border-color: #38bdf8 !important;
    color: #38bdf8 !important;
}
[data-theme="dark"] .qb-option-row {
    background: rgba(15, 23, 42, 0.65);
}
/* Image Attachment Box - Clean Preview Only */
.qb-image-box {
    background: #ffffff;
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.qb-image-box:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.12);
}
.qb-image-box img {
    max-height: 190px;
    max-width: 100%;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fafafa;
}

/* Word / Excel Rich Table Editor */
.qb-word-table-editor {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
}
.qb-table-toolbar {
    background: linear-gradient(135deg, #f1f5f9 0%, #e9eff6 100%);
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
.qb-tbl-tool-btn {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    font-size: .74rem !important;
    font-weight: 600 !important;
    padding: .25rem .5rem !important;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    color: #334155 !important;
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
    border-radius: 5px !important;
}
.qb-tbl-tool-btn:hover {
    background: #e2e8f0 !important;
    color: #0284c7 !important;
    border-color: #94a3b8 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,.08) !important;
}
/* Merge btn - primary accent */
.qb-tbl-btn-merge {
    background: #eff6ff !important;
    border: 1.5px solid #93c5fd !important;
    color: #1d4ed8 !important;
    font-weight: 700 !important;
}
.qb-tbl-btn-merge:hover {
    background: #dbeafe !important;
    border-color: #3b82f6 !important;
    color: #1d4ed8 !important;
}
/* Split btn */
.qb-tbl-btn-split {
    background: #f0fdf4 !important;
    border: 1.5px solid #86efac !important;
    color: #15803d !important;
    font-weight: 700 !important;
}
.qb-tbl-btn-split:hover {
    background: #dcfce7 !important;
    border-color: #4ade80 !important;
}
/* Border btn - info accent */
.qb-tbl-btn-border {
    background: #f0f9ff !important;
    border: 1.5px solid #7dd3fc !important;
    color: #0369a1 !important;
    font-weight: 700 !important;
}
.qb-tbl-btn-border:hover {
    background: #e0f2fe !important;
    border-color: #0284c7 !important;
}
/* Selection status bar */
.qb-sel-status {
    font-size: .72rem;
    font-weight: 700;
    color: #0369a1;
    background: #e0f2fe;
    border: 1px solid #7dd3fc;
    border-radius: 5px;
    padding: .18rem .55rem;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    white-space: nowrap;
}
.qb-sel-status.has-selection {
    background: #dbeafe;
    color: #1d4ed8;
    border-color: #93c5fd;
}
/* Border swatch grid */
.qb-border-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    padding: 6px;
}
.qb-border-swatch {
    width: 48px;
    height: 40px;
    border: 1.5px solid #cbd5e1;
    border-radius: 5px;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: .62rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all .15s ease;
    gap: 2px;
}
.qb-border-swatch:hover {
    border-color: #0284c7;
    background: #eff6ff;
    color: #0369a1;
    transform: scale(1.05);
}
.qb-border-swatch svg {
    width: 20px;
    height: 20px;
}
/* Color ring */
.qb-color-ring {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    border: 2px solid #e2e8f0;
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
}
.qb-color-ring:hover {
    border-color: #0284c7;
    transform: scale(1.1);
}
.qb-rich-table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 0;
}
.qb-rich-table th,
.qb-rich-table td {
    min-width: 85px;
    min-height: 36px;
    padding: 8px 10px;
    vertical-align: middle;
    outline: none;
    transition: background-color .15s ease, box-shadow .15s ease;
    border: 1px solid #cbd5e1;
    position: relative;
    cursor: cell;
}
.qb-rich-table th {
    background: #f1f5f9;
    font-weight: 700;
    color: #0f172a;
    text-align: center;
}
.qb-rich-table td {
    background: #ffffff;
    color: #1e293b;
}
.qb-rich-table td:empty::before,
.qb-rich-table th:empty::before {
    content: '...';
    color: #cbd5e1;
    pointer-events: none;
}
.qb-rich-table .qb-cell-selected {
    box-shadow: inset 0 0 0 2.5px #3b82f6 !important;
    background-color: rgba(219, 234, 254, 0.7) !important;
    color: #0f172a !important;
}
.qb-rich-table.table-borderless td,
.qb-rich-table.table-borderless th {
    border: 1px dashed #e2e8f0;
}
.qb-rich-table.qb-border-outside {
    border: 2.5px solid #475569;
}
.qb-rich-table.qb-border-horizontal td,
.qb-rich-table.qb-border-horizontal th {
    border-left: none; border-right: none;
    border-top: none; border-bottom: 1px solid #cbd5e1;
}

/* ── Selection mode indicator ── */
.qb-sel-mode-badge {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    font-weight: 700;
    color: #1d4ed8;
    background: #dbeafe;
    border: 1.5px solid #93c5fd;
    border-radius: 20px;
    padding: 3px 10px 3px 8px;
    animation: pulseIn .2s ease;
}
.qb-sel-mode-badge.visible { display: flex; }
@keyframes pulseIn {
    from { opacity:0; transform: scale(.9); }
    to   { opacity:1; transform: scale(1); }
}
.qb-sel-mode-badge .badge-clear {
    background: none; border: none;
    color: #1d4ed8; cursor: pointer;
    font-size: .85rem; line-height: 1;
    padding: 0 0 0 2px;
    opacity: .7;
}
.qb-sel-mode-badge .badge-clear:hover { opacity: 1; }

/* ── Floating Bottom Action Bar ── */
.qb-float-bar {
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 200;
    background: #ffffff;
    border-top: 2px solid #e2e8f0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    opacity: 0;
    pointer-events: none;
    transform: translateY(8px);
    transition: opacity .2s ease, transform .2s ease;
    box-shadow: 0 -4px 20px rgba(0,0,0,.1);
}
.qb-float-bar.visible {
    opacity: 1;
    pointer-events: all;
    transform: translateY(0);
}
/* Mobile: fixed to bottom of viewport */
@media (max-width: 767px) {
    .qb-float-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px 12px env(safe-area-inset-bottom, 8px);
        z-index: 1050;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        gap: 4px;
        scrollbar-width: none;
    }
    .qb-float-bar::-webkit-scrollbar { display: none; }
    /* Push page content up so bar doesn't cover table */
    .qb-float-bar-spacer { height: 72px; }
}
/* Bar action buttons */
.qb-fab {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    min-width: 52px;
    padding: 7px 10px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    color: #334155;
    font-size: .62rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s ease;
    flex-shrink: 0;
    user-select: none;
    -webkit-user-select: none;
    /* touch target minimum 44px */
    min-height: 52px;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}
.qb-fab svg { flex-shrink: 0; }
.qb-fab:hover, .qb-fab:active {
    background: #f1f5f9;
    border-color: #94a3b8;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.qb-fab.fab-merge   { border-color: #93c5fd; color: #1d4ed8; background: #eff6ff; }
.qb-fab.fab-merge:hover { background: #dbeafe; border-color: #60a5fa; }
.qb-fab.fab-split   { border-color: #86efac; color: #15803d; background: #f0fdf4; }
.qb-fab.fab-split:hover { background: #dcfce7; border-color: #4ade80; }
.qb-fab.fab-border  { border-color: #7dd3fc; color: #0369a1; background: #f0f9ff; }
.qb-fab.fab-border:hover { background: #e0f2fe; border-color: #38bdf8; }
.qb-fab.fab-color   { border-color: #fbbf24; color: #92400e; background: #fffbeb; }
.qb-fab.fab-color:hover { background: #fef3c7; border-color: #f59e0b; }
.qb-fab.fab-danger  { border-color: #fca5a5; color: #b91c1c; background: #fef2f2; }
.qb-fab.fab-danger:hover { background: #fee2e2; border-color: #f87171; }

.qb-fab-sep {
    width: 1px;
    height: 36px;
    background: #e2e8f0;
    flex-shrink: 0;
    align-self: center;
}

/* ── Border Modal (replaces dropdown on mobile) ── */
.qb-border-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1100;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
}
.qb-border-modal-overlay.open {
    opacity: 1;
    pointer-events: all;
}
.qb-border-modal {
    background: #ffffff;
    border-radius: 20px 20px 0 0;
    width: 100%;
    max-width: 480px;
    max-height: 85vh;
    overflow-y: auto;
    padding: 0 0 env(safe-area-inset-bottom, 16px);
    transform: translateY(100%);
    transition: transform .25s cubic-bezier(.4,0,.2,1);
    box-shadow: 0 -8px 40px rgba(0,0,0,.2);
}
.qb-border-modal-overlay.open .qb-border-modal {
    transform: translateY(0);
}
@media (min-width: 768px) {
    .qb-border-modal-overlay {
        align-items: center;
        padding: 24px;
    }
    .qb-border-modal {
        border-radius: 18px;
        transform: scale(0.96);
        max-height: 90vh;
        box-shadow: 0 24px 60px rgba(0,0,0,0.25);
    }
    .qb-border-modal-overlay.open .qb-border-modal {
        transform: scale(1);
    }
    .qb-border-modal-handle {
        display: none;
    }
}
.qb-border-modal-handle {
    width: 40px; height: 4px;
    background: #cbd5e1;
    border-radius: 2px;
    margin: 10px auto 4px;
}
.qb-border-modal-title {
    text-align: center;
    font-weight: 700;
    font-size: .9rem;
    color: #0f172a;
    padding: 8px 16px 12px;
    border-bottom: 1px solid #f1f5f9;
}
.qb-border-modal-sub {
    font-size: .72rem;
    color: #64748b;
    font-weight: 400;
    display: block;
    margin-top: 2px;
}

/* Border swatch grid - responsive */
.qb-border-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    padding: 14px;
}
@media (min-width: 400px) {
    .qb-border-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (min-width: 540px) {
    .qb-border-grid { grid-template-columns: repeat(5, 1fr); }
}
.qb-border-swatch {
    aspect-ratio: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: .65rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all .15s ease;
    gap: 4px;
    padding: 8px 4px;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
    min-height: 60px;
}
.qb-border-swatch:hover, .qb-border-swatch:active {
    border-color: #0284c7;
    background: #eff6ff;
    color: #0369a1;
    transform: scale(1.04);
}
.qb-border-swatch svg { width: 26px; height: 26px; }

/* Color picker grid */
.qb-color-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 16px;
}
.qb-color-ring {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2.5px solid rgba(0,0,0,.08);
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
}
.qb-color-ring:hover, .qb-color-ring:active {
    border-color: #0284c7;
    transform: scale(1.15);
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.qb-color-modal-section {
    padding: 10px 16px 6px;
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.qb-modal-close-btn {
    display: block;
    width: calc(100% - 32px);
    margin: 10px 16px 16px;
    padding: 12px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f1f5f9;
    font-weight: 700;
    font-size: .82rem;
    color: #334155;
    cursor: pointer;
    text-align: center;
    touch-action: manipulation;
}
.qb-modal-close-btn:hover { background: #e2e8f0; }

/* ═══════════════════════════════════════
   DARK MODE OVERRIDES
═══════════════════════════════════════ */
[data-theme="dark"] .qb-doc-upload-btn {
    background: rgba(2, 132, 199, 0.15) !important;
    color: #38bdf8 !important;
    border-color: rgba(56, 189, 248, 0.4) !important;
}
[data-theme="dark"] .qb-quick-paste-btn {
    background: rgba(245, 158, 11, 0.15) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.4) !important;
}
[data-theme="dark"] .qb-builder-item {
    background: #151e32; border-color: #243049;
}
[data-theme="dark"] .qb-toolbar-btn {
    background: #1e293b; border-color: #334155; color: #cbd5e1;
}
[data-theme="dark"] .qb-option-row {
    background: rgba(15, 23, 42, 0.65); border-color: #334155;
}
[data-theme="dark"] .qb-image-box {
    background: #1e293b; border-color: #334155;
}
[data-theme="dark"] .qb-image-box img {
    border-color: #334155; background: #0f172a;
}
[data-theme="dark"] .qb-word-table-editor {
    background: #0f172a; border-color: #243049;
}
[data-theme="dark"] .qb-tbl-header {
    background: linear-gradient(135deg, #1e293b 0%, #162032 100%);
    border-color: #334155 !important;
}
[data-theme="dark"] .qb-tbl-header .fw-bold {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .qb-tbl-hint {
    color: #94a3b8;
}
[data-theme="dark"] .qb-table-canvas-wrap {
    background: #0f172a;
}
[data-theme="dark"] .qb-rich-table th {
    background: #1e293b; color: #38bdf8; border-color: #334155;
}
[data-theme="dark"] .qb-rich-table td {
    background: #0f172a; color: #f8fafc; border-color: #334155;
}
[data-theme="dark"] .qb-rich-table .qb-cell-selected {
    box-shadow: inset 0 0 0 2.5px #38bdf8 !important;
    background-color: rgba(56, 189, 248, 0.22) !important;
    color: #ffffff !important;
}
[data-theme="dark"] .qb-rich-table td:empty::before,
[data-theme="dark"] .qb-rich-table th:empty::before {
    color: #475569;
}
[data-theme="dark"] .qb-float-bar {
    background: rgba(15, 23, 42, 0.95);
    border-color: #334155;
    box-shadow: 0 8px 32px rgba(0,0,0,0.6);
}
[data-theme="dark"] .qb-fab {
    background: #1e293b; color: #e2e8f0; border-color: #334155;
}
[data-theme="dark"] .qb-fab:hover,
[data-theme="dark"] .qb-fab:active {
    background: #334155; color: #38bdf8; border-color: #475569;
}
[data-theme="dark"] .qb-fab.fab-merge {
    background: rgba(30, 58, 138, 0.5); color: #93c5fd; border-color: #3b82f6;
}
[data-theme="dark"] .qb-fab.fab-split {
    background: rgba(20, 83, 45, 0.5); color: #86efac; border-color: #22c55e;
}
[data-theme="dark"] .qb-fab.fab-border {
    background: rgba(12, 74, 110, 0.5); color: #7dd3fc; border-color: #0284c7;
}
[data-theme="dark"] .qb-fab.fab-color {
    background: rgba(88, 28, 135, 0.5); color: #d8b4fe; border-color: #a855f7;
}
[data-theme="dark"] .qb-fab.fab-danger {
    background: rgba(127, 29, 29, 0.4); color: #fca5a5; border-color: #ef4444;
}
[data-theme="dark"] .qb-border-modal {
    background: #1e293b; color: #f8fafc; box-shadow: 0 -8px 40px rgba(0,0,0,0.6);
}
[data-theme="dark"] .qb-border-modal-title {
    color: #f8fafc; border-color: #334155;
}
[data-theme="dark"] .qb-border-modal-sub {
    color: #94a3b8;
}
[data-theme="dark"] .qb-border-modal-handle {
    background: #475569;
}
[data-theme="dark"] .qb-border-swatch {
    background: #0f172a; border-color: #334155; color: #94a3b8;
}
[data-theme="dark"] .qb-border-swatch:hover,
[data-theme="dark"] .qb-border-swatch:active {
    background: #1e293b; border-color: #38bdf8; color: #7dd3fc;
}
[data-theme="dark"] .qb-border-swatch svg line,
[data-theme="dark"] .qb-border-swatch svg rect {
    stroke: #94a3b8;
}
[data-theme="dark"] .qb-color-modal-section {
    color: #94a3b8;
}
[data-theme="dark"] .qb-modal-close-btn {
    background: #0f172a; border-color: #334155; color: #cbd5e1;
}
[data-theme="dark"] .qb-modal-close-btn:hover {
    background: #1e293b; color: #f1f5f9;
}
[data-theme="dark"] .qb-sel-mode-badge {
    background: rgba(30, 58, 138, 0.5); color: #93c5fd; border-color: #3b82f6;
}
[data-theme="dark"] .qb-live-preview-box {
    background: #0f172a; border-color: #38bdf8; color: #f8fafc;
}

/* Word-style Interactive Insert Grid Picker */
.word-grid-picker-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 16px;
}
.word-grid-picker {
    display: grid;
    grid-template-columns: repeat(8, 28px);
    grid-template-rows: repeat(8, 28px);
    gap: 4px;
    padding: 10px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    user-select: none;
    touch-action: none;
}
.word-grid-cell {
    width: 28px;
    height: 28px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    transition: background-color .1s ease, border-color .1s ease;
}
.word-grid-cell.highlighted {
    background: #2563eb;
    border-color: #1d4ed8;
}
.word-grid-status-badge {
    margin-top: 10px;
    font-size: .85rem;
    font-weight: 700;
    color: #1d4ed8;
    background: #eff6ff;
    padding: 4px 16px;
    border-radius: 20px;
    border: 1px solid #bfdbfe;
}
.word-preset-chip {
    padding: 6px 14px;
    font-size: .78rem;
    font-weight: 700;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    cursor: pointer;
    transition: all .15s ease;
}
.word-preset-chip:hover {
    background: #e0f2fe;
    border-color: #38bdf8;
    color: #0369a1;
    transform: translateY(-1px);
}
.word-spin-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s ease;
}
.word-spin-btn:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
}

[data-theme="dark"] .word-grid-picker {
    background: #0f172a;
    border-color: #334155;
}
[data-theme="dark"] .word-grid-cell {
    background: #1e293b;
    border-color: #475569;
}
[data-theme="dark"] .word-grid-cell.highlighted {
    background: #0284c7;
    border-color: #38bdf8;
}
[data-theme="dark"] .word-grid-status-badge {
    background: rgba(30, 58, 138, 0.4);
    color: #93c5fd;
    border-color: #3b82f6;
}
[data-theme="dark"] .word-preset-chip {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
[data-theme="dark"] .word-preset-chip:hover {
    background: #334155;
    color: #38bdf8;
}
[data-theme="dark"] .word-spin-btn {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
[data-theme="dark"] .word-spin-btn:hover {
    background: #334155;
}

/* ── Word-Style Table Ribbon Toolbar & Editor Layout ── */
.qb-word-table-editor {
    display: block !important;
    width: 100% !important;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,.04);
    overflow: hidden;
    position: relative;
    box-sizing: border-box !important;
    margin-bottom: 0.75rem;
}
.qb-table-ribbon {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 8px 12px !important;
    background: #f8fafc !important;
    border-bottom: 1.5px solid #e2e8f0 !important;
    width: 100% !important;
    box-sizing: border-box !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.qb-table-ribbon::-webkit-scrollbar {
    display: none;
}
.qb-ribbon-group {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-shrink: 0 !important;
}
.qb-ribbon-sep {
    width: 1px !important;
    height: 22px !important;
    background: #cbd5e1 !important;
    margin: 0 3px !important;
    flex-shrink: 0 !important;
}
.qb-ribbon-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 5px 11px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #334155 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    user-select: none !important;
    line-height: 1.25 !important;
    flex-shrink: 0 !important;
}
.qb-ribbon-btn:hover {
    background: #eff6ff !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
    transform: translateY(-1px) !important;
}
.qb-ribbon-btn-danger {
    color: #ef4444 !important;
}
.qb-ribbon-btn-danger:hover {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
    color: #dc2626 !important;
}
.qb-ribbon-btn:disabled {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    background: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
    color: #94a3b8 !important;
    transform: none !important;
}
.qb-table-canvas-wrap {
    display: block !important;
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    padding: 1rem !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
}
.qb-rich-table {
    border-collapse: collapse !important;
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed !important;
    margin-bottom: 0 !important;
    background: #ffffff !important;
    box-sizing: border-box !important;
}
.qb-rich-table th,
.qb-rich-table td {
    min-width: 90px !important;
    min-height: 44px !important;
    padding: 10px 14px !important;
    vertical-align: top !important;
    outline: none !important;
    transition: background-color .12s ease, box-shadow .12s ease;
    border: 1px solid #cbd5e1 !important;
    position: relative;
    cursor: text;
    font-size: 0.88rem !important;
    line-height: 1.45 !important;
    user-select: text;
    -webkit-user-select: text;
    word-break: break-word;
}
.qb-rich-table th {
    background: #f1f5f9 !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    text-align: left !important;
}
.qb-rich-table td {
    background: #ffffff !important;
    color: #1e293b !important;
}
.qb-rich-table td:empty::before,
.qb-rich-table th:empty::before {
    content: '...' !important;
    color: #94a3b8 !important;
    pointer-events: none;
}
.qb-rich-table .qb-cell-selected {
    box-shadow: inset 0 0 0 2.5px #2563eb !important;
    background-color: rgba(219, 234, 254, 0.75) !important;
    color: #0f172a !important;
}
.qb-table-footer-actions {
    padding: 8px 12px;
    background: #f8fafc;
    border-top: 1px dashed #e2e8f0;
    text-align: center;
}

/* ── Responsive Enhancements Across Screen Sizes ── */
@media (min-width: 992px) {
    .qb-table-ribbon {
        flex-wrap: wrap !important;
    }
}
@media (max-width: 768px) {
    .qb-table-canvas-wrap {
        padding: 0.65rem !important;
    }
    .qb-rich-table th,
    .qb-rich-table td {
        min-width: 80px !important;
        padding: 8px 10px !important;
        font-size: 0.82rem !important;
    }
}
@media (max-width: 576px) {
    .qb-word-table-editor .bg-light.border-bottom {
        padding: 8px 10px !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
    }
    .qb-word-table-editor .bg-light.border-bottom .badge {
        font-size: 0.72rem !important;
        padding: 4px 7px !important;
    }
    .qb-word-table-editor .bg-light.border-bottom .btn {
        font-size: 0.72rem !important;
        padding: 3px 8px !important;
    }
    .qb-table-footer-actions {
        padding: 8px 10px !important;
    }
    .qb-table-footer-actions .btn {
        width: 100% !important;
        padding: 6px 12px !important;
        font-size: 0.8rem !important;
    }
    .modal-dialog {
        margin: 0.5rem auto !important;
        max-width: calc(100% - 1rem) !important;
    }
    .md-modal-content {
        padding: 1.25rem !important;
    }
    .word-stepper-box {
        padding: 0.65rem !important;
        gap: 0.5rem !important;
    }
    .word-spin-btn {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
    }
}

/* Modal Title Color Fix for Light & Dark Mode */
.md-modal-content .md-title,
.modal-content .md-title {
    color: #0f172a !important;
    text-shadow: none !important;
}

/* ── Symbol Button Palette (Modal Simbol) ── */
.symbol-btn {
    min-width: 40px;
    height: 40px;
    font-size: 1.15rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    transition: all 0.15s ease;
    cursor: pointer;
}
.symbol-btn:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1d4ed8;
    transform: translateY(-1px) scale(1.05);
}

/* ── Mini Preview Table (Modal Tabel) ── */
.word-mini-th {
    border: 1px solid #cbd5e1;
    background: #e2e8f0;
    padding: 4px;
    text-align: center;
    color: #334155;
    font-weight: 700;
}
.word-mini-td {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding: 4px;
    text-align: center;
    color: #94a3b8;
}

/* ── Dark Mode for Table Ribbon, Editor & Modals ── */
[data-theme="dark"] .qb-word-table-editor {
    background: #151e32 !important;
    border-color: #243049 !important;
}
[data-theme="dark"] .qb-word-table-editor .bg-light {
    background: #1e293b !important;
    border-color: #243049 !important;
}
[data-theme="dark"] .qb-word-table-editor .text-dark {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .qb-table-ribbon {
    background: #151e32 !important;
    border-color: #243049 !important;
}
[data-theme="dark"] .qb-ribbon-sep {
    background: #334155 !important;
}
[data-theme="dark"] .qb-ribbon-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-ribbon-btn:hover {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .qb-ribbon-btn-danger {
    color: #f87171 !important;
}
[data-theme="dark"] .qb-ribbon-btn-danger:hover {
    background: rgba(239, 68, 68, 0.2) !important;
    border-color: #ef4444 !important;
    color: #fca5a5 !important;
}
[data-theme="dark"] .qb-ribbon-btn:disabled {
    background: #0f172a !important;
    border-color: #1e293b !important;
    color: #475569 !important;
    opacity: 0.38 !important;
}
[data-theme="dark"] .qb-table-canvas-wrap {
    background: #0b1120 !important;
}
[data-theme="dark"] .qb-rich-table {
    background: #0b1120 !important;
}
[data-theme="dark"] .qb-rich-table th {
    background: #1e293b !important;
    color: #cbd5e1 !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .qb-rich-table td {
    background: #0f172a !important;
    color: #f1f5f9 !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .qb-rich-table td:empty::before,
[data-theme="dark"] .qb-rich-table th:empty::before {
    color: #475569 !important;
}
[data-theme="dark"] .qb-rich-table td.qb-cell-selected,
[data-theme="dark"] .qb-rich-table th.qb-cell-selected {
    outline: 2px solid #38bdf8 !important;
    background: rgba(14, 165, 233, 0.15) !important;
}
[data-theme="dark"] .qb-table-footer-actions {
    background: #0b1120 !important;
    border-color: #243049 !important;
}

/* ── Dark Mode Universal for Modals (Gambar, Tabel, Simbol) ── */
[data-theme="dark"] .modal-content,
[data-theme="dark"] .md-modal-content {
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    color: #f8fafc !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5) !important;
}
[data-theme="dark"] .md-modal-content .md-title,
[data-theme="dark"] .md-modal-content h5,
[data-theme="dark"] .md-modal-content h6 {
    color: #f8fafc !important;
}
[data-theme="dark"] .md-modal-content .text-muted,
[data-theme="dark"] .md-modal-content .small.text-muted {
    color: #94a3b8 !important;
}
[data-theme="dark"] .md-modal-content .text-dark {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .md-modal-content .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}
[data-theme="dark"] .md-modal-content .nav-pills .nav-link {
    background: #0f172a !important;
    color: #94a3b8 !important;
    border: 1px solid #334155 !important;
}
[data-theme="dark"] .md-modal-content .nav-pills .nav-link:hover {
    color: #f8fafc !important;
    background: #1e293b !important;
}
[data-theme="dark"] .md-modal-content .nav-pills .nav-link.active {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
}
[data-theme="dark"] .md-modal-content .form-control {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .md-modal-content .form-control:focus {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
}
[data-theme="dark"] .md-modal-content .form-control::placeholder {
    color: #64748b !important;
}
[data-theme="dark"] .md-modal-content .md-form-label {
    color: #cbd5e1 !important;
}
.word-stepper-box,
.word-preview-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
}
.word-checkbox-label {
    color: #1e293b;
}
.word-spin-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #1e293b;
}

[data-theme="dark"] .word-stepper-box,
[data-theme="dark"] .word-preview-box {
    background-color: #0f172a !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .word-checkbox-label {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .word-spin-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .word-spin-btn:hover {
    background: #334155 !important;
    border-color: #475569 !important;
}
[data-theme="dark"] .md-modal-content .btn-white,
[data-theme="dark"] .md-modal-content .btn-outline-secondary {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .md-modal-content .btn-white:hover,
[data-theme="dark"] .md-modal-content .btn-outline-secondary:hover {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .symbol-btn {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .symbol-btn:hover {
    background-color: #334155 !important;
    border-color: #38bdf8 !important;
    color: #38bdf8 !important;
}
[data-theme="dark"] .word-mini-th {
    border-color: #334155 !important;
    background: #1e293b !important;
    color: #38bdf8 !important;
}
[data-theme="dark"] .word-mini-td {
    border-color: #334155 !important;
    background: #0f172a !important;
    color: #64748b !important;
}
[data-theme="dark"] .md-modal-actions .md-btn-light {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .md-modal-actions .md-btn-light:hover {
    background: #1e293b !important;
    color: #f8fafc !important;
}

/* Document Upload Modal Styles */
.qb-doc-dropzone {
    padding: 1.6rem 1.25rem;
    border-radius: 12px;
    border: 2px dashed rgba(8, 145, 178, 0.38);
    background: rgba(8, 145, 178, 0.03);
    text-align: center;
    transition: all .2s ease;
}
.qb-doc-dropzone:hover {
    border-color: #0891b2;
    background: rgba(8, 145, 178, 0.06);
}
.qb-doc-dropzone-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto .65rem;
    border-radius: 50%;
    background: rgba(8, 145, 178, 0.12);
    color: #0891b2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
}
.qb-doc-dropzone-title {
    color: var(--tblr-heading-color, #0f172a);
    font-size: .94rem;
}
.qb-doc-template-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .85rem 1rem;
    border-radius: 10px;
    background: rgba(8, 145, 178, 0.05);
    border: 1px solid rgba(8, 145, 178, 0.2);
    transition: all .2s ease;
}
.qb-doc-template-content {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex: 1;
    min-width: 0;
}
.qb-doc-template-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(8, 145, 178, 0.15);
    color: #0891b2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.qb-doc-template-title {
    font-weight: 700;
    font-size: .84rem;
    color: var(--tblr-heading-color, #0f172a);
    line-height: 1.35;
}
.qb-doc-template-sub {
    font-size: .74rem;
    color: var(--tblr-muted, #64748b);
    line-height: 1.35;
}
.qb-doc-template-btn {
    font-size: .78rem;
    font-weight: 700;
    padding: .45rem .85rem;
    border-radius: 8px;
    white-space: nowrap;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    box-shadow: none !important;
}
.qb-doc-tips-box {
    padding: .85rem 1rem;
    border-radius: 10px;
    background: rgba(245, 158, 11, 0.07);
    border: 1px solid rgba(245, 158, 11, 0.22);
    font-size: .76rem;
}
.qb-doc-tips-header {
    font-weight: 700;
    color: #b45309;
    display: flex;
    align-items: center;
    gap: .4rem;
    margin-bottom: .5rem;
    font-size: .79rem;
}
.qb-doc-tips-list {
    padding-left: 1.25rem;
    margin: 0;
    color: var(--tblr-body-color, #334155);
    display: flex;
    flex-direction: column;
    gap: .28rem;
}
.qb-doc-tips-list code {
    background: rgba(245, 158, 11, 0.15);
    color: #b45309;
    padding: .1rem .35rem;
    border-radius: 4px;
    font-size: .73rem;
}
.qb-doc-modal-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .65rem;
    flex-wrap: wrap;
    margin-top: 1.15rem;
    padding-top: .5rem;
}
.qb-doc-btn-group {
    display: flex;
    align-items: center;
    gap: .65rem;
    flex-wrap: wrap;
}
.qb-doc-btn-save {
    background: #059669 !important;
    border-color: #059669 !important;
    color: #ffffff !important;
}
.qb-doc-btn-save:hover {
    background: #047857 !important;
    border-color: #047857 !important;
}

/* Modal Document Upload - Responsive */
@media (max-width: 576px) {
    #modalDocUpload .md-modal-content {
        padding: 1.15rem !important;
    }
    .qb-doc-dropzone {
        padding: 1.25rem .75rem;
    }
    .qb-doc-template-box {
        flex-direction: column;
        align-items: stretch;
        gap: .75rem;
    }
    .qb-doc-template-btn {
        width: 100%;
        justify-content: center;
    }
    .qb-doc-modal-actions {
        flex-direction: column-reverse;
        align-items: stretch;
        gap: .6rem;
    }
    .qb-doc-btn-group {
        flex-direction: column;
        width: 100%;
        gap: .6rem;
    }
    .qb-doc-modal-actions button,
    .qb-doc-btn-group button {
        width: 100%;
        justify-content: center;
    }
}

/* Modal Document Upload - Dark Mode */
[data-theme="dark"] .qb-doc-dropzone {
    background: rgba(8, 145, 178, 0.05) !important;
    border-color: rgba(8, 145, 178, 0.35) !important;
}
[data-theme="dark"] .qb-doc-dropzone-title {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .qb-doc-template-box {
    background: rgba(8, 145, 178, 0.08) !important;
    border-color: rgba(8, 145, 178, 0.28) !important;
}
[data-theme="dark"] .qb-doc-template-title {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .qb-doc-template-sub {
    color: #94a3b8 !important;
}
[data-theme="dark"] .qb-doc-tips-box {
    background: rgba(245, 158, 11, 0.07) !important;
    border-color: rgba(245, 158, 11, 0.25) !important;
}
[data-theme="dark"] .qb-doc-tips-header {
    color: #fbbf24 !important;
}
[data-theme="dark"] .qb-doc-tips-list {
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-doc-tips-list code {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #fde68a !important;
}
[data-theme="dark"] .qb-doc-btn-cancel {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-doc-btn-cancel:hover {
    background: #1e293b !important;
    color: #f8fafc !important;
}
</style>


<script>
    let qbQuestionCounter = 0;
    let pendingDeleteIndex = null;
    let activeCardIndexForTool = null;
    let parsedDocQuestionsList = [];

    // State data per card: { cleanText, imageUrl, tableHtml }
    const qbCardsState = {};
    const qbActiveCell = {};
    const qbSelectedCells = {};

    // Helper: Extract image URL and table HTML from raw question text
    function extractMediaAndTableFromText(rawText) {
        if (!rawText) return { cleanText: '', imageUrl: null, tableHtml: null };

        let text = rawText;
        let imageUrl = null;
        let tableHtml = null;

        // 1. Extract Markdown image: ![alt](url)
        const mdImgMatch = text.match(/!\[(.*?)\]\((.*?)\)/);
        if (mdImgMatch) {
            imageUrl = mdImgMatch[2];
            text = text.replace(mdImgMatch[0], '');
        }

        // 2. Extract HTML img if not found: <img src="url"...>
        if (!imageUrl) {
            const htmlImgMatch = text.match(/<img[^>]+src=["']([^"']+)["'][^>]*>/i);
            if (htmlImgMatch) {
                imageUrl = htmlImgMatch[1];
                text = text.replace(/<div[^>]*>\s*<img[^>]+>\s*<\/div>/i, '').replace(htmlImgMatch[0], '');
            }
        }

        // 3. Extract HTML Table: <table ...> ... </table>
        const htmlTableMatch = text.match(/<div[^>]*class=["'][^"']*table-responsive[^"']*["'][^>]*>\s*<table[^>]*>[\s\S]*?<\/table>\s*<\/div>/i) || text.match(/<table[^>]*>[\s\S]*?<\/table>/i);
        if (htmlTableMatch) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlTableMatch[0], 'text/html');
            const table = doc.querySelector('table');
            if (table) {
                table.classList.add('qb-rich-table');
                tableHtml = table.outerHTML;
            }
            text = text.replace(htmlTableMatch[0], '');
        }

        // 4. Extract Markdown Table: | col1 | col2 |\n|---|---|\n| a | b |
        if (!tableHtml) {
            const lines = text.split('\n');
            const tableLines = [];
            const nonTableLines = [];

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                if (line.startsWith('|') && line.endsWith('|')) {
                    tableLines.push(line);
                } else {
                    nonTableLines.push(lines[i]);
                }
            }

            if (tableLines.length >= 2) {
                let mdTblHtml = '<table class="qb-rich-table"><thead><tr>';
                let isHeader = true;
                let inBody = false;

                tableLines.forEach((tLine, idx) => {
                    if (/^\|?\s*[-:\s|]+\s*\|?$/.test(tLine)) {
                        isHeader = false;
                        return;
                    }
                    const cells = tLine.replace(/^\||\|$/g, '').split('|').map(c => c.trim().replace(/&#124;/g, '|'));
                    if (isHeader && idx === 0) {
                        cells.forEach(c => { mdTblHtml += `<th>${escapeHtml(c)}</th>`; });
                        mdTblHtml += '</tr></thead><tbody>';
                        isHeader = false;
                        inBody = true;
                    } else {
                        if (!inBody) { mdTblHtml += '<tbody>'; inBody = true; }
                        mdTblHtml += '<tr>';
                        cells.forEach(c => { mdTblHtml += `<td>${escapeHtml(c)}</td>`; });
                        mdTblHtml += '</tr>';
                    }
                });

                if (inBody) mdTblHtml += '</tbody>';
                mdTblHtml += '</table>';
                tableHtml = mdTblHtml;
                text = nonTableLines.join('\n');
            }
        }

        let cleanText = text.replace(/\n{3,}/g, '\n\n').trim();
        return { cleanText, imageUrl, tableHtml };
    }

    // Helper: Clean Table HTML for database storage & student view
    function cleanTableHtmlForSaving(html) {
        if (!html) return '';
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const table = doc.querySelector('table');
        if (!table) return html;

        const isBorderless = table.classList.contains('table-borderless');
        const isStriped = table.classList.contains('table-striped');
        const isOutside = table.classList.contains('qb-border-outside');
        const isHorizontal = table.classList.contains('qb-border-horizontal');
        
        let baseClass = 'table table-sm align-middle mb-0';
        if (isBorderless) {
            baseClass += ' table-borderless';
        } else if (isOutside) {
            baseClass += ' qb-border-outside table-bordered';
        } else if (isHorizontal) {
            baseClass += ' qb-border-horizontal';
        } else {
            baseClass += ' table-bordered';
        }
        if (isStriped) {
            baseClass += ' table-striped';
        }

        table.className = baseClass;
        table.removeAttribute('id');

        table.querySelectorAll('*').forEach(el => {
            el.removeAttribute('contenteditable');
            el.removeAttribute('tabindex');
            el.classList.remove('qb-cell-selected', 'qb-cell-active', 'table-active');
            if (el.getAttribute('style') === '') el.removeAttribute('style');
        });

        return table.outerHTML;
    }

    // Helper: Compile clean text + image + table into complete Markdown / HTML
    function compileFinalQuestionText(cleanText, imageUrl, tableHtml) {
        let out = (cleanText || '').trim();
        if (imageUrl) {
            out += (out ? '\n\n' : '') + `![Gambar](${imageUrl})`;
        }
        if (tableHtml && tableHtml.trim()) {
            const cleanTbl = cleanTableHtmlForSaving(tableHtml);
            out += (out ? '\n\n' : '') + `<div class="table-responsive my-2.5">\n${cleanTbl}\n</div>`;
        }
        return out;
    }

    function renderQbQuestionCard(index, data = null) {
        let rawQText = data ? (data.question_text || '') : '';
        const qType = data ? (data.question_type || 'multiple_choice') : 'multiple_choice';
        const options = data ? (data.options || ['', '', '', '']) : ['', '', '', ''];
        const correctOpt = data ? (data.correct_option !== undefined ? data.correct_option : 0) : 0;
        const correctTf = data ? (data.correct_tf || 'Benar') : 'Benar';
        const pairs = data ? (data.pairs || [{premise: '', match: ''}, {premise: '', match: ''}]) : [{premise: '', match: ''}, {premise: '', match: ''}];

        // Extract clean text, image, and table
        const extracted = extractMediaAndTableFromText(rawQText);
        let cleanText = data && data.cleanText !== undefined ? data.cleanText : extracted.cleanText;
        let imageUrl = data && data.imageUrl !== undefined ? data.imageUrl : extracted.imageUrl;
        let tableHtml = data && data.tableHtml !== undefined ? data.tableHtml : extracted.tableHtml;

        // Save into card state
        qbCardsState[index] = {
            cleanText: cleanText,
            imageUrl: imageUrl,
            tableHtml: tableHtml
        };

        const card = document.createElement('div');
        card.className = 'qb-builder-item';
        card.id = `qbCard_${index}`;
        card.setAttribute('data-current-type', qType);

        let optionsHtml = '';
        for (let i = 0; i < 4; i++) {
            const letter = String.fromCharCode(65 + i);
            const val = options[i] || '';
            const isChecked = (parseInt(correctOpt) === i) ? 'checked' : '';
            const req = (i < 2) ? 'required' : '';
            optionsHtml += `
                <div class="qb-option-row mb-2">
                    <label class="qb-opt-radio-wrap m-0">
                        <input type="radio" name="questions[${index}][correct_option]" value="${i}" ${isChecked} class="form-check-input m-0">
                        <span>${letter}</span>
                    </label>
                    <input type="text" name="questions[${index}][options][${i}]" class="qb-option-input" placeholder="Tuliskan pilihan jawaban ${letter}..." value="${escapeHtml(val)}" ${req}>
                </div>
            `;
        }

        let pairsHtml = '';
        pairs.forEach((p, pIdx) => {
            pairsHtml += `
                <div class="row g-2 align-items-center mb-2 qb-pair-row">
                    <div class="col-12 col-md-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][premise]" class="form-control form-control-sm" placeholder="Premis / Pernyataan ${pIdx + 1}" value="${escapeHtml(p.premise || '')}" required>
                    </div>
                    <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                        <i class="ti ti-arrow-right"></i>
                    </div>
                    <div class="col-10 col-md-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][match]" class="form-control form-control-sm" placeholder="Pasangan Jawaban ${pIdx + 1}" value="${escapeHtml(p.match || '')}" required>
                    </div>
                    <div class="col-2 col-md-1 text-center">
                        <button type="button" class="md-icon-btn red" onclick="removeQbPairRow(this)" title="Hapus pasangan">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="md-badge blue qb-num-badge">Soal #${index + 1}</span>
                </div>
                <button type="button" class="md-icon-btn red delete-qb-btn" onclick="requestRemoveQbQuestionCard(${index})" title="Hapus soal ini">
                    <i class="ti ti-trash"></i>
                </button>
            </div>

            <!-- Format Selector -->
            <div class="mb-3">
                <div class="qb-format-pills">
                    <div class="qb-format-pill">
                        <input type="radio" name="questions[${index}][question_type]" id="qbtype_${index}_mc" value="multiple_choice" ${qType === 'multiple_choice' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'multiple_choice')">
                        <label for="qbtype_${index}_mc"><i class="ti ti-list-check"></i> Pilihan Ganda</label>
                    </div>
                    <div class="qb-format-pill">
                        <input type="radio" name="questions[${index}][question_type]" id="qbtype_${index}_tf" value="true_false" ${qType === 'true_false' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'true_false')">
                        <label for="qbtype_${index}_tf"><i class="ti ti-checkup-list"></i> Benar / Salah</label>
                    </div>
                    <div class="qb-format-pill">
                        <input type="radio" name="questions[${index}][question_type]" id="qbtype_${index}_match" value="matching" ${qType === 'matching' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'matching')">
                        <label for="qbtype_${index}_match"><i class="ti ti-arrows-left-right"></i> Menjodohkan</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-3 ${qType === 'matching' ? 'd-none' : ''}" id="qb_sec_qtext_${index}">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1.5 qb-question-header">
                    <label class="md-form-label mb-0">Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
                    <div class="qb-toolbar-wrap">
                        <button type="button" class="qb-toolbar-btn" onclick="openInsertImageModal(${index})" title="Lampirkan Gambar">
                            <i class="ti ti-photo text-primary"></i> <span>Gambar</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="openVisualTableModalOrAdd(${index})" title="Editor Tabel (Word / Excel)">
                            <i class="ti ti-table text-success"></i> <span>Tabel</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="openInsertSymbolModal(${index})" title="Simbol Matematika / Sains">
                            <i class="ti ti-math-function text-warning"></i> <span>Simbol</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="toggleLivePreview(${index})" title="Pratinjau Tampilan Lengkap">
                            <i class="ti ti-eye text-info"></i> <span>Pratinjau</span>
                        </button>
                    </div>
                </div>

                <!-- Bersih: Hanya teks pertanyaan murni tanpa kode gambar atau tabel -->
                <textarea name="questions[${index}][question_text]" id="qb_qtextarea_${index}" class="form-control mb-2" rows="3" placeholder="Tuliskan pertanyaan soal..." ${qType === 'matching' ? '' : 'required'} oninput="handleTextareaInput(${index})">${escapeHtml(cleanText)}</textarea>
                
                <!-- Container Lampiran Gambar (Hanya preview gambar bersih) -->
                <div id="qb_image_attachment_wrap_${index}" class="mb-2"></div>

                <!-- Container Editor Tabel Visual (Word / Excel style) -->
                <div id="qb_table_editor_wrap_${index}" class="mb-2"></div>

                <!-- Live Preview Area -->
                <div id="qb_preview_${index}" class="qb-live-preview-box p-3 mt-2 d-none"></div>
            </div>

            <!-- Section MC -->
            <div class="sec-mc ${qType === 'multiple_choice' ? '' : 'd-none'}" id="qb_sec_mc_${index}">
                <label class="md-form-label mb-1">Pilihan Jawaban (Klik radio pada opsi yang menjadi Kunci Jawaban):</label>
                <div class="options-container">
                    ${optionsHtml}
                </div>
            </div>

            <!-- Section TF -->
            <div class="sec-tf ${qType === 'true_false' ? '' : 'd-none'}" id="qb_sec_tf_${index}">
                <label class="md-form-label mb-2">Pilih Kunci Jawaban Pernyataan <span class="text-danger">*</span>:</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-true w-100" for="qb${index}_tf_true">
                            <input type="radio" class="form-check-input qb-tf-radio" name="questions[${index}][correct_tf]" id="qb${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-check"></i></span>
                                <span class="qb-tf-title">Benar (True)</span>
                            </div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-false w-100" for="qb${index}_tf_false">
                            <input type="radio" class="form-check-input qb-tf-radio" name="questions[${index}][correct_tf]" id="qb${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-x"></i></span>
                                <span class="qb-tf-title">Salah (False)</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section Matching -->
            <div class="sec-matching ${qType === 'matching' ? '' : 'd-none'}" id="qb_sec_matching_${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="md-form-label mb-0"><i class="ti ti-arrows-left-right me-1" style="color:#0891b2;"></i> Pasangan Soal &amp; Jawaban:</label>
                    <button type="button" class="md-btn-secondary" style="padding:.2rem .6rem;font-size:.72rem;" onclick="addQbPairToCard(${index})">
                        <i class="ti ti-plus"></i> Tambah Pasangan
                    </button>
                </div>
                <div class="pairs-container" id="qb_pairs_container_${index}">
                    ${pairsHtml}
                </div>
            </div>
        `;

        return card;
    }

    function handleTextareaInput(index) {
        const textarea = document.getElementById(`qb_qtextarea_${index}`);
        if (!textarea) return;
        if (!qbCardsState[index]) qbCardsState[index] = {};
        qbCardsState[index].cleanText = textarea.value;
        updateCardLivePreview(index);
    }

    // Render Image Attachment Box in Question Card (ONLY Image Preview, NO Code / Path)
    function renderImageAttachmentWidget(index) {
        const wrap = document.getElementById(`qb_image_attachment_wrap_${index}`);
        if (!wrap) return;

        const state = qbCardsState[index];
        const imageUrl = state ? state.imageUrl : null;

        if (!imageUrl) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-image-box shadow-xs" id="qb_img_box_${index}">
                <img src="${imageUrl}" alt="Pratinjau Gambar Soal">
                <div class="d-flex align-items-center gap-2 mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="font-size: .74rem; font-weight: 600;" onclick="openInsertImageModal(${index})">
                        <i class="ti ti-refresh"></i> Ganti Gambar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5" style="font-size: .74rem; font-weight: 600;" onclick="removeAttachedImage(${index})">
                        <i class="ti ti-trash"></i> Hapus Gambar
                    </button>
                </div>
            </div>
        `;
    }

    function attachImageToCard(index, url) {
        if (!qbCardsState[index]) qbCardsState[index] = {};
        qbCardsState[index].imageUrl = url;
        renderImageAttachmentWidget(index);
        updateCardLivePreview(index);
    }

    function removeAttachedImage(index) {
        if (!qbCardsState[index]) return;
        qbCardsState[index].imageUrl = null;
        renderImageAttachmentWidget(index);
        updateCardLivePreview(index);
    }

    // Helper: Build table coordinate matrix for span-aware selection & merge
    function getTableMatrix(table) {
        const matrix = [];
        const rows = table.querySelectorAll('tr');
        rows.forEach((tr, rIdx) => {
            if (!matrix[rIdx]) matrix[rIdx] = [];
            let cIdx = 0;
            Array.from(tr.children).forEach(cell => {
                while (matrix[rIdx][cIdx]) {
                    cIdx++;
                }
                const rowspan = parseInt(cell.getAttribute('rowspan') || 1);
                const colspan = parseInt(cell.getAttribute('colspan') || 1);
                for (let r = 0; r < rowspan; r++) {
                    for (let c = 0; c < colspan; c++) {
                        if (!matrix[rIdx + r]) matrix[rIdx + r] = [];
                        matrix[rIdx + r][cIdx + c] = cell;
                    }
                }
                cIdx += colspan;
            });
        });
        return matrix;
    }

    // Helper: Get bounding coordinates of a cell in table
    function getCellCoords(table, targetCell) {
        const matrix = getTableMatrix(table);
        let minR = Infinity, maxR = -Infinity, minC = Infinity, maxC = -Infinity;
        matrix.forEach((row, r) => {
            row.forEach((cell, c) => {
                if (cell === targetCell) {
                    minR = Math.min(minR, r);
                    maxR = Math.max(maxR, r);
                    minC = Math.min(minC, c);
                    maxC = Math.max(maxC, c);
                }
            });
        });
        return { minR, maxR, minC, maxC, matrix };
    }

    // Helper: Select rectangular range of cells from cell1 to cell2
    function selectCellRange(index, cell1, cell2) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const matrix = getTableMatrix(table);
        const pos1 = getCellCoords(table, cell1);
        const pos2 = getCellCoords(table, cell2);

        const rMin = Math.min(pos1.minR, pos2.minR);
        const rMax = Math.max(pos1.maxR, pos2.maxR);
        const cMin = Math.min(pos1.minC, pos2.minC);
        const cMax = Math.max(pos1.maxC, pos2.maxC);

        const cells = table.querySelectorAll('th, td');
        cells.forEach(c => c.classList.remove('qb-cell-selected'));
        if (!qbSelectedCells[index]) qbSelectedCells[index] = new Set();
        qbSelectedCells[index].clear();

        for (let r = rMin; r <= rMax; r++) {
            if (!matrix[r]) continue;
            for (let c = cMin; c <= cMax; c++) {
                const cell = matrix[r][c];
                if (cell && !qbSelectedCells[index].has(cell)) {
                    qbSelectedCells[index].add(cell);
                    cell.classList.add('qb-cell-selected');
                }
            }
        }
    }

    // Setup interactive events: Direct contenteditable typing & Mouse Drag Block Selection + Tab Navigation
    function setupRichTableEventListeners(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;

        const table = wrap.querySelector('table');
        if (!table) return;

        table.classList.add('qb-rich-table');
        const cells = table.querySelectorAll('th, td');

        if (!qbSelectedCells[index]) qbSelectedCells[index] = new Set();

        let isDragging = false;
        let dragStartCell = null;
        let touchStartCell = null;

        cells.forEach(cell => {
            cell.setAttribute('contenteditable', 'true');
            cell.setAttribute('tabindex', '0');

            // Tab key navigation like Microsoft Word
            cell.onkeydown = function(e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    const allCells = Array.from(table.querySelectorAll('th, td'));
                    const curIdx = allCells.indexOf(cell);
                    if (e.shiftKey) {
                        // Previous cell
                        if (curIdx > 0) {
                            allCells[curIdx - 1].focus();
                        }
                    } else {
                        // Next cell
                        if (curIdx < allCells.length - 1) {
                            allCells[curIdx + 1].focus();
                        } else {
                            // Last cell: automatically append a new row (Word style)!
                            insertTableRow(index, 'after');
                            const newCells = Array.from(table.querySelectorAll('th, td'));
                            if (newCells.length > allCells.length) {
                                newCells[allCells.length].focus();
                            }
                        }
                    }
                }
            };

            // Mouse down
            cell.onmousedown = function(e) {
                if (e.shiftKey && qbActiveCell[index] && wrap.contains(qbActiveCell[index])) {
                    e.preventDefault();
                    selectCellRange(index, qbActiveCell[index], cell);
                    updateFloatBar(index);
                    return;
                }

                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    if (qbSelectedCells[index].has(cell)) {
                        qbSelectedCells[index].delete(cell);
                        cell.classList.remove('qb-cell-selected');
                    } else {
                        qbSelectedCells[index].add(cell);
                        cell.classList.add('qb-cell-selected');
                    }
                    qbActiveCell[index] = cell;
                    updateFloatBar(index);
                    return;
                }

                isDragging = true;
                dragStartCell = cell;
                qbActiveCell[index] = cell;

                cells.forEach(c => c.classList.remove('qb-cell-selected'));
                qbSelectedCells[index].clear();
                qbSelectedCells[index].add(cell);
                cell.classList.add('qb-cell-selected');
                updateFloatBar(index);
            };

            // Mouse over while dragging
            cell.onmouseover = function(e) {
                if (isDragging && dragStartCell && dragStartCell !== cell) {
                    selectCellRange(index, dragStartCell, cell);
                    updateFloatBar(index);
                }
            };

            // Focus
            cell.onfocus = function() {
                if (qbSelectedCells[index].size <= 1) {
                    cells.forEach(c => c.classList.remove('qb-cell-selected'));
                    qbSelectedCells[index].clear();
                    qbSelectedCells[index].add(cell);
                    cell.classList.add('qb-cell-selected');
                    qbActiveCell[index] = cell;
                    updateFloatBar(index);
                }
            };

            // Input sync
            cell.oninput = function() {
                syncRichTableToState(index, true);
            };

            // Touch events for Mobile/Tablet
            cell.addEventListener('touchstart', function(e) {
                touchStartCell = cell;
                qbActiveCell[index] = cell;
                if (!e.shiftKey && qbSelectedCells[index].size <= 1) {
                    cells.forEach(c => c.classList.remove('qb-cell-selected'));
                    qbSelectedCells[index].clear();
                    qbSelectedCells[index].add(cell);
                    cell.classList.add('qb-cell-selected');
                }
                updateFloatBar(index);
            }, { passive: true });
        });

        // Keyboard shortcuts for table (Ctrl+Z: Undo, Ctrl+Y or Ctrl+Shift+Z: Redo)
        table.onkeydown = function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) {
                    redoTableAction(index);
                } else {
                    undoTableAction(index);
                }
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redoTableAction(index);
            }
        };

        // Touch drag across cells
        const onTouchMoveHandler = function(e) {
            if (!touchStartCell) return;
            const touch = e.touches[0];
            const targetEl = document.elementFromPoint(touch.clientX, touch.clientY);
            if (targetEl && (targetEl.tagName === 'TD' || targetEl.tagName === 'TH') && wrap.contains(targetEl)) {
                if (targetEl !== touchStartCell) {
                    selectCellRange(index, touchStartCell, targetEl);
                    updateFloatBar(index);
                }
            }
        };

        const onTouchEndHandler = function() {
            touchStartCell = null;
        };

        wrap.removeEventListener('touchmove', wrap._touchMoveHandler);
        wrap.removeEventListener('touchend', wrap._touchEndHandler);
        wrap._touchMoveHandler = onTouchMoveHandler;
        wrap._touchEndHandler = onTouchEndHandler;
        wrap.addEventListener('touchmove', onTouchMoveHandler, { passive: true });
        wrap.addEventListener('touchend', onTouchEndHandler, { passive: true });

        const onMouseUpHandler = function() {
            isDragging = false;
            dragStartCell = null;
        };
        document.removeEventListener('mouseup', wrap._mouseUpHandler);
        wrap._mouseUpHandler = onMouseUpHandler;
        document.addEventListener('mouseup', onMouseUpHandler);

        // Auto select first cell if none selected
        if (!qbActiveCell[index] || !wrap.contains(qbActiveCell[index])) {
            const firstCell = table.querySelector('th, td');
            if (firstCell) {
                firstCell.classList.add('qb-cell-selected');
                qbActiveCell[index] = firstCell;
                qbSelectedCells[index].add(firstCell);
            }
        }
        updateFloatBar(index);
    }

    // Render Clean & Modern Table Editor
    function renderVisualTableEditor(index) {
        const wrap = document.getElementById(`qb_table_editor_wrap_${index}`);
        if (!wrap) return;

        const state = qbCardsState[index];
        const tableHtml = state ? state.tableHtml : null;

        if (!tableHtml || !tableHtml.trim()) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-word-table-editor rounded-3 border overflow-hidden mb-2" id="qb_tbl_card_${index}">
                <!-- Clean Compact Header -->
                <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="ti ti-table me-1"></i> Tabel Soal</span>
                        <span class="text-muted small d-none d-sm-inline" style="font-size:0.75rem;">Klik pada sel untuk mengetik teks</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2" style="font-size:0.74rem;" onclick="openInsertTableModal(${index})">
                            <i class="ti ti-settings me-2"></i> Ubah Ukuran
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size:0.74rem;" onclick="removeRichTable(${index})">
                            <i class="ti ti-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>

                <!-- Modern Organized Toolbar -->
                <div class="qb-table-ribbon">
                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" id="qb_tbl_undo_btn_${index}" onclick="undoTableAction(${index})" title="Urungkan (Undo) [Ctrl+Z]" disabled>
                            <i class="ti ti-arrow-back-up text-primary"></i> Undo
                        </button>
                        <button type="button" class="qb-ribbon-btn" id="qb_tbl_redo_btn_${index}" onclick="redoTableAction(${index})" title="Ulangi (Redo) [Ctrl+Y]" disabled>
                            <i class="ti ti-arrow-forward-up text-primary"></i> Redo
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="insertTableRow(${index}, 'after')" title="Tambah Baris di Bawah">
                            <i class="ti ti-row-insert-bottom text-primary"></i> + Baris
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="insertTableCol(${index}, 'after')" title="Tambah Kolom di Kanan">
                            <i class="ti ti-column-insert-right text-success"></i> + Kolom
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="mergeSelectedCells(${index})" title="Gabungkan sel yang dipilih">
                            <i class="ti ti-arrows-merge text-indigo"></i> Gabung
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="unmergeCell(${index})" title="Pisahkan sel">
                            <i class="ti ti-arrows-split text-info"></i> Pisah
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="openBorderModal(${index})" title="Atur Garis Tabel">
                            <i class="ti ti-border-all text-secondary"></i> Garis
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="openColorModal(${index})" title="Warna Latar Sel">
                            <i class="ti ti-color-swatch text-warning"></i> Warna
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn qb-ribbon-btn-danger" onclick="deleteActiveTableRow(${index})" title="Hapus Baris Ini">
                            <i class="ti ti-trash-x"></i> - Baris
                        </button>
                        <button type="button" class="qb-ribbon-btn qb-ribbon-btn-danger" onclick="deleteActiveTableCol(${index})" title="Hapus Kolom Ini">
                            <i class="ti ti-trash-x"></i> - Kolom
                        </button>
                    </div>
                </div>

                <!-- Canvas -->
                <div class="qb-table-canvas-wrap" id="qb_table_canvas_wrap_${index}">
                    ${tableHtml}
                </div>

                <!-- Subtle Bottom Action -->
                <div class="qb-table-footer-actions py-2 bg-light text-center border-top">
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-1 shadow-none" style="font-size:0.78rem;" onclick="insertTableRow(${index}, 'after')">
                        <i class="ti ti-plus me-1"></i> Tambah Baris Baru
                    </button>
                </div>
            </div>
        `;

        setupRichTableEventListeners(index);
        initTableHistory(index, tableHtml);
    }

    // ── Shared modals (Border & Color) ───
    function buildSharedModals() {
        // 1. Border Modal
        if (!document.getElementById('qb_border_modal_overlay')) {
            const bmo = document.createElement('div');
            bmo.id = 'qb_border_modal_overlay';
            bmo.className = 'qb-border-modal-overlay';
            bmo.innerHTML = `
                <div class="qb-border-modal">
                    <div class="qb-border-modal-handle d-md-none"></div>
                    
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:.95rem;">Pilih Jenis Garis (Border)</h6>
                            <span class="small text-muted" id="qb_border_modal_sub" style="font-size:.74rem;">Garis akan diterapkan ke sel yang dipilih</span>
                        </div>
                        <button type="button" class="btn-close" onclick="closeBorderModal()" aria-label="Tutup"></button>
                    </div>

                    <!-- Per-cell borders -->
                    <div class="qb-color-modal-section">Garis Pada Sel Terpilih</div>
                    <div class="qb-border-grid" id="qb_border_cell_grid">
                        <button type="button" class="qb-border-swatch" data-btype="all">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="2" fill="none"/><line x1="1" y1="10" x2="19" y2="10" stroke="#334155" stroke-width="1.5"/><line x1="10" y1="1" x2="10" y2="19" stroke="#334155" stroke-width="1.5"/></svg>
                            Semua
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="outside">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="2.5" fill="none"/></svg>
                            Luar
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="none">
                            <svg viewBox="0 0 20 20"><rect x="2" y="2" width="16" height="16" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/><line x1="5" y1="5" x2="15" y2="15" stroke="#ef4444" stroke-width="1.5"/></svg>
                            Hapus
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="top">
                            <svg viewBox="0 0 20 20"><line x1="1" y1="2" x2="19" y2="2" stroke="#334155" stroke-width="2.5"/><rect x="3" y="5" width="14" height="12" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/></svg>
                            Atas
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="bottom">
                            <svg viewBox="0 0 20 20"><rect x="3" y="3" width="14" height="12" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/><line x1="1" y1="18" x2="19" y2="18" stroke="#334155" stroke-width="2.5"/></svg>
                            Bawah
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="thick">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="4" fill="none"/></svg>
                            Tebal
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="left">
                            <svg viewBox="0 0 20 20"><line x1="2" y1="1" x2="2" y2="19" stroke="#334155" stroke-width="2.5"/><rect x="5" y="3" width="12" height="14" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/></svg>
                            Kiri
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="right">
                            <svg viewBox="0 0 20 20"><rect x="3" y="3" width="12" height="14" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/><line x1="18" y1="1" x2="18" y2="19" stroke="#334155" stroke-width="2.5"/></svg>
                            Kanan
                        </button>
                        <button type="button" class="qb-border-swatch" data-btype="horizontal">
                            <svg viewBox="0 0 20 20"><line x1="1" y1="7" x2="19" y2="7" stroke="#334155" stroke-width="1.5"/><line x1="1" y1="13" x2="19" y2="13" stroke="#334155" stroke-width="1.5"/></svg>
                            H-Line
                        </button>
                    </div>

                    <!-- Table-wide presets -->
                    <div class="qb-color-modal-section">Garis Untuk Seluruh Tabel</div>
                    <div class="qb-border-grid" id="qb_border_table_grid">
                        <button type="button" class="qb-border-swatch" data-tbtype="all">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="2" fill="none"/><line x1="1" y1="10" x2="19" y2="10" stroke="#334155" stroke-width="1.5"/><line x1="10" y1="1" x2="10" y2="19" stroke="#334155" stroke-width="1.5"/></svg>
                            Semua
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="none">
                            <svg viewBox="0 0 20 20"><rect x="2" y="2" width="16" height="16" stroke="#cbd5e1" stroke-width="1" stroke-dasharray="3,3" fill="none"/><line x1="5" y1="5" x2="15" y2="15" stroke="#ef4444" stroke-width="1.5"/></svg>
                            Tanpa
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="outside">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="2.5" fill="none"/></svg>
                            Luar
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="horizontal">
                            <svg viewBox="0 0 20 20"><line x1="1" y1="7" x2="19" y2="7" stroke="#334155" stroke-width="1.5"/><line x1="1" y1="13" x2="19" y2="13" stroke="#334155" stroke-width="1.5"/></svg>
                            H-Line
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="striped">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="6" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1"/><rect x="1" y="8" width="18" height="5" fill="#ffffff" stroke="#94a3b8" stroke-width="1"/><rect x="1" y="14" width="18" height="5" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1"/></svg>
                            Zebra
                        </button>
                    </div>

                    <button type="button" class="qb-modal-close-btn" onclick="closeBorderModal()">Selesai</button>
                </div>
            `;
            document.body.appendChild(bmo);

            bmo.addEventListener('click', e => { if (e.target === bmo) closeBorderModal(); });
            bmo.querySelectorAll('[data-btype]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (window._qbBorderModalIndex !== undefined) {
                        setCellBorder(window._qbBorderModalIndex, btn.dataset.btype);
                        closeBorderModal();
                    }
                });
            });
            bmo.querySelectorAll('[data-tbtype]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (window._qbBorderModalIndex !== undefined) {
                        setTableBorderStyle(window._qbBorderModalIndex, btn.dataset.tbtype);
                        closeBorderModal();
                    }
                });
            });
        }

        // 3. Color Modal
        if (!document.getElementById('qb_color_modal_overlay')) {
            const cmo = document.createElement('div');
            cmo.id = 'qb_color_modal_overlay';
            cmo.className = 'qb-border-modal-overlay';
            cmo.innerHTML = `
                <div class="qb-border-modal">
                    <div class="qb-border-modal-handle d-md-none"></div>

                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:.95rem;">Warna Latar Sel (Shading)</h6>
                            <span class="small text-muted" style="font-size:.74rem;">Pilih warna untuk mempercantik sel yang dipilih</span>
                        </div>
                        <button type="button" class="btn-close" onclick="closeColorModal()" aria-label="Tutup"></button>
                    </div>

                    <div class="qb-color-modal-section">Pilihan Warna:</div>
                    <div class="qb-color-grid">
                        ${[
                            ['#ffffff','Putih','#94a3b8'],['#dbeafe','Biru Muda'],['#dcfce7','Hijau Muda'],
                            ['#fef9c3','Kuning'],['#fae8ff','Ungu Muda'],['#fee2e2','Merah Muda'],
                            ['#f1f5f9','Abu-abu'],['#1e40af','Biru Tua'],['#0f172a','Hitam'],
                            ['#fef3c7','Oranye Muda'],['#d1fae5','Hijau Tua'],['#ede9fe','Lavender'],
                        ].map(([c, n, bc]) => `<button type="button" class="qb-color-ring" style="background:${c};${bc?'border-color:'+bc+';':''}" title="${n}" data-color="${c}"></button>`).join('')}
                    </div>
                    <div class="qb-color-modal-section" style="padding-top:4px;">Hapus Warna</div>
                    <div style="padding: 0 16px 8px;">
                        <button type="button" class="qb-modal-close-btn" style="margin:0;width:100%;background:#fef2f2;border-color:#fca5a5;color:#b91c1c;" data-color="">Hapus Warna Latar</button>
                    </div>
                    <button type="button" class="qb-modal-close-btn" onclick="closeColorModal()">Selesai</button>
                </div>
            `;
            document.body.appendChild(cmo);

            cmo.addEventListener('click', e => { if (e.target === cmo) closeColorModal(); });
            cmo.querySelectorAll('[data-color]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (window._qbColorModalIndex !== undefined) {
                        setCellBackground(window._qbColorModalIndex, btn.dataset.color || '');
                        closeColorModal();
                    }
                });
            });
        }
    }

    // Clean Mini Table Preview
    function updateWordMiniPreview(cols, rows, hasHeader) {
        const previewEl = document.getElementById('qb_word_mini_preview');
        if (!previewEl) return;

        const displayCols = Math.min(cols, 6);
        const displayRows = Math.min(rows, 4);

        let tableHtml = '<table style="width:100%; border-collapse:collapse; font-size:0.68rem; border-radius:4px; overflow:hidden;">';
        if (hasHeader) {
            tableHtml += '<thead><tr>';
            for (let c = 0; c < displayCols; c++) {
                tableHtml += '<th class="word-mini-th">K' + (c+1) + '</th>';
            }
            tableHtml += '</tr></thead>';
        }
        tableHtml += '<tbody>';
        for (let r = 0; r < displayRows; r++) {
            tableHtml += '<tr>';
            for (let c = 0; c < displayCols; c++) {
                tableHtml += '<td class="word-mini-td">·</td>';
            }
            tableHtml += '</tr>';
        }
        tableHtml += '</tbody></table>';

        if (cols > 6 || rows > 4) {
            tableHtml += `<div class="text-muted text-center mt-1" style="font-size:0.67rem;">(+ ${cols > 6 ? (cols - 6) + ' kolom lagi ' : ''}${rows > 4 ? (rows - 4) + ' baris lagi' : ''})</div>`;
        }
        previewEl.innerHTML = tableHtml;
    }

    function applyWordGridDimensions(cols, rows) {
        const colsInput = document.getElementById('qb_table_cols_input');
        const rowsInput = document.getElementById('qb_table_rows_input');
        if (colsInput) colsInput.value = cols;
        if (rowsInput) rowsInput.value = rows;
        updateWordBadgeFromInputs();
    }

    function adjustWordInput(inputId, delta) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let val = parseInt(input.value || 1) + delta;
        const min = parseInt(input.min || 1);
        const max = parseInt(input.max || 25);
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;
        updateWordBadgeFromInputs();
    }

    function updateWordBadgeFromInputs() {
        const cols = parseInt(document.getElementById('qb_table_cols_input').value || 3);
        const rows = parseInt(document.getElementById('qb_table_rows_input').value || 3);
        const hasHeader = document.getElementById('qb_table_has_header') ? document.getElementById('qb_table_has_header').checked : true;
        updateWordMiniPreview(cols, rows, hasHeader);
    }

    function openInsertTableModal(index) {
        window._qbInsertTableModalIndex = index;
        applyWordGridDimensions(3, 3);
        const modalEl = document.getElementById('modalInsertTable');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function closeInsertTableModal() {
        const modalEl = document.getElementById('modalInsertTable');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    function confirmInsertWordTable() {
        const index = window._qbInsertTableModalIndex;
        if (index === undefined) return;

        const cols = Math.max(1, parseInt(document.getElementById('qb_table_cols_input').value || 3));
        const rows = Math.max(1, parseInt(document.getElementById('qb_table_rows_input').value || 3));
        const hasHeader = document.getElementById('qb_table_has_header').checked;

        let ths = '';
        if (hasHeader) {
            for (let c = 1; c <= cols; c++) {
                ths += `<th>Kolom ${c}</th>`;
            }
        }

        let trs = '';
        for (let r = 1; r <= rows; r++) {
            let tds = '';
            for (let c = 1; c <= cols; c++) {
                tds += `<td></td>`;
            }
            trs += `<tr>${tds}</tr>`;
        }

        const tableHtml = `
            <table class="qb-rich-table table-bordered">
                ${hasHeader ? `<thead><tr>${ths}</tr></thead>` : ''}
                <tbody>${trs}</tbody>
            </table>
        `;

        if (!qbCardsState[index]) qbCardsState[index] = {};
        qbCardsState[index].tableHtml = tableHtml;
        renderVisualTableEditor(index);
        updateCardLivePreview(index);
        closeInsertTableModal();

        // Focus the first cell immediately so user can type
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (wrap) {
            const firstCell = wrap.querySelector('th, td');
            if (firstCell) {
                firstCell.focus();
                firstCell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }

    function openBorderModal(index) {
        buildSharedModals();
        window._qbBorderModalIndex = index;
        const cnt = qbSelectedCells[index] ? qbSelectedCells[index].size : 0;
        const sub = document.getElementById('qb_border_modal_sub');
        if (sub) sub.textContent = cnt > 1 ? `Diterapkan ke ${cnt} sel terpilih` : 'Diterapkan ke sel aktif';
        document.getElementById('qb_border_modal_overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeBorderModal() {
        const el = document.getElementById('qb_border_modal_overlay');
        if (el) el.classList.remove('open');
        document.body.style.overflow = '';
    }
    function openColorModal(index) {
        buildSharedModals();
        window._qbColorModalIndex = index;
        document.getElementById('qb_color_modal_overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeColorModal() {
        const el = document.getElementById('qb_color_modal_overlay');
        if (el) el.classList.remove('open');
        document.body.style.overflow = '';
    }
    function clearCellSelection(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        wrap.querySelectorAll('.qb-cell-selected').forEach(c => c.classList.remove('qb-cell-selected'));
        if (qbSelectedCells[index]) qbSelectedCells[index].clear();
        updateFloatBar(index);
    }

    // ── Update floating bar visibility & badge ─────────────────────────────
    function updateFloatBar(index) {
        const bar = document.getElementById(`qb_float_bar_${index}`);
        const badge = document.getElementById(`qb_sel_badge_${index}`);
        const badgeText = document.getElementById(`qb_sel_badge_text_${index}`);
        const count = qbSelectedCells[index] ? qbSelectedCells[index].size : 0;

        // Show bar if we have an active cell
        if (qbActiveCell[index]) {
            if (bar) bar.classList.add('visible');
        } else {
            if (bar) bar.classList.remove('visible');
        }

        // Badge
        if (badge && badgeText) {
            if (count >= 2) {
                badge.classList.add('visible');
                badgeText.textContent = count + ' sel';
            } else {
                badge.classList.remove('visible');
            }
        }
    }

    // ── Table Undo & Redo History System ───
    const qbTableHistory = {}; // index -> { past: [], future: [], current: '' }
    const tableHistoryDebounceTimers = {};

    function initTableHistory(index, initialHtml) {
        if (!qbTableHistory[index]) {
            qbTableHistory[index] = {
                past: [],
                future: [],
                current: initialHtml || ''
            };
        } else {
            if (qbTableHistory[index].current && qbTableHistory[index].current !== initialHtml) {
                qbTableHistory[index].past.push(qbTableHistory[index].current);
                if (qbTableHistory[index].past.length > 30) qbTableHistory[index].past.shift();
                qbTableHistory[index].current = initialHtml;
                qbTableHistory[index].future = [];
            } else if (!qbTableHistory[index].current) {
                qbTableHistory[index].current = initialHtml || '';
            }
        }
        updateUndoRedoButtons(index);
    }

    function pushTableHistory(index, isDebounced = false) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const snapshot = table.outerHTML;

        if (!qbTableHistory[index]) {
            initTableHistory(index, snapshot);
            return;
        }

        const h = qbTableHistory[index];
        if (h.current === snapshot) return;

        if (isDebounced) {
            clearTimeout(tableHistoryDebounceTimers[index]);
            tableHistoryDebounceTimers[index] = setTimeout(() => {
                if (h.current !== snapshot) {
                    h.past.push(h.current);
                    if (h.past.length > 30) h.past.shift();
                    h.current = snapshot;
                    h.future = [];
                    updateUndoRedoButtons(index);
                }
            }, 400);
        } else {
            clearTimeout(tableHistoryDebounceTimers[index]);
            h.past.push(h.current);
            if (h.past.length > 30) h.past.shift();
            h.current = snapshot;
            h.future = [];
            updateUndoRedoButtons(index);
        }
    }

    function undoTableAction(index) {
        const h = qbTableHistory[index];
        if (!h || h.past.length === 0) return;

        clearTimeout(tableHistoryDebounceTimers[index]);
        h.future.push(h.current);
        const prevState = h.past.pop();
        h.current = prevState;

        restoreTableSnapshot(index, prevState);
        updateUndoRedoButtons(index);
    }

    function redoTableAction(index) {
        const h = qbTableHistory[index];
        if (!h || h.future.length === 0) return;

        clearTimeout(tableHistoryDebounceTimers[index]);
        h.past.push(h.current);
        const nextState = h.future.pop();
        h.current = nextState;

        restoreTableSnapshot(index, nextState);
        updateUndoRedoButtons(index);
    }

    function restoreTableSnapshot(index, html) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;

        wrap.innerHTML = html;
        qbActiveCell[index] = null;
        if (qbSelectedCells[index]) qbSelectedCells[index].clear();

        setupRichTableEventListeners(index);

        if (!qbCardsState[index]) qbCardsState[index] = {};
        qbCardsState[index].tableHtml = html;
        updateCardLivePreview(index);
    }

    function updateUndoRedoButtons(index) {
        const undoBtn = document.getElementById(`qb_tbl_undo_btn_${index}`);
        const redoBtn = document.getElementById(`qb_tbl_redo_btn_${index}`);
        const h = qbTableHistory[index];

        if (undoBtn) {
            undoBtn.disabled = !h || h.past.length === 0;
        }
        if (redoBtn) {
            redoBtn.disabled = !h || h.future.length === 0;
        }
    }

    function syncRichTableToState(index, isDebounced = false) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        if (!qbCardsState[index]) qbCardsState[index] = {};
        qbCardsState[index].tableHtml = table.outerHTML;
        updateCardLivePreview(index);

        pushTableHistory(index, isDebounced);
    }

    function openVisualTableModalOrAdd(index) {
        if (!qbCardsState[index]) qbCardsState[index] = {};
        if (!qbCardsState[index].tableHtml || !qbCardsState[index].tableHtml.trim()) {
            openInsertTableModal(index);
            return;
        }

        renderVisualTableEditor(index);
        updateCardLivePreview(index);

        const cardEl = document.getElementById(`qb_tbl_card_${index}`);
        if (cardEl) {
            cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // 1-Click Merge for blocked cells
    function mergeSelectedCells(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const selected = qbSelectedCells[index];
        if (!selected || selected.size < 2) {
            if (selected && selected.size === 1) {
                const cell = Array.from(selected)[0];
                const colspan = parseInt(cell.getAttribute('colspan') || 1);
                const rowspan = parseInt(cell.getAttribute('rowspan') || 1);
                if (colspan > 1 || rowspan > 1) {
                    unmergeCell(index);
                    return;
                }
            }
            alert('Silakan blok (tarik dengan mouse) minimal 2 sel yang berdampingan terlebih dahulu untuk digabungkan.');
            return;
        }

        const matrix = getTableMatrix(table);
        let minR = Infinity, maxR = -Infinity, minC = Infinity, maxC = -Infinity;

        selected.forEach(cell => {
            const coords = getCellCoords(table, cell);
            minR = Math.min(minR, coords.minR);
            maxR = Math.max(maxR, coords.maxR);
            minC = Math.min(minC, coords.minC);
            maxC = Math.max(maxC, coords.maxC);
        });

        const topLeftCell = matrix[minR][minC];
        if (!topLeftCell) return;

        // Collect combined text
        const textParts = [];
        selected.forEach(cell => {
            const t = cell.innerText.trim();
            if (t) textParts.push(t);
        });

        const totalColspan = (maxC - minC + 1);
        const totalRowspan = (maxR - minR + 1);

        if (totalColspan > 1) {
            topLeftCell.setAttribute('colspan', totalColspan);
        } else {
            topLeftCell.removeAttribute('colspan');
        }

        if (totalRowspan > 1) {
            topLeftCell.setAttribute('rowspan', totalRowspan);
        } else {
            topLeftCell.removeAttribute('rowspan');
        }

        topLeftCell.innerText = textParts.join(' ');

        // Remove other selected cells
        selected.forEach(cell => {
            if (cell !== topLeftCell && cell.parentElement) {
                cell.remove();
            }
        });

        // Reset selection to the merged cell
        selected.clear();
        selected.add(topLeftCell);
        qbActiveCell[index] = topLeftCell;

        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    function unmergeCell(index) {
        const activeCell = qbActiveCell[index];
        if (!activeCell) return;

        const colspan = parseInt(activeCell.getAttribute('colspan') || 1);
        const rowspan = parseInt(activeCell.getAttribute('rowspan') || 1);

        if (colspan <= 1 && rowspan <= 1) {
            alert('Sel ini adalah sel normal (belum di-merge).');
            return;
        }

        if (colspan > 1) {
            for (let i = 1; i < colspan; i++) {
                const td = document.createElement(activeCell.tagName.toLowerCase());
                td.innerHTML = '';
                activeCell.parentElement.insertBefore(td, activeCell.nextSibling);
            }
            activeCell.removeAttribute('colspan');
        }

        if (rowspan > 1) {
            let nextRow = activeCell.parentElement.nextElementSibling;
            for (let r = 1; r < rowspan && nextRow; r++) {
                const td = document.createElement('td');
                td.innerHTML = '';
                nextRow.appendChild(td);
                nextRow = nextRow.nextElementSibling;
            }
            activeCell.removeAttribute('rowspan');
        }

        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    // Apply border style to ONLY selected cells (Excel per-cell style)
    function setCellBorder(index, borderType) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0)
            ? Array.from(selected)
            : (qbActiveCell[index] ? [qbActiveCell[index]] : []);

        if (targetCells.length === 0) {
            return;
        }

        const brd = '1.5px solid #334155';
        const brdThick = '3px solid #0f172a';
        const brdNone = 'none';

        targetCells.forEach(c => {
            // Reset per-cell border first
            c.style.borderTop = '';
            c.style.borderBottom = '';
            c.style.borderLeft = '';
            c.style.borderRight = '';

            switch (borderType) {
                case 'all':
                    c.style.border = brd;
                    break;
                case 'none':
                    c.style.border = brdNone;
                    break;
                case 'outside':
                    c.style.border = brd;
                    break;
                case 'top':
                    c.style.borderTop = brd;
                    break;
                case 'bottom':
                    c.style.borderBottom = brd;
                    break;
                case 'left':
                    c.style.borderLeft = brd;
                    break;
                case 'right':
                    c.style.borderRight = brd;
                    break;
                case 'horizontal':
                    c.style.borderTop = brd;
                    c.style.borderBottom = brd;
                    c.style.borderLeft = brdNone;
                    c.style.borderRight = brdNone;
                    break;
                case 'thick':
                    c.style.border = brdThick;
                    break;
            }
        });

        syncRichTableToState(index);
    }

    // Apply border style to the ENTIRE TABLE (table-wide preset)
    function setTableBorderStyle(index, borderType) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        table.classList.remove('table-bordered', 'table-borderless', 'table-striped', 'qb-border-outside', 'qb-border-horizontal');
        table.style.border = '';
        const cells = table.querySelectorAll('th, td');

        cells.forEach(c => {
            c.style.border = '';
            c.style.borderTop = '';
            c.style.borderBottom = '';
            c.style.borderLeft = '';
            c.style.borderRight = '';
        });

        if (borderType === 'all') {
            table.classList.add('table-bordered');
            cells.forEach(c => c.style.border = '1px solid #cbd5e1');
        } else if (borderType === 'none') {
            table.classList.add('table-borderless');
            cells.forEach(c => c.style.border = 'none');
        } else if (borderType === 'outside') {
            table.classList.add('qb-border-outside');
            table.style.border = '2px solid #64748b';
            cells.forEach(c => c.style.border = 'none');
        } else if (borderType === 'horizontal') {
            table.classList.add('qb-border-horizontal');
            cells.forEach(c => {
                c.style.border = 'none';
                c.style.borderBottom = '1px solid #cbd5e1';
            });
        } else if (borderType === 'header-only') {
            const theadCells = table.querySelectorAll('thead th, thead td, tr:first-child th');
            cells.forEach(c => c.style.border = 'none');
            theadCells.forEach(c => c.style.borderBottom = '2px solid #334155');
        } else if (borderType === 'striped') {
            table.classList.add('table-striped', 'table-bordered');
            cells.forEach(c => c.style.border = '1px solid #cbd5e1');
        }

        syncRichTableToState(index);
    }

    function setCellBackground(index, color) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0) ? Array.from(selected) : (qbActiveCell[index] ? [qbActiveCell[index]] : []);

        targetCells.forEach(c => {
            c.style.backgroundColor = color || '';
        });
        syncRichTableToState(index);
    }

    function toggleCellBold(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0) ? Array.from(selected) : (qbActiveCell[index] ? [qbActiveCell[index]] : []);
        
        if (targetCells.length === 0) return;
        const isCurrentlyBold = targetCells[0].style.fontWeight === 'bold' || targetCells[0].style.fontWeight === '700';

        targetCells.forEach(c => {
            c.style.fontWeight = isCurrentlyBold ? 'normal' : 'bold';
        });
        syncRichTableToState(index);
    }

    function toggleCellHeader(index) {
        const activeCell = qbActiveCell[index];
        if (!activeCell) return;

        const isTh = activeCell.tagName === 'TH';
        const newCell = document.createElement(isTh ? 'td' : 'th');
        newCell.innerHTML = activeCell.innerHTML;
        if (activeCell.getAttribute('colspan')) newCell.setAttribute('colspan', activeCell.getAttribute('colspan'));
        if (activeCell.getAttribute('rowspan')) newCell.setAttribute('rowspan', activeCell.getAttribute('rowspan'));
        if (activeCell.getAttribute('style')) newCell.setAttribute('style', activeCell.getAttribute('style'));

        activeCell.parentElement.replaceChild(newCell, activeCell);
        qbActiveCell[index] = newCell;

        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    function alignActiveCell(index, align) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0) ? Array.from(selected) : (qbActiveCell[index] ? [qbActiveCell[index]] : []);

        targetCells.forEach(c => {
            c.style.textAlign = align;
        });
        syncRichTableToState(index);
    }

    function insertTableRow(index, position = 'after') {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = qbActiveCell[index] && wrap.contains(qbActiveCell[index]) ? qbActiveCell[index] : null;
        const targetTr = activeCell ? activeCell.parentElement : table.querySelector('tbody tr') || table.querySelector('tr');
        if (!targetTr) return;

        let colCount = 0;
        Array.from(targetTr.children).forEach(c => {
            colCount += parseInt(c.getAttribute('colspan') || 1);
        });
        if (colCount <= 0) colCount = 3;

        const newTr = document.createElement('tr');
        for (let i = 0; i < colCount; i++) {
            const td = document.createElement('td');
            td.innerHTML = '';
            newTr.appendChild(td);
        }

        if (position === 'before') {
            targetTr.parentElement.insertBefore(newTr, targetTr);
        } else {
            targetTr.parentElement.insertBefore(newTr, targetTr.nextSibling);
        }

        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    function insertTableCol(index, position = 'after') {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = qbActiveCell[index] && wrap.contains(qbActiveCell[index]) ? qbActiveCell[index] : null;
        let cIdx = activeCell ? Array.from(activeCell.parentElement.children).indexOf(activeCell) : -1;

        const rows = table.querySelectorAll('tr');
        rows.forEach(tr => {
            const isHead = tr.parentElement.tagName === 'THEAD' || tr.querySelector('th') !== null;
            const newCell = document.createElement(isHead ? 'th' : 'td');
            newCell.innerHTML = isHead ? 'Kolom Baru' : '';

            if (cIdx !== -1 && cIdx < tr.children.length) {
                const targetCell = tr.children[cIdx];
                if (position === 'before') {
                    tr.insertBefore(newCell, targetCell);
                } else {
                    tr.insertBefore(newCell, targetCell.nextSibling);
                }
            } else {
                tr.appendChild(newCell);
            }
        });

        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    function deleteActiveTableRow(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const allRows = table.querySelectorAll('tr');
        if (allRows.length <= 1) {
            alert('Tabel harus memiliki minimal 1 baris.');
            return;
        }

        const activeCell = qbActiveCell[index] && wrap.contains(qbActiveCell[index]) ? qbActiveCell[index] : null;
        const targetTr = activeCell ? activeCell.parentElement : allRows[allRows.length - 1];
        if (targetTr) {
            targetTr.remove();
            qbActiveCell[index] = null;
            setupRichTableEventListeners(index);
            syncRichTableToState(index);
        }
    }

    function deleteActiveTableCol(index) {
        const wrap = document.getElementById(`qb_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = qbActiveCell[index] && wrap.contains(qbActiveCell[index]) ? qbActiveCell[index] : null;
        let cIdx = activeCell ? Array.from(activeCell.parentElement.children).indexOf(activeCell) : 0;
        if (cIdx === -1) cIdx = 0;

        const rows = table.querySelectorAll('tr');
        rows.forEach(tr => {
            if (tr.children.length > 1 && cIdx < tr.children.length) {
                tr.children[cIdx].remove();
            }
        });

        qbActiveCell[index] = null;
        setupRichTableEventListeners(index);
        syncRichTableToState(index);
    }

    function removeRichTable(index) {
        if (!qbCardsState[index]) return;
        qbCardsState[index].tableHtml = null;
        qbActiveCell[index] = null;
        if (qbSelectedCells[index]) qbSelectedCells[index].clear();
        delete qbTableHistory[index];
        renderVisualTableEditor(index);
        updateCardLivePreview(index);
    }

    function handleQbFormatChange(index, newType) {
        const card = document.getElementById(`qbCard_${index}`);
        if (!card) return;

        const oldType = card.getAttribute('data-current-type') || 'multiple_choice';
        const qTextArea = card.querySelector('textarea[name*="[question_text]"]');
        const qText = qTextArea ? qTextArea.value.trim() : '';

        let hasContent = false;
        if (qText.length > 0 || (qbCardsState[index] && (qbCardsState[index].imageUrl || qbCardsState[index].tableHtml))) {
            hasContent = true;
        } else if (oldType === 'multiple_choice') {
            const mcOpts = card.querySelectorAll('.sec-mc input[type="text"]');
            mcOpts.forEach(i => { if (i.value.trim().length > 0) hasContent = true; });
        } else if (oldType === 'matching') {
            const matchOpts = card.querySelectorAll('.sec-matching input[type="text"]');
            matchOpts.forEach(i => { if (i.value.trim().length > 0) hasContent = true; });
        }

        if (hasContent) {
            const oldRadio = card.querySelector(`input[value="${oldType}"]`);
            if (oldRadio) oldRadio.checked = true;

            addNewQbQuestionCard({ question_type: newType });
            const newCards = document.querySelectorAll('.qb-builder-item');
            const lastCard = newCards[newCards.length - 1];
            if (lastCard) {
                lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            card.setAttribute('data-current-type', newType);
            switchQbItemType(index, newType);
        }
    }

    function switchQbItemType(index, type) {
        const card = document.getElementById(`qbCard_${index}`);
        const secMc = document.getElementById(`qb_sec_mc_${index}`);
        const secTf = document.getElementById(`qb_sec_tf_${index}`);
        const secMatch = document.getElementById(`qb_sec_matching_${index}`);
        const qTextDiv = document.getElementById(`qb_sec_qtext_${index}`);
        const qTextArea = card ? card.querySelector('textarea[name*="[question_text]"]') : null;
        const mcInputs = secMc.querySelectorAll('input[type="text"]');
        const matchInputs = secMatch.querySelectorAll('input[type="text"]');

        if (type === 'true_false') {
            if (qTextDiv) qTextDiv.classList.remove('d-none');
            if (qTextArea) qTextArea.setAttribute('required', 'required');
            secMc.classList.add('d-none');
            secTf.classList.remove('d-none');
            secMatch.classList.add('d-none');
            mcInputs.forEach(i => i.removeAttribute('required'));
            matchInputs.forEach(i => i.removeAttribute('required'));
        } else if (type === 'matching') {
            if (qTextDiv) qTextDiv.classList.add('d-none');
            if (qTextArea) qTextArea.removeAttribute('required');
            secMc.classList.add('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.remove('d-none');
            mcInputs.forEach(i => i.removeAttribute('required'));
            matchInputs.forEach(i => i.setAttribute('required', 'required'));
        } else {
            if (qTextDiv) qTextDiv.classList.remove('d-none');
            if (qTextArea) qTextArea.setAttribute('required', 'required');
            secMc.classList.remove('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.add('d-none');
            mcInputs.forEach((i, idx) => {
                if (idx < 2) i.setAttribute('required', 'required');
                else i.removeAttribute('required');
            });
            matchInputs.forEach(i => i.removeAttribute('required'));
        }
    }

    function addNewQbQuestionCard(data = null) {
        const container = document.getElementById('qbQuestionsContainer');
        const card = renderQbQuestionCard(qbQuestionCounter, data);
        container.appendChild(card);
        const cardIndex = qbQuestionCounter;
        qbQuestionCounter++;

        renderImageAttachmentWidget(cardIndex);
        renderVisualTableEditor(cardIndex);

        updateQbQuestionNumbers();
        setTimeout(() => updateCardLivePreview(cardIndex), 30);
    }

    function requestRemoveQbQuestionCard(index) {
        const allCards = document.querySelectorAll('.qb-builder-item');
        if (allCards.length <= 1) {
            showQbModalAlert('Pemberitahuan', 'Minimal harus ada 1 butir soal dalam form.');
            return;
        }
        pendingDeleteIndex = index;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteCard'));
        modal.show();
    }

    function formatQuestionPreviewHtml(raw) {
        if (!raw || !raw.trim()) return '<em class="text-muted">Belum ada teks pertanyaan...</em>';

        // 1. Render Markdown images: ![alt](url)
        let html = raw.replace(/!\[(.*?)\]\((.*?)\)/g, (match, alt, url) => {
            return `<div class="text-center my-2 q-media-wrap"><img src="${url}" alt="${alt || 'Gambar Soal'}" class="img-fluid rounded border shadow-xs" style="max-height: 280px; object-fit: contain;"></div>`;
        });

        // 2. Render Markdown tables: | col1 | col2 |
        const lines = html.split('\n');
        let out = [];
        let tblBuf = [];

        const flushTbl = () => {
            if (tblBuf.length === 0) return;
            let tblHtml = '<div class="table-responsive my-2"><table class="table table-bordered table-sm table-striped align-middle mb-0">';
            let isHdr = true;
            let inBdy = false;

            tblBuf.forEach((rowStr, idx) => {
                let trm = rowStr.trim();
                if (/^\|?\s*[-:\s|]+\s*\|?$/.test(trm)) return;
                let cells = trm.replace(/^\||\|$/g, '').split('|');

                if (isHdr && idx === 0) {
                    tblHtml += '<thead class="table-light"><tr>';
                    cells.forEach(c => { tblHtml += `<th class="text-center fw-bold text-nowrap">${c.trim()}</th>`; });
                    tblHtml += '</tr></thead><tbody>';
                    isHdr = false;
                    inBdy = true;
                } else {
                    if (!inBdy) { tblHtml += '<tbody>'; inBdy = true; }
                    tblHtml += '<tr>';
                    cells.forEach(c => { tblHtml += `<td class="text-center">${c.trim()}</td>`; });
                    tblHtml += '</tr>';
                }
            });
            if (inBdy) tblHtml += '</tbody>';
            tblHtml += '</table></div>';
            out.push(tblHtml);
            tblBuf = [];
        };

        lines.forEach(l => {
            let trm = l.trim();
            if (trm.startsWith('|') && trm.endsWith('|')) {
                tblBuf.push(trm);
            } else {
                flushTbl();
                out.push(l);
            }
        });
        flushTbl();

        return out.map(l => {
            let trm = l.trim();
            if (trm.startsWith('<div') || trm.startsWith('<table') || trm.startsWith('<p')) return l;
            return trm ? `<div class="mb-1">${trm}</div>` : '<div class="my-1"></div>';
        }).join('\n');
    }

    function updateCardLivePreview(index) {
        const previewBox = document.getElementById(`qb_preview_${index}`);
        const textarea = document.getElementById(`qb_qtextarea_${index}`);
        if (!previewBox || !textarea) return;

        const state = qbCardsState[index] || {};
        const cleanText = textarea.value.trim();
        const compiled = compileFinalQuestionText(cleanText, state.imageUrl, state.tableHtml);

        if (!compiled) {
            previewBox.classList.add('d-none');
            return;
        }

        const hasMedia = !!state.imageUrl;
        const hasTable = !!(state.tableHtml && state.tableHtml.trim());

        let badgeLabel = 'Pratinjau Langsung';
        if (hasMedia && hasTable) {
            badgeLabel = 'Gambar & Tabel Aktif';
        } else if (hasMedia) {
            badgeLabel = 'Gambar Aktif';
        } else if (hasTable) {
            badgeLabel = 'Tabel Aktif';
        }

        previewBox.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2 pb-1.5 border-bottom" style="border-color: rgba(2, 132, 199, 0.25) !important;">
                <span class="d-flex align-items-center gap-1.5 fw-bold" style="font-size:0.76rem; color:#0369a1;">
                    <i class="ti ti-eye"></i> Pratinjau Tampilan Soal:
                </span>
                <span class="badge ${hasMedia || hasTable ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'}" style="font-size:0.68rem; font-weight:700;">
                    ${badgeLabel}
                </span>
            </div>
            <div class="qb-preview-content">
                ${formatQuestionPreviewHtml(compiled)}
            </div>
        `;
    }

    function toggleLivePreview(index) {
        const previewBox = document.getElementById(`qb_preview_${index}`);
        if (!previewBox) return;

        if (previewBox.classList.contains('d-none')) {
            updateCardLivePreview(index);
            previewBox.classList.remove('d-none');
        } else {
            previewBox.classList.add('d-none');
        }
    }

    function openInsertImageModal(index) {
        activeCardIndexForTool = index;
        document.getElementById('modalImgFileInput').value = '';
        document.getElementById('modalImgUrlInput').value = '';
        document.getElementById('modalImgPreviewWrap').classList.add('d-none');
        const modal = new bootstrap.Modal(document.getElementById('modalInsertImage'));
        modal.show();
    }

    function openInsertSymbolModal(index) {
        activeCardIndexForTool = index;
        const modal = new bootstrap.Modal(document.getElementById('modalInsertSymbol'));
        modal.show();
    }

    function insertSymbolToActiveCard(symbol) {
        if (activeCardIndexForTool === null) return;
        const textarea = document.getElementById(`qb_qtextarea_${activeCardIndexForTool}`);
        if (!textarea) return;

        insertTextAtCursor(textarea, symbol);
        handleTextareaInput(activeCardIndexForTool);
    }

    function insertTextAtCursor(textarea, textToInsert) {
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + textToInsert + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + textToInsert.length;
        textarea.focus();
    }

    function confirmInsertImage() {
        if (activeCardIndexForTool === null) return;

        const fileInput = document.getElementById('modalImgFileInput');
        const urlInput = document.getElementById('modalImgUrlInput');

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            const progressEl = document.getElementById('modalImgUploadProgress');
            const submitBtn = document.getElementById('btnConfirmInsertImage');
            progressEl.classList.remove('d-none');
            submitBtn.disabled = true;

            fetch('{{ route('admin.upload-editor-image') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                progressEl.classList.add('d-none');
                submitBtn.disabled = false;
                if (data.location || data.url) {
                    const imgUrl = data.location || data.url;
                    attachImageToCard(activeCardIndexForTool, imgUrl);

                    const modalEl = document.getElementById('modalInsertImage');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else {
                    showQbModalAlert('Gagal Upload', data.error || 'Terjadi kesalahan saat mengunggah gambar.');
                }
            })
            .catch(err => {
                progressEl.classList.add('d-none');
                submitBtn.disabled = false;
                showQbModalAlert('Gagal Upload', 'Terjadi kesalahan jaringan saat mengunggah gambar.');
            });
        } else if (urlInput.value.trim()) {
            const imgUrl = urlInput.value.trim();
            attachImageToCard(activeCardIndexForTool, imgUrl);

            const modalEl = document.getElementById('modalInsertImage');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else {
            showQbModalAlert('Perhatian', 'Pilih file gambar atau masukkan URL gambar terlebih dahulu.');
        }
    }

    // Document Upload & Parser Logic
    document.addEventListener('DOMContentLoaded', function() {
        addNewQbQuestionCard();

        // On submit: compile final question_text from cleanText + image + rich table for all cards
        const form = document.getElementById('batchQbForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const cards = document.querySelectorAll('.qb-builder-item');
                cards.forEach((card, idx) => {
                    const textarea = card.querySelector('textarea[name*="[question_text]"]');
                    if (textarea) {
                        syncRichTableToState(idx);
                        const state = qbCardsState[idx] || {};
                        const compiled = compileFinalQuestionText(textarea.value, state.imageUrl, state.tableHtml);
                        textarea.value = compiled;
                    }
                });
            });
        }

        document.getElementById('btnConfirmDeleteCard')?.addEventListener('click', function() {
            if (pendingDeleteIndex !== null) {
                const card = document.getElementById(`qbCard_${pendingDeleteIndex}`);
                if (card) {
                    card.remove();
                    delete qbCardsState[pendingDeleteIndex];
                    updateQbQuestionNumbers();
                }
                pendingDeleteIndex = null;
            }
            const modalEl = document.getElementById('modalConfirmDeleteCard');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });

        const docFileInput = document.getElementById('docFileInput');
        if (docFileInput) {
            docFileInput.addEventListener('change', handleDocumentFileSelect);
        }

        const dropZone = document.getElementById('docDropZone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#0284c7';
                    dropZone.style.background = '#f0f9ff';
                });
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = '#0891b2';
                    dropZone.style.background = 'var(--tblr-body-bg,#f8fafc)';
                });
            });
            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files && files.length > 0) {
                    docFileInput.files = files;
                    handleDocumentFileSelect();
                }
            });
        }
    });

    function handleDocumentFileSelect() {
        const fileInput = document.getElementById('docFileInput');
        if (!fileInput.files || !fileInput.files[0]) return;

        const file = fileInput.files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        if (['zip', 'rar'].includes(ext)) {
            showQbModalAlert('Format Tidak Didukung', 'File arsip (.zip dan .rar) tidak didukung untuk naskah soal. Silakan gunakan dokumen Word (.docx), PDF (.pdf), atau Teks (.txt).');
            fileInput.value = '';
            return;
        }

        const nameEl = document.getElementById('selectedDocName');
        nameEl.innerText = `File terpilih: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        nameEl.classList.remove('d-none');

        const loadingEl = document.getElementById('docParseLoading');
        loadingEl.classList.remove('d-none');

        parseDocumentViaBackend(file);
    }

    function parseDocumentViaBackend(file) {
        const loadingEl = document.getElementById('docParseLoading');
        const loadingTextEl = loadingEl ? (loadingEl.querySelector('.qb-loading-text') || loadingEl) : null;
        if (loadingEl) loadingEl.classList.remove('d-none');
        if (loadingTextEl) loadingTextEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah dokumen naskah soal...';

        // Jika file berukuran > 1MB, gunakan Chunked Uploader agar bebas timeout
        if (file.size > 1024 * 1024 && typeof ChunkedUploader !== 'undefined') {
            const uploader = new ChunkedUploader({
                targetFolder: 'question-banks-temp',
                uploadUrl: '{{ route('admin.upload.chunk') }}',
                cancelUrl: '{{ route('admin.upload.chunk.cancel') }}',
                onProgress: function(progress) {
                    if (loadingTextEl) {
                        loadingTextEl.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah dokumen: ${progress.percent}%...`;
                    }
                },
                onSuccess: function(data) {
                    if (loadingTextEl) {
                        loadingTextEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menganalisis butir soal dari dokumen...';
                    }

                    const formData = new FormData();
                    formData.append('document_chunk_path', data.file_path);
                    formData.append('original_filename', data.original_filename);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('admin.question-banks.parse-document') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(parseData => {
                        if (loadingEl) loadingEl.classList.add('d-none');
                        if (parseData.success && parseData.questions && parseData.questions.length > 0) {
                            renderExtractedDocQuestions(parseData.questions, parseData.raw_text);
                        } else {
                            showQbModalAlert('Gagal Parsing', parseData.message || 'Tidak ada butir soal yang berhasil dideteksi.');
                        }
                    })
                    .catch(err => {
                        if (loadingEl) loadingEl.classList.add('d-none');
                        showQbModalAlert('Gagal Membaca Dokumen', 'Terjadi kesalahan saat memproses dokumen: ' + err.message);
                    });
                },
                onError: function(err) {
                    if (loadingEl) loadingEl.classList.add('d-none');
                    showQbModalAlert('Gagal Unggah Dokumen', err.message);
                }
            });

            uploader.upload(file);
        } else {
            // Direct upload fallback untuk file kecil
            const formData = new FormData();
            formData.append('document_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('admin.question-banks.parse-document') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (loadingEl) loadingEl.classList.add('d-none');
                if (data.success && data.questions && data.questions.length > 0) {
                    renderExtractedDocQuestions(data.questions, data.raw_text);
                } else {
                    showQbModalAlert('Gagal Parsing', data.message || 'Tidak ada butir soal yang berhasil dideteksi.');
                }
            })
            .catch(err => {
                if (loadingEl) loadingEl.classList.add('d-none');
                showQbModalAlert('Gagal Membaca Dokumen', 'Terjadi kesalahan jaringan atau format dokumen tidak dapat dibaca.');
            });
        }
    }

    function parseRawQuestionTextToObjects(text) {
        const lines = text.replace(/\r\n/g, '\n').split('\n');
        const blocks = [];
        let currentBlock = [];
        let hasStartedFirstQuestion = false;

        const hasNumberedQuestions = lines.some(l => /^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)/i.test(l.trim()));

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;

            const isNewQuestion = /^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)/i.test(line);

            if (isNewQuestion) {
                if (currentBlock.length > 0) {
                    blocks.push(currentBlock);
                }
                currentBlock = [line];
                hasStartedFirstQuestion = true;
            } else if (hasStartedFirstQuestion || !hasNumberedQuestions) {
                currentBlock.push(line);
            }
        }
        if (currentBlock.length > 0) {
            blocks.push(currentBlock);
        }

        const parsedQuestions = [];

        blocks.forEach(block => {
            let qText = '';
            let qType = 'multiple_choice';
            let options = [];
            let correctOpt = 0;
            let correctTf = 'Benar';
            let pairs = [];

            let inOptions = false;
            let hasOptions = false;

            block.forEach(l => {
                const tr = l.trim();
                if (/^([A-Da-d])[\.\)]\s*(.*)$/.test(tr)) {
                    hasOptions = true;
                }
            });

            block.forEach(rawLine => {
                const line = rawLine.trim();
                if (!line) return;

                const isTableOrImageLine = line.startsWith('|') || line.startsWith('!') || line.startsWith('<img') || line.startsWith('<table');

                const optMatch = line.match(/^([A-Da-d])[\.\)]\s*(.*)$/);
                const keyMatch = line.match(/^(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*(benar|salah|true|false|[A-Ea-e]\b)/i);
                const pairMatch = line.match(/^(.{1,60}?)\s*(?:=|->|—)\s*(.{1,60})$/);

                if (keyMatch) {
                    const ans = keyMatch[1].toLowerCase();
                    if (ans === 'benar' || ans === 'true') {
                        qType = 'true_false';
                        correctTf = 'Benar';
                    } else if (ans === 'salah' || ans === 'false') {
                        qType = 'true_false';
                        correctTf = 'Salah';
                    } else {
                        const letterCode = ans.toUpperCase().charCodeAt(0) - 65;
                        if (letterCode >= 0 && letterCode <= 4) {
                            correctOpt = letterCode;
                        }
                    }
                } else if (!isTableOrImageLine && optMatch) {
                    inOptions = true;
                    options.push(optMatch[2].trim());
                } else if (!inOptions && !hasOptions && !isTableOrImageLine && pairMatch && !/^(\d+[\.\)]|\bsoal|\bperhatikan|\bberapakah|\btentukan|\bjika|\bhitung|\bapakah)/i.test(line) && !line.endsWith('?')) {
                    pairs.push({
                        premise: pairMatch[1].trim(),
                        match: pairMatch[2].trim()
                    });
                } else if (!inOptions) {
                    const clean = line.replace(/^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)\s*/i, '');
                    qText += (qText ? '\n' : '') + clean;
                }
            });

            if (pairs.length >= 2 && !hasOptions) {
                qType = 'matching';
            } else if (qType === 'true_false') {
                // true_false
            } else if (options.length >= 2 || hasOptions) {
                qType = 'multiple_choice';
            } else if (qText.toLowerCase().includes('benar') || qText.toLowerCase().includes('salah')) {
                qType = 'true_false';
            }

            while (options.length < 4) {
                options.push('');
            }

            if (!qText && pairs.length === 0 && !options[0]) {
                return;
            }

            parsedQuestions.push({
                question_text: qText || 'Pertanyaan Kuis',
                question_type: qType,
                options: options.slice(0, 4),
                correct_option: correctOpt,
                correct_tf: correctTf,
                pairs: pairs.length >= 2 ? pairs : [{premise: '', match: ''}, {premise: '', match: ''}]
            });
        });

        return parsedQuestions;
    }

    function renderExtractedDocQuestions(questions, rawText) {
        parsedDocQuestionsList = questions;

        const countBadge = document.getElementById('docExtractedCountBadge');
        countBadge.innerHTML = `<i class="ti ti-circle-check text-success me-1"></i> ${questions.length} Butir Soal Terdeteksi`;

        const rawTextarea = document.getElementById('docRawTextarea');
        rawTextarea.value = rawText || '';

        const previewList = document.getElementById('docQuestionsPreviewList');
        previewList.innerHTML = '';

        questions.forEach((q, idx) => {
            const item = document.createElement('div');
            item.className = 'p-3 rounded-2 border bg-white';
            item.style.fontSize = '.8rem';
            
            let typeBadge = '<span class="badge bg-primary-subtle text-primary">Pilihan Ganda</span>';
            if (q.question_type === 'true_false') {
                typeBadge = '<span class="badge bg-warning-subtle text-warning">Benar / Salah</span>';
            } else if (q.question_type === 'matching') {
                typeBadge = '<span class="badge bg-info-subtle text-info">Menjodohkan</span>';
            }

            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="fw-bold text-dark">Butir Soal #${idx + 1}</span>
                    ${typeBadge}
                </div>
                <div class="mb-2 text-dark">
                    ${formatQuestionPreviewHtml(q.question_text)}
                </div>
            `;
            previewList.appendChild(item);
        });

        document.getElementById('docExtractedSection').classList.remove('d-none');
        document.getElementById('btnApplyExtractedDoc').disabled = (questions.length === 0);
        const directSaveBtn = document.getElementById('btnDirectSaveDoc');
        if (directSaveBtn) {
            directSaveBtn.disabled = (questions.length === 0);
        }
    }

    function toggleDocRawText() {
        const rawEl = document.getElementById('docRawTextarea');
        rawEl.classList.toggle('d-none');
    }

    function directSaveExtractedDoc() {
        const fileInput = document.getElementById('docFileInput');
        if (!fileInput.files || !fileInput.files[0]) {
            showQbModalAlert('Perhatian', 'Pilih file dokumen Word atau PDF terlebih dahulu.');
            return;
        }

        const hiddenInput = document.getElementById('hiddenDirectDocFileInput');
        hiddenInput.files = fileInput.files;
        document.getElementById('directDocUploadForm').submit();
    }

    function applyExtractedDocQuestions() {
        if (!parsedDocQuestionsList || parsedDocQuestionsList.length === 0) return;

        const container = document.getElementById('qbQuestionsContainer');
        container.innerHTML = '';
        qbQuestionCounter = 0;

        parsedDocQuestionsList.forEach(q => {
            addNewQbQuestionCard(q);
        });

        const modalEl = document.getElementById('modalDocUpload');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        document.getElementById('batchQbForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function addQbPairToCard(qIndex) {
        const container = document.getElementById(`qb_pairs_container_${qIndex}`);
        const currentCount = container.querySelectorAll('.qb-pair-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 qb-pair-row';
        row.innerHTML = `
            <div class="col-12 col-md-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][premise]" class="form-control form-control-sm" placeholder="Premis / Pernyataan ${currentCount + 1}" required>
            </div>
            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                <i class="ti ti-arrow-right"></i>
            </div>
            <div class="col-10 col-md-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][match]" class="form-control form-control-sm" placeholder="Pasangan Jawaban ${currentCount + 1}" required>
            </div>
            <div class="col-2 col-md-1 text-center">
                <button type="button" class="md-icon-btn red" onclick="removeQbPairRow(this)" title="Hapus pasangan">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    function removeQbPairRow(button) {
        const container = button.closest('.pairs-container');
        const rows = container.querySelectorAll('.qb-pair-row');
        if (rows.length <= 2) {
            showQbModalAlert('Pemberitahuan', 'Soal menjodohkan membutuhkan minimal 2 pasangan.');
            return;
        }
        button.closest('.qb-pair-row').remove();
    }

    function showQbModalAlert(title, message) {
        document.getElementById('modalAlertTitle').innerText = title;
        document.getElementById('modalAlertMsg').innerText = message;
        const modal = new bootstrap.Modal(document.getElementById('modalAlertQb'));
        modal.show();
    }

    function updateQbQuestionNumbers() {
        const cards = document.querySelectorAll('.qb-builder-item');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.qb-num-badge');
            if (badge) badge.innerText = `Soal #${idx + 1}`;
            
            const delBtn = card.querySelector('.delete-qb-btn');
            if (delBtn) {
                delBtn.style.display = (cards.length > 1) ? 'inline-flex' : 'none';
            }
        });

        const submitText = document.getElementById('submitQbBtnText');
        if (submitText) {
            submitText.innerText = `Simpan Semua Soal (${cards.length} Butir)`;
        }

        const counterBadge = document.getElementById('qbQuestionCounterBadge');
        if (counterBadge) {
            counterBadge.innerHTML = `<i class="ti ti-list-check"></i> ${cards.length} Butir Soal`;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function loadSampleQbText() {
        const sampleText = `1. Perhatikan gambar diagram segitiga siku-siku di bawah ini!
![Diagram Segitiga](/storage/questions/sample_segitiga.png)
Berdasarkan teorema Pythagoras, berapakah panjang sisi miring (c) jika diketahui panjang sisi a = 6 cm dan b = 8 cm?
A. 10 cm
B. 12 cm
C. 14 cm
D. 16 cm
Kunci: A

2. Perhatikan tabel data penjualan buku di toko literasi selama 5 hari kerja berikut:
| Hari | Jumlah Terjual (Eksemplar) |
|---|---|
| Senin | 15 |
| Selasa | 20 |
| Rabu | 25 |
| Kamis | 30 |
| Jumat | 10 |
Berapakah total penjualan buku dari hari Senin sampai Rabu?
A. 50
B. 60
C. 70
D. 80
Kunci: B

3. Apa ibukota negara Indonesia saat ini?
A. Jakarta
B. Bandung
C. Surabaya
D. Medan
Kunci: A

4. Bumi mengelilingi matahari dalam kurun waktu satu tahun penuh (revolusi bumi).
Kunci: Benar

5. Logam merkuri (raksa) berwujud padat pada suhu ruangan kamar normal (25 derajat celcius).
Kunci: Salah

6. Jodohkan bahasa pemrograman berikut dengan ekstensi filenya:
PHP = .php
Python = .py
JavaScript = .js
CSS = .css`;

        const textarea = document.getElementById('quickPasteQbTextarea');
        if (textarea) {
            textarea.value = sampleText;
        }
    }

    function parseAndInsertQbQuestions() {
        const text = document.getElementById('quickPasteQbTextarea').value.trim();
        if (!text) {
            showQbModalAlert('Perhatian', 'Silakan tempelkan naskah soal terlebih dahulu.');
            return;
        }

        const questions = parseRawQuestionTextToObjects(text);
        if (questions.length === 0) {
            showQbModalAlert('Format Salah', 'Format soal tidak terdeteksi. Pastikan setiap butir soal diawali angka nomor soal (misal: 1. Pertanyaan).');
            return;
        }

        const container = document.getElementById('qbQuestionsContainer');
        container.innerHTML = '';
        qbQuestionCounter = 0;

        questions.forEach(q => {
            addNewQbQuestionCard(q);
        });

        const modalEl = document.getElementById('modalQuickPaste');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        document.getElementById('batchQbForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>
<script src="{{ asset('js/chunked-uploader.js') }}"></script>
@endsection
