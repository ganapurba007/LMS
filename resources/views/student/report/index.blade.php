<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section */
        .report-hero {
            background: linear-gradient(135deg, #1e3d60 0%, #2b5788 55%, #20456E 100%);
            border-bottom: 3px solid #66A3BF;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            padding-top: 1.75rem !important;
            padding-bottom: 1.75rem !important;
        }
        .report-hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        .report-hero::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 25%;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Modern Metric Card */
        .metric-card-modern {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(51, 104, 160, 0.12);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            padding: 1.25rem 1.35rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .metric-card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(51, 104, 160, 0.08);
            border-color: rgba(51, 104, 160, 0.28);
        }
        .metric-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        /* Content Card Modern */
        .content-card-modern {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(51, 104, 160, 0.12);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: border-color 0.2s ease;
        }
        @media (min-width: 768px) {
            .content-card-modern {
                border-radius: 16px;
                margin-bottom: 1.75rem;
            }
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
        @media (min-width: 768px) {
            .content-card-header {
                padding: 1.1rem 1.45rem;
            }
        }

        /* Table Styling */
        .table-modern {
            margin-bottom: 0;
            width: 100%;
        }
        .table-modern thead th {
            background-color: #F1F5F9;
            color: #475569;
            font-size: 0.73rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            white-space: nowrap;
        }
        .table-modern tbody td {
            padding: 0.85rem 1rem;
            font-size: 0.84rem;
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
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(51, 104, 160, 0.1);
        }
        .hero-title {
            font-size: clamp(1.25rem, 3.2vw, 1.85rem);
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }
    </style>

    <!-- 1. Hero Section: Laporan Belajar Siswa -->
    <section class="report-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Top Bar: Kelas, Predikat & Tombol Kembali -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5 mb-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Kelas -->
                    <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5" 
                          style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                        <i class="ti ti-school text-primary"></i> Kelas {{ Auth::user()->schoolClass->name ?? 'Siswa' }}
                    </span>
                    <!-- Predikat -->
                    <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5"
                          style="background: {{ $predicateBadgeBg }}; color: {{ $predicateBadgeColor }} !important; font-size: 0.8rem;">
                        <i class="ti ti-medal"></i> Predikat: {{ $gradePredicate }}
                    </span>
                </div>

                <!-- Tombol Kembali -->
                <a href="{{ route('dashboard') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm hover-lift" 
                   style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(8px); font-size: 0.82rem;">
                    <i class="ti ti-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>

            <!-- Judul & Tanggal Diperbarui -->
            <div>
                <h1 class="hero-title mb-2 text-white" style="font-family: 'Jost', sans-serif;">
                    Laporan &amp; Rekapitulasi Hasil Belajar
                </h1>
                
                <div class="d-flex align-items-center gap-1.5 text-white-50 small" style="font-size: 0.82rem;">
                    <i class="ti ti-calendar text-warning"></i>
                    <span>Diperbarui: {{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2rem !important; padding-bottom: 3.5rem !important;">

        <!-- Metrics Summary Cards (4 Kartu Ringkasan Prestasi) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 g-lg-3.5 mb-4 pb-1">
            
            <!-- Card 1: Progres Materi -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Progres Materi</span>
                            <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #3368A0, #66A3BF);">
                                <i class="ti ti-books"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mb-2.5">
                            <h2 class="fs-3 fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; line-height: 1;">
                                {{ $materialProgressPercent }}%
                            </h2>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ $materialProgressPercent }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" 
                                 aria-valuenow="{{ $materialProgressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-circle-check text-success fs-6"></i>
                        <span>{{ $completedMaterials }} dari {{ $totalMaterials }} materi selesai</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Rata-Rata Tugas -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Rata-Rata Tugas</span>
                            <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #059669, #10B981);">
                                <i class="ti ti-clipboard-check"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mb-2.5">
                            <h2 class="fs-3 fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; line-height: 1;">
                                {{ $avgAssignmentScore }}
                            </h2>
                            <span class="text-muted small fw-semibold">/ 100</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ min(100, $avgAssignmentScore) }}%; background: linear-gradient(90deg, #059669, #10B981);" 
                                 aria-valuenow="{{ min(100, $avgAssignmentScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-checklist text-success fs-6"></i>
                        <span>{{ $submissions->whereNotNull('grade')->count() }} tugas dinilai</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Rata-Rata Kuis -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Rata-Rata Kuis</span>
                            <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #D97706, #F59E0B);">
                                <i class="ti ti-award"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mb-2.5">
                            <h2 class="fs-3 fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; line-height: 1;">
                                {{ $avgQuizScore }}
                            </h2>
                            <span class="text-muted small fw-semibold">/ 100</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ min(100, $avgQuizScore) }}%; background: linear-gradient(90deg, #D97706, #F59E0B);" 
                                 aria-valuenow="{{ min(100, $avgQuizScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-help-hexagon text-warning fs-6"></i>
                        <span>{{ $quizAttempts->count() }} kuis diselesaikan</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Indeks Gabungan -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Indeks Gabungan</span>
                            <div class="metric-icon-box text-white" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                                <i class="ti ti-medal"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mb-2.5">
                            <h2 class="fs-3 fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; line-height: 1;">
                                {{ $overallScore }}
                            </h2>
                            <span class="text-muted small fw-semibold">/ 100</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ min(100, $overallScore) }}%; background: linear-gradient(90deg, #6366F1, #8B5CF6);" 
                                 aria-valuenow="{{ min(100, $overallScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-chart-arrows text-primary fs-6"></i>
                        <span>Gabungan nilai tugas &amp; kuis</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. Rekapitulasi Performa per Mata Pelajaran (Subject Breakdown) -->
        <div class="content-card-modern mb-4">
            <div class="content-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 32px; height: 32px;">
                        <i class="ti ti-layout-grid fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                        Rekapitulasi per Mata Pelajaran
                    </h6>
                </div>
                <span class="badge rounded-pill px-2.5 py-1 font-bold small" style="background: rgba(51, 104, 160, 0.1); color: #20456E; font-size: 0.74rem;">
                    {{ count($subjectBreakdown) }} Mata Pelajaran
                </span>
            </div>

            <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="min-width: 170px;">Mata Pelajaran</th>
                            <th style="min-width: 200px;">Progres Materi</th>
                            <th class="text-center" style="min-width: 130px;">Rata-Rata Tugas</th>
                            <th class="text-center" style="min-width: 130px;">Rata-Rata Kuis</th>
                            <th class="text-center" style="min-width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectBreakdown as $idx => $item)
                            <tr>
                                <td class="text-center fw-bold text-muted" style="font-size: 0.82rem;">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0 shadow-xs" 
                                             style="width: 32px; height: 32px; background: linear-gradient(135deg, #3368A0, #66A3BF); font-size: 0.82rem;">
                                            <i class="ti ti-book"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $item['subject']->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between small text-muted mb-1.5" style="font-size: 0.76rem;">
                                        <span>{{ $item['materials_completed'] }}/{{ $item['materials_total'] }} Modul</span>
                                        <span class="fw-bold text-primary">{{ $item['materials_progress'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                                        <div class="progress-bar rounded-pill" role="progressbar" 
                                             style="width: {{ $item['materials_progress'] }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" 
                                             aria-valuenow="{{ $item['materials_progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(!is_null($item['avg_assignment']))
                                        @php
                                            $grade = $item['avg_assignment'];
                                            $badgeClass = $grade >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($grade >= 60 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                        @endphp
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold {{ $badgeClass }}" style="font-size: 0.78rem;">
                                            {{ number_format($grade, 1) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!is_null($item['avg_quiz']))
                                        @php
                                            $score = $item['avg_quiz'];
                                            $scoreBadgeClass = $score >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($score >= 60 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                        @endphp
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold {{ $scoreBadgeClass }}" style="font-size: 0.78rem;">
                                            {{ number_format($score, 1) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('student.materials.index', ['subject_id' => $item['subject']->id]) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1 hover-lift text-decoration-none"
                                       style="font-size: 0.76rem; border-color: rgba(51, 104, 160, 0.3); color: #20456E;">
                                        Buka <i class="ti ti-chevron-right fs-6"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="background: rgba(51, 104, 160, 0.08); width: 44px; height: 44px;">
                                        <i class="ti ti-folders-off fs-4 text-secondary"></i>
                                    </div>
                                    <div class="small fw-semibold text-dark">Belum Ada Data Mata Pelajaran</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Mata pelajaran kelas Anda akan tampil di sini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Detailed History: Riwayat Tugas & Riwayat Kuis (2 Kolom Sejajar Rapi) -->
        <div class="row g-3 g-lg-4">
            
            <!-- Kolom Kiri: Riwayat & Nilai Tugas -->
            <div class="col-lg-6">
                <div class="content-card-modern h-100 mb-0">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #059669, #10B981); width: 28px; height: 28px;">
                                <i class="ti ti-file-check fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Riwayat &amp; Nilai Tugas
                            </h6>
                        </div>
                        <span class="badge rounded-pill px-2.5 py-1 font-bold small" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.74rem;">
                            {{ $submissions->count() }} Terkumpul
                        </span>
                    </div>

                    <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 140px;">Tugas</th>
                                    <th style="min-width: 100px;">Pelajaran</th>
                                    <th class="text-center" style="min-width: 90px;">Nilai</th>
                                    <th class="text-center" style="min-width: 70px;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $sub)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.86rem; max-width: 170px;">{{ $sub->assignment->title ?? 'Tugas' }}</div>
                                            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                <i class="ti ti-calendar text-secondary"></i> {{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d M Y') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-0.5 font-semibold text-truncate" style="background: #F1F5F9; color: #334155; font-size: 0.72rem; border: 1px solid #E2E8F0; max-width: 110px;">
                                                {{ $sub->assignment->subject->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(!is_null($sub->grade))
                                                @php
                                                    $subGrade = $sub->grade;
                                                    $subGradeClass = $subGrade >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($subGrade >= 60 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                                @endphp
                                                <span class="badge rounded-pill px-2.5 py-1 font-bold {{ $subGradeClass }}" style="font-size: 0.78rem;">
                                                    {{ number_format($subGrade, 1) }}
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-2 py-0.5 text-secondary" style="background: #F1F5F9; font-size: 0.7rem; border: 1px solid #E2E8F0;">
                                                    <i class="ti ti-clock me-0.5 text-warning"></i> Menunggu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($sub->assignment)
                                                <a href="{{ route('student.assignments.show', $sub->assignment) }}" 
                                                   class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 font-bold hover-lift text-decoration-none"
                                                   style="font-size: 0.74rem;">
                                                    Lihat <i class="ti ti-arrow-up-right"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                            <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.08); width: 44px; height: 44px;">
                                                <i class="ti ti-file-off fs-4 text-secondary"></i>
                                            </div>
                                            <div class="small fw-semibold text-dark">Belum Ada Tugas</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">Tugas yang Anda kumpulkan akan tampil di sini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat & Skor Kuis -->
            <div class="col-lg-6">
                <div class="content-card-modern h-100 mb-0">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #D97706, #F59E0B); width: 28px; height: 28px;">
                                <i class="ti ti-award fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Riwayat &amp; Skor Kuis
                            </h6>
                        </div>
                        <span class="badge rounded-pill px-2.5 py-1 font-bold small" style="background: rgba(245, 158, 11, 0.12); color: #D97706; font-size: 0.74rem;">
                            {{ $quizAttempts->count() }} Selesai
                        </span>
                    </div>

                    <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 140px;">Kuis</th>
                                    <th style="min-width: 100px;">Pelajaran</th>
                                    <th class="text-center" style="min-width: 90px;">Skor</th>
                                    <th class="text-center" style="min-width: 70px;">Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.86rem; max-width: 170px;">{{ $attempt->quiz->title ?? 'Kuis' }}</div>
                                            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                                <i class="ti ti-calendar-check text-secondary"></i> {{ $attempt->submitted_at ? $attempt->submitted_at->translatedFormat('d M Y') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-0.5 font-semibold text-truncate" style="background: #F1F5F9; color: #334155; font-size: 0.72rem; border: 1px solid #E2E8F0; max-width: 110px;">
                                                {{ $attempt->quiz->subject->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $qScore = $attempt->score;
                                                $qScoreClass = $qScore >= 75 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : ($qScore >= 60 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                                            @endphp
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold {{ $qScoreClass }}" style="font-size: 0.78rem;">
                                                {{ number_format($qScore, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($attempt->quiz)
                                                <a href="{{ route('student.quizzes.result', $attempt->quiz) }}" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 font-bold hover-lift text-decoration-none"
                                                   style="font-size: 0.74rem; border-color: rgba(51, 104, 160, 0.3); color: #20456E;">
                                                    Review <i class="ti ti-arrow-up-right"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                            <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.08); width: 44px; height: 44px;">
                                                <i class="ti ti-help-off fs-4 text-secondary"></i>
                                            </div>
                                            <div class="small fw-semibold text-dark">Belum Ada Kuis</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">Kuis yang Anda selesaikan akan tampil di sini.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
