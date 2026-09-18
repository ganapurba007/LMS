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
                            <div class="d-flex align-items-center gap-2">
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
                            </div>
                            <button type="button" class="qz-btn-delete-q" 
                                    onclick="openDeleteQuizQuestionModal('{{ route('admin.quizzes.destroy-question', [$quiz, $q]) }}', '{{ addslashes(Str::limit($q->question_text, 60)) }}')" 
                                    title="Hapus Soal">
                                <i class="ti ti-trash"></i> <span>Hapus</span>
                            </button>
                        </div>
                        <p class="qz-question-text mb-2">{{ $q->question_text }}</p>

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
                                                <td class="premise-cell">{{ $opt->option_text }}</td>
                                                <td class="match-cell"><i class="ti ti-arrow-right me-1"></i> {{ $opt->match_text }}</td>
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
                                        <span>{{ $opt->option_text }}</span>
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
                                        <span class="opt-text">{{ $opt->option_text }}</span>
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
            <div class="qz-card-header">
                <h5 class="qz-card-title">
                    <i class="ti ti-file-import text-primary me-1.5"></i> Impor Soal dari Bank Soal
                </h5>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.quizzes.import-questions', $quiz) }}" method="POST">
                    @csrf
                    <div class="qz-qb-scroll mb-3">
                        @forelse($questionBanks as $qb)
                            <label class="qz-qb-item d-flex align-items-center gap-2 mb-1.5" for="qb_{{ $qb->id }}">
                                <input class="form-check-input mt-0 flex-shrink-0" type="checkbox" name="question_bank_ids[]" value="{{ $qb->id }}" id="qb_{{ $qb->id }}">
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
                    <button type="submit" class="md-btn-primary w-100 justify-content-center" {{ $questionBanks->isEmpty() ? 'disabled' : '' }}>
                        <i class="ti ti-file-import"></i> <span>Impor Soal Terpilih</span>
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
</style>

<script>
    let questionCounter = 0;
    let pendingDeleteQuizIndex = null;

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

    function renderQuestionCard(index, data = null) {
        const qText = data ? (data.question_text || '') : '';
        const qType = data ? (data.question_type || 'multiple_choice') : 'multiple_choice';
        const options = (data && data.options) ? data.options : ['', '', '', ''];
        const correctOpt = (data && typeof data.correct_option !== 'undefined') ? parseInt(data.correct_option) : 0;
        const correctTf = (data && data.correct_tf) ? data.correct_tf : 'Benar';
        const pairs = (data && data.pairs && data.pairs.length >= 2) ? data.pairs : [
            { premise: '', match: '' },
            { premise: '', match: '' }
        ];

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

            <!-- Pertanyaan -->
            <div class="mb-2 ${qType === 'matching' ? 'd-none' : ''}" id="quiz_sec_qtext_${index}">
                <textarea name="questions[${index}][question_text]" class="form-control form-control-sm qz-form-control" rows="2" placeholder="Tuliskan pertanyaan soal..." ${qType === 'matching' ? '' : 'required'}>${escapeHtml(qText)}</textarea>
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
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Kunci Jawaban yang Benar:</label>
                <div class="qz-builder-tf-wrap">
                    <label class="qz-builder-tf-box qz-tf-true" for="q${index}_tf_true">
                        <input class="form-check-input me-1.5" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                        <span class="qz-tf-text"><i class="ti ti-check me-0.5"></i> Benar (True)</span>
                    </label>
                    <label class="qz-builder-tf-box qz-tf-false" for="q${index}_tf_false">
                        <input class="form-check-input me-1.5" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                        <span class="qz-tf-text"><i class="ti ti-x me-0.5"></i> Salah (False)</span>
                    </label>
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

    // Smart Tab Change & Auto Spawn logic for Quiz Show
    function handleQuizFormatChange(index, newType) {
        const card = document.getElementById(`qCard_${index}`);
        if (!card) return;

        const oldType = card.getAttribute('data-current-type') || 'multiple_choice';

        const qTextArea = card.querySelector('textarea[name*="[question_text]"]');
        const qText = qTextArea ? qTextArea.value.trim() : '';

        let hasContent = false;
        if (qText.length > 0) {
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
        questionCounter++;
        updateQuestionNumbers();
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

        document.getElementById('btnConfirmDeleteQuizCard')?.addEventListener('click', function() {
            if (pendingDeleteQuizIndex !== null) {
                const card = document.getElementById(`qCard_${pendingDeleteQuizIndex}`);
                if (card) {
                    card.remove();
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
