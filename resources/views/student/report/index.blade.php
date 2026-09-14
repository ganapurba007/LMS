<x-app-layout>
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section Laporan Belajar Siswa (Proporsional, Elegan & Seimbang) */
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

        /* Modern Metric Card (Kompak, Rapi, Sejajar & Bernapas Lega) */
        .metric-card-modern {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(32, 69, 110, 0.12);
            box-shadow: 0 2px 8px rgba(32, 69, 110, 0.03);
            padding: 1.25rem 1.3rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .metric-card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(32, 69, 110, 0.08);
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
            border: 1px solid rgba(32, 69, 110, 0.12);
            box-shadow: 0 2px 10px rgba(32, 69, 110, 0.03);
            overflow: hidden;
            margin-bottom: 1.75rem;
        }
        .content-card-header {
            padding: 0.95rem 1.35rem;
            background: #F8FAFC;
            border-bottom: 1px solid rgba(32, 69, 110, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        /* Table Styling (Rapi, Berjarak Nyaman, Tidak Dempet) */
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
            padding: 0.85rem 1.15rem;
            border-bottom: 1px solid rgba(32, 69, 110, 0.12);
            white-space: nowrap;
        }
        .table-modern tbody td {
            padding: 0.9rem 1.15rem;
            font-size: 0.85rem;
            color: #1e293b;
            border-bottom: 1px solid rgba(32, 69, 110, 0.07);
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
            box-shadow: 0 4px 10px rgba(32, 69, 110, 0.1);
        }
    </style>

    <!-- 1. Dedicated Hero Section: Laporan Belajar Siswa (Proporsional & Pas) -->
    <section class="report-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Breadcrumbs & Badges Row -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2.5">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge px-3 py-1.5 rounded-pill shadow-2xs font-bold d-inline-flex align-items-center gap-1.5" style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.78rem;">
                        <i class="ti ti-school text-primary"></i> Kelas {{ Auth::user()->schoolClass->name ?? 'Siswa' }}
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill font-semibold d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.16); font-size: 0.78rem;">
                        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Dashboard</a>
                        <i class="ti ti-chevron-right fs-6"></i>
                        <span class="text-white font-bold">Laporan Diri</span>
                    </span>
                </div>

                <div class="text-white-50 small d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                    <i class="ti ti-calendar-check fs-5 text-warning"></i>
                    <span>Diperbarui: {{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <!-- Title & User Info -->
            <div class="row align-items-center g-3 pt-1">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm" style="background: rgba(255, 255, 255, 0.18); width: 46px; height: 46px;">
                            <i class="ti ti-report-analytics fs-2 text-warning"></i>
                        </div>
                        <div>
                            <h1 class="fs-4 fw-bold mb-1 text-white" style="font-family: 'Jost', sans-serif; letter-spacing: -0.3px; line-height: 1.3;">
                                Laporan Progres Belajar Diri
                            </h1>
                            <div class="text-white-50 small d-flex flex-wrap align-items-center gap-2" style="font-size: 0.82rem;">
                                <span>Siswa: <strong class="text-white">{{ Auth::user()->name }}</strong></span>
                                <span>•</span>
                                <span>{{ Auth::user()->email }}</span>
                                <span>•</span>
                                <span>Kelas {{ Auth::user()->schoolClass->name ?? 'Aktif' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Predikat & Skor Rata-rata -->
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex flex-column align-items-lg-end gap-1.5">
                        <div class="d-inline-flex align-items-center gap-2 px-4 py-1.5 rounded-pill shadow-sm"
                             style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);">
                            <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.6px;">Predikat:</span>
                            <span class="badge rounded-pill px-3 py-1 font-bold shadow-2xs" 
                                  style="background-color: {{ $predicateBadgeBg }}; color: {{ $predicateBadgeColor }}; font-size: 0.8rem;">
                                <i class="ti ti-trophy me-1"></i> {{ $gradePredicate }}
                            </span>
                        </div>
                        <div class="text-white-50 small pe-1" style="font-size: 0.78rem;">
                            Indeks Gabungan: <strong class="text-white fs-6">{{ $overallScore }}</strong> / 100
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area: Rapi, Bernapas Lega & Proporsional -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2rem !important; padding-bottom: 3.5rem !important;">

        <!-- Metrics Summary Cards (4 Kartu Ringkasan Prestasi Pas, Rapi & Seimbang) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 g-lg-3.5 mb-4 pb-1">
            
            <!-- Card 1: Progres Materi -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Progres Materi</span>
                            <div class="metric-icon-box" style="background: rgba(37, 99, 235, 0.08); color: #2563EB;">
                                <i class="ti ti-book-2"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mb-2.5">
                            <h2 class="fs-3 fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; line-height: 1;">
                                {{ $materialProgressPercent }}%
                            </h2>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                            <div class="progress-bar rounded-pill" role="progressbar" 
                                 style="width: {{ $materialProgressPercent }}%; background: linear-gradient(90deg, #2563EB, #38bdf8);" 
                                 aria-valuenow="{{ $materialProgressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(32, 69, 110, 0.1) !important; font-size: 0.76rem;">
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
                            <div class="metric-icon-box" style="background: rgba(16, 185, 129, 0.08); color: #10B981;">
                                <i class="ti ti-file-check"></i>
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
                                 style="width: {{ min(100, $avgAssignmentScore) }}%; background: linear-gradient(90deg, #10B981, #34d399);" 
                                 aria-valuenow="{{ min(100, $avgAssignmentScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(32, 69, 110, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-checklist text-success fs-6"></i>
                        <span>{{ $submissions->whereNotNull('grade')->count() }} tugas dinilai (Total {{ $submissions->count() }})</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Rata-Rata Kuis -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Rata-Rata Kuis</span>
                            <div class="metric-icon-box" style="background: rgba(245, 158, 11, 0.08); color: #F59E0B;">
                                <i class="ti ti-help-hexagon"></i>
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
                                 style="width: {{ min(100, $avgQuizScore) }}%; background: linear-gradient(90deg, #F59E0B, #fbbf24);" 
                                 aria-valuenow="{{ min(100, $avgQuizScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(32, 69, 110, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-award text-warning fs-6"></i>
                        <span>{{ $quizAttempts->count() }} kuis selesai dikerjakan</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Nilai Keseluruhan -->
            <div class="col">
                <div class="metric-card-modern h-100">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Nilai Keseluruhan</span>
                            <div class="metric-icon-box" style="background: rgba(139, 92, 246, 0.08); color: #8B5CF6;">
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
                                 style="width: {{ min(100, $overallScore) }}%; background: linear-gradient(90deg, #8B5CF6, #a78bfa);" 
                                 aria-valuenow="{{ min(100, $overallScore) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1.5 pt-2.5 mt-3 border-top" style="border-color: rgba(32, 69, 110, 0.1) !important; font-size: 0.76rem;">
                        <i class="ti ti-chart-arrows text-primary fs-6"></i>
                        <span>Evaluasi gabungan tugas &amp; kuis</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. Rekapitulasi Performa per Mata Pelajaran (Subject Breakdown) -->
        <div class="content-card-modern mb-4 pb-1">
            <div class="content-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-2 text-white shadow-2xs" style="background: #20456E; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                        <i class="ti ti-layout-grid fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                            Rekapitulasi Performa per Mata Pelajaran
                        </h5>
                        <div class="text-muted small" style="font-size: 0.76rem;">
                            Progres materi, nilai rata-rata tugas, dan skor kuis untuk setiap mata pelajaran
                        </div>
                    </div>
                </div>
                <span class="badge bg-white text-primary border rounded-pill px-3 py-1 font-bold small shadow-2xs" style="font-size: 0.76rem; border-color: rgba(32, 69, 110, 0.18) !important;">
                    {{ count($subjectBreakdown) }} Mata Pelajaran
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Mata Pelajaran</th>
                            <th style="width: 240px;">Progres Materi</th>
                            <th class="text-center" style="width: 150px;">Rata-Rata Tugas</th>
                            <th class="text-center" style="width: 150px;">Rata-Rata Kuis</th>
                            <th class="text-end" style="width: 135px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectBreakdown as $idx => $item)
                            <tr>
                                <td class="text-center fw-bold text-muted" style="font-size: 0.82rem;">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center shrink-0" 
                                             style="width: 32px; height: 32px; background: rgba(32, 69, 110, 0.08); color: #20456E;">
                                            <i class="ti ti-book fs-6"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $item['subject']->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between small text-muted mb-1.5" style="font-size: 0.76rem;">
                                        <span>{{ $item['materials_completed'] }}/{{ $item['materials_total'] }} Materi</span>
                                        <span class="fw-bold text-primary">{{ $item['materials_progress'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 50rem; background-color: #E2E8F0;">
                                        <div class="progress-bar rounded-pill" role="progressbar" 
                                             style="width: {{ $item['materials_progress'] }}%; background: linear-gradient(90deg, #20456E, #3368A0);" 
                                             aria-valuenow="{{ $item['materials_progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(!is_null($item['avg_assignment']))
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-2xs" style="background: #dcfce7; color: #15803d; font-size: 0.8rem; border: 1px solid #bbf7d0;">
                                            {{ $item['avg_assignment'] }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!is_null($item['avg_quiz']))
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-2xs" style="background: #eff6ff; color: #1d4ed8; font-size: 0.8rem; border: 1px solid #bfdbfe;">
                                            {{ $item['avg_quiz'] }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('student.materials.index', ['subject_id' => $item['subject']->id]) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1 hover-lift"
                                       style="font-size: 0.76rem; border-color: rgba(32, 69, 110, 0.3); color: #20456E;">
                                        Buka Materi <i class="ti ti-chevron-right fs-6"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="ti ti-folders-off fs-1 text-muted d-block mb-1.5"></i>
                                    Belum ada mata pelajaran yang terdaftar untuk kelas Anda.
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
                            <div class="rounded-2 text-white shadow-2xs" style="background: #10B981; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-file-check fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Riwayat &amp; Nilai Tugas
                            </h5>
                        </div>
                        <span class="badge bg-white text-success border rounded-pill px-2.5 py-1 font-bold small shadow-2xs" style="font-size: 0.74rem; border-color: rgba(16, 185, 129, 0.25) !important;">
                            {{ $submissions->count() }} Terkumpul
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tugas</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-end">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $sub)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $sub->assignment->title ?? 'Tugas' }}</div>
                                            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="ti ti-calendar text-secondary"></i> {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y, H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2.5 py-1 font-semibold" style="background: #F1F5F9; color: #334155; font-size: 0.74rem; border: 1px solid #E2E8F0;">
                                                {{ $sub->assignment->subject->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(!is_null($sub->grade))
                                                <span class="badge rounded-pill px-3 py-1 font-bold shadow-2xs" 
                                                      style="background: {{ $sub->grade >= 75 ? '#dcfce7' : '#fef3c7' }}; color: {{ $sub->grade >= 75 ? '#15803d' : '#b45309' }}; font-size: 0.8rem; border: 1px solid {{ $sub->grade >= 75 ? '#bbf7d0' : '#fde68a' }};">
                                                    {{ $sub->grade }} / 100
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-2.5 py-1 text-secondary" style="background: #F1F5F9; font-size: 0.72rem; border: 1px solid #E2E8F0;">
                                                    <i class="ti ti-clock me-1 text-warning"></i> Menunggu Koreksi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($sub->assignment)
                                                <a href="{{ route('student.assignments.show', $sub->assignment) }}" 
                                                   class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 font-bold hover-lift"
                                                   style="font-size: 0.75rem;">
                                                    Lihat <i class="ti ti-arrow-up-right"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                            <i class="ti ti-file-off fs-2 text-muted d-block mb-1.5"></i>
                                            Belum ada tugas yang dikumpulkan.
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
                            <div class="rounded-2 text-white shadow-2xs" style="background: #F59E0B; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-award fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Riwayat &amp; Skor Kuis
                            </h5>
                        </div>
                        <span class="badge bg-white text-warning border rounded-pill px-2.5 py-1 font-bold small shadow-2xs" style="font-size: 0.74rem; border-color: rgba(245, 158, 11, 0.25) !important;">
                            {{ $quizAttempts->count() }} Selesai
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kuis</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-end">Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $attempt->quiz->title ?? 'Kuis' }}</div>
                                            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                                <i class="ti ti-calendar-check text-secondary"></i> {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2.5 py-1 font-semibold" style="background: #F1F5F9; color: #334155; font-size: 0.74rem; border: 1px solid #E2E8F0;">
                                                {{ $attempt->quiz->subject->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-1 font-bold shadow-2xs" 
                                                  style="background: {{ $attempt->score >= 75 ? '#dcfce7' : ($attempt->score >= 60 ? '#eff6ff' : '#fee2e2') }}; color: {{ $attempt->score >= 75 ? '#15803d' : ($attempt->score >= 60 ? '#1d4ed8' : '#b91c1c') }}; font-size: 0.8rem; border: 1px solid {{ $attempt->score >= 75 ? '#bbf7d0' : ($attempt->score >= 60 ? '#bfdbfe' : '#fecaca') }};">
                                                {{ $attempt->score }} / 100
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($attempt->quiz)
                                                <a href="{{ route('student.quizzes.result', $attempt->quiz) }}" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 font-bold hover-lift"
                                                   style="font-size: 0.75rem; border-color: rgba(32, 69, 110, 0.3); color: #20456E;">
                                                    Review <i class="ti ti-arrow-up-right"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                            <i class="ti ti-help-off fs-2 text-muted d-block mb-1.5"></i>
                                            Belum ada kuis yang diselesaikan.
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
