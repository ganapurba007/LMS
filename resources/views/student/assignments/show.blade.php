<x-app-layout>
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        .assignment-detail-hero {
            background: linear-gradient(135deg, #20456E 0%, #3368A0 55%, #2b5788 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
        }
        .assignment-detail-hero::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        .content-card-modern {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(51, 104, 160, 0.12);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 1.75rem;
            transition: border-color 0.2s ease;
        }
        .content-card-modern:hover {
            border-color: rgba(102, 163, 191, 0.35);
        }
        .content-card-header {
            padding: 1.25rem 1.5rem;
            background: #F2EFE7;
            border-bottom: 1px solid rgba(51, 104, 160, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-sticky-box {
            position: sticky;
            top: 6.5rem;
        }
    </style>

    @php
        $isSubmitted = !is_null($submission);
        $isGraded = $isSubmitted && !is_null($submission->grade);
        $isOverdue = $assignment->due_date && $assignment->due_date->isPast() && !$isSubmitted;
    @endphp

    <!-- 1. Dedicated Assignment Detail Page Hero -->
    <section class="assignment-detail-hero py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Breadcrumbs & Badges -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-bold d-inline-flex align-items-center gap-1.5" style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                        <i class="ti ti-tag text-primary"></i> {{ $assignment->subject->name ?? 'Mata Pelajaran' }}
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill shadow-sm font-semibold d-inline-flex align-items-center gap-1.5" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); font-size: 0.8rem;">
                        <i class="ti ti-school"></i> Kelas {{ $assignment->schoolClass->name ?? 'Siswa' }}
                    </span>
                    <span class="badge px-3 py-1.5 rounded-pill font-semibold d-inline-flex align-items-center gap-1" style="background: rgba(255, 255, 255, 0.15); font-size: 0.8rem;">
                        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Dashboard</a>
                        <i class="ti ti-chevron-right fs-6"></i>
                        <a href="{{ route('student.assignments.index') }}" class="text-white text-decoration-none opacity-80 hover:opacity-100">Tugas Kelas</a>
                        <i class="ti ti-chevron-right fs-6"></i>
                        <span class="text-white font-bold">Detail Tugas</span>
                    </span>
                </div>

                <!-- Back to Index Button -->
                <div>
                    <a href="{{ route('student.assignments.index') }}" 
                       class="btn btn-sm rounded-pill px-3.5 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm" 
                       style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(6px);">
                        <i class="ti ti-arrow-left"></i> Kembali ke Daftar Tugas
                    </a>
                </div>
            </div>

            <!-- Assignment Title Header -->
            <div class="row align-items-center g-4 mt-1">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-extrabold mb-3 text-white" style="font-family: 'Jost', sans-serif; letter-spacing: -0.5px; line-height: 1.25;">
                        {{ $assignment->title }}
                    </h1>
                    
                    <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                        <span class="d-inline-flex align-items-center gap-1.5 text-white">
                            <i class="ti ti-user-circle fs-5 text-warning"></i>
                            <strong>{{ $assignment->instructor->name ?? 'Guru Pengampu' }}</strong>
                        </span>
                        <span>•</span>
                        <span>
                            <i class="ti ti-calendar me-1"></i> Dibuat: {{ $assignment->created_at ? $assignment->created_at->format('d F Y') : '-' }}
                        </span>
                        <span>•</span>
                        @if($isOverdue)
                            <span class="badge px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5 font-bold" style="background-color: #FFE8E8; color: #DC2626 !important; border: 1px solid #FFA8A8; font-size: 0.82rem;">
                                <i class="ti ti-alert-triangle-filled text-danger fs-6"></i> Batas: {{ $assignment->due_date ? $assignment->due_date->format('d F Y - H:i') . ' WIB' : 'Tanpa Batas' }}
                            </span>
                        @else
                            <span class="badge px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5 font-bold" style="background-color: #FEF3C7; color: #92400E !important; border: 1px solid #FCD34D; font-size: 0.82rem;">
                                <i class="ti ti-clock-hour-4 text-warning fs-6"></i> Batas: {{ $assignment->due_date ? $assignment->due_date->format('d F Y - H:i') . ' WIB' : 'Tanpa Batas' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Hero Right: Spacious Status Pill -->
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex align-items-center gap-3 shadow-sm border" 
                         style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border-color: rgba(255, 255, 255, 0.6) !important; border-radius: 50rem; padding: 8px 12px 8px 22px;">
                        <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.8px;">Status:</span>
                        @if($isGraded)
                            <span class="badge bg-success rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 font-bold shadow-sm" style="font-size: 0.85rem;">
                                <i class="ti ti-award fs-6"></i> Dinilai: {{ number_format($submission->grade, 1) }} / 100
                            </span>
                        @elseif($isSubmitted)
                            <span class="badge bg-info text-white rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 font-bold shadow-sm" style="font-size: 0.85rem;">
                                <i class="ti ti-circle-check fs-6"></i> Sudah Dikumpulkan
                            </span>
                        @elseif($isOverdue)
                            <span class="badge bg-danger rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 font-bold shadow-sm" style="font-size: 0.85rem;">
                                <i class="ti ti-alert-triangle fs-6"></i> Waktu Habis
                            </span>
                        @else
                            <span class="badge rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 font-bold" style="background: #e2e8f0; color: #475569; font-size: 0.85rem;">
                                <i class="ti ti-clock fs-6"></i> Belum Dikumpulkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        
        <!-- Flash Message Notification -->
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

        <div class="row g-4">
            
            <!-- Left Main Column (Instructions, Grade & Feedback, Submission Form) -->
            <div class="col-lg-8">
                
                <!-- 2.1 Hasil Penilaian & Koreksi Guru (If Graded) -->
                @if($isGraded)
                    <div class="content-card-modern border-success-subtle shadow-sm" style="border-left: 4px solid #059669 !important;">
                        <div class="content-card-header py-2.5 px-3 px-md-4" style="background: rgba(16, 185, 129, 0.08); border-bottom: 1px solid rgba(16, 185, 129, 0.15);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #059669, #10b981); width: 28px; height: 28px;">
                                    <i class="ti ti-trophy fs-6"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem; letter-spacing: 0.2px;">
                                    Hasil Penilaian & Koreksi Guru
                                </h6>
                            </div>
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2.5 py-1 font-semibold small d-inline-flex align-items-center gap-1">
                                <i class="ti ti-circle-check-filled fs-6"></i> Tuntas Dinilai
                            </span>
                        </div>
                        <div class="p-3 p-md-3.5 bg-white">
                            <!-- Compact Grade Metric Strip -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="px-3 py-1.5 rounded-2 text-center" style="background: #ffffff; border: 1.5px solid #86efac; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.08);">
                                        <div class="text-muted" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Nilai Akhir</div>
                                        <div class="fw-extrabold text-success" style="font-family: 'Jost', sans-serif; font-size: 1.65rem; line-height: 1.1;">
                                            {{ number_format($submission->grade, 1) }}
                                            <span class="text-muted fw-normal" style="font-size: 0.85rem;">/ 100</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small mb-0.5">Status Evaluasi Selesai</div>
                                        <div class="text-muted small" style="font-size: 0.8rem;">
                                            Tugas telah diperiksa dan dinilai oleh guru pengampu.
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm-end small text-muted" style="font-size: 0.8rem;">
                                    @if($submission->graded_at)
                                        <div class="d-flex align-items-center gap-1 justify-content-sm-end">
                                            <i class="ti ti-calendar-check text-success"></i>
                                            <span>Dinilai: <strong>{{ $submission->graded_at->format('d M Y - H:i') }} WIB</strong></span>
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
                                        <span>Catatan & Masukan Guru:</span>
                                    </div>
                                    <p class="mb-0 text-slate-700 small" style="line-height: 1.6; font-size: 0.865rem;">
                                        {{ $submission->feedback }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 2.2 Petunjuk & Deskripsi Tugas Card -->
                <div class="content-card-modern">
                    <div class="content-card-header py-2.5 px-3 px-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 28px; height: 28px;">
                                <i class="ti ti-file-description fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                                Petunjuk & Lembar Soal Tugas
                            </h6>
                        </div>
                        <span class="badge rounded-pill px-2.5 py-1 font-semibold small" style="background: rgba(51, 104, 160, 0.1); color: #3368A0;">
                            <i class="ti ti-writing me-1"></i> Tugas Mandiri
                        </span>
                    </div>
                    <div class="p-3.5 p-md-4">
                        <div class="p-3.5 rounded-3 text-dark fs-6" style="background: #F8FAFC; border: 1px solid rgba(51, 104, 160, 0.12); line-height: 1.7; font-size: 0.925rem;">
                            {!! nl2br(e($assignment->description ?? 'Tidak ada petunjuk khusus untuk tugas ini.')) !!}
                        </div>
                    </div>
                </div>

                <!-- 2.3 Formulir Pengumpulan Jawaban (Hanya 1 Kali Pengumpulan) -->
                <div class="content-card-modern">
                    <div class="content-card-header py-2.5 px-3 px-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: {{ $isSubmitted ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0284c7, #38bdf8)' }}; width: 28px; height: 28px;">
                                <i class="ti {{ $isSubmitted ? 'ti-circle-check' : 'ti-send' }} fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                                Formulir Pengumpulan Jawaban
                            </h6>
                        </div>
                        @if($isSubmitted)
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1 font-bold small">
                                <i class="ti ti-lock me-1"></i> Sudah Dikumpulkan (Terkunci)
                            </span>
                        @elseif($isOverdue)
                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-1 font-bold small">
                                <i class="ti ti-lock me-1"></i> Pengumpulan Ditutup
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 font-bold small">
                                <i class="ti ti-edit me-1"></i> Belum Mengirim
                            </span>
                        @endif
                    </div>

                    <div class="p-3.5 p-md-4">
                        
                        @if($isSubmitted)
                            <div class="alert alert-success border-0 rounded-3 shadow-sm mb-3 d-flex align-items-center gap-3 p-3" style="background-color: #ecfdf5; border: 1px solid #a7f3d0 !important; color: #065f46;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0" style="width: 36px; height: 36px; background: #d1fae5; color: #059669;">
                                    <i class="ti ti-circle-check fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6 mb-0.5">Jawaban Tugas Telah Berhasil Dikumpulkan</div>
                                    <div class="small opacity-90">
                                        Dikirim pada <strong>{{ $submission->submitted_at ? $submission->submitted_at->format('d F Y - H:i') . ' WIB' : '-' }}</strong>. Sesuai aturan sistem, pengumpulan tugas hanya dapat dilakukan <strong>1 (satu) kali</strong> dan jawaban Anda telah dikunci.
                                    </div>
                                </div>
                            </div>
                        @elseif($isOverdue)
                            <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-3 d-flex align-items-center gap-3 p-3" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca !important;">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center shrink-0" style="width: 36px; height: 36px;">
                                    <i class="ti ti-lock fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6 mb-0.5">Batas Waktu Pengumpulan Telah Berakhir</div>
                                    <div class="small opacity-90">
                                        Tenggat waktu pengerjaan tugas ini telah lewat pada <strong>{{ $assignment->due_date ? $assignment->due_date->format('d F Y - H:i') . ' WIB' : '-' }}</strong>. Pengumpulan jawaban telah dinonaktifkan.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST">
                            @csrf

                            <div class="mb-3.5">
                                <label for="answer_text" class="form-label fw-bold text-dark mb-1.5 small">
                                    Teks Jawaban / Catatan Pengerjaan Tugas <span class="text-danger">*</span>
                                </label>
                                <textarea name="answer_text" 
                                          id="answer_text" 
                                          rows="7" 
                                          class="form-control rounded-3 p-3 shadow-none @error('answer_text') is-invalid @enderror" 
                                          style="border: 1.5px solid {{ $isSubmitted ? 'rgba(16, 185, 129, 0.35)' : ($isOverdue ? 'rgba(220, 53, 69, 0.3)' : 'rgba(51, 104, 160, 0.2)') }}; font-size: 0.925rem; line-height: 1.7; {{ ($isSubmitted || $isOverdue) ? 'background-color: #f8fafc; cursor: default;' : '' }}" 
                                          {{ ($isSubmitted || $isOverdue) ? 'disabled readonly' : 'required' }}
                                          placeholder="{{ $isSubmitted ? 'Jawaban tugas Anda telah tersimpan dan dikunci.' : ($isOverdue ? 'Batas waktu pengerjaan telah berakhir. Pengumpulan tugas telah dinonaktifkan.' : 'Ketik jawaban tugas essay, uraian, atau tautan berkas pengerjaan Anda di sini...') }}">{{ old('answer_text', $submission?->answer_text) }}</textarea>
                                @error('answer_text')
                                    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted small mt-1.5">
                                    @if($isSubmitted)
                                        <span class="text-success fw-semibold"><i class="ti ti-lock me-1"></i> Jawaban Anda telah tersimpan dan dikunci permanen (Hanya dapat dikirim 1 kali).</span>
                                    @elseif($isOverdue)
                                        <span class="text-danger fw-semibold"><i class="ti ti-lock me-1"></i> Form telah dikunci karena melewati batas waktu pengerjaan.</span>
                                    @else
                                        <i class="ti ti-info-circle text-primary me-1"></i> Pengumpulan hanya dapat dilakukan 1 kali. Pastikan jawaban Anda sudah lengkap sebelum menekan tombol kirim.
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-3 border-top" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                                <div class="text-muted small">
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
                                            class="btn rounded-pill px-4 py-2 font-bold shadow-none d-inline-flex align-items-center gap-2" 
                                            disabled 
                                            style="cursor: not-allowed; opacity: 0.85; background-color: #e2e8f0; color: #475569; border: 1px solid #cbd5e1;">
                                        <i class="ti ti-lock fs-5 text-secondary"></i>
                                        Telah Dikumpulkan (Pengumpulan 1x)
                                    </button>
                                @elseif($isOverdue)
                                    <button type="button" 
                                            class="btn btn-secondary rounded-pill px-4 py-2 font-bold shadow-none d-inline-flex align-items-center gap-2" 
                                            disabled 
                                            style="cursor: not-allowed; opacity: 0.65; background-color: #94a3b8; border-color: #94a3b8;">
                                        <i class="ti ti-lock fs-5"></i>
                                        Waktu Berakhir — Pengumpulan Ditutup
                                    </button>
                                @else
                                    <button type="submit" 
                                            class="btn text-white rounded-pill px-4 py-2.5 font-bold shadow-sm d-inline-flex align-items-center gap-2 hover-lift" 
                                            style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                                        <i class="ti ti-send fs-5"></i>
                                        Kirim Jawaban Tugas
                                    </button>
                                @endif
                            </div>
                        </form>

                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column (Sticky Timeline, Teacher Profile, Quick Navigation) -->
            <div class="col-lg-4">
                <div class="sidebar-sticky-box">
                    
                    <!-- Sidebar Card 1: Batas Waktu & Urgensi -->
                    <div class="content-card-modern p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: {{ $isOverdue ? '#DC2626' : 'linear-gradient(135deg, #D97706, #F59E0B)' }}; width: 34px; height: 34px;">
                                <i class="ti ti-alarm fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif;">Tenggat Waktu (Deadline)</h6>
                        </div>

                        <div class="mb-3">
                            <div class="p-3 rounded-3 mb-3" style="background: {{ $isOverdue ? '#fef2f2' : '#F8FAFC' }}; border: 1px dashed {{ $isOverdue ? '#fca5a5' : 'rgba(51, 104, 160, 0.2)' }};">
                                <div class="small text-muted mb-1">Batas Akhir:</div>
                                <div class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }} fs-6">
                                    <i class="ti ti-calendar me-1"></i> {{ $assignment->due_date ? $assignment->due_date->format('d M Y - H:i') . ' WIB' : 'Tanpa Batas Waktu' }}
                                </div>
                                <div class="small mt-1 {{ $isOverdue ? 'text-danger fw-semibold' : 'text-secondary' }}">
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
                        <div class="pt-3 border-top" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                            <div class="d-flex align-items-center gap-2 small text-success mb-2">
                                <i class="ti ti-circle-check-filled"></i> Lembar tugas dibuka
                            </div>
                            <div class="d-flex align-items-center gap-2 small {{ $isSubmitted ? 'text-success' : 'text-muted' }} mb-2">
                                <i class="ti {{ $isSubmitted ? 'ti-circle-check-filled' : 'ti-circle' }}"></i> Jawaban dikirimkan
                            </div>
                            <div class="d-flex align-items-center gap-2 small {{ $isGraded ? 'text-success' : 'text-muted' }}">
                                <i class="ti {{ $isGraded ? 'ti-circle-check-filled' : 'ti-circle' }}"></i> Nilai & umpan balik guru
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Card 2: Profil Guru Pengampu -->
                    <div class="content-card-modern p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 34px; height: 34px;">
                                <i class="ti ti-user fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif;">Guru Pengampu</h6>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 48px; height: 48px; font-size: 1.1rem;">
                                {{ strtoupper(substr($assignment->instructor->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate fs-6">{{ $assignment->instructor->name ?? 'Guru Pengampu' }}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    NIP: {{ $assignment->instructor->nip ?? '-' }}
                                </div>
                                <span class="badge rounded-pill px-2 py-0.5 mt-1" style="background: rgba(51, 104, 160, 0.1); color: #3368A0; font-size: 0.68rem;">
                                    Guru Mata Pelajaran
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="border-color: rgba(51, 104, 160, 0.1) !important;">
                            <span>Email:</span>
                            <span class="text-dark fw-semibold text-truncate ms-2">{{ $assignment->instructor->email ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Sidebar Card 3: Pintasan Navigasi -->
                    <div class="content-card-modern p-4">
                        <h6 class="fw-bold text-dark mb-3" style="font-family: 'Jost', sans-serif;">Pintasan Menu Belajar</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.assignments.index') }}" class="btn btn-light border text-start py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift">
                                <span><i class="ti ti-clipboard-list text-primary me-2"></i> Semua Tugas Kelas</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('student.materials.index') }}" class="btn btn-light border text-start py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift">
                                <span><i class="ti ti-books text-success me-2"></i> Modul Materi Pelajaran</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('student.quizzes.index') }}" class="btn btn-light border text-start py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift">
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
