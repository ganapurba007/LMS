<x-app-layout>
    <!-- Include Bootstrap & Webfonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <!-- Arsha Hero Section with Real High-Res Learning Photo (Attaches directly under top navbar) -->
    <section id="hero" class="arsha-hero py-5" data-aos="zoom-out" data-aos-delay="100"
        style="background: linear-gradient(135deg, #3368A0 0%, #20456E 100%); margin-top: 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 d-flex flex-column justify-content-center text-center text-lg-start">
                    <div class="mb-3" data-aos="fade-up" data-aos-delay="150">
                        <span
                            class="badge text-primary font-bold px-3.5 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2"
                            style="background-color: #F2EFE7; color: #3368A0 !important; font-size: 0.85rem;">
                            <i class="ti ti-rocket text-warning"></i> Dashboard Siswa — Kelas
                            {{ $user->schoolClass->name ?? 'Siswa SMA' }}
                        </span>
                    </div>

                    <h1 class="text-white fw-extrabold display-5 mb-3"
                        style="font-family: 'Jost', sans-serif; line-height: 1.2;" data-aos="fade-up"
                        data-aos-delay="200">
                        Belajar Jadi Lebih Seru, Satu Ruang untuk Semua Progresmu
                    </h1>

                    <p class="lead text-white-50 fs-5 mb-4" data-aos="fade-up" data-aos-delay="300">
                        Halo, <strong>{{ $user->name }}</strong>! Yuk lanjutkan perjalanan belajarmu — semua materi,
                        tugas, dan kuis interaktif sudah menunggu di sini.
                    </p>

                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start"
                        data-aos="fade-up" data-aos-delay="400">
                        <a class="btn text-white font-bold px-4 py-3 rounded-pill text-decoration-none shadow-lg d-inline-flex align-items-center gap-2 hover-lift"
                            href="{{ route('student.materials.index') }}"
                            style="background: linear-gradient(135deg, #66A3BF 0%, #3368A0 100%);">
                            <i class="ti ti-player-play-filled fs-5 text-warning"></i> Mulai Belajar Sekarang
                        </a>
                        <a class="btn text-white font-bold px-4 py-3 rounded-pill text-decoration-none d-inline-flex align-items-center gap-2 hover-lift"
                            href="{{ route('student.quizzes.index') }}"
                            style="border: 2px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);">
                            <i class="ti ti-help-hexagon fs-5"></i> Ikuti Kuis
                        </a>
                    </div>
                </div>

                <!-- Right Column: Real Learning Image with Floating Badges -->
                <div class="col-lg-6 text-center text-lg-end" data-aos="zoom-in" data-aos-delay="200">
                    <div class="position-relative d-inline-block hero-breathe-animation">
                        <!-- High Quality SMA Learning Image -->
                        <img src="{{ asset('images/hero-sma.jpg') }}" alt="Siswa SMA Belajar Interaktif"
                            class="img-fluid rounded-4 shadow-2xl border-4 hero-breathe-shadow"
                            style="border-color: #F2EFE7; max-height: 380px; object-fit: cover; width: 100%;">

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ambient Geometric Vectors Layer (Floating Translucent Geometric Blueprint Shapes) -->
    <div class="position-relative overflow-hidden">
        <!-- Floating Geometric Vector 1: Top-Left Compass / Target Ring -->
        <svg class="position-absolute d-none d-md-block pointer-events-none"
            style="top: 2%; left: -60px; width: 300px; height: 300px; opacity: 0.16; z-index: 0;" viewBox="0 0 200 200"
            fill="none" stroke="#3368A0">
            <circle cx="100" cy="100" r="85" stroke-width="1.2" stroke-dasharray="6 6" />
            <circle cx="100" cy="100" r="55" stroke-width="1" stroke-dasharray="3 3" />
            <circle cx="100" cy="100" r="24" stroke-width="1" />
            <line x1="100" y1="5" x2="100" y2="195" stroke-width="0.8"
                stroke-dasharray="4 4" />
            <line x1="5" y1="100" x2="195" y2="100" stroke-width="0.8"
                stroke-dasharray="4 4" />
            <rect x="94" y="94" width="12" height="12" stroke-width="1" />
        </svg>

        <!-- Floating Geometric Vector 2: Right Side Isometric Cube -->
        <svg class="position-absolute d-none d-lg-block pointer-events-none"
            style="top: 24%; right: -50px; width: 240px; height: 240px; opacity: 0.18; z-index: 0;"
            viewBox="0 0 100 100" fill="none" stroke="#66A3BF">
            <polygon points="50,10 90,32 50,55 10,32" stroke-width="1.2" fill="rgba(102, 163, 191, 0.08)" />
            <polygon points="10,32 50,55 50,95 10,72" stroke-width="1.2" fill="rgba(51, 104, 160, 0.05)" />
            <polygon points="90,32 50,55 50,95 90,72" stroke-width="1.2" fill="rgba(32, 69, 110, 0.07)" />
            <circle cx="50" cy="55" r="3" fill="#66A3BF" />
        </svg>

        <!-- Floating Geometric Vector 3: Left Side Hexagonal Lattice Wireframe -->
        <svg class="position-absolute d-none d-lg-block pointer-events-none"
            style="top: 52%; left: -40px; width: 260px; height: 260px; opacity: 0.16; z-index: 0;" viewBox="0 0 100 100"
            fill="none" stroke="#3368A0">
            <polygon points="50,5 90,25 90,75 50,95 10,75 10,25" stroke-width="1" stroke-dasharray="5 5" />
            <polygon points="50,20 80,35 80,65 50,80 20,65 20,35" stroke-width="1.2"
                fill="rgba(200, 223, 219, 0.12)" />
            <circle cx="50" cy="50" r="12" stroke-width="1" />
        </svg>

        <!-- Floating Geometric Vector 4: Bottom-Right Radial Matrix -->
        <svg class="position-absolute d-none d-md-block pointer-events-none"
            style="top: 80%; right: -30px; width: 280px; height: 280px; opacity: 0.16; z-index: 0;"
            viewBox="0 0 100 100" fill="none" stroke="#66A3BF">
            <circle cx="50" cy="50" r="46" stroke-width="1" stroke-dasharray="4 6" />
            <circle cx="50" cy="50" r="32" stroke-width="1.2" />
            <line x1="50" y1="4" x2="50" y2="96" stroke-width="0.8"
                stroke-dasharray="3 3" />
            <line x1="4" y1="50" x2="96" y2="50" stroke-width="0.8"
                stroke-dasharray="3 3" />
            <rect x="26" y="26" width="48" height="48" stroke-width="0.8" stroke-dasharray="3 3" />
        </svg>

        <!-- Modern & Dynamic Interactive Statistics Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-5 position-relative z-1" data-aos="fade-up">
            <div class="arsha-section-title text-center mb-0" data-aos="fade-up">
                <span
                    class="badge rounded-pill px-3 py-1.5 font-bold uppercase tracking-wider text-xs mb-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
                    style="background: rgba(51, 104, 160, 0.1); color: #3368A0 !important; border: 1px solid rgba(51, 104, 160, 0.2);">
                    <i class="ti ti-star text-warning"></i> Progress
                </span>
                <h2 style="color: #3368A0; letter-spacing: -0.5px;">Ringkasan Progres</h2>
                <p class="text-secondary fw-medium">Pantau capaian materi pembelajaran, tugas, dan kuis Anda</p>
            </div>
            <!-- Responsive 4 Metric Cards Grid -->
            <div class="row g-3 g-xl-4">
                <!-- Stat Card 1: Total Materi Pelajaran -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('student.materials.index') }}" class="stat-card-modern h-100 p-3.5 p-sm-4"
                        style="--stat-color: #3368A0; --stat-glow: rgba(51, 104, 160, 0.35); --stat-glow-bg: rgba(51, 104, 160, 0.1); --stat-bar-gradient: linear-gradient(90deg, #3368A0, #66A3BF);">
                        <!-- Top Accent Color Bar -->
                        <div class="stat-top-bar"></div>
                        <!-- Ambient Corner Glow -->
                        <div class="stat-glow-radial"></div>
                        <!-- Watermark Icon -->
                        <i class="ti ti-books stat-watermark"></i>

                        <div class="position-relative z-1 d-flex flex-column justify-content-between h-100">
                            <!-- Header: Icon & Category Badge -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <div class="stat-icon-wrapper"
                                    style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                                    <i class="ti ti-books fs-2"></i>
                                </div>
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold small"
                                    style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.72rem;">
                                    Modul
                                </span>
                            </div>

                            <!-- Value & Title -->
                            <div class="mb-3">
                                <div class="display-6 fw-extrabold text-dark mb-1"
                                    style="font-family: 'Jost', sans-serif; line-height: 1;">
                                    {{ $totalClassMaterials }}
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Total Materi</div>
                            </div>

                            <!-- Footer Action -->
                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between"
                                style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                <span class="stat-footer-link">Buka Materi <i class="ti ti-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Stat Card 2: Materi Telah Dipelajari -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('student.materials.index') }}" class="stat-card-modern h-100 p-3.5 p-sm-4"
                        style="--stat-color: #059669; --stat-glow: rgba(16, 185, 129, 0.35); --stat-glow-bg: rgba(16, 185, 129, 0.1); --stat-bar-gradient: linear-gradient(90deg, #059669, #10B981);">
                        <!-- Top Accent Color Bar -->
                        <div class="stat-top-bar"></div>
                        <!-- Ambient Corner Glow -->
                        <div class="stat-glow-radial"></div>
                        <!-- Watermark Icon -->
                        <i class="ti ti-circle-check stat-watermark"></i>

                        <div class="position-relative z-1 d-flex flex-column justify-content-between h-100">
                            <!-- Header: Icon & Percentage Badge -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <div class="stat-icon-wrapper"
                                    style="background: linear-gradient(135deg, #059669 0%, #10B981 100%);">
                                    <i class="ti ti-circle-check fs-2"></i>
                                </div>
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold small"
                                    style="background: rgba(16, 185, 129, 0.12); color: #059669 !important; font-size: 0.72rem;">
                                    {{ round($overallProgress) }}% Selesai
                                </span>
                            </div>

                            <!-- Value & Title -->
                            <div class="mb-2">
                                <div class="d-flex align-items-baseline gap-1.5 mb-1">
                                    <span class="display-6 fw-extrabold text-dark"
                                        style="font-family: 'Jost', sans-serif; line-height: 1;">{{ $completedMaterialsCount }}</span>
                                    <span class="text-muted small fw-semibold">/ {{ $totalClassMaterials }}</span>
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Materi Selesai</div>
                            </div>

                            <!-- Progress Bar & Footer Action -->
                            <div class="mt-auto">
                                <div class="progress rounded-pill mb-3"
                                    style="height: 6px; background: rgba(16, 185, 129, 0.14);">
                                    <div class="progress-bar rounded-pill"
                                        style="width: {{ min(100, $overallProgress) }}%; background: linear-gradient(90deg, #059669, #10B981);">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top"
                                    style="border-color: rgba(16, 185, 129, 0.1) !important;">
                                    <span class="stat-footer-link" style="color: #059669;">Lihat Progres <i
                                            class="ti ti-arrow-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Stat Card 3: Tugas Perlu Dikumpulkan -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('student.assignments.index') }}" class="stat-card-modern h-100 p-3.5 p-sm-4"
                        style="--stat-color: #D97706; --stat-glow: rgba(245, 158, 11, 0.35); --stat-glow-bg: rgba(245, 158, 11, 0.1); --stat-bar-gradient: linear-gradient(90deg, #D97706, #F59E0B);">
                        <!-- Top Accent Color Bar -->
                        <div class="stat-top-bar"></div>
                        <!-- Ambient Corner Glow -->
                        <div class="stat-glow-radial"></div>
                        <!-- Watermark Icon -->
                        <i class="ti ti-notebook stat-watermark"></i>

                        <div class="position-relative z-1 d-flex flex-column justify-content-between h-100">
                            <!-- Header: Icon & Status Badge -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <div class="stat-icon-wrapper"
                                    style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);">
                                    <i class="ti ti-notebook fs-2"></i>
                                </div>
                                @if ($upcomingAssignments->count() > 0)
                                    <span
                                        class="badge rounded-pill px-2.5 py-1 text-warning-emphasis fw-bold small d-inline-flex align-items-center gap-1.5"
                                        style="background: rgba(245, 158, 11, 0.15); font-size: 0.72rem;">
                                        <span class="pulse-dot-amber"></span> Perlu Dikirim
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill px-2.5 py-1 text-success fw-bold small d-inline-flex align-items-center gap-1"
                                        style="background: rgba(16, 185, 129, 0.14); font-size: 0.72rem;">
                                        <i class="ti ti-check"></i> Bebas Tugas
                                    </span>
                                @endif
                            </div>

                            <!-- Value & Title -->
                            <div class="mb-3">
                                <div class="display-6 fw-extrabold text-dark mb-1"
                                    style="font-family: 'Jost', sans-serif; line-height: 1;">
                                    {{ $upcomingAssignments->count() }}
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Tugas Aktif</div>
                            </div>

                            <!-- Footer Action -->
                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between"
                                style="border-color: rgba(245, 158, 11, 0.1) !important;">
                                <span class="stat-footer-link" style="color: #D97706;">Cek Tugas <i
                                        class="ti ti-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Stat Card 4: Kuis Online Aktif -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('student.quizzes.index') }}" class="stat-card-modern h-100 p-3.5 p-sm-4"
                        style="--stat-color: #6366F1; --stat-glow: rgba(99, 102, 241, 0.35); --stat-glow-bg: rgba(99, 102, 241, 0.1); --stat-bar-gradient: linear-gradient(90deg, #6366F1, #8B5CF6);">
                        <!-- Top Accent Color Bar -->
                        <div class="stat-top-bar"></div>
                        <!-- Ambient Corner Glow -->
                        <div class="stat-glow-radial"></div>
                        <!-- Watermark Icon -->
                        <i class="ti ti-help-hexagon stat-watermark"></i>

                        <div class="position-relative z-1 d-flex flex-column justify-content-between h-100">
                            <!-- Header: Icon & Live Badge -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <div class="stat-icon-wrapper"
                                    style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);">
                                    <i class="ti ti-help-hexagon fs-2"></i>
                                </div>
                                @if ($activeQuizzes->count() > 0)
                                    <span
                                        class="badge rounded-pill px-2.5 py-1 fw-bold small d-inline-flex align-items-center gap-1.5"
                                        style="background: rgba(99, 102, 241, 0.14); color: #6366F1; font-size: 0.72rem;">
                                        <span class="pulse-dot-live"></span> Live Test
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill px-2.5 py-1 fw-bold small d-inline-flex align-items-center gap-1"
                                        style="background: rgba(16, 185, 129, 0.14); color: #059669; font-size: 0.72rem;">
                                        <i class="ti ti-check"></i> Bebas Kuis
                                    </span>
                                @endif
                            </div>

                            <!-- Value & Title -->
                            <div class="mb-3">
                                <div class="display-6 fw-extrabold text-dark mb-1"
                                    style="font-family: 'Jost', sans-serif; line-height: 1;">
                                    {{ $activeQuizzes->count() }}
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Kuis Aktif</div>
                            </div>

                            <!-- Footer Action -->
                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between"
                                style="border-color: rgba(99, 102, 241, 0.1) !important;">
                                <span class="stat-footer-link"
                                    style="color: #6366F1;">{{ $activeQuizzes->count() > 0 ? 'Mulai Kuis' : 'Lihat Kuis' }}
                                    <i class="ti ti-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-5">
            <!-- Arsha Features & Services Section (4 Modern Cards) -->
            <div class="mb-5">
                <div class="arsha-section-title text-center mb-0 mt-3" data-aos="fade-up">
                    <span
                        class="badge rounded-pill px-3 py-1.5 font-bold uppercase tracking-wider text-xs mb-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
                        style="background: rgba(51, 104, 160, 0.1); color: #3368A0 !important; border: 1px solid rgba(51, 104, 160, 0.2);">
                        <i class="ti ti-star text-warning"></i> Fasilitas Belajar
                    </span>
                    <h2 style="color: #3368A0; letter-spacing: -0.5px;">FITUR & FASILITAS PEMBELAJARAN</h2>
                    <p class="text-secondary fw-medium">Nikmati pengalaman belajar terbaik dengan berbagai
                        fasilitas interaktif pilihan kami</p>
                </div>

                <div class="row g-4">
                    <!-- Feature 1: Modul Interaktif -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card-modern h-100 p-4"
                            style="--feat-color: #3368A0; --feat-glow: rgba(51, 104, 160, 0.35); --feat-gradient: linear-gradient(90deg, #3368A0, #66A3BF);">
                            <div class="feature-top-bar"></div>
                            <i class="ti ti-book-2 feature-watermark"></i>
                            <div class="feature-icon-wrapper"
                                style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                                <i class="ti ti-book-2 fs-2"></i>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 mb-2 fw-bold mx-auto"
                                style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.72rem;">
                                <i class="ti ti-device-laptop me-1"></i> Multi-Media
                            </span>
                            <h5 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Modul
                                Interaktif</h5>
                            <p class="text-muted small mb-0">Materi pembelajaran lengkap berupa artikel, video YouTube,
                                dan dokumen PDF panduan.</p>
                        </div>
                    </div>

                    <!-- Feature 2: Tugas Essay Online -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card-modern h-100 p-4"
                            style="--feat-color: #059669; --feat-glow: rgba(16, 185, 129, 0.35); --feat-gradient: linear-gradient(90deg, #059669, #10B981);">
                            <div class="feature-top-bar"></div>
                            <i class="ti ti-pencil feature-watermark"></i>
                            <div class="feature-icon-wrapper"
                                style="background: linear-gradient(135deg, #059669 0%, #10B981 100%);">
                                <i class="ti ti-pencil fs-2"></i>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 mb-2 fw-bold mx-auto"
                                style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 0.72rem;">
                                <i class="ti ti-writing me-1"></i> Feedback Guru
                            </span>
                            <h5 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Tugas Essay
                                Online</h5>
                            <p class="text-muted small mb-0">Kumpulkan jawaban tugas essay dengan praktis, dapatkan
                                skor transparan, serta catatan feedback dari guru.</p>
                        </div>
                    </div>

                    <!-- Feature 3: Kuis Realtime -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card-modern h-100 p-4"
                            style="--feat-color: #D97706; --feat-glow: rgba(245, 158, 11, 0.35); --feat-gradient: linear-gradient(90deg, #D97706, #F59E0B);">
                            <div class="feature-top-bar"></div>
                            <i class="ti ti-clock-play feature-watermark"></i>
                            <div class="feature-icon-wrapper"
                                style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%);">
                                <i class="ti ti-clock-play fs-2"></i>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 mb-2 fw-bold mx-auto"
                                style="background: rgba(245, 158, 11, 0.1); color: #D97706; font-size: 0.72rem;">
                                <i class="ti ti-stopwatch me-1"></i> Auto-Submit
                            </span>
                            <h5 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Kuis Realtime
                            </h5>
                            <p class="text-muted small mb-0">Uji pemahaman lewat kuis pilihan ganda berbatas waktu
                                dengan auto-submit dan review kunci jawaban.</p>
                        </div>
                    </div>

                    <!-- Feature 4: Laporan Diri -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-card-modern h-100 p-4"
                            style="--feat-color: #6366F1; --feat-glow: rgba(99, 102, 241, 0.35); --feat-gradient: linear-gradient(90deg, #6366F1, #8B5CF6);">
                            <div class="feature-top-bar"></div>
                            <i class="ti ti-chart-dots feature-watermark"></i>
                            <div class="feature-icon-wrapper"
                                style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);">
                                <i class="ti ti-chart-dots fs-2"></i>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 mb-2 fw-bold mx-auto"
                                style="background: rgba(99, 102, 241, 0.1); color: #6366F1; font-size: 0.72rem;">
                                <i class="ti ti-report-analytics me-1"></i> Rekap Nilai
                            </span>
                            <h5 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Laporan Diri
                            </h5>
                            <p class="text-muted small mb-0">Pantau perkembangan akademik, statistik pengerjaan kuis,
                                dan perolehan nilaimu secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Showcase Banner with Learning Image -->
            <div class="mb-5" data-aos="zoom-in">
                <div class="card border-0 rounded-4 overflow-hidden shadow-xl text-white position-relative"
                    style="background: linear-gradient(135deg, #162f59 0%, #20456E 40%, #3368A0 100%); border: 1px solid rgba(255, 255, 255, 0.18) !important;">
                    <!-- Ambient Glow Decoration -->
                    <div class="position-absolute top-0 end-0 p-5 pointer-events-none"
                        style="background: radial-gradient(circle, rgba(102, 163, 191, 0.25) 0%, transparent 70%); width: 350px; height: 350px;">
                    </div>

                    <div class="card-body p-4 p-lg-5 position-relative z-1">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-5 text-center text-lg-start">
                                <span
                                    class="badge bg-warning text-dark font-bold px-3 py-2 rounded-pill mb-3 d-inline-flex align-items-center gap-1.5 shadow-sm">
                                    <i class="ti ti-trophy-filled text-dark"></i> QUIZ CHAMPIONSHIP
                                </span>
                                <h3 class="fw-extrabold text-white display-6 mb-3"
                                    style="font-family: 'Jost', sans-serif; letter-spacing: -0.5px;">Uji Pemahamanmu &
                                    Raih Skor Tertinggi!</h3>
                                <p class="text-white-50 fs-6 mb-4" style="line-height: 1.6;">
                                    Tantang dirimu mengerjakan kuis online dengan timer hitung mundur. Dapatkan evaluasi
                                    instan dan pembahasan jawaban secara langsung.
                                </p>
                                <a href="{{ route('student.quizzes.index') }}"
                                    class="btn text-primary font-bold px-4 py-3 rounded-pill text-decoration-none shadow-lg hover-lift d-inline-flex align-items-center gap-2"
                                    style="background-color: #ffffff; color: #3368A0 !important;">
                                    <i class="ti ti-help-hexagon fs-5"></i> Lihat Daftar Kuis Aktif <i
                                        class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                            <div class="col-lg-7 text-center">
                                <img src="{{ asset('images/quiz-achievement.jpg') }}" alt="Prestasi Kuis Siswa SMA"
                                    class="img-fluid rounded-4 shadow-2xl"
                                    style="border: 3px solid rgba(255, 255, 255, 0.25); max-height: 320px; width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arsha Courses Catalog Section (Ambient Frosted Container Layout) -->
            <div class="p-4 p-md-5 rounded-4 shadow-sm mb-5 position-relative overflow-hidden bg-abstract-catalog">
                <div class="arsha-section-title text-center mb-4" data-aos="fade-up">
                    <span
                        class="badge rounded-pill px-3.5 py-2 font-bold uppercase tracking-wider text-xs mb-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
                        style="background: linear-gradient(135deg, #3368A0, #66A3BF); color: #ffffff !important;">
                        <i class="ti ti-books text-white"></i> Modul Siswa
                    </span>
                    <h2 style="color: #3368A0; letter-spacing: -0.5px;">MATERI PELAJARAN</h2>
                    <p class="text-secondary fw-medium">Pilih materi pelajaran kelasmu dan eksplorasi modul interaktif,
                        video pembelajaran, serta dokumen PDF</p>
                </div>

                <div class="row g-4 justify-content-center">
                    @forelse($materials as $index => $mat)
                        @php
                            $gradients = [
                                'linear-gradient(135deg, #1e3a8a 0%, #3368A0 100%)',
                                'linear-gradient(135deg, #3368A0 0%, #66A3BF 100%)',
                                'linear-gradient(135deg, #0f172a 0%, #3368A0 100%)',
                                'linear-gradient(135deg, #20456E 0%, #66A3BF 100%)',
                            ];
                            $cardBgGradient = $gradients[$index % count($gradients)];
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4" data-aos="zoom-in"
                            data-aos-delay="{{ 100 + ($index % 3) * 100 }}">
                            <div class="course-card-modern h-100 position-relative">
                                <div>
                                    <!-- Rich Colorful Course Card Banner Header -->
                                    <div class="p-4 text-white position-relative overflow-hidden d-flex flex-column justify-content-between"
                                        style="background: {{ $cardBgGradient }}; min-height: 135px;">
                                        <!-- Giant Background Watermark Icon -->
                                        <i class="ti ti-bookmark position-absolute bottom-0 end-0 me-n2 mb-n3 opacity-20 text-white pointer-events-none"
                                            style="font-size: 5.5rem;"></i>

                                        <div
                                            class="d-flex align-items-center justify-content-between mb-2 position-relative z-1">
                                            <span
                                                class="badge text-white font-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1"
                                                style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(6px);">
                                                <i class="ti ti-tag me-1"></i>
                                                {{ $mat->subject->name ?? 'Mata Pelajaran' }}
                                            </span>
                                            <span class="badge text-dark rounded-pill px-2.5 py-1 font-bold shadow-sm"
                                                style="background-color: #F2EFE7; font-size: 0.72rem;">
                                                <i class="ti ti-star-filled text-warning me-1"></i> Modul SMA
                                            </span>
                                        </div>

                                        <h5 class="fw-extrabold text-white mb-0 position-relative z-1"
                                            style="font-family: 'Jost', sans-serif; font-size: 1.2rem; line-height: 1.3;">
                                            {{ $mat->title }}
                                        </h5>
                                    </div>

                                    <!-- Card Body Content -->
                                    <div class="p-4 bg-white">
                                        <!-- Instructor Row -->
                                        <div class="d-flex align-items-center gap-2.5 mb-3">
                                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                                style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 36px; height: 36px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($mat->instructor->name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-muted small" style="font-size: 0.72rem;">Guru
                                                    Pengampu</div>
                                                <div class="fw-bold text-dark small" style="margin-top: -2px;">
                                                    {{ $mat->instructor->name ?? 'Pengajar SMA' }}</div>
                                            </div>
                                        </div>

                                        <!-- Content Format Badges -->
                                        <div class="d-flex flex-wrap gap-1.5 mb-3">
                                            @if ($mat->youtube_url)
                                                <span class="badge rounded-pill text-danger font-bold px-2.5 py-1"
                                                    style="background: rgba(220, 38, 38, 0.1); font-size: 0.72rem;">
                                                    <i class="ti ti-brand-youtube me-1"></i> Video HD
                                                </span>
                                            @endif
                                            @if ($mat->document_path)
                                                <span class="badge rounded-pill text-success font-bold px-2.5 py-1"
                                                    style="background: rgba(16, 185, 129, 0.12); font-size: 0.72rem;">
                                                    <i class="ti ti-file-text me-1"></i> PDF Handout
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Meta Strip -->
                                        <div class="d-flex align-items-center justify-content-between text-muted small pt-3 border-top"
                                            style="border-color: rgba(51, 104, 160, 0.1) !important;">
                                            <span><i class="ti ti-clock text-warning me-1"></i> 15-30 Menit</span>
                                            <span><i class="ti ti-school text-info me-1"></i>
                                                {{ $mat->schoolClass->name ?? 'Kelas SMA' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 pt-0 bg-white">
                                    <a href="{{ route('student.materials.show', $mat) }}"
                                        class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift"
                                        style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                                        Pelajari Modul Ini <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="py-5 text-center text-muted rounded-4 bg-white border"
                                style="border-color: rgba(51, 104, 160, 0.15) !important;">
                                <i class="ti ti-notes-off fs-1 text-secondary mb-2 d-block"></i>
                                <h5 class="fw-bold text-dark">Belum ada materi untuk kelas Anda saat ini.</h5>
                            </div>
                        </div>
                    @endforelse

                    @if ($materials->count() == 1)
                        <!-- Companion Card to create a balanced 2-card layout when only 1 material is published -->
                        <div class="col-12 col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                            <div class="course-card-modern h-100 p-4 d-flex flex-column justify-content-between text-center"
                                style="background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(200, 223, 219, 0.25) 100%) !important; border: 2px dashed rgba(51, 104, 160, 0.22) !important;">
                                <div class="my-auto py-3">
                                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm"
                                        style="background: rgba(51, 104, 160, 0.1); width: 60px; height: 60px;">
                                        <i class="ti ti-books fs-2 text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2" style="font-family: 'Jost', sans-serif;">Modul
                                        Baru Segera Hadir</h5>
                                    <p class="text-muted small mb-0 px-2">Guru pengampu sedang menyiapkan modul dan
                                        video interaktif tambahan untuk kelas Anda.</p>
                                </div>
                                <a href="{{ route('student.materials.index') }}"
                                    class="btn rounded-pill py-2.5 font-bold hover-lift shadow-sm text-decoration-none"
                                    style="background: #ffffff; color: #3368A0; border: 1px solid rgba(51, 104, 160, 0.2);">
                                    <i class="ti ti-search me-1"></i> Telusuri Arsip Modul
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bottom CTA Action Button -->
                <div class="text-center mt-4 mt-md-5 px-3" data-aos="fade-up">
                    <a href="{{ route('student.materials.index') }}" class="btn btn-bottom-cta">
                        <i class="ti ti-layout-grid fs-5 shrink-0"></i>
                        <span>Jelajahi Seluruh Materi</span>
                        <i class="ti ti-arrow-right fs-5 shrink-0 cta-arrow"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- Active Quizzes & Assignments Section (Rich Ambient Frosted Widgets) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-5">
            <div class="row g-4">
                <!-- Left Column: Active Quizzes Widget -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="widget-glass-card h-100 p-4 p-md-4.5">
                        <!-- Top Accent Color Bar -->
                        <div
                            style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #6366F1, #8B5CF6);">
                        </div>

                        <!-- Clean Unified Section Header -->
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom"
                            style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-4 text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm"
                                    style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); width: 46px; height: 46px; box-shadow: 0 8px 18px rgba(99, 102, 241, 0.3) !important;">
                                    <i class="ti ti-help-hexagon fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="fw-extrabold text-dark m-0 d-flex align-items-center gap-2"
                                        style="font-family: 'Jost', sans-serif; font-size: 1.25rem;">
                                        Kuis Online Aktif
                                    </h4>
                                    <div class="text-muted small" style="font-size: 0.78rem;">Evaluasi pengerjaan
                                        real-time</div>
                                </div>
                            </div>
                            <a href="{{ route('student.quizzes.index') }}"
                                class="btn btn-sm rounded-pill px-3.5 py-1.5 font-bold hover-lift text-decoration-none shadow-sm"
                                style="background: rgba(99, 102, 241, 0.1); color: #6366F1 !important; font-size: 0.8rem; border: 1px solid rgba(99, 102, 241, 0.2);">
                                Lihat Semua <i class="ti ti-arrow-right ms-0.5"></i>
                            </a>
                        </div>

                        <!-- Quizzes Item List -->
                        <div class="d-flex flex-column gap-3">
                            @forelse($activeQuizzes as $qz)
                                <div
                                    class="item-card-modern p-4 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 hover-lift">
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="mb-2">
                                            <span
                                                class="badge text-white px-3 py-1 rounded-pill font-bold shadow-xs d-inline-flex align-items-center gap-1"
                                                style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); font-size: 0.75rem;">
                                                <i class="ti ti-tag"></i> {{ $qz->subject->name ?? 'Kuis' }}
                                            </span>
                                        </div>
                                        <h5 class="fw-extrabold text-dark mb-2"
                                            style="font-family: 'Jost', sans-serif; font-size: 1.05rem; line-height: 1.4;">
                                            {{ $qz->title }}
                                        </h5>
                                        <div class="text-muted small d-flex flex-wrap align-items-center gap-3"
                                            style="font-size: 0.8rem;">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-clock text-primary fs-6"></i>
                                                {{ $qz->duration_minutes }} Menit
                                            </span>
                                            <span class="opacity-30">•</span>
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-calendar-event text-danger fs-6"></i> Batas: <strong
                                                    class="text-dark">{{ $qz->deadline ? $qz->deadline->format('d M H:i') : '-' }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.quizzes.show', $qz) }}"
                                        class="btn text-white px-4 py-2.5 rounded-pill shadow-sm font-bold hover-lift shrink-0 text-decoration-none"
                                        style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); font-size: 0.85rem;">
                                        Ikuti Kuis <i class="ti ti-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-5 px-4 rounded-4"
                                    style="background: linear-gradient(145deg, rgba(255, 255, 255, 0.9) 0%, rgba(200, 223, 219, 0.2) 100%); border: 1px solid rgba(16, 185, 129, 0.2);">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                        style="background: rgba(16, 185, 129, 0.12); width: 68px; height: 68px;">
                                        <i class="ti ti-circle-check fs-1 text-success"></i>
                                    </div>
                                    <h5 class="fw-extrabold text-dark mb-1" style="font-family: 'Jost', sans-serif;">
                                        Semua Kuis Tuntas!</h5>
                                    <p class="text-muted small mb-3 max-w-sm mx-auto">Hebat! Tidak ada kuis aktif yang
                                        terlewat saat ini. Pertahankan prestasimu dan pantau hasil evaluasi sebelumnya.
                                    </p>
                                    <a href="{{ route('student.quizzes.index') }}"
                                        class="btn btn-sm rounded-pill px-4 py-2 font-bold hover-lift shadow-sm text-decoration-none"
                                        style="background: rgba(16, 185, 129, 0.12); color: #059669 !important; border: 1px solid rgba(16, 185, 129, 0.25);">
                                        <i class="ti ti-history me-1"></i> Riwayat & Skor Kuis
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Upcoming Assignments Widget -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="widget-glass-card h-100 p-4 p-md-4.5">
                        <!-- Top Accent Color Bar -->
                        <div
                            style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #D97706, #F59E0B);">
                        </div>

                        <!-- Clean Unified Section Header -->
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom"
                            style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-4 text-white d-flex align-items-center justify-content-center shrink-0 shadow-sm"
                                    style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%); width: 46px; height: 46px; box-shadow: 0 8px 18px rgba(245, 158, 11, 0.3) !important;">
                                    <i class="ti ti-notebook fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="fw-extrabold text-dark m-0 d-flex align-items-center gap-2"
                                        style="font-family: 'Jost', sans-serif; font-size: 1.25rem;">
                                        Tugas Perlu Dikumpulkan
                                    </h4>
                                    <div class="text-muted small" style="font-size: 0.78rem;">Pengumpulan jawaban
                                        essay & dokumen</div>
                                </div>
                            </div>
                            <a href="{{ route('student.assignments.index') }}"
                                class="btn btn-sm rounded-pill px-3.5 py-1.5 font-bold hover-lift text-decoration-none shadow-sm"
                                style="background: rgba(245, 158, 11, 0.1); color: #D97706 !important; font-size: 0.8rem; border: 1px solid rgba(245, 158, 11, 0.2);">
                                Lihat Semua <i class="ti ti-arrow-right ms-0.5"></i>
                            </a>
                        </div>

                        <!-- Assignments Item List -->
                        <div class="d-flex flex-column gap-3">
                            @forelse($upcomingAssignments as $asg)
                                <div
                                    class="item-card-modern p-4 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 hover-lift">
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="mb-2">
                                            <span
                                                class="badge text-white px-3 py-1 rounded-pill font-bold shadow-xs d-inline-flex align-items-center gap-1"
                                                style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%); font-size: 0.75rem;">
                                                <i class="ti ti-bookmark"></i> {{ $asg->subject->name ?? 'Tugas' }}
                                            </span>
                                        </div>
                                        <h5 class="fw-extrabold text-dark mb-2"
                                            style="font-family: 'Jost', sans-serif; font-size: 1.05rem; line-height: 1.4;">
                                            {{ $asg->title }}
                                        </h5>
                                        <div class="text-muted small d-flex flex-wrap align-items-center gap-3"
                                            style="font-size: 0.8rem;">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="ti ti-calendar-event text-danger fs-6"></i> Batas Waktu:
                                                <strong
                                                    class="text-danger">{{ $asg->due_date ? $asg->due_date->format('d M H:i') : '-' }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.assignments.show', $asg) }}"
                                        class="btn text-white px-4 py-2.5 rounded-pill shadow-sm font-bold hover-lift shrink-0 text-decoration-none align-self-sm-center"
                                        style="background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%); font-size: 0.85rem;">
                                        Kerjakan Tugas <i class="ti ti-send ms-1"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-5 px-4 rounded-4"
                                    style="background: linear-gradient(145deg, rgba(255, 255, 255, 0.9) 0%, rgba(51, 104, 160, 0.08) 100%); border: 1px solid rgba(51, 104, 160, 0.2);">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                                        style="background: rgba(51, 104, 160, 0.12); width: 68px; height: 68px;">
                                        <i class="ti ti-checklist fs-1 text-primary"></i>
                                    </div>
                                    <h5 class="fw-extrabold text-dark mb-1" style="font-family: 'Jost', sans-serif;">
                                        Semua Tugas Selesai!</h5>
                                    <p class="text-muted small mb-3 max-w-sm mx-auto">Bagus sekali! Tidak ada tugas
                                        yang menunggu pengumpulan saat ini. Kamu selalu bisa meninjau riwayat tugasmu.
                                    </p>
                                    <a href="{{ route('student.assignments.index') }}"
                                        class="btn btn-sm rounded-pill px-4 py-2 font-bold hover-lift shadow-sm text-decoration-none"
                                        style="background: rgba(51, 104, 160, 0.12); color: #3368A0 !important; border: 1px solid rgba(51, 104, 160, 0.25);">
                                        <i class="ti ti-folder-check me-1"></i> Riwayat Pengumpulan Tugas
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Close Ambient Geometric Vectors Layer -->
</x-app-layout>
