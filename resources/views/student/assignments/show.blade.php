<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section */
        .assignment-detail-hero {
            background: linear-gradient(135deg, #1e3d60 0%, #2b5788 55%, #20456E 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
            padding-top: 1.75rem !important;
            padding-bottom: 1.75rem !important;
        }
        .assignment-detail-hero::before {
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
        .assignment-detail-hero::after {
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

        /* Modern Card Component */
        .content-card-modern {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(51, 104, 160, 0.12);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        @media (min-width: 768px) {
            .content-card-modern {
                border-radius: 16px;
                margin-bottom: 1.75rem;
            }
        }
        .content-card-modern:hover {
            border-color: rgba(102, 163, 191, 0.35);
        }
        .content-card-header {
            padding: 0.9rem 1rem;
            background: #F8FAFC;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        @media (min-width: 768px) {
            .content-card-header {
                padding: 1.15rem 1.4rem;
            }
        }

        /* Sticky Sidebar for Desktop only */
        @media (min-width: 992px) {
            .sidebar-sticky-box {
                position: sticky;
                top: 5.5rem;
            }
        }

        /* Typography & Utilities */
        .hero-title {
            font-size: clamp(1.25rem, 3.2vw, 1.85rem);
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }
        .assignment-instructions {
            font-size: 0.95rem;
            line-height: 1.75;
            color: #334155;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        @media (min-width: 768px) {
            .assignment-instructions {
                font-size: 1rem;
                line-height: 1.8;
            }
        }
    </style>

    @php
        $isSubmitted = !is_null($submission);
        $isGraded = $isSubmitted && !is_null($submission->grade);
        $isOverdue = $assignment->due_date && $assignment->due_date->isPast() && !$isSubmitted;
    @endphp

    <!-- 1. Dedicated Assignment Detail Page Hero -->
    <section class="assignment-detail-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Top Bar: Pelajaran & Tombol Kembali -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5 mb-3">
                <!-- Pelajaran -->
                <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5" 
                      style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                    <i class="ti ti-tag text-primary"></i> {{ $assignment->subject->name ?? 'Mata Pelajaran' }}
                </span>

                <!-- Tombol Kembali -->
                <a href="{{ route('student.assignments.index') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm hover-lift" 
                   style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(8px); font-size: 0.82rem;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Judul & Tanggal Tenggat Tugas -->
            <div>
                <h1 class="hero-title mb-2 text-white" style="font-family: 'Jost', sans-serif;">
                    {{ $assignment->title }}
                </h1>
                
                <div class="d-flex align-items-center gap-1.5 text-white-50 small" style="font-size: 0.82rem;">
                    <i class="ti ti-calendar text-warning"></i>
                    <span>Tenggat: {{ $assignment->due_date ? $assignment->due_date->translatedFormat('d F Y, H:i') . ' WIB' : 'Tanpa Batas Waktu' }}</span>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 2rem !important; padding-bottom: 3.5rem !important;">
        
        <!-- Flash Message Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-2 p-3" role="alert" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                <i class="ti ti-circle-check fs-4 shrink-0"></i>
                <div class="fw-semibold small">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-2 p-3" role="alert" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                <i class="ti ti-alert-triangle fs-4 shrink-0"></i>
                <div class="fw-semibold small">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3 g-lg-4">
            
            <!-- Left Main Column (Instructions, Grade & Feedback, Submission Form) -->
            <div class="col-lg-8">
                
                <!-- 2.1 Hasil Penilaian & Koreksi Guru (If Graded) -->
                @if($isGraded)
                    <div class="content-card-modern border-success-subtle shadow-sm" style="border-left: 4px solid #059669 !important;">
                        <div class="content-card-header" style="background: rgba(16, 185, 129, 0.08); border-bottom: 1px solid rgba(16, 185, 129, 0.15);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #059669, #10b981); width: 28px; height: 28px;">
                                    <i class="ti ti-trophy fs-6"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                    Hasil Penilaian &amp; Koreksi Guru
                                </h6>
                            </div>
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2.5 py-1 font-semibold small d-inline-flex align-items-center gap-1">
                                <i class="ti ti-circle-check-filled fs-6"></i> Tuntas Dinilai
                            </span>
                        </div>
                        <div class="p-3 p-sm-3.5 bg-white">
                            <!-- Responsive Grade Metric Strip -->
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="px-3 py-1.5 rounded-2 text-center shrink-0" style="background: #ffffff; border: 1.5px solid #86efac; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.08); min-width: 90px;">
                                        <div class="text-muted" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Nilai Akhir</div>
                                        <div class="fw-extrabold text-success" style="font-family: 'Jost', sans-serif; font-size: 1.55rem; line-height: 1.1;">
                                            {{ number_format($submission->grade, 1) }}
                                            <span class="text-muted fw-normal" style="font-size: 0.8rem;">/100</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small mb-0.5" style="font-size: 0.88rem;">Status Evaluasi Selesai</div>
                                        <div class="text-muted small" style="font-size: 0.78rem; line-height: 1.35;">
                                            Tugas telah diperiksa dan dinilai oleh guru pengampu.
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm-end small text-muted border-top border-sm-top-0 pt-2 pt-sm-0" style="font-size: 0.78rem;">
                                    @if($submission->graded_at)
                                        <div class="d-flex align-items-center gap-1 justify-content-sm-end">
                                            <i class="ti ti-calendar-check text-success"></i>
                                            <span>Dinilai: <strong>{{ $submission->graded_at->translatedFormat('d F Y, H:i') }} WIB</strong></span>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1 justify-content-sm-end mt-0.5">
                                        <i class="ti ti-user-check text-success"></i>
                                        <span>Penilai: <strong>{{ $assignment->instructor->name ?? 'Guru Pengampu' }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            @if($submission->feedback)
                                <div class="mt-2.5 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 3.5px solid #10b981;">
                                    <div class="small fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                        <i class="ti ti-message-dots text-success fs-6"></i>
                                        <span>Catatan &amp; Masukan Guru:</span>
                                    </div>
                                    <p class="mb-0 text-slate-700 small" style="line-height: 1.6; font-size: 0.85rem; word-break: break-word;">
                                        {{ $submission->feedback }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 2.2 Detail & Petunjuk Pengerjaan Tugas Card -->
                <div class="content-card-modern">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 28px; height: 28px;">
                                <i class="ti ti-file-text fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Detail &amp; Petunjuk Pengerjaan Tugas
                            </h6>
                        </div>
                    </div>
                    <div class="p-3 p-sm-4">
                        <!-- Judul Tugas -->
                        <div class="mb-4 pb-3 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="ti ti-notebook me-1 text-primary"></i> Judul Tugas
                            </label>
                            <h4 class="fw-extrabold text-dark mb-0" style="font-family: 'Jost', sans-serif; font-size: 1.25rem; line-height: 1.4;">
                                {{ $assignment->title }}
                            </h4>
                        </div>

                        <!-- Deskripsi & Instruksi Pengerjaan (TinyMCE Content) -->
                        <div>
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="ti ti-file-description me-1 text-primary"></i> Petunjuk &amp; Instruksi Pengerjaan Tugas
                            </label>
                            <div class="p-3 p-sm-3.5 rounded-3 article-body tinymce-content md-material-content assignment-instructions" style="background: var(--tblr-card-bg, #ffffff); border: 1px solid rgba(51, 104, 160, 0.12); color: var(--tblr-body-color, #1e293b);">
                                @if(!empty($assignment->description))
                                    {!! $assignment->description !!}
                                @else
                                    <span class="text-muted">Tidak ada deskripsi / petunjuk khusus untuk tugas ini.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2.3 Formulir Pengumpulan Jawaban -->
                <div class="content-card-modern">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: {{ $isSubmitted ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0284c7, #38bdf8)' }}; width: 28px; height: 28px;">
                                <i class="ti {{ $isSubmitted ? 'ti-circle-check' : 'ti-send' }} fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Formulir Pengumpulan Jawaban
                            </h6>
                        </div>
                        @if($isSubmitted)
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2.5 py-1 font-bold small text-truncate" style="max-width: 170px;">
                                <i class="ti ti-lock me-1"></i> Terkunci
                            </span>
                        @elseif($isOverdue)
                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2.5 py-1 font-bold small">
                                <i class="ti ti-lock me-1"></i> Ditutup
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1 font-bold small">
                                <i class="ti ti-edit me-1"></i> Belum Kirim
                            </span>
                        @endif
                    </div>

                    <div class="p-3 p-sm-4">
                        
                        @if(Auth::user() && Auth::user()->isGuru())
                            <div class="p-3 p-sm-3.5 rounded-3 border bg-light-subtle mb-0" style="border-color: rgba(51, 104, 160, 0.2) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 font-bold" style="font-size: 0.74rem;">
                                        <i class="ti ti-eye me-1"></i> Mode Guru
                                    </span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 font-bold" style="font-size: 0.74rem;">
                                        {{ $teacherStats['submitted_percent'] ?? 0 }}% Terkumpul
                                    </span>
                                </div>
                                <div class="row g-2 text-center my-2.5">
                                    <div class="col-6">
                                        <div class="p-2.5 rounded-3 bg-white border shadow-xs">
                                            <div class="text-muted small" style="font-size: 0.7rem;">Sudah Mengumpulkan</div>
                                            <div class="fw-extrabold text-dark fs-5">{{ $teacherStats['submitted_count'] ?? 0 }} <span class="text-muted fs-6 fw-normal">/ {{ $teacherStats['total_students'] ?? 0 }}</span></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2.5 rounded-3 bg-white border shadow-xs">
                                            <div class="text-muted small" style="font-size: 0.7rem;">Sudah Dinilai</div>
                                            <div class="fw-extrabold text-success fs-5">{{ $teacherStats['graded_count'] ?? 0 }} <span class="text-muted fs-6 fw-normal">Siswa</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress rounded-pill shadow-inner mb-3" style="height: 8px; background: rgba(51, 104, 160, 0.12);">
                                    <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $teacherStats['submitted_percent'] ?? 0 }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" aria-valuenow="{{ $teacherStats['submitted_percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <a href="{{ route('admin.submissions.index') }}" class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.85rem;">
                                    <i class="ti ti-clipboard-check fs-5"></i> Buka Penilaian Tugas di Admin
                                </a>
                            </div>
                        @else
                            @if($isSubmitted)
                                <div class="alert alert-success border-0 rounded-3 shadow-xs mb-3 d-flex align-items-start gap-2.5 p-3" style="background-color: #ecfdf5; border: 1px solid #a7f3d0 !important; color: #065f46;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0 mt-0.5" style="width: 32px; height: 32px; background: #d1fae5; color: #059669;">
                                        <i class="ti ti-circle-check fs-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold mb-0.5" style="font-size: 0.9rem;">Jawaban Berhasil Dikumpulkan</div>
                                        <div class="small opacity-90" style="font-size: 0.78rem; line-height: 1.4;">
                                            Dikirim pada <strong>{{ $submission->submitted_at ? $submission->submitted_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</strong>. Sesuai aturan sistem, pengumpulan tugas hanya dapat dilakukan <strong>1 (satu) kali</strong> dan jawaban Anda telah dikunci.
                                        </div>
                                    </div>
                                </div>
                            @elseif($isOverdue)
                                <div class="alert alert-danger border-0 rounded-3 shadow-xs mb-3 d-flex align-items-start gap-2.5 p-3" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center shrink-0 mt-0.5" style="width: 32px; height: 32px;">
                                        <i class="ti ti-lock fs-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold mb-0.5" style="font-size: 0.9rem;">Batas Waktu Pengumpulan Telah Berakhir</div>
                                        <div class="small opacity-90" style="font-size: 0.78rem; line-height: 1.4;">
                                            Tenggat waktu pengerjaan tugas ini telah lewat pada <strong>{{ $assignment->due_date ? $assignment->due_date->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</strong>. Pengumpulan jawaban telah dinonaktifkan.
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="answer_text" class="form-label fw-bold text-dark mb-1.5 small" style="font-size: 0.82rem;">
                                        Teks Jawaban / Catatan Pengerjaan Tugas <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="answer_text" 
                                              id="answer_text" 
                                              rows="6" 
                                              class="form-control rounded-3 p-3 shadow-none @error('answer_text') is-invalid @enderror" 
                                              style="border: 1.5px solid {{ $isSubmitted ? 'rgba(16, 185, 129, 0.35)' : ($isOverdue ? 'rgba(220, 53, 69, 0.3)' : 'rgba(51, 104, 160, 0.2)') }}; font-size: 0.88rem; line-height: 1.65; {{ ($isSubmitted || $isOverdue) ? 'background-color: #f8fafc; cursor: default;' : '' }}" 
                                              {{ ($isSubmitted || $isOverdue) ? 'disabled readonly' : 'required' }}
                                              placeholder="{{ $isSubmitted ? 'Jawaban tugas Anda telah tersimpan dan dikunci.' : ($isOverdue ? 'Batas waktu pengerjaan telah berakhir. Pengumpulan tugas telah dinonaktifkan.' : 'Ketik jawaban tugas essay, uraian, atau tautan berkas pengerjaan Anda di sini...') }}">{{ old('answer_text', $submission?->answer_text) }}</textarea>
                                    @error('answer_text')
                                        <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted small mt-1.5" style="font-size: 0.72rem;">
                                        @if($isSubmitted)
                                            <span class="text-success fw-semibold"><i class="ti ti-lock me-1"></i> Jawaban Anda telah tersimpan dan dikunci permanen (Hanya dapat dikirim 1 kali).</span>
                                        @elseif($isOverdue)
                                            <span class="text-danger fw-semibold"><i class="ti ti-lock me-1"></i> Form telah dikunci karena melewati batas waktu pengerjaan.</span>
                                        @else
                                            <i class="ti ti-info-circle text-primary me-0.5"></i> Pengumpulan hanya dapat dilakukan 1 kali. Pastikan jawaban Anda sudah lengkap sebelum menekan tombol kirim.
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2.5 gap-sm-3 pt-3 border-top" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                                    <div class="text-muted small" style="font-size: 0.76rem;">
                                        @if($isSubmitted)
                                            <i class="ti ti-circle-check text-success me-1"></i> Terkirim: {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : '-' }}
                                        @elseif($isOverdue)
                                            <span class="text-danger fw-semibold"><i class="ti ti-alert-triangle me-1"></i> Pengumpulan ditutup</span>
                                        @else
                                            <i class="ti ti-pencil me-1"></i> Form siap diisi
                                        @endif
                                    </div>

                                    @if($isSubmitted)
                                        <button type="button" 
                                                class="btn rounded-pill px-3.5 py-2 font-bold shadow-none d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-sm-auto" 
                                                disabled 
                                                style="cursor: not-allowed; opacity: 0.85; background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                                            <i class="ti ti-lock fs-5 text-secondary"></i>
                                            Telah Dikumpulkan (Pengumpulan 1x)
                                        </button>
                                    @elseif($isOverdue)
                                        <button type="button" 
                                                class="btn btn-secondary rounded-pill px-3.5 py-2 font-bold shadow-none d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-sm-auto" 
                                                disabled 
                                                style="cursor: not-allowed; opacity: 0.65; background-color: #94a3b8; border-color: #94a3b8; font-size: 0.82rem;">
                                            <i class="ti ti-lock fs-5"></i>
                                            Waktu Berakhir — Pengumpulan Ditutup
                                        </button>
                                    @else
                                        <button type="submit" 
                                                class="btn text-white rounded-pill px-4 py-2.5 font-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 hover-lift w-100 w-sm-auto" 
                                                style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%); font-size: 0.85rem;">
                                            <i class="ti ti-send fs-5"></i>
                                            Kirim Jawaban Tugas
                                        </button>
                                    @endif
                                </div>
                            </form>
                        @endif

                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column (Sticky Timeline, Teacher Profile, Quick Navigation) -->
            <div class="col-lg-4">
                <div class="sidebar-sticky-box">
                    
                    <!-- Sidebar Card 1: Batas Waktu & Urgensi -->
                    <div class="content-card-modern p-3 p-sm-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: {{ $isOverdue ? '#DC2626' : 'linear-gradient(135deg, #D97706, #F59E0B)' }}; width: 32px; height: 32px;">
                                <i class="ti ti-alarm fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">Tenggat Waktu</h6>
                        </div>

                        <div class="mb-3">
                            <div class="p-2.5 p-sm-3 rounded-3" style="background: {{ $isOverdue ? '#fef2f2' : '#F8FAFC' }}; border: 1px dashed {{ $isOverdue ? '#fca5a5' : 'rgba(51, 104, 160, 0.2)' }};">
                                <div class="small text-muted mb-1" style="font-size: 0.74rem;">Batas Akhir:</div>
                                <div class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }} fs-6" style="font-size: 0.92rem !important;">
                                    <i class="ti ti-calendar me-1"></i> {{ $assignment->due_date ? $assignment->due_date->translatedFormat('d F Y, H:i') . ' WIB' : 'Tanpa Batas Waktu' }}
                                </div>
                                <div class="small mt-1 {{ $isOverdue ? 'text-danger fw-semibold' : 'text-secondary' }}" style="font-size: 0.76rem;">
                                    @if($assignment->due_date)
                                        @if($assignment->due_date->isPast())
                                            <i class="ti ti-alert-circle me-1"></i> Waktu pengerjaan telah berakhir.
                                        @else
                                            <i class="ti ti-clock me-1"></i> Berakhir {{ $assignment->due_date->diffForHumans() }}.
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Checkpoint Checklist -->
                        <div class="pt-2.5 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                            <div class="d-flex align-items-center gap-2 small text-success mb-1.5" style="font-size: 0.78rem;">
                                <i class="ti ti-circle-check-filled"></i> Lembar tugas dibuka
                            </div>
                            <div class="d-flex align-items-center gap-2 small {{ $isSubmitted ? 'text-success' : 'text-muted' }} mb-1.5" style="font-size: 0.78rem;">
                                <i class="ti {{ $isSubmitted ? 'ti-circle-check-filled' : 'ti-circle' }}"></i> Jawaban dikirimkan
                            </div>
                            <div class="d-flex align-items-center gap-2 small {{ $isGraded ? 'text-success' : 'text-muted' }}" style="font-size: 0.78rem;">
                                <i class="ti {{ $isGraded ? 'ti-circle-check-filled' : 'ti-circle' }}"></i> Nilai &amp; umpan balik guru
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Card 2: Profil Guru Pengampu -->
                    <div class="content-card-modern p-3 p-sm-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 32px; height: 32px;">
                                <i class="ti ti-user fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">Guru Pengampu</h6>
                        </div>

                        <div class="d-flex align-items-center gap-2.5 gap-sm-3 mb-2">
                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 42px; height: 42px; font-size: 1rem;">
                                {{ strtoupper(substr($assignment->instructor->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">{{ $assignment->instructor->name ?? 'Guru Pengampu' }}</div>
                                @if($assignment->instructor && $assignment->instructor->nip)
                                    <div class="text-muted small" style="font-size: 0.74rem;">
                                        NIP: {{ $assignment->instructor->nip }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($assignment->instructor && $assignment->instructor->email)
                            <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                                <span>Email:</span>
                                <span class="text-dark fw-semibold text-truncate ms-2">{{ $assignment->instructor->email }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar Card 3: Pintasan Navigasi -->
                    <div class="content-card-modern p-3 p-sm-4">
                        <h6 class="fw-bold text-dark mb-2.5" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">Pintasan Menu Belajar</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.assignments.index') }}" class="btn btn-light border text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift" style="font-size: 0.82rem;">
                                <span><i class="ti ti-clipboard-list text-primary me-2"></i> Semua Tugas</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('student.materials.index') }}" class="btn btn-light border text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift" style="font-size: 0.82rem;">
                                <span><i class="ti ti-books text-success me-2"></i> Modul Materi Pelajaran</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('student.quizzes.index') }}" class="btn btn-light border text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift" style="font-size: 0.82rem;">
                                <span><i class="ti ti-help-hexagon text-warning me-2"></i> Kuis Online Aktif</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
