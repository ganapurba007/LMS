@extends('layouts.be.master')
@section('header_title', 'Master Data — Edit Bank Soal')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Bank Soal</h5>
        </div>
    </div>
    <a href="{{ route('admin.question-banks.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> <span>Kembali</span>
    </a>
</div>

{{-- Flash Alert --}}
@if(session('error'))
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-database-edit"></i>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h6 class="md-form-head-title mb-0">Informasi Soal</h6>
            <span class="md-badge blue">
                {{ strtoupper(str_replace('_', ' ', $questionBank->question_type ?? 'multiple_choice')) }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.question-banks.update', $questionBank) }}" id="editQbForm">
        @csrf
        @method('PUT')

        <div class="md-form-body">
            <!-- Format Question Type Switcher -->
            <div class="mb-4">
                <label class="md-form-label">Format Tipe Soal <span class="text-danger">*</span></label>
                <div class="qb-format-pills">
                    <div class="qb-format-pill">
                        <input type="radio" name="question_type" id="edit_type_mc" value="multiple_choice" {{ old('question_type', $questionBank->question_type) === 'multiple_choice' ? 'checked' : '' }} onchange="switchEditType('multiple_choice')">
                        <label for="edit_type_mc"><i class="ti ti-list-check"></i> Pilihan Ganda</label>
                    </div>

                    <div class="qb-format-pill">
                        <input type="radio" name="question_type" id="edit_type_tf" value="true_false" {{ old('question_type', $questionBank->question_type) === 'true_false' ? 'checked' : '' }} onchange="switchEditType('true_false')">
                        <label for="edit_type_tf"><i class="ti ti-checkup-list"></i> Benar / Salah</label>
                    </div>

                    <div class="qb-format-pill">
                        <input type="radio" name="question_type" id="edit_type_match" value="matching" {{ old('question_type', $questionBank->question_type) === 'matching' ? 'checked' : '' }} onchange="switchEditType('matching')">
                        <label for="edit_type_match"><i class="ti ti-arrows-left-right"></i> Menjodohkan</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan Soal -->
            <div class="mb-4 {{ old('question_type', $questionBank->question_type) === 'matching' ? 'd-none' : '' }}" id="sec_edit_qtext">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1.5 qb-question-header">
                    <label for="question_text" class="md-form-label mb-0">Teks Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
                    <div class="qb-toolbar-wrap">
                        <button type="button" class="qb-toolbar-btn" onclick="openInsertImageModalEdit()" title="Lampirkan Gambar">
                            <i class="ti ti-photo text-primary"></i> <span>Gambar</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="openVisualTableOrAddEdit()" title="Buat / Edit Tabel Data">
                            <i class="ti ti-table text-success"></i> <span>Tabel</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="openInsertSymbolModalEdit()" title="Simbol Matematika / Sains">
                            <i class="ti ti-math-function text-warning"></i> <span>Simbol</span>
                        </button>
                        <button type="button" class="qb-toolbar-btn" onclick="toggleLivePreviewEdit()" title="Pratinjau Lengkap">
                            <i class="ti ti-eye text-info"></i> <span>Pratinjau</span>
                        </button>
                    </div>
                </div>

                <!-- Teks murni tanpa kode -->
                <textarea id="question_text" name="question_text" rows="3" class="form-control mb-2 @error('question_text') is-invalid @enderror" placeholder="Tuliskan pertanyaan atau instruksi..." {{ old('question_type', $questionBank->question_type) === 'matching' ? '' : 'required' }} oninput="handleTextareaInputEdit()">{{ old('question_text', $questionBank->question_text) }}</textarea>
                
                <!-- Container Lampiran Gambar -->
                <div id="edit_image_attachment_wrap" class="mb-2"></div>

                <!-- Container Editor Tabel Visual -->
                <div id="edit_table_editor_wrap" class="mb-2"></div>

                <!-- Live Preview Area -->
                <div id="edit_live_preview" class="qb-live-preview-box p-3 mt-2 d-none"></div>

                @error('question_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Section Pilihan Ganda -->
            @php
                $options = $questionBank->options->values();
                $defaultCorrect = $options->search(fn($o) => $o->is_correct);
                if ($defaultCorrect === false) { $defaultCorrect = 0; }
            @endphp
            <div id="sec_edit_mc" class="mb-4 {{ old('question_type', $questionBank->question_type) === 'multiple_choice' ? '' : 'd-none' }}">
                <label class="md-form-label mb-2">Pilihan Jawaban &amp; Kunci Jawaban <span class="text-danger">*</span></label>
                <div class="d-flex flex-column gap-2">
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $optVal = isset($options[$i]) ? $options[$i]->option_text : '';
                            $letter = chr(65 + $i);
                        @endphp
                        <div class="qb-option-row">
                            <label class="qb-opt-radio-wrap m-0">
                                <input type="radio" name="correct_option" id="correct_{{ $i }}" value="{{ $i }}" {{ old('correct_option', (string)$defaultCorrect) == (string)$i ? 'checked' : '' }} class="form-check-input m-0">
                                <span>{{ $letter }}</span>
                            </label>
                            <input type="text" name="options[{{ $i }}]" id="opt_input_{{ $i }}" class="qb-option-input @error('options.'.$i) is-invalid @enderror" value="{{ old('options.'.$i, $optVal) }}" placeholder="Tuliskan pilihan jawaban {{ $letter }}..." {{ $i < 2 ? 'required' : '' }}>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Section Benar / Salah -->
            @php
                $tfCorrect = 'Benar';
                if ($questionBank->isTrueFalse()) {
                    $correctOpt = $questionBank->options->firstWhere('is_correct', true);
                    if ($correctOpt && $correctOpt->option_text === 'Salah') {
                        $tfCorrect = 'Salah';
                    }
                }
            @endphp
            <div id="sec_edit_tf" class="mb-4 {{ old('question_type', $questionBank->question_type) === 'true_false' ? '' : 'd-none' }}">
                <label class="md-form-label mb-2">Pilih Kunci Jawaban Pernyataan <span class="text-danger">*</span></label>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-true w-100" for="edit_tf_true">
                            <input type="radio" class="form-check-input qb-tf-radio" name="correct_tf" id="edit_tf_true" value="Benar" {{ old('correct_tf', $tfCorrect) === 'Benar' ? 'checked' : '' }}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-check"></i></span>
                                <span class="qb-tf-title">Benar (True)</span>
                            </div>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="qb-tf-card qb-tf-card-false w-100" for="edit_tf_false">
                            <input type="radio" class="form-check-input qb-tf-radio" name="correct_tf" id="edit_tf_false" value="Salah" {{ old('correct_tf', $tfCorrect) === 'Salah' ? 'checked' : '' }}>
                            <div class="qb-tf-content">
                                <span class="qb-tf-icon"><i class="ti ti-circle-x"></i></span>
                                <span class="qb-tf-title">Salah (False)</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section Menjodohkan -->
            <div id="sec_edit_matching" class="mb-4 {{ old('question_type', $questionBank->question_type) === 'matching' ? '' : 'd-none' }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="md-form-label mb-0"><i class="ti ti-arrows-left-right me-1" style="color:#0891b2;"></i> Pasangan Soal &amp; Jawaban Benar <span class="text-danger">*</span></label>
                    <button type="button" class="md-btn-secondary" style="padding:.2rem .6rem;font-size:.72rem;" onclick="addEditPairRow()">
                        <i class="ti ti-plus"></i> Tambah Pasangan
                    </button>
                </div>

                <div id="edit_pairs_container" class="d-flex flex-column gap-2">
                    @if($questionBank->isMatching() && $options->count() >= 2)
                        @foreach($options as $pIdx => $pairOpt)
                            <div class="row g-2 align-items-center edit-pair-row">
                                <div class="col-12 col-md-5">
                                    <input type="text" name="pairs[{{ $pIdx }}][premise]" class="form-control form-control-sm edit-pair-input" placeholder="Soal / Premis {{ $pIdx + 1 }}" value="{{ old('pairs.'.$pIdx.'.premise', $pairOpt->option_text) }}">
                                </div>
                                <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                                    <i class="ti ti-arrow-right"></i>
                                </div>
                                <div class="col-10 col-md-5">
                                    <input type="text" name="pairs[{{ $pIdx }}][match]" class="form-control form-control-sm edit-pair-input" placeholder="Pasangan Jawaban {{ $pIdx + 1 }}" value="{{ old('pairs.'.$pIdx.'.match', $pairOpt->match_text) }}">
                                </div>
                                <div class="col-2 col-md-1 text-center">
                                    <button type="button" class="md-icon-btn red" onclick="removeEditPairRow(this)" title="Hapus baris">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Default 2 rows -->
                        <div class="row g-2 align-items-center edit-pair-row">
                            <div class="col-12 col-md-5">
                                <input type="text" name="pairs[0][premise]" class="form-control form-control-sm edit-pair-input" placeholder="Soal / Premis 1">
                            </div>
                            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                                <i class="ti ti-arrow-right"></i>
                            </div>
                            <div class="col-10 col-md-5">
                                <input type="text" name="pairs[0][match]" class="form-control form-control-sm edit-pair-input" placeholder="Pasangan Jawaban 1">
                            </div>
                            <div class="col-2 col-md-1 text-center">
                                <button type="button" class="md-icon-btn red" onclick="removeEditPairRow(this)" title="Hapus baris">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row g-2 align-items-center edit-pair-row">
                            <div class="col-12 col-md-5">
                                <input type="text" name="pairs[1][premise]" class="form-control form-control-sm edit-pair-input" placeholder="Soal / Premis 2">
                            </div>
                            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                                <i class="ti ti-arrow-right"></i>
                            </div>
                            <div class="col-10 col-md-5">
                                <input type="text" name="pairs[1][match]" class="form-control form-control-sm edit-pair-input" placeholder="Pasangan Jawaban 2">
                            </div>
                            <div class="col-2 col-md-1 text-center">
                                <button type="button" class="md-icon-btn red" onclick="removeEditPairRow(this)" title="Hapus baris">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.question-banks.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')

<style>
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
.qb-option-input::placeholder {
    color: #94a3b8 !important;
    opacity: 1;
}

/* Form Controls & Textareas */
.md-form-card textarea.form-control,
.md-form-card input.form-control,
.edit-pair-input {
    border: 1.5px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    color: #0f172a !important;
    font-size: .84rem !important;
    border-radius: 8px !important;
}
.md-form-card textarea.form-control:focus,
.md-form-card input.form-control:focus,
.edit-pair-input:focus {
    border-color: #0284c7 !important;
    background-color: #ffffff !important;
    color: #0f172a !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}
.md-form-card textarea.form-control::placeholder,
.md-form-card input.form-control::placeholder,
.edit-pair-input::placeholder {
    color: #94a3b8 !important;
    opacity: 1;
}

/* True / False Boxes */
.qb-tf-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .75rem 1rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
    cursor: pointer;
    font-size: .84rem;
    font-weight: 700;
    color: #334155;
    transition: all .15s ease;
}
.qb-tf-box:hover {
    border-color: #94a3b8;
    background: #ffffff;
    color: #0f172a;
}
.qb-tf-wrap input:checked + .qb-tf-true {
    border-color: #059669;
    background: #ecfdf5;
    color: #047857;
    box-shadow: 0 2px 6px rgba(5, 150, 105, 0.15);
}
.qb-tf-wrap input:checked + .qb-tf-false {
    border-color: #dc2626;
    background: #fef2f2;
    color: #b91c1c;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
}

/* ============================================================
   DARK MODE OVERRIDES
   ============================================================ */
[data-theme="dark"] .qb-format-pill label {
    border-color: #334155;
    background: rgba(15, 23, 42, 0.65);
    color: #94a3b8;
}
[data-theme="dark"] .qb-format-pill label:hover {
    background: rgba(15, 23, 42, 0.9);
    border-color: #475569;
    color: #f1f5f9;
}
[data-theme="dark"] .qb-format-pill input:checked + label {
    background: rgba(56, 189, 248, 0.18);
    border-color: #38bdf8;
    color: #38bdf8;
    box-shadow: 0 2px 8px rgba(56, 189, 248, 0.2);
}

[data-theme="dark"] .qb-option-row {
    background: rgba(15, 23, 42, 0.65);
    border-color: #334155;
}
[data-theme="dark"] .qb-option-row:hover {
    border-color: #475569;
    background: rgba(15, 23, 42, 0.85);
}
[data-theme="dark"] .qb-option-row:focus-within {
    border-color: #38bdf8;
    background: rgba(15, 23, 42, 0.95);
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
}
[data-theme="dark"] .qb-opt-radio-wrap {
    color: #94a3b8;
}
[data-theme="dark"] .qb-opt-radio-wrap input:checked ~ span {
    color: #34d399;
}
[data-theme="dark"] .qb-option-input {
    color: #f8fafc !important;
}
[data-theme="dark"] .qb-option-input::placeholder {
    color: #64748b !important;
}

[data-theme="dark"] .md-form-card textarea.form-control,
[data-theme="dark"] .md-form-card input.form-control,
[data-theme="dark"] .edit-pair-input {
    border-color: #334155 !important;
    background-color: rgba(15, 23, 42, 0.75) !important;
    color: #f8fafc !important;
}
[data-theme="dark"] .md-form-card textarea.form-control:focus,
[data-theme="dark"] .md-form-card input.form-control:focus,
[data-theme="dark"] .edit-pair-input:focus {
    border-color: #38bdf8 !important;
    background-color: rgba(15, 23, 42, 0.95) !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
}
[data-theme="dark"] .md-form-card textarea.form-control::placeholder,
[data-theme="dark"] .md-form-card input.form-control::placeholder,
[data-theme="dark"] .edit-pair-input::placeholder {
    color: #64748b !important;
}

[data-theme="dark"] .qb-tf-box {
    border-color: #334155;
    background: rgba(15, 23, 42, 0.65);
    color: #cbd5e1;
}
[data-theme="dark"] .qb-tf-box:hover {
    border-color: #475569;
    background: rgba(15, 23, 42, 0.9);
    color: #f8fafc;
}
[data-theme="dark"] .qb-tf-wrap input:checked + .qb-tf-true {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.18);
    color: #34d399;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}
[data-theme="dark"] .qb-tf-wrap input:checked + .qb-tf-false {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.18);
    color: #f87171;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
}
</style>

<script>
    function switchEditType(type) {
        const secMc = document.getElementById('sec_edit_mc');
        const secTf = document.getElementById('sec_edit_tf');
        const secMatch = document.getElementById('sec_edit_matching');
        const secQtext = document.getElementById('sec_edit_qtext');
        const qTextArea = document.getElementById('question_text');
        const mcInput0 = document.getElementById('opt_input_0');
        const mcInput1 = document.getElementById('opt_input_1');
        const pairInputs = document.querySelectorAll('.edit-pair-input');

        if (type === 'matching') {
            if (secQtext) secQtext.classList.add('d-none');
            if (qTextArea) qTextArea.removeAttribute('required');
        } else {
            if (secQtext) secQtext.classList.remove('d-none');
            if (qTextArea) qTextArea.setAttribute('required', 'required');
        }

        if (type === 'true_false') {
            secMc.classList.add('d-none');
            secTf.classList.remove('d-none');
            secMatch.classList.add('d-none');
            if (mcInput0) mcInput0.removeAttribute('required');
            if (mcInput1) mcInput1.removeAttribute('required');
            pairInputs.forEach(i => i.removeAttribute('required'));
        } else if (type === 'matching') {
            secMc.classList.add('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.remove('d-none');
            if (mcInput0) mcInput0.removeAttribute('required');
            if (mcInput1) mcInput1.removeAttribute('required');
            pairInputs.forEach(i => i.setAttribute('required', 'required'));
        } else {
            secMc.classList.remove('d-none');
            secTf.classList.add('d-none');
            secMatch.classList.add('d-none');
            if (mcInput0) mcInput0.setAttribute('required', 'required');
            if (mcInput1) mcInput1.setAttribute('required', 'required');
            pairInputs.forEach(i => i.removeAttribute('required'));
        }
    }

    function addEditPairRow() {
        const container = document.getElementById('edit_pairs_container');
        const count = container.querySelectorAll('.edit-pair-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center edit-pair-row';
        row.innerHTML = `
            <div class="col-12 col-md-5">
                <input type="text" name="pairs[${count}][premise]" class="form-control form-control-sm edit-pair-input" placeholder="Soal / Premis ${count + 1}" required>
            </div>
            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center" style="color:#0891b2;">
                <i class="ti ti-arrow-right"></i>
            </div>
            <div class="col-10 col-md-5">
                <input type="text" name="pairs[${count}][match]" class="form-control form-control-sm edit-pair-input" placeholder="Pasangan Jawaban ${count + 1}" required>
            </div>
            <div class="col-2 col-md-1 text-center">
                <button type="button" class="md-icon-btn red" onclick="removeEditPairRow(this)" title="Hapus baris">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    function removeEditPairRow(btn) {
        const container = document.getElementById('edit_pairs_container');
        const rows = container.querySelectorAll('.edit-pair-row');
        if (rows.length <= 2) {
            alert('Soal menjodohkan membutuhkan minimal 2 pasangan.');
            return;
        }
        btn.closest('.edit-pair-row').remove();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkedType = document.querySelector('input[name="question_type"]:checked');
        if (checkedType) {
            switchEditType(checkedType.value);
        }
    });
</script>

<!-- Modal Sisipkan Gambar (Edit) -->
<div class="modal fade" id="modalInsertImageEdit" tabindex="-1" aria-hidden="true">
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

            <ul class="nav nav-pills nav-fill mb-3" id="imageTabsEdit" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-1.5" style="font-size: .82rem;" id="tabUploadImgEdit" data-bs-toggle="pill" data-bs-target="#paneUploadImgEdit" type="button"><i class="ti ti-upload me-1"></i> Upload File</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-1.5" style="font-size: .82rem;" id="tabUrlImgEdit" data-bs-toggle="pill" data-bs-target="#paneUrlImgEdit" type="button"><i class="ti ti-link me-1"></i> URL Gambar</button>
                </li>
            </ul>

            <div class="tab-content mb-3">
                <!-- Tab Upload -->
                <div class="tab-pane fade show active" id="paneUploadImgEdit">
                    <label class="md-form-label mb-1">Pilih File Gambar (JPG, PNG, GIF, WebP)</label>
                    <input type="file" id="modalImgFileInputEdit" class="form-control form-control-sm mb-2" accept="image/*">
                    <div id="modalImgUploadProgressEdit" class="d-none text-center py-2">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <span class="small ms-1">Mengunggah gambar...</span>
                    </div>
                </div>
                <!-- Tab URL -->
                <div class="tab-pane fade" id="paneUrlImgEdit">
                    <label class="md-form-label mb-1">Tautan / URL Gambar</label>
                    <input type="url" id="modalImgUrlInputEdit" class="form-control form-control-sm mb-2" placeholder="https://example.com/gambar.png">
                </div>
            </div>

            <!-- Preview Image -->
            <div id="modalImgPreviewWrapEdit" class="p-2 word-preview-box rounded-3 text-center mb-3 d-none">
                <div class="text-muted small mb-1">Pratinjau Gambar:</div>
                <img id="modalImgPreviewElEdit" src="" alt="Pratinjau" style="max-height: 160px; max-width: 100%; border-radius: 6px;">
            </div>

            <div class="md-modal-actions">
                <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="md-btn-submit" id="btnConfirmInsertImageEdit" onclick="confirmInsertImageEdit()">
                    <i class="ti ti-check"></i> Pasang Gambar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Simbol Matematika (Edit) -->
<div class="modal fade" id="modalInsertSymbolEdit" tabindex="-1" aria-hidden="true">
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

            <div class="d-flex flex-wrap gap-1.5 mb-3">
                @php
                    $symbols = ['²', '³', '⁴', '√', '∛', 'π', '±', '÷', '×', '≤', '≥', '≠', '≈', '≡', '°', 'α', 'β', 'γ', 'θ', 'λ', 'μ', 'Σ', 'Δ', 'Ω', '½', '¼', '¾', '⅓', '⅔', '∞', '∫', '∂', '→', '↔', '∈', '∉', '⊂', '⊆', '∩', '∪'];
                @endphp
                @foreach($symbols as $sym)
                    <button type="button" class="symbol-btn" onclick="insertSymbolToEditCard('{{ $sym }}')">
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

<!-- Modal Sisipkan Tabel Edit (Rapi, Clean, Intuitif) -->
<div class="modal fade" id="modalInsertTableEdit" tabindex="-1" aria-hidden="true">
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
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInputEdit('edit_table_cols_input', -1)">−</button>
                        <input type="number" id="edit_table_cols_input" value="3" min="1" max="15" class="form-control form-control-sm text-center fw-bold text-primary" style="width:52px;font-size:1.05rem;" oninput="updateWordBadgeFromInputsEdit()">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInputEdit('edit_table_cols_input', 1)">+</button>
                    </div>
                </div>
                <div style="width:1px;height:46px;background:#cbd5e1;"></div>
                <div class="flex-fill text-center">
                    <span class="small fw-bold text-muted d-block mb-1" style="font-size:0.7rem;letter-spacing:.04em;">JUMLAH BARIS</span>
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInputEdit('edit_table_rows_input', -1)">−</button>
                        <input type="number" id="edit_table_rows_input" value="3" min="1" max="25" class="form-control form-control-sm text-center fw-bold text-primary" style="width:52px;font-size:1.05rem;" oninput="updateWordBadgeFromInputsEdit()">
                        <button type="button" class="btn btn-sm btn-white border px-2 py-1 fw-bold shadow-none word-spin-btn" onclick="adjustWordInputEdit('edit_table_rows_input', 1)">+</button>
                    </div>
                </div>
            </div>

            <!-- Ukuran Cepat (Presets) -->
            <div class="d-flex align-items-center justify-content-between gap-1 mb-3">
                <span class="small text-muted" style="font-size:0.75rem;">Ukuran Cepat:</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensionsEdit(2,2)">2×2</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensionsEdit(3,3)">3×3</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensionsEdit(4,3)">4×3</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 fw-semibold" style="font-size:0.75rem;" onclick="applyWordGridDimensionsEdit(5,4)">5×4</button>
                </div>
            </div>

            <!-- Header Checkbox -->
            <div class="form-check text-start mb-3 ms-1">
                <input class="form-check-input" type="checkbox" id="edit_table_has_header" checked onchange="updateWordBadgeFromInputsEdit()">
                <label class="form-check-label small fw-semibold word-checkbox-label cursor-pointer" for="edit_table_has_header">
                    Jadikan baris pertama sebagai Judul (Header)
                </label>
            </div>

            <!-- Pratinjau Tabel -->
            <div class="p-2.5 mb-3 word-preview-box rounded-3">
                <div class="small fw-semibold text-muted text-center mb-1.5" style="font-size:0.72rem;">Pratinjau Bentuk Tabel:</div>
                <div id="edit_word_mini_preview"></div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary w-50 py-2 fw-bold" onclick="confirmInsertWordTableEdit()">
                    <i class="ti ti-check me-1"></i> Buat Tabel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
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
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,.04);
    overflow: hidden;
    position: relative;
    transition: all .2s ease;
}
.qb-word-table-editor:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 20px rgba(0,0,0,.07);
}
.qb-tbl-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1.5px solid #e2e8f0;
    padding: .65rem .85rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
}
.qb-tbl-hint {
    font-size: .72rem;
    color: #64748b;
    margin-top: 1px;
}
.qb-tbl-hint kbd {
    background: #e2e8f0;
    color: #334155;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: .68rem;
}

/* Selection Mode Badge */
.qb-sel-mode-badge {
    display: none;
    align-items: center;
    gap: 4px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #93c5fd;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: .72rem;
    font-weight: 700;
}
.qb-sel-mode-badge.visible { display: inline-flex; }
.qb-sel-mode-badge .badge-clear {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: .8rem;
    padding: 0 0 0 2px;
    line-height: 1;
}
.qb-sel-mode-badge .badge-clear:hover { color: #dc2626; }

/* Table Canvas */
.qb-table-canvas-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: .75rem;
    background: #fafafa;
}
.qb-rich-table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 0;
    background: #ffffff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.qb-rich-table th,
.qb-rich-table td {
    min-width: 80px;
    min-height: 38px;
    padding: 9px 12px;
    vertical-align: middle;
    outline: none;
    transition: background-color .12s ease, box-shadow .12s ease;
    border: 1px solid #cbd5e1;
    position: relative;
    cursor: cell;
    font-size: .85rem;
    line-height: 1.4;
    user-select: text;
    -webkit-user-select: text;
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
    box-shadow: inset 0 0 0 2.5px #2563eb !important;
    background-color: rgba(219, 234, 254, 0.75) !important;
    color: #0f172a !important;
}
.qb-rich-table.table-borderless td,
.qb-rich-table.table-borderless th {
    border: 1px dashed #cbd5e1;
}
.qb-rich-table.qb-border-outside {
    border: 2.5px solid #475569;
}
.qb-rich-table.qb-border-horizontal td,
.qb-rich-table.qb-border-horizontal th {
    border-left: none;
    border-right: none;
    border-top: none;
    border-bottom: 1px solid #cbd5e1;
}

/* Floating Action Bar */
.qb-float-bar {
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1.5px solid #e2e8f0;
    padding: .45rem .65rem;
    display: flex;
    align-items: center;
    gap: .3rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    transition: transform .2s cubic-bezier(.4,0,.2,1), opacity .2s ease;
    scrollbar-width: none;
}
.qb-float-bar::-webkit-scrollbar { display: none; }

@media (max-width: 767.98px) {
    .qb-float-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: .5rem .75rem env(safe-area-inset-bottom, .5rem);
        border-radius: 16px 16px 0 0;
        border-top: 1.5px solid #cbd5e1;
        box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.15);
        transform: translateY(120%);
        opacity: 0;
        pointer-events: none;
    }
    .qb-float-bar.visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: all;
    }
}
.qb-float-bar-spacer {
    height: 58px;
}

/* FAB Action Buttons */
.qb-fab {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    padding: 6px 9px;
    min-width: 48px;
    height: 44px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: .65rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
    flex-shrink: 0;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}
.qb-fab svg {
    flex-shrink: 0;
}
.qb-fab:hover, .qb-fab:active {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0284c7;
    transform: translateY(-1px);
}
.qb-fab:active {
    transform: scale(0.95);
}
.qb-fab.fab-merge {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
    min-width: 58px;
}
.qb-fab.fab-merge:hover, .qb-fab.fab-merge:active {
    background: #dbeafe;
    border-color: #3b82f6;
    color: #1e40af;
}
.qb-fab.fab-split {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
}
.qb-fab.fab-split:hover, .qb-fab.fab-split:active {
    background: #dcfce7;
    border-color: #22c55e;
}
.qb-fab.fab-border {
    background: #f0f9ff;
    border-color: #7dd3fc;
    color: #0369a1;
}
.qb-fab.fab-border:hover, .qb-fab.fab-border:active {
    background: #e0f2fe;
    border-color: #0284c7;
}
.qb-fab.fab-color {
    background: #faf5ff;
    border-color: #d8b4fe;
    color: #7e22ce;
}
.qb-fab.fab-color:hover, .qb-fab.fab-color:active {
    background: #f3e8ff;
    border-color: #a855f7;
}
.qb-fab.fab-danger {
    color: #dc2626;
    background: #fef2f2;
    border-color: #fecaca;
}
.qb-fab.fab-danger:hover, .qb-fab.fab-danger:active {
    background: #fee2e2;
    border-color: #ef4444;
}
.qb-fab-sep {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
    margin: 0 2px;
    flex-shrink: 0;
}

/* Bottom-sheet Modal for Garis (Border) & Warna */
.qb-border-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1050;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(4px);
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

/* Border swatch grid */
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

/* ═══════════════════════════════════════
   DARK MODE OVERRIDES
═══════════════════════════════════════ */
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
</style>

<script>
    const editCardState = {
        cleanText: '',
        imageUrl: null,
        tableHtml: null
    };
    let editActiveCell = null;
    let editSelectedCells = new Set();

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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

    // Image Attachment Widget (ONLY Image Preview, NO Code / Path)
    function renderImageAttachmentWidgetEdit() {
        const wrap = document.getElementById('edit_image_attachment_wrap');
        if (!wrap) return;

        if (!editCardState.imageUrl) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-image-box shadow-xs" id="edit_img_box">
                <img src="${editCardState.imageUrl}" alt="Pratinjau Gambar Soal">
                <div class="d-flex align-items-center gap-2 mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="font-size: .74rem; font-weight: 600;" onclick="openInsertImageModalEdit()">
                        <i class="ti ti-refresh"></i> Ganti Gambar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5" style="font-size: .74rem; font-weight: 600;" onclick="removeAttachedImageEdit()">
                        <i class="ti ti-trash"></i> Hapus Gambar
                    </button>
                </div>
            </div>
        `;
    }

    function attachImageEdit(url) {
        editCardState.imageUrl = url;
        renderImageAttachmentWidgetEdit();
        updateCardLivePreviewEdit();
    }

    function removeAttachedImageEdit() {
        editCardState.imageUrl = null;
        renderImageAttachmentWidgetEdit();
        updateCardLivePreviewEdit();
    }

    // Helper: Build table coordinate matrix for span-aware selection & merge
    function getTableMatrixEdit(table) {
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
    function getCellCoordsEdit(table, targetCell) {
        const matrix = getTableMatrixEdit(table);
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
    function selectCellRangeEdit(cell1, cell2) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const matrix = getTableMatrixEdit(table);
        const pos1 = getCellCoordsEdit(table, cell1);
        const pos2 = getCellCoordsEdit(table, cell2);

        const rMin = Math.min(pos1.minR, pos2.minR);
        const rMax = Math.max(pos1.maxR, pos2.maxR);
        const cMin = Math.min(pos1.minC, pos2.minC);
        const cMax = Math.max(pos1.maxC, pos2.maxC);

        const cells = table.querySelectorAll('th, td');
        cells.forEach(c => c.classList.remove('qb-cell-selected'));
        editSelectedCells.clear();

        for (let r = rMin; r <= rMax; r++) {
            if (!matrix[r]) continue;
            for (let c = cMin; c <= cMax; c++) {
                const cell = matrix[r][c];
                if (cell && !editSelectedCells.has(cell)) {
                    editSelectedCells.add(cell);
                    cell.classList.add('qb-cell-selected');
                }
            }
        }
    }

    // Setup interactive events: Direct contenteditable typing, Mouse Drag Block Selection & Touch Drag for Mobile
    function setupRichTableEventListenersEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;

        const table = wrap.querySelector('table');
        if (!table) return;

        const cells = table.querySelectorAll('th, td');
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
                            insertTableRowEdit('after');
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
                if (e.shiftKey && editActiveCell && wrap.contains(editActiveCell)) {
                    e.preventDefault();
                    selectCellRangeEdit(editActiveCell, cell);
                    updateFloatBarEdit();
                    return;
                }

                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    if (editSelectedCells.has(cell)) {
                        editSelectedCells.delete(cell);
                        cell.classList.remove('qb-cell-selected');
                    } else {
                        editSelectedCells.add(cell);
                        cell.classList.add('qb-cell-selected');
                    }
                    editActiveCell = cell;
                    updateFloatBarEdit();
                    return;
                }

                isDragging = true;
                dragStartCell = cell;
                editActiveCell = cell;

                cells.forEach(c => c.classList.remove('qb-cell-selected'));
                editSelectedCells.clear();
                editSelectedCells.add(cell);
                cell.classList.add('qb-cell-selected');
                updateFloatBarEdit();
            };

            // Mouse over while dragging
            cell.onmouseover = function(e) {
                if (isDragging && dragStartCell && dragStartCell !== cell) {
                    selectCellRangeEdit(dragStartCell, cell);
                    updateFloatBarEdit();
                }
            };

            // Focus
            cell.onfocus = function() {
                if (editSelectedCells.size <= 1) {
                    cells.forEach(c => c.classList.remove('qb-cell-selected'));
                    editSelectedCells.clear();
                    editSelectedCells.add(cell);
                    cell.classList.add('qb-cell-selected');
                    editActiveCell = cell;
                    updateFloatBarEdit();
                }
            };

            // Input sync
            cell.oninput = function() {
                syncRichTableToStateEdit(true);
            };

            // Touch events for Mobile/Tablet
            cell.addEventListener('touchstart', function(e) {
                touchStartCell = cell;
                editActiveCell = cell;
                if (!e.shiftKey && editSelectedCells.size <= 1) {
                    cells.forEach(c => c.classList.remove('qb-cell-selected'));
                    editSelectedCells.clear();
                    editSelectedCells.add(cell);
                    cell.classList.add('qb-cell-selected');
                }
                updateFloatBarEdit();
            }, { passive: true });
        });

        // Keyboard shortcuts for table (Ctrl+Z: Undo, Ctrl+Y or Ctrl+Shift+Z: Redo)
        table.onkeydown = function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) {
                    redoTableActionEdit();
                } else {
                    undoTableActionEdit();
                }
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redoTableActionEdit();
            }
        };

        // Touch drag across cells
        const onTouchMoveHandler = function(e) {
            if (!touchStartCell) return;
            const touch = e.touches[0];
            const targetEl = document.elementFromPoint(touch.clientX, touch.clientY);
            if (targetEl && (targetEl.tagName === 'TD' || targetEl.tagName === 'TH') && wrap.contains(targetEl)) {
                if (targetEl !== touchStartCell) {
                    selectCellRangeEdit(touchStartCell, targetEl);
                    updateFloatBarEdit();
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
        if (!editActiveCell || !wrap.contains(editActiveCell)) {
            const firstCell = table.querySelector('th, td');
            if (firstCell) {
                firstCell.classList.add('qb-cell-selected');
                editActiveCell = firstCell;
                editSelectedCells.add(firstCell);
            }
        }
        updateFloatBarEdit();
    }

    // Render Clean & Modern Table Editor (Edit Mode)
    function renderVisualTableEditorEdit() {
        const wrap = document.getElementById('edit_table_editor_wrap');
        if (!wrap) return;

        const tableHtml = editCardState.tableHtml;
        if (!tableHtml || !tableHtml.trim()) {
            wrap.innerHTML = '';
            return;
        }

        wrap.innerHTML = `
            <div class="qb-word-table-editor rounded-3 border overflow-hidden mb-2" id="edit_tbl_card">
                <!-- Clean Compact Header -->
                <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="ti ti-table me-1"></i> Tabel Soal</span>
                        <span class="text-muted small d-none d-sm-inline" style="font-size:0.75rem;">Klik pada sel untuk mengetik teks</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2" style="font-size:0.74rem;" onclick="openInsertTableModalEdit()">
                            <i class="ti ti-settings me-1"></i> Ubah Ukuran
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2" style="font-size:0.74rem;" onclick="removeRichTableEdit()">
                            <i class="ti ti-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>

                <!-- Modern Organized Toolbar -->
                <div class="qb-table-ribbon">
                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" id="edit_tbl_undo_btn" onclick="undoTableActionEdit()" title="Urungkan (Undo) [Ctrl+Z]" disabled>
                            <i class="ti ti-arrow-back-up text-primary"></i> Undo
                        </button>
                        <button type="button" class="qb-ribbon-btn" id="edit_tbl_redo_btn" onclick="redoTableActionEdit()" title="Ulangi (Redo) [Ctrl+Y]" disabled>
                            <i class="ti ti-arrow-forward-up text-primary"></i> Redo
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="insertTableRowEdit('after')" title="Tambah Baris di Bawah">
                            <i class="ti ti-row-insert-bottom text-primary"></i> + Baris
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="insertTableColEdit('after')" title="Tambah Kolom di Kanan">
                            <i class="ti ti-column-insert-right text-success"></i> + Kolom
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="mergeSelectedCellsEdit()" title="Gabungkan sel yang dipilih">
                            <i class="ti ti-arrows-merge text-indigo"></i> Gabung
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="unmergeCellEdit()" title="Pisahkan sel">
                            <i class="ti ti-arrows-split text-info"></i> Pisah
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn" onclick="openBorderModalEdit()" title="Atur Garis Tabel">
                            <i class="ti ti-border-all text-secondary"></i> Garis
                        </button>
                        <button type="button" class="qb-ribbon-btn" onclick="openColorModalEdit()" title="Warna Latar Sel">
                            <i class="ti ti-color-swatch text-warning"></i> Warna
                        </button>
                    </div>

                    <div class="qb-ribbon-sep"></div>

                    <div class="qb-ribbon-group">
                        <button type="button" class="qb-ribbon-btn qb-ribbon-btn-danger" onclick="deleteActiveTableRowEdit()" title="Hapus Baris Ini">
                            <i class="ti ti-trash-x"></i> - Baris
                        </button>
                        <button type="button" class="qb-ribbon-btn qb-ribbon-btn-danger" onclick="deleteActiveTableColEdit()" title="Hapus Kolom Ini">
                            <i class="ti ti-trash-x"></i> - Kolom
                        </button>
                    </div>
                </div>

                <!-- Canvas -->
                <div class="qb-table-canvas-wrap" id="edit_table_canvas_wrap">
                    ${tableHtml}
                </div>

                <!-- Subtle Bottom Action -->
                <div class="qb-table-footer-actions py-2 bg-light text-center border-top">
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-1 shadow-none" style="font-size:0.78rem;" onclick="insertTableRowEdit('after')">
                        <i class="ti ti-plus me-1"></i> Tambah Baris Baru
                    </button>
                </div>
            </div>
        `;

        setupRichTableEventListenersEdit();
        initTableHistoryEdit(tableHtml);
    }

    // ── Shared dialog modals for Edit mode (Clean & User-Friendly) ───────────────────────────
    function buildSharedModalsEdit() {
        // 1. Border Modal
        if (!document.getElementById('edit_border_modal_overlay')) {
            const bmo = document.createElement('div');
            bmo.id = 'edit_border_modal_overlay';
            bmo.className = 'qb-border-modal-overlay';
            bmo.innerHTML = `
                <div class="qb-border-modal" id="edit_border_modal">
                    <div class="qb-border-modal-handle d-md-none"></div>

                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:.95rem;">Pilih Jenis Garis (Border)</h6>
                            <span class="small text-muted" id="edit_border_modal_sub" style="font-size:.74rem;">Garis akan diterapkan ke sel yang dipilih</span>
                        </div>
                        <button type="button" class="btn-close" onclick="closeBorderModalEdit()" aria-label="Tutup"></button>
                    </div>

                    <!-- Per-cell borders -->
                    <div class="qb-color-modal-section">Garis Pada Sel Terpilih</div>
                    <div class="qb-border-grid" id="edit_border_cell_grid">
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
                    <div class="qb-border-grid" id="edit_border_table_grid">
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
                            Kotak
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="horizontal">
                            <svg viewBox="0 0 20 20"><line x1="1" y1="5" x2="19" y2="5" stroke="#334155" stroke-width="1.5"/><line x1="1" y1="10" x2="19" y2="10" stroke="#334155" stroke-width="1.5"/><line x1="1" y1="15" x2="19" y2="15" stroke="#334155" stroke-width="1.5"/></svg>
                            Garis Baris
                        </button>
                        <button type="button" class="qb-border-swatch" data-tbtype="striped">
                            <svg viewBox="0 0 20 20"><rect x="1" y="1" width="18" height="18" stroke="#334155" stroke-width="1.5" fill="none"/><rect x="1" y="6" width="18" height="5" fill="#e2e8f0"/><rect x="1" y="15" width="18" height="4" fill="#e2e8f0"/></svg>
                            Belang
                        </button>
                    </div>

                    <button type="button" class="qb-modal-close-btn" onclick="closeBorderModalEdit()">Selesai</button>
                </div>
            `;
            document.body.appendChild(bmo);

            bmo.addEventListener('click', e => { if (e.target === bmo) closeBorderModalEdit(); });
            bmo.querySelectorAll('[data-btype]').forEach(btn => {
                btn.addEventListener('click', () => {
                    setCellBorderEdit(btn.dataset.btype);
                    closeBorderModalEdit();
                });
            });
            bmo.querySelectorAll('[data-tbtype]').forEach(btn => {
                btn.addEventListener('click', () => {
                    setTableBorderStyleEdit(btn.dataset.tbtype);
                    closeBorderModalEdit();
                });
            });
        }

        // 3. Color Modal
        if (!document.getElementById('edit_color_modal_overlay')) {
            const cmo = document.createElement('div');
            cmo.id = 'edit_color_modal_overlay';
            cmo.className = 'qb-border-modal-overlay';
            cmo.innerHTML = `
                <div class="qb-border-modal">
                    <div class="qb-border-modal-handle d-md-none"></div>

                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:.95rem;">Warna Latar Sel (Shading)</h6>
                            <span class="small text-muted" style="font-size:.74rem;">Pilih warna latar belakang sel yang dipilih</span>
                        </div>
                        <button type="button" class="btn-close" onclick="closeColorModalEdit()" aria-label="Tutup"></button>
                    </div>

                    <div class="qb-color-modal-section">Pilihan Warna Standar</div>
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
                    <button type="button" class="qb-modal-close-btn" onclick="closeColorModalEdit()">Selesai</button>
                </div>
            `;
            document.body.appendChild(cmo);

            cmo.addEventListener('click', e => { if (e.target === cmo) closeColorModalEdit(); });
            cmo.querySelectorAll('[data-color]').forEach(btn => {
                btn.addEventListener('click', () => {
                    setCellBackgroundEdit(btn.dataset.color || '');
                    closeColorModalEdit();
                });
            });
        }
    }

    // Clean Mini Table Preview
    function updateWordMiniPreviewEdit(cols, rows, hasHeader) {
        const previewEl = document.getElementById('edit_word_mini_preview');
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

    function applyWordGridDimensionsEdit(cols, rows) {
        const colsInput = document.getElementById('edit_table_cols_input');
        const rowsInput = document.getElementById('edit_table_rows_input');
        if (colsInput) colsInput.value = cols;
        if (rowsInput) rowsInput.value = rows;
        updateWordBadgeFromInputsEdit();
    }

    function adjustWordInputEdit(inputId, delta) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let val = parseInt(input.value || 1) + delta;
        const min = parseInt(input.min || 1);
        const max = parseInt(input.max || 25);
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;
        updateWordBadgeFromInputsEdit();
    }

    function updateWordBadgeFromInputsEdit() {
        const cols = parseInt(document.getElementById('edit_table_cols_input').value || 3);
        const rows = parseInt(document.getElementById('edit_table_rows_input').value || 3);
        const hasHeader = document.getElementById('edit_table_has_header') ? document.getElementById('edit_table_has_header').checked : true;
        updateWordMiniPreviewEdit(cols, rows, hasHeader);
    }

    function openInsertTableModalEdit() {
        applyWordGridDimensionsEdit(3, 3);
        const modalEl = document.getElementById('modalInsertTableEdit');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function closeInsertTableModalEdit() {
        const modalEl = document.getElementById('modalInsertTableEdit');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    function confirmInsertWordTableEdit() {
        const cols = Math.max(1, parseInt(document.getElementById('edit_table_cols_input').value || 3));
        const rows = Math.max(1, parseInt(document.getElementById('edit_table_rows_input').value || 3));
        const hasHeader = document.getElementById('edit_table_has_header').checked;

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

        editCardState.tableHtml = tableHtml;
        renderVisualTableEditorEdit();
        updateCardLivePreviewEdit();
        closeInsertTableModalEdit();

        // Focus the first cell immediately so user can type
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (wrap) {
            const firstCell = wrap.querySelector('th, td');
            if (firstCell) {
                firstCell.focus();
                firstCell.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }

    function openBorderModalEdit() {
        buildSharedModalsEdit();
        const cnt = editSelectedCells ? editSelectedCells.size : 0;
        const sub = document.getElementById('edit_border_modal_sub');
        if (sub) sub.textContent = cnt > 1 ? `Diterapkan ke ${cnt} sel terpilih` : 'Diterapkan ke sel aktif';
        document.getElementById('edit_border_modal_overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeBorderModalEdit() {
        const el = document.getElementById('edit_border_modal_overlay');
        if (el) el.classList.remove('open');
        document.body.style.overflow = '';
    }
    function openColorModalEdit() {
        buildSharedModalsEdit();
        document.getElementById('edit_color_modal_overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeColorModalEdit() {
        const el = document.getElementById('edit_color_modal_overlay');
        if (el) el.classList.remove('open');
        document.body.style.overflow = '';
    }
    function clearCellSelectionEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        wrap.querySelectorAll('.qb-cell-selected').forEach(c => c.classList.remove('qb-cell-selected'));
        if (editSelectedCells) editSelectedCells.clear();
        updateFloatBarEdit();
    }

    function updateFloatBarEdit() {
        const bar = document.getElementById('edit_float_bar');
        const badge = document.getElementById('edit_sel_badge');
        const badgeText = document.getElementById('edit_sel_badge_text');
        const count = editSelectedCells ? editSelectedCells.size : 0;

        if (editActiveCell) {
            if (bar) bar.classList.add('visible');
        } else {
            if (bar) bar.classList.remove('visible');
        }

        if (badge && badgeText) {
            if (count >= 2) {
                badge.classList.add('visible');
                badgeText.textContent = count + ' sel';
            } else {
                badge.classList.remove('visible');
            }
        }
    }

    // ── Table Undo & Redo History System (Edit Mode) ───
    let editTableHistory = { past: [], future: [], current: '' };
    let editTableHistoryDebounceTimer = null;

    function initTableHistoryEdit(initialHtml) {
        if (!editTableHistory.current) {
            editTableHistory = {
                past: [],
                future: [],
                current: initialHtml || ''
            };
        } else if (editTableHistory.current !== initialHtml) {
            editTableHistory.past.push(editTableHistory.current);
            if (editTableHistory.past.length > 30) editTableHistory.past.shift();
            editTableHistory.current = initialHtml;
            editTableHistory.future = [];
        }
        updateUndoRedoButtonsEdit();
    }

    function pushTableHistoryEdit(isDebounced = false) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const snapshot = table.outerHTML;

        if (!editTableHistory.current) {
            initTableHistoryEdit(snapshot);
            return;
        }

        if (editTableHistory.current === snapshot) return;

        if (isDebounced) {
            clearTimeout(editTableHistoryDebounceTimer);
            editTableHistoryDebounceTimer = setTimeout(() => {
                if (editTableHistory.current !== snapshot) {
                    editTableHistory.past.push(editTableHistory.current);
                    if (editTableHistory.past.length > 30) editTableHistory.past.shift();
                    editTableHistory.current = snapshot;
                    editTableHistory.future = [];
                    updateUndoRedoButtonsEdit();
                }
            }, 400);
        } else {
            clearTimeout(editTableHistoryDebounceTimer);
            editTableHistory.past.push(editTableHistory.current);
            if (editTableHistory.past.length > 30) editTableHistory.past.shift();
            editTableHistory.current = snapshot;
            editTableHistory.future = [];
            updateUndoRedoButtonsEdit();
        }
    }

    function undoTableActionEdit() {
        if (editTableHistory.past.length === 0) return;

        clearTimeout(editTableHistoryDebounceTimer);
        editTableHistory.future.push(editTableHistory.current);
        const prevState = editTableHistory.past.pop();
        editTableHistory.current = prevState;

        restoreTableSnapshotEdit(prevState);
        updateUndoRedoButtonsEdit();
    }

    function redoTableActionEdit() {
        if (editTableHistory.future.length === 0) return;

        clearTimeout(editTableHistoryDebounceTimer);
        editTableHistory.past.push(editTableHistory.current);
        const nextState = editTableHistory.future.pop();
        editTableHistory.current = nextState;

        restoreTableSnapshotEdit(nextState);
        updateUndoRedoButtonsEdit();
    }

    function restoreTableSnapshotEdit(html) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;

        wrap.innerHTML = html;
        editActiveCell = null;
        if (editSelectedCells) editSelectedCells.clear();

        setupRichTableEventListenersEdit();

        editCardState.tableHtml = html;
        updateCardLivePreviewEdit();
    }

    function updateUndoRedoButtonsEdit() {
        const undoBtn = document.getElementById('edit_tbl_undo_btn');
        const redoBtn = document.getElementById('edit_tbl_redo_btn');

        if (undoBtn) {
            undoBtn.disabled = editTableHistory.past.length === 0;
        }
        if (redoBtn) {
            redoBtn.disabled = editTableHistory.future.length === 0;
        }
    }

    function syncRichTableToStateEdit(isDebounced = false) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        editCardState.tableHtml = table.outerHTML;
        updateCardLivePreviewEdit();

        pushTableHistoryEdit(isDebounced);
    }

    function openVisualTableOrAddEdit() {
        if (!editCardState.tableHtml || !editCardState.tableHtml.trim()) {
            openInsertTableModalEdit();
            return;
        }
        renderVisualTableEditorEdit();
        updateCardLivePreviewEdit();

        const cardEl = document.getElementById('edit_tbl_card');
        if (cardEl) {
            cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // 1-Click Merge for blocked cells in Edit mode
    function mergeSelectedCellsEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        if (!editSelectedCells || editSelectedCells.size < 2) {
            if (editSelectedCells && editSelectedCells.size === 1) {
                const cell = Array.from(editSelectedCells)[0];
                const colspan = parseInt(cell.getAttribute('colspan') || 1);
                const rowspan = parseInt(cell.getAttribute('rowspan') || 1);
                if (colspan > 1 || rowspan > 1) {
                    unmergeCellEdit();
                    return;
                }
            }
            alert('Silakan blok (tarik dengan mouse) minimal 2 sel yang berdampingan terlebih dahulu untuk digabungkan.');
            return;
        }

        const matrix = getTableMatrixEdit(table);
        let minR = Infinity, maxR = -Infinity, minC = Infinity, maxC = -Infinity;

        editSelectedCells.forEach(cell => {
            const coords = getCellCoordsEdit(table, cell);
            minR = Math.min(minR, coords.minR);
            maxR = Math.max(maxR, coords.maxR);
            minC = Math.min(minC, coords.minC);
            maxC = Math.max(maxC, coords.maxC);
        });

        const topLeftCell = matrix[minR][minC];
        if (!topLeftCell) return;

        // Collect combined text
        const textParts = [];
        editSelectedCells.forEach(cell => {
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
        editSelectedCells.forEach(cell => {
            if (cell !== topLeftCell && cell.parentElement) {
                cell.remove();
            }
        });

        // Reset selection to the merged cell
        editSelectedCells.clear();
        editSelectedCells.add(topLeftCell);
        editActiveCell = topLeftCell;

        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    function unmergeCellEdit() {
        const activeCell = editActiveCell;
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

        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    // Apply border to ONLY the selected cells (Excel per-cell style)
    function setCellBorderEdit(borderType) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const targetCells = (editSelectedCells && editSelectedCells.size > 0)
            ? Array.from(editSelectedCells)
            : (editActiveCell ? [editActiveCell] : []);

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
                case 'all': c.style.border = brd; break;
                case 'none': c.style.border = brdNone; break;
                case 'outside': c.style.border = brd; break;
                case 'top': c.style.borderTop = brd; break;
                case 'bottom': c.style.borderBottom = brd; break;
                case 'left': c.style.borderLeft = brd; break;
                case 'right': c.style.borderRight = brd; break;
                case 'horizontal':
                    c.style.borderTop = brd;
                    c.style.borderBottom = brd;
                    c.style.borderLeft = brdNone;
                    c.style.borderRight = brdNone;
                    break;
                case 'thick': c.style.border = brdThick; break;
            }
        });

        syncRichTableToStateEdit();
    }

    // Apply border to the entire table
    function setTableBorderStyleEdit(borderType) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
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

        syncRichTableToStateEdit();
    }

    function setCellBackgroundEdit(color) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const targetCells = (editSelectedCells && editSelectedCells.size > 0) ? Array.from(editSelectedCells) : (editActiveCell ? [editActiveCell] : []);

        targetCells.forEach(c => {
            c.style.backgroundColor = color || '';
        });
        syncRichTableToStateEdit();
    }

    function toggleCellBoldEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const targetCells = (editSelectedCells && editSelectedCells.size > 0) ? Array.from(editSelectedCells) : (editActiveCell ? [editActiveCell] : []);
        
        if (targetCells.length === 0) return;
        const isCurrentlyBold = targetCells[0].style.fontWeight === 'bold' || targetCells[0].style.fontWeight === '700';

        targetCells.forEach(c => {
            c.style.fontWeight = isCurrentlyBold ? 'normal' : 'bold';
        });
        syncRichTableToStateEdit();
    }

    function toggleCellHeaderEdit() {
        const activeCell = editActiveCell;
        if (!activeCell) return;

        const isTh = activeCell.tagName === 'TH';
        const newCell = document.createElement(isTh ? 'td' : 'th');
        newCell.innerHTML = activeCell.innerHTML;
        if (activeCell.getAttribute('colspan')) newCell.setAttribute('colspan', activeCell.getAttribute('colspan'));
        if (activeCell.getAttribute('rowspan')) newCell.setAttribute('rowspan', activeCell.getAttribute('rowspan'));
        if (activeCell.getAttribute('style')) newCell.setAttribute('style', activeCell.getAttribute('style'));

        activeCell.parentElement.replaceChild(newCell, activeCell);
        editActiveCell = newCell;

        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    function alignActiveCellEdit(align) {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const targetCells = (editSelectedCells && editSelectedCells.size > 0) ? Array.from(editSelectedCells) : (editActiveCell ? [editActiveCell] : []);

        targetCells.forEach(c => {
            c.style.textAlign = align;
        });
        syncRichTableToStateEdit();
    }

    function insertTableRowEdit(position = 'after') {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = editActiveCell && wrap.contains(editActiveCell) ? editActiveCell : null;
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

        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    function insertTableColEdit(position = 'after') {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = editActiveCell && wrap.contains(editActiveCell) ? editActiveCell : null;
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

        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    function deleteActiveTableRowEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const allRows = table.querySelectorAll('tr');
        if (allRows.length <= 1) {
            alert('Tabel harus memiliki minimal 1 baris.');
            return;
        }

        const activeCell = editActiveCell && wrap.contains(editActiveCell) ? editActiveCell : null;
        const targetTr = activeCell ? activeCell.parentElement : allRows[allRows.length - 1];
        if (targetTr) {
            targetTr.remove();
            editActiveCell = null;
            setupRichTableEventListenersEdit();
            syncRichTableToStateEdit();
        }
    }

    function deleteActiveTableColEdit() {
        const wrap = document.getElementById('edit_table_canvas_wrap');
        if (!wrap) return;
        const table = wrap.querySelector('table');
        if (!table) return;

        const activeCell = editActiveCell && wrap.contains(editActiveCell) ? editActiveCell : null;
        let cIdx = activeCell ? Array.from(activeCell.parentElement.children).indexOf(activeCell) : 0;
        if (cIdx === -1) cIdx = 0;

        const rows = table.querySelectorAll('tr');
        rows.forEach(tr => {
            if (tr.children.length > 1 && cIdx < tr.children.length) {
                tr.children[cIdx].remove();
            }
        });

        editActiveCell = null;
        setupRichTableEventListenersEdit();
        syncRichTableToStateEdit();
    }

    function removeRichTableEdit() {
        editCardState.tableHtml = null;
        editActiveCell = null;
        if (editSelectedCells) editSelectedCells.clear();
        editTableHistory = { past: [], future: [], current: '' };
        renderVisualTableEditorEdit();
        updateCardLivePreviewEdit();
    }

    function handleTextareaInputEdit() {
        const textarea = document.getElementById('question_text');
        if (!textarea) return;
        editCardState.cleanText = textarea.value;
        updateCardLivePreviewEdit();
    }

    function formatQuestionPreviewHtml(raw) {
        if (!raw || !raw.trim()) return '<em class="text-muted">Belum ada teks pertanyaan...</em>';

        // 1. Render Markdown images: ![alt](url)
        let html = raw.replace(/!\[(.*?)\]\((.*?)\)/g, (match, alt, url) => {
            return `<div class="text-center my-2.5 q-media-wrap"><img src="${url}" alt="${alt || 'Gambar Soal'}" class="img-fluid rounded border shadow-xs" style="max-height: 280px; object-fit: contain;"></div>`;
        });

        // 2. If it contains raw HTML table or markdown table
        const lines = html.split('\n');
        let out = [];
        let tblBuf = [];

        const flushTbl = () => {
            if (tblBuf.length === 0) return;
            let tblHtml = '<div class="table-responsive my-2.5"><table class="table table-bordered table-sm table-striped align-middle mb-0">';
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
            if (trm.startsWith('<div') || trm.startsWith('<table') || trm.startsWith('<p') || trm.startsWith('<thead') || trm.startsWith('<tbody') || trm.startsWith('<tr')) return l;
            return trm ? `<div class="mb-1">${trm}</div>` : '<div class="my-1"></div>';
        }).join('\n');
    }

    function updateCardLivePreviewEdit() {
        const previewBox = document.getElementById('edit_live_preview');
        const textarea = document.getElementById('question_text');
        if (!previewBox || !textarea) return;

        const cleanText = textarea.value.trim();
        const compiled = compileFinalQuestionText(cleanText, editCardState.imageUrl, editCardState.tableHtml);

        if (!compiled) {
            previewBox.classList.add('d-none');
            return;
        }

        const hasMedia = !!editCardState.imageUrl;
        const hasTable = !!(editCardState.tableHtml && editCardState.tableHtml.trim());

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

    function toggleLivePreviewEdit() {
        const previewBox = document.getElementById('edit_live_preview');
        if (!previewBox) return;

        if (previewBox.classList.contains('d-none')) {
            updateCardLivePreviewEdit();
            previewBox.classList.remove('d-none');
        } else {
            previewBox.classList.add('d-none');
        }
    }

    function openInsertImageModalEdit() {
        document.getElementById('modalImgFileInputEdit').value = '';
        document.getElementById('modalImgUrlInputEdit').value = '';
        document.getElementById('modalImgPreviewWrapEdit').classList.add('d-none');
        const modal = new bootstrap.Modal(document.getElementById('modalInsertImageEdit'));
        modal.show();
    }

    function openInsertSymbolModalEdit() {
        const modal = new bootstrap.Modal(document.getElementById('modalInsertSymbolEdit'));
        modal.show();
    }

    function insertSymbolToEditCard(sym) {
        const textarea = document.getElementById('question_text');
        if (!textarea) return;
        insertTextAtCursorEdit(textarea, sym);
        handleTextareaInputEdit();
    }

    function insertTextAtCursorEdit(textarea, textToInsert) {
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + textToInsert + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + textToInsert.length;
        textarea.focus();
    }

    function confirmInsertImageEdit() {
        const fileInput = document.getElementById('modalImgFileInputEdit');
        const urlInput = document.getElementById('modalImgUrlInputEdit');

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            const progressEl = document.getElementById('modalImgUploadProgressEdit');
            const submitBtn = document.getElementById('btnConfirmInsertImageEdit');
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
                    attachImageEdit(imgUrl);

                    const modalEl = document.getElementById('modalInsertImageEdit');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else {
                    alert(data.error || 'Terjadi kesalahan saat mengunggah gambar.');
                }
            })
            .catch(err => {
                progressEl.classList.add('d-none');
                submitBtn.disabled = false;
                alert('Terjadi kesalahan jaringan saat mengunggah gambar.');
            });
        } else if (urlInput.value.trim()) {
            const imgUrl = urlInput.value.trim();
            attachImageEdit(imgUrl);

            const modalEl = document.getElementById('modalInsertImageEdit');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else {
            alert('Pilih file gambar atau masukkan URL gambar terlebih dahulu.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('question_text');
        if (textarea) {
            const raw = textarea.value;
            const extracted = extractMediaAndTableFromText(raw);
            editCardState.cleanText = extracted.cleanText;
            editCardState.imageUrl = extracted.imageUrl;
            editCardState.tableHtml = extracted.tableHtml;

            // Set clean text to textarea
            textarea.value = extracted.cleanText;

            renderImageAttachmentWidgetEdit();
            renderVisualTableEditorEdit();
            updateCardLivePreviewEdit();
        }

        // On form submit: compile final question_text
        const form = document.getElementById('editQbForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const textarea = document.getElementById('question_text');
                if (textarea) {
                    syncRichTableToStateEdit();
                    const compiled = compileFinalQuestionText(textarea.value, editCardState.imageUrl, editCardState.tableHtml);
                    textarea.value = compiled;
                }
            });
        }
    });
</script>
@endsection
