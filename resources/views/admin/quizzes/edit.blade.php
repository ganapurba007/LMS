@extends('layouts.be.master')
@section('header_title', 'Master Data — Edit Pengaturan Kuis')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Pengaturan Kuis</h5>
        </div>
    </div>
    <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> <span>Kembali</span>
    </a>
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
        <div class="md-form-head-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
            <i class="ti ti-clipboard-check"></i>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h6 class="md-form-head-title mb-0">Pengaturan Kuis ID #{{ $quiz->id }}</h6>
            <span class="md-badge blue">
                {{ $quiz->questions_count }} Soal
            </span>
        </div>
    </div>

    <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="md-form-body">
            <!-- Judul Kuis -->
            <div class="mb-3">
                <label for="title" class="md-form-label">Judul Kuis / Ujian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $quiz->title) }}" required placeholder="Contoh: Kuis Harian Bab 2 Persamaan Kuadrat">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mapel & Kelas -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label for="subject_id" class="md-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $quiz->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="class_id" class="md-form-label">Kelas Target <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $quiz->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Durasi, Poin, Deadline -->
            <div class="row g-3 mb-2">
                <div class="col-12 col-md-4">
                    <label for="duration_minutes" class="md-form-label">Durasi Pengerjaan (Menit) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" required>
                    <div class="md-form-hint">Waktu hitung mundur pengerjaan kuis siswa.</div>
                    @error('duration_minutes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="points_per_question" class="md-form-label">Poin per Butir Soal <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('points_per_question') is-invalid @enderror" id="points_per_question" name="points_per_question" value="{{ old('points_per_question', $quiz->points_per_question) }}" min="1" required>
                    <div class="md-form-hint">Bobot nilai setiap jawaban benar.</div>
                    @error('points_per_question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="deadline" class="md-form-label">Batas Waktu (Deadline) <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', $quiz->deadline ? $quiz->deadline->format('Y-m-d\TH:i') : '') }}" required>
                    <div class="md-form-hint">Tenggat akhir pengisian kuis.</div>
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.quizzes.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
