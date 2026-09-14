@extends('layouts.be.master')

@section('header_title', 'Kelola Soal Kuis — ' . $quiz->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Kelola Soal Kuis: {{ $quiz->title }}</h3>
        <p class="text-muted small mb-0">Tambah, impor dari bank soal, atau susun butir soal kuis multi-format.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.quizzes.students', $quiz) }}" class="btn btn-outline-info d-inline-flex align-items-center gap-1.5 fw-semibold">
            <i class="ti ti-users"></i> Hasil &amp; Status Siswa
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kuis
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-circle fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quiz Overview Card -->
<div class="card shadow-sm border-0 mb-4 rounded-3">
    <div class="card-body p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <span class="text-muted small text-uppercase fw-bold">Mata Pelajaran</span>
                <div class="fw-bold fs-5 text-primary">{{ $quiz->subject->name ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <span class="text-muted small text-uppercase fw-bold">Kelas Target</span>
                <div class="fw-bold fs-5 text-dark">{{ $quiz->classroom->name ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <span class="text-muted small text-uppercase fw-bold">Durasi Pengerjaan</span>
                <div class="fw-bold fs-5 text-dark"><i class="ti ti-clock me-1"></i>{{ $quiz->formatted_duration }}</div>
            </div>
            <div class="col-md-3">
                <span class="text-muted small text-uppercase fw-bold">Poin per Soal</span>
                <div class="fw-bold fs-5 text-success">{{ $quiz->points_per_question }} Poin</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Current Quiz Questions List -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-dark">Daftar Soal Kuis ({{ $quiz->questions->count() === $quiz->total_questions_count ? $quiz->total_questions_count . ' Soal' : $quiz->questions->count() . ' Nomor • ' . $quiz->total_questions_count . ' Butir Soal' }})</h5>
            </div>
            <div class="card-body p-0">
                @forelse($quiz->questions as $index => $q)
                    <div class="p-3 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0">Soal {{ $index + 1 }}</h6>
                                @if($q->isMatching())
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="ti ti-arrows-left-right me-1"></i> Menjodohkan
                                    </span>
                                @elseif($q->isTrueFalse())
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="ti ti-checkup-list me-1"></i> Benar / Salah
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="ti ti-list-check me-1"></i> Pilihan Ganda
                                    </span>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5 rounded-3" 
                                    onclick="openDeleteQuizQuestionModal('{{ route('admin.quizzes.destroy-question', [$quiz, $q]) }}', '{{ addslashes(Str::limit($q->question_text, 60)) }}')" 
                                    title="Hapus Soal">
                                <i class="ti ti-trash me-1"></i> Hapus
                            </button>
                        </div>
                        <p class="mb-2 fw-medium text-dark">{{ $q->question_text }}</p>

                        @if($q->isMatching())
                            <!-- Display Matching Pairs -->
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%;">Premis / Pertanyaan</th>
                                            <th style="width: 50%;">Pasangan Jawaban Benar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($q->options as $opt)
                                            <tr>
                                                <td class="fw-semibold text-dark">{{ $opt->option_text }}</td>
                                                <td class="text-success fw-bold"><i class="ti ti-arrow-right me-1"></i> {{ $opt->match_text }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($q->isTrueFalse())
                            <!-- Display True/False -->
                            <div class="d-flex align-items-center gap-3 mt-2">
                                @foreach($q->options as $opt)
                                    <div class="px-3 py-1.5 rounded-3 border d-flex align-items-center gap-1.5 small {{ $opt->is_correct ? 'bg-success-subtle border-success text-success fw-bold' : 'bg-light border-light text-muted' }}">
                                        <i class="ti {{ $opt->is_correct ? 'ti-circle-check-filled' : 'ti-circle' }}"></i>
                                        <span>{{ $opt->option_text }}</span>
                                        @if($opt->is_correct)
                                            <span class="badge bg-success ms-1" style="font-size: 0.68rem;">Kunci Jawaban</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Display Multiple Choice Options -->
                            <div class="ps-3 border-start border-3 border-primary">
                                @foreach($q->options as $optIndex => $opt)
                                    <div class="small mb-1 {{ $opt->is_correct ? 'text-success fw-bold' : 'text-muted' }}">
                                        @if($opt->is_correct)
                                            <i class="ti ti-check me-1"></i>
                                        @else
                                            <i class="ti ti-circle me-1"></i>
                                        @endif
                                        <span class="me-1 fw-bold">{{ chr(65 + $optIndex) }}.</span> {{ $opt->option_text }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-help-off fs-1 d-block mb-2 text-secondary"></i>
                        Belum ada soal dalam kuis ini. Silakan impor dari Bank Soal atau tambah soal manual.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Import from Bank Soal or Manual Creation -->
    <div class="col-lg-5">
        <!-- Import from Question Bank Card -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-header py-3 bg-white border-bottom">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="ti ti-file-import text-primary me-1"></i> Impor Soal dari Bank Soal
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.quizzes.import-questions', $quiz) }}" method="POST">
                    @csrf
                    <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                        @forelse($questionBanks as $qb)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="question_bank_ids[]" value="{{ $qb->id }}" id="qb_{{ $qb->id }}">
                                <label class="form-check-label small" for="qb_{{ $qb->id }}">
                                    @if($qb->isMatching())
                                        <span class="badge bg-info-subtle text-info me-1" style="font-size: 0.65rem;">Menjodohkan</span>
                                    @elseif($qb->isTrueFalse())
                                        <span class="badge bg-warning-subtle text-warning-emphasis me-1" style="font-size: 0.65rem;">Benar/Salah</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary me-1" style="font-size: 0.65rem;">Pilgan</span>
                                    @endif
                                    {{ Str::limit($qb->question_text, 65) }}
                                </label>
                            </div>
                        @empty
                            <div class="small text-muted">Bank Soal masih kosong.</div>
                        @endforelse
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" {{ $questionBanks->isEmpty() ? 'disabled' : '' }}>
                        <i class="ti ti-file-import me-1"></i> Impor Soal Terpilih
                    </button>
                </form>
            </div>
        </div>

        <!-- Multi-Question Builder Card -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="ti ti-cards text-success me-1"></i> Buat Soal Kuis
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#modalQuickPaste">
                    <i class="ti ti-bolt text-warning"></i> Input Cepat (Teks)
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
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex align-items-center justify-content-center gap-1.5 fw-bold" onclick="addNewQuestionCard()">
                            <i class="ti ti-plus"></i> Tambah Butir Soal Lagi
                        </button>
                        <button type="submit" id="submitAllBtn" class="btn btn-success btn-sm w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                            <i class="ti ti-device-floppy"></i> <span id="submitBtnText">Simpan Semua Soal (1 Butir)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Paste (Input Cepat Soal dari Teks) -->
<div class="modal fade" id="modalQuickPaste" tabindex="-1" aria-labelledby="modalQuickPasteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalQuickPasteLabel">
                    <i class="ti ti-bolt text-warning fs-4"></i> Input Cepat Soal dari Teks (Salin-Tempel Massal)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-2">
                    Salin naskah soal Anda dari dokumen (Word / PDF / Notepad), lalu tempelkan di bawah ini. Sistem cerdas akan mendeteksi soal <strong>Pilihan Ganda</strong>, <strong>Benar/Salah</strong>, dan <strong>Menjodohkan</strong> sekaligus!
                </p>
                
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold small text-primary mb-0">Area Teks Naskah Soal:</label>
                    <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 small" onclick="loadSampleText()">
                        <i class="ti ti-file-text me-1"></i> Muat Contoh Format
                    </button>
                </div>
                <textarea id="quickPasteTextarea" class="form-control font-monospace" rows="10" placeholder="1. Apa ibukota negara Indonesia?&#10;A. Jakarta&#10;B. Bandung&#10;C. Surabaya&#10;D. Medan&#10;Kunci: A&#10;&#10;2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.&#10;Kunci: Benar&#10;&#10;3. Jodohkan bahasa pemrograman dengan ekstensinya:&#10;PHP = .php&#10;Python = .py&#10;JavaScript = .js"></textarea>

                <div class="alert alert-light border small text-muted mt-3 mb-0 p-2.5 rounded-3">
                    <div class="fw-bold text-dark mb-1"><i class="ti ti-info-circle text-primary me-1"></i> Panduan Format:</div>
                    <ul class="mb-0 ps-3">
                        <li><strong>Pilihan Ganda:</strong> Diawali nomor, baris opsi diawali <code>A.</code>, <code>B.</code>, dst, dan baris kunci <code>Kunci: A</code> atau <code>Jawaban: A</code>.</li>
                        <li><strong>Benar / Salah:</strong> Pertanyaan/pernyataan diikuti baris <code>Kunci: Benar</code> atau <code>Kunci: Salah</code>.</li>
                        <li><strong>Menjodohkan:</strong> Pernyataan diikuti baris pasangan dengan tanda <code>=</code> (contoh: <code>Indonesia = Jakarta</code>).</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-2.5">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="parseAndInsertQuestions()">
                    <i class="ti ti-sparkles me-1"></i> Konversi &amp; Masukkan ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Saved Quiz Question Confirmation -->
<div class="modal fade" id="modalDeleteQuizQuestion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="ti ti-trash fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Hapus Soal Kuis Ini?</h5>
            <p class="text-muted small mb-4" id="deleteModalQuestionText">Soal akan dihapus dari kuis ini.</p>
            <form id="deleteQuizQuestionForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                        <i class="ti ti-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Builder Card Confirmation -->
<div class="modal fade" id="modalConfirmDeleteQuizCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="ti ti-trash fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Hapus Kartu Soal Ini?</h5>
            <p class="text-muted small mb-4">Kartu butir soal ini akan dihapus dari form pembuatan.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmDeleteQuizCard" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="ti ti-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alert Info -->
<div class="modal fade" id="modalAlertQuiz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="ti ti-alert-triangle fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1" id="modalAlertQuizTitle">Pemberitahuan</h5>
            <p class="text-muted small mb-4" id="modalAlertQuizMsg">Minimal harus ada 1 butir soal dalam form.</p>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Mengerti</button>
        </div>
    </div>
</div>

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
        card.className = 'question-builder-item border rounded-3 p-3 bg-light position-relative shadow-2xs overflow-hidden';
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
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="questions[${index}][correct_option]" value="${i}" ${checked} title="Tandai sebagai kunci jawaban">
                    </div>
                    <input type="text" name="questions[${index}][options][]" class="form-control form-control-sm bg-white" placeholder="${placeholder}" value="${escapeHtml(val)}" ${isReq}>
                </div>
            `;
        }

        let pairsHtml = '';
        pairs.forEach((p, pIdx) => {
            pairsHtml += `
                <div class="row g-1 align-items-center mb-1.5 pair-row">
                    <div class="col-6">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][premise]" class="form-control form-control-sm bg-white" placeholder="Premis ${pIdx + 1}" value="${escapeHtml(p.premise || '')}">
                    </div>
                    <div class="col-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][match]" class="form-control form-control-sm bg-white" placeholder="Pasangan ${pIdx + 1}" value="${escapeHtml(p.match || '')}">
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
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-bold q-num-badge">Soal #1</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-q-btn" onclick="requestRemoveQuestionCard(${index})" title="Hapus butir soal ini">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Format Selector (Responsive Flex-Wrap) -->
            <div class="mb-2">
                <div class="d-flex flex-wrap gap-1" role="group">
                    <div class="flex-fill" style="min-width: 80px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_mc" value="multiple_choice" ${qType === 'multiple_choice' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'multiple_choice')">
                        <label class="btn btn-sm btn-outline-primary w-100 text-nowrap py-1 px-1.5" for="qtype_${index}_mc"><i class="ti ti-list-check me-1"></i>Pilgan</label>
                    </div>
                    <div class="flex-fill" style="min-width: 95px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_tf" value="true_false" ${qType === 'true_false' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'true_false')">
                        <label class="btn btn-sm btn-outline-warning text-dark w-100 text-nowrap py-1 px-1.5" for="qtype_${index}_tf"><i class="ti ti-checkup-list me-1"></i>Benar/Salah</label>
                    </div>
                    <div class="flex-fill" style="min-width: 100px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qtype_${index}_match" value="matching" ${qType === 'matching' ? 'checked' : ''} onchange="handleQuizFormatChange(${index}, 'matching')">
                        <label class="btn btn-sm btn-outline-info text-dark w-100 text-nowrap py-1 px-1.5" for="qtype_${index}_match"><i class="ti ti-arrows-left-right me-1"></i>Menjodohkan</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-2 ${qType === 'matching' ? 'd-none' : ''}" id="quiz_sec_qtext_${index}">
                <textarea name="questions[${index}][question_text]" class="form-control form-control-sm bg-white" rows="2" placeholder="Tuliskan pertanyaan soal..." ${qType === 'matching' ? '' : 'required'}>${escapeHtml(qText)}</textarea>
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
                <div class="d-flex gap-2">
                    <div class="form-check form-check-inline p-2 border rounded bg-white flex-fill text-center">
                        <input class="form-check-input" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                        <label class="form-check-label fw-bold text-success small" for="q${index}_tf_true">Benar (True)</label>
                    </div>
                    <div class="form-check form-check-inline p-2 border rounded bg-white flex-fill text-center">
                        <input class="form-check-input" type="radio" name="questions[${index}][correct_tf]" id="q${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                        <label class="form-check-label fw-bold text-danger small" for="q${index}_tf_false">Salah (False)</label>
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
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][premise]" class="form-control form-control-sm bg-white" placeholder="Premis ${currentCount + 1}" required>
            </div>
            <div class="col-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][match]" class="form-control form-control-sm bg-white" placeholder="Pasangan ${currentCount + 1}" required>
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
