@extends('layouts.be.master')

@section('header_title', 'Edit Soal — Bank Soal #' . $questionBank->id)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Edit Soal Bank Soal</h3>
        <p class="text-muted small mb-0">Perbarui pertanyaan, tipe format soal, dan pilihan/pasangan jawaban kunci.</p>
    </div>
    <a href="{{ route('admin.question-banks.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-edit text-primary"></i> Form Edit Soal ID #{{ $questionBank->id }}
                </h5>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                    Format: {{ strtoupper(str_replace('_', ' ', $questionBank->question_type ?? 'multiple_choice')) }}
                </span>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.question-banks.update', $questionBank) }}" id="editQbForm">
                    @csrf
                    @method('PUT')

                    <!-- Format Question Type Switcher -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Format Tipe Soal <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-1" role="group">
                            <div class="flex-fill" style="min-width: 120px;">
                                <input type="radio" class="btn-check" name="question_type" id="edit_type_mc" value="multiple_choice" {{ old('question_type', $questionBank->question_type) === 'multiple_choice' ? 'checked' : '' }} onchange="switchEditType('multiple_choice')">
                                <label class="btn btn-outline-primary w-100 text-nowrap py-2 fw-semibold" for="edit_type_mc">
                                    <i class="ti ti-list-check me-1"></i> Pilihan Ganda
                                </label>
                            </div>

                            <div class="flex-fill" style="min-width: 120px;">
                                <input type="radio" class="btn-check" name="question_type" id="edit_type_tf" value="true_false" {{ old('question_type', $questionBank->question_type) === 'true_false' ? 'checked' : '' }} onchange="switchEditType('true_false')">
                                <label class="btn btn-outline-warning text-dark w-100 text-nowrap py-2 fw-semibold" for="edit_type_tf">
                                    <i class="ti ti-checkup-list me-1"></i> Benar / Salah
                                </label>
                            </div>

                            <div class="flex-fill" style="min-width: 120px;">
                                <input type="radio" class="btn-check" name="question_type" id="edit_type_match" value="matching" {{ old('question_type', $questionBank->question_type) === 'matching' ? 'checked' : '' }} onchange="switchEditType('matching')">
                                <label class="btn btn-outline-info text-dark w-100 text-nowrap py-2 fw-semibold" for="edit_type_match">
                                    <i class="ti ti-arrows-left-right me-1"></i> Menjodohkan
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal -->
                    <div class="mb-4 {{ old('question_type', $questionBank->question_type) === 'matching' ? 'd-none' : '' }}" id="sec_edit_qtext">
                        <label for="question_text" class="form-label fw-semibold text-dark">Teks Pertanyaan / Instruksi Soal <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold text-dark mb-2">Pilihan Jawaban &amp; Kunci Jawaban Benar <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-3">Tandai radio button pada opsi yang merupakan **jawaban benar**.</p>

                        <div class="vstack gap-2">
                            @for($i = 0; $i < 4; $i++)
                                @php
                                    $optVal = isset($options[$i]) ? $options[$i]->option_text : '';
                                @endphp
                                <div class="input-group input-group-lg shadow-2xs rounded-3 overflow-hidden">
                                    <div class="input-group-text bg-light border-end-0">
                                        <input class="form-check-input mt-0" type="radio" name="correct_option" id="correct_{{ $i }}" value="{{ $i }}" {{ old('correct_option', (string)$defaultCorrect) == (string)$i ? 'checked' : '' }}>
                                        <label for="correct_{{ $i }}" class="ms-2 fw-bold text-success small">Kunci {{ chr(65 + $i) }}</label>
                                    </div>
                                    <input type="text" name="options[{{ $i }}]" id="opt_input_{{ $i }}" class="form-control bg-white @error('options.'.$i) is-invalid @enderror" value="{{ old('options.'.$i, $optVal) }}" placeholder="Opsi Jawaban {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
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
                        <label class="form-label fw-semibold text-dark mb-2">Kunci Jawaban Pernyataan <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card border p-3 rounded-3 text-center bg-white">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input me-2" type="radio" name="correct_tf" id="edit_tf_true" value="Benar" {{ old('correct_tf', $tfCorrect) === 'Benar' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-success fs-6" for="edit_tf_true">
                                            <i class="ti ti-check me-1"></i> Benar (True)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border p-3 rounded-3 text-center bg-white">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input me-2" type="radio" name="correct_tf" id="edit_tf_false" value="Salah" {{ old('correct_tf', $tfCorrect) === 'Salah' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-danger fs-6" for="edit_tf_false">
                                            <i class="ti ti-x me-1"></i> Salah (False)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Menjodohkan -->
                    <div id="sec_edit_matching" class="mb-4 {{ old('question_type', $questionBank->question_type) === 'matching' ? '' : 'd-none' }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold text-dark mb-0">Pasangan yang Cocok (Soal &amp; Pasangannya) <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3" onclick="addEditPairRow()">
                                <i class="ti ti-plus me-1"></i> Tambah Pasangan
                            </button>
                        </div>

                        <div class="row g-2 text-muted small fw-semibold mb-2 d-none d-md-flex px-1">
                            <div class="col-md-5">Soal / Premis</div>
                            <div class="col-md-1 text-center"></div>
                            <div class="col-md-5">Pasangan Jawaban Benar</div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="edit_pairs_container" class="d-flex flex-column gap-2">
                            @if($questionBank->isMatching() && $options->count() >= 2)
                                @foreach($options as $pIdx => $pairOpt)
                                    <div class="row g-2 align-items-center edit-pair-row">
                                        <div class="col-12 col-md-5">
                                            <input type="text" name="pairs[{{ $pIdx }}][premise]" class="form-control edit-pair-input" placeholder="Soal / Premis {{ $pIdx + 1 }}" value="{{ old('pairs.'.$pIdx.'.premise', $pairOpt->option_text) }}">
                                        </div>
                                        <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                                            <i class="ti ti-arrow-right fs-5"></i>
                                        </div>
                                        <div class="col-10 col-md-5">
                                            <input type="text" name="pairs[{{ $pIdx }}][match]" class="form-control edit-pair-input" placeholder="Pasangan Jawaban {{ $pIdx + 1 }}" value="{{ old('pairs.'.$pIdx.'.match', $pairOpt->match_text) }}">
                                        </div>
                                        <div class="col-2 col-md-1 text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeEditPairRow(this)" title="Hapus baris">
                                                <i class="ti ti-x fs-6"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default 2 empty rows -->
                                <div class="row g-2 align-items-center edit-pair-row">
                                    <div class="col-12 col-md-5">
                                        <input type="text" name="pairs[0][premise]" class="form-control edit-pair-input" placeholder="Soal / Premis 1">
                                    </div>
                                    <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                                        <i class="ti ti-arrow-right fs-5"></i>
                                    </div>
                                    <div class="col-10 col-md-5">
                                        <input type="text" name="pairs[0][match]" class="form-control edit-pair-input" placeholder="Pasangan Jawaban 1">
                                    </div>
                                    <div class="col-2 col-md-1 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeEditPairRow(this)" title="Hapus baris">
                                            <i class="ti ti-x fs-6"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center edit-pair-row">
                                    <div class="col-12 col-md-5">
                                        <input type="text" name="pairs[1][premise]" class="form-control edit-pair-input" placeholder="Soal / Premis 2">
                                    </div>
                                    <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                                        <i class="ti ti-arrow-right fs-5"></i>
                                    </div>
                                    <div class="col-10 col-md-5">
                                        <input type="text" name="pairs[1][match]" class="form-control edit-pair-input" placeholder="Pasangan Jawaban 2">
                                    </div>
                                    <div class="col-2 col-md-1 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeEditPairRow(this)" title="Hapus baris">
                                            <i class="ti ti-x fs-6"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.question-banks.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                <input type="text" name="pairs[${count}][premise]" class="form-control edit-pair-input" placeholder="Soal / Premis ${count + 1}" required>
            </div>
            <div class="d-none d-md-flex col-md-1 align-items-center justify-content-center text-info">
                <i class="ti ti-arrow-right fs-5"></i>
            </div>
            <div class="col-10 col-md-5">
                <input type="text" name="pairs[${count}][match]" class="form-control edit-pair-input" placeholder="Pasangan Jawaban ${count + 1}" required>
            </div>
            <div class="col-2 col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeEditPairRow(this)" title="Hapus baris">
                    <i class="ti ti-x fs-6"></i>
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
