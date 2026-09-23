@extends('layouts.be.master')

@section('header_title', 'Hasil & Status Siswa — ' . $quiz->title)

@section('content')
@include('admin._partials.master-data-styles')

<style>
    /* ── Dark Mode Global Text & Background Overrides for Tabler ── */
    [data-theme="dark"] .text-dark,
    [data-bs-theme="dark"] .text-dark,
    body.theme-dark .text-dark,
    body.dark-mode .text-dark {
        color: var(--tblr-heading-color, #f8fafc) !important;
    }

    [data-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .bg-white,
    body.theme-dark .bg-white,
    body.dark-mode .bg-white {
        background-color: var(--tblr-card-bg, #1e293b) !important;
    }

    [data-theme="dark"] .bg-light,
    [data-bs-theme="dark"] .bg-light,
    body.theme-dark .bg-light,
    body.dark-mode .bg-light {
        background-color: var(--tblr-bg-surface-secondary, rgba(255, 255, 255, 0.03)) !important;
    }

    /* ── Page Header & Title Contrast in Light & Dark Mode ── */
    .md-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(15, 45, 65, 0.4) !important;
        margin: 0;
        line-height: 1.25;
    }
    [data-theme="dark"] .md-title,
    [data-bs-theme="dark"] .md-title,
    body.theme-dark .md-title,
    body.dark-mode .md-title {
        color: #f8fafc !important;
        text-shadow: none !important;
    }

    .md-page-icon.quiz-page-icon {
        background: #ffffff !important;
        color: #16465c !important;
        border: 1px solid rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 2px 8px rgba(15, 45, 65, 0.15) !important;
    }
    [data-theme="dark"] .md-page-icon.quiz-page-icon,
    [data-bs-theme="dark"] .md-page-icon.quiz-page-icon,
    body.theme-dark .md-page-icon.quiz-page-icon,
    body.dark-mode .md-page-icon.quiz-page-icon {
        background: rgba(102, 163, 191, 0.18) !important;
        color: #66A3BF !important;
        border-color: rgba(102, 163, 191, 0.3) !important;
        box-shadow: none !important;
    }

    /* ── Badges Header: Kelas & Mata Pelajaran (Lebih Kecil & Compact) ── */
    .quiz-badge-class,
    .quiz-badge-subject {
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 600;
        line-height: 1.25;
        white-space: nowrap;
        letter-spacing: 0.1px;
        transition: all 0.18s ease;
    }
    .quiz-badge-class:hover,
    .quiz-badge-subject:hover {
        transform: translateY(-1px);
    }

    /* Kelas Badge (Light Mode) */
    .quiz-badge-class {
        background: #ffffff !important;
        border: 1px solid #bfdbfe !important;
        color: #1e40af !important;
        box-shadow: 0 1px 4px rgba(15, 45, 65, 0.1) !important;
    }
    .quiz-badge-class i {
        color: #2563eb !important;
        font-size: 0.82rem;
    }

    /* Mata Pelajaran Badge (Light Mode) */
    .quiz-badge-subject {
        background: #ffffff !important;
        border: 1px solid #bbf7d0 !important;
        color: #166534 !important;
        box-shadow: 0 1px 4px rgba(15, 45, 65, 0.1) !important;
    }
    .quiz-badge-subject i {
        color: #16a34a !important;
        font-size: 0.82rem;
    }

    /* Kelas Badge (Dark Mode) */
    [data-theme="dark"] .quiz-badge-class,
    [data-bs-theme="dark"] .quiz-badge-class,
    body.theme-dark .quiz-badge-class,
    body.dark-mode .quiz-badge-class {
        background: rgba(30, 41, 59, 0.95) !important;
        border: 1px solid #3b82f6 !important;
        color: #dbeafe !important;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35) !important;
    }
    [data-theme="dark"] .quiz-badge-class i,
    [data-bs-theme="dark"] .quiz-badge-class i,
    body.theme-dark .quiz-badge-class i,
    body.dark-mode .quiz-badge-class i {
        color: #93c5fd !important;
    }

    /* Mata Pelajaran Badge (Dark Mode) */
    [data-theme="dark"] .quiz-badge-subject,
    [data-bs-theme="dark"] .quiz-badge-subject,
    body.theme-dark .quiz-badge-subject,
    body.dark-mode .quiz-badge-subject {
        background: rgba(30, 41, 59, 0.95) !important;
        border: 1px solid #10b981 !important;
        color: #d1fae5 !important;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.35) !important;
    }
    [data-theme="dark"] .quiz-badge-subject i,
    [data-bs-theme="dark"] .quiz-badge-subject i,
    body.theme-dark .quiz-badge-subject i,
    body.dark-mode .quiz-badge-subject i {
        color: #6ee7b7 !important;
    }

    /* ── Analisis Performa Soal: 5 Terbanyak Salah & Benar (Responsive & Neat) ── */
    .quiz-analytics-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        background: var(--tblr-card-bg, #ffffff);
        box-shadow: 0 2px 8px rgba(15, 45, 65, 0.05);
        transition: all 0.2s ease;
    }
    .quiz-analytics-card:hover {
        box-shadow: 0 4px 14px rgba(15, 45, 65, 0.08);
    }
    [data-theme="dark"] .quiz-analytics-card,
    [data-bs-theme="dark"] .quiz-analytics-card,
    body.theme-dark .quiz-analytics-card,
    body.dark-mode .quiz-analytics-card {
        background: var(--tblr-card-bg, #1e293b);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .quiz-analytics-header {
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    }
    .quiz-analytics-header.danger {
        background: linear-gradient(180deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.01) 100%);
        border-bottom-color: rgba(239, 68, 68, 0.15);
    }
    .quiz-analytics-header.success {
        background: linear-gradient(180deg, rgba(16, 185, 129, 0.06) 0%, rgba(16, 185, 129, 0.01) 100%);
        border-bottom-color: rgba(16, 185, 129, 0.15);
    }
    [data-theme="dark"] .quiz-analytics-header.danger,
    [data-bs-theme="dark"] .quiz-analytics-header.danger,
    body.theme-dark .quiz-analytics-header.danger,
    body.dark-mode .quiz-analytics-header.danger {
        background: rgba(239, 68, 68, 0.12);
        border-bottom-color: rgba(239, 68, 68, 0.25);
    }
    [data-theme="dark"] .quiz-analytics-header.success,
    [data-bs-theme="dark"] .quiz-analytics-header.success,
    body.theme-dark .quiz-analytics-header.success,
    body.dark-mode .quiz-analytics-header.success {
        background: rgba(16, 185, 129, 0.12);
        border-bottom-color: rgba(16, 185, 129, 0.25);
    }

    .qa-item {
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid var(--tblr-border-color, #f1f5f9);
        transition: background-color 0.15s ease;
    }
    .qa-item:last-child {
        border-bottom: none;
    }
    .qa-item:hover {
        background-color: var(--tblr-bg-surface-secondary, rgba(15, 45, 65, 0.02));
    }
    [data-theme="dark"] .qa-item,
    [data-bs-theme="dark"] .qa-item,
    body.theme-dark .qa-item,
    body.dark-mode .qa-item {
        border-bottom-color: rgba(255, 255, 255, 0.05);
    }
    [data-theme="dark"] .qa-item:hover,
    [data-bs-theme="dark"] .qa-item:hover,
    body.theme-dark .qa-item:hover,
    body.dark-mode .qa-item:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }

    .qa-rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
        flex-shrink: 0;
    }
    .qa-rank-badge.danger {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
    }
    .qa-rank-badge.success {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #86efac;
    }
    [data-theme="dark"] .qa-rank-badge.danger,
    [data-bs-theme="dark"] .qa-rank-badge.danger,
    body.theme-dark .qa-rank-badge.danger,
    body.dark-mode .qa-rank-badge.danger {
        background: rgba(220, 38, 38, 0.25);
        color: #fca5a5;
        border-color: rgba(248, 113, 113, 0.4);
    }
    [data-theme="dark"] .qa-rank-badge.success,
    [data-bs-theme="dark"] .qa-rank-badge.success,
    body.theme-dark .qa-rank-badge.success,
    body.dark-mode .qa-rank-badge.success {
        background: rgba(22, 163, 74, 0.25);
        color: #86efac;
        border-color: rgba(74, 222, 128, 0.4);
    }

    .qa-text {
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.45;
        color: var(--tblr-heading-color, #0f172a);
        word-break: break-word;
    }
    [data-theme="dark"] .qa-text,
    [data-bs-theme="dark"] .qa-text,
    body.theme-dark .qa-text,
    body.dark-mode .qa-text {
        color: #f1f5f9;
    }

    .qa-meta-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.45rem;
    }
    @media (max-width: 576px) {
        .qa-meta-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.35rem;
        }
    }

    /* ── Modal Detail Jawaban Siswa Styling ── */
    .modal-metric-strip {
        padding: 0.75rem 1rem;
        background: var(--tblr-bg-surface-secondary, rgba(15, 45, 65, 0.03));
        border-radius: 10px;
        border: 1px solid var(--tblr-border-color, #e2e8f0);
    }
    [data-theme="dark"] .modal-metric-strip,
    [data-bs-theme="dark"] .modal-metric-strip,
    body.theme-dark .modal-metric-strip,
    body.dark-mode .modal-metric-strip {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .modal-score-box {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border: 1px solid #7dd3fc;
        border-radius: 10px;
        padding: 0.35rem 0.8rem;
        text-align: center;
        min-width: 68px;
    }
    [data-theme="dark"] .modal-score-box,
    [data-bs-theme="dark"] .modal-score-box,
    body.theme-dark .modal-score-box,
    body.dark-mode .modal-score-box {
        background: rgba(2, 132, 199, 0.2);
        border-color: rgba(56, 189, 248, 0.4);
    }

    .filter-pill {
        cursor: pointer;
        font-size: 0.73rem;
        font-weight: 600;
        transition: all 0.15s ease;
        user-select: none;
    }
    .filter-pill:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }
    .filter-pill.active {
        background: var(--tblr-primary, #0284c7) !important;
        color: #ffffff !important;
        border-color: var(--tblr-primary, #0284c7) !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
    }

    .answer-card {
        background: var(--tblr-card-bg, #ffffff);
        border: 1px solid var(--tblr-border-color, #e2e8f0);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 3px rgba(15, 45, 65, 0.04);
        transition: all 0.15s ease;
    }
    [data-theme="dark"] .answer-card,
    [data-bs-theme="dark"] .answer-card,
    body.theme-dark .answer-card,
    body.dark-mode .answer-card {
        background: rgba(30, 41, 59, 0.6);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: none;
    }

    .opt-box {
        padding: 0.55rem 0.85rem;
        border-radius: 8px;
        margin-top: 0.35rem;
        font-size: 0.82rem;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        word-break: break-word;
    }
    .opt-box.normal {
        background: var(--tblr-bg-surface-secondary, #f8fafc);
        border-color: var(--tblr-border-color, #e2e8f0);
        color: var(--tblr-body-color, #334155);
    }
    [data-theme="dark"] .opt-box.normal,
    [data-bs-theme="dark"] .opt-box.normal,
    body.theme-dark .opt-box.normal,
    body.dark-mode .opt-box.normal {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
    }
    .opt-box.correct-target {
        background: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.35);
        color: #065f46;
    }
    [data-theme="dark"] .opt-box.correct-target,
    [data-bs-theme="dark"] .opt-box.correct-target,
    body.theme-dark .opt-box.correct-target,
    body.dark-mode .opt-box.correct-target {
        background: rgba(16, 185, 129, 0.18);
        border-color: rgba(52, 211, 153, 0.4);
        color: #6ee7b7;
    }
    .opt-box.wrong-choice {
        background: rgba(239, 68, 68, 0.08);
        border-color: rgba(239, 68, 68, 0.35);
        color: #991b1b;
    }
    [data-theme="dark"] .opt-box.wrong-choice,
    [data-bs-theme="dark"] .opt-box.wrong-choice,
    body.theme-dark .opt-box.wrong-choice,
    body.dark-mode .opt-box.wrong-choice {
        background: rgba(239, 68, 68, 0.18);
        border-color: rgba(248, 113, 113, 0.4);
        color: #fca5a5;
    }
</style>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon quiz-page-icon">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                    <!-- Badge Kelas -->
                    <span class="quiz-badge-class" title="Kelas Target">
                        <i class="ti ti-school"></i> <span>{{ $quiz->schoolClass->name ?? $quiz->classroom->name ?? 'Semua Kelas' }}</span>
                    </span>
                    <!-- Badge Mata Pelajaran -->
                    <span class="quiz-badge-subject" title="Mata Pelajaran">
                        <i class="ti ti-book"></i> <span>{{ $quiz->subject->name ?? 'Mata Pelajaran' }}</span>
                    </span>
                </div>
                <h5 class="md-title">Hasil &amp; Status Siswa: {{ $quiz->title }}</h5>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary">
                <i class="ti ti-arrow-left"></i> <span>Kembali</span>
            </a>
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="md-btn-primary">
                <i class="ti ti-list-check"></i> <span>Kelola Soal</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert md-alert success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert md-alert danger alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quiz Details Overview Cards -->
    @php
        $totalStudents = $students->count();
        $submittedCount = $attempts->filter(fn($a) => !is_null($a->submitted_at))->count();
        $inProgressCount = $attempts->filter(fn($a) => is_null($a->submitted_at))->count();
        $notStartedCount = $totalStudents - $attempts->count();
        $avgScore = $submittedCount > 0 ? round($attempts->filter(fn($a) => !is_null($a->submitted_at))->avg('score'), 1) : 0;
        $isDeadlinePast = $quiz->deadline && now()->greaterThan($quiz->deadline);
    @endphp

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="metric-icon-box blue">
                        <i class="ti ti-users fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Total Siswa Kelas</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $totalStudents }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">Siswa</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="metric-icon-box green">
                        <i class="ti ti-circle-check fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Sudah Selesai</div>
                        <div class="fw-bold text-success mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $submittedCount }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ {{ $totalStudents }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="metric-icon-box amber">
                        <i class="ti ti-clock fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Sedang / Belum</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $inProgressCount }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">Proses | {{ $notStartedCount }} Belum</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="metric-icon-box teal">
                        <i class="ti ti-award fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Rata-rata Nilai</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $avgScore }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ 100</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analisis Performa Soal (5 Terbanyak Salah & 5 Terbanyak Benar) -->
    <div class="row g-3 mb-4">
        <!-- 5 Soal Paling Banyak Dijawab Salah -->
        <div class="col-12 col-xl-6">
            <div class="card md-card quiz-analytics-card danger-card h-100">
                <div class="quiz-analytics-header danger d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: #dc2626; width: 28px; height: 28px;">
                            <i class="ti ti-alert-circle fs-6"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                            5 Soal Terbanyak Dijawab Salah
                        </h6>
                    </div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 font-semibold small">
                        Evaluasi Butuh Remedial
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($submittedCount == 0 || $mostIncorrectQuestions->isEmpty())
                        <div class="p-4 text-center text-muted small">
                            <i class="ti ti-info-circle fs-3 text-secondary d-block mb-1"></i>
                            Belum ada pengerjaan kuis selesai untuk menganalisis soal terbanyak salah.
                        </div>
                    @else
                        @foreach($mostIncorrectQuestions as $idx => $stat)
                            <div class="qa-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="qa-rank-badge danger mt-0.5">#{{ $idx + 1 }}</div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="qa-text mb-1">
                                            {!! $stat['question']->summary_text !!}
                                        </div>
                                        <div class="qa-meta-bar">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 font-semibold" style="font-size: 0.72rem;">
                                                <i class="ti ti-x me-0.5"></i> {{ $stat['incorrect_count'] }} dari {{ $submittedCount }} Siswa Salah ({{ $stat['incorrect_pct'] }}%)
                                            </span>
                                            <div class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="ti ti-key text-success"></i> <span>Kunci:</span> <strong class="text-success text-truncate" style="max-width: 250px;" title="{!! strip_tags($stat['correct_answer_text']) !!}">{!! strip_tags($stat['correct_answer_text']) !!}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- 5 Soal Paling Banyak Dijawab Benar -->
        <div class="col-12 col-xl-6">
            <div class="card md-card quiz-analytics-card success-card h-100">
                <div class="quiz-analytics-header success d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: #059669; width: 28px; height: 28px;">
                            <i class="ti ti-circle-check fs-6"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.92rem;">
                            5 Soal Terbanyak Dijawab Benar
                        </h6>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 font-semibold small">
                        Pemahaman Baik
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($submittedCount == 0 || $mostCorrectQuestions->isEmpty())
                        <div class="p-4 text-center text-muted small">
                            <i class="ti ti-info-circle fs-3 text-secondary d-block mb-1"></i>
                            Belum ada pengerjaan kuis selesai untuk menganalisis soal terbanyak benar.
                        </div>
                    @else
                        @foreach($mostCorrectQuestions as $idx => $stat)
                            <div class="qa-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="qa-rank-badge success mt-0.5">#{{ $idx + 1 }}</div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="qa-text mb-1">
                                            {!! $stat['question']->summary_text !!}
                                        </div>
                                        <div class="qa-meta-bar">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 font-semibold" style="font-size: 0.72rem;">
                                                <i class="ti ti-check me-0.5"></i> {{ $stat['correct_count'] }} dari {{ $submittedCount }} Siswa Benar ({{ $stat['correct_pct'] }}%)
                                            </span>
                                            <div class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="ti ti-key text-success"></i> <span>Kunci:</span> <strong class="text-success text-truncate" style="max-width: 250px;" title="{!! strip_tags($stat['correct_answer_text']) !!}">{!! strip_tags($stat['correct_answer_text']) !!}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card md-card">
        <div class="card-header border-bottom py-3 px-3.5 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2" style="background: var(--tblr-card-bg, #ffffff);">
            <div>
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.92rem;">
                    <i class="ti ti-user-check text-primary fs-5"></i> Daftar Siswa &amp; Hasil Kuis
                </h6>
                <div class="text-muted mt-0.5" style="font-size: 0.78rem;">
                    Batas Waktu (Deadline): <span class="fw-semibold text-dark">{{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') : '-' }}</span>
                    @if($isDeadlinePast)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1 px-2 py-0.5" style="font-size: 0.68rem;">Deadline Berakhir</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 px-2 py-0.5" style="font-size: 0.68rem;">Aktif</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table md-table table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="md-th-no text-center" style="width: 55px;">NO</th>
                            <th style="min-width: 180px;">NAMA SISWA</th>
                            <th style="width: 160px;">STATUS</th>
                            <th style="min-width: 200px;">WAKTU PENGERJAAN</th>
                            <th style="width: 110px;">DURASI</th>
                            <th class="text-center" style="width: 130px;">NILAI / SKOR</th>
                            <th class="md-th-action text-center" style="width: 130px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $attempt = $attempts->get($student->id);
                            @endphp
                            <tr>
                                <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ $student->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $student->email }}</div>
                                </td>
                                <td>
                                    @if(!$attempt)
                                        <span class="md-badge gray">
                                            <i class="ti ti-minus me-0.5"></i> Belum Mengerjakan
                                        </span>
                                    @elseif(!$attempt->submitted_at)
                                        <span class="md-badge amber">
                                            <i class="ti ti-clock me-0.5"></i> Sedang Mengerjakan
                                        </span>
                                    @else
                                        <span class="md-badge green">
                                            <i class="ti ti-circle-check me-0.5"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt)
                                        <div style="font-size: 0.78rem; line-height: 1.35;">
                                            <div class="text-dark"><i class="ti ti-playstation-square text-primary me-1"></i> Mulai: {{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</div>
                                            @if($attempt->submitted_at)
                                                <div class="text-muted"><i class="ti ti-checkbox text-success me-1"></i> Selesai: {{ $attempt->submitted_at->format('d M Y H:i') }}</div>
                                            @else
                                                <div class="text-warning fw-semibold"><i class="ti ti-loader text-warning me-1"></i> Dalam Proses...</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt && $attempt->submitted_at && $attempt->started_at)
                                        <span class="text-dark fw-semibold" style="font-size: 0.78rem;">
                                            {{ $attempt->duration_formatted }}
                                        </span>
                                    @elseif($attempt && $attempt->started_at)
                                        <span class="text-warning fw-semibold" style="font-size: 0.78rem;">
                                            {{ $attempt->started_at->diffForHumans(null, true) }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($attempt && !is_null($attempt->submitted_at))
                                        <span class="md-badge blue fw-bold" style="font-size: 0.82rem; padding: 0.3rem 0.65rem;">
                                            {{ $attempt->score }} <span class="fw-normal text-muted" style="font-size: 0.7rem;">/ 100</span>
                                        </span>
                                    @elseif($attempt)
                                        <span class="md-badge amber">
                                            Belum Submit
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td class="md-td-action text-center">
                                    <div class="md-action-group text-center d-flex align-items-center justify-content-center gap-1">
                                        @if($attempt)
                                            <!-- Tombol lihat jawaban kuis siswa -->
                                            <button type="button" 
                                                    class="md-icon-btn blue" 
                                                    onclick="openStudentAnswersModal('{{ route('admin.quizzes.students.answers', [$quiz, $student]) }}', '{{ addslashes($student->name) }}')" 
                                                    title="Lihat Detail Jawaban Kuis Siswa">
                                                <i class="ti ti-eye"></i>
                                            </button>

                                            <!-- Tombol Reset Pengerjaan Siswa -->
                                            <button type="button" 
                                                    class="md-icon-btn red" 
                                                    onclick="openResetAttemptModal('{{ route('admin.quizzes.students.reset', [$quiz, $student]) }}', '{{ addslashes($student->name) }}', {{ $isDeadlinePast ? 'true' : 'false' }})" 
                                                    title="Reset Pengerjaan Siswa">
                                                <i class="ti ti-rotate-2"></i>
                                            </button>
                                        @else
                                            <button type="button" class="md-icon-btn gray" disabled title="Siswa belum mengerjakan kuis" style="opacity: 0.4; cursor: not-allowed;">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            <button type="button" class="md-icon-btn gray" disabled title="Siswa belum mengerjakan kuis" style="opacity: 0.4; cursor: not-allowed;">
                                                <i class="ti ti-rotate-2"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="md-empty-row">
                                    <div class="md-empty-state">
                                        <div class="md-empty-icon-wrap purple">
                                            <i class="ti ti-users-minus"></i>
                                        </div>
                                        <div class="md-empty-title">Belum Ada Siswa Terdaftar</div>
                                        <div class="md-empty-desc">Belum ada siswa yang terdaftar pada kelas <strong>{{ $quiz->schoolClass->name ?? 'ini' }}</strong>.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Konfirmasi Reset Pengerjaan Siswa -->
<div class="modal fade" id="modalResetAttempt" tabindex="-1" aria-labelledby="modalResetAttemptLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-body text-center p-4">
                <div class="md-modal-icon amber mx-auto mb-3">
                    <i class="ti ti-rotate-2"></i>
                </div>
                <h5 class="md-modal-title mb-2">Reset Pengerjaan?</h5>
                <p class="md-modal-text text-muted small mb-3" id="resetModalStudentText">Siswa akan dapat mengerjakan ulang kuis dari awal.</p>
                <div id="resetModalDeadlineWarning" class="alert md-alert warning p-2.5 small mb-3 text-start d-none" style="font-size: 0.78rem;">
                    <i class="ti ti-alert-triangle me-1"></i> <strong>Perhatian:</strong> Batas waktu (deadline) kuis ini sudah berakhir. Agar siswa bisa mengerjakan ulang, perpanjang batas waktu di menu Edit Kuis.
                </div>
                <form id="resetAttemptForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="md-btn-danger">
                            <i class="ti ti-rotate-2"></i> <span>Ya, Reset</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Detail Jawaban Siswa -->
<div class="modal fade" id="modalStudentAnswers" tabindex="-1" aria-labelledby="modalStudentAnswersLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content md-modal-content" style="background: var(--tblr-card-bg, #ffffff); border: 1px solid var(--tblr-border-color, #e2e8f0);">
            <div class="modal-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between" style="border-color: var(--tblr-border-color, #e2e8f0) !important;">
                <div>
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" style="font-size: 1.05rem;" id="studentAnswerModalTitle">
                        <i class="ti ti-file-text text-primary"></i> Detail Jawaban Siswa
                    </h5>
                    <div class="text-muted small mt-0.5" id="studentAnswerModalSubtitle">Analisis Lembar Jawaban Kuis</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3 p-md-4">
                <!-- Metrics Summary Strip Inside Modal -->
                <div class="modal-metric-strip d-flex align-items-center justify-content-between flex-wrap gap-2.5 mb-3" id="studentModalMetricStrip">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-score-box">
                            <div class="text-muted" style="font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">NILAI</div>
                            <div class="fw-extrabold text-primary" style="font-size: 1.35rem; line-height: 1.1;" id="modalStudentScore">0</div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0.5" id="modalStudentName" style="font-size: 0.92rem;">-</div>
                            <div class="text-muted small" style="font-size: 0.76rem;" id="modalStudentTimeInfo">Waktu Submit: -</div>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                        <span class="badge filter-pill active px-2.5 py-1.5 rounded-pill" onclick="filterModalAnswers('all', this)">
                            Semua Soal
                        </span>
                        <span class="badge filter-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill" onclick="filterModalAnswers('correct', this)">
                            <i class="ti ti-check me-0.5"></i> Benar (<span id="countModalCorrect">0</span>)
                        </span>
                        <span class="badge filter-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill" onclick="filterModalAnswers('wrong', this)">
                            <i class="ti ti-x me-0.5"></i> Salah (<span id="countModalWrong">0</span>)
                        </span>
                        <span class="badge filter-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill" onclick="filterModalAnswers('unanswered', this)">
                            Belum (<span id="countModalUnanswered">0</span>)
                        </span>
                    </div>
                </div>

                <!-- Container Questions List -->
                <div id="modalAnswersLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted small mt-2">Memuat jawaban siswa...</div>
                </div>

                <div id="modalAnswersList" class="d-none">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <div class="modal-footer border-top py-2.5 px-4" style="background: var(--tblr-bg-surface-secondary, rgba(0,0,0,0.02)); border-color: var(--tblr-border-color, #e2e8f0) !important;">
                <button type="button" class="md-btn-light ms-auto" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openResetAttemptModal(url, studentName, isDeadlinePast) {
        const form = document.getElementById('resetAttemptForm');
        form.action = url;
        const textEl = document.getElementById('resetModalStudentText');
        if (textEl && studentName) {
            textEl.innerText = `Anda akan menghapus hasil & riwayat pengerjaan kuis untuk siswa "${studentName}".`;
        }
        const warningEl = document.getElementById('resetModalDeadlineWarning');
        if (warningEl) {
            if (isDeadlinePast) {
                warningEl.classList.remove('d-none');
            } else {
                warningEl.classList.add('d-none');
            }
        }
        const modal = new bootstrap.Modal(document.getElementById('modalResetAttempt'));
        modal.show();
    }

    let currentAnswersData = [];

    function openStudentAnswersModal(fetchUrl, studentName) {
        document.getElementById('studentAnswerModalTitle').innerHTML = `<i class="ti ti-file-text text-primary"></i> Detail Jawaban: ${studentName}`;
        document.getElementById('modalAnswersLoading').classList.remove('d-none');
        document.getElementById('modalAnswersList').classList.add('d-none');
        
        const modal = new bootstrap.Modal(document.getElementById('modalStudentAnswers'));
        modal.show();

        fetch(fetchUrl)
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat jawaban');
                return res.json();
            })
            .then(data => {
                document.getElementById('modalStudentName').innerText = data.student.name;
                document.getElementById('modalStudentScore').innerText = data.attempt.score;
                document.getElementById('modalStudentTimeInfo').innerText = `Submit: ${data.attempt.submitted_at} | Durasi: ${data.attempt.duration}`;

                currentAnswersData = data.questions;
                renderModalAnswerCards(data.questions);

                document.getElementById('modalAnswersLoading').classList.add('d-none');
                document.getElementById('modalAnswersList').classList.remove('d-none');
            })
            .catch(err => {
                document.getElementById('modalAnswersLoading').innerHTML = `<div class="text-danger fw-semibold"><i class="ti ti-alert-triangle me-1"></i> Terjadi kesalahan: ${err.message}</div>`;
            });
    }

    function renderModalAnswerCards(questions) {
        const container = document.getElementById('modalAnswersList');
        container.innerHTML = '';

        let correctCount = 0;
        let wrongCount = 0;
        let unansweredCount = 0;

        questions.forEach((item) => {
            if (!item.is_answered) {
                unansweredCount++;
            } else if (item.is_correct) {
                correctCount++;
            } else {
                wrongCount++;
            }

            let badgeHtml = '';
            let filterCategory = 'unanswered';
            if (!item.is_answered) {
                badgeHtml = `<span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1 rounded-pill"><i class="ti ti-minus me-1"></i> Belum Dijawab</span>`;
                filterCategory = 'unanswered';
            } else if (item.is_correct) {
                badgeHtml = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="ti ti-check me-1"></i> Jawaban Benar</span>`;
                filterCategory = 'correct';
            } else {
                badgeHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill"><i class="ti ti-x me-1"></i> Jawaban Salah</span>`;
                filterCategory = 'wrong';
            }

            let optionsHtml = '';
            if (item.question_type === 'matching') {
                optionsHtml += `<div class="mt-2.5">`;
                item.options.forEach((opt) => {
                    const isPairOk = opt.is_pair_correct;
                    const optClass = isPairOk ? 'correct-target' : 'wrong-choice';
                    const icon = isPairOk ? '<i class="ti ti-check text-success me-1"></i>' : '<i class="ti ti-x text-danger me-1"></i>';
                    optionsHtml += `
                        <div class="opt-box ${optClass} mb-1.5 d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <div>
                                ${icon} <strong>${opt.option_text}</strong> → <span class="fst-italic">${opt.student_match ?? '(Kosong)'}</span>
                            </div>
                            <div class="small opacity-75">
                                Kunci: <strong>${opt.correct_match}</strong>
                            </div>
                        </div>
                    `;
                });
                optionsHtml += `</div>`;
            } else {
                optionsHtml += `<div class="mt-2">`;
                item.options.forEach((opt) => {
                    let boxStyle = 'normal';
                    let labelTag = '';

                    if (opt.is_selected && opt.is_correct) {
                        boxStyle = 'correct-target';
                        labelTag = `<span class="badge bg-success text-white ms-auto font-semibold" style="font-size: 0.68rem;"><i class="ti ti-check me-0.5"></i> Pilihan Siswa (Benar)</span>`;
                    } else if (opt.is_selected && !opt.is_correct) {
                        boxStyle = 'wrong-choice';
                        labelTag = `<span class="badge bg-danger text-white ms-auto font-semibold" style="font-size: 0.68rem;"><i class="ti ti-x me-0.5"></i> Pilihan Siswa (Salah)</span>`;
                    } else if (!opt.is_selected && opt.is_correct) {
                        boxStyle = 'correct-target';
                        labelTag = `<span class="badge bg-success-subtle text-success border border-success-subtle ms-auto font-semibold" style="font-size: 0.68rem;"><i class="ti ti-key me-0.5"></i> Kunci Jawaban</span>`;
                    }

                    optionsHtml += `
                        <div class="opt-box ${boxStyle} d-flex align-items-center justify-content-between gap-2">
                            <span>${opt.option_text}</span>
                            ${labelTag}
                        </div>
                    `;
                });
                optionsHtml += `</div>`;
            }

            const card = document.createElement('div');
            card.className = `answer-card filter-item filter-${filterCategory}`;
            card.innerHTML = `
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom" style="border-color: var(--tblr-border-color, #f1f5f9) !important;">
                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                        <span class="badge bg-primary me-1.5" style="font-size: 0.74rem;">Soal #${item.number}</span>
                    </div>
                    ${badgeHtml}
                </div>
                <div class="fw-semibold text-dark mb-2" style="font-size: 0.92rem; line-height: 1.5;">
                    ${item.question_text}
                </div>
                ${optionsHtml}
            `;
            container.appendChild(card);
        });

        document.getElementById('countModalCorrect').innerText = correctCount;
        document.getElementById('countModalWrong').innerText = wrongCount;
        document.getElementById('countModalUnanswered').innerText = unansweredCount;
    }

    function filterModalAnswers(category, element) {
        document.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        const items = document.querySelectorAll('.filter-item');
        items.forEach(item => {
            if (category === 'all') {
                item.style.display = 'block';
            } else if (item.classList.contains(`filter-${category}`)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endsection
