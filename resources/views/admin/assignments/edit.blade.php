@extends('layouts.be.master')
@section('header_title', 'Pembelajaran — Edit Terbit Tugas')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Tugas Terbit</h5>
            <div class="text-muted small">Pilih tugas dari Bank Tugas, tentukan kelas penerima dan batas waktu pengerjaan.</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.assignment-banks.create') }}" class="md-btn-primary" style="background:#0ca678;border-color:#0ca678;">
            <i class="ti ti-plus"></i> <span>Buat Master di Bank Tugas</span>
        </a>
        <a href="{{ route('admin.assignments.index') }}" class="md-btn-secondary">
            <i class="ti ti-arrow-left"></i> <span>Kembali</span>
        </a>
    </div>
</div>

{{-- Flash Alert --}}
@if($errors->any())
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> Terjadi kesalahan pada form pengisian. Silakan periksa kolom yang ditandai.
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-clipboard-list"></i>
        </div>
        <h6 class="md-form-head-title mb-0">Edit Terbit Tugas Siswa</h6>
    </div>

    <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="md-form-body">
            @if(!isset($assignmentBanks) || $assignmentBanks->count() === 0)
                <div class="alert alert-warning py-3 px-4 mb-4 rounded-3 d-flex align-items-center gap-3">
                    <div style="font-size:1.8rem;"><i class="ti ti-alert-circle"></i></div>
                    <div>
                        <div class="fw-bold mb-1">Bank Tugas Masih Kosong</div>
                        <div class="small mb-2">Anda belum memiliki master data tugas di Bank Tugas. Silakan buat master tugas terlebih dahulu.</div>
                        <a href="{{ route('admin.assignment-banks.create') }}" class="btn btn-sm btn-warning fw-bold">
                            <i class="ti ti-plus me-1"></i> Buat Master di Bank Tugas
                        </a>
                    </div>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <!-- Dropdown 1: Pilih Tugas dari Bank Tugas -->
                <div class="col-12 col-md-6">
                    <label for="assignment_bank_id" class="md-form-label fw-bold text-primary">
                        <i class="ti ti-notebook me-1"></i> Pilih Tugas (Bank Tugas) <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2 @error('assignment_bank_id') is-invalid @enderror" id="assignment_bank_id" name="assignment_bank_id" required>
                        <option value="">-- Pilih Tugas dari Bank Tugas --</option>
                        @if(isset($assignmentBanks))
                            @foreach($assignmentBanks as $ab)
                                @php
                                    $isMatched = old('assignment_bank_id') == $ab->id || (old('assignment_bank_id') === null && strtolower(trim($ab->title)) === strtolower(trim($assignment->title)));
                                @endphp
                                <option value="{{ $ab->id }}" {{ $isMatched ? 'selected' : '' }}>
                                    {{ $ab->title }} {{ $ab->subject ? '('.$ab->subject->name.')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('assignment_bank_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dropdown 2: Pilih Kelas Target -->
                <div class="col-12 col-md-6">
                    <label for="class_id" class="md-form-label fw-bold text-primary">
                        <i class="ti ti-school me-1"></i> Pilih Kelas Target <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2 @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                        <option value="">-- Pilih Kelas Penerima --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $assignment->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Batas Waktu (Deadline) -->
            <div class="mb-4">
                <label for="due_date" class="md-form-label fw-bold text-primary">
                    <i class="ti ti-calendar-event me-1"></i> Batas Waktu (Deadline) <span class="text-danger">*</span>
                </label>
                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}" required>
                <div class="md-form-hint">Tenggat waktu pengumpulan tugas oleh siswa di kelas target.</div>
                @error('due_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Preview Data Tugas Terpilih -->
            <div id="abPreviewCard" class="p-3 rounded-3 d-none mb-3" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;width:28px;height:28px;font-size:.8rem;">
                        <i class="ti ti-check"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="font-size:.85rem;color:var(--tblr-heading-color,#0f172a);">Ringkasan Tugas Terpilih</h6>
                </div>
                <div class="p-3 rounded border" style="background:var(--tblr-card-bg,#ffffff);border-color:var(--tblr-border-color,#e2e8f0) !important;font-size:0.85rem;">
                    <div class="fw-bold mb-1" style="color:var(--tblr-heading-color,#0f172a);font-size:0.92rem;" id="pvTitle">-</div>
                    <div class="mb-2" style="color:var(--tblr-text-muted,#64748b);" id="pvSubject"><i class="ti ti-book me-1"></i> -</div>
                    <div class="small text-muted" id="pvDescription"></div>
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.assignments.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan Tugas
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    $(function () {
        function updateAssignmentPreview(abId) {
            if (!abId) {
                $('#abPreviewCard').addClass('d-none');
                return;
            }

            $.ajax({
                url: '/admin/assignment-banks/' + abId + '/json',
                type: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: function (data) {
                    $('#pvTitle').text(data.title || '-');
                    $('#pvSubject').html('<i class="ti ti-book me-1"></i> Mata Pelajaran: ' + (data.subject_name || 'Umum'));
                    
                    if (data.description) {
                        $('#pvDescription').html('<strong>Instruksi:</strong> ' + data.description);
                    } else {
                        $('#pvDescription').html('<span class="text-muted">Tidak ada deskripsi/instruksi khusus.</span>');
                    }
                    $('#abPreviewCard').removeClass('d-none');
                },
                error: function () {
                    $('#abPreviewCard').addClass('d-none');
                }
            });
        }

        $('#assignment_bank_id').on('change', function () {
            updateAssignmentPreview($(this).val());
        });

        if ($('#assignment_bank_id').val()) {
            updateAssignmentPreview($('#assignment_bank_id').val());
        }
    });
</script>
@endpush
