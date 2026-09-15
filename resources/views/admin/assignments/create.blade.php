@extends('layouts.be.master')
@section('header_title', 'Master Data — Buat Tugas Baru')

@section('content')
@include('admin._partials.master-data-styles')

<div class="col-md-12">
    {{-- Page Header --}}
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                <i class="ti ti-clipboard-plus"></i>
            </div>
            <div>
                <h5 class="md-title">Buat Tugas Siswa Baru</h5>
            </div>
        </div>
        <a href="{{ route('admin.assignments.index') }}" class="md-btn-secondary">
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
            <div class="md-form-head-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                <i class="ti ti-clipboard-list"></i>
            </div>
            <h6 class="md-form-head-title mb-0">Informasi Tugas Siswa</h6>
        </div>

        <form action="{{ route('admin.assignments.store') }}" method="POST">
            @csrf

            <div class="md-form-body">
                <!-- Judul Tugas -->
                <div class="mb-3">
                    <label for="title" class="md-form-label">Judul Tugas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Tugas Mandiri Bab 1 Aljabar">
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
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
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
                <div class="mb-3">
                    <label for="due_date" class="md-form-label">Batas Waktu (Deadline)</label>
                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                    <div class="md-form-hint">Tenggat waktu pengumpulan tugas oleh siswa. Kosongkan jika tanpa batas waktu.</div>
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Deskripsi / Petunjuk Pengerjaan -->
                <div class="mb-3">
                    <label for="description" class="md-form-label">Deskripsi &amp; Instruksi Pengerjaan</label>
                    <textarea class="form-control tinymce @error('description') is-invalid @enderror" id="description" name="description" rows="6" placeholder="Tuliskan petunjuk atau rincian soal tugas di sini...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="md-form-footer">
                <a href="{{ route('admin.assignments.index') }}" class="md-btn-light">Batal</a>
                <button type="submit" class="md-btn-submit">
                    <i class="ti ti-device-floppy"></i> Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
