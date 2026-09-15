@extends('layouts.be.master')
@section('header_title', 'Master Data — Tambah Materi Pembelajaran')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                  <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Tambah Materi Baru</h5>
        </div>
    </div>
    <a href="{{ route('admin.materials.index') }}" class="md-btn-secondary">
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
            <i class="ti ti-file-text"></i>
        </div>
        <h6 class="md-form-head-title mb-0">Informasi Materi Pembelajaran</h6>
    </div>

    <form action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="md-form-body">
            <!-- Judul Materi -->
            <div class="mb-3">
                <label for="title" class="md-form-label">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Misal: Pengenalan Algoritma & Pemrograman Dasar">
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

            <!-- Urutan Tampil -->
            <div class="mb-4">
                <label for="order" class="md-form-label">Urutan Tampil</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0" placeholder="0">
                <div class="md-form-hint">Materi dengan nomor urutan lebih kecil akan ditampilkan lebih dahulu kepada siswa.</div>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="p-3 mb-4 rounded-3" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;width:30px;height:30px;font-size:.85rem;">
                        <i class="ti ti-layers-intersect"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="font-size:.85rem;color:var(--tblr-heading-color,#0f172a);">Konten &amp; Media Pembelajaran</h6>
                </div>

                <!-- Teks / Artikel -->
                <div class="mb-3">
                    <textarea class="form-control tinymce @error('content') is-invalid @enderror" id="content" name="content" rows="6" placeholder="Tuliskan teks, instruksi atau artikel materi pelajaran di sini...">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <!-- YouTube Video -->
                    <div class="col-12 col-md-6">
                        <label for="video_url" class="md-form-label"><i class="ti ti-brand-youtube text-danger me-1"></i> URL Video YouTube (Opsional)</label>
                        <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dokumen File -->
                    <div class="col-12 col-md-6">
                        <label for="document_file" class="md-form-label"><i class="ti ti-file-download text-warning me-1"></i> File Dokumen Lampiran (Maks 20MB)</label>
                        <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file">
                        <div class="md-form-hint">Format didukung: PDF, DOCX, PPTX, XLSX, PNG, JPG.</div>
                        @error('document_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.materials.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Simpan Materi
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
