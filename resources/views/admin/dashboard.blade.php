@extends('layouts.be.master')

@section('header_title', 'Dashboard — Admin & Guru')

@section('content')

{{-- ═══════════════════════════════════════════════
     HERO BANNER
     ═══════════════════════════════════════════════ --}}
<div class="dash-hero-banner rounded-4 mb-4 overflow-hidden">
    <div class="dash-hero-decor" aria-hidden="true">
        <div class="dash-hero-orb orb-1"></div>
        <div class="dash-hero-orb orb-2"></div>
        <div class="dash-hero-orb orb-3"></div>
    </div>
    <div class="dash-hero-body">
        <div class="dash-hero-left">
            <div class="dash-hero-avatar-wrap">
                <div class="dash-hero-avatar-ring"></div>
                <div class="dash-hero-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
            <div class="dash-hero-text">
                <div class="dash-hero-greeting">
                    Selamat Datang, <span class="dash-hero-name">{{ Auth::user()->name }}</span>
                    <span class="dash-hero-role-badge">
                        <i class="ti ti-award"></i>
                        {{ Auth::user()->role && Auth::user()->role->name === 'guru' ? 'Guru' : 'Admin' }}
                    </span>
                </div>
                <div class="dash-hero-date">
                    <i class="ti ti-calendar-event"></i>
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
        </div>
        <div class="dash-hero-right">
            <a href="{{ route('dashboard') }}" class="dash-portal-btn">
                <i class="ti ti-external-link"></i>
                <span>Portal Siswa</span>
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     KPI CARDS
     ═══════════════════════════════════════════════ --}}
<div class="dash-kpi-grid mb-4">

    <a href="{{ route('admin.users.index') }}" class="dash-kpi-card accent-teal">
        <div class="dash-kpi-icon"><i class="ti ti-users"></i></div>
        <div class="dash-kpi-body">
            <div class="dash-kpi-label">Total Siswa</div>
            <div class="dash-kpi-value">{{ $totalStudents }}</div>
        </div>
        <i class="ti ti-chevron-right dash-kpi-arrow"></i>
    </a>

    <a href="{{ route('admin.submissions.index') }}" class="dash-kpi-card accent-amber">
        <div class="dash-kpi-icon"><i class="ti ti-checkup-list"></i></div>
        <div class="dash-kpi-body">
            <div class="dash-kpi-label">Perlu Dinilai</div>
            <div class="dash-kpi-value">
                {{ $ungradedSubmissions }}
                @if($ungradedSubmissions > 0)
                    <span class="dash-kpi-pip urgent"></span>
                @endif
            </div>
        </div>
        <i class="ti ti-chevron-right dash-kpi-arrow"></i>
    </a>

    <a href="{{ route('admin.materials.index') }}" class="dash-kpi-card accent-blue">
        <div class="dash-kpi-icon"><i class="ti ti-books"></i></div>
        <div class="dash-kpi-body">
            <div class="dash-kpi-label">Materi</div>
            <div class="dash-kpi-value">{{ $totalMaterials }}</div>
        </div>
        <i class="ti ti-chevron-right dash-kpi-arrow"></i>
    </a>

    <a href="{{ route('admin.quizzes.index') }}" class="dash-kpi-card accent-rose">
        <div class="dash-kpi-icon"><i class="ti ti-help-circle"></i></div>
        <div class="dash-kpi-body">
            <div class="dash-kpi-label">Kuis Aktif</div>
            <div class="dash-kpi-value">{{ $activeQuizzes }}</div>
        </div>
        <i class="ti ti-chevron-right dash-kpi-arrow"></i>
    </a>

</div>

{{-- ═══════════════════════════════════════════════
     MAIN GRID
     ═══════════════════════════════════════════════ --}}
<div class="dash-main-grid">

    {{-- ──── LEFT ──── --}}
    <div class="dash-col">

        {{-- Komentar Terbaru --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-blue"><i class="ti ti-messages"></i></div>
                <h6 class="dash-card-title">Komentar Terbaru dari Siswa</h6>
                <a href="{{ route('admin.materials.index') }}" class="dash-more-link">
                    Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentDiscussions->count() > 0)
                <div class="dash-feed">
                    @foreach($recentDiscussions as $disc)
                        @php
                            $url      = $disc->material ? route('admin.materials.show', $disc->material).'#discussion-item-'.$disc->id : '#';
                            $name     = $disc->user->name ?? 'Siswa';
                            $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($name)), 0, 2))));
                            $pal      = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2'];
                            $bg       = $pal[abs(crc32($name)) % count($pal)];
                        @endphp
                        <a href="{{ $url }}" class="dash-feed-row">
                            <div class="dfa" style="background:{{ $bg }};">{{ $initials }}</div>
                            <div class="dash-feed-info">
                                <div class="dfi-top">
                                    <span class="dfi-name">{{ $name }}</span>
                                    <span class="dfi-time"><i class="ti ti-clock"></i> {{ $disc->created_at?->diffForHumans() ?? '-' }}</span>
                                </div>
                                @if($disc->material)
                                    <div class="dfi-mat"><i class="ti ti-book"></i> {{ Str::limit($disc->material->title, 42) }}</div>
                                @endif
                                <div class="dfi-txt">{{ Str::limit($disc->comment, 100) }}</div>
                            </div>
                            <i class="ti ti-chevron-right dash-row-arrow"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty">
                    <div class="dash-empty-icon icon-blue"><i class="ti ti-message-off"></i></div>
                    <span>Belum ada komentar siswa</span>
                </div>
            @endif
        </div>

        {{-- Tugas Perlu Dinilai --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-amber"><i class="ti ti-clipboard-text"></i></div>
                <h6 class="dash-card-title">Tugas Perlu Dinilai</h6>
                <a href="{{ route('admin.submissions.index') }}" class="dash-more-link">
                    Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentUngradedSubmissions->count() > 0)
                <div class="dash-rows">
                    @foreach($recentUngradedSubmissions as $sub)
                        @php
                            $sn   = $sub->student->name ?? 'Siswa';
                            $si   = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($sn)), 0, 2))));
                            $pal2 = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2','#0d9488'];
                            $sbg  = $pal2[abs(crc32($sn)) % count($pal2)];
                            $hrs  = $sub->created_at ? now()->diffInHours($sub->created_at) : 0;
                            $urg  = $hrs < 6 ? 'danger' : ($hrs < 24 ? 'warning' : 'muted');
                        @endphp
                        <a href="{{ route('admin.submissions.show', $sub) }}" class="dash-row">
                            <div class="dfa sm" style="background:{{ $sbg }};">{{ $si }}</div>
                            <div class="dash-row-info">
                                <div class="dri-top">
                                    <span class="dri-name">{{ $sn }}</span>
                                    <span class="dash-tbadge {{ $urg }}">
                                        <i class="ti ti-clock"></i> {{ $sub->created_at?->diffForHumans() ?? '-' }}
                                    </span>
                                </div>
                                <div class="dri-sub"><i class="ti ti-clipboard"></i> {{ Str::limit($sub->assignment->title ?? 'Tugas', 48) }}</div>
                                <div class="dri-tags">
                                    <span class="dtag">{{ $sub->student->schoolClass->name ?? '-' }}</span>
                                    <span class="dtag">{{ $sub->assignment->subject->name ?? '-' }}</span>
                                </div>
                            </div>
                            <i class="ti ti-chevron-right dash-row-arrow"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty">
                    <div class="dash-empty-icon icon-teal"><i class="ti ti-circle-check"></i></div>
                    <span>Semua tugas sudah dinilai</span>
                </div>
            @endif
        </div>

        {{-- Tugas Terbaru --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-amber"><i class="ti ti-clipboard-list"></i></div>
                <h6 class="dash-card-title">Tugas Terbaru</h6>
                <a href="{{ route('admin.assignments.index') }}" class="dash-more-link">
                    Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentAssignments->count() > 0)
                <div class="dash-rows">
                    @foreach($recentAssignments as $assignment)
                        @php
                            $isPast   = $assignment->due_date && $assignment->due_date->isPast();
                            $isUrgent = $assignment->due_date && !$isPast && $assignment->due_date->diffInHours(now()) <= 24;
                            $badge    = $isPast ? 'danger' : ($isUrgent ? 'warning' : 'teal');
                            $label    = $isPast ? 'Terlewat' : ($isUrgent ? 'Segera' : 'Aktif');
                            $ic       = $isPast ? '#ef4444' : ($isUrgent ? '#f59e0b' : '#10b981');
                            $ibg      = $isPast ? 'rgba(239,68,68,.1)' : ($isUrgent ? 'rgba(245,158,11,.1)' : 'rgba(16,185,129,.1)');
                            $iname    = $isPast ? 'alert-triangle' : ($isUrgent ? 'clock-exclamation' : 'clipboard-check');
                        @endphp
                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="dash-row">
                            <div class="dash-sico" style="background:{{ $ibg }};color:{{ $ic }};"><i class="ti ti-{{ $iname }}"></i></div>
                            <div class="dash-row-info">
                                <div class="dri-top">
                                    <span class="dri-name">{{ Str::limit($assignment->title, 42) }}</span>
                                    <span class="dsbadge {{ $badge }}">{{ $label }}</span>
                                </div>
                                <div class="dri-tags">
                                    <span class="dtag">{{ $assignment->schoolClass->name ?? '-' }}</span>
                                    <span class="dtag">{{ $assignment->subject->name ?? '-' }}</span>
                                    <span class="dtag">{{ $assignment->submissions_count }} Kumpul</span>
                                </div>
                                @if($assignment->due_date)
                                    <div class="dri-sub"><i class="ti ti-calendar-due"></i> {{ $assignment->due_date->isoFormat('D MMM Y, HH:mm') }}</div>
                                @endif
                            </div>
                            <i class="ti ti-chevron-right dash-row-arrow"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty">
                    <div class="dash-empty-icon icon-amber"><i class="ti ti-clipboard-off"></i></div>
                    <span>Belum ada tugas</span>
                </div>
            @endif
        </div>

    </div>{{-- /LEFT --}}

    {{-- ──── RIGHT ──── --}}
    <div class="dash-col">

        {{-- Akses Cepat --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-blue"><i class="ti ti-grid-4x4"></i></div>
                <h6 class="dash-card-title">Akses Cepat</h6>
            </div>
            <div class="dash-qgrid">
                <a href="{{ route('admin.materials.create') }}" class="dash-qtile">
                    <i class="ti ti-file-plus" style="color:#206bc4;"></i>
                    <span>Buat Materi</span>
                </a>
                <a href="{{ route('admin.assignments.create') }}" class="dash-qtile">
                    <i class="ti ti-clipboard-plus" style="color:#f59e0b;"></i>
                    <span>Buat Tugas</span>
                </a>
                <a href="{{ route('admin.quizzes.create') }}" class="dash-qtile">
                    <i class="ti ti-help-circle" style="color:#ef4444;"></i>
                    <span>Buat Kuis</span>
                </a>
                <a href="{{ route('admin.question-banks.index') }}" class="dash-qtile">
                    <i class="ti ti-database" style="color:#0891b2;"></i>
                    <span>Bank Soal</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="dash-qtile">
                    <i class="ti ti-report-analytics" style="color:#0ca678;"></i>
                    <span>Rekap Nilai</span>
                </a>
                <a href="{{ route('admin.classes.index') }}" class="dash-qtile">
                    <i class="ti ti-school" style="color:#8b5cf6;"></i>
                    <span>Data Kelas</span>
                </a>
            </div>
        </div>

        {{-- Materi Terkini --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-cyan"><i class="ti ti-book"></i></div>
                <h6 class="dash-card-title">Materi Terkini</h6>
                <a href="{{ route('admin.materials.index') }}" class="dash-more-link">
                    Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentMaterials->count() > 0)
                <div class="dash-rows">
                    @foreach($recentMaterials as $mat)
                        <a href="{{ route('admin.materials.show', $mat) }}" class="dash-row">
                            <div class="dash-sico" style="background:rgba(8,145,178,.1);color:#0891b2;"><i class="ti ti-file-text"></i></div>
                            <div class="dash-row-info">
                                <div class="dri-name">{{ Str::limit($mat->title, 45) }}</div>
                                <div class="dri-tags">
                                    <span class="dtag">{{ $mat->schoolClass->name ?? '-' }}</span>
                                    <span class="dtag">{{ $mat->subject->name ?? '-' }}</span>
                                    @if($mat->discussions_count > 0)
                                        <span class="dtag"><i class="ti ti-messages"></i> {{ $mat->discussions_count }}</span>
                                    @endif
                                </div>
                            </div>
                            <i class="ti ti-chevron-right dash-row-arrow"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty">
                    <div class="dash-empty-icon icon-cyan"><i class="ti ti-book-off"></i></div>
                    <span>Belum ada materi</span>
                </div>
            @endif
        </div>

        {{-- Kuis Terkini --}}
        <div class="dash-card">
            <div class="dash-card-head">
                <div class="dash-card-icon icon-rose"><i class="ti ti-help-hexagon"></i></div>
                <h6 class="dash-card-title">Kuis &amp; Evaluasi</h6>
                <a href="{{ route('admin.quizzes.index') }}" class="dash-more-link">
                    Semua <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            @if($recentQuizzes->count() > 0)
                <div class="dash-rows">
                    @foreach($recentQuizzes as $quiz)
                        @php $active = !$quiz->end_date || !$quiz->end_date->isPast(); @endphp
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="dash-row">
                            <div class="dash-sico"
                                 style="background:{{ $active ? 'rgba(16,185,129,.1)' : 'rgba(100,116,139,.1)' }};
                                        color:{{ $active ? '#10b981' : '#64748b' }};">
                                <i class="ti ti-{{ $active ? 'help-circle' : 'help-off' }}"></i>
                            </div>
                            <div class="dash-row-info">
                                <div class="dri-top">
                                    <span class="dri-name">{{ Str::limit($quiz->title, 36) }}</span>
                                    <span class="dsbadge {{ $active ? 'teal' : 'muted' }}">{{ $active ? 'Aktif' : 'Selesai' }}</span>
                                </div>
                                <div class="dri-tags">
                                    <span class="dtag">{{ $quiz->schoolClass->name ?? '-' }}</span>
                                    <span class="dtag">{{ $quiz->questions_count }} Soal</span>
                                    <span class="dtag">{{ $quiz->attempts_count }} Percobaan</span>
                                </div>
                                @if($quiz->end_date)
                                    <div class="dri-sub"><i class="ti ti-calendar-x"></i> {{ $quiz->end_date->isoFormat('D MMM Y, HH:mm') }}</div>
                                @endif
                            </div>
                            <i class="ti ti-chevron-right dash-row-arrow"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty">
                    <div class="dash-empty-icon icon-rose"><i class="ti ti-help-off"></i></div>
                    <span>Belum ada kuis aktif</span>
                </div>
            @endif
        </div>

    </div>{{-- /RIGHT --}}

</div>

{{-- ═══════════════════════════════════
     STYLES
     ═══════════════════════════════════ --}}
<style>
/* ── HERO ── */
.dash-hero-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #0f2847 100%);
    box-shadow: 0 6px 28px rgba(0,0,0,.2);
    position: relative;
}
.dash-hero-decor { position:absolute; inset:0; overflow:hidden; border-radius:inherit; pointer-events:none; }
.dash-hero-orb { position:absolute; border-radius:50%; filter:blur(40px); }
.orb-1 { width:260px; height:260px; background:rgba(32,107,196,.22); top:-80px; right:-30px; }
.orb-2 { width:180px; height:180px; background:rgba(12,166,120,.12); bottom:-50px; left:25%; }
.orb-3 { width:110px; height:110px; background:rgba(147,51,234,.12); top:-20px; left:42%; }

.dash-hero-body {
    position:relative; z-index:1;
    padding:1.2rem 1.4rem;
    display:flex; align-items:center; justify-content:space-between;
    gap:1rem; flex-wrap:wrap;
}
.dash-hero-left { display:flex; align-items:center; gap:.9rem; min-width:0; flex:1; }

.dash-hero-avatar-wrap { position:relative; flex-shrink:0; }
.dash-hero-avatar-ring {
    position:absolute; inset:-3px; border-radius:50%;
    background:conic-gradient(from 0deg,#206bc4 0%,#0ca678 50%,#206bc4 100%);
    opacity:.6; filter:blur(3px);
}
.dash-hero-avatar {
    position:relative; z-index:1;
    width:46px; height:46px; border-radius:50%;
    background:linear-gradient(135deg,#206bc4,#1a569d);
    border:2px solid rgba(255,255,255,.18);
    color:#fff; font-weight:800; font-size:1.2rem; letter-spacing:-.5px;
    display:flex; align-items:center; justify-content:center;
}
.dash-hero-text { min-width:0; }
.dash-hero-greeting {
    font-size:1rem; font-weight:700; color:#fff;
    display:flex; align-items:center; flex-wrap:wrap; gap:.35rem;
    line-height:1.3; margin-bottom:.2rem;
}
.dash-hero-name { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }
.dash-hero-role-badge {
    font-size:.63rem; font-weight:700; letter-spacing:.3px;
    padding:.2em .6em; border-radius:50px;
    background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
    color:#fff; display:inline-flex; align-items:center; gap:.3em; white-space:nowrap;
}
.dash-hero-date {
    font-size:.74rem; color:rgba(255,255,255,.48);
    display:flex; align-items:center; gap:.35rem;
}
.dash-hero-right { flex-shrink:0; }
.dash-portal-btn {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.45rem 1rem; border-radius:50px; font-size:.78rem; font-weight:600;
    color:#fff; text-decoration:none;
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.22);
    backdrop-filter:blur(6px); transition:all .18s ease; white-space:nowrap;
}
.dash-portal-btn:hover { background:rgba(255,255,255,.2); border-color:rgba(255,255,255,.38); color:#fff; transform:translateY(-1px); }

/* ── KPI GRID ── */
.dash-kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.75rem; }
.dash-kpi-card {
    background:var(--tblr-card-bg,#fff);
    border:1px solid var(--tblr-border-color,#e2e8f0);
    border-radius:12px; padding:.85rem 1rem;
    display:flex; align-items:center; gap:.8rem;
    text-decoration:none; transition:transform .18s ease,box-shadow .18s ease;
    position:relative; overflow:hidden;
}
.dash-kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(0,0,0,.08); }

.accent-teal  { border-left:3px solid #0ca678; }
.accent-teal  .dash-kpi-icon { background:rgba(12,166,120,.1);  color:#0ca678; }
.accent-amber { border-left:3px solid #f59e0b; }
.accent-amber .dash-kpi-icon { background:rgba(245,158,11,.1);  color:#f59e0b; }
.accent-blue  { border-left:3px solid #206bc4; }
.accent-blue  .dash-kpi-icon { background:rgba(32,107,196,.1);  color:#206bc4; }
.accent-rose  { border-left:3px solid #ef4444; }
.accent-rose  .dash-kpi-icon { background:rgba(239,68,68,.1);   color:#ef4444; }

.dash-kpi-icon {
    width:36px; height:36px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1.05rem;
}
.dash-kpi-body { min-width:0; flex:1; }
.dash-kpi-label {
    font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px;
    color:var(--tblr-text-muted,#64748b); white-space:nowrap; overflow:hidden;
    text-overflow:ellipsis; margin-bottom:.2rem;
}
.dash-kpi-value {
    font-size:1.5rem; font-weight:800; color:var(--tblr-heading-color,#0f172a);
    line-height:1; display:flex; align-items:center; gap:.4rem;
}
.dash-kpi-pip {
    width:7px; height:7px; border-radius:50%; flex-shrink:0;
}
.dash-kpi-pip.urgent { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); }
.dash-kpi-arrow {
    font-size:.88rem; color:var(--tblr-text-muted,#64748b); opacity:.35;
    transition:transform .18s ease,opacity .18s ease; flex-shrink:0;
}
.dash-kpi-card:hover .dash-kpi-arrow { transform:translateX(3px); opacity:.7; }

/* ── MAIN GRID ── */
.dash-main-grid { display:grid; grid-template-columns:1fr minmax(0,370px); gap:1rem; align-items:start; }
.dash-col { display:flex; flex-direction:column; gap:1rem; }

/* ── CARD SHELL ── */
.dash-card {
    background:var(--tblr-card-bg,#fff);
    border:1px solid var(--tblr-border-color,#e2e8f0);
    border-radius:12px; overflow:hidden;
    box-shadow:0 1px 3px rgba(0,0,0,.04);
}
.dash-card-head {
    display:flex; align-items:center; gap:.55rem;
    padding:.7rem 1rem;
    border-bottom:1px solid var(--tblr-border-color,#e2e8f0);
}
.dash-card-icon {
    width:26px; height:26px; border-radius:7px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.82rem;
}
.icon-blue  { background:rgba(32,107,196,.1);  color:#206bc4; }
.icon-amber { background:rgba(245,158,11,.1);  color:#f59e0b; }
.icon-teal  { background:rgba(12,166,120,.1);  color:#0ca678; }
.icon-cyan  { background:rgba(8,145,178,.1);   color:#0891b2; }
.icon-rose  { background:rgba(239,68,68,.1);   color:#ef4444; }

.dash-card-title {
    font-size:.82rem; font-weight:700; color:var(--tblr-heading-color,#0f172a);
    margin:0; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.dash-more-link {
    font-size:.7rem; font-weight:600; color:var(--tblr-primary,#206bc4);
    text-decoration:none; display:inline-flex; align-items:center; gap:.2rem;
    white-space:nowrap; flex-shrink:0; padding:.2rem .55rem; border-radius:50px;
    transition:background .15s ease;
}
.dash-more-link:hover { background:rgba(32,107,196,.08); }

/* ── ROW ITEMS (fully clickable) ── */
.dash-rows { display:flex; flex-direction:column; }

.dash-row {
    display:flex; align-items:center; gap:.75rem;
    padding:.65rem 1rem;
    border-bottom:1px solid var(--tblr-border-color,#e2e8f0);
    text-decoration:none; transition:background .14s ease;
    cursor:pointer;
}
.dash-row:last-child { border-bottom:none; }
.dash-row:hover { background:rgba(32,107,196,.04); }
[data-theme="dark"] .dash-row:hover { background:rgba(59,130,246,.07); }

.dash-sico {
    width:30px; height:30px; min-width:30px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.88rem;
}
.dash-row-info { min-width:0; flex:1; }
.dri-top { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-bottom:.18rem; }
.dri-name {
    font-size:.8rem; font-weight:700; color:var(--tblr-heading-color,#0f172a);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1;
}
.dri-sub {
    font-size:.7rem; color:var(--tblr-text-muted,#64748b);
    display:flex; align-items:center; gap:.28rem;
    margin-top:.2rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.dri-tags { display:flex; flex-wrap:wrap; gap:.22rem; margin-top:.22rem; }
.dtag {
    font-size:.6rem; font-weight:600; padding:.14em .42em; border-radius:50px;
    background:rgba(0,0,0,.05); color:var(--tblr-text-muted,#64748b);
    border:1px solid rgba(0,0,0,.07); display:inline-flex; align-items:center; gap:.18rem;
    white-space:nowrap;
}
[data-theme="dark"] .dtag { background:rgba(255,255,255,.07); border-color:rgba(255,255,255,.11); color:#8a99ad; }

.dash-row-arrow {
    font-size:.82rem; color:var(--tblr-text-muted,#64748b); opacity:.28; flex-shrink:0;
    transition:transform .16s ease, opacity .16s ease;
}
.dash-row:hover .dash-row-arrow { transform:translateX(3px); opacity:.65; }

/* Status badges */
.dsbadge {
    font-size:.6rem; font-weight:700; padding:.18em .5em; border-radius:50px;
    white-space:nowrap; flex-shrink:0;
}
.dsbadge.teal    { background:rgba(12,166,120,.1);  color:#0ca678; border:1px solid rgba(12,166,120,.2); }
.dsbadge.warning { background:rgba(245,158,11,.1);  color:#d97706; border:1px solid rgba(245,158,11,.2); }
.dsbadge.danger  { background:rgba(239,68,68,.1);   color:#ef4444; border:1px solid rgba(239,68,68,.2); }
.dsbadge.muted   { background:rgba(100,116,139,.1); color:#64748b; border:1px solid rgba(100,116,139,.2); }

.dash-tbadge {
    font-size:.6rem; font-weight:600; padding:.18em .45em; border-radius:50px;
    display:inline-flex; align-items:center; gap:.22rem; white-space:nowrap; flex-shrink:0;
}
.dash-tbadge.danger  { background:rgba(239,68,68,.08);   color:#ef4444; }
.dash-tbadge.warning { background:rgba(245,158,11,.08);  color:#d97706; }
.dash-tbadge.muted   { background:rgba(100,116,139,.08); color:#64748b; }

/* ── FEED ROWS ── */
.dash-feed { display:flex; flex-direction:column; }
.dash-feed-row {
    display:flex; align-items:flex-start; gap:.7rem;
    padding:.65rem 1rem;
    border-bottom:1px solid var(--tblr-border-color,#e2e8f0);
    text-decoration:none; transition:background .14s ease; cursor:pointer;
}
.dash-feed-row:last-child { border-bottom:none; }
.dash-feed-row:hover { background:rgba(32,107,196,.04); }
[data-theme="dark"] .dash-feed-row:hover { background:rgba(59,130,246,.07); }
.dash-feed-info { min-width:0; flex:1; }
.dfi-top { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-bottom:.2rem; }
.dfi-name {
    font-size:.8rem; font-weight:700; color:var(--tblr-heading-color,#0f172a);
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.dfi-time {
    font-size:.66rem; color:var(--tblr-text-muted,#64748b);
    white-space:nowrap; display:inline-flex; align-items:center; gap:.22rem; flex-shrink:0;
}
.dfi-mat {
    font-size:.66rem; font-weight:600; color:#0891b2;
    background:rgba(8,145,178,.08); border:1px solid rgba(8,145,178,.14);
    padding:.12em .42em; border-radius:50px; display:inline-flex; align-items:center;
    gap:.22rem; margin-bottom:.2rem; max-width:100%; overflow:hidden;
    text-overflow:ellipsis; white-space:nowrap;
}
.dfi-txt {
    font-size:.75rem; color:var(--tblr-text-muted,#64748b); line-height:1.45;
}

/* Avatar pill */
.dfa {
    width:32px; height:32px; min-width:32px; border-radius:50%;
    color:#fff; font-weight:800; font-size:.72rem;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.dfa.sm { width:28px; height:28px; min-width:28px; font-size:.66rem; }

/* ── QUICK ACCESS GRID ── */
.dash-qgrid {
    display:grid; grid-template-columns:repeat(3,1fr);
    gap:.5rem; padding:.65rem;
}
.dash-qtile {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:.35rem; padding:.65rem .4rem; border-radius:10px; text-decoration:none;
    background:var(--tblr-body-bg,#f4f6fa);
    border:1px solid var(--tblr-border-color,#e2e8f0);
    transition:all .18s ease;
}
.dash-qtile:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.07); border-color:var(--tblr-primary,#206bc4); }
[data-theme="dark"] .dash-qtile { background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.09); }
.dash-qtile i { font-size:1.25rem; line-height:1; }
.dash-qtile span { font-size:.67rem; font-weight:700; color:var(--tblr-heading-color,#0f172a); text-align:center; line-height:1.2; }

/* ── EMPTY STATE ── */
.dash-empty {
    display:flex; flex-direction:column; align-items:center;
    padding:1.6rem 1rem; gap:.45rem; text-align:center;
}
.dash-empty-icon {
    width:36px; height:36px; border-radius:9px;
    display:flex; align-items:center; justify-content:center; font-size:1.05rem;
}
.dash-empty span { font-size:.78rem; color:var(--tblr-text-muted,#64748b); }

/* ══════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════ */

/* Tablet landscape → stack 2-col to 1-col */
@media (max-width: 1100px) {
    .dash-main-grid { grid-template-columns:1fr; }
    /* On wide tablet, right col comes first as a quick shortcuts row */
    .dash-col:last-child { order:-1; }
    /* Keep quick grid 6-across on tablet */
    .dash-qgrid { grid-template-columns:repeat(6,1fr); padding:.55rem; }
}

/* Tablet portrait */
@media (max-width: 768px) {
    .dash-kpi-grid { grid-template-columns:repeat(2,1fr); gap:.55rem; }
    .dash-hero-body { padding:1rem 1.1rem; gap:.75rem; }
    .dash-hero-avatar { width:42px; height:42px; font-size:1.1rem; }
    .dash-hero-greeting { font-size:.95rem; }
    .dash-kpi-value { font-size:1.35rem; }
    .dash-qgrid { grid-template-columns:repeat(6,1fr); gap:.4rem; }
}

/* Mobile (< 576px) */
@media (max-width: 576px) {
    .dash-hero-body   { padding:.8rem .95rem; gap:.6rem; }
    .dash-hero-avatar { width:38px; height:38px; font-size:1rem; }
    .dash-hero-greeting { font-size:.88rem; }
    .dash-hero-name   { max-width:120px; }
    .dash-hero-date   { font-size:.68rem; }
    .dash-portal-btn  { font-size:.72rem; padding:.4rem .85rem; }

    .dash-kpi-grid  { gap:.45rem; }
    .dash-kpi-card  { padding:.7rem .75rem; gap:.6rem; }
    .dash-kpi-icon  { width:32px; height:32px; font-size:.95rem; }
    .dash-kpi-label { font-size:.58rem; }
    .dash-kpi-value { font-size:1.2rem; }
    .dash-kpi-arrow { display:none; }

    .dash-card-head  { padding:.6rem .8rem; }
    .dash-card-title { font-size:.78rem; }

    .dash-row   { padding:.55rem .8rem; gap:.6rem; }
    .dri-name   { font-size:.76rem; }
    .dri-sub    { font-size:.66rem; }
    .dtag       { font-size:.57rem; }

    .dash-feed-row { padding:.55rem .8rem; gap:.6rem; }
    .dfi-txt    { font-size:.71rem; }

    .dash-qgrid { grid-template-columns:repeat(3,1fr); gap:.4rem; padding:.55rem; }
    .dash-qtile { padding:.55rem .35rem; }
    .dash-qtile i    { font-size:1.1rem; }
    .dash-qtile span { font-size:.62rem; }

    .dash-sico  { width:26px; height:26px; min-width:26px; font-size:.82rem; }
    .dfa        { width:28px; height:28px; min-width:28px; font-size:.68rem; }
    .dfa.sm     { width:24px; height:24px; min-width:24px; font-size:.62rem; }
}

/* Very small (< 380px) */
@media (max-width: 380px) {
    .dash-hero-role-badge { display:none; }
    .dash-kpi-label { font-size:.54rem; }
    .dash-kpi-value { font-size:1.1rem; }
    .dash-qgrid { grid-template-columns:repeat(3,1fr); }
    .dsbadge, .dash-tbadge { display:none; }
    .dash-row-arrow { display:none; }
}
</style>

@endsection
