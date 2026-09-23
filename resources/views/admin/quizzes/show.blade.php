@extends('layouts.be.master')

@section('header_title', 'Kelola Soal Kuis — ' . $quiz->title)

@section('content')
@include('admin._partials.master-data-styles')

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
                <i class="ti ti-list-check"></i>
            </div>
            <div>
                <h5 class="md-title">Kelola Soal Kuis: {{ $quiz->title }}</h5>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.students', $quiz) }}" class="md-btn-secondary">
                <i class="ti ti-users"></i> <span>Hasil &amp; Status Siswa</span>
            </a>
            <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary">
                <i class="ti ti-arrow-left"></i> <span>Kembali</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert md-alert success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert md-alert danger alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quiz Overview Card -->
    <div class="card md-card qz-overview-card mb-3">
        <div class="card-body p-3 p-md-3.5">
            <div class="row g-3 align-items-center">
                <div class="col-6 col-md-3">
                    <span class="qz-meta-label">Mata Pelajaran</span>
                    <div class="qz-meta-val text-primary">{{ $quiz->subject->name ?? '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="qz-meta-label">Kelas Target</span>
                    <div class="qz-meta-val qz-text-main">{{ $quiz->schoolClass->name ?? $quiz->classroom->name ?? '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="qz-meta-label">Durasi Pengerjaan</span>
                    <div class="qz-meta-val qz-text-main"><i class="ti ti-clock me-1 text-warning"></i>{{ $quiz->formatted_duration }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="qz-meta-label">Poin per Soal</span>
                    <div class="qz-meta-val text-success">{{ $quiz->points_per_question }} Poin</div>
                </div>
            </div>
        </div>
    </div>

<div class="row g-4">
    <!-- Left Column: Current Quiz Questions List -->
    <div class="col-lg-7">
        <div class="card md-card qz-card">
            <div class="qz-card-header d-flex justify-content-between align-items-center">
                <h5 class="qz-card-title">
                    <i class="ti ti-list-details text-primary me-1.5"></i>
                    Daftar Soal Kuis <span class="qz-count-badge">({{ $quiz->questions->count() === $quiz->total_questions_count ? $quiz->total_questions_count . ' Soal' : $quiz->questions->count() . ' Nomor • ' . $quiz->total_questions_count . ' Butir Soal' }})</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse($quiz->questions as $index => $q)
                    <div class="qz-question-item {{ $loop->last ? 'last' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <h6 class="qz-question-num mb-0">Soal {{ $index + 1 }}</h6>
                                @if($q->isMatching())
                                    <span class="md-badge qb-badge-matching">
                                        <i class="ti ti-arrows-left-right"></i> Menjodohkan
                                    </span>
                                @elseif($q->isTrueFalse())
                                    <span class="md-badge qb-badge-tf">
                                        <i class="ti ti-checkup-list"></i> Benar / Salah
                                    </span>
                                @else
                                    <span class="md-badge qb-badge-mc">
                                        <i class="ti ti-list-check"></i> Pilihan Ganda
                                    </span>
                                @endif

                                @if(str_contains($q->question_text, '![') || str_contains($q->question_text, '<img'))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                                        <i class="ti ti-photo me-0.5"></i> Gambar
                                    </span>
                                @endif
                                @if(str_contains($q->question_text, '<table') || str_contains($q->question_text, '|'))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.68rem; font-weight: 700;">
                                        <i class="ti ti-table me-0.5"></i> Tabel
                                    </span>
                                @endif
                            </div>
                            @php
                                $previewQuestionText = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($q->question_text))), 60);
                            @endphp
                            <button type="button" class="qz-btn-delete-q" 
                                    data-action="{{ route('admin.quizzes.destroy-question', [$quiz, $q]) }}"
                                    data-text="{{ $previewQuestionText }}"
                                    onclick="openDeleteQuizQuestionModal(this.dataset.action, this.dataset.text)" 
                                    title="Hapus Soal">
                                <i class="ti ti-trash"></i> <span>Hapus</span>
                            </button>
                        </div>
                        <div class="qz-question-text mb-2">{!! $q->formatted_question_text !!}</div>

                        @if($q->isMatching())
                            <!-- Display Matching Pairs -->
                            <div class="table-responsive mt-2">
                                <table class="table table-sm qz-match-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Premis / Pertanyaan</th>
                                            <th style="width: 50%;">Pasangan Jawaban Benar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($q->options as $opt)
                                            <tr>
                                                <td class="premise-cell">{!! $opt->option_text !!}</td>
                                                <td class="match-cell"><i class="ti ti-arrow-right me-1"></i> {!! $opt->match_text !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($q->isTrueFalse())
                            <!-- Display True/False -->
                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                @foreach($q->options as $opt)
                                    <div class="qz-tf-badge {{ $opt->is_correct ? 'qz-tf-correct' : 'qz-tf-inactive' }}">
                                        <i class="ti {{ $opt->is_correct ? 'ti-circle-check-filled text-success' : 'ti-circle text-muted' }}"></i>
                                        <span>{!! $opt->option_text !!}</span>
                                        @if($opt->is_correct)
                                            <span class="qz-tf-key-tag">Kunci</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Display Multiple Choice Options -->
                            <div class="qz-mc-options">
                                @foreach($q->options as $optIndex => $opt)
                                    <div class="qz-mc-opt {{ $opt->is_correct ? 'is-correct' : '' }}">
                                        <span class="qz-mc-icon">
                                            @if($opt->is_correct)
                                                <i class="ti ti-circle-check-filled text-success"></i>
                                            @else
                                                <i class="ti ti-circle text-muted"></i>
                                            @endif
                                        </span>
                                        <span class="opt-letter">{{ chr(65 + $optIndex) }}.</span>
                                        <span class="opt-text">{!! $opt->option_text !!}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-help-off fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada soal dalam kuis ini. Silakan impor dari Bank Soal atau tambah soal manual.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Import from Bank Soal or Manual Creation -->
    <div class="col-lg-5">
        <!-- Import from Question Bank Card -->
        <div class="card md-card qz-card mb-4">
            <div class="qz-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="qz-card-title mb-0">
                    <i class="ti ti-file-import text-primary me-1.5"></i> Impor Soal dari Bank Soal
                </h5>
                @if(!$questionBanks->isEmpty())
                <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" id="btnToggleSelectAllQb" onclick="toggleSelectAllQuestionBanks()" style="font-size: 0.76rem; font-weight: 700; padding: 0.28rem 0.65rem; border-radius: 6px;">
                    <i class="ti ti-checks" id="iconSelectAllQb"></i> <span id="textSelectAllQb">Pilih Semua</span>
                </button>
                @endif
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.quizzes.import-questions', $quiz) }}" method="POST" id="formImportQuestionBank">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <span class="small text-muted" style="font-size:0.75rem;">Pilih butir soal yang ingin dimasukkan:</span>
                        <span class="badge bg-primary-subtle text-primary fw-bold" id="badgeSelectedQbCount" style="font-size:0.72rem;">0 dipilih</span>
                    </div>
                    <div class="qz-qb-scroll mb-3">
                        @forelse($questionBanks as $qb)
                            <label class="qz-qb-item d-flex align-items-center gap-2 mb-1.5" for="qb_{{ $qb->id }}">
                                <input class="form-check-input mt-0 flex-shrink-0 qb-import-checkbox" type="checkbox" name="question_bank_ids[]" value="{{ $qb->id }}" id="qb_{{ $qb->id }}" onchange="updateSelectedQbCounter()">
                                @if($qb->isMatching())
                                    <span class="md-badge qb-badge-matching qz-mini-badge">Menjodohkan</span>
                                @elseif($qb->isTrueFalse())
                                    <span class="md-badge qb-badge-tf qz-mini-badge">Benar/Salah</span>
                                @else
                                    <span class="md-badge qb-badge-mc qz-mini-badge">Pilgan</span>
                                @endif
                                <span class="qz-qb-item-text">{{ Str::limit($qb->question_text, 65) }}</span>
                            </label>
                        @empty
                            <div class="small text-muted py-2">Bank Soal masih kosong.</div>
                        @endforelse
                    </div>
                    <button type="submit" id="btnSubmitImportQb" class="md-btn-primary w-100 justify-content-center" {{ $questionBanks->isEmpty() ? 'disabled' : '' }}>
                        <i class="ti ti-file-import"></i> <span id="btnSubmitImportQbText">Impor Soal Terpilih</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Multi-Question Builder Card -->
        <div class="card md-card qz-card">
            <div class="qz-card-header d-flex justify-content-between align-items-center">
                <h5 class="qz-card-title">
                    <i class="ti ti-cards text-success me-1.5"></i> Buat Soal Kuis
                </h5>
                <button type="button" class="qz-btn-quickpaste" data-bs-toggle="modal" data-bs-target="#modalQuickPaste">
                    <i class="ti ti-bolt text-warning"></i> <span>Input Cepat (Teks)</span>
                </button>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.quizzes.store-question', $quiz) }}" method="POST" id="batchQuestionForm">
                    @csrf

                    <!-- Container for question cards -->
                    <div id="questionsContainer" class="d-flex flex-column gap-3 mb-3">
                        <!-- Questions will be rendered dynamically by JavaScript -->
                    </div>

                    <!-- Action Bar: Add Question & Submit All -->
                    <div class="d-flex flex-column gap-2 pt-2 border-top">
                        <button type="button" class="md-btn-secondary w-100 justify-content-center py-2" onclick="addNewQuestionCard()">
                            <i class="ti ti-plus"></i> <span>Tambah Butir Soal Lagi</span>
                        </button>
                        <button type="submit" id="submitAllBtn" class="md-btn-submit w-100 justify-content-center py-2">
                            <i class="ti ti-device-floppy"></i> <span id="submitBtnText">Simpan Semua Soal (1 Butir)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Quick Paste (Input Cepat Soal dari Teks) -->
<div class="modal fade" id="modalQuickPaste" tabindex="-1" aria-labelledby="modalQuickPasteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content md-modal-content text-start">
            <div class="modal-header qz-modal-header px-4 py-3">
                <h5 class="modal-title fw-bold qz-modal-title d-flex align-items-center gap-2" id="modalQuickPasteLabel">
                    <i class="ti ti-bolt text-warning fs-4"></i> Input Cepat Soal dari Teks (Salin-Tempel Massal)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="qz-modal-subtitle small mb-2">
                    Salin naskah soal Anda dari dokumen (Word / PDF / Notepad), lalu tempelkan di bawah ini. Sistem cerdas akan mendeteksi soal <strong>Pilihan Ganda</strong>, <strong>Benar/Salah</strong>, dan <strong>Menjodohkan</strong> sekaligus!
                </p>
                
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold small text-primary mb-0">Area Teks Naskah Soal:</label>
                    <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 small" onclick="loadSampleText()">
                        <i class="ti ti-file-text me-1"></i> Muat Contoh Format
                    </button>
                </div>
                <textarea id="quickPasteTextarea" class="form-control font-monospace qz-form-control" rows="10" placeholder="1. Apa ibukota negara Indonesia?&#10;A. Jakarta&#10;B. Bandung&#10;C. Surabaya&#10;D. Medan&#10;Kunci: A&#10;&#10;2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.&#10;Kunci: Benar&#10;&#10;3. Jodohkan bahasa pemrograman dengan ekstensinya:&#10;PHP = .php&#10;Python = .py&#10;JavaScript = .js"></textarea>

                <div class="qz-guide-alert mt-3 mb-0 p-3 rounded-3">
                    <div class="fw-bold qz-guide-title mb-1"><i class="ti ti-info-circle text-primary me-1"></i> Panduan Format:</div>
                    <ul class="mb-0 ps-3">
                        <li><strong>Pilihan Ganda:</strong> Diawali nomor, baris opsi diawali <code>A.</code>, <code>B.</code>, dst, dan baris kunci <code>Kunci: A</code> atau <code>Jawaban: A</code>.</li>
                        <li><strong>Benar / Salah:</strong> Pertanyaan/pernyataan diikuti baris <code>Kunci: Benar</code> atau <code>Kunci: Salah</code>.</li>
                        <li><strong>Menjodohkan:</strong> Pernyataan diikuti baris pasangan dengan tanda <code>=</code> (contoh: <code>Indonesia = Jakarta</code>).</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer qz-modal-footer px-4 py-2.5">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="md-btn-primary" onclick="parseAndInsertQuestions()">
                    <i class="ti ti-sparkles"></i> <span>Konversi &amp; Masukkan ke Form</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Saved Quiz Question Confirmation -->
<div class="modal fade" id="modalDeleteQuizQuestion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-body">
                <div class="md-modal-icon red">
                    <i class="ti ti-trash"></i>
                </div>
                <h5 class="md-modal-title">Hapus Soal Kuis Ini?</h5>
                <p class="md-modal-text" id="deleteModalQuestionText">Soal akan dihapus dari kuis ini.</p>
                <form id="deleteQuizQuestionForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="md-btn-danger">
                            <i class="ti ti-trash"></i> <span>Ya, Hapus</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Builder Card Confirmation -->
<div class="modal fade" id="modalConfirmDeleteQuizCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-body">
                <div class="md-modal-icon red">
                    <i class="ti ti-trash"></i>
                </div>
                <h5 class="md-modal-title">Hapus Kartu Soal Ini?</h5>
                <p class="md-modal-text">Kartu butir soal ini akan dihapus dari form pembuatan.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmDeleteQuizCard" class="md-btn-danger">
                        <i class="ti ti-trash"></i> <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alert Info -->
<div class="modal fade" id="modalAlertQuiz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-body">
                <div class="md-modal-icon amber">
                    <i class="ti ti-alert-triangle"></i>
                </div>
                <h5 class="md-modal-title" id="modalAlertQuizTitle">Pemberitahuan</h5>
                <p class="md-modal-text" id="modalAlertQuizMsg">Minimal harus ada 1 butir soal dalam form.</p>
                <button type="button" class="md-btn-primary" data-bs-dismiss="modal">Mengerti</button>
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
                    <div class="md-page-icon" style="background:rgba(59,130,246,.1);color:#2563eb;width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;">
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

            <div class="md-modal-actions d-flex gap-2 justify-content-end">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="md-btn-primary" id="btnConfirmInsertImage" onclick="confirmInsertImage()">
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
                    <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;">
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
                <button type="button" class="md-btn-primary w-100 justify-content-center" data-bs-dismiss="modal">Selesai</button>
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

<style>
/* ==========================================================================
   QUIZ SHOW & QUESTION BUILDER DEDICATED STYLES (Light & Dark Compatible)
   ========================================================================== */

/* Overview Card */
.qz-overview-card {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
}
.qz-meta-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #64748b;
}
.qz-meta-val {
    font-size: 0.98rem;
    font-weight: 700;
    margin-top: .15rem;
}
.qz-text-main {
    color: #0f172a;
}

/* Base Quiz Card */
.qz-card {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px;
}
.qz-card-header {
    background: transparent;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    padding: .85rem 1.15rem;
}
.qz-card-title {
    font-size: .95rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
}
.qz-count-badge {
    font-size: .82rem;
    font-weight: 600;
    color: #64748b;
    margin-left: .35rem;
}

/* Question List Items */
.qz-question-item {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--tblr-border-color, #f1f5f9);
    transition: background .15s ease;
}
.qz-question-item.last {
    border-bottom: none;
}
.qz-question-num {
    font-size: .9rem;
    font-weight: 800;
    color: #0f172a;
}
.qz-question-text {
    font-size: .88rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.55;
}
.qz-question-text img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.qz-question-text .q-media-wrap {
    margin: 0.6rem 0;
}
.qz-question-text .table-responsive {
    margin: 0.6rem 0;
    border-radius: 8px;
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    overflow-x: auto;
    background: #ffffff;
}
.qz-question-text table {
    margin-bottom: 0 !important;
    font-size: 0.84rem;
    font-weight: normal;
    width: 100%;
}
.qz-question-text table th {
    background-color: #f8fafc;
    font-weight: 600;
    color: #334155;
    padding: 0.45rem 0.65rem;
    border-color: var(--tblr-border-color, #e2e8f0);
}
.qz-question-text table td {
    padding: 0.45rem 0.65rem;
    border-color: var(--tblr-border-color, #e2e8f0);
}

[data-bs-theme="dark"] .qz-question-text,
[data-theme="dark"] .qz-question-text,
body.theme-dark .qz-question-text {
    color: #f1f5f9;
}
[data-bs-theme="dark"] .qz-question-text .table-responsive,
[data-theme="dark"] .qz-question-text .table-responsive,
body.theme-dark .qz-question-text .table-responsive {
    background: #1e293b;
    border-color: rgba(255,255,255,0.12);
}
[data-bs-theme="dark"] .qz-question-text table,
[data-theme="dark"] .qz-question-text table,
body.theme-dark .qz-question-text table {
    color: #e2e8f0;
}
[data-bs-theme="dark"] .qz-question-text table th,
[data-theme="dark"] .qz-question-text table th,
body.theme-dark .qz-question-text table th {
    background-color: #0f172a;
    color: #f8fafc;
    border-color: rgba(255,255,255,0.12);
}
[data-bs-theme="dark"] .qz-question-text table td,
[data-theme="dark"] .qz-question-text table td,
body.theme-dark .qz-question-text table td {
    border-color: rgba(255,255,255,0.1);
}
[data-bs-theme="dark"] .qz-question-text img,
[data-theme="dark"] .qz-question-text img,
body.theme-dark .qz-question-text img {
    border-color: rgba(255,255,255,0.15) !important;
    background: #0f172a;
}
.qz-btn-delete-q {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .2rem .55rem;
    border-radius: 6px;
    font-size: .74rem;
    font-weight: 600;
    color: #ef4444;
    background: #fef2f2;
    border: 1px solid #fecaca;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none;
}
.qz-btn-delete-q:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
}

/* Matching Table */
.qz-match-table {
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
}
.qz-match-table thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: .74rem;
    padding: .5rem .75rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
}
.qz-match-table tbody td {
    padding: .5rem .75rem;
    border-bottom: 1px solid var(--tblr-border-color, #f1f5f9);
    font-size: .8rem;
    vertical-align: middle;
}
.qz-match-table tbody tr:last-child td {
    border-bottom: none;
}
.premise-cell {
    font-weight: 600;
    color: #0f172a;
}
.match-cell {
    font-weight: 700;
    color: #059669;
}

/* True / False Display Badges */
.qz-tf-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .35rem .75rem;
    border-radius: 7px;
    font-size: .78rem;
    font-weight: 600;
}
.qz-tf-correct {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}
.qz-tf-inactive {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.qz-tf-key-tag {
    background: #059669;
    color: #ffffff;
    font-size: .65rem;
    font-weight: 700;
    padding: .08rem .35rem;
    border-radius: 4px;
    margin-left: .25rem;
}

/* Multiple Choice Display */
.qz-mc-options {
    display: flex;
    flex-direction: column;
    gap: .35rem;
    padding-left: .75rem;
    border-left: 3px solid #3b82f6;
    margin-top: .4rem;
}
.qz-mc-opt {
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .83rem;
    line-height: 1.45;
    color: #475569;
}
.qz-mc-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    font-size: .95rem;
    flex-shrink: 0;
}
.qz-mc-opt.is-correct {
    color: #059669;
    font-weight: 700;
}
.opt-letter {
    font-weight: 700;
    min-width: 18px;
    color: #334155;
}
.opt-text {
    flex: 1;
}

/* Bank Soal List in Right Column */
.qz-qb-scroll {
    max-height: 250px;
    overflow-y: auto;
    padding-right: 4px;
}
.qz-qb-item {
    padding: .4rem .6rem;
    border-radius: 7px;
    cursor: pointer;
    transition: background .15s ease;
}
.qz-qb-item:hover {
    background: #f1f5f9;
}
.qz-qb-item .form-check-input {
    width: 16px;
    height: 16px;
    cursor: pointer;
}
.qz-mini-badge {
    font-size: .65rem !important;
    padding: .15rem .45rem !important;
    flex-shrink: 0;
}
.qz-qb-item-text {
    font-weight: 500;
    color: #1e293b;
}

/* Input Cepat Button */
.qz-btn-quickpaste {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .75rem;
    border-radius: 7px;
    font-size: .76rem;
    font-weight: 700;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none;
}
.qz-btn-quickpaste:hover {
    background: #fef3c7;
    border-color: #f59e0b;
    color: #92400e;
}

/* Question Builder Dynamic Cards */
.question-builder-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1rem;
    transition: all .15s ease;
}
.qz-builder-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .6rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
.qz-form-control {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a !important;
}
.qz-form-control:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
}
.qz-input-group-text {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-right: none !important;
}

/* Format Pills in Builder */
.qz-format-pills {
    display: flex;
    gap: .4rem;
    width: 100%;
}
.qz-format-pill {
    flex: 1;
    text-align: center;
    padding: .35rem .5rem;
    border-radius: 7px;
    font-size: .76rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}
.btn-check:checked + .qz-pill-mc {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #1d4ed8 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
}
.btn-check:checked + .qz-pill-tf {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #b45309 !important;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
}
.btn-check:checked + .qz-pill-match {
    background: #0891b2 !important;
    color: #ffffff !important;
    border-color: #0e7490 !important;
    box-shadow: 0 2px 6px rgba(8, 145, 178, 0.25);
}

/* True/False Selector in Builder */
.qz-builder-tf-wrap {
    display: flex;
    gap: .6rem;
    width: 100%;
}
.qz-builder-tf-box {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .5rem .75rem;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    margin-bottom: 0;
}
.qz-tf-true .qz-tf-text {
    color: #059669;
    font-weight: 700;
    font-size: .8rem;
}
.qz-tf-false .qz-tf-text {
    color: #dc2626;
    font-weight: 700;
    font-size: .8rem;
}

/* Quick Paste Modal Styles */
.qz-modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.qz-modal-title {
    color: #0f172a;
    font-size: 1.05rem;
}
.qz-modal-subtitle {
    color: #64748b;
}
.qz-guide-alert {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: .78rem;
}
.qz-guide-title {
    color: #0f172a;
}
.qz-modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

/* ==========================================================================
   DARK MODE OVERRIDES ([data-theme="dark"])
   ========================================================================== */
[data-theme="dark"] .qz-overview-card {
    background: var(--tblr-card-bg, #182234);
    border-color: var(--tblr-border-color, rgba(255, 255, 255, 0.1));
}
[data-theme="dark"] .qz-meta-label {
    color: #94a3b8;
}
[data-theme="dark"] .qz-text-main {
    color: #f8fafc;
}

[data-theme="dark"] .qz-card {
    background: var(--tblr-card-bg, #182234);
    border-color: var(--tblr-border-color, rgba(255, 255, 255, 0.1));
}
[data-theme="dark"] .qz-card-header {
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .qz-card-title {
    color: #f8fafc;
}
[data-theme="dark"] .qz-count-badge {
    color: #94a3b8;
}

[data-theme="dark"] .qz-question-item {
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .qz-question-num {
    color: #f8fafc;
}
[data-theme="dark"] .qz-question-text {
    color: #f1f5f9;
}
[data-theme="dark"] .qz-btn-delete-q {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.35);
    color: #f87171;
}
[data-theme="dark"] .qz-btn-delete-q:hover {
    background: rgba(239, 68, 68, 0.25);
    color: #fca5a5;
    border-color: #ef4444;
}

/* Matching Table Dark Mode */
[data-theme="dark"] .qz-match-table {
    border-color: rgba(255, 255, 255, 0.12);
}
[data-theme="dark"] .qz-match-table thead th {
    background: rgba(15, 23, 42, 0.6);
    color: #cbd5e1;
    border-bottom-color: rgba(255, 255, 255, 0.12);
}
[data-theme="dark"] .qz-match-table tbody td {
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .premise-cell {
    color: #e2e8f0;
}
[data-theme="dark"] .match-cell {
    color: #34d399;
}

/* True / False Badges Dark Mode */
[data-theme="dark"] .qz-tf-correct {
    background: rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.35);
    color: #34d399;
}
[data-theme="dark"] .qz-tf-inactive {
    background: rgba(15, 23, 42, 0.45);
    border-color: rgba(255, 255, 255, 0.1);
    color: #94a3b8;
}

/* MC Options Dark Mode */
[data-theme="dark"] .qz-mc-opt {
    color: #94a3b8;
}
[data-theme="dark"] .qz-mc-opt.is-correct {
    color: #34d399;
}

/* Right Column Bank Soal Dark Mode */
[data-theme="dark"] .qz-qb-item:hover {
    background: rgba(255, 255, 255, 0.06);
}
[data-theme="dark"] .qz-qb-item-text {
    color: #e2e8f0;
}

/* Quick Paste Button Dark Mode */
[data-theme="dark"] .qz-btn-quickpaste {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.35);
    color: #fbbf24;
}
[data-theme="dark"] .qz-btn-quickpaste:hover {
    background: rgba(245, 158, 11, 0.25);
    border-color: #fbbf24;
    color: #fef08a;
}

/* Builder Items Dark Mode */
[data-theme="dark"] .question-builder-item {
    background: rgba(15, 23, 42, 0.45);
    border-color: rgba(255, 255, 255, 0.1);
}
[data-theme="dark"] .qz-builder-header {
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .qz-form-control {
    background: rgba(15, 23, 42, 0.75) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .qz-form-control:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.2) !important;
}
[data-theme="dark"] .qz-form-control::placeholder {
    color: #64748b !important;
}
[data-theme="dark"] .qz-input-group-text {
    background: rgba(15, 23, 42, 0.75) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

/* Format Pills Dark Mode */
[data-theme="dark"] .qz-format-pill {
    background: rgba(30, 41, 59, 0.6);
    border-color: rgba(255, 255, 255, 0.12);
    color: #cbd5e1;
}
[data-theme="dark"] .btn-check:checked + .qz-pill-mc {
    background: #3b82f6 !important;
    color: #ffffff !important;
    border-color: #60a5fa !important;
}
[data-theme="dark"] .btn-check:checked + .qz-pill-tf {
    background: #f59e0b !important;
    color: #ffffff !important;
    border-color: #fbbf24 !important;
}
[data-theme="dark"] .btn-check:checked + .qz-pill-match {
    background: #06b6d4 !important;
    color: #ffffff !important;
    border-color: #22d3ee !important;
}

/* Builder TF Box Dark Mode */
[data-theme="dark"] .qz-builder-tf-box {
    background: rgba(15, 23, 42, 0.7);
    border-color: rgba(255, 255, 255, 0.15);
}
[data-theme="dark"] .qz-tf-true .qz-tf-text {
    color: #34d399;
}
[data-theme="dark"] .qz-tf-false .qz-tf-text {
    color: #f87171;
}

/* Modals Dark Mode */
[data-theme="dark"] .qz-modal-header {
    background: rgba(15, 23, 42, 0.6);
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .qz-modal-title {
    color: #f8fafc;
}
[data-theme="dark"] .qz-modal-subtitle {
    color: #94a3b8;
}
[data-theme="dark"] .qz-guide-alert {
    background: rgba(15, 23, 42, 0.5);
    border-color: rgba(255, 255, 255, 0.1);
    color: #94a3b8;
}
[data-theme="dark"] .qz-guide-title {
    color: #f8fafc;
}
[data-theme="dark"] .qz-guide-alert strong {
    color: #e2e8f0;
}
[data-theme="dark"] .qz-guide-alert code {
    background: rgba(255, 255, 255, 0.08);
    color: #38bdf8;
}
[data-theme="dark"] .qz-modal-footer {
    background: rgba(15, 23, 42, 0.6);
    border-top-color: rgba(255, 255, 255, 0.08);
}

/* ==========================================================================
   QUESTION BUILDER TOOLBAR & VISUAL TABLE EDITOR STYLES (Light & Dark)
   ========================================================================== */

/* Question Header & Toolbar */
.qb-question-header {
    margin-bottom: 0.4rem;
}
.qb-toolbar-wrap {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    flex-wrap: wrap;
}
.qb-toolbar-btn {
    padding: .28rem .55rem;
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
    color: #0369a1;
}

/* Mobile segmented toolbar: 4 equal columns */
@media (max-width: 576px) {
    .qb-question-header {
        display: flex;
        flex-direction: column;
        align-items: stretch !important;
        gap: .45rem;
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

/* Live Preview Box */
.qb-live-preview-box {
    background: #f8fafc;
    border: 1.5px dashed #0284c7;
    border-radius: 8px;
    font-size: .85rem;
    line-height: 1.55;
}
.qb-live-preview-box img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

/* Image Attachment Box */
.qb-image-box {
    background: #ffffff;
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    padding: 0.65rem 0.85rem;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 0.45rem;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.qb-image-box:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.12);
}
.qb-image-box img {
    max-height: 170px;
    max-width: 100%;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fafafa;
}

/* Visual Table Editor & Ribbon (Excel / Word Style) */
.qb-word-table-editor {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}
.qb-table-ribbon {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 6px 10px !important;
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
    gap: 3px !important;
    flex-shrink: 0 !important;
}
.qb-ribbon-sep {
    width: 1px !important;
    height: 20px !important;
    background: #cbd5e1 !important;
    margin: 0 2px !important;
    flex-shrink: 0 !important;
}
.qb-ribbon-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 4px 8px !important;
    font-size: .73rem !important;
    font-weight: 700 !important;
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #334155 !important;
    cursor: pointer !important;
    transition: all .15s ease !important;
    white-space: nowrap !important;
    user-select: none !important;
    line-height: 1.25 !important;
}
.qb-ribbon-btn:hover:not(:disabled) {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,.06);
}
.qb-ribbon-btn:disabled {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    box-shadow: none !important;
    transform: none !important;
}
.qb-ribbon-btn-danger {
    color: #dc2626 !important;
    border-color: #fca5a5 !important;
    background: #fef2f2 !important;
}
.qb-ribbon-btn-danger:hover:not(:disabled) {
    background: #fee2e2 !important;
    border-color: #ef4444 !important;
    color: #b91c1c !important;
}

/* Canvas Wrap */
.qb-table-canvas-wrap {
    padding: 0.85rem;
    overflow-x: auto;
    background: #ffffff;
    max-height: 380px;
}
.qb-rich-table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 0;
}
.qb-rich-table th,
.qb-rich-table td {
    min-width: 75px;
    min-height: 32px;
    padding: 6px 8px;
    vertical-align: middle;
    outline: none;
    transition: background-color .15s ease, box-shadow .15s ease;
    border: 1px solid #cbd5e1;
    position: relative;
    cursor: cell;
    font-size: 0.82rem;
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

/* Table Stepper & Modals */
.word-stepper-box {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
}
.word-spin-btn {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 1rem;
    background: #ffffff;
    color: #334155;
}
.word-spin-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.word-preview-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
}
.word-mini-th {
    background: #cbd5e1;
    color: #0f172a;
    font-weight: 700;
    text-align: center;
    padding: 3px 2px;
    border: 1px solid #94a3b8;
}
.word-mini-td {
    background: #ffffff;
    color: #94a3b8;
    text-align: center;
    padding: 3px 2px;
    border: 1px solid #e2e8f0;
}

/* Symbol Buttons */
.symbol-btn {
    width: 38px;
    height: 38px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #1e293b;
    font-size: 1.15rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s ease;
}
.symbol-btn:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1d4ed8;
    transform: scale(1.08);
}

/* Border & Color Modals */
.qb-border-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1100;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(3px);
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
    border-radius: 18px 18px 0 0;
    width: 100%;
    max-width: 480px;
    max-height: 85vh;
    overflow-y: auto;
    padding: 0 0 16px;
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
        border-radius: 16px;
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
.qb-border-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    padding: 12px 16px;
}
@media (max-width: 420px) {
    .qb-border-grid { grid-template-columns: repeat(3, 1fr); }
}
.qb-border-swatch {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
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
    min-height: 54px;
}
.qb-border-swatch:hover {
    border-color: #0284c7;
    background: #eff6ff;
    color: #0369a1;
    transform: scale(1.04);
}
.qb-border-swatch svg { width: 22px; height: 22px; }
.qb-color-modal-section {
    padding: 10px 16px 4px;
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.qb-color-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 10px 16px;
}
.qb-color-ring {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 2px solid rgba(0,0,0,.08);
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
}
.qb-color-ring:hover {
    border-color: #0284c7;
    transform: scale(1.15);
}
.qb-modal-close-btn {
    display: block;
    width: calc(100% - 32px);
    margin: 8px 16px 12px;
    padding: 10px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #f1f5f9;
    font-weight: 700;
    font-size: .8rem;
    color: #334155;
    cursor: pointer;
    text-align: center;
}
.qb-modal-close-btn:hover { background: #e2e8f0; }

/* ── Dark Mode Overrides for Question Toolbar & Visual Editor ── */
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
[data-theme="dark"] .qb-image-box {
    background: rgba(15, 23, 42, 0.75);
    border-color: rgba(59, 130, 246, 0.4);
}
[data-theme="dark"] .qb-image-box img {
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}
[data-theme="dark"] .qb-word-table-editor {
    background: #182234 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
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
[data-theme="dark"] .qb-ribbon-btn:hover:not(:disabled) {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .qb-ribbon-btn-danger {
    color: #f87171 !important;
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
}
[data-theme="dark"] .qb-ribbon-btn-danger:hover:not(:disabled) {
    background: rgba(239, 68, 68, 0.25) !important;
    border-color: #ef4444 !important;
}
[data-theme="dark"] .qb-table-canvas-wrap {
    background: #0f172a !important;
}
[data-theme="dark"] .qb-rich-table th {
    background: #1e293b !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}
[data-theme="dark"] .qb-rich-table td {
    background: #0f172a !important;
    color: #f8fafc !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
}
[data-theme="dark"] .qb-rich-table .qb-cell-selected {
    box-shadow: inset 0 0 0 2.5px #60a5fa !important;
    background-color: rgba(59, 130, 246, 0.3) !important;
    color: #ffffff !important;
}
[data-theme="dark"] .word-stepper-box,
[data-theme="dark"] .word-preview-box {
    background: rgba(15, 23, 42, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}
[data-theme="dark"] .word-spin-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .word-mini-th {
    background: #334155 !important;
    color: #f8fafc !important;
    border-color: #475569 !important;
}
[data-theme="dark"] .word-mini-td {
    background: #1e293b !important;
    color: #64748b !important;
    border-color: #334155 !important;
}
[data-theme="dark"] .symbol-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .symbol-btn:hover {
    background: #334155 !important;
    border-color: #60a5fa !important;
    color: #93c5fd !important;
}
[data-theme="dark"] .qb-border-modal {
    background: #182234 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .qb-border-modal h6,
[data-theme="dark"] .qb-border-modal span,
[data-theme="dark"] .qb-border-modal-section {
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-border-swatch {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-border-swatch:hover {
    background: #334155 !important;
    border-color: #38bdf8 !important;
    color: #38bdf8 !important;
}
[data-theme="dark"] .qb-border-swatch svg rect,
[data-theme="dark"] .qb-border-swatch svg line {
    stroke: #cbd5e1 !important;
}
[data-theme="dark"] .qb-modal-close-btn {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .qb-modal-close-btn:hover {
    background: #334155 !important;
}
</style>

<script>
    let questionCounter = 0;
    let pendingDeleteQuizIndex = null;
    let activeCardIndexForTool = null;

    // State data per question card: { cleanText, imageUrl, tableHtml }
    const quizCardsState = {};
    const qbActiveCell = {};
    const qbSelectedCells = {};
    const qbTableHistory = {}; // index -> { past: [], future: [], current: '' }
    const tableHistoryDebounceTimers = {};

    /* ══════════════════════════════════════════════════════════════════
       1. IMPOR DARI BANK SOAL: PILIH SEMUA & COUNTER LOGIC
       ══════════════════════════════════════════════════════════════════ */
    function updateSelectedQbCounter() {
        const checkboxes = document.querySelectorAll('.qb-import-checkbox');
        const checked = document.querySelectorAll('.qb-import-checkbox:checked');
        const badge = document.getElementById('badgeSelectedQbCount');
        const btnText = document.getElementById('btnSubmitImportQbText');
        const toggleText = document.getElementById('textSelectAllQb');
        const toggleIcon = document.getElementById('iconSelectAllQb');

        if (badge) {
            badge.innerText = `${checked.length} dipilih`;
        }
        if (btnText) {
            btnText.innerText = checked.length > 0 ? `Impor Soal Terpilih (${checked.length} Butir)` : 'Impor Soal Terpilih';
        }
        if (toggleText && checkboxes.length > 0) {
            if (checked.length === checkboxes.length) {
                toggleText.innerText = 'Batal Pilih Semua';
                if (toggleIcon) toggleIcon.className = 'ti ti-x';
            } else {
                toggleText.innerText = 'Pilih Semua';
                if (toggleIcon) toggleIcon.className = 'ti ti-checks';
            }
        }
    }

    function toggleSelectAllQuestionBanks() {
        const checkboxes = document.querySelectorAll('.qb-import-checkbox');
        const checked = document.querySelectorAll('.qb-import-checkbox:checked');
        const shouldCheckAll = (checked.length !== checkboxes.length);

        checkboxes.forEach(cb => {
            cb.checked = shouldCheckAll;
        });
        updateSelectedQbCounter();
    }

    /* ══════════════════════════════════════════════════════════════════
       2. MODAL & GENERAL ALERTS
       ══════════════════════════════════════════════════════════════════ */
    function openDeleteQuizQuestionModal(actionUrl, text) {
        const form = document.getElementById('deleteQuizQuestionForm');
        form.action = actionUrl;
        const msg = document.getElementById('deleteModalQuestionText');
        if (msg && text) {
            msg.innerText = `Anda akan menghapus: "${text}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteQuizQuestion'));
        modal.show();
    }

    function showQuizModalAlert(title, message) {
        document.getElementById('modalAlertQuizTitle').innerText = title;
        document.getElementById('modalAlertQuizMsg').innerText = message;
        const modal = new bootstrap.Modal(document.getElementById('modalAlertQuiz'));
        modal.show();
    }

    /* ══════════════════════════════════════════════════════════════════
       3. MEDIA & TABLE EXTRACTION & COMPILATION
       ══════════════════════════════════════════════════════════════════ */
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

    /* ══════════════════════════════════════════════════════════════════
       4. PREVIEW RENDERING & DRAWER
       ══════════════════════════════════════════════════════════════════ */
    function formatQuestionPreviewHtml(raw) {
        if (!raw || !raw.trim()) return '<em class="text-muted">Belum ada teks pertanyaan...</em>';

        // 1. Render Markdown images: ![alt](url)
        let html = raw.replace(/!\[(.*?)\]\((.*?)\)/g, (match, alt, url) => {
            return `<div class="text-center my-2 q-media-wrap"><img src="${url}" alt="${alt || 'Gambar Soal'}" class="img-fluid rounded border shadow-xs" style="max-height: 240px; object-fit: contain;"></div>`;
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

    function updateQuizCardLivePreview(index) {
        const previewBox = document.getElementById(`qz_preview_${index}`);
        const textarea = document.getElementById(`qz_qtextarea_${index}`);
        if (!previewBox || !textarea) return;

        const state = quizCardsState[index] || {};
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
        const previewBox = document.getElementById(`qz_preview_${index}`);
        if (!previewBox) return;

        if (previewBox.classList.contains('d-none')) {
            updateQuizCardLivePreview(index);
            previewBox.classList.remove('d-none');
        } else {
            previewBox.classList.add('d-none');
        }
    }

    /* ══════════════════════════════════════════════════════════════════
       5. IMAGE ATTACHMENT BOX & MODAL LOGIC
       ══════════════════════════════════════════════════════════════════ */
    function renderImageAttachmentWidget(index) {
        const wrap = document.getElementById(`quiz_image_attachment_wrap_${index}`);
        if (!wrap) return;

        const state = quizCardsState[index];
        const imageUrl = state ? state.imageUrl : null;

        if (!imageUrl) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-image-box shadow-xs" id="quiz_img_box_${index}">
                <img src="${imageUrl}" alt="Pratinjau Gambar Soal">
                <div class="d-flex align-items-center gap-2 mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: .74rem; font-weight: 600;" onclick="openInsertImageModal(${index})">
                        <i class="ti ti-refresh"></i> Ganti Gambar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size: .74rem; font-weight: 600;" onclick="removeAttachedImage(${index})">
                        <i class="ti ti-trash"></i> Hapus Gambar
                    </button>
                </div>
            </div>
        `;
    }

    function attachImageToCard(index, url) {
        if (!quizCardsState[index]) quizCardsState[index] = {};
        quizCardsState[index].imageUrl = url;
        renderImageAttachmentWidget(index);
        updateQuizCardLivePreview(index);
    }

    function removeAttachedImage(index) {
        if (!quizCardsState[index]) return;
        quizCardsState[index].imageUrl = null;
        renderImageAttachmentWidget(index);
        updateQuizCardLivePreview(index);
    }

    function openInsertImageModal(index) {
        activeCardIndexForTool = index;
        document.getElementById('modalImgFileInput').value = '';
        document.getElementById('modalImgUrlInput').value = '';
        document.getElementById('modalImgPreviewWrap').classList.add('d-none');
        const modal = new bootstrap.Modal(document.getElementById('modalInsertImage'));
        modal.show();
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
                    showQuizModalAlert('Gagal Upload', data.error || 'Terjadi kesalahan saat mengunggah gambar.');
                }
            })
            .catch(err => {
                progressEl.classList.add('d-none');
                submitBtn.disabled = false;
                showQuizModalAlert('Gagal Upload', 'Terjadi kesalahan jaringan saat mengunggah gambar.');
            });
        } else if (urlInput.value.trim()) {
            const imgUrl = urlInput.value.trim();
            attachImageToCard(activeCardIndexForTool, imgUrl);

            const modalEl = document.getElementById('modalInsertImage');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else {
            showQuizModalAlert('Perhatian', 'Pilih file gambar atau masukkan URL gambar terlebih dahulu.');
        }
    }

    /* ══════════════════════════════════════════════════════════════════
       6. SYMBOL INSERTION LOGIC
       ══════════════════════════════════════════════════════════════════ */
    function openInsertSymbolModal(index) {
        activeCardIndexForTool = index;
        const modal = new bootstrap.Modal(document.getElementById('modalInsertSymbol'));
        modal.show();
    }

    function insertSymbolToActiveCard(symbol) {
        if (activeCardIndexForTool === null) return;
        const textarea = document.getElementById(`qz_qtextarea_${activeCardIndexForTool}`);
        if (!textarea) return;

        insertTextAtCursor(textarea, symbol);
        handleQuizTextareaInput(activeCardIndexForTool);
    }

    function insertTextAtCursor(textarea, textToInsert) {
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + textToInsert + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + textToInsert.length;
        textarea.focus();
    }

    /* ══════════════════════════════════════════════════════════════════
       7. TABLE MODAL, STEPPER, & PRESETS
       ══════════════════════════════════════════════════════════════════ */
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

    function applyWordGridDimensions(cols, rows) {
        const colsInput = document.getElementById('qb_table_cols_input');
        const rowsInput = document.getElementById('qb_table_rows_input');
        if (colsInput) colsInput.value = cols;
        if (rowsInput) rowsInput.value = rows;
        updateWordBadgeFromInputs();
    }

    function updateWordBadgeFromInputs() {
        const cols = parseInt(document.getElementById('qb_table_cols_input').value || 3);
        const rows = parseInt(document.getElementById('qb_table_rows_input').value || 3);
        const hasHeader = document.getElementById('qb_table_has_header') ? document.getElementById('qb_table_has_header').checked : true;
        updateWordMiniPreview(cols, rows, hasHeader);
    }

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

        if (!quizCardsState[index]) quizCardsState[index] = {};
        quizCardsState[index].tableHtml = tableHtml;
        renderVisualTableEditor(index);
        updateQuizCardLivePreview(index);
        closeInsertTableModal();

        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
        if (wrap) {
            const firstCell = wrap.querySelector('th, td');
            if (firstCell) {
                firstCell.focus();
                firstCell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }

    function openVisualTableModalOrAdd(index) {
        if (!quizCardsState[index]) quizCardsState[index] = {};
        if (!quizCardsState[index].tableHtml || !quizCardsState[index].tableHtml.trim()) {
            openInsertTableModal(index);
            return;
        }

        renderVisualTableEditor(index);
        updateQuizCardLivePreview(index);

        const cardEl = document.getElementById(`quiz_tbl_card_${index}`);
        if (cardEl) {
            cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    /* ══════════════════════════════════════════════════════════════════
       8. VISUAL TABLE EDITOR & WORD/EXCEL STYLE RIBBON ACTIONS
       ══════════════════════════════════════════════════════════════════ */
    function renderVisualTableEditor(index) {
        const wrap = document.getElementById(`quiz_table_editor_wrap_${index}`);
        if (!wrap) return;

        const state = quizCardsState[index];
        const tableHtml = state ? state.tableHtml : null;

        if (!tableHtml || !tableHtml.trim()) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-word-table-editor rounded-3 border overflow-hidden mb-2" id="quiz_tbl_card_${index}">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="ti ti-table me-1"></i> Tabel Soal</span>
                        <span class="text-muted small d-none d-sm-inline" style="font-size:0.75rem;">Klik pada sel untuk mengetik teks</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2" style="font-size:0.74rem;" onclick="openInsertTableModal(${index})">
                            <i class="ti ti-settings me-1"></i> Ubah Ukuran
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size:0.74rem;" onclick="removeRichTable(${index})">
                            <i class="ti ti-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>

                <!-- Word / Excel Style Ribbon -->
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
                <div class="qb-table-canvas-wrap" id="quiz_table_canvas_wrap_${index}">
                    ${tableHtml}
                </div>

                <!-- Bottom Action: Quick Append Row -->
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

    function setupRichTableEventListeners(index) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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

            // Tab key navigation
            cell.onkeydown = function(e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    const allCells = Array.from(table.querySelectorAll('th, td'));
                    const curIdx = allCells.indexOf(cell);
                    if (e.shiftKey) {
                        if (curIdx > 0) allCells[curIdx - 1].focus();
                    } else {
                        if (curIdx < allCells.length - 1) {
                            allCells[curIdx + 1].focus();
                        } else {
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
                    return;
                }

                isDragging = true;
                dragStartCell = cell;
                qbActiveCell[index] = cell;

                cells.forEach(c => c.classList.remove('qb-cell-selected'));
                qbSelectedCells[index].clear();
                qbSelectedCells[index].add(cell);
                cell.classList.add('qb-cell-selected');
            };

            // Mouse over while dragging
            cell.onmouseover = function() {
                if (isDragging && dragStartCell && dragStartCell !== cell) {
                    selectCellRange(index, dragStartCell, cell);
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
                }
            };

            // Typing sync
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
            }, { passive: true });
        });

        // Keyboard shortcuts for table (Ctrl+Z: Undo, Ctrl+Y: Redo)
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

        const onTouchMoveHandler = function(e) {
            if (!touchStartCell) return;
            const touch = e.touches[0];
            const targetEl = document.elementFromPoint(touch.clientX, touch.clientY);
            if (targetEl && (targetEl.tagName === 'TD' || targetEl.tagName === 'TH') && wrap.contains(targetEl)) {
                if (targetEl !== touchStartCell) {
                    selectCellRange(index, touchStartCell, targetEl);
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

        if (!qbActiveCell[index] || !wrap.contains(qbActiveCell[index])) {
            const firstCell = table.querySelector('th, td');
            if (firstCell) {
                firstCell.classList.add('qb-cell-selected');
                qbActiveCell[index] = firstCell;
                qbSelectedCells[index].add(firstCell);
            }
        }
    }

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

    function selectCellRange(index, cell1, cell2) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
        if (!wrap) return;

        wrap.innerHTML = html;
        qbActiveCell[index] = null;
        if (qbSelectedCells[index]) qbSelectedCells[index].clear();

        setupRichTableEventListeners(index);

        if (!quizCardsState[index]) quizCardsState[index] = {};
        quizCardsState[index].tableHtml = html;
        updateQuizCardLivePreview(index);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        if (!quizCardsState[index]) quizCardsState[index] = {};
        quizCardsState[index].tableHtml = table.outerHTML;
        updateQuizCardLivePreview(index);

        pushTableHistory(index, isDebounced);
    }

    function insertTableRow(index, position = 'after') {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        if (!quizCardsState[index]) return;
        quizCardsState[index].tableHtml = null;
        qbActiveCell[index] = null;
        if (qbSelectedCells[index]) qbSelectedCells[index].clear();
        delete qbTableHistory[index];
        renderVisualTableEditor(index);
        updateQuizCardLivePreview(index);
    }

    function mergeSelectedCells(index) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
            alert('Silakan blok minimal 2 sel berdampingan terlebih dahulu untuk digabungkan.');
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

        selected.forEach(cell => {
            if (cell !== topLeftCell && cell.parentElement) {
                cell.remove();
            }
        });

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
            alert('Sel ini adalah sel normal (belum digabung).');
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

    /* ══════════════════════════════════════════════════════════════════
       9. SHARED BORDER & COLOR MODALS
       ══════════════════════════════════════════════════════════════════ */
    function buildSharedModals() {
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
                        <button type="button" class="qb-border-swatch" data-btype="horizontal">
                            <svg viewBox="0 0 20 20"><line x1="1" y1="7" x2="19" y2="7" stroke="#334155" stroke-width="1.5"/><line x1="1" y1="13" x2="19" y2="13" stroke="#334155" stroke-width="1.5"/></svg>
                            H-Line
                        </button>
                    </div>

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
                            <span class="small text-muted" style="font-size:.74rem;">Pilih warna untuk mempercantik sel terpilih</span>
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

    function setCellBorder(index, borderType) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0)
            ? Array.from(selected)
            : (qbActiveCell[index] ? [qbActiveCell[index]] : []);

        if (targetCells.length === 0) return;

        const brd = '1.5px solid #334155';
        const brdThick = '3px solid #0f172a';
        const brdNone = 'none';

        targetCells.forEach(c => {
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

    function setTableBorderStyle(index, borderType) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
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
        }

        syncRichTableToState(index);
    }

    function setCellBackground(index, color) {
        const wrap = document.getElementById(`quiz_table_canvas_wrap_${index}`);
        if (!wrap) return;
        const selected = qbSelectedCells[index];
        const targetCells = (selected && selected.size > 0) ? Array.from(selected) : (qbActiveCell[index] ? [qbActiveCell[index]] : []);

        targetCells.forEach(c => {
            c.style.backgroundColor = color || '';
        });
        syncRichTableToState(index);
    }

    /* ══════════════════════════════════════════════════════════════════
       10. QUESTION BUILDER DYNAMIC CARDS & EVENT LISTENERS
       ══════════════════════════════════════════════════════════════════ */
    function renderQuestionCard(index, data = null) {
        let rawQText = data ? (data.question_text || '') : '';
        const qType = data ? (data.question_type || 'multiple_choice') : 'multiple_choice';
        const options = (data && data.options) ? data.options : ['', '', '', ''];
        const correctOpt = (data && typeof data.correct_option !== 'undefined') ? parseInt(data.correct_option) : 0;
        const correctTf = (data && data.correct_tf) ? data.correct_tf : 'Benar';
        const pairs = (data && data.pairs && data.pairs.length >= 2) ? data.pairs : [
            { premise: '', match: '' },
            { premise: '', match: '' }
        ];

        // Extract clean text, image, and table
        const extracted = extractMediaAndTableFromText(rawQText);
        let cleanText = data && data.cleanText !== undefined ? data.cleanText : extracted.cleanText;
        let imageUrl = data && data.imageUrl !== undefined ? data.imageUrl : extracted.imageUrl;
        let tableHtml = data && data.tableHtml !== undefined ? data.tableHtml : extracted.tableHtml;

        quizCardsState[index] = {
            cleanText: cleanText,
            imageUrl: imageUrl,
            tableHtml: tableHtml
        };

        const card = document.createElement('div');
        card.className = 'question-builder-item position-relative overflow-hidden';
        card.id = `qCard_${index}`;
        card.setAttribute('data-q-idx', index);
        card.setAttribute('data-current-type', qType);

        let optionsHtml = '';
        for (let i = 0; i < 4; i++) {
            const letter = String.fromCharCode(65 + i);
            const val = options[i] || '';
            const checked = (correctOpt === i) ? 'checked' : '';
            const placeholder = `Opsi ${letter}` + (i >= 2 ? ' (opsional)' : '');
            const isReq = (i < 2) ? 'required' : '';
            optionsHtml += `
                <div class="input-group input-group-sm mb-1.5">
                    <div class="input-group-text qz-input-group-text">
                        <input class="form-check-input mt-0" type="radio" name="questions[${index}][correct_option]" value="${i}" ${checked} title="Tandai sebagai kunci jawaban">
                    </div>
                    <input type="text" name="questions[${index}][options][]" class="form-control form-control-sm qz-form-control" placeholder="${placeholder}" value="${escapeHtml(val)}" ${isReq}>
                </div>
            `;
        }

        let pairsHtml = '';
        pairs.forEach((p, pIdx) => {
            pairsHtml += `
                <div class="row g-1 align-items-center mb-1.5 pair-row">
                    <div class="col-6">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][premise]" class="form-control form-control-sm qz-form-control" placeholder="Premis ${pIdx + 1}" value="${escapeHtml(p.premise || '')}">
                    </div>
                    <div class="col-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][match]" class="form-control form-control-sm qz-form-control" placeholder="Pasangan ${pIdx + 1}" value="${escapeHtml(p.match || '')}">
                    </div>
                    <div class="col-1 text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removePairRowFromCard(this)" title="Hapus baris">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        card.innerHTML = `
            <div class="qz-builder-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-bold q-num-badge">Soal #1</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-q-btn" onclick="requestRemoveQuestionCard(${index})" title="Hapus butir soal ini">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Format Selector -->
            <div class="mb-2">
                <div class="qz-format-pills" role="group">
                    <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_mc" value="multiple_choice" ${qType === 'multiple_choice' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'multiple_choice')">
                    <label class="qz-format-pill qz-pill-mc" for="qtype_${index}_mc"><i class="ti ti-list-check me-1"></i>Pilgan</label>

                    <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_tf" value="true_false" ${qType === 'true_false' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'true_false')">
                    <label class="qz-format-pill qz-pill-tf" for="qtype_${index}_tf"><i class="ti ti-checkup-list me-1"></i>Benar/Salah</label>

                    <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_match" value="matching" ${qType === 'matching' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'matching')">
                    <label class="qz-format-pill qz-pill-match" for="qtype_${index}_match"><i class="ti ti-arrows-left-right me-1"></i>Menjodohkan</label>
                </div>
            </div>

            <!-- Pertanyaan & Rich Toolbar (Identik dengan Bank Soal) -->
            <div class="mb-2 ${qType === 'matching' ? 'd-none' : ''}" id="quiz_sec_qtext_${index}">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1.5 qb-question-header">
                    <label class="form-label small fw-semibold text-muted mb-0">Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
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

                <textarea name="questions[${index}][question_text]" id="qz_qtextarea_${index}" class="form-control form-control-sm qz-form-control mb-2" rows="2" placeholder="Tuliskan pertanyaan soal..." ${qType === 'matching' ? '' : 'required'} oninput="handleQuizTextareaInput(${index})">${escapeHtml(cleanText)}</textarea>
                
                <!-- Container Lampiran Gambar -->
                <div id="quiz_image_attachment_wrap_${index}" class="mb-2"></div>

                <!-- Container Editor Tabel Visual -->
                <div id="quiz_table_editor_wrap_${index}" class="mb-2"></div>

                <div id="qz_preview_${index}" class="qb-live-preview-box p-2.5 mt-2 d-none"></div>
            </div>

            <!-- Section MC -->
            <div class="sec-mc ${qType === 'multiple_choice' ? '' : 'd-none'}" id="sec_mc_${index}">
                <label class="form-label small fw-semibold text-muted mb-1">Opsi Pilihan (Pilih Radio Kunci Benar):</label>
                <div class="options-container">
                    ${optionsHtml}
                </div>
            </div>

            <!-- Section TF -->
            <div class="sec-tf ${qType === 'true_false' ? '' : 'd-none'}" id="sec_tf_${index}">
                <label class="form-label small fw-semibold text-muted mb-2">Pilih Kunci Jawaban yang Benar <span class="text-danger">*</span>:</label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-true w-100" for="q${index}_tf_true">
                            <input class="form-check-input qb-tf-radio" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-check"></i></span>
                                <span class="qb-tf-title">Benar (True)</span>
                            </div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-false w-100" for="q${index}_tf_false">
                            <input class="form-check-input qb-tf-radio" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-x"></i></span>
                                <span class="qb-tf-title">Salah (False)</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section Matching -->
            <div class="sec-matching ${qType === 'matching' ? '' : 'd-none'}" id="sec_matching_${index}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-semibold text-muted mb-0">Pasangan yang Cocok:</label>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none" onclick="addPairToCard(${index})">
                        <i class="ti ti-plus"></i> Tambah Pasangan
                    </button>
                </div>
                <div class="pairs-container" id="pairs_container_${index}">
                    ${pairsHtml}
                </div>
            </div>
        `;

        return card;
    }

    function handleQuizTextareaInput(index) {
        const textarea = document.getElementById(`qz_qtextarea_${index}`);
        if (!textarea) return;
        if (!quizCardsState[index]) quizCardsState[index] = {};
        quizCardsState[index].cleanText = textarea.value;
        updateQuizCardLivePreview(index);
    }

    function handleQuizFormatChange(index, newType) {
        const card = document.getElementById(`qCard_${index}`);
        if (!card) return;

        const oldType = card.getAttribute('data-current-type') || 'multiple_choice';
        const qTextArea = card.querySelector('textarea[name*="[question_text]"]');
        const qText = qTextArea ? qTextArea.value.trim() : '';

        let hasContent = false;
        if (qText.length > 0 || (quizCardsState[index] && (quizCardsState[index].imageUrl || quizCardsState[index].tableHtml))) {
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

            addNewQuestionCard({ question_type: newType });

            const newCards = document.querySelectorAll('.question-builder-item');
            const lastCard = newCards[newCards.length - 1];
            if (lastCard) {
                lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            card.setAttribute('data-current-type', newType);
            switchItemType(index, newType);
        }
    }

    function switchItemType(index, type) {
        const secMc = document.getElementById(`sec_mc_${index}`);
        const secTf = document.getElementById(`sec_tf_${index}`);
        const secMatch = document.getElementById(`sec_matching_${index}`);
        const qSecText = document.getElementById(`quiz_sec_qtext_${index}`);
        const card = document.getElementById(`qCard_${index}`);
        const qTextArea = card ? card.querySelector('textarea[name*="[question_text]"]') : null;
        const mcInputs = secMc.querySelectorAll('input[type="text"]');
        const matchInputs = secMatch.querySelectorAll('input[type="text"]');

        if (type === 'matching') {
            if (qSecText) qSecText.classList.add('d-none');
            if (qTextArea) qTextArea.removeAttribute('required');
        } else {
            if (qSecText) qSecText.classList.remove('d-none');
            if (qTextArea) qTextArea.setAttribute('required', 'required');
        }

        if (type === 'true_false') {
            secMc.classList.add('d-none');
            secTf.classList.remove('d-none');
            secMatch.classList.add('d-none');
            mcInputs.forEach(i => i.removeAttribute('required'));
            matchInputs.forEach(i => i.removeAttribute('required'));
        } else if (type === 'matching') {
            secMc.classList.add('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.remove('d-none');
            mcInputs.forEach(i => i.removeAttribute('required'));
            matchInputs.forEach(i => i.setAttribute('required', 'required'));
        } else {
            secMc.classList.remove('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.add('d-none');
            mcInputs.forEach((input, i) => {
                if (i < 2) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
            matchInputs.forEach(i => i.removeAttribute('required'));
        }
    }

    function addNewQuestionCard(data = null) {
        const container = document.getElementById('questionsContainer');
        const card = renderQuestionCard(questionCounter, data);
        container.appendChild(card);
        const cardIndex = questionCounter;
        questionCounter++;

        renderImageAttachmentWidget(cardIndex);
        renderVisualTableEditor(cardIndex);

        updateQuestionNumbers();
        setTimeout(() => updateQuizCardLivePreview(cardIndex), 30);
    }

    function requestRemoveQuestionCard(index) {
        const allCards = document.querySelectorAll('.question-builder-item');
        if (allCards.length <= 1) {
            showQuizModalAlert('Pemberitahuan', 'Minimal harus ada 1 butir soal dalam form.');
            return;
        }
        pendingDeleteQuizIndex = index;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteQuizCard'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        addNewQuestionCard();
        updateSelectedQbCounter();

        // Form Submission: Compile Final Question Texts (Text + Image + Visual Table)
        const form = document.getElementById('batchQuestionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const cards = document.querySelectorAll('.question-builder-item');
                cards.forEach((card) => {
                    const idx = card.getAttribute('data-q-idx');
                    const textarea = card.querySelector('textarea[name*="[question_text]"]');
                    if (textarea && idx !== null) {
                        syncRichTableToState(idx);
                        const state = quizCardsState[idx] || {};
                        const compiled = compileFinalQuestionText(textarea.value, state.imageUrl, state.tableHtml);
                        textarea.value = compiled;
                    }
                });
            });
        }

        document.getElementById('btnConfirmDeleteQuizCard')?.addEventListener('click', function() {
            if (pendingDeleteQuizIndex !== null) {
                const card = document.getElementById(`qCard_${pendingDeleteQuizIndex}`);
                if (card) {
                    card.remove();
                    delete quizCardsState[pendingDeleteQuizIndex];
                    delete qbActiveCell[pendingDeleteQuizIndex];
                    delete qbSelectedCells[pendingDeleteQuizIndex];
                    delete qbTableHistory[pendingDeleteQuizIndex];
                    updateQuestionNumbers();
                }
                pendingDeleteQuizIndex = null;
            }
            const modalEl = document.getElementById('modalConfirmDeleteQuizCard');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    });

    function addPairToCard(qIndex) {
        const container = document.getElementById(`pairs_container_${qIndex}`);
        const currentCount = container.querySelectorAll('.pair-row').length;
        const row = document.createElement('div');
        row.className = 'row g-1 align-items-center mb-1.5 pair-row';
        row.innerHTML = `
            <div class="col-6">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][premise]" class="form-control form-control-sm qz-form-control" placeholder="Premis ${currentCount + 1}" required>
            </div>
            <div class="col-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][match]" class="form-control form-control-sm qz-form-control" placeholder="Pasangan ${currentCount + 1}" required>
            </div>
            <div class="col-1 text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removePairRowFromCard(this)">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    function removePairRowFromCard(button) {
        const container = button.closest('.pairs-container');
        const rows = container.querySelectorAll('.pair-row');
        if (rows.length <= 2) {
            showQuizModalAlert('Pemberitahuan', 'Soal menjodohkan membutuhkan minimal 2 pasangan.');
            return;
        }
        button.closest('.pair-row').remove();
    }

    function updateQuestionNumbers() {
        const cards = document.querySelectorAll('.question-builder-item');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.q-num-badge');
            if (badge) badge.innerText = `Soal #${idx + 1}`;
            
            const delBtn = card.querySelector('.delete-q-btn');
            if (delBtn) {
                delBtn.style.display = (cards.length > 1) ? 'inline-block' : 'none';
            }
        });

        const submitText = document.getElementById('submitBtnText');
        if (submitText) {
            submitText.innerText = `Simpan Semua Soal (${cards.length} Butir)`;
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

    // Modal Quick Paste Parser
    function loadSampleText() {
        const sample = `1. Apa ibukota negara Indonesia saat ini?\nA. Jakarta\nB. Surabaya\nC. Bandung\nD. Medan\nKunci: A\n\n2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.\nKunci: Benar\n\n3. Jodohkan bahasa pemrograman dengan ekstensinya:\nPHP = .php\nPython = .py\nJavaScript = .js`;
        document.getElementById('quickPasteTextarea').value = sample;
    }

    function parseAndInsertQuestions() {
        const text = document.getElementById('quickPasteTextarea').value.trim();
        if (!text) {
            showQuizModalAlert('Perhatian', 'Silakan tempelkan naskah soal terlebih dahulu.');
            return;
        }

        const lines = text.replace(/\r\n/g, '\n').split('\n');
        const blocks = [];
        let currentBlock = [];

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            const isNewQuestion = /^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)/i.test(line);

            if (isNewQuestion && currentBlock.length > 0) {
                blocks.push(currentBlock);
                currentBlock = [line];
            } else if (line !== '' || currentBlock.length > 0) {
                currentBlock.push(line);
            }
        }
        if (currentBlock.length > 0) {
            blocks.push(currentBlock);
        }

        if (blocks.length === 0) {
            showQuizModalAlert('Format Salah', 'Format soal tidak terdeteksi. Pastikan setiap butir soal diawali angka nomor soal (misal: 1. Pertanyaan).');
            return;
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
            let inPairs = false;

            block.forEach(rawLine => {
                const line = rawLine.trim();
                if (!line) return;

                const optMatch = line.match(/^([A-Da-d])[\.\)]\s*(.*)$/);
                const keyMatch = line.match(/^(?:kunci|jawaban|key|ans)\s*[\:\=]?\s*([A-Da-d]|benar|salah|true|false)/i);
                const pairMatch = line.match(/^(.+?)\s*(?:=|->)\s*(.+)$/);

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
                } else if (optMatch) {
                    inOptions = true;
                    options.push(optMatch[2].trim());
                } else if (!inOptions && pairMatch && !line.match(/^(\d+[\.\)]|\bsoal)/i)) {
                    inPairs = true;
                    pairs.push({
                        premise: pairMatch[1].trim(),
                        match: pairMatch[2].trim()
                    });
                } else if (!inOptions && !inPairs) {
                    const clean = line.replace(/^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)\s*/i, '');
                    qText += (qText ? ' ' : '') + clean;
                }
            });

            if (pairs.length >= 2) {
                qType = 'matching';
            } else if (qType === 'true_false') {
                // already detected via keyMatch (Benar/Salah)
            } else if (options.length >= 2) {
                qType = 'multiple_choice';
            } else if (qText.toLowerCase().includes('benar') || qText.toLowerCase().includes('salah')) {
                qType = 'true_false';
            }

            while (options.length < 4) {
                options.push('');
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

        const container = document.getElementById('questionsContainer');
        container.innerHTML = '';
        questionCounter = 0;

        parsedQuestions.forEach(q => {
            addNewQuestionCard(q);
        });

        const modalEl = document.getElementById('modalQuickPaste');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        document.getElementById('batchQuestionForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>
@endsection
