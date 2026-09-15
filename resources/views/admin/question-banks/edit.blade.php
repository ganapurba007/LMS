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
            <h6 class="md-form-head-title mb-0">Informasi Soal ID #{{ $questionBank->id }}</h6>
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
    padding: .45rem .6rem;
    border-radius: 6px;
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    background: var(--tblr-body-bg, #f8fafc);
    color: var(--tblr-text-muted, #64748b);
    font-size: .78rem;
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
