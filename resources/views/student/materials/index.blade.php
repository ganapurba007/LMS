<x-app-layout>
    <!-- Include Bootstrap & Custom Styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Custom Styling for Courses & Materials Page */
        .materials-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 50%, #2b5788 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
        }
        .materials-hero::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 450px;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .material-card {
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
        .material-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(51, 104, 160, 0.16);
            border-color: rgba(102, 163, 191, 0.4);
        }
        .material-card-header {
            min-height: 125px;
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }
        .material-watermark {
            position: absolute;
            right: -10px;
            bottom: -15px;
            font-size: 5rem;
            opacity: 0.18;
            color: #ffffff;
            pointer-events: none;
            transition: transform 0.4s ease;
        }
        .material-card:hover .material-watermark {
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
        .table-custom-materials {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-materials thead th {
            background: #F2EFE7;
            color: #20456E;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid rgba(51, 104, 160, 0.12);
        }
        .table-custom-materials tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(51, 104, 160, 0.07);
        }
        .table-custom-materials tbody tr:hover {
            background-color: rgba(200, 223, 219, 0.25);
        }
    </style>

    <!-- 1. Dedicated Page Hero Header Banner -->
    <section class="materials-hero text-white py-5">
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
                            <span class="text-white font-bold">Materi</span>
                        </span>
                    </div>

                    <!-- Heading & Subtitle -->
                    <h1 class="display-6 fw-extrabold mb-3" style="font-family: 'Jost', sans-serif; letter-spacing: -0.5px; line-height: 1.2;">
                        Katalog Modul & Materi Pelajaran
                    </h1>
                    <p class="text-white-50 fs-6 mb-4 pe-lg-4" style="line-height: 1.6;">
                        Eksplorasi kumpulan modul pembelajaran interaktif, simak video penjelasan pengampu, unduh materi PDF rangkuman, dan tuntaskan target belajarmu dengan teratur.
                    </p>

                    <!-- Hero Quick Search Bar -->
                    <form action="{{ route('student.materials.index') }}" method="GET" class="p-2 rounded-pill shadow-lg d-flex align-items-center max-w-lg mx-auto mx-lg-0 border" style="background-color: #F2EFE7; border-color: rgba(102, 163, 191, 0.3) !important;">
                        <i class="ti ti-search text-muted fs-4 ms-3 me-2"></i>
                        <input type="text" 
                               name="search" 
                               id="heroSearchInput"
                               value="{{ request('search') }}" 
                               class="form-control shadow-none bg-transparent text-dark border-0" 
                               placeholder="Cari materi, topik, atau mata pelajaran...">
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
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 34px; height: 34px;">
                                    <i class="ti ti-chart-pie fs-5"></i>
                                </div>
                                <span class="fw-bold text-dark fs-6" style="font-family: 'Jost', sans-serif;">Ringkasan Progres Materi</span>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 font-bold text-white shadow-sm" style="background: {{ $progressPercent >= 100 ? '#10B981' : '#3368A0' }}; font-size: 0.75rem;">
                                {{ $progressPercent }}% Selesai
                            </span>
                        </div>

                        <!-- Progress Bar Indicator -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-muted small fw-semibold mb-1">
                                <span>Pencapaian Materi</span>
                                <span class="text-dark font-bold">{{ $completedCount }} dari {{ $totalMaterials }} Modul</span>
                            </div>
                            <div class="progress rounded-pill shadow-inner" style="height: 10px; background: rgba(51, 104, 160, 0.12);">
                                <div class="progress-bar rounded-pill progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ $progressPercent }}%; background: linear-gradient(90deg, #10B981, #059669);" 
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
                                    <div class="text-primary fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $totalMaterials }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Total Modul</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2.5 rounded-3 border bg-white">
                                    <div class="text-success fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $completedCount }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Selesai</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2.5 rounded-3 border bg-white">
                                    <div class="text-warning fw-extrabold fs-4 mb-0" style="line-height: 1;">{{ $uncompletedCount }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">Belum Selesai</div>
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

        <!-- 2. Interactive Filter Bar & View Mode Controller -->
        <div class="card border-0 rounded-4 shadow-sm mb-4" style="background-color: #ffffff; border: 1px solid rgba(51, 104, 160, 0.14) !important; padding: 1.25rem 1.5rem;">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                
                <!-- Left: Subject Filter Pills -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small fw-bold text-uppercase tracking-wider me-1 d-none d-md-inline" style="font-size: 0.75rem;">
                        <i class="ti ti-filter me-1"></i> Mapel:
                    </span>
                    
                    <a href="{{ route('student.materials.index', array_merge(request()->except(['subject_id', 'page']))) }}" 
                       class="filter-pill-btn {{ !request('subject_id') ? 'active' : '' }}">
                        Semua Mapel
                        <span class="badge rounded-pill {{ !request('subject_id') ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} ms-1">
                            {{ $totalMaterials }}
                        </span>
                    </a>

                    @foreach($subjects as $subj)
                        <a href="{{ route('student.materials.index', array_merge(request()->except(['page']), ['subject_id' => $subj->id])) }}" 
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
                                @if(request('status') === 'completed')
                                    Status: Selesai
                                @elseif(request('status') === 'uncompleted')
                                    Status: Belum Ditinjau
                                @else
                                    Semua Status
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-1 text-sm">
                            <li>
                                <a class="dropdown-item rounded-2 {{ !request('status') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.materials.index', array_merge(request()->except(['status', 'page']))) }}">
                                    <i class="ti ti-list-details me-2"></i> Semua Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 {{ request('status') === 'completed' ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.materials.index', array_merge(request()->except(['page']), ['status' => 'completed'])) }}">
                                    <i class="ti ti-circle-check text-success me-2"></i> Sudah Selesai
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 {{ request('status') === 'uncompleted' ? 'active fw-bold' : '' }}" 
                                   href="{{ route('student.materials.index', array_merge(request()->except(['page']), ['status' => 'uncompleted'])) }}">
                                    <i class="ti ti-clock text-warning me-2"></i> Belum Ditinjau
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Reset Filter Button (If Filter Active) -->
                    @if(request('search') || request('subject_id') || request('status'))
                        <a href="{{ route('student.materials.index') }}" 
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
                                id="btnGridView" 
                                onclick="switchMaterialsView('grid')" 
                                title="Tampilan Kartu (Grid)">
                            <i class="ti ti-layout-grid fs-5"></i>
                        </button>
                        <button type="button" 
                                class="view-btn" 
                                id="btnListView" 
                                onclick="switchMaterialsView('list')" 
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
                            <a href="{{ route('student.materials.index', array_merge(request()->except(['search', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                    @if(request('subject_id'))
                        @php
                            $activeSubject = $subjects->firstWhere('id', request('subject_id'));
                        @endphp
                        @if($activeSubject)
                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                Mapel: {{ $activeSubject->name }}
                                <a href="{{ route('student.materials.index', array_merge(request()->except(['subject_id', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                    @endif
                    @if(request('status'))
                        <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                            Status: {{ request('status') === 'completed' ? 'Selesai' : 'Belum Ditinjau' }}
                            <a href="{{ route('student.materials.index', array_merge(request()->except(['status', 'page']))) }}" class="text-danger ms-1 text-decoration-none">&times;</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 3. Materials Container: GRID VIEW (Default) -->
        <div id="materialsGridView">
            <div class="row g-4" id="materialsGridRow">
                @forelse($materials as $index => $mat)
                    @php
                        $isDone = in_array($mat->id, $completedIds);
                        
                        // Subject specific color gradients for rich visual appeal
                        $gradients = [
                            'linear-gradient(135deg, #1e3a8a 0%, #3368A0 100%)',
                            'linear-gradient(135deg, #0f766e 0%, #0d9488 100%)',
                            'linear-gradient(135deg, #7c2d12 0%, #c2410c 100%)',
                            'linear-gradient(135deg, #4338ca 0%, #6366f1 100%)',
                            'linear-gradient(135deg, #831843 0%, #be185d 100%)',
                            'linear-gradient(135deg, #1e293b 0%, #334155 100%)',
                        ];
                        $cardBgGradient = $gradients[$index % count($gradients)];
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4 material-item-card" 
                         data-title="{{ strtolower($mat->title) }}" 
                         data-subject="{{ strtolower($mat->subject->name ?? '') }}" 
                         data-instructor="{{ strtolower($mat->instructor->name ?? '') }}"
                         data-status="{{ $isDone ? 'completed' : 'uncompleted' }}">
                        <div class="material-card h-100 position-relative">
                            
                            <!-- Card Top Section with Gradient & Badges -->
                            <div class="material-card-header" style="background: {{ $cardBgGradient }};">
                                <i class="ti ti-bookmark material-watermark"></i>
                                
                                <div class="d-flex align-items-center justify-content-between position-relative z-1 mb-2">
                                    <span class="badge text-white font-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.22); backdrop-filter: blur(6px); font-size: 0.76rem;">
                                        <i class="ti ti-tag me-1"></i> {{ $mat->subject->name ?? 'Mata Pelajaran' }}
                                    </span>

                                    @if($isDone)
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: #10B981; color: #ffffff; font-size: 0.72rem;">
                                            <i class="ti ti-circle-check"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2.5 py-1 font-bold shadow-sm d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.88); color: #475569; font-size: 0.72rem;">
                                            <i class="ti ti-clock"></i> Belum Ditinjau
                                        </span>
                                    @endif
                                </div>

                                <h5 class="fw-extrabold text-white mb-0 position-relative z-1" style="font-family: 'Jost', sans-serif; font-size: 1.25rem; line-height: 1.35;">
                                    {{ $mat->title }}
                                </h5>
                            </div>

                            <!-- Card Middle Content -->
                            <div class="p-4 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    <!-- Teacher Info -->
                                    <div class="d-flex align-items-center gap-2.5 mb-3">
                                        <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 38px; height: 38px; font-size: 0.9rem;">
                                            {{ strtoupper(substr($mat->instructor->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-muted small" style="font-size: 0.72rem;">Guru Pengampu</div>
                                            <div class="fw-bold text-dark text-truncate" style="margin-top: -2px; font-size: 0.9rem;">
                                                {{ $mat->instructor->name ?? 'Pengajar SMA' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Media Content Badges -->
                                    <div class="d-flex flex-wrap gap-1.5 mb-3">
                                        @if($mat->content)
                                            <span class="badge rounded-pill font-bold px-2.5 py-1" style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.72rem;">
                                                <i class="ti ti-file-text me-1"></i> Modul Bacaan
                                            </span>
                                        @endif
                                        @if($mat->video_url)
                                            <span class="badge rounded-pill font-bold px-2.5 py-1" style="background: rgba(220, 38, 38, 0.1); color: #DC2626; font-size: 0.72rem;">
                                                <i class="ti ti-brand-youtube me-1"></i> Video YouTube
                                            </span>
                                        @endif
                                        @if($mat->document_path)
                                            <span class="badge rounded-pill font-bold px-2.5 py-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem;">
                                                <i class="ti ti-file-download me-1"></i> Berkas Dokumen
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer Row -->
                                <div class="pt-3 border-top d-flex align-items-center justify-content-between text-muted small" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                    <span><i class="ti ti-calendar me-1 text-primary"></i> {{ $mat->created_at ? $mat->created_at->format('d M Y') : 'Terbaru' }}</span>
                                    <span><i class="ti ti-school me-1 text-info"></i> {{ $mat->schoolClass->name ?? 'Kelas Saya' }}</span>
                                </div>
                            </div>

                            <!-- Card Bottom Action Button -->
                            <div class="p-4 pt-0 bg-white">
                                <a href="{{ route('student.materials.show', $mat) }}" 
                                   class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" 
                                   style="background: {{ $isDone ? 'linear-gradient(135deg, #059669 0%, #10B981 100%)' : 'linear-gradient(135deg, #3368A0 0%, #66A3BF 100%)' }};">
                                    @if($isDone)
                                        <i class="ti ti-circle-check fs-5"></i> Buka Ulang Modul
                                    @else
                                        <i class="ti ti-book-2 fs-5"></i> Pelajari Modul Ini
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
                                <i class="ti ti-books-off fs-1 text-muted"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Belum Ada Materi Pembelajaran</h4>
                            <p class="text-muted small max-w-md mx-auto mb-4">
                                @if(request('search') || request('subject_id') || request('status'))
                                    Tidak ditemukan modul yang sesuai dengan kriteria filter yang Anda pilih. Silakan sesuaikan kata kunci atau reset filter.
                                @else
                                    Materi untuk kelas Anda belum diterbitkan oleh guru pengampu. Silakan periksa kembali beberapa saat lagi.
                                @endif
                            </p>
                            @if(request('search') || request('subject_id') || request('status'))
                                <div>
                                    <a href="{{ route('student.materials.index') }}" class="btn btn-primary rounded-pill px-4 py-2 font-bold shadow-sm">
                                        <i class="ti ti-refresh me-1"></i> Reset Semua Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. Materials Container: LIST VIEW (Clean Modern Table) -->
        <div id="materialsListView" style="display: none;">
            <div class="table-custom-materials mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 32%;">Judul & Modul Materi</th>
                                <th style="width: 18%;">Mata Pelajaran</th>
                                <th style="width: 18%;">Guru Pengampu</th>
                                <th style="width: 16%;">Media Konten</th>
                                <th style="width: 16%;">Status</th>
                                <th class="pe-4 text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="materialsTableBody">
                            @forelse($materials as $mat)
                                @php
                                    $isDone = in_array($mat->id, $completedIds);
                                @endphp
                                <tr class="material-item-row"
                                    data-title="{{ strtolower($mat->title) }}" 
                                    data-subject="{{ strtolower($mat->subject->name ?? '') }}" 
                                    data-instructor="{{ strtolower($mat->instructor->name ?? '') }}"
                                    data-status="{{ $isDone ? 'completed' : 'uncompleted' }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 42px; height: 42px;">
                                                <i class="ti ti-book-2 fs-4"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('student.materials.show', $mat) }}" class="fw-bold text-dark text-decoration-none hover:text-primary" style="font-size: 0.95rem;">
                                                    {{ $mat->title }}
                                                </a>
                                                <div class="text-muted small" style="font-size: 0.72rem;">
                                                    Dibuat: {{ $mat->created_at ? $mat->created_at->format('d M Y') : '-' }} • Kelas {{ $mat->schoolClass->name ?? 'Saya' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge px-2.5 py-1.5 rounded-pill font-bold" style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.78rem;">
                                            <i class="ti ti-tag me-1"></i> {{ $mat->subject->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="background: #64748b; width: 28px; height: 28px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($mat->instructor->name ?? 'G', 0, 1)) }}
                                            </div>
                                            <span class="text-dark small fw-semibold">{{ $mat->instructor->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($mat->content)
                                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" title="Modul Bacaan Teks">
                                                    <i class="ti ti-file-text"></i> Teks
                                                </span>
                                            @endif
                                            @if($mat->video_url)
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" title="Video Pembelajaran YouTube">
                                                    <i class="ti ti-brand-youtube"></i> Video
                                                </span>
                                            @endif
                                            @if($mat->document_path)
                                                <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill" title="Berkas Dokumen PDF">
                                                    <i class="ti ti-file-download"></i> PDF
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($isDone)
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.78rem;">
                                                <i class="ti ti-circle-check"></i> Selesai
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-2.5 py-1 font-bold d-inline-flex align-items-center gap-1" style="background: rgba(100, 116, 139, 0.12); color: #64748b; font-size: 0.78rem;">
                                                <i class="ti ti-clock"></i> Belum Ditinjau
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('student.materials.show', $mat) }}" 
                                           class="btn btn-sm rounded-pill px-3 py-1.5 font-bold shadow-sm d-inline-flex align-items-center gap-1 text-white hover-lift" 
                                           style="background: {{ $isDone ? '#059669' : '#3368A0' }};">
                                            <i class="ti ti-eye"></i> Pelajari
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="ti ti-books-off fs-2 d-block mb-2 text-secondary"></i>
                                        Belum ada materi pembelajaran yang dapat ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Pagination Navigation -->
        @if($materials->hasPages())
            <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 pt-3">
                <div class="text-muted small">
                    Menampilkan <span class="fw-bold text-dark">{{ $materials->firstItem() }}</span> - <span class="fw-bold text-dark">{{ $materials->lastItem() }}</span> dari <span class="fw-bold text-dark">{{ $materials->total() }}</span> total modul materi
                </div>
                <div>
                    {{ $materials->links() }}
                </div>
            </div>
        @endif

    </div>

    <!-- Bootstrap Bundle JS & Client-side View Toggle Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Switch between Grid View and List View
        function switchMaterialsView(mode) {
            const gridEl = document.getElementById('materialsGridView');
            const listEl = document.getElementById('materialsListView');
            const btnGrid = document.getElementById('btnGridView');
            const btnList = document.getElementById('btnListView');

            if (mode === 'list') {
                gridEl.style.display = 'none';
                listEl.style.display = 'block';
                btnGrid.classList.remove('active');
                btnList.classList.add('active');
                localStorage.setItem('materials_view_mode', 'list');
            } else {
                gridEl.style.display = 'block';
                listEl.style.display = 'none';
                btnList.classList.remove('active');
                btnGrid.classList.add('active');
                localStorage.setItem('materials_view_mode', 'grid');
            }
        }

        // Restore saved view mode preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedMode = localStorage.getItem('materials_view_mode');
            if (savedMode === 'list') {
                switchMaterialsView('list');
            }

            // Realtime fast client-side filter on typing in hero search
            const heroSearch = document.getElementById('heroSearchInput');
            if (heroSearch) {
                heroSearch.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.material-item-card');
                    const rows = document.querySelectorAll('.material-item-row');

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
