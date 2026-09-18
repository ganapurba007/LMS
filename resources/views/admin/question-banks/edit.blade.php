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
                <label for="question_text" class="md-form-label">Teks Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
                <textarea id="question_text" name="question_text" rows="3" class="form-control @error('question_text') is-invalid @enderror" placeholder="Tuliskan pertanyaan atau instruksi..." {{ old('question_type', $questionBank->question_type) === 'matching' ? '' : 'required' }}>{{ old('question_text', $questionBank->question_text) }}</textarea>
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
                <label class="md-form-label mb-2">Kunci Jawaban Pernyataan <span class="text-danger">*</span></label>
                <div class="row g-2">
                    <div class="col-6 qb-tf-wrap">
                        <input type="radio" class="d-none" name="correct_tf" id="edit_tf_true" value="Benar" {{ old('correct_tf', $tfCorrect) === 'Benar' ? 'checked' : '' }}>
                        <label class="qb-tf-box qb-tf-true w-100" for="edit_tf_true">
                            <i class="ti ti-check"></i> Benar (True)
                        </label>
                    </div>
                    <div class="col-6 qb-tf-wrap">
                        <input type="radio" class="d-none" name="correct_tf" id="edit_tf_false" value="Salah" {{ old('correct_tf', $tfCorrect) === 'Salah' ? 'checked' : '' }}>
                        <label class="qb-tf-box qb-tf-false w-100" for="edit_tf_false">
                            <i class="ti ti-x"></i> Salah (False)
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
@endsection
