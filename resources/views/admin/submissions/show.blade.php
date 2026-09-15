@extends('layouts.be.master')
@section('header_title', 'Master Data — Koreksi & Penilaian Tugas')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;">
            <i class="ti ti-pencil-check"></i>
        </div>
        <div>
            <h5 class="md-title">Koreksi Tugas: {{ $submission->assignment->title ?? 'Tugas' }}</h5>
        </div>
    </div>
    <a href="{{ route('admin.submissions.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> <span>Kembali</span>
    </a>
</div>

{{-- Flash Alert --}}
@if(session('success'))
    <div class="md-alert success mb-4">
        <i class="ti ti-check-circle"></i> {{ session('success') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif
@if($errors->any())
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> Terjadi kesalahan dalam penginputan nilai. Silakan periksa kembali formulir.
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Informasi Pengumpulan & Jawaban Siswa -->
    <div class="col-12 col-lg-7">
        <div class="md-card mb-4">
            <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;width:32px;height:32px;font-size:.9rem;">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="font-size:.9rem;color:var(--tblr-heading-color,#0f172a);">Informasi Pengumpulan Siswa</h6>
                </div>
                @if(!is_null($submission->grade))
                    <span class="md-badge teal fw-bold">
                        <i class="ti ti-circle-check"></i> Nilai: {{ number_format($submission->grade, 1) }} / 100
                    </span>
                @else
                    <span class="md-badge amber">
                        <i class="ti ti-clock"></i> Belum Dinilai
                    </span>
                @endif
            </div>

            <div class="p-3 p-md-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold" style="font-size:.68rem;">Nama Siswa</span>
                        <div class="fw-bold text-dark mt-0.5" style="font-size:.88rem;">{{ $submission->student->name ?? '-' }}</div>
                        <div class="text-muted" style="font-size:.75rem;">{{ $submission->student->email ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold" style="font-size:.68rem;">Kelas Target</span>
                        <div class="fw-bold text-dark mt-0.5" style="font-size:.88rem;">{{ $submission->assignment->schoolClass->name ?? '-' }}</div>
                        <div class="text-muted" style="font-size:.75rem;">Mata Pelajaran: {{ $submission->assignment->subject->name ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold" style="font-size:.68rem;">Judul Tugas</span>
                        <div class="fw-bold text-primary mt-0.5" style="font-size:.88rem;">{{ $submission->assignment->title ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold" style="font-size:.68rem;">Waktu Pengumpulan</span>
                        <div class="fw-bold text-dark mt-0.5" style="font-size:.88rem;">
                            <i class="ti ti-clock me-1 text-warning-emphasis"></i>
                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i:s') : '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="md-form-label mb-2">
                        <i class="ti ti-file-text text-primary me-1"></i> Jawaban / Catatan Pengumpulan Siswa:
                    </label>
                    <div class="p-3 rounded-3" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);min-height:120px;font-size:.84rem;line-height:1.6;color:var(--tblr-body-color,#1e293b);white-space:pre-wrap;">{{ $submission->answer_text ?: 'Siswa tidak menyertakan catatan jawaban teks.' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Form Penilaian & Feedback Guru -->
    <div class="col-12 col-lg-5">
        <div class="md-form-card w-100" style="max-width:100%;">
            <div class="md-form-head">
                <div class="md-form-head-icon" style="background:rgba(12,166,120,.1);color:#0ca678;">
                    <i class="ti ti-pencil-check"></i>
                </div>
                <h6 class="md-form-head-title mb-0">Form Penilaian Evaluasi</h6>
            </div>

            <form action="{{ route('admin.submissions.grade', $submission) }}" method="POST">
                @csrf

                <div class="md-form-body">
                    <!-- Nilai Siswa -->
                    <div class="mb-3">
                        <label for="grade" class="md-form-label">Nilai Tugas (Skala 0 - 100) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" 
                                   class="form-control @error('grade') is-invalid @enderror" 
                                   id="grade" name="grade" 
                                   value="{{ old('grade', $submission->grade) }}" 
                                   required placeholder="Contoh: 85.0">
                            <span class="input-group-text fw-bold text-muted" style="background:var(--tblr-body-bg,#f8fafc);font-size:.82rem;">/ 100</span>
                        </div>
                        <div class="md-form-hint">Masukkan nilai angka mulai dari 0 hingga 100.</div>
                        @error('grade')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Feedback / Umpan Balik -->
                    <div class="mb-2">
                        <label for="feedback" class="md-form-label">Catatan &amp; Umpan Balik Guru (Opsional)</label>
                        <textarea class="form-control @error('feedback') is-invalid @enderror" 
                                  id="feedback" name="feedback" rows="5" 
                                  placeholder="Berikan saran evaluasi, komentar perbaikan, atau apresiasi pengerjaan siswa...">{{ old('feedback', $submission->feedback) }}</textarea>
                        <div class="md-form-hint">Umpan balik ini akan dapat dilihat langsung oleh siswa pada akun mereka.</div>
                        @error('feedback')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="md-form-footer">
                    <a href="{{ route('admin.submissions.index') }}" class="md-btn-light">Batal</a>
                    <button type="submit" class="md-btn-submit">
                        <i class="ti ti-device-floppy"></i> Simpan Nilai &amp; Umpan Balik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')
@endsection
