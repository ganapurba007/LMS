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
        <div class="md-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">TOTAL SISWA</span>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: rgba(32,107,196,.1); color: #206bc4; font-size: 1rem;">
                    <i class="ti ti-users"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">{{ $totalStudentsCount }} <span class="text-muted fw-normal" style="font-size: .75rem;">Siswa</span></div>
        </div>
    </div>

    <!-- Card 2: Rata-rata Progres Materi -->
    <div class="col-6 col-lg-3">
        <div class="md-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">PROGRES MATERI</span>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: rgba(8,145,178,.1); color: #0891b2; font-size: 1rem;">
                    <i class="ti ti-books"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">{{ $avgClassMaterialProgress }}%</div>
        </div>
    </div>

    <!-- Card 3: Rata-rata Nilai Tugas -->
    <div class="col-6 col-lg-3">
        <div class="md-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">RATA-RATA TUGAS</span>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: rgba(12,166,120,.1); color: #0ca678; font-size: 1rem;">
                    <i class="ti ti-clipboard-check"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">{{ $avgClassAssignmentGrade !== null ? number_format($avgClassAssignmentGrade, 1) : '-' }}</div>
        </div>
    </div>

    <!-- Card 4: Rata-rata Nilai Kuis -->
    <div class="col-6 col-lg-3">
        <div class="md-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">RATA-RATA KUIS</span>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: rgba(245,158,11,.1); color: #d97706; font-size: 1rem;">
                    <i class="ti ti-award"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">{{ $avgClassQuizScore !== null ? number_format($avgClassQuizScore, 1) : '-' }}</div>
        </div>
    </div>
</div>

<!-- Filter Options Card -->
<div class="md-card mb-4 p-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
        <!-- Filter Kelas -->
        <div class="col-12 col-md-5">
            <label class="md-form-label mb-1" style="font-size: 0.78rem;">
                Filter Kelas:
            </label>
            <select name="class_id" class="form-select form-select-sm select2" onchange="this.form.submit()">
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
            <label class="md-form-label mb-1" style="font-size: 0.78rem;">
                Filter Mata Pelajaran:
            </label>
            <select name="subject_id" class="form-select form-select-sm select2" onchange="this.form.submit()">
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
            <a href="{{ route('admin.reports.index') }}" class="md-btn-secondary w-100 justify-content-center" style="padding: .38rem .8rem; font-size: .8rem;">
                <i class="ti ti-refresh"></i> <span>Reset</span>
            </a>
        </div>
    </form>
</div>

<!-- Analytics Table Card with DataTables -->
<div class="md-card">
    <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; background: rgba(12,166,120,.1); color: #0ca678; font-size: 0.85rem;">
                <i class="ti ti-table"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Data Siswa &amp; Rekapitulasi Nilai</h6>
        </div>
    </div>

    <div class="md-table-wrap">
        <table id="reportsTable" class="table table-hover md-table w-100 mb-0">
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
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                {{ $student->name }}
                            </div>
                            <div class="text-muted" style="font-size: 0.74rem;">
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
                                <div class="progress flex-grow-1 rounded-pill" style="height: 6px; background-color: #E2E8F0;">
                                    <div class="progress-bar rounded-pill" role="progressbar" 
                                         style="width: {{ $student->materials_percentage }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" 
                                         aria-valuenow="{{ $student->materials_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small fw-bold text-primary" style="font-size: 0.78rem; min-width: 42px;">{{ $student->materials_percentage }}%</span>
                            </div>
                        </td>
                        <td class="text-center" data-order="{{ $student->avg_assignment_grade ?? -1 }}">
                            @if(!is_null($student->avg_assignment_grade))
                                <span class="md-badge teal fw-bold" style="font-size: 0.78rem; padding: .26rem .65rem;">
                                    {{ number_format($student->avg_assignment_grade, 1) }} <span class="fw-normal text-muted" style="font-size: .68rem;">/ 100</span>
                                </span>
                            @else
                                <span class="md-badge gray">
                                    Belum ada nilai
                                </span>
                            @endif
                        </td>
                        <td class="text-center" data-order="{{ $student->avg_quiz_score ?? -1 }}">
                            @if(!is_null($student->avg_quiz_score))
                                <span class="md-badge amber fw-bold" style="font-size: 0.78rem; padding: .26rem .65rem;">
                                    {{ number_format($student->avg_quiz_score, 1) }} <span class="fw-normal text-muted" style="font-size: .68rem;">/ 100</span>
                                </span>
                            @else
                                <span class="md-badge gray">
                                    Belum ada nilai
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row">
                            <i class="ti ti-users-minus"></i>
                            Tidak ditemukan siswa pada filter kelas atau mata pelajaran yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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