@extends('layouts.be.master')

@section('header_title', 'Laporan & Rekap Nilai Siswa')

@push('styles')
<style>
    /* Modern Metric Cards */
    .metric-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(51, 104, 160, 0.12);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        padding: 1.15rem 1.25rem;
        transition: all 0.2s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
    .metric-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(51, 104, 160, 0.08);
        border-color: rgba(51, 104, 160, 0.28);
    }
    .metric-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    /* Content Card Modern */
    .content-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(51, 104, 160, 0.12);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .content-card-header {
        padding: 0.95rem 1.25rem;
        background: #F8FAFC;
        border-bottom: 1px solid rgba(51, 104, 160, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* Table Styling */
    .table-modern {
        margin-bottom: 0;
    }
    .table-modern thead th {
        background-color: #F1F5F9;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(51, 104, 160, 0.12);
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 0.85rem 1rem;
        font-size: 0.85rem;
        color: #1e293b;
        border-bottom: 1px solid rgba(51, 104, 160, 0.07);
        vertical-align: middle;
    }
    .table-modern tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-modern tbody tr:hover {
        background-color: #F8FAFC;
    }
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }

    /* Custom DataTables Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 0.75rem 1.25rem;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 0.85rem 1.25rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        padding: 0.35rem 0.75rem;
        font-size: 0.84rem;
        outline: none;
        box-shadow: none;
        transition: border-color 0.2s ease;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3368A0;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        padding: 0.35rem 1.75rem 0.35rem 0.75rem;
        font-size: 0.84rem;
    }
    .dataTables_wrapper .pagination .page-link {
        border-radius: 6px;
        margin: 0 2px;
        font-size: 0.82rem;
        color: #20456E;
    }
    .dataTables_wrapper .pagination .page-item.active .page-link {
        background-color: #20456E;
        border-color: #20456E;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
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

<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 heading-custom">Laporan &amp; Rekapitulasi Nilai Siswa</h3>
        <p class="text-muted-custom small mb-0">Pantau progres penyelesaian materi, nilai tugas, dan capaian kuis kelas secara real-time</p>
    </div>
    <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
        <a href="{{ route('admin.reports.export-csv', request()->query()) }}" 
           class="btn btn-success rounded-pill px-3.5 py-2 font-bold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm hover-lift w-100 w-sm-auto"
           style="font-size: 0.84rem;">
            <i class="ti ti-file-spreadsheet fs-5"></i> Unduh Rekap CSV
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
        <i class="ti ti-circle-check fs-4"></i>
        <div class="fw-semibold small">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Metric Overview Summary Widgets -->
<div class="row g-2.5 g-md-3 mb-4">
    <!-- Card 1: Total Siswa -->
    <div class="col-6 col-lg-3">
        <div class="metric-card-modern">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Siswa</span>
                <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #20456E, #3368A0);">
                    <i class="ti ti-users"></i>
                </div>
            </div>
            <div>
                <div class="fw-extrabold text-dark fs-4 mb-0" style="font-family: 'Jost', sans-serif;">
                    {{ $totalStudentsCount }}
                </div>
                <div class="text-muted small" style="font-size: 0.72rem;">Siswa Terdaftar</div>
            </div>
        </div>
    </div>

    <!-- Card 2: Rata-rata Progres Materi -->
    <div class="col-6 col-lg-3">
        <div class="metric-card-modern">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Progres Materi</span>
                <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #3368A0, #66A3BF);">
                    <i class="ti ti-books"></i>
                </div>
            </div>
            <div>
                <div class="fw-extrabold text-primary fs-4 mb-0" style="font-family: 'Jost', sans-serif;">
                    {{ $avgClassMaterialProgress }}%
                </div>
                <div class="text-muted small" style="font-size: 0.72rem;">Rata-rata Penyelesaian</div>
            </div>
        </div>
    </div>

    <!-- Card 3: Rata-rata Nilai Tugas -->
    <div class="col-6 col-lg-3">
        <div class="metric-card-modern">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Rata-rata Tugas</span>
                <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #059669, #10B981);">
                    <i class="ti ti-clipboard-check"></i>
                </div>
            </div>
            <div>
                <div class="fw-extrabold text-success fs-4 mb-0" style="font-family: 'Jost', sans-serif;">
                    {{ $avgClassAssignmentGrade !== null ? $avgClassAssignmentGrade : '-' }}
                </div>
                <div class="text-muted small" style="font-size: 0.72rem;">Skala Nilai 0 - 100</div>
            </div>
        </div>
    </div>

    <!-- Card 4: Rata-rata Nilai Kuis -->
    <div class="col-6 col-lg-3">
        <div class="metric-card-modern">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Rata-rata Kuis</span>
                <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #D97706, #F59E0B);">
                    <i class="ti ti-award"></i>
                </div>
            </div>
            <div>
                <div class="fw-extrabold text-warning-emphasis fs-4 mb-0" style="font-family: 'Jost', sans-serif;">
                    {{ $avgClassQuizScore !== null ? $avgClassQuizScore : '-' }}
                </div>
                <div class="text-muted small" style="font-size: 0.72rem;">Skala Nilai 0 - 100</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Options Card -->
<div class="content-card-modern mb-4">
    <div class="content-card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 28px; height: 28px;">
                <i class="ti ti-filter fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Filter Data Rekapitulasi</h6>
        </div>
        <span class="text-muted small" style="font-size: 0.75rem;">Sesuaikan kelas dan mata pelajaran</span>
    </div>
    <div class="p-3 p-sm-4 bg-white">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2.5 g-sm-3 align-items-end">
            <!-- Filter Kelas -->
            <div class="col-12 col-md-5">
                <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.78rem;">
                    <i class="ti ti-school me-1 text-primary"></i> Pilih Kelas Siswa:
                </label>
                <select name="class_id" class="form-select form-select-sm shadow-none" onchange="this.form.submit()" style="border-radius: 8px; font-size: 0.84rem; padding: 0.5rem 0.75rem;">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Mata Pelajaran -->
            <div class="col-12 col-md-5">
                <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.78rem;">
                    <i class="ti ti-tag me-1 text-primary"></i> Pilih Mata Pelajaran (Opsional):
                </label>
                <select name="subject_id" class="form-select form-select-sm shadow-none" onchange="this.form.submit()" style="border-radius: 8px; font-size: 0.84rem; padding: 0.5rem 0.75rem;">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $selectedSubjectId == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Filter Button -->
            <div class="col-12 col-md-2">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-light border w-100 rounded-3 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 hover-lift text-decoration-none" style="font-size: 0.82rem;">
                    <i class="ti ti-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Analytics Table Card with DataTables -->
<div class="content-card-modern">
    <div class="content-card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #059669, #10B981); width: 28px; height: 28px;">
                <i class="ti ti-table fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Data Siswa &amp; Rekapitulasi Pembelajaran</h6>
        </div>
        <span class="badge rounded-pill px-2.5 py-1 font-bold small" style="background: rgba(51, 104, 160, 0.1); color: #20456E; font-size: 0.74rem;">
            Total {{ $totalStudentsCount }} Siswa
        </span>
    </div>

    <div class="py-2">
        <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
            <table id="reportsTable" class="table table-modern table-hover w-100 mb-0">
                <thead>
                    <tr>
                        <th class="ps-3 ps-sm-4 text-center" style="width: 50px;">No</th>
                        <th style="min-width: 220px;">Siswa</th>
                        <th style="min-width: 120px;">Kelas</th>
                        <th style="min-width: 190px;">Progres Materi</th>
                        <th style="min-width: 140px;" class="text-center">Rata-rata Tugas</th>
                        <th style="min-width: 140px;" class="text-center pe-3 pe-sm-4">Rata-rata Kuis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="ps-3 ps-sm-4 text-center fw-bold text-muted" style="font-size: 0.82rem;">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shrink-0 shadow-xs" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 34px; height: 34px; font-size: 0.82rem;">
                                        {{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem; max-width: 220px;">{{ $student->name }}</div>
                                        <div class="small text-muted text-truncate" style="font-size: 0.74rem; max-width: 220px;">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 font-semibold" style="background: rgba(51, 104, 160, 0.1); color: #20456E; font-size: 0.74rem;">
                                    <i class="ti ti-school me-0.5"></i> {{ $student->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td data-order="{{ $student->materials_percentage }}">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1 rounded-pill" style="height: 7px; background-color: #E2E8F0;">
                                        <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $student->materials_percentage }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" aria-valuenow="{{ $student->materials_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small fw-bold text-primary" style="font-size: 0.78rem; min-width: 42px;">{{ $student->materials_percentage }}%</span>
                                </div>
                            </td>
                            <td class="text-center" data-order="{{ $student->avg_assignment_grade ?? -1 }}">
                                @if(!is_null($student->avg_assignment_grade))
                                    @php
                                        $grade = $student->avg_assignment_grade;
                                        $gradeClass = $grade >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($grade >= 60 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold {{ $gradeClass }}" style="font-size: 0.78rem;">
                                        {{ number_format($grade, 1) }} / 100
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5" style="font-size: 0.7rem;">Belum ada nilai</span>
                                @endif
                            </td>
                            <td class="text-center pe-3 pe-sm-4" data-order="{{ $student->avg_quiz_score ?? -1 }}">
                                @if(!is_null($student->avg_quiz_score))
                                    @php
                                        $score = $student->avg_quiz_score;
                                        $scoreClass = $score >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($score >= 60 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold {{ $scoreClass }}" style="font-size: 0.78rem;">
                                        {{ number_format($score, 1) }} / 100
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5" style="font-size: 0.7rem;">Belum ada nilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="background: rgba(51, 104, 160, 0.06); width: 52px; height: 52px;">
                                    <i class="ti ti-users-minus fs-2 text-muted"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Siswa</h6>
                                <p class="small text-muted mb-0">Tidak ditemukan siswa pada filter kelas atau mata pelajaran yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
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