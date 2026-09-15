@extends('layouts.be.master')
@section('header_title', 'Master Data — Tambah Bank Soal')

@section('content')

{{-- Page Header --}}
<div class="col-md-12">
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                <i class="ti ti-database-plus"></i>
            </div>
            <div>
                <h5 class="md-title">Tambah Bank Soal</h5>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="md-btn-secondary" data-bs-toggle="modal" data-bs-target="#modalQuickPaste" style="color:#d97706;border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.05);">
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
                <span id="qbQuestionCounterBadge" class="md-badge" style="background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.2);font-weight:700;">
                    <i class="ti ti-list-check"></i> 1 Butir Soal
                </span>
            </div>
        </div>

        <div class="md-form-body">
            <div class="alert border-0 rounded-3 d-flex align-items-start gap-2 mb-4 p-3" style="background:rgba(8,145,178,.06);color:var(--tblr-heading-color,#0f172a);font-size:.82rem;">
                <i class="ti ti-info-circle fs-5" style="color:#0891b2;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    Pilih tipe format soal untuk tiap butir. Anda dapat membuat lebih dari satu soal sekaligus dan menekan <strong>Simpan Semua Soal</strong> ketika selesai.
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

<!-- Modal Quick Paste -->
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
                Tempelkan naskah soal Anda (Word/PDF/Notepad). Sistem akan otomatis mengenali format Pilihan Ganda, Benar/Salah, dan Menjodohkan.
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

<!-- Modal Custom Delete Card Confirmation -->
<div class="modal fade" id="modalConfirmDeleteCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Butir Soal Ini?</h6>
            <p class="md-modal-text">Butir soal ini akan dihapus dari daftar form pembuatan.</p>
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
.qb-builder-item {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 10px;
    padding: 1.15rem;
    transition: all .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.02);
}
.qb-builder-item:hover {
    border-color: rgba(8,145,178,.4);
    box-shadow: 0 4px 12px rgba(8,145,178,.05);
}
.qb-btn-add-more {
    width: 100%;
    padding: .75rem 1rem;
    background: transparent;
    border: 1.5px dashed rgba(8,145,178,.35);
    border-radius: 8px;
    color: #0891b2;
    font-weight: 700;
    font-size: .82rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    cursor: pointer;
    transition: all .18s ease;
}
.qb-btn-add-more:hover {
    background: rgba(8,145,178,.05);
    border-color: #0891b2;
    color: #0891b2;
}
.qb-format-pills {
    display: flex;
    flex-wrap: wrap;
    gap: .35rem;
}
.qb-format-pill {
    flex: 1;
    min-width: 110px;
}
.qb-format-pill input {
    display: none;
}
.qb-format-pill label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    width: 100%;
    padding: .4rem .6rem;
    border-radius: 6px;
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    background: var(--tblr-body-bg, #f8fafc);
    color: var(--tblr-text-muted, #64748b);
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
    margin: 0;
}
.qb-format-pill input:checked + label {
    background: rgba(8,145,178,.1);
    border-color: #0891b2;
    color: #0891b2;
    font-weight: 700;
}
.qb-option-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    background: var(--tblr-body-bg, #f8fafc);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 7px;
    padding: .35rem .6rem;
    transition: border-color .15s ease;
}
.qb-option-row:focus-within {
    border-color: #0891b2;
    background: #fff;
}
.qb-opt-radio-wrap {
    display: flex;
    align-items: center;
    gap: .3rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--tblr-text-muted, #64748b);
    cursor: pointer;
    flex-shrink: 0;
}
.qb-opt-radio-wrap input:checked ~ span {
    color: #0ca678;
}
.qb-option-input {
    border: none !important;
    background: transparent !important;
    padding: .25rem .3rem !important;
    font-size: .8rem !important;
    box-shadow: none !important;
    outline: none !important;
    flex: 1;
}
.qb-tf-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    padding: .6rem;
    border: 1.5px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 8px;
    background: var(--tblr-body-bg, #f8fafc);
    cursor: pointer;
    font-size: .8rem;
    font-weight: 700;
    transition: all .15s ease;
}
.qb-tf-box input:checked + label {
    color: inherit;
}
.qb-tf-wrap input:checked + .qb-tf-true {
    border-color: #0ca678;
    background: rgba(12,166,120,.08);
    color: #0ca678;
}
.qb-tf-wrap input:checked + .qb-tf-false {
    border-color: #ef4444;
    background: rgba(239,68,68,.08);
    color: #ef4444;
}
.qb-pair-item {
    display: flex;
    align-items: center;
    gap: .4rem;
}
</style>

<script>
    let qbQuestionCounter = 0;
    let pendingDeleteIndex = null;

    function renderQbQuestionCard(index, data = null) {
        const qText = data ? (data.question_text || '') : '';
        const qType = data ? (data.question_type || 'multiple_choice') : 'multiple_choice';
        const options = data ? (data.options || ['', '', '', '']) : ['', '', '', ''];
        const correctOpt = data ? (data.correct_option !== undefined ? data.correct_option : 0) : 0;
        const correctTf = data ? (data.correct_tf || 'Benar') : 'Benar';
        const pairs = data ? (data.pairs || [{premise: '', match: ''}, {premise: '', match: ''}]) : [{premise: '', match: ''}, {premise: '', match: ''}];

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
                <label class="md-form-label mb-1">Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
                <textarea name="questions[${index}][question_text]" class="form-control" rows="2" placeholder="Tuliskan pertanyaan atau instruksi soal..." ${qType === 'matching' ? '' : 'required'}>${escapeHtml(qText)}</textarea>
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
                <label class="md-form-label mb-2">Kunci Jawaban Pernyataan:</label>
                <div class="row g-2">
                    <div class="col-6 qb-tf-wrap">
                        <input type="radio" class="d-none" name="questions[${index}][correct_tf]" id="qb${index}_tf_true" value="Benar" ${correctTf === 'Benar' ? 'checked' : ''}>
                        <label class="qb-tf-box qb-tf-true w-100" for="qb${index}_tf_true">
                            <i class="ti ti-check"></i> Benar (True)
                        </label>
                    </div>
                    <div class="col-6 qb-tf-wrap">
                        <input type="radio" class="d-none" name="questions[${index}][correct_tf]" id="qb${index}_tf_false" value="Salah" ${correctTf === 'Salah' ? 'checked' : ''}>
                        <label class="qb-tf-box qb-tf-false w-100" for="qb${index}_tf_false">
                            <i class="ti ti-x"></i> Salah (False)
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

    function handleQbFormatChange(index, newType) {
        const card = document.getElementById(`qbCard_${index}`);
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
                // true_false
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
