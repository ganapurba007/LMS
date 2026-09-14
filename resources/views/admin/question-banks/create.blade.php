@extends('layouts.be.master')

@section('header_title', 'Tambah Soal Ke Bank Soal')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Tambah Soal ke Bank Soal</h3>
        <p class="text-muted small mb-0">Buat satu atau beberapa butir soal sekaligus, atau gunakan fitur input cepat teks.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalQuickPaste">
            <i class="ti ti-bolt text-warning"></i> Input Cepat (Teks)
        </button>
        <a href="{{ route('admin.question-banks.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

            <!-- Header Card -->
            <div class="card-header bg-white border-bottom py-3 py-md-4 px-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 44px; height: 44px; background: rgba(49, 101, 155, 0.1);">
                            <i class="ti ti-database-edit fs-4" style="color: #31659B;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Form Pembuatan Soal Bank Soal</h5>
                            <p class="text-muted mb-0 small">Pilih tipe format soal (Pilihan Ganda, Benar/Salah, Menjodohkan) dan isi dengan mudah</p>
                        </div>
                    </div>
                    <span id="qbQuestionCounterBadge" class="badge rounded-pill text-nowrap align-self-start align-self-sm-center px-3 py-2"
                          style="background: rgba(49, 101, 155, 0.1); color: #31659B; font-size: 0.75rem;">
                        <i class="ti ti-list-check me-1"></i> 1 Butir Soal
                    </span>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <form method="POST" action="{{ route('admin.question-banks.store') }}" id="batchQbForm">
                    @csrf

                    <!-- Info hint -->
                    <div class="alert border-0 rounded-3 d-flex align-items-start gap-2 mb-4" style="background: rgba(49, 101, 155, 0.07);">
                        <i class="ti ti-info-circle mt-1" style="color: #31659B;"></i>
                        <div class="small text-muted mb-0">
                            Isi setiap butir soal dengan lengkap. Anda bisa berpindah tipe format soal atau menambah butir soal baru, dan seluruh soal akan <strong>tersimpan otomatis sekaligus</strong> saat mengklik Simpan.
                        </div>
                    </div>

                    <!-- Container for question cards -->
                    <div id="qbQuestionsContainer" class="d-flex flex-column gap-3 mb-4">
                        <!-- Rendered by JavaScript -->
                    </div>

                    <!-- Tombol tambah soal (full width, dashed style agar terlihat sebagai "area tambah") -->
                    <button type="button"
                            class="btn btn-outline-primary btn-sm w-100 py-2.5 mb-4 d-flex align-items-center justify-content-center gap-2 rounded-3 fw-bold"
                            style="border-style: dashed;"
                            onclick="addNewQbQuestionCard()">
                        <i class="ti ti-plus"></i> Tambah Butir Soal Lagi
                    </button>

                    <!-- Action buttons -->
                    <div class="d-flex flex-column-reverse flex-md-row justify-content-md-end align-items-stretch align-items-md-center gap-2 pt-3 border-top position-sticky bottom-0 bg-white"
                         style="z-index: 5;">
                        <a href="{{ route('admin.question-banks.index') }}" class="btn btn-light btn-sm px-4 py-2 rounded-3 order-2 order-md-1">
                            Batal
                        </a>
                        <button type="submit" id="submitQbAllBtn"
                                class="btn btn-primary btn-sm px-4 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 rounded-3 order-1 order-md-2">
                            <i class="ti ti-device-floppy"></i>
                            <span id="submitQbBtnText">Simpan Semua Soal (1 Butir)</span>
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
                    <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 small" onclick="loadSampleQbText()">
                        <i class="ti ti-file-text me-1"></i> Muat Contoh Format
                    </button>
                </div>
                <textarea id="quickPasteQbTextarea" class="form-control font-monospace" rows="10" placeholder="1. Apa ibukota negara Indonesia?&#10;A. Jakarta&#10;B. Bandung&#10;C. Surabaya&#10;D. Medan&#10;Kunci: A&#10;&#10;2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.&#10;Kunci: Benar&#10;&#10;3. Jodohkan bahasa pemrograman dengan ekstensinya:&#10;PHP = .php&#10;Python = .py&#10;JavaScript = .js"></textarea>

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
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="parseAndInsertQbQuestions()">
                    <i class="ti ti-sparkles me-1"></i> Konversi &amp; Masukkan ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom Delete Card Confirmation -->
<div class="modal fade" id="modalConfirmDeleteCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="ti ti-trash fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Hapus Kartu Soal Ini?</h5>
            <p class="text-muted small mb-4">Kartu butir soal ini akan dihapus dari form pembuatan.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmDeleteCard" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="ti ti-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Custom Alert Info -->
<div class="modal fade" id="modalAlertQb" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="ti ti-alert-triangle fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1" id="modalAlertTitle">Pemberitahuan</h5>
            <p class="text-muted small mb-4" id="modalAlertMsg">Minimal harus ada 1 butir soal dalam form.</p>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Mengerti</button>
        </div>
    </div>
</div>

<script>
    let qbQuestionCounter = 0;
    let pendingDeleteIndex = null;

    function renderQbQuestionCard(index, data = null) {
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
        card.className = 'qb-builder-item border rounded-3 p-3 bg-light position-relative shadow-2xs overflow-hidden';
        card.id = `qbCard_${index}`;
        card.setAttribute('data-q-idx', index);
        card.setAttribute('data-current-type', qType);

        let optionsHtml = '';
        for (let i = 0; i < 4; i++) {
            const letter = String.fromCharCode(65 + i);
            const val = options[i] || '';
            const checked = (correctOpt === i) ? 'checked' : '';
            optionsHtml += `
                <div class="input-group input-group-sm mb-1.5">
                    <div class="input-group-text bg-white">
                        <input class="form-check-input mt-0" type="radio" name="questions[${index}][correct_option]" value="${i}" ${checked} title="Tandai sebagai kunci jawaban">
                    </div>
                    <input type="text" name="questions[${index}][options][]" class="form-control form-control-sm bg-white" placeholder="Opsi ${letter}" value="${escapeHtml(val)}" ${i < 2 ? 'required' : ''}>
                </div>
            `;
        }

        let pairsHtml = '';
        pairs.forEach((p, pIdx) => {
            pairsHtml += `
                <div class="row g-2 align-items-center mb-2 qb-pair-row">
                    <div class="col-12 col-md-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][premise]" class="form-control form-control-sm bg-white" placeholder="Soal / Premis ${pIdx + 1}" value="${escapeHtml(p.premise || '')}">
                    </div>
                    <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                        <i class="ti ti-arrow-right fs-5"></i>
                    </div>
                    <div class="col-10 col-md-5">
                        <input type="text" name="questions[${index}][pairs][${pIdx}][match]" class="form-control form-control-sm bg-white" placeholder="Pasangan Jawaban ${pIdx + 1}" value="${escapeHtml(p.match || '')}">
                    </div>
                    <div class="col-2 col-md-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeQbPairRow(this)" title="Hapus baris pasangan ini">
                            <i class="ti ti-x fs-6"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-1 fw-bold qb-num-badge">Soal #1</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-qb-btn" onclick="requestRemoveQbQuestionCard(${index})" title="Hapus butir soal ini">
                        <i class="ti ti-trash fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Format Selector (Responsive Flex-Wrap) -->
            <div class="mb-2">
                <div class="d-flex flex-wrap gap-1" role="group">
                    <div class="flex-fill" style="min-width: 100px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qbtype_${index}_mc" value="multiple_choice" ${qType === 'multiple_choice' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'multiple_choice')">
                        <label class="btn btn-sm btn-outline-primary w-100 text-nowrap py-1.5 px-2" for="qbtype_${index}_mc"><i class="ti ti-list-check me-1"></i>Pilihan Ganda</label>
                    </div>
                    <div class="flex-fill" style="min-width: 100px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qbtype_${index}_tf" value="true_false" ${qType === 'true_false' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'true_false')">
                        <label class="btn btn-sm btn-outline-warning text-dark w-100 text-nowrap py-1.5 px-2" for="qbtype_${index}_tf"><i class="ti ti-checkup-list me-1"></i>Benar/Salah</label>
                    </div>
                    <div class="flex-fill" style="min-width: 100px;">
                        <input type="radio" class="btn-check" name="questions[${index}][question_type]" id="qbtype_${index}_match" value="matching" ${qType === 'matching' ? 'checked' : ''} onchange="handleQbFormatChange(${index}, 'matching')">
                        <label class="btn btn-sm btn-outline-info text-dark w-100 text-nowrap py-1.5 px-2" for="qbtype_${index}_match"><i class="ti ti-arrows-left-right me-1"></i>Menjodohkan</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan -->
            <div class="mb-2 ${qType === 'matching' ? 'd-none' : ''}" id="qb_sec_qtext_${index}">
                <label class="form-label small fw-semibold text-dark mb-1">Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
                <textarea name="questions[${index}][question_text]" class="form-control form-control-sm bg-white" rows="2" placeholder="Tuliskan pertanyaan atau instruksi soal di sini..." ${qType === 'matching' ? '' : 'required'}>${escapeHtml(qText)}</textarea>
            </div>

            <!-- Section MC -->
            <div class="sec-mc ${qType === 'multiple_choice' ? '' : 'd-none'}" id="qb_sec_mc_${index}">
                <label class="form-label small fw-semibold text-muted mb-1">Opsi Pilihan (Pilih Radio Kunci Jawaban Benar):</label>
                <div class="options-container">
                    ${optionsHtml}
                </div>
            </div>

            <!-- Section TF -->
            <div class="sec-tf ${qType === 'true_false' ? '' : 'd-none'}" id="qb_sec_tf_${index}">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Kunci Jawaban yang Benar:</label>
                <div class="d-flex gap-2">
                    <div class="form-check form-check-inline p-2 border rounded bg-white flex-fill text-center">
                        <input class="form-check-input" type="radio" name="questions[${index}][correct_tf]" id="qb${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                        <label class="form-check-label fw-bold text-success small" for="qb${index}_tf_true">Benar (True)</label>
                    </div>
                    <div class="form-check form-check-inline p-2 border rounded bg-white flex-fill text-center">
                        <input class="form-check-input" type="radio" name="questions[${index}][correct_tf]" id="qb${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                        <label class="form-check-label fw-bold text-danger small" for="qb${index}_tf_false">Salah (False)</label>
                    </div>
                </div>
            </div>

            <!-- Section Matching -->
            <div class="sec-matching ${qType === 'matching' ? '' : 'd-none'}" id="qb_sec_matching_${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label small fw-semibold text-dark mb-0"><i class="ti ti-arrows-left-right text-info me-1"></i> Soal &amp; Pasangan Jawaban:</label>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold" onclick="addQbPairToCard(${index})">
                        <i class="ti ti-plus"></i> Tambah Pasangan
                    </button>
                </div>
                <div class="row g-2 text-muted small fw-semibold mb-1 d-none d-md-flex px-1">
                    <div class="col-md-5">Soal / Premis</div>
                    <div class="col-md-1 text-center"></div>
                    <div class="col-md-5">Pasangan Jawaban Benar</div>
                    <div class="col-md-1"></div>
                </div>
                <div class="pairs-container" id="qb_pairs_container_${index}">
                    ${pairsHtml}
                </div>
            </div>
        `;

        return card;
    }

    // Smart Format Switcher & Auto-Spawn logic
    function handleQbFormatChange(index, newType) {
        const card = document.getElementById(`qbCard_${index}`);
        if (!card) return;

        const oldType = card.getAttribute('data-current-type') || 'multiple_choice';

        // Check if current card has content typed
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
            // Revert radio check on card index to oldType
            const oldRadio = card.querySelector(`input[value="${oldType}"]`);
            if (oldRadio) oldRadio.checked = true;

            // Automatically spawn a new card with newType so both questions are preserved and saved!
            addNewQbQuestionCard({ question_type: newType });

            // Scroll smoothly to newly created card
            const newCards = document.querySelectorAll('.qb-builder-item');
            const lastCard = newCards[newCards.length - 1];
            if (lastCard) {
                lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Empty card, simply switch form visibility
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
        qbQuestionCounter++;
        updateQbQuestionNumbers();
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

    document.addEventListener('DOMContentLoaded', function() {
        addNewQbQuestionCard();

        document.getElementById('btnConfirmDeleteCard')?.addEventListener('click', function() {
            if (pendingDeleteIndex !== null) {
                const card = document.getElementById(`qbCard_${pendingDeleteIndex}`);
                if (card) {
                    card.remove();
                    updateQbQuestionNumbers();
                }
                pendingDeleteIndex = null;
            }
            const modalEl = document.getElementById('modalConfirmDeleteCard');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });
    });

    function addQbPairToCard(qIndex) {
        const container = document.getElementById(`qb_pairs_container_${qIndex}`);
        const currentCount = container.querySelectorAll('.qb-pair-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 qb-pair-row';
        row.innerHTML = `
            <div class="col-12 col-md-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][premise]" class="form-control form-control-sm bg-white" placeholder="Soal / Premis ${currentCount + 1}" required>
            </div>
            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                <i class="ti ti-arrow-right fs-5"></i>
            </div>
            <div class="col-10 col-md-5">
                <input type="text" name="questions[${qIndex}][pairs][${currentCount}][match]" class="form-control form-control-sm bg-white" placeholder="Pasangan Jawaban ${currentCount + 1}" required>
            </div>
            <div class="col-2 col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeQbPairRow(this)" title="Hapus baris pasangan ini">
                    <i class="ti ti-x fs-6"></i>
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
                delBtn.style.display = (cards.length > 1) ? 'inline-block' : 'none';
            }
        });

        const submitText = document.getElementById('submitQbBtnText');
        if (submitText) {
            submitText.innerText = `Simpan Semua Soal (${cards.length} Butir)`;
        }

        const counterBadge = document.getElementById('qbQuestionCounterBadge');
        if (counterBadge) {
            counterBadge.innerHTML = `<i class="ti ti-list-check me-1"></i> ${cards.length} Butir Soal`;
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
    function loadSampleQbText() {
        const sample = `1. Apa ibukota negara Indonesia saat ini?\nA. Jakarta\nB. Surabaya\nC. Bandung\nD. Medan\nKunci: A\n\n2. Bumi mengelilingi matahari dalam kurun waktu 1 tahun.\nKunci: Benar\n\n3. Jodohkan bahasa pemrograman dengan ekstensinya:\nPHP = .php\nPython = .py\nJavaScript = .js`;
        document.getElementById('quickPasteQbTextarea').value = sample;
    }

    function parseAndInsertQbQuestions() {
        const text = document.getElementById('quickPasteQbTextarea').value.trim();
        if (!text) {
            showQbModalAlert('Perhatian', 'Silakan tempelkan naskah soal terlebih dahulu.');
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
            showQbModalAlert('Format Salah', 'Format soal tidak terdeteksi. Pastikan setiap butir soal diawali angka nomor soal (misal: 1. Pertanyaan).');
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

        const container = document.getElementById('qbQuestionsContainer');
        container.innerHTML = '';
        qbQuestionCounter = 0;

        parsedQuestions.forEach(q => {
            addNewQbQuestionCard(q);
        });

        const modalEl = document.getElementById('modalQuickPaste');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        document.getElementById('batchQbForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>
@endsection
