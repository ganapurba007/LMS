<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        .quizzes-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 55%, #2b5788 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
        }
        .quizzes-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .quiz-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(51, 104, 160, 0.14);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px rgba(32, 69, 110, 0.12);
            border-color: rgba(102, 163, 191, 0.45);
        }
        .quiz-card-header {
            padding: 1.35rem 1.4rem 1.15rem;
            position: relative;
            overflow: hidden;
        }
        .quiz-watermark {
            position: absolute;
            right: -10px;
            bottom: -15px;
            font-size: 5.5rem;
            color: rgba(255, 255, 255, 0.14);
            pointer-events: none;
            line-height: 1;
        }
        .filter-pill-btn {
            border-radius: 50rem;
            padding: 0.5rem 1.15rem;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 1px solid rgba(51, 104, 160, 0.2);
            background: #ffffff;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .filter-pill-btn:hover, .filter-pill-btn.active {
            background: #20456E;
            color: #ffffff !important;
            border-color: #20456E;
            box-shadow: 0 2px 8px rgba(32, 69, 110, 0.25);
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
        .table-custom-quizzes {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-quizzes thead th {
            background: #F2EFE7;
            color: #20456E;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            padding: 1.1rem 1.25rem;
            border-bottom: 2px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-quizzes tbody td {
            padding: 1.15rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(51, 104, 160, 0.07);
        }
        .table-custom-quizzes tbody tr:hover {
            background-color: rgba(200, 223, 219, 0.22);
        }

        /* Responsive Mobile Adjustments (< 768px) */
        @media (max-width: 767.98px) {
            .quizzes-hero {
                padding-top: 1.5rem !important;
                padding-bottom: 1.75rem !important;
            }
            .quizzes-hero h1 {
                font-size: 1.45rem !important;
                line-height: 1.3 !important;
            }
            .quizzes-hero p.lead {
                font-size: 0.88rem !important;
                margin-bottom: 1.25rem !important;
            }
            .filter-pill-btn {
                padding: 0.4rem 0.9rem !important;
                font-size: 0.76rem !important;
            }
            .quiz-card {
                border-radius: 16px !important;
            }
            .quiz-card-header {
                padding: 1.15rem 1.15rem 1rem !important;
            }
            .quiz-card-header h5 {
                font-size: 1.1rem !important;
            }
            .quiz-card .p-4 {
                padding: 1.15rem !important;
            }
        }
    </style>

    <!-- 1. Dedicated Page Hero Header Banner -->
    <section class="quizzes-hero py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            <div class="row align-items-center g-4">
                
                <!-- Left: Title, Badges & Breadcrumb -->
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-bold d-inline-flex align-items-center gap-1.5" style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                            <i class="ti ti-school text-primary"></i> Kelas {{ Auth::user()->schoolClass->name ?? 'Siswa' }}
                        </span>
                        <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-semibold d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); font-size: 0.8rem;">
                            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Dashboard</a>
                            <i class="ti ti-chevron-right fs-6"></i>
                            <span class="text-white font-bold">Kuis</span>
                        </span>
                        <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-semibold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.25); color: #d1fae5; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.8rem;">
                            <i class="ti ti-award"></i> {{ $completedCount }} Selesai
                        </span>
                    </div>

                    <h1 class="display-5 fw-extrabold mb-2 text-white" style="font-family: 'Jost', sans-serif; letter-spacing: -0.5px;">
                        Kuis & Ujian Kelas
                    </h1>
                    <p class="text-white-50 lead fs-6 mb-4" style="max-width: 580px; line-height: 1.6;">
                        Uji pemahaman dan evaluasi penguasaan materi belajar Anda melalui kuis online interaktif dengan penilaian otomatis.
                    </p>

                    <!-- Quick Hero Search Input -->
                    <form action="{{ route('student.quizzes.index') }}" method="GET" class="d-flex align-items-center gap-2" style="max-width: 480px;">
                        @if(request('subject_id'))
                            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white p-1" style="border: 2px solid rgba(255, 255, 255, 0.3);">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                                <i class="ti ti-search fs-5"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   class="form-control border-0 shadow-none ps-2" 
                                   placeholder="Cari judul kuis, guru, atau mata pelajaran..." 
                                   id="quizSearchInput" 
                                   style="font-size: 0.9rem;">
                            <button type="submit" class="btn text-white rounded-pill px-4 font-bold" style="background: #20456E;">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Progress & Stats Overview Widget (Lega & Nyaman) -->
                <div class="col-lg-5">
                    <div class="p-4 rounded-4 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(14px); border: 1px solid rgba(255, 255, 255, 0.6);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #059669, #10B981); width: 36px; height: 36px;">
                                    <i class="ti ti-trophy fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif;">Pencapaian Kuis Saya</h6>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 font-bold text-white shadow-sm" style="background: #059669; font-size: 0.8rem;">
                                {{ $progressPercent }}% Selesai
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-muted small fw-semibold mb-1">
                                <span>Tingkat Kelulusan Kuis</span>
                                <span class="text-dark font-bold">{{ $completedCount }} dari {{ $totalQuizzes }} Kuis</span>
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

                        <!-- Mini Stats 3 Columns (Lega & Bersih) -->
                        <div class="row g-2 text-center pt-2">
                            <div class="col-4">
                                <div class="p-3 rounded-3 border bg-white shadow-2xs">
                                    <div class="text-primary fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $totalQuizzes }}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">Total Kuis</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 border bg-white shadow-2xs">
                                    <div class="text-success fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $completedCount }}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">Selesai</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 border bg-white shadow-2xs">
                                    <div class="text-warning fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $unattemptedCount }}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">Belum Ikut</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                <i class="ti ti-circle-check fs-4"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                <i class="ti ti-alert-triangle fs-4"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 2. Interactive Filter Bar & View Controller (Lega dan Longgar) -->
        <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: #ffffff; border: 1px solid rgba(51, 104, 160, 0.14) !important; padding: 1.25rem 1.5rem;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                
                <!-- Left: Subject Filter Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider me-1 d-none d-md-inline" style="font-size: 0.75rem;">
                        <i class="ti ti-filter me-1"></i> Mapel:
                    </span>
                    
                    <a href="{{ route('student.quizzes.index', array_merge(request()->except(['subject_id', 'page']))) }}" 
                       class="filter-pill-btn {{ !request('subject_id') ? 'active' : '' }}">
                        Semua Mapel
                        <span class="badge rounded-pill {{ !request('subject_id') ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} ms-1">
                            {{ $totalQuizzes }}
                        </span>
                    </a>

                    @foreach($subjects as $subj)
                        <a href="{{ route('student.quizzes.index', array_merge(request()->except(['page']), ['subject_id' => $subj->id])) }}" 
                           class="filter-pill-btn {{ request('subject_id') == $subj->id ? 'active' : '' }}">
                            {{ $subj->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Right: Status Dropdown Filter, Reset & View Toggle -->
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-lg-end">
                    
                    <!-- Status Dropdown Filter -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 dropdown-toggle d-flex align-items-center gap-1.5 font-semibold" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false" 
                                style="border-color: rgba(51, 104, 160, 0.25); color: #334155; font-size: 0.85rem;">
                            <i class="ti ti-adjustments-horizontal text-primary"></i>
                            <span>
                                @if(request('status') === 'completed')
                                    Status: Selesai
                                @elseif(request('status') === 'in_progress')
                                    Status: Sedang Dikerjakan
                                @elseif(request('status') === 'unattempted')
                                    Status: Belum Dikerjakan
                                @else
                                    Status: Semua
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 p-1" style="font-size: 0.85rem;">
                            <li><a class="dropdown-item rounded-2 py-1.5 {{ !request('status') ? 'active' : '' }}" href="{{ route('student.quizzes.index', array_merge(request()->except(['status', 'page']))) }}">Semua Status</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item rounded-2 py-1.5 {{ request('status') === 'completed' ? 'active' : '' }}" href="{{ route('student.quizzes.index', array_merge(request()->except(['page']), ['status' => 'completed'])) }}">Selesai (Skor Tersedia)</a></li>
                            <li><a class="dropdown-item rounded-2 py-1.5 {{ request('status') === 'in_progress' ? 'active' : '' }}" href="{{ route('student.quizzes.index', array_merge(request()->except(['page']), ['status' => 'in_progress'])) }}">Sedang Dikerjakan</a></li>
                            <li><a class="dropdown-item rounded-2 py-1.5 {{ request('status') === 'unattempted' ? 'active' : '' }}" href="{{ route('student.quizzes.index', array_merge(request()->except(['page']), ['status' => 'unattempted'])) }}">Belum Dikerjakan</a></li>
                        </ul>
                    </div>

                    <!-- Reset Filter Button -->
                    @if(request('search') || request('subject_id') || request('status'))
                        <a href="{{ route('student.quizzes.index') }}" 
                           class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 font-semibold"
                           title="Hapus Semua Filter" style="font-size: 0.85rem;">
                            <i class="ti ti-refresh"></i>
                            <span class="d-none d-sm-inline">Reset</span>
                        </a>
                    @endif

                    <!-- View Toggle Buttons (Grid vs List) -->
                    <div class="d-flex align-items-center gap-1 bg-light p-1 rounded-3 border">
                        <button type="button" 
                                class="view-btn active" 
                                id="btnQuizGrid" 
                                onclick="switchQuizView('grid')" 
                                title="Tampilan Kartu (Grid)">
                            <i class="ti ti-layout-grid fs-5"></i>
                        </button>
                        <button type="button" 
                                class="view-btn" 
                                id="btnQuizList" 
                                onclick="switchQuizView('list')" 
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
                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                            Pencarian: "{{ request('search') }}"
                            <a href="{{ route('student.quizzes.index', array_merge(request()->except(['search', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('subject_id'))
                        @php
                            $activeSubj = $subjects->firstWhere('id', request('subject_id'));
                        @endphp
                        @if($activeSubj)
                            <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                                Mapel: {{ $activeSubj->name }}
                                <a href="{{ route('student.quizzes.index', array_merge(request()->except(['subject_id', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                    @endif
                    @if(request('status'))
                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                            Status: {{ request('status') === 'completed' ? 'Selesai' : (request('status') === 'in_progress' ? 'Sedang Dikerjakan' : 'Belum Dikerjakan') }}
                            <a href="{{ route('student.quizzes.index', array_merge(request()->except(['status', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 3. Quizzes Container: GRID VIEW (Default) -->
        <div id="quizGridView">
            <div class="row g-4" id="quizGridRow">
                @forelse($quizzes as $index => $quiz)
                    @php
                        $userAttempt = $quiz->attempts->first();
                        $isCompleted = $userAttempt && !is_null($userAttempt->submitted_at);
                        $isInProgress = $userAttempt && is_null($userAttempt->submitted_at);
                        $isOverdue = $quiz->deadline && $quiz->deadline->isPast() && !$isCompleted;

                        $gradients = [
                            'linear-gradient(135deg, #1e3a8a 0%, #3368A0 100%)',
                            'linear-gradient(135deg, #065f46 0%, #059669 100%)',
                            'linear-gradient(135deg, #78350f 0%, #d97706 100%)',
                            'linear-gradient(135deg, #3730a3 0%, #4f46e5 100%)',
                            'linear-gradient(135deg, #831843 0%, #be185d 100%)',
                        ];
                        $cardBgGradient = $gradients[$index % count($gradients)];
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4 quiz-item-card" 
                         data-title="{{ strtolower($quiz->title) }}" 
                         data-subject="{{ strtolower($quiz->subject->name ?? '') }}" 
                         data-instructor="{{ strtolower($quiz->instructor->name ?? '') }}"
                         data-status="{{ $isCompleted ? 'completed' : ($isInProgress ? 'in_progress' : 'unattempted') }}">
                        <div class="quiz-card h-100 position-relative">
                            
                            <!-- Card Header with Gradient Banner -->
                            <div class="quiz-card-header" style="background: {{ $cardBgGradient }};">
                                <i class="ti ti-award quiz-watermark"></i>
                                
                                <div class="d-flex align-items-center justify-content-between position-relative z-1 mb-2">
                                    <span class="badge text-white font-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.22); backdrop-filter: blur(6px); font-size: 0.78rem;">
                                        <i class="ti ti-tag me-1"></i> {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                                    </span>

                                    @if($isCompleted)
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #10B981; color: #ffffff; font-size: 0.78rem;">
                                            <i class="ti ti-trophy"></i> Skor: {{ $userAttempt->score }}
                                        </span>
                                    @elseif($isInProgress)
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #f59e0b; color: #ffffff; font-size: 0.78rem;">
                                            <i class="ti ti-hourglass"></i> Sedang Dikerjakan
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #DC2626; color: #ffffff; font-size: 0.78rem;">
                                            <i class="ti ti-alert-triangle"></i> Waktu Habis
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.9); color: #334155; font-size: 0.78rem;">
                                            <i class="ti ti-clock"></i> Belum Ikut
                                        </span>
                                    @endif
                                </div>

                                <h5 class="fw-extrabold text-white mb-0 position-relative z-1" style="font-family: 'Jost', sans-serif; font-size: 1.25rem; line-height: 1.35;">
                                    {{ $quiz->title }}
                                </h5>
                            </div>

                            <!-- Card Middle Content (Lega & Nyaman) -->
                            <div class="p-4 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <!-- Teacher Info -->
                                    <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                        <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 38px; height: 38px; font-size: 0.9rem;">
                                            {{ strtoupper(substr($quiz->instructor->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-muted small" style="font-size: 0.72rem;">Guru Pembuat Kuis</div>
                                            <div class="fw-bold text-dark text-truncate" style="margin-top: -2px; font-size: 0.9rem;">
                                                {{ $quiz->instructor->name ?? 'Guru SMA' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quiz Parameters Pill Row -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="p-2.5 rounded-3 bg-light text-center border">
                                                <div class="text-muted small" style="font-size: 0.72rem;"><i class="ti ti-clock me-1 text-primary"></i> Durasi</div>
                                                <div class="fw-bold text-dark fs-6">{{ $quiz->formatted_duration }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-2.5 rounded-3 bg-light text-center border">
                                                <div class="text-muted small" style="font-size: 0.72rem;"><i class="ti ti-list-check me-1 text-success"></i> Soal</div>
                                                <div class="fw-bold text-dark fs-6">{{ $quiz->total_questions_count }} Butir</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deadline Info Pill -->
                                    <div class="p-2.5 rounded-3 mb-2 d-flex align-items-center justify-content-between {{ $isOverdue ? 'bg-danger-subtle text-danger' : 'bg-light text-secondary' }}">
                                        <span class="small fw-semibold d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-clock-hour-4 {{ $isOverdue ? 'text-danger' : 'text-primary' }}"></i> Batas Waktu:
                                        </span>
                                        <span class="small fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                            {{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') . ' WIB' : 'Tanpa Batas' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Action Button (Lega & Mudah Diklik) -->
                                <div class="pt-3 border-top mt-2" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                    @if($isCompleted)
                                        <a href="{{ route('student.quizzes.result', $quiz) }}" 
                                           class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                                           style="background: linear-gradient(135deg, #059669 0%, #10B981 100%);">
                                            <i class="ti ti-eye fs-5"></i> Lihat Hasil & Pembahasan
                                        </a>
                                    @elseif($isInProgress)
                                        <a href="{{ route('student.quizzes.attempt', $quiz) }}" 
                                           class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                                           style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                                            <i class="ti ti-player-play fs-5"></i> Lanjutkan Pengerjaan
                                        </a>
                                    @elseif($isOverdue)
                                        <button class="btn btn-secondary w-100 rounded-pill py-2.5 font-bold shadow-none d-flex align-items-center justify-content-center gap-2" disabled style="opacity: 0.65; cursor: not-allowed;">
                                            <i class="ti ti-lock fs-5"></i> Waktu Habis — Kuis Ditutup
                                        </button>
                                    @else
                                        <a href="{{ route('student.quizzes.show', $quiz) }}" 
                                           class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                                           style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%);">
                                            <i class="ti ti-arrow-right fs-5"></i> Ikuti Kuis Ini
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 rounded-4 shadow-sm text-center py-5" style="background-color: #ffffff; border: 1px dashed rgba(51, 104, 160, 0.25) !important;">
                            <div class="card-body p-5">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="background: rgba(51, 104, 160, 0.08); width: 80px; height: 80px;">
                                    <i class="ti ti-help-circle text-primary" style="font-size: 2.75rem;"></i>
                                </div>
                                <h4 class="fw-extrabold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Belum Ada Kuis Tersedia</h4>
                                <p class="text-muted mx-auto mb-4" style="max-width: 480px; line-height: 1.6;">
                                    Saat ini belum ada kuis atau ujian online yang diterbitkan untuk kelas Anda. Silakan periksa kembali berkala atau tanyakan guru pengampu.
                                </p>
                                <a href="{{ route('dashboard') }}" class="btn text-white rounded-pill px-4 py-2.5 font-bold shadow-sm" style="background: #20456E;">
                                    <i class="ti ti-home me-1"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. Quizzes Container: LIST VIEW (Modern Table, Tersembunyi Default) -->
        <div id="quizListView" style="display: none;">
            <div class="table-responsive table-custom-quizzes">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 30%;">Judul Kuis</th>
                            <th style="width: 16%;">Mata Pelajaran</th>
                            <th style="width: 16%;">Guru Pengampu</th>
                            <th style="width: 12%;">Durasi & Soal</th>
                            <th style="width: 14%;">Batas Waktu</th>
                            <th style="width: 12%;">Status & Skor</th>
                            <th class="pe-4 text-end" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="quizTableBody">
                        @forelse($quizzes as $quiz)
                            @php
                                $userAttempt = $quiz->attempts->first();
                                $isCompleted = $userAttempt && !is_null($userAttempt->submitted_at);
                                $isInProgress = $userAttempt && is_null($userAttempt->submitted_at);
                                $isOverdue = $quiz->deadline && $quiz->deadline->isPast() && !$isCompleted;
                            @endphp
                            <tr class="quiz-item-row"
                                data-title="{{ strtolower($quiz->title) }}" 
                                data-subject="{{ strtolower($quiz->subject->name ?? '') }}" 
                                data-instructor="{{ strtolower($quiz->instructor->name ?? '') }}"
                                data-status="{{ $isCompleted ? 'completed' : ($isInProgress ? 'in_progress' : 'unattempted') }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm" style="background: linear-gradient(135deg, #059669, #10B981); width: 42px; height: 42px;">
                                            <i class="ti ti-award fs-4"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('student.quizzes.show', $quiz) }}" class="fw-bold text-dark text-decoration-none hover:text-primary" style="font-size: 0.95rem;">
                                                {{ $quiz->title }}
                                            </a>
                                            <div class="text-muted small" style="font-size: 0.72rem;">
                                                Kelas {{ Auth::user()->schoolClass->name ?? 'Saya' }} • {{ $quiz->total_questions_count }} Butir Soal
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge px-2.5 py-1.5 rounded-pill font-bold" style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.78rem;">
                                        <i class="ti ti-tag me-1"></i> {{ $quiz->subject->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="background: #64748b; width: 28px; height: 28px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($quiz->instructor->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <span class="text-dark small fw-semibold">{{ $quiz->instructor->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <i class="ti ti-clock text-primary me-1"></i> {{ $quiz->formatted_duration }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $quiz->total_questions_count }} Butir Soal</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                        <i class="ti ti-calendar {{ $isOverdue ? 'text-danger' : 'text-muted' }} me-1"></i>
                                        {{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') . ' WIB' : 'Tanpa Batas' }}
                                    </div>
                                </td>
                                <td>
                                    @if($isCompleted)
                                        <span class="badge rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.8rem;">
                                            <i class="ti ti-trophy"></i> Skor: {{ $userAttempt->score }}
                                        </span>
                                    @elseif($isInProgress)
                                        <span class="badge rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.8rem;">
                                            <i class="ti ti-hourglass"></i> Mengerjakan
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="badge rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(220, 38, 38, 0.12); color: #dc2626; font-size: 0.8rem;">
                                            <i class="ti ti-lock"></i> Waktu Habis
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1 font-bold d-inline-flex align-items-center gap-1 text-muted bg-light border" style="font-size: 0.8rem;">
                                            <i class="ti ti-clock"></i> Belum Ikut
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($isCompleted)
                                        <a href="{{ route('student.quizzes.result', $quiz) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 font-semibold">
                                            Hasil
                                        </a>
                                    @elseif($isInProgress)
                                        <a href="{{ route('student.quizzes.attempt', $quiz) }}" class="btn btn-sm btn-warning text-white rounded-pill px-3 font-semibold">
                                            Lanjut
                                        </a>
                                    @elseif($isOverdue)
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled style="opacity: 0.6;">
                                            Tutup
                                        </button>
                                    @else
                                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm rounded-pill px-3 font-semibold text-white" style="background: #20456E;">
                                            Mulai
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Tidak ada kuis yang sesuai dengan filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $quizzes->links() }}
        </div>

    </div>

    <!-- Client-side Interactive Search & View Switcher JavaScript -->
    <script>
        function switchQuizView(mode) {
            const gridView = document.getElementById('quizGridView');
            const listView = document.getElementById('quizListView');
            const btnGrid = document.getElementById('btnQuizGrid');
            const btnList = document.getElementById('btnQuizList');

            if (mode === 'list') {
                gridView.style.display = 'none';
                listView.style.display = 'block';
                btnList.classList.add('active');
                btnGrid.classList.remove('active');
                localStorage.setItem('student_quiz_view_preference', 'list');
            } else {
                gridView.style.display = 'block';
                listView.style.display = 'none';
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
                localStorage.setItem('student_quiz_view_preference', 'grid');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedView = localStorage.getItem('student_quiz_view_preference');
            if (savedView === 'list') {
                switchQuizView('list');
            }

            // Real-time client-side filter
            const searchInput = document.getElementById('quizSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.quiz-item-card');
                    const rows = document.querySelectorAll('.quiz-item-row');

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
