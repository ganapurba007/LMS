@extends('layouts.be.master')
@section('header_title', 'Master Data — Tambah Bank Tugas')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-notebook"></i>
        </div>
        <div>
            <h5 class="md-title">Tambah Master Tugas</h5>
        </div>
    </div>
    <a href="{{ route('admin.assignment-banks.index') }}" class="md-btn-secondary">
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
        <h6 class="md-form-head-title mb-0">Informasi Master Bank Tugas</h6>
    </div>

    <form action="{{ route('admin.assignment-banks.store') }}" method="POST">
        @csrf

        <div class="md-form-body">
            <!-- Judul Tugas -->
            <div class="mb-3">
                <label for="title" class="md-form-label">Judul Tugas <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Misal: Tugas Proyek Pemrograman Laravel Rest API">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mapel -->
            <div class="mb-4">
                <label for="subject_id" class="md-form-label">Mata Pelajaran (Opsional)</label>
                <select class="form-select select2 @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
                    <option value="">-- Pilih Mata Pelajaran (Umum) --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }})
                        </option>
                    @endforeach
                </select>
                <div class="md-form-hint">Dapat dikosongkan jika tugas bersifat umum untuk berbagai mata pelajaran.</div>
                @error('subject_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Deskripsi / Instruksi Tugas -->
            <div class="mb-3">
                <label for="description" class="md-form-label">Deskripsi &amp; Instruksi Tugas</label>
                <textarea class="form-control tinymce @error('description') is-invalid @enderror" id="description" name="description" rows="6" placeholder="Tuliskan instruksi tugas secara rinci di sini...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.assignment-banks.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Simpan Master Tugas
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
