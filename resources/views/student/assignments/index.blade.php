<x-app-layout>
    <!-- Include Bootstrap, Tabler Icons & Custom Theme CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        .assignments-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 50%, #2b5788 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
        }
        .assignments-hero::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 450px;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .assignment-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(51, 104, 160, 0.12);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .assignment-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(51, 104, 160, 0.16);
            border-color: rgba(102, 163, 191, 0.4);
        }
        .assignment-card-header {
            min-height: 125px;
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }
        .assignment-watermark {
            position: absolute;
            right: -10px;
            bottom: -15px;
            font-size: 5rem;
            opacity: 0.18;
            color: #ffffff;
            pointer-events: none;
            transition: transform 0.4s ease;
        }
        .assignment-card:hover .assignment-watermark {
            transform: scale(1.1) rotate(-8deg);
            opacity: 0.26;
        }
        .filter-pill-btn {
            border-radius: 50px;
            padding: 0.45rem 1.15rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1.5px solid rgba(51, 104, 160, 0.2);
            background: #ffffff;
            color: #3368A0;
        }
        .filter-pill-btn:hover {
            background: rgba(51, 104, 160, 0.08);
            border-color: #3368A0;
            color: #20456E;
            transform: translateY(-1px);
        }
        .filter-pill-btn.active {
            background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);
            color: #ffffff !important;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(51, 104, 160, 0.3);
        }
        .view-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid rgba(51, 104, 160, 0.2);
            background: #ffffff;
            color: #64748b;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .view-btn.active {
            background: #3368A0;
            color: #ffffff;
            border-color: #3368A0;
            box-shadow: 0 2px 8px rgba(51, 104, 160, 0.25);
        }
        .table-custom-assignments {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-assignments thead th {
            background: #F2EFE7;
            color: #20456E;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-assignments tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(51, 104, 160, 0.07);
        }
        .table-custom-assignments tbody tr:hover {
            background-color: rgba(200, 223, 219, 0.25);
        }
    </style>

    <!-- 1. Dedicated Page Hero Header Banner -->
    <section class="assignments-hero py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            <div class="row align-items-center g-4">
                
                <div class="col-lg-7 text-center text-lg-start">
                    <!-- Breadcrumbs & Class Badge -->
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-2 mb-3">
                        <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-bold d-inline-flex align-items-center gap-1.5" style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                            <i class="ti ti-school text-primary"></i> Kelas {{ Auth::user()->schoolClass->name ?? 'Siswa SMA' }}
                        </span>
                        <span class="badge px-3 py-1.5 rounded-pill font-semibold d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); font-size: 0.8rem;">
                            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Dashboard</a>
                            <i class="ti ti-chevron-right fs-6"></i>
                            <span class="text-white font-bold">Tugas</span>
                        </span>
                    </div>

                    <!-- Heading & Subtitle -->
                    <h1 class="display-6 fw-extrabold mb-3 text-white" style="font-family: 'Jost', sans-serif; letter-spacing: -0.5px; line-height: 1.2;">
                        Daftar Tugas & Lembar Kerja Siswa
                    </h1>
                    <p class="text-white-50 fs-6 mb-4 pe-lg-4" style="line-height: 1.6;">
                        Pantau tenggat waktu tugas kelas, kerjakan soal essay interaktif, unggah jawaban, dan periksa nilai serta umpan balik dari guru pengampu.
                    </p>

                    <!-- Hero Quick Search Bar -->
                    <form action="{{ route('student.assignments.index') }}" method="GET" class="p-2 rounded-pill shadow-lg d-flex align-items-center max-w-lg mx-auto mx-lg-0 border" style="background-color: #F2EFE7; border-color: rgba(102, 163, 191, 0.3) !important;">
                        <i class="ti ti-search text-muted fs-4 ms-3 me-2"></i>
                        <input type="text" 
                               name="search" 
                               id="heroAssignmentSearch"
                               value="{{ request('search') }}" 
                               class="form-control shadow-none bg-transparent text-dark border-0" 
                               placeholder="Cari judul tugas, mata pelajaran, atau guru...">
                        @if(request('subject_id'))
                            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <button type="submit" class="btn text-white rounded-pill px-4 py-2 font-bold shrink-0 shadow-sm" style="background-color: #3368A0;">
                            Cari <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>

                <!-- Right Column: Quick Progress Overview Card -->
                <div class="col-lg-5">
                    <div class="p-4 rounded-4 shadow-xl text-dark position-relative border" style="background: rgba(242, 239, 231, 0.95); backdrop-filter: blur(12px); border-color: rgba(255, 255, 255, 0.5) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.15) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #059669, #10B981); width: 34px; height: 34px;">
                                    <i class="ti ti-checklist fs-5"></i>
                                </div>
                                <span class="fw-bold text-dark fs-6" style="font-family: 'Jost', sans-serif;">Penyelesaian Tugas</span>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 font-bold text-white shadow-sm" style="background: {{ $progressPercent >= 100 ? '#10B981' : '#3368A0' }}; font-size: 0.75rem;">
                                {{ $progressPercent }}% Terkumpul
                            </span>
                        </div>

                        <!-- Progress Bar Indicator -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-muted small fw-semibold mb-1">
                                <span>Progres Pengumpulan</span>
                                <span class="text-dark font-bold">{{ $submittedCount }} dari {{ $totalAssignments }} Tugas</span>
                            </div>
                            <div class="progress rounded-pill shadow-inner" style="height: 10px; background: rgba(51, 104, 160, 0.12);">
                                <div class="progress-bar rounded-pill progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ $progressPercent }}%; background: linear-gradient(90deg, #059669, #10B981);" 
                                     aria-valuenow="{{ $progressPercent }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Mini Stats 3 Columns -->
                        <div class="row g-2 text-center pt-2">
                            <div class="col-4">
                                <div class="p-2.5 rounded-3 border bg-white">
                                    <div class="text-primary fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $totalAssignments }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Total Tugas</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2.5 rounded-3 border bg-white">
                                    <div class="text-success fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $submittedCount }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Terkumpul</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2.5 rounded-3 border bg-white">
                                    <div class="text-warning fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $unsubmittedCount }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Belum Kirim</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Ambient Geometric Vectors Layer & Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">

        <!-- 2. Interactive Filter Bar & View Mode Controller (Lega dan Tidak Mepet) -->
        <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: #ffffff; border: 1px solid rgba(51, 104, 160, 0.14) !important; padding: 1.25rem 1.5rem;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                
                <!-- Left: Subject Filter Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider me-1 d-none d-md-inline" style="font-size: 0.75rem;">
                        <i class="ti ti-filter me-1"></i> Mapel:
                    </span>
                    
                    <a href="{{ route('student.assignments.index', array_merge(request()->except(['subject_id', 'page']))) }}" 
                       class="filter-pill-btn {{ !request('subject_id') ? 'active' : '' }}">
                        Semua Mapel
                        <span class="badge rounded-pill {{ !request('subject_id') ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} ms-1">
                            {{ $totalAssignments }}
                        </span>
                    </a>

                    @foreach($subjects as $subj)
                        <a href="{{ route('student.assignments.index', array_merge(request()->except(['page']), ['subject_id' => $subj->id])) }}" 
                           class="filter-pill-btn {{ request('subject_id') == $subj->id ? 'active' : '' }}">
                            {{ $subj->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Right: Status Dropdown Filter & View Mode Switcher -->
                <div class="d-flex align-items-center justify-content-between justify-content-lg-end gap-2.5">
                    <!-- Status Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 border" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                style="background-color: #F2EFE7; border-color: rgba(51, 104, 160, 0.2) !important; color: #20456E;">
                            <i class="ti ti-adjustments-horizontal"></i>
                            <span>
                                @if(request('status') === 'submitted')
                                    Status: Sudah Dikumpulkan
                                @elseif(request('status') === 'unsubmitted')
                                    Status: Belum Dikumpulkan
                                @elseif(request('status') === 'graded')
                                    Status: Telah Dinilai
                                @else
                                    Semua Status
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-1 text-sm">
                            <li>
                                <a class="dropdown-item rounded-2 {{ !request('status') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.assignments.index', array_merge(request()->except(['status', 'page']))) }}">
                                    <i class="ti ti-list-details me-2"></i> Semua Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 {{ request('status') === 'submitted' ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.assignments.index', array_merge(request()->except(['page']), ['status' => 'submitted'])) }}">
                                    <i class="ti ti-circle-check text-success me-2"></i> Sudah Dikumpulkan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 {{ request('status') === 'unsubmitted' ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.assignments.index', array_merge(request()->except(['page']), ['status' => 'unsubmitted'])) }}">
                                    <i class="ti ti-clock text-warning me-2"></i> Belum Dikumpulkan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 {{ request('status') === 'graded' ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.assignments.index', array_merge(request()->except(['page']), ['status' => 'graded'])) }}">
                                    <i class="ti ti-award text-primary me-2"></i> Telah Dinilai
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Reset Filter Button -->
                    @if(request('search') || request('subject_id') || request('status'))
                        <a href="{{ route('student.assignments.index') }}" 
                           class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1"
                           title="Hapus Semua Filter">
                            <i class="ti ti-refresh"></i>
                            <span class="d-none d-sm-inline">Reset</span>
                        </a>
                    @endif

                    <!-- View Toggle Buttons (Grid vs List) -->
                    <div class="d-flex align-items-center gap-1 bg-light p-1 rounded-3 border">
                        <button type="button" 
                                class="view-btn active" 
                                id="btnAssignmentGrid" 
                                onclick="switchAssignmentsView('grid')" 
                                title="Tampilan Kartu (Grid)">
                            <i class="ti ti-layout-grid fs-5"></i>
                        </button>
                        <button type="button" 
                                class="view-btn" 
                                id="btnAssignmentList" 
                                onclick="switchAssignmentsView('list')" 
                                title="Tampilan Daftar (Tabel)">
                            <i class="ti ti-list fs-5"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Active Filter Chips Indicator -->
            @if(request('search') || request('subject_id') || request('status'))
                <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                    <span class="text-muted small fw-semibold">Filter aktif:</span>
                    @if(request('search'))
                        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                            Pencarian: "{{ request('search') }}"
                            <a href="{{ route('student.assignments.index', array_merge(request()->except(['search', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('subject_id'))
                        @php
                            $activeSubject = $subjects->firstWhere('id', request('subject_id'));
                        @endphp
                        @if($activeSubject)
                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                Mapel: {{ $activeSubject->name }}
                                <a href="{{ route('student.assignments.index', array_merge(request()->except(['subject_id', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                    @endif
                    @if(request('status'))
                        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                            Status: {{ request('status') === 'submitted' ? 'Sudah Dikumpulkan' : (request('status') === 'graded' ? 'Telah Dinilai' : 'Belum Dikumpulkan') }}
                            <a href="{{ route('student.assignments.index', array_merge(request()->except(['status', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 3. Assignments Container: GRID VIEW (Default) -->
        <div id="assignmentsGridView">
            <div class="row g-4" id="assignmentsGridRow">
                @forelse($assignments as $index => $asg)
                    @php
                        $sub = $submissions->get($asg->id);
                        $isSubmitted = !is_null($sub);
                        $isGraded = $isSubmitted && !is_null($sub->grade);
                        $isOverdue = $asg->due_date && $asg->due_date->isPast() && !$isSubmitted;

                        $gradients = [
                            'linear-gradient(135deg, #1e3a8a 0%, #3368A0 100%)',
                            'linear-gradient(135deg, #065f46 0%, #059669 100%)',
                            'linear-gradient(135deg, #78350f 0%, #d97706 100%)',
                            'linear-gradient(135deg, #3730a3 0%, #4f46e5 100%)',
                            'linear-gradient(135deg, #831843 0%, #be185d 100%)',
                        ];
                        $cardBgGradient = $gradients[$index % count($gradients)];
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4 assignment-item-card" 
                         data-title="{{ strtolower($asg->title) }}" 
                         data-subject="{{ strtolower($asg->subject->name ?? '') }}" 
                         data-instructor="{{ strtolower($asg->instructor->name ?? '') }}"
                         data-status="{{ $isGraded ? 'graded' : ($isSubmitted ? 'submitted' : 'unsubmitted') }}">
                        <div class="assignment-card h-100 position-relative">
                            
                            <!-- Card Header with Gradient Banner -->
                            <div class="assignment-card-header" style="background: {{ $cardBgGradient }};">
                                <i class="ti ti-clipboard-list assignment-watermark"></i>
                                
                                <div class="d-flex align-items-center justify-content-between position-relative z-1 mb-2">
                                    <span class="badge text-white font-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.22); backdrop-filter: blur(6px); font-size: 0.76rem;">
                                        <i class="ti ti-tag me-1"></i> {{ $asg->subject->name ?? 'Mata Pelajaran' }}
                                    </span>

                                    @if($isGraded)
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #10B981; color: #ffffff; font-size: 0.72rem;">
                                            <i class="ti ti-award"></i> Nilai: {{ number_format($sub->grade, 1) }}
                                        </span>
                                    @elseif($isSubmitted)
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #0284c7; color: #ffffff; font-size: 0.72rem;">
                                            <i class="ti ti-check"></i> Terkumpul
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #DC2626; color: #ffffff; font-size: 0.72rem;">
                                            <i class="ti ti-alert-triangle"></i> Terlewat
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.88); color: #475569; font-size: 0.72rem;">
                                            <i class="ti ti-clock"></i> Belum Kirim
                                        </span>
                                    @endif
                                </div>

                                <h5 class="fw-extrabold text-white mb-0 position-relative z-1" style="font-family: 'Jost', sans-serif; font-size: 1.25rem; line-height: 1.35;">
                                    {{ $asg->title }}
                                </h5>
                            </div>

                            <!-- Card Middle Content -->
                            <div class="p-4 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <!-- Teacher Info -->
                                    <div class="d-flex align-items-center gap-2.5 mb-3">
                                        <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 38px; height: 38px; font-size: 0.9rem;">
                                            {{ strtoupper(substr($asg->instructor->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-muted small" style="font-size: 0.72rem;">Guru Pengampu</div>
                                            <div class="fw-bold text-dark text-truncate" style="margin-top: -2px; font-size: 0.9rem;">
                                                {{ $asg->instructor->name ?? 'Pengajar SMA' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description Snippet -->
                                    <p class="text-muted small mb-3" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ !empty($asg->description) ? \Illuminate\Support\Str::limit(strip_tags($asg->description), 90) : 'Tidak ada petunjuk khusus untuk tugas ini.' }}
                                    </p>

                                    <!-- Deadline Info Pill -->
                                    <div class="p-2.5 rounded-3 mb-2 d-flex align-items-center justify-content-between {{ $isOverdue ? 'bg-danger-subtle text-danger' : 'bg-light text-secondary' }}">
                                        <span class="small fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-clock-hour-4 {{ $isOverdue ? 'text-danger' : 'text-primary' }}"></i> Batas Waktu:
                                        </span>
                                        <span class="small fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                            {{ $asg->due_date ? $asg->due_date->format('d M Y H:i') . ' WIB' : 'Tanpa Batas' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Footer Row -->
                                <div class="pt-3 border-top d-flex align-items-center justify-content-between text-muted small" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                    <span><i class="ti ti-school me-1 text-info"></i> {{ $asg->schoolClass->name ?? 'Kelas Saya' }}</span>
                                    @if($isGraded)
                                        <span class="text-success fw-bold"><i class="ti ti-circle-check me-1"></i> Dinilai</span>
                                    @elseif($isSubmitted)
                                        <span class="text-info fw-bold"><i class="ti ti-hourglass me-1"></i> Menunggu Nilai</span>
                                    @else
                                        <span class="text-muted"><i class="ti ti-pencil me-1"></i> Perlu Dikerjakan</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Bottom Action Button -->
                            <div class="p-4 pt-0 bg-white">
                                <a href="{{ route('student.assignments.show', $asg) }}" 
                                   class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                                   style="background: {{ $isGraded ? 'linear-gradient(135deg, #059669 0%, #10B981 100%)' : ($isSubmitted ? 'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)' : 'linear-gradient(135deg, #3368A0 0%, #66A3BF 100%)') }};">
                                    @if($isGraded)
                                        <i class="ti ti-award fs-5"></i> Lihat Hasil & Nilai
                                    @elseif($isSubmitted)
                                        <i class="ti ti-file-check fs-5"></i> Buka Jawaban Dikirim
                                    @else
                                        <i class="ti ti-pencil fs-5"></i> Kerjakan Tugas Ini
                                    @endif
                                    <i class="ti ti-arrow-right ms-auto"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="background: rgba(51, 104, 160, 0.08); width: 80px; height: 80px;">
                                <i class="ti ti-clipboard-off fs-1 text-muted"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Belum Ada Tugas</h4>
                            <p class="text-muted small max-w-md mx-auto mb-4">
                                @if(request('search') || request('subject_id') || request('status'))
                                    Tidak ditemukan tugas yang sesuai dengan kriteria filter yang Anda pilih. Silakan sesuaikan kata kunci atau reset filter.
                                @else
                                    Bapak/Ibu guru pengampu belum menerbitkan tugas baru untuk kelas Anda. Pantau terus halaman ini secara berkala.
                                @endif
                            </p>
                            @if(request('search') || request('subject_id') || request('status'))
                                <div>
                                    <a href="{{ route('student.assignments.index') }}" class="btn btn-primary rounded-pill px-4 py-2 font-bold shadow-sm">
                                        <i class="ti ti-refresh me-1"></i> Reset Semua Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. Assignments Container: LIST VIEW (Clean Modern Table) -->
        <div id="assignmentsListView" style="display: none;">
            <div class="table-custom-assignments mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center ps-3" style="width: 50px;">No</th>
                                <th style="width: 28%;">Judul Tugas</th>
                                <th style="width: 15%;">Mata Pelajaran</th>
                                <th style="width: 15%;">Guru Pengampu</th>
                                <th style="width: 15%;">Batas Waktu (Deadline)</th>
                                <th style="width: 13%;">Status</th>
                                <th class="text-center" style="width: 8%;">Nilai</th>
                                <th class="text-center pe-3" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentsTableBody">
                            @forelse($assignments as $asg)
                                @php
                                    $sub = $submissions->get($asg->id);
                                    $isSubmitted = !is_null($sub);
                                    $isGraded = $isSubmitted && !is_null($sub->grade);
                                    $isOverdue = $asg->due_date && $asg->due_date->isPast() && !$isSubmitted;
                                    $rowNo = ($assignments->currentPage() - 1) * $assignments->perPage() + $loop->iteration;
                                @endphp
                                <tr class="assignment-item-row"
                                    data-title="{{ strtolower($asg->title) }}" 
                                    data-subject="{{ strtolower($asg->subject->name ?? '') }}" 
                                    data-instructor="{{ strtolower($asg->instructor->name ?? '') }}"
                                    data-status="{{ $isGraded ? 'graded' : ($isSubmitted ? 'submitted' : 'unsubmitted') }}">
                                    <td class="text-center ps-3 fw-bold text-muted" style="font-size: 0.85rem;">
                                        {{ $rowNo }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm" style="background: linear-gradient(135deg, #059669, #10B981); width: 40px; height: 40px;">
                                                <i class="ti ti-clipboard-list fs-4"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('student.assignments.show', $asg) }}" class="fw-bold text-dark text-decoration-none hover:text-primary" style="font-size: 0.95rem;">
                                                    {{ $asg->title }}
                                                </a>
                                                <div class="text-muted small" style="font-size: 0.72rem;">
                                                    Kelas {{ $asg->schoolClass->name ?? 'Saya' }} • Dibuat {{ $asg->created_at ? $asg->created_at->format('d M Y') : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge px-2.5 py-1.5 rounded-pill font-bold" style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.78rem;">
                                            <i class="ti ti-tag me-1"></i> {{ $asg->subject->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="background: #64748b; width: 28px; height: 28px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($asg->instructor->name ?? 'G', 0, 1)) }}
                                            </div>
                                            <span class="text-dark small fw-semibold">{{ $asg->instructor->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                            <i class="ti ti-clock {{ $isOverdue ? 'text-danger' : 'text-muted' }} me-1"></i>
                                            {{ $asg->due_date ? $asg->due_date->format('d M Y H:i') . ' WIB' : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($isGraded)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.78rem;">
                                                <i class="ti ti-award"></i> Dinilai
                                            </span>
                                        @elseif($isSubmitted)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; font-size: 0.78rem;">
                                                <i class="ti ti-circle-check"></i> Sudah Dikirim
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(220, 38, 38, 0.12); color: #dc2626; font-size: 0.78rem;">
                                                <i class="ti ti-alert-triangle"></i> Terlewat
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(217, 119, 6, 0.12); color: #d97706; font-size: 0.78rem;">
                                                <i class="ti ti-clock"></i> Belum Kirim
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($isGraded)
                                            <span class="badge bg-success px-2.5 py-1 rounded-pill fw-bold text-white fs-6">
                                                {{ number_format($sub->grade, 1) }}
                                            </span>
                                        @elseif($isSubmitted)
                                            <span class="text-muted small">Menunggu</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('student.assignments.show', $asg) }}" 
                                               class="btn btn-sm rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1 text-white hover-lift text-center" 
                                               style="background: {{ $isGraded ? '#059669' : ($isSubmitted ? '#0284c7' : '#3368A0') }}; min-width: 105px;">
                                                @if($isGraded)
                                                    <i class="ti ti-award"></i> Lihat Nilai
                                                @elseif($isSubmitted)
                                                    <i class="ti ti-file-check"></i> Jawaban
                                                @else
                                                    <i class="ti ti-pencil"></i> Kerjakan
                                                @endif
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="ti ti-clipboard-off fs-2 d-block mb-2 text-secondary"></i>
                                        Belum ada tugas pembelajaran untuk kelas Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- 5. Pagination Navigation -->
        @if($assignments->hasPages())
            <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 pt-3">
                <div class="text-muted small">
                    Menampilkan <span class="fw-bold text-dark">{{ $assignments->firstItem() }}</span> - <span class="fw-bold text-dark">{{ $assignments->lastItem() }}</span> dari <span class="fw-bold text-dark">{{ $assignments->total() }}</span> total tugas
                </div>
                <div>
                    {{ $assignments->links() }}
                </div>
            </div>
        @endif

    </div>

    <!-- Bootstrap Bundle JS & Client-side View Toggle Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Switch between Grid View and List View
        function switchAssignmentsView(mode) {
            const gridEl = document.getElementById('assignmentsGridView');
            const listEl = document.getElementById('assignmentsListView');
            const btnGrid = document.getElementById('btnAssignmentGrid');
            const btnList = document.getElementById('btnAssignmentList');

            if (mode === 'list') {
                gridEl.style.display = 'none';
                listEl.style.display = 'block';
                btnGrid.classList.remove('active');
                btnList.classList.add('active');
                localStorage.setItem('assignments_view_mode', 'list');
            } else {
                gridEl.style.display = 'block';
                listEl.style.display = 'none';
                btnList.classList.remove('active');
                btnGrid.classList.add('active');
                localStorage.setItem('assignments_view_mode', 'grid');
            }
        }

        // Restore saved view mode preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedMode = localStorage.getItem('assignments_view_mode');
            if (savedMode === 'list') {
                switchAssignmentsView('list');
            }

            // Realtime fast client-side filter on typing in hero search
            const heroSearch = document.getElementById('heroAssignmentSearch');
            if (heroSearch) {
                heroSearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.assignment-item-card');
                    const rows = document.querySelectorAll('.assignment-item-row');

                    cards.forEach(card => {
                        const title = card.getAttribute('data-title') || '';
                        const subject = card.getAttribute('data-subject') || '';
                        const instructor = card.getAttribute('data-instructor') || '';
                        if (title.includes(query) || subject.includes(query) || instructor.includes(query)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    rows.forEach(row => {
                        const title = row.getAttribute('data-title') || '';
                        const subject = row.getAttribute('data-subject') || '';
                        const instructor = row.getAttribute('data-instructor') || '';
                        if (title.includes(query) || subject.includes(query) || instructor.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
