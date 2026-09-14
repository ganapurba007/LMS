<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        .quiz-detail-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 55%, #2b5788 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
        }
        .quiz-detail-hero::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 450px;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .content-card-modern {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(51, 104, 160, 0.14);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.75rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .content-card-modern:hover {
            border-color: rgba(102, 163, 191, 0.35);
        }
        .content-card-header {
            padding: 1.15rem 1.6rem;
            background: #F2EFE7;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .sidebar-sticky-box {
            position: sticky;
            top: 5.5rem;
        }
        .rule-step-card {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.25rem 1.4rem;
            border-radius: 14px;
            background: #ffffff;
            border: 1.5px solid rgba(51, 104, 160, 0.12);
            transition: all 0.2s ease;
        }
        .rule-step-card:hover {
            border-color: #3368A0;
            background: #fafcff;
            transform: translateX(4px);
        }
        .rule-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }
        .mini-color-legend {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .quiz-cta-box {
            background: #F8FAFC;
            border: 1.5px solid rgba(51, 104, 160, 0.16);
            border-radius: 18px;
            padding: 2.25rem 2rem;
            text-align: center;
        }

        /* Mobile & Responsive Screen Adjustments (< 768px) */
        @media (max-width: 767.98px) {
            .quiz-detail-hero {
                padding-top: 1.25rem !important;
                padding-bottom: 1.5rem !important;
            }
            .quiz-detail-hero h1 {
                font-size: 1.35rem !important;
                line-height: 1.35 !important;
                margin-bottom: 0.75rem !important;
            }
            .quiz-detail-hero .badge {
                font-size: 0.76rem !important;
                padding: 5px 10px !important;
            }
            .quiz-detail-hero .meta-stats-row {
                font-size: 0.8rem !important;
                gap: 0.6rem !important;
            }
            .hero-status-pill {
                display: flex !important;
                width: 100% !important;
                justify-content: space-between !important;
                padding: 8px 14px !important;
                margin-top: 0.75rem !important;
            }
            .content-card-modern {
                border-radius: 16px !important;
                margin-bottom: 1.25rem !important;
            }
            .content-card-header {
                padding: 1rem 1.15rem !important;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.6rem;
            }
            .content-card-header h5 {
                font-size: 1.05rem !important;
            }
            .content-card-body-pad {
                padding: 1.15rem 1rem !important;
            }
            .rule-step-card {
                gap: 0.85rem !important;
                padding: 1rem 1.05rem !important;
                border-radius: 12px !important;
            }
            .rule-icon-box {
                width: 38px !important;
                height: 38px !important;
                border-radius: 10px !important;
                font-size: 1.15rem !important;
            }
            .rule-step-card h6 {
                font-size: 0.92rem !important;
                line-height: 1.35 !important;
            }
            .rule-step-card p {
                font-size: 0.83rem !important;
                line-height: 1.55 !important;
            }
            .mini-color-legend {
                font-size: 0.74rem !important;
                padding: 4px 9px !important;
            }
            .quiz-cta-box {
                padding: 1.5rem 1.15rem !important;
                border-radius: 16px !important;
            }
            .quiz-cta-box h4 {
                font-size: 1.15rem !important;
            }
            .quiz-cta-box .btn {
                width: 100% !important;
                padding: 0.85rem 1.25rem !important;
                font-size: 0.95rem !important;
                justify-content: center;
            }
            .sidebar-sticky-box {
                position: static !important;
                margin-top: 0.5rem;
            }
        }
    </style>

    @php
        $isCompleted = $attempt && !is_null($attempt->submitted_at);
        $isInProgress = $attempt && is_null($attempt->submitted_at);
        $isOverdue = $quiz->deadline && $quiz->deadline->isPast() && !$isCompleted;
    @endphp

    <!-- 1. Dedicated Quiz Hero Header Banner -->
    <section class="quiz-detail-hero py-4 py-md-5">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Breadcrumbs & Badges -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2.5 mb-3">
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5" style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.78rem;">
                        <i class="ti ti-tag text-primary"></i> {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); font-size: 0.78rem;">
                        <i class="ti ti-school"></i> Kelas {{ $quiz->schoolClass->name ?? 'Siswa' }}
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill font-semibold d-inline-flex align-items-center gap-1 d-none d-sm-inline-flex" style="background: rgba(255, 255, 255, 0.15); font-size: 0.78rem;">
                        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Dashboard</a>
                        <i class="ti ti-chevron-right fs-6"></i>
                        <a href="{{ route('student.quizzes.index') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Kuis Online</a>
                        <i class="ti ti-chevron-right fs-6"></i>
                        <span class="text-white font-bold">Petunjuk Pengerjaan</span>
                    </span>
                </div>

                <!-- Back Button -->
                <div>
                    <a href="{{ route('student.quizzes.index') }}" 
                       class="btn btn-sm rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-xs" 
                       style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(6px); font-size: 0.8rem;">
                        <i class="ti ti-arrow-left"></i> <span class="d-none d-sm-inline">Kembali ke</span> Daftar Kuis
                    </a>
                </div>
            </div>

            <!-- Quiz Title & Info Row -->
            <div class="row align-items-center g-3 g-md-4 mt-1">
                <div class="col-lg-8">
                    <h1 class="display-6 fs-3 fs-md-2 fw-extrabold mb-2 text-white" style="font-family: 'Jost', sans-serif; letter-spacing: -0.3px; line-height: 1.25;">
                        {{ $quiz->title }}
                    </h1>
                    
                    <div class="d-flex flex-wrap align-items-center gap-2 text-white small meta-stats-row">
                        <span class="badge px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 font-semibold" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(6px); font-size: 0.78rem;">
                            <i class="ti ti-user-circle text-warning fs-6"></i>
                            <strong>{{ $quiz->instructor->name ?? 'Guru Pengampu' }}</strong>
                        </span>
                        <span class="badge px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 font-semibold" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(6px); font-size: 0.78rem;">
                            <i class="ti ti-clock fs-6"></i> {{ $quiz->formatted_duration }}
                        </span>
                        <span class="badge px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 font-semibold" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(6px); font-size: 0.78rem;">
                            <i class="ti ti-list-check fs-6"></i> {{ $quiz->total_questions_count }} Butir Soal
                        </span>
                        @if($isOverdue)
                            <span class="badge px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 font-bold" style="background-color: #FFE8E8; color: #DC2626 !important; border: 1px solid #FFA8A8; font-size: 0.78rem;">
                                <i class="ti ti-alert-triangle-filled text-danger fs-6"></i> Batas: {{ $quiz->deadline ? $quiz->deadline->format('d M Y - H:i') . ' WIB' : 'Tanpa Batas' }}
                            </span>
                        @else
                            <span class="badge px-3 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 font-bold" style="background-color: #FEF3C7; color: #92400E !important; border: 1px solid #FCD34D; font-size: 0.78rem;">
                                <i class="ti ti-clock-hour-4 text-warning fs-6"></i> Batas: {{ $quiz->deadline ? $quiz->deadline->format('d M Y - H:i') . ' WIB' : 'Tanpa Batas' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Hero Right: Spacious Status Pill -->
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="hero-status-pill d-inline-flex align-items-center gap-2.5 shadow-sm border" 
                         style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border-color: rgba(255, 255, 255, 0.6) !important; border-radius: 50rem; padding: 7px 16px;">
                        <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.8px;">Status:</span>
                        @if($isCompleted)
                            <span class="badge bg-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 font-bold shadow-xs" style="font-size: 0.82rem;">
                                <i class="ti ti-trophy fs-6"></i> Selesai (Skor: {{ $attempt->score }})
                            </span>
                        @elseif($isInProgress)
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 font-bold shadow-xs" style="font-size: 0.82rem;">
                                <i class="ti ti-hourglass fs-6"></i> Sedang Mengerjakan
                            </span>
                        @elseif($isOverdue)
                            <span class="badge bg-danger rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 font-bold shadow-xs" style="font-size: 0.82rem;">
                                <i class="ti ti-alert-triangle fs-6"></i> Waktu Habis
                            </span>
                        @else
                            <span class="badge rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 font-bold" style="background: #e2e8f0; color: #475569; font-size: 0.82rem;">
                                <i class="ti ti-clock fs-6"></i> Belum Dikerjakan
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area -->
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 py-md-5">
        
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
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2 p-3 p-md-4" role="alert" style="background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd !important;">
                <i class="ti ti-info-circle fs-4"></i>
                <div class="fw-semibold">{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            
            <!-- Left Main Column (Instructions, Rules, and Action Button) -->
            <div class="col-lg-8">
                
                <!-- Petunjuk & Tata Tertib Pengerjaan Card -->
                <div class="content-card-modern">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 36px; height: 36px;">
                                <i class="ti ti-book-2 fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 1.15rem;">
                                Petunjuk & Tata Tertib Pengerjaan Kuis
                            </h5>
                        </div>
                        <span class="badge rounded-pill px-3 py-1.5 font-bold small" style="background: rgba(51, 104, 160, 0.12); color: #20456E;">
                            <i class="ti ti-device-laptop me-1"></i> Ujian Online Real-Time
                        </span>
                    </div>

                    <div class="content-card-body-pad p-3 p-sm-4 p-md-5 bg-white">
                        
                        <!-- Description if available -->
                        @if($quiz->description)
                            <div class="p-3.5 p-md-4 rounded-4 mb-4" style="background: #F8FAFC; border-left: 4px solid #3368A0; border-top: 1px solid #E2E8F0; border-right: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0;">
                                <div class="text-primary small fw-bold text-uppercase mb-1.5" style="letter-spacing: 0.5px;">
                                    <i class="ti ti-notes me-1"></i> Catatan Khusus dari Guru Pengampu:
                                </div>
                                <div class="text-dark fw-medium" style="line-height: 1.65; font-size: 0.95rem;">
                                    {{ $quiz->description }}
                                </div>
                            </div>
                        @endif

                        <!-- Notice Heading -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-1" style="font-family: 'Jost', sans-serif; font-size: 1.05rem;">
                                Harap baca ketentuan berikut dengan seksama sebelum memulai kuis:
                            </h6>
                            <p class="text-muted small mb-0">
                                Sistem menerapkan proteksi pengerjaan otomatis untuk menjamin kelancaran dan integritas ujian Anda.
                            </p>
                        </div>

                        <!-- 6 Step / Rule Cards (Lega, Jelas & Modern) -->
                        <div class="d-flex flex-column gap-3 mb-5">
                            
                            <!-- 1. Waktu Berjalan Real-Time di Server -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(220, 53, 69, 0.12); color: #DC2626;">
                                    <i class="ti ti-clock-stop fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">1. Waktu Berjalan Real-Time di Server</h6>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        Durasi ujian adalah <strong>{{ $quiz->formatted_duration }}</strong> (<strong>{{ $quiz->duration_hms }}</strong>). Waktu akan langsung dihitung mundur di server begitu tombol <em>Mulai Kuis Sekarang</em> ditekan. 
                                        <strong class="text-danger">Jika Anda keluar dari kuis, menutup tab browser, atau me-refresh halaman, waktu ujian tetap terus berjalan</strong> dan tidak akan berhenti.
                                    </p>
                                </div>
                            </div>

                            <!-- 2. Penyimpanan Jawaban Otomatis (Auto-Save) -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                    <i class="ti ti-cloud-check fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">2. Penyimpanan Otomatis (Auto-Save)</h6>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        Setiap kali Anda mengklik opsi pilihan (A, B, C, D, dsb.), sistem secara otomatis menyimpan jawaban Anda ke server secara instan. Jawaban yang telah Anda pilih tidak akan hilang jika tab tertutup atau browser tiba-tiba tertutup.
                                    </p>
                                </div>
                            </div>

                            <!-- 3. Tampilan Satu Soal per Halaman & Indikator Warna -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                                    <i class="ti ti-layout-grid fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">3. Tampilan Satu Halaman Satu Soal & Penilaian Mandiri</h6>
                                    <p class="text-secondary small mb-2" style="line-height: 1.65;">
                                        Soal kuis disajikan bertahap <strong>satu nomor per halaman</strong> untuk memudahkan fokus Anda. Pada soal <em>Menjodohkan</em>, skor dinilai mandiri per pasangan premis (soal), bukan per paket. Gunakan tombol navigasi untuk berpindah soal. Panel dilengkapi penanda warna:
                                    </p>
                                    <div class="d-flex flex-wrap align-items-center gap-2 pt-1">
                                        <div class="mini-color-legend" style="background: #2563EB; color: #ffffff;">
                                            <i class="ti ti-edit fs-6"></i> Warna Biru: Sedang Dikerjakan
                                        </div>
                                        <div class="mini-color-legend" style="background: #10B981; color: #ffffff;">
                                            <i class="ti ti-circle-check fs-6"></i> Warna Hijau: Sudah Dijawab
                                        </div>
                                        <div class="mini-color-legend" style="background: #F1F5F9; color: #475569; border: 1.5px solid #CBD5E1;">
                                            <i class="ti ti-circle-dashed fs-6"></i> Warna Abu-abu: Belum Dijawab
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Pengumpulan Otomatis (Auto-Submit Saat Waktu Habis) -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(245, 158, 11, 0.12); color: #D97706;">
                                    <i class="ti ti-bolt fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">4. Otomatis Dikumpulkan Saat Waktu Habis</h6>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        Apabila hitungan mundur waktu telah mencapai <strong>00:00:00</strong>, lembar kuis akan otomatis dikumpulkan dan dinilai oleh sistem tanpa perlu menekan tombol kumpulkan manual.
                                    </p>
                                </div>
                            </div>

                            <!-- 5. Kestabilan Perangkat & Internet -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(14, 165, 233, 0.12); color: #0284C7;">
                                    <i class="ti ti-wifi fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">5. Kestabilan Koneksi Internet & Baterai</h6>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        Pastikan perangkat Anda memiliki daya baterai yang cukup dan koneksi internet stabil sepanjang pengerjaan kuis untuk kelancaran sinkronisasi data.
                                    </p>
                                </div>
                            </div>

                            <!-- 6. Pengumpulan Lembar Jawaban -->
                            <div class="rule-step-card">
                                <div class="rule-icon-box" style="background: rgba(100, 116, 139, 0.12); color: #334155;">
                                    <i class="ti ti-checkup-list fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">6. Kumpulkan Setelah Yakin</h6>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        Jika telah menyelesaikan seluruh soal sebelum waktu habis, klik tombol <strong>"Kumpulkan Jawaban Kuis"</strong>. Setelah dikumpulkan, hasil nilai dan ulasan pembahasan dapat langsung dilihat.
                                    </p>
                                </div>
                            </div>

                        </div>

                        <!-- Action Button Card (Lega, Nyaman, Sesuai Status Siswa) -->
                        <div class="quiz-cta-box p-4 p-md-5 rounded-4 text-center border" style="background: #F8FAFC; border-color: rgba(51, 104, 160, 0.18) !important;">
                            @if($isCompleted)
                                <div class="mb-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-success mb-2" style="background: rgba(16, 185, 129, 0.15); width: 68px; height: 68px;">
                                        <i class="ti ti-trophy" style="font-size: 2.4rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-1">Anda Sudah Menyelesaikan Kuis Ini</h4>
                                    <div class="display-6 fw-extrabold text-success mb-2">Nilai Akhir: {{ $attempt->score }} / 100</div>
                                    <p class="text-muted small mb-3">
                                        Diserahkan pada {{ $attempt->submitted_at ? $attempt->submitted_at->format('d F Y, H:i') . ' WIB' : '-' }}
                                    </p>
                                </div>
                                <a href="{{ route('student.quizzes.result', $quiz) }}" 
                                   class="btn text-white rounded-pill px-5 py-3 font-bold shadow-sm d-inline-flex align-items-center gap-2 hover-lift" 
                                   style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); font-size: 1.05rem;">
                                    <i class="ti ti-file-certificate fs-5"></i> Lihat Hasil & Pembahasan Soal
                                </a>
                            @elseif($isInProgress)
                                <div class="mb-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-warning mb-2" style="background: rgba(245, 158, 11, 0.15); width: 68px; height: 68px;">
                                        <i class="ti ti-player-play" style="font-size: 2.4rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-1">Sesi Pengerjaan Kuis Sedang Berlangsung</h4>
                                    <p class="text-muted small mb-3">
                                        Waktu ujian Anda sedang berjalan di server. Silakan klik tombol di bawah untuk langsung melanjutkan lembar pengerjaan kuis.
                                    </p>
                                </div>
                                <a href="{{ route('student.quizzes.attempt', $quiz) }}" 
                                   class="btn text-white rounded-pill px-5 py-3 font-bold shadow-sm d-inline-flex align-items-center gap-2 hover-lift" 
                                   style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); font-size: 1.05rem;">
                                    <i class="ti ti-player-play fs-5"></i> Lanjutkan Pengerjaan Kuis Sekarang
                                </a>
                            @elseif($isOverdue)
                                <div class="mb-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-danger mb-2" style="background: rgba(220, 38, 38, 0.15); width: 68px; height: 68px;">
                                        <i class="ti ti-lock" style="font-size: 2.4rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-danger mb-1">Batas Waktu Telah Berakhir</h4>
                                    <p class="text-muted small mb-3">
                                        Tenggat waktu kuis ini telah terlewat pada <strong>{{ $quiz->deadline ? $quiz->deadline->format('d F Y - H:i') . ' WIB' : '-' }}</strong>. Kuis telah ditutup dan tidak dapat dimulai lagi.
                                    </p>
                                </div>
                            @elseif($quiz->questions->count() === 0)
                                <div class="mb-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-warning mb-2" style="background: rgba(245, 158, 11, 0.15); width: 68px; height: 68px;">
                                        <i class="ti ti-clock-pause text-warning" style="font-size: 2.4rem;"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-1">Soal Ujian Belum Tersedia</h4>
                                    <p class="text-muted small mb-3">
                                        Guru pengampu belum mengunggah butir pertanyaan untuk kuis ini. Silakan periksa kembali nanti atau hubungi guru Anda.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-secondary rounded-pill px-5 py-3 font-bold shadow-none" disabled style="opacity: 0.65; cursor: not-allowed; font-size: 1rem;">
                                    <i class="ti ti-hourglass-empty me-1"></i> Soal Belum Tersedia
                                </button>
                            @else
                                <div class="mb-3">
                                    <h4 class="fw-bold text-dark mb-1">Sudah Siap Memulai Ujian?</h4>
                                    <p class="text-muted small mb-3">
                                        Pastikan Anda telah memahami seluruh petunjuk di atas sebelum menekan tombol mulai. Waktu akan langsung dihitung mundur di server.
                                    </p>
                                </div>
                                <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="btn text-white rounded-pill px-5 py-3 font-bold shadow-sm d-inline-flex align-items-center gap-2 hover-lift" 
                                            style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 1.05rem;">
                                        <i class="ti ti-player-play fs-5"></i> Mulai Kuis Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column (Sticky Parameters & Teacher Profile) -->
            <div class="col-lg-4">
                <div class="sidebar-sticky-box">
                    
                    <!-- Sidebar Card 1: 4 Parameter Kuis (Lega & Luas) -->
                    <div class="content-card-modern p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 34px; height: 34px;">
                                <i class="ti ti-adjustments fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif;">Parameter Kuis</h6>
                        </div>

                        <div class="row g-2.5 mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <div class="text-muted small mb-1" style="font-size: 0.75rem;"><i class="ti ti-clock me-1 text-primary"></i> Durasi</div>
                                    <div class="fs-5 fw-extrabold text-dark">{{ $quiz->formatted_duration }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <div class="text-muted small mb-1" style="font-size: 0.75rem;"><i class="ti ti-list-check me-1 text-success"></i> Soal</div>
                                    <div class="fs-5 fw-extrabold text-dark">{{ $quiz->total_questions_count }} Butir</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <div class="text-muted small mb-1" style="font-size: 0.75rem;"><i class="ti ti-award me-1 text-warning"></i> Bobot Poin</div>
                                    <div class="fs-5 fw-extrabold text-dark">{{ $quiz->points_per_question ?? 100 }} (Skala 100)</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light text-center border">
                                    <div class="text-muted small mb-1" style="font-size: 0.75rem;"><i class="ti ti-shield-check me-1 text-info"></i> Format</div>
                                    <div class="fs-6 fw-extrabold text-dark text-truncate">{{ $quiz->question_types_summary }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deadline Card -->
                        <div class="p-3 rounded-3 mb-0" style="background: {{ $isOverdue ? '#fef2f2' : '#F8FAFC' }}; border: 1px dashed {{ $isOverdue ? '#fca5a5' : 'rgba(51, 104, 160, 0.25)' }};">
                            <div class="small text-muted mb-1">Batas Waktu Pengerjaan:</div>
                            <div class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }} fs-6">
                                <i class="ti ti-calendar me-1"></i> {{ $quiz->deadline ? $quiz->deadline->format('d M Y - H:i') . ' WIB' : 'Tanpa Batas Waktu' }}
                            </div>
                            <div class="small mt-1 {{ $isOverdue ? 'text-danger fw-semibold' : 'text-secondary' }}">
                                @if($quiz->deadline)
                                    @if($quiz->deadline->isPast())
                                        <i class="ti ti-alert-circle me-1"></i> Waktu pengerjaan telah berakhir.
                                    @else
                                        <i class="ti ti-clock me-1"></i> Berakhir {{ $quiz->deadline->diffForHumans() }}.
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Card 2: Guru Pengampu -->
                    <div class="content-card-modern p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0284c7, #38bdf8); width: 34px; height: 34px;">
                                <i class="ti ti-user fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif;">Guru Pembuat Kuis</h6>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 48px; height: 48px; font-size: 1.15rem;">
                                {{ strtoupper(substr($quiz->instructor->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark fs-6 text-truncate">{{ $quiz->instructor->name ?? 'Guru Pengampu' }}</div>
                                <div class="text-muted small" style="font-size: 0.78rem;">
                                    NIP: {{ $quiz->instructor->nip ?? '-' }}
                                </div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-0.5 mt-1" style="font-size: 0.72rem;">
                                    Guru {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Nav -->
                    <a href="{{ route('student.quizzes.index') }}" 
                       class="btn btn-outline-secondary w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                       style="border-color: rgba(51, 104, 160, 0.25);">
                        <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kuis
                    </a>

                </div>
            </div>

        </div>

    </div>
</x-app-layout>
