@extends('layouts.be.master')

@section('header_title', 'Dashboard Utama — Admin & Guru')

@section('content')

{{-- ─────────────────────────────────────────────
     1. Hero Welcome Banner
     ───────────────────────────────────────────── --}}
<div class="card border-0 rounded-4 mb-4 overflow-hidden position-relative"
     style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 8px 32px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.06);">

    {{-- Decorative background circles --}}
    <div aria-hidden="true" style="pointer-events:none;position:absolute;inset:0;overflow:hidden;">
        <div style="position:absolute;width:300px;height:300px;border-radius:50%;
                    background:radial-gradient(circle, rgba(32,107,196,.25) 0%, transparent 70%);
                    top:-80px;right:-60px;"></div>
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;
                    background:radial-gradient(circle, rgba(12,166,120,.12) 0%, transparent 70%);
                    bottom:-60px;left:40%;"></div>
        <div style="position:absolute;width:120px;height:120px;border-radius:50%;
                    border:1px solid rgba(255,255,255,.06);
                    bottom:20px;right:220px;"></div>
    </div>

    <div class="card-body p-4 position-relative" style="z-index:1;">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                {{-- Glowing avatar --}}
                <div class="flex-shrink-0 position-relative">
                    <div style="position:absolute;inset:-4px;border-radius:50%;
                                background:conic-gradient(from 0deg, #206bc4, #0ca678, #206bc4);
                                opacity:.7;filter:blur(4px);"></div>
                    <div class="avatar text-white rounded-circle fw-bold d-flex align-items-center justify-content-center shadow"
                         style="width:54px;height:54px;font-size:1.35rem;letter-spacing:-1px;
                                background:linear-gradient(135deg,#206bc4,#1a569d);
                                position:relative;z-index:1;border:2px solid rgba(255,255,255,.2);">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold mb-0 text-white" style="font-size:1.2rem;">
                            Selamat Datang, {{ Auth::user()->name }}!
                        </h4>
                        <span class="badge rounded-pill px-3 py-1"
                              style="font-size:.72rem;background:rgba(32,107,196,.4);
                                     border:1px solid rgba(255,255,255,.15);color:#fff;
                                     backdrop-filter:blur(4px);">
                            <i class="ti ti-award me-1"></i>
                            {{ Auth::user()->role->name === 'guru' ? 'Guru Pengampu' : 'Administrator' }}
                        </span>
                    </div>
                    <p class="text-white-50 mb-0 small">
                        <i class="ti ti-calendar me-1"></i>
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('dashboard') }}"
                   class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm"
                   style="background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.3); color: #fff; backdrop-filter: blur(4px);">
                    <i class="ti ti-external-link text-info" style="font-size: 1.1rem;"></i>
                    <span>Lihat Portal Siswa</span>
                </a>
            </div>
        </div>
    </div>
</div>


{{-- ─────────────────────────────────────────────
     2. KPI Cards (4 kolom)
     ───────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Total Siswa --}}
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none h-100 d-block">
            <div class="card card-hover stat-card-accent-success h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted-custom fw-bold text-uppercase"
                              style="font-size:.7rem;letter-spacing:.7px;">TOTAL SISWA</span>
                        <div class="avatar-icon-box avatar-icon-success">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                    <div class="h2 fw-bold heading-custom mb-2">{{ $totalStudents }}</div>
                    <div class="d-flex align-items-center justify-content-between text-muted-custom"
                         style="font-size:.78rem;">
                        <span>Siswa aktif terdaftar</span>
                        <i class="ti ti-chevron-right opacity-40"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Tugas Perlu Nilai --}}
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.submissions.index') }}" class="text-decoration-none h-100 d-block">
            <div class="card card-hover stat-card-accent-warning h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted-custom fw-bold text-uppercase"
                              style="font-size:.7rem;letter-spacing:.7px;">TUGAS PERLU NILAI</span>
                        <div class="avatar-icon-box avatar-icon-warning">
                            <i class="ti ti-checkup-list"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h2 fw-bold heading-custom mb-0">{{ $ungradedSubmissions }}</div>
                        @if($ungradedSubmissions > 0)
                            <span class="badge badge-soft-danger rounded-pill px-2" style="font-size:.68rem;">
                                Perlu Dinilai
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted-custom"
                         style="font-size:.78rem;">
                        <span>Tugas belum dinilai</span>
                        <i class="ti ti-chevron-right opacity-40"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Materi & Diskusi --}}
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.materials.index') }}" class="text-decoration-none h-100 d-block">
            <div class="card card-hover stat-card-accent-primary h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted-custom fw-bold text-uppercase"
                              style="font-size:.7rem;letter-spacing:.7px;">MATERI &amp; DISKUSI</span>
                        <div class="avatar-icon-box avatar-icon-primary">
                            <i class="ti ti-messages"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="h2 fw-bold heading-custom mb-0">{{ $totalMaterials }}</div>
                        <span class="badge badge-soft-primary rounded-pill px-2" style="font-size:.68rem;">
                            {{ $totalDiscussions }} Diskusi
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted-custom"
                         style="font-size:.78rem;">
                        <span>Modul &amp; ruang diskusi</span>
                        <i class="ti ti-chevron-right opacity-40"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Kuis Aktif --}}
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.quizzes.index') }}" class="text-decoration-none h-100 d-block">
            <div class="card card-hover stat-card-accent-danger h-100 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted-custom fw-bold text-uppercase"
                              style="font-size:.7rem;letter-spacing:.7px;">KUIS &amp; UJIAN AKTIF</span>
                        <div class="avatar-icon-box avatar-icon-danger">
                            <i class="ti ti-help-circle"></i>
                        </div>
                    </div>
                    <div class="h2 fw-bold heading-custom mb-2">{{ $activeQuizzes }}</div>
                    <div class="d-flex align-items-center justify-content-between text-muted-custom"
                         style="font-size:.78rem;">
                        <span>Kuis belum terlewat</span>
                        <i class="ti ti-chevron-right opacity-40"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     3. Main Two-Column Layout
     ───────────────────────────────────────────── --}}
<div class="row g-4">

    {{-- ╔══════════════════════════╗
         ║  LEFT COLUMN (7 / 12)   ║
         ╚══════════════════════════╝ --}}
    <div class="col-lg-7 d-flex flex-column gap-4">

        {{-- CARD A: 5 Komentar Terakhir di Ruang Diskusi --}}
        <div class="card shadow-sm mb-0">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-icon-box avatar-icon-primary" style="width:36px;height:36px;font-size:1rem;">
                        <i class="ti ti-messages"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold" style="font-size:.92rem;">
                            Komentar Terbaru dari Siswa
                        </h6>
                        <span class="text-muted-custom" style="font-size:.73rem;">
                            Klik untuk membuka ruang diskusi materi
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.materials.index') }}"
                   class="btn btn-sm btn-soft-primary rounded-pill fw-semibold px-3"
                   style="font-size:.75rem;">
                    Semua Materi <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="card-body p-4">
                @if($recentDiscussions->count() > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($recentDiscussions as $disc)
                            @php
                                $discussionUrl = $disc->material
                                    ? route('admin.materials.show', $disc->material) . '#discussion-item-' . $disc->id
                                    : '#';
                                $name     = $disc->user->name ?? 'Siswa';
                                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($name)), 0, 2))));
                                // Deterministic colour per user
                                $colors   = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2'];
                                $avatarBg = $colors[crc32($name) % count($colors)];
                            @endphp
                            <a href="{{ $discussionUrl }}"
                               class="dashboard-feed-item d-flex gap-3"
                               title="Klik untuk membuka ruang diskusi materi">

                                {{-- Avatar Inisial --}}
                                <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="width:40px;height:40px;min-width:40px;
                                            background:{{ $avatarBg }};
                                            font-size:.8rem;letter-spacing:.5px;
                                            box-shadow:0 3px 10px {{ $avatarBg }}55;">
                                    {{ $initials }}
                                </div>

                                {{-- Konten --}}
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                        <span class="fw-bold heading-custom" style="font-size:.88rem;">
                                            {{ $name }}
                                        </span>
                                        <span class="text-muted-custom d-inline-flex align-items-center gap-1 flex-shrink-0"
                                              style="font-size:.72rem;white-space:nowrap;">
                                            <i class="ti ti-clock"></i>
                                            {{ $disc->created_at ? $disc->created_at->diffForHumans() : '-' }}
                                        </span>
                                    </div>
                                    @if($disc->material)
                                        <div class="mb-1">
                                            <span class="badge badge-soft-info rounded-pill px-2" style="font-size:.67rem;">
                                                <i class="ti ti-book me-1"></i>{{ Str::limit($disc->material->title, 35) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-muted-custom" style="font-size:.83rem;line-height:1.6;">
                                        {{ Str::limit($disc->comment, 180) }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="py-4 text-center">
                        <div class="avatar-icon-box avatar-icon-primary mx-auto mb-3"
                             style="width:48px;height:48px;font-size:1.4rem;">
                            <i class="ti ti-message-off"></i>
                        </div>
                        <h6 class="fw-bold heading-custom mb-1">Belum Ada Komentar Siswa</h6>
                        <p class="text-muted-custom small mb-0">
                            Komentar siswa di ruang diskusi materi akan muncul di sini.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD B: 5 Tugas Siswa Menunggu Penilaian --}}
        <div class="card mb-0 border-0 rounded-4 overflow-hidden" style="box-shadow:0 2px 16px rgba(245,158,11,.12);">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:linear-gradient(135deg,rgba(245,158,11,.08) 0%,rgba(255,255,255,1) 60%);border-bottom:1px solid rgba(245,158,11,.15);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                         style="width:40px;height:40px;background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 4px 12px rgba(217,119,6,.35);">
                        <i class="ti ti-clipboard-text text-white" style="font-size:1.15rem;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold" style="font-size:.95rem;color:#1e293b;">Tugas Menunggu Penilaian</h6>
                        <span class="text-muted" style="font-size:.73rem;">Pengumpulan siswa yang belum dinilai</span>
                    </div>
                </div>
                <a href="{{ route('admin.submissions.index') }}"
                   class="btn btn-sm rounded-pill fw-semibold px-3 d-inline-flex align-items-center gap-1"
                   style="font-size:.75rem;background:#f59e0b;color:#fff;border:none;box-shadow:0 2px 8px rgba(245,158,11,.3);">
                    Lihat Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentUngradedSubmissions->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentUngradedSubmissions as $sub)
                        @php
                            $sname    = $sub->student->name ?? 'Siswa';
                            $sinit    = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($sname)), 0, 2))));
                            $scolors  = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2','#0d9488'];
                            $sBg      = $scolors[crc32($sname) % count($scolors)];
                            $hoursAgo = $sub->created_at ? now()->diffInHours($sub->created_at) : 0;
                            $urgency  = $hoursAgo < 6 ? 'danger' : ($hoursAgo < 24 ? 'warning' : 'secondary');
                        @endphp
                        <div class="list-group-item px-4 py-3 d-flex align-items-center gap-3"
                             style="transition:background .18s;border-left:3px solid {{ $urgency === 'danger' ? '#ef4444' : ($urgency === 'warning' ? '#f59e0b' : '#94a3b8') }};">
                            {{-- Avatar inisial siswa --}}
                            <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                 style="width:40px;height:40px;min-width:40px;font-size:.78rem;
                                        background:{{ $sBg }};letter-spacing:.5px;
                                        box-shadow:0 3px 10px {{ $sBg }}44;">
                                {{ $sinit }}
                            </div>
                            {{-- Info --}}
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <div class="fw-bold text-truncate" style="font-size:.87rem;color:#1e293b;max-width:170px;" title="{{ $sname }}">{{ $sname }}</div>
                                    <span class="badge rounded-pill flex-shrink-0"
                                          style="font-size:.65rem;padding:.28em .65em;
                                                 background:{{ $urgency === 'danger' ? 'rgba(239,68,68,.12)' : ($urgency === 'warning' ? 'rgba(245,158,11,.12)' : 'rgba(148,163,184,.12)') }};
                                                 color:{{ $urgency === 'danger' ? '#dc2626' : ($urgency === 'warning' ? '#b45309' : '#64748b') }};">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ $sub->created_at ? $sub->created_at->diffForHumans() : '-' }}
                                    </span>
                                </div>
                                <div class="text-truncate mb-1" style="font-size:.8rem;color:#475569;" title="{{ $sub->assignment->title ?? 'Tugas' }}">
                                    <i class="ti ti-clipboard me-1 opacity-60"></i>{{ $sub->assignment->title ?? 'Tugas' }}
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(14,165,233,.1);color:#0284c7;">
                                        <i class="ti ti-school me-1"></i>{{ $sub->student->schoolClass->name ?? '-' }}
                                    </span>
                                    <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(99,102,241,.1);color:#6366f1;">
                                        <i class="ti ti-book me-1"></i>{{ $sub->assignment->subject->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            {{-- Action --}}
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.submissions.show', $sub) }}"
                                   class="btn btn-sm rounded-pill fw-semibold d-inline-flex align-items-center gap-1"
                                   style="font-size:.76rem;padding:.35rem .85rem;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;box-shadow:0 2px 8px rgba(217,119,6,.3);white-space:nowrap;">
                                    <i class="ti ti-star-filled"></i> Nilai
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card-body py-5 text-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                         style="width:52px;height:52px;background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(16,185,129,.05));
                                box-shadow:0 4px 16px rgba(16,185,129,.15);">
                        <i class="ti ti-circle-check" style="font-size:1.5rem;color:#10b981;"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size:.9rem;color:#1e293b;">Semua Tugas Selesai Dinilai</h6>
                    <p class="text-muted small mb-0">Tidak ada pengumpulan tugas yang tertunda saat ini.</p>
                </div>
            @endif
        </div>

        {{-- CARD C: 5 Tugas Pembelajaran Terbaru --}}
        <div class="card mb-0 border-0 rounded-4 overflow-hidden" style="box-shadow:0 2px 16px rgba(245,158,11,.1);">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:linear-gradient(135deg,rgba(245,158,11,.07) 0%,rgba(255,255,255,1) 60%);border-bottom:1px solid rgba(245,158,11,.15);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                         style="width:40px;height:40px;background:linear-gradient(135deg,#fbbf24,#f59e0b);box-shadow:0 4px 12px rgba(245,158,11,.3);">
                        <i class="ti ti-clipboard-list text-white" style="font-size:1.15rem;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold" style="font-size:.95rem;color:#1e293b;">Tugas Pembelajaran Terakhir</h6>
                        <span class="text-muted" style="font-size:.73rem;">Pantau tenggat & status pengumpulan siswa</span>
                    </div>
                </div>
                <a href="{{ route('admin.assignments.index') }}"
                   class="btn btn-sm rounded-pill fw-semibold px-3 d-inline-flex align-items-center gap-1"
                   style="font-size:.75rem;background:rgba(245,158,11,.12);color:#b45309;border:1px solid rgba(245,158,11,.25);">
                    Semua Tugas <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentAssignments->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentAssignments as $assignment)
                        @php
                            $isPast    = $assignment->due_date && $assignment->due_date->isPast();
                            $isUrgent  = $assignment->due_date && !$isPast && $assignment->due_date->diffInHours(now()) <= 24;
                            $statusColor = $isPast ? '#ef4444' : ($isUrgent ? '#f59e0b' : '#10b981');
                            $statusLabel = $isPast ? 'Terlewat' : ($isUrgent ? 'Segera' : 'Aktif');
                        @endphp
                        <div class="list-group-item px-4 py-3"
                             style="transition:background .18s;border-left:3px solid {{ $statusColor }};">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3 min-w-0 flex-grow-1">
                                    {{-- Status dot icon --}}
                                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 mt-1"
                                         style="width:38px;height:38px;background:{{ $isPast ? 'rgba(239,68,68,.1)' : ($isUrgent ? 'rgba(245,158,11,.1)' : 'rgba(16,185,129,.1)') }};">
                                        <i class="ti ti-{{ $isPast ? 'alert-triangle' : ($isUrgent ? 'clock-exclamation' : 'clipboard-check') }}"
                                           style="font-size:1.1rem;color:{{ $statusColor }};"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                               class="fw-bold text-decoration-none text-truncate"
                                               style="font-size:.88rem;color:#1e293b;max-width:200px;"
                                               title="{{ $assignment->title }}">{{ $assignment->title }}</a>
                                            <span class="badge rounded-pill flex-shrink-0"
                                                  style="font-size:.62rem;padding:.25em .6em;
                                                         background:{{ $isPast ? 'rgba(239,68,68,.12)' : ($isUrgent ? 'rgba(245,158,11,.12)' : 'rgba(16,185,129,.12)') }};
                                                         color:{{ $isPast ? '#dc2626' : ($isUrgent ? '#b45309' : '#059669') }};">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(100,116,139,.1);color:#475569;">
                                                <i class="ti ti-school me-1"></i>{{ $assignment->schoolClass->name ?? 'Kelas' }}
                                            </span>
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(14,165,233,.1);color:#0284c7;">
                                                <i class="ti ti-book me-1"></i>{{ $assignment->subject->name ?? '-' }}
                                            </span>
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(99,102,241,.1);color:#6366f1;">
                                                <i class="ti ti-file-upload me-1"></i>{{ $assignment->submissions_count }} Pengumpulan
                                            </span>
                                        </div>
                                        @if($assignment->due_date)
                                            <div class="d-flex align-items-center gap-1" style="font-size:.76rem;color:{{ $statusColor }};">
                                                <i class="ti ti-calendar-due"></i>
                                                <span>Tenggat: {{ $assignment->due_date->isoFormat('D MMM YYYY, HH:mm') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0 mt-1">
                                    <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                       class="btn btn-sm rounded-pill fw-semibold d-inline-flex align-items-center gap-1"
                                       style="font-size:.75rem;padding:.32rem .75rem;background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.25);"
                                       title="Edit & Detail Tugas">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card-body py-5 text-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                         style="width:52px;height:52px;background:rgba(245,158,11,.1);box-shadow:0 4px 16px rgba(245,158,11,.1);">
                        <i class="ti ti-clipboard-off" style="font-size:1.5rem;color:#d97706;"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size:.9rem;color:#1e293b;">Belum Ada Tugas</h6>
                    <p class="text-muted small mb-0">Tugas pembelajaran yang dibuat akan muncul di sini.</p>
                </div>
            @endif
        </div>

    </div>{{-- /LEFT COLUMN --}}

    {{-- ╔══════════════════════════╗
         ║  RIGHT COLUMN (5 / 12)  ║
         ╚══════════════════════════╝ --}}
    <div class="col-lg-5 d-flex flex-column gap-4">

        {{-- CARD D: Akses Cepat Pengelolaan --}}
        <div class="card shadow-sm mb-0">
            <div class="card-header px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-icon-box avatar-icon-primary" style="width:36px;height:36px;font-size:1rem;">
                        <i class="ti ti-layout-grid"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold" style="font-size:.92rem;">Akses Cepat Pengelolaan</h6>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-4">
                        <a href="{{ route('admin.materials.create') }}" class="quick-action-card">
                            <i class="ti ti-file-plus mb-2 text-primary" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Buat Materi</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Modul &amp; Video</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('admin.assignments.create') }}" class="quick-action-card">
                            <i class="ti ti-clipboard-plus mb-2 text-warning" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Buat Tugas</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Tenggat &amp; Bobot</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('admin.quizzes.create') }}" class="quick-action-card">
                            <i class="ti ti-help-circle mb-2 text-danger" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Buat Kuis</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Ujian &amp; Evaluasi</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('admin.question-banks.index') }}" class="quick-action-card">
                            <i class="ti ti-database mb-2 text-info" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Bank Soal</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Koleksi Soal</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('admin.reports.index') }}" class="quick-action-card">
                            <i class="ti ti-report-analytics mb-2 text-success" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Rekap Nilai</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Laporan &amp; Ekspor</span>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('admin.classes.index') }}" class="quick-action-card">
                            <i class="ti ti-school mb-2 text-secondary" style="font-size:1.6rem;"></i>
                            <span class="fw-bold heading-custom d-block" style="font-size:.78rem;">Data Kelas</span>
                            <span class="text-muted-custom d-block" style="font-size:.68rem;">Daftar Siswa</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD E: 5 Materi Pembelajaran Terbaru --}}
        <div class="card shadow-sm mb-0">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-icon-box avatar-icon-info" style="width:36px;height:36px;font-size:1rem;">
                        <i class="ti ti-book"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold" style="font-size:.92rem;">5 Materi Terkini</h6>
                </div>
                <a href="{{ route('admin.materials.index') }}"
                   class="btn btn-sm btn-soft-info rounded-pill fw-semibold px-3"
                   style="font-size:.75rem;">
                    Lihat Semua <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>

            @if($recentMaterials->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentMaterials as $mat)
                        <div class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                <a href="{{ route('admin.materials.show', $mat) }}"
                                   class="fw-bold heading-custom text-decoration-none d-block text-truncate mb-2"
                                   style="font-size:.86rem;">
                                    {{ $mat->title }}
                                </a>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge badge-soft-secondary rounded-pill px-2" style="font-size:.67rem;">
                                        {{ $mat->schoolClass->name ?? 'Kelas' }}
                                    </span>
                                    <span class="badge badge-soft-info rounded-pill px-2" style="font-size:.67rem;">
                                        {{ $mat->subject->name ?? '-' }}
                                    </span>
                                    @if($mat->discussions_count > 0)
                                        <span class="badge badge-soft-primary rounded-pill px-2" style="font-size:.67rem;">
                                            <i class="ti ti-messages me-1"></i>{{ $mat->discussions_count }} Diskusi
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.materials.show', $mat) }}"
                               class="btn btn-sm btn-soft-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                               style="width:34px;height:34px;" title="Lihat &amp; Balas Diskusi">
                                <i class="ti ti-messages" style="font-size:.9rem;"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card-body py-4 text-center text-muted-custom small">
                    Belum ada materi pembelajaran yang dibuat.
                </div>
            @endif
        </div>

        {{-- CARD F: 5 Kuis & Evaluasi Terbaru --}}
        <div class="card mb-0 border-0 rounded-4 overflow-hidden" style="box-shadow:0 2px 16px rgba(239,68,68,.12);">
            <div class="card-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="background:linear-gradient(135deg,rgba(239,68,68,.07) 0%,rgba(255,255,255,1) 60%);border-bottom:1px solid rgba(239,68,68,.15);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                         style="width:40px;height:40px;background:linear-gradient(135deg,#f87171,#ef4444);box-shadow:0 4px 12px rgba(239,68,68,.35);">
                        <i class="ti ti-help-hexagon text-white" style="font-size:1.15rem;"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold" style="font-size:.95rem;color:#1e293b;">Kuis &amp; Evaluasi Terkini</h6>
                        <span class="text-muted" style="font-size:.73rem;">Pantau kuis aktif dan rekap pengerjaan siswa</span>
                    </div>
                </div>
                <a href="{{ route('admin.quizzes.index') }}"
                   class="btn btn-sm rounded-pill fw-semibold px-3 d-inline-flex align-items-center gap-1"
                   style="font-size:.75rem;background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);">
                    Lihat Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentQuizzes->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentQuizzes as $quiz)
                        @php
                            $quizActive = !$quiz->end_date || !$quiz->end_date->isPast();
                            $quizColor  = $quizActive ? '#10b981' : '#94a3b8';
                            $quizLabel  = $quizActive ? 'Aktif' : 'Berakhir';
                        @endphp
                        <div class="list-group-item px-4 py-3"
                             style="transition:background .18s;border-left:3px solid {{ $quizColor }};">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3 min-w-0 flex-grow-1">
                                    {{-- Icon --}}
                                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 mt-1"
                                         style="width:38px;height:38px;background:{{ $quizActive ? 'rgba(16,185,129,.1)' : 'rgba(148,163,184,.1)' }};">
                                        <i class="ti ti-{{ $quizActive ? 'help-circle' : 'help-off' }}"
                                           style="font-size:1.1rem;color:{{ $quizColor }};"></i>
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <a href="{{ route('admin.quizzes.show', $quiz) }}"
                                               class="fw-bold text-decoration-none text-truncate"
                                               style="font-size:.88rem;color:#1e293b;max-width:190px;"
                                               title="{{ $quiz->title }}">{{ $quiz->title }}</a>
                                            <span class="badge rounded-pill flex-shrink-0"
                                                  style="font-size:.62rem;padding:.25em .6em;
                                                         background:{{ $quizActive ? 'rgba(16,185,129,.12)' : 'rgba(148,163,184,.12)' }};
                                                         color:{{ $quizActive ? '#059669' : '#64748b' }};">
                                                {{ $quizLabel }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(100,116,139,.1);color:#475569;">
                                                <i class="ti ti-school me-1"></i>{{ $quiz->schoolClass->name ?? 'Kelas' }}
                                            </span>
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(99,102,241,.1);color:#6366f1;">
                                                <i class="ti ti-notes me-1"></i>{{ $quiz->questions_count }} Soal
                                            </span>
                                            <span class="badge rounded-pill" style="font-size:.64rem;background:rgba(14,165,233,.1);color:#0284c7;">
                                                <i class="ti ti-user-check me-1"></i>{{ $quiz->attempts_count }} Percobaan
                                            </span>
                                        </div>
                                        @if($quiz->end_date)
                                            <div class="d-flex align-items-center gap-1" style="font-size:.76rem;color:{{ $quizColor }};">
                                                <i class="ti ti-calendar-x"></i>
                                                <span>Berakhir: {{ $quiz->end_date->isoFormat('D MMM YYYY, HH:mm') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0 mt-1">
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}"
                                       class="btn btn-sm rounded-pill fw-semibold d-inline-flex align-items-center gap-1"
                                       style="font-size:.75rem;padding:.32rem .75rem;background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);"
                                       title="Lihat Hasil & Rekap Kuis">
                                        <i class="ti ti-chart-bar"></i> Hasil
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card-body py-5 text-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                         style="width:52px;height:52px;background:rgba(239,68,68,.1);box-shadow:0 4px 16px rgba(239,68,68,.1);">
                        <i class="ti ti-help-off" style="font-size:1.5rem;color:#ef4444;"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size:.9rem;color:#1e293b;">Belum Ada Kuis Aktif</h6>
                    <p class="text-muted small mb-0">Kuis dan evaluasi yang dibuat akan muncul di sini.</p>
                </div>
            @endif
        </div>

    </div>{{-- /RIGHT COLUMN --}}

</div>{{-- /main row --}}

@endsection
