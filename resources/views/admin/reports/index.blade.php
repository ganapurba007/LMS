@extends('layouts.be.master')

@section('header_title', 'Master Data — Laporan & Rekap Nilai Siswa')

@section('content')
@include('admin._partials.master-data-styles')

@php
    $studentCollection = $students;
    $totalStudentsCount = $students->count();
    
    $validMaterials = $studentCollection->filter(fn($s) => isset($s->materials_percentage));
    $avgClassMaterialProgress = $validMaterials->count() > 0 ? round($validMaterials->avg('materials_percentage'), 1) : 0;
    
    $validAssignments = $studentCollection->filter(fn($s) => !is_null($s->avg_assignment_grade));
    $avgClassAssignmentGrade = $validAssignments->count() > 0 ? round($validAssignments->avg('avg_assignment_grade'), 1) : null;
    
    $validQuizzes = $studentCollection->filter(fn($s) => !is_null($s->avg_quiz_score));
    $avgClassQuizScore = $validQuizzes->count() > 0 ? round($validQuizzes->avg('avg_quiz_score'), 1) : null;
@endphp

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;">
            <i class="ti ti-chart-bar"></i>
        </div>
        <div>
            <h5 class="md-title">Rekapitulasi Nilai Siswa</h5>
        </div>
    </div>
    <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="md-btn-primary">
        <i class="ti ti-file-spreadsheet"></i>
        <span>Export Excel</span>
    </a>
</div>

{{-- Flash Alert --}}
@if(session('success'))
    <div class="md-alert success mb-4">
        <i class="ti ti-check-circle"></i> {{ session('success') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<!-- Metric Overview Summary Widgets -->
<div class="row g-3 g-md-3 mb-4">
    <!-- Card 1: Total Siswa -->
    <div class="col-6 col-lg-3">
        <div class="card md-card rp-metric-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="rp-metric-label">TOTAL SISWA</span>
                <div class="rp-metric-icon blue">
                    <i class="ti ti-users"></i>
                </div>
            </div>
            <div class="rp-metric-value">{{ $totalStudentsCount }} <span class="rp-metric-unit">Siswa</span></div>
        </div>
    </div>

    <!-- Card 2: Rata-rata Progres Materi -->
    <div class="col-6 col-lg-3">
        <div class="card md-card rp-metric-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="rp-metric-label">PROGRES MATERI</span>
                <div class="rp-metric-icon cyan">
                    <i class="ti ti-books"></i>
                </div>
            </div>
            <div class="rp-metric-value">{{ $avgClassMaterialProgress }}%</div>
        </div>
    </div>

    <!-- Card 3: Rata-rata Nilai Tugas -->
    <div class="col-6 col-lg-3">
        <div class="card md-card rp-metric-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="rp-metric-label">RATA-RATA TUGAS</span>
                <div class="rp-metric-icon teal">
                    <i class="ti ti-clipboard-check"></i>
                </div>
            </div>
            <div class="rp-metric-value">{{ $avgClassAssignmentGrade !== null ? number_format($avgClassAssignmentGrade, 1) : '-' }}</div>
        </div>
    </div>

    <!-- Card 4: Rata-rata Nilai Kuis -->
    <div class="col-6 col-lg-3">
        <div class="card md-card rp-metric-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="rp-metric-label">RATA-RATA KUIS</span>
                <div class="rp-metric-icon amber">
                    <i class="ti ti-award"></i>
                </div>
            </div>
            <div class="rp-metric-value">{{ $avgClassQuizScore !== null ? number_format($avgClassQuizScore, 1) : '-' }}</div>
        </div>
    </div>
</div>

<!-- Filter Options Card -->
<div class="card md-card rp-filter-card mb-4">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2.5 align-items-center">
        <!-- Filter Kelas -->
        <div class="col-12 col-md-5">
            <select name="class_id" class="form-select select2 rp-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Mata Pelajaran -->
        <div class="col-12 col-md-5">
            <select name="subject_id" class="form-select select2 rp-select" onchange="this.form.submit()">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ $selectedSubjectId == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} ({{ $s->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Reset Filter Button -->
        <div class="col-12 col-md-2">
            <a href="{{ route('admin.reports.index') }}" class="md-btn-secondary w-100 justify-content-center rp-btn-reset" title="Reset Filter">
                <i class="ti ti-rotate-clockwise"></i> <span>Reset</span>
            </a>
        </div>
    </form>
</div>

<!-- Analytics Table Card with DataTables -->
<div class="card md-card rp-table-card">
    <div class="rp-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rp-header-icon">
                <i class="ti ti-table"></i>
            </div>
            <h6 class="rp-card-title mb-0">Data Siswa &amp; Rekapitulasi Nilai</h6>
        </div>
    </div>

    <div class="md-table-wrap">
        <table id="reportsTable" class="table table-hover md-table rp-table w-100 mb-0">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th style="min-width: 170px;">Progres Materi</th>
                    <th class="text-center" style="width: 140px;">Rata-rata Tugas</th>
                    <th class="text-center" style="width: 140px;">Rata-rata Kuis</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="rp-student-name">
                                {{ $student->name }}
                            </div>
                            <div class="rp-student-email">
                                {{ $student->email }}
                            </div>
                        </td>
                        <td>
                            <span class="md-badge blue">
                                <i class="ti ti-school me-1"></i> {{ $student->schoolClass->name ?? '-' }}
                            </span>
                        </td>
                        <td data-order="{{ $student->materials_percentage }}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress rp-progress flex-grow-1 rounded-pill">
                                     <div class="progress-bar rounded-pill" role="progressbar" 
                                          style="width: {{ $student->materials_percentage }}%;" 
                                          aria-valuenow="{{ $student->materials_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="rp-progress-text">{{ $student->materials_percentage }}%</span>
                            </div>
                        </td>
                        <td class="text-center" data-order="{{ $student->avg_assignment_grade ?? -1 }}">
                            @if(!is_null($student->avg_assignment_grade))
                                <span class="md-badge teal fw-bold rp-grade-badge">
                                    {{ number_format($student->avg_assignment_grade, 1) }} <span class="rp-grade-max">/ 100</span>
                                </span>
                            @else
                                <span class="rp-grade-empty">
                                    <i class="ti ti-minus me-0.5"></i> Belum ada nilai
                                </span>
                            @endif
                        </td>
                        <td class="text-center" data-order="{{ $student->avg_quiz_score ?? -1 }}">
                            @if(!is_null($student->avg_quiz_score))
                                <span class="md-badge amber fw-bold rp-grade-badge">
                                    {{ number_format($student->avg_quiz_score, 1) }} <span class="rp-grade-max">/ 100</span>
                                </span>
                            @else
                                <span class="rp-grade-empty">
                                    <i class="ti ti-minus me-0.5"></i> Belum ada nilai
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap blue">
                                    <i class="ti ti-chart-bar-off"></i>
                                </div>
                                <div class="md-empty-title">Tidak Ditemukan Data Nilai Siswa</div>
                                <div class="md-empty-desc">
                                    @if(request('class_id') || request('subject_id') || request('search'))
                                        Tidak ada data rekapitulasi nilai siswa yang cocok dengan filter kelas atau mata pelajaran yang dipilih.
                                    @else
                                        Pilih kelas dan mata pelajaran pada filter di atas untuk melihat rekapitulasi nilai tugas dan kuis siswa.
                                    @endif
                                </div>
                                @if(request('class_id') || request('subject_id') || request('search'))
                                    <div class="md-empty-action">
                                        <a href="{{ route('admin.reports.index') }}" class="md-btn-secondary" style="font-size:.78rem;padding:.35rem .8rem;">
                                            <i class="ti ti-x"></i> Reset Filter
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
/* ==========================================================================
   REPORTS / REKAPITULASI NILAI STYLES (Light & Dark Compatible)
   ========================================================================== */

/* Metric Cards */
.rp-metric-card {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform .15s ease, box-shadow .15s ease;
}
.rp-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.rp-metric-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}
.rp-metric-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.rp-metric-unit {
    font-size: .75rem;
    font-weight: 500;
    color: #64748b;
}
.rp-metric-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.rp-metric-icon.blue { background: rgba(32, 107, 196, 0.12); color: #206bc4; }
.rp-metric-icon.cyan { background: rgba(8, 145, 178, 0.12); color: #0891b2; }
.rp-metric-icon.teal { background: rgba(12, 166, 120, 0.12); color: #0ca678; }
.rp-metric-icon.amber { background: rgba(245, 158, 11, 0.12); color: #d97706; }

/* Filter Card */
.rp-filter-card {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px;
    padding: .85rem 1.15rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.rp-btn-reset {
    height: 38px !important;
    padding: 0 .9rem !important;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .4rem !important;
}

/* Select2 Container Overrides for ruangterra */
.rp-filter-card .select2-container {
    width: 100% !important;
}
.rp-filter-card .select2-container--default .select2-selection--single {
    height: 38px !important;
    background-color: var(--tblr-card-bg, #ffffff) !important;
    border: 1px solid var(--tblr-border-color, #cbd5e1) !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 .65rem !important;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.rp-filter-card .select2-container--default .select2-selection--single:hover {
    border-color: #94a3b8 !important;
}
.rp-filter-card .select2-container--default.select2-container--open .select2-selection--single,
.rp-filter-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
}
.rp-filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 0 !important;
    color: #0f172a !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
}
.rp-filter-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 8px !important;
}
.rp-filter-card .select2-container--default .select2-selection--single .select2-selection__clear {
    margin-right: 18px !important;
    color: #94a3b8 !important;
    font-size: 1.05rem !important;
    line-height: 36px !important;
}
.rp-filter-card .select2-container--default .select2-selection--single .select2-selection__clear:hover {
    color: #ef4444 !important;
}

/* Table Card & Header */
.rp-table-card {
    background: var(--tblr-card-bg, #ffffff);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 12px;
    overflow: hidden;
}
.rp-card-header {
    padding: .9rem 1.25rem;
    border-bottom: 1px solid var(--tblr-border-color, #e2e8f0);
    background: var(--tblr-card-bg, #ffffff);
}
.rp-header-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(12, 166, 120, 0.12);
    color: #0ca678;
    font-size: 0.95rem;
}
.rp-card-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0f172a;
}

/* Student Cells */
.rp-student-name {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
}
.rp-student-email {
    font-size: 0.74rem;
    color: #64748b;
    margin-top: .1rem;
}

/* Progress Bar */
.rp-progress {
    height: 7px;
    background-color: #e2e8f0;
}
.rp-progress .progress-bar {
    background: linear-gradient(90deg, #184e66, #296d8c);
}
.rp-progress-text {
    font-size: 0.8rem;
    font-weight: 800;
    color: #184e66;
    min-width: 44px;
}

/* Grades */
.rp-grade-badge {
    font-size: 0.8rem !important;
    padding: .28rem .7rem !important;
}
.rp-grade-max {
    font-size: .68rem;
    opacity: 0.8;
    font-weight: 500;
}
.rp-grade-empty {
    display: inline-flex;
    align-items: center;
    padding: .25rem .6rem;
    border-radius: 6px;
    font-size: 0.74rem;
    font-weight: 600;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #64748b;
}

/* DataTables Styling */
.dataTables_wrapper {
    padding: .85rem 1.15rem;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: .85rem;
    font-size: .82rem;
    color: #475569;
}
.dataTables_wrapper .dataTables_length select {
    padding: .3rem 1.8rem .3rem .6rem;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-size: .8rem;
    color: #0f172a;
}
.dataTables_wrapper .dataTables_filter input {
    padding: .32rem .75rem;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-size: .8rem;
    color: #0f172a;
    outline: none;
    margin-left: .5rem;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}
.dataTables_wrapper .dataTables_info {
    padding-top: .85rem;
    font-size: .8rem;
    color: #64748b;
}
.dataTables_wrapper .dataTables_paginate {
    padding-top: .85rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    padding: .28rem .65rem !important;
    font-size: .8rem !important;
    margin: 0 .15rem !important;
    border: 1px solid transparent !important;
    color: #334155 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #184e66 !important;
    border-color: #184e66 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    background: transparent !important;
    border-color: transparent !important;
}

/* ==========================================================================
   DARK MODE OVERRIDES ([data-theme="dark"])
   ========================================================================== */
[data-theme="dark"] .rp-metric-card,
[data-theme="dark"] .rp-filter-card,
[data-theme="dark"] .rp-table-card {
    background: var(--tblr-card-bg, #182234);
    border-color: var(--tblr-border-color, rgba(255, 255, 255, 0.1));
}
[data-theme="dark"] .rp-metric-label {
    color: #94a3b8;
}
[data-theme="dark"] .rp-metric-value {
    color: #f8fafc;
}
[data-theme="dark"] .rp-metric-unit {
    color: #94a3b8;
}
[data-theme="dark"] .rp-metric-icon.blue { background: rgba(59, 130, 246, 0.18); color: #60a5fa; }
[data-theme="dark"] .rp-metric-icon.cyan { background: rgba(8, 145, 178, 0.18); color: #38bdf8; }
[data-theme="dark"] .rp-metric-icon.teal { background: rgba(12, 166, 120, 0.18); color: #34d399; }
[data-theme="dark"] .rp-metric-icon.amber { background: rgba(245, 158, 11, 0.18); color: #fbbf24; }


[data-theme="dark"] .rp-filter-card .select2-container--default .select2-selection--single {
    background-color: rgba(15, 23, 42, 0.75) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}
[data-theme="dark"] .rp-filter-card .select2-container--default .select2-selection--single:hover {
    border-color: rgba(255, 255, 255, 0.3) !important;
}
[data-theme="dark"] .rp-filter-card .select2-container--default.select2-container--open .select2-selection--single,
[data-theme="dark"] .rp-filter-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2) !important;
}
[data-theme="dark"] .rp-filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #f8fafc !important;
}
[data-theme="dark"] .rp-filter-card .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #94a3b8 transparent transparent transparent !important;
}
[data-theme="dark"] .rp-filter-card .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #94a3b8 transparent !important;
}

[data-theme="dark"] .rp-card-header {
    background: transparent;
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .rp-card-title {
    color: #f8fafc;
}

[data-theme="dark"] .rp-student-name {
    color: #f8fafc;
}
[data-theme="dark"] .rp-student-email {
    color: #94a3b8;
}

[data-theme="dark"] .rp-progress {
    background-color: rgba(255, 255, 255, 0.1);
}
[data-theme="dark"] .rp-progress .progress-bar {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
}
[data-theme="dark"] .rp-progress-text {
    color: #60a5fa;
}

[data-theme="dark"] .rp-grade-empty {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.12);
    color: #94a3b8;
}

/* DataTables Dark Mode */
[data-theme="dark"] .dataTables_wrapper .dataTables_length,
[data-theme="dark"] .dataTables_wrapper .dataTables_filter,
[data-theme="dark"] .dataTables_wrapper .dataTables_info {
    color: #94a3b8;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_length select {
    background-color: rgba(15, 23, 42, 0.75);
    border-color: #334155;
    color: #f8fafc;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_filter input {
    background-color: rgba(15, 23, 42, 0.75);
    border-color: #334155;
    color: #f8fafc;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_filter input::placeholder {
    color: #64748b;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #60a5fa;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #cbd5e1 !important;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
}
[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current,
[data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #2563eb !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable && $('#reportsTable tbody tr td').length > 1) {
            $('#reportsTable').DataTable({
                pageLength: 15,
                lengthMenu: [
                    [10, 15, 25, 50, -1],
                    [10, 15, 25, 50, 'Semua']
                ],
                order: [[0, 'asc']],
                language: {
                    search: "Cari Siswa:",
                    searchPlaceholder: "Ketik nama/email/kelas...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Menampilkan 0 data",
                    infoFiltered: "(disaring dari _MAX_ total siswa)",
                    zeroRecords: "Tidak ada siswa yang sesuai dengan pencarian",
                    paginate: {
                        first: '<i class="ti ti-chevrons-left"></i>',
                        last: '<i class="ti ti-chevrons-right"></i>',
                        next: '<i class="ti ti-chevron-right"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>'
                    }
                }
            });
        }
    });
</script>
@endpush