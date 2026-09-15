<x-app-layout :suppressGlobalAlerts="true">
    <!-- Include Bootstrap, Tabler Icons & Custom CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section */
        .material-detail-hero {
            background: linear-gradient(135deg, #1e3d60 0%, #2b5788 55%, #20456E 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #66A3BF;
            color: #ffffff;
            padding-top: 1.75rem !important;
            padding-bottom: 1.75rem !important;
        }
        .material-detail-hero::before {
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
        .material-detail-hero::after {
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

        /* Article / WYSIWYG Content Responsive Styling */
        .article-body {
            font-size: 0.96rem;
            line-height: 1.75;
            color: #334155;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        @media (min-width: 768px) {
            .article-body {
                font-size: 1.05rem;
                line-height: 1.8;
            }
        }
        .article-body p {
            margin-bottom: 1.15rem;
        }
        .article-body img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin: 0.85rem 0;
            display: block;
        }
        .article-body table {
            display: block;
            width: 100% !important;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 1.25rem 0;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .article-body table th,
        .article-body table td {
            padding: 0.6rem 0.85rem;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }
        .article-body table th {
            background-color: #f8fafc;
            font-weight: 600;
        }
        .article-body pre {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.88em;
            color: #0f172a;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 1rem 0;
        }
        .article-body code {
            background: #f1f5f9;
            border-radius: 6px;
            padding: 0.15rem 0.35rem;
            font-size: 0.88em;
            color: #0f172a;
            word-break: break-word;
        }
        .article-body blockquote {
            border-left: 4px solid #3368A0;
            padding: 0.5rem 0 0.5rem 1rem;
            color: #475569;
            font-style: italic;
            margin: 1.25rem 0;
            background: rgba(51, 104, 160, 0.04);
            border-radius: 0 8px 8px 0;
        }
        .article-body iframe,
        .article-body embed,
        .article-body object,
        .article-body video {
            max-width: 100% !important;
            border-radius: 10px;
            margin: 0.75rem 0;
        }

        /* Discussion & Comments Responsive Styling */
        .comment-bubble {
            background: #ffffff;
            border: 1px solid rgba(51, 104, 160, 0.12);
            border-radius: 12px;
            padding: 0.75rem 0.85rem;
            transition: all 0.2s ease;
        }
        @media (min-width: 768px) {
            .comment-bubble {
                padding: 0.85rem 1.15rem;
            }
        }
        .comment-bubble:hover {
            border-color: rgba(51, 104, 160, 0.28);
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
        }
        .comment-bubble.teacher-comment {
            background: #f8fbff;
            border-color: #bfdbfe;
            border-left: 3.5px solid #3b82f6;
        }
        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        @media (min-width: 768px) {
            .comment-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }
        }
        .reply-avatar {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.68rem !important;
        }
        @media (min-width: 768px) {
            .reply-avatar {
                width: 28px !important;
                height: 28px !important;
                font-size: 0.72rem !important;
            }
        }
        .discussion-thread {
            position: relative;
        }
        .discussion-replies {
            position: relative;
            margin-left: 0.65rem;
            padding-left: 0.65rem;
            border-left: 2px solid rgba(51, 104, 160, 0.2);
        }
        @media (min-width: 768px) {
            .discussion-replies {
                margin-left: 1.5rem;
                padding-left: 1rem;
            }
        }
        .reply-bubble {
            background: #f8fafc;
            border: 1px solid rgba(51, 104, 160, 0.12);
            border-radius: 10px;
            padding: 0.55rem 0.75rem;
            transition: all 0.2s ease;
        }
        @media (min-width: 768px) {
            .reply-bubble {
                padding: 0.65rem 0.95rem;
            }
        }
        .reply-bubble:hover {
            background: #ffffff;
            border-color: rgba(51, 104, 160, 0.28);
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .reply-bubble.teacher-reply {
            background: #f0f7ff;
            border-color: #bfdbfe;
            border-left: 3px solid #3b82f6;
        }
        .discussion-scroll-box {
            max-height: 440px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(51, 104, 160, 0.2) transparent;
            padding-right: 0.25rem;
        }
        .discussion-scroll-box::-webkit-scrollbar {
            width: 4px;
        }
        .discussion-scroll-box::-webkit-scrollbar-thumb {
            background-color: rgba(51, 104, 160, 0.2);
            border-radius: 6px;
        }
        .btn-reply-action {
            background: transparent;
            border: none;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #3368A0;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.15s ease;
            min-height: 26px;
        }
        .btn-reply-action:hover {
            color: #1e40af;
            background: rgba(51, 104, 160, 0.08);
            text-decoration: none;
        }
        .btn-reply-action.text-danger:hover {
            color: #b91c1c !important;
            background: rgba(220, 38, 38, 0.08);
        }

        /* Sticky Sidebar for Desktop only */
        @media (min-width: 992px) {
            .sidebar-sticky-box {
                position: sticky;
                top: 5.5rem;
            }
        }

        /* Fluid Typography and Buttons */
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
    </style>

    <!-- 1. Dedicated Material Detail Page Hero -->
    <section class="material-detail-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 position-relative z-1">
            
            <!-- Top Bar: Pelajaran & Tombol Kembali -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5 mb-3">
                <!-- Pelajaran -->
                <span class="badge px-3 py-1.5 rounded-pill shadow-xs font-bold d-inline-flex align-items-center gap-1.5" 
                      style="background-color: #F2EFE7; color: #20456E !important; font-size: 0.8rem;">
                    <i class="ti ti-tag text-primary"></i> {{ $material->subject->name ?? 'Mata Pelajaran' }}
                </span>

                <!-- Tombol Kembali -->
                <a href="{{ route('student.materials.index') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1.5 font-bold d-inline-flex align-items-center gap-1.5 text-white text-decoration-none shadow-sm hover-lift" 
                   style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4); backdrop-filter: blur(8px); font-size: 0.82rem;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Judul & Tanggal Materi -->
            <div>
                <h1 class="hero-title mb-2 text-white" style="font-family: 'Jost', sans-serif;">
                    {{ $material->title }}
                </h1>
                
                <div class="d-flex align-items-center gap-1.5 text-white-50 small" style="font-size: 0.82rem;">
                    <i class="ti ti-calendar text-warning"></i>
                    <span>{{ $material->created_at ? $material->created_at->translatedFormat('d F Y') : '-' }}</span>
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

        <div class="row g-3 g-lg-4">
            
            <!-- Left Main Column (Video, Modul Content, Documents, Discussions) -->
            <div class="col-lg-8">
                
                <!-- 2.1 Video Pembelajaran Embed (If Available) -->
                @if($material->video_url)
                    <div class="content-card-modern">
                        <div class="content-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: #DC2626; width: 30px; height: 30px;">
                                    <i class="ti ti-brand-youtube fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                                    Video Pembelajaran
                                </h5>
                            </div>
                        </div>
                        <div class="p-2.5 p-sm-3 p-md-4">
                            <div class="ratio ratio-16x9 rounded-3 rounded-md-4 overflow-hidden shadow-sm border">
                                @php
                                    $embedUrl = $material->video_url;
                                    if (str_contains($embedUrl, 'watch?v=')) {
                                        $embedUrl = str_replace('watch?v=', 'embed/', $embedUrl);
                                    } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                        $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $embedUrl);
                                    }
                                @endphp
                                <iframe src="{{ $embedUrl }}" title="{{ $material->title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2.2 Isi / Artikel Modul Materi -->
                @if($material->content)
                    <div class="content-card-modern">
                        <div class="content-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 30px; height: 30px;">
                                    <i class="ti ti-file-text fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                                    Uraian Materi
                                </h5>
                            </div>
                        </div>
                        <div class="p-3 p-sm-4 p-md-5">
                            <div class="article-body">
                                {!! $material->content !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2.3 Lampiran Dokumen Pembelajaran (If Available) -->
                @if($material->document_path)
                    <div class="content-card-modern">
                        <div class="content-card-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: #D97706; width: 30px; height: 30px;">
                                    <i class="ti ti-file-download fs-5"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">
                                    Lampiran Dokumen
                                </h5>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between p-3 p-sm-3.5 rounded-3 rounded-md-4 border gap-3" style="background: #F8FAFC;">
                                <div class="d-flex align-items-center gap-2.5 gap-sm-3">
                                    <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm shrink-0" style="background: linear-gradient(135deg, #D97706, #F59E0B); width: 42px; height: 42px;">
                                        <i class="ti ti-file-type-pdf fs-3"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h6 class="fw-bold mb-0.5 text-dark" style="font-size: 0.92rem;">Dokumen Pendukung Materi</h6>
                                        <div class="text-muted small" style="font-size: 0.76rem; line-height: 1.35;">Unduh materi ini untuk dibaca secara offline.</div>
                                    </div>
                                </div>
                                <div class="shrink-0 mt-2 mt-sm-0">
                                    <a href="{{ asset('storage/'.$material->document_path) }}" target="_blank" download class="btn text-white rounded-pill px-3.5 py-2 font-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5 hover-lift w-100 w-sm-auto" style="background: linear-gradient(135deg, #D97706, #F59E0B); font-size: 0.82rem;">
                                        <i class="ti ti-download fs-5"></i> Unduh Dokumen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Empty State if no media content is present -->
                @if(!$material->video_url && !$material->content && !$material->document_path)
                    <div class="content-card-modern p-4 p-md-5 text-center text-muted">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="background: rgba(51, 104, 160, 0.08); width: 60px; height: 60px;">
                            <i class="ti ti-book-off fs-2 text-secondary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Belum Ada Konten Pembelajaran</h5>
                        <p class="small text-muted mb-0">Guru pengampu belum melampirkan teks modul, video, atau dokumen pada topik materi ini.</p>
                    </div>
                @endif

                <!-- 2.4 Ruang Diskusi Interaktif Realtime -->
                <div class="content-card-modern">
                    <div class="content-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-xs shrink-0" style="background: linear-gradient(135deg, #059669, #10B981); width: 28px; height: 28px;">
                                <i class="ti ti-messages fs-6"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.95rem;">
                                Ruang Diskusi &amp; Tanya Jawab
                            </h6>
                        </div>
                        <span class="badge rounded-pill px-2.5 py-1 font-semibold small shadow-xs" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem;">
                            <i class="ti ti-users me-0.5"></i> {{ $material->discussions->count() }} Diskusi
                        </span>
                    </div>

                    <div class="p-2.5 p-sm-3 p-md-3.5">
                        
                        <!-- Discussion Comments Feed Container -->
                        <div id="discussion-list" class="discussion-scroll-box d-flex flex-column gap-2 mb-3">
                            @forelse($material->rootDiscussions as $disc)
                                @php
                                    $isTeacherComment = ($disc->user->role->name ?? '') === 'guru';
                                @endphp
                                <div id="discussion-item-{{ $disc->id }}" class="discussion-thread d-flex flex-column gap-1.5">
                                    <!-- Parent Comment Bubble -->
                                    <div class="comment-bubble d-flex gap-2 gap-sm-2.5 {{ $isTeacherComment ? 'teacher-comment' : '' }}">
                                        <!-- Avatar -->
                                        <div class="comment-avatar text-white shadow-xs" style="background: {{ $isTeacherComment ? 'linear-gradient(135deg, #1e40af, #3b82f6)' : 'linear-gradient(135deg, #0f766e, #14b8a6)' }};">
                                            {{ strtoupper(substr($disc->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        
                                        <!-- Body -->
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.84rem; max-width: 180px;">{{ $disc->user->name ?? 'Pengguna' }}</span>
                                                    @if($isTeacherComment)
                                                        <span class="badge rounded-pill px-1.5 py-0.5 font-bold" style="font-size: 0.62rem; background: #3b82f6; color: #ffffff;">
                                                            <i class="ti ti-school me-0.5"></i> Guru
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill px-1.5 py-0.5 font-semibold" style="font-size: 0.62rem; background: #f1f5f9; color: #475569;">
                                                            Siswa
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-muted small" style="font-size: 0.7rem;" title="{{ $disc->created_at ? $disc->created_at->locale('id')->translatedFormat('l, d F Y H:i') . ' WIB' : '' }}">
                                                    <i class="ti ti-clock me-0.5"></i> {{ $disc->created_at ? $disc->created_at->locale('id')->diffForHumans() : '' }}
                                                </span>
                                            </div>
                                            <p class="text-secondary mb-1.5" style="line-height: 1.48; white-space: pre-line; font-size: 0.825rem; word-break: break-word;">{{ $disc->comment }}</p>

                                            <!-- Reply Action Line -->
                                            <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3">
                                                <button type="button" 
                                                        class="btn-reply-action" 
                                                        onclick="openReplyForm('reply-form-{{ $disc->id }}', '{{ addslashes($disc->user->name ?? 'Pengguna') }}')">
                                                    <i class="ti ti-arrow-back-up"></i> Balas
                                                </button>
                                                @if(Auth::id() === $disc->user_id || (Auth::user() && Auth::user()->isGuru()))
                                                    <form action="{{ route('student.materials.discussions.destroy', [$material, $disc]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-reply-action text-danger">
                                                            <i class="ti ti-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($disc->replies->count() > 0)
                                                    <span class="badge rounded-pill bg-light text-secondary border px-2 py-0.5 font-semibold" style="font-size: 0.68rem;">
                                                        <i class="ti ti-corner-down-right me-0.5 text-primary"></i> {{ $disc->replies->count() }} balasan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nested Replies -->
                                    @if($disc->replies->count() > 0)
                                        <div class="discussion-replies d-flex flex-column gap-1.5 mt-0.5">
                                            @foreach($disc->replies as $reply)
                                                @php
                                                    $isTeacherReply = ($reply->user->role->name ?? '') === 'guru';
                                                @endphp
                                                <div id="discussion-item-{{ $reply->id }}" class="reply-bubble d-flex gap-1.5 gap-sm-2 {{ $isTeacherReply ? 'teacher-reply' : '' }}">
                                                    <!-- Avatar Reply -->
                                                    <div class="comment-avatar reply-avatar text-white shadow-xs" style="background: {{ $isTeacherReply ? 'linear-gradient(135deg, #1e40af, #3b82f6)' : 'linear-gradient(135deg, #475569, #64748b)' }};">
                                                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <!-- Body Reply -->
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-0.5">
                                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                <span class="fw-bold text-dark text-truncate" style="font-size: 0.78rem; max-width: 140px;">{{ $reply->user->name ?? 'Pengguna' }}</span>
                                                                @if($isTeacherReply)
                                                                    <span class="badge rounded-pill px-1.5 py-0.2 font-bold" style="font-size: 0.58rem; background: #3b82f6; color: #ffffff;">
                                                                        <i class="ti ti-school me-0.5"></i> Guru
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill px-1.5 py-0.2 font-semibold" style="font-size: 0.58rem; background: #f1f5f9; color: #475569;">
                                                                        Siswa
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <span class="text-muted small" style="font-size: 0.66rem;" title="{{ $reply->created_at ? $reply->created_at->locale('id')->translatedFormat('l, d F Y H:i') . ' WIB' : '' }}">
                                                                <i class="ti ti-clock me-0.5"></i> {{ $reply->created_at ? $reply->created_at->locale('id')->diffForHumans() : '' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-secondary mb-1" style="line-height: 1.42; white-space: pre-line; font-size: 0.78rem; word-break: break-word;">{{ $reply->comment }}</p>
                                                        
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button type="button" 
                                                                    class="btn-reply-action" 
                                                                    style="font-size: 0.7rem;"
                                                                    onclick="openReplyForm('reply-form-{{ $disc->id }}', '{{ addslashes($reply->user->name ?? 'Pengguna') }}')">
                                                                <i class="ti ti-arrow-back-up"></i> Balas
                                                            </button>
                                                            @if(Auth::id() === $reply->user_id || (Auth::user() && Auth::user()->isGuru()))
                                                                <form action="{{ route('student.materials.discussions.destroy', [$material, $reply]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus balasan ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn-reply-action text-danger" style="font-size: 0.7rem;">
                                                                        <i class="ti ti-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Inline Reply Form for this Thread -->
                                    <div id="reply-form-{{ $disc->id }}" class="reply-form-container ms-2 ms-sm-3 ms-md-4 ps-1 ps-md-2.5 d-none mt-1">
                                        <form action="{{ route('student.materials.discussions', $material) }}" method="POST" class="p-2 rounded-3 border bg-light-subtle shadow-xs">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $disc->id }}">
                                            <div class="d-flex align-items-center justify-content-between mb-1 gap-2">
                                                <span class="badge bg-white text-primary border px-2 py-0.5 font-medium text-truncate reply-target-label" style="font-size: 0.68rem; max-width: 80%;">
                                                    <i class="ti ti-arrow-back-up me-1"></i> Membalas <strong class="reply-username">{{ $disc->user->name ?? 'Pengguna' }}</strong>
                                                </span>
                                                <button type="button" class="btn-close shadow-none" style="font-size: 0.55rem;" onclick="closeReplyForm('reply-form-{{ $disc->id }}')"></button>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <input type="text" 
                                                       name="comment" 
                                                       class="form-control form-control-sm px-2.5 py-1 shadow-none reply-input border-end-0" 
                                                       placeholder="Tulis balasan..." 
                                                       style="font-size: 0.78rem;" 
                                                       required>
                                                <button type="submit" class="btn btn-primary btn-sm px-2.5 d-flex align-items-center gap-1 font-bold" style="font-size: 0.75rem;">
                                                    <i class="ti ti-send"></i> Kirim
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="background: rgba(51, 104, 160, 0.08); width: 44px; height: 44px;">
                                        <i class="ti ti-messages-off fs-4 text-secondary"></i>
                                    </div>
                                    <div class="small fw-semibold text-dark">Belum ada diskusi</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Jadilah yang pertama mengajukan pertanyaan atau tanggapan terkait materi ini!</div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Form Input Komentar Baru -->
                        @if(Auth::check())
                            <form action="{{ route('student.materials.discussions', $material) }}" method="POST" class="pt-2 border-top" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                                @csrf
                                <div class="d-flex align-items-center gap-2 mb-1.5">
                                    <div class="comment-avatar text-white shadow-xs" style="background: linear-gradient(135deg, #20456E, #3368A0); width: 26px; height: 26px; font-size: 0.7rem;">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="small fw-bold text-dark" style="font-size: 0.78rem;">Tulis Pertanyaan / Komentar Baru:</span>
                                </div>
                                <div class="input-group input-group-sm shadow-xs rounded-3 overflow-hidden border" style="border-color: rgba(51, 104, 160, 0.22) !important;">
                                    <input type="text" 
                                           name="comment" 
                                           class="form-control border-0 px-2.5 px-sm-3 py-2 shadow-none @error('comment') is-invalid @enderror" 
                                           placeholder="Ketik pertanyaan atau tanggapan seputar materi..." 
                                           style="font-size: 0.82rem;" 
                                           required>
                                    <button type="submit" class="btn text-white px-3 px-sm-3.5 font-bold d-flex align-items-center gap-1 shadow-none" style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%); font-size: 0.8rem;">
                                        <i class="ti ti-send"></i> <span class="d-none d-sm-inline">Kirim</span>
                                    </button>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                    <i class="ti ti-info-circle me-0.5"></i> Pertanyaan dan tanggapan kamu dapat dilihat oleh guru serta rekan sekelas.
                                </div>
                                @error('comment')
                                    <div class="text-danger small mt-1 fw-semibold" style="font-size: 0.75rem;">{{ $message }}</div>
                                @enderror
                            </form>
                        @endif

                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column (Sticky Progress, Teacher Card, Quick Navigation) -->
            <div class="col-lg-4">
                <div class="sidebar-sticky-box">
                    
                    <!-- Sidebar Card 1: Status & Progres Pembelajaran -->
                    <div class="content-card-modern p-3 p-sm-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(51, 104, 160, 0.12) !important;">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center shrink-0" style="background: linear-gradient(135deg, #3368A0, #66A3BF); width: 32px; height: 32px;">
                                <i class="ti ti-list-check fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">Status Penyelesaian</h6>
                        </div>

                        <div class="mb-3">
                            @if(Auth::user() && Auth::user()->isGuru())
                                <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1.5px solid rgba(51, 104, 160, 0.2);">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Progres Kelas</span>
                                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-bold" style="font-size: 0.74rem;">
                                            {{ $teacherStats['completed_percent'] ?? 0 }}% Selesai
                                        </span>
                                    </div>
                                    <div class="fw-extrabold text-dark fs-5 mb-1" style="font-family: 'Jost', sans-serif;">
                                        {{ $teacherStats['completed_count'] ?? 0 }} <span class="text-muted fs-6 fw-normal">/ {{ $teacherStats['total_students'] ?? 0 }} Siswa</span>
                                    </div>
                                    <div class="progress rounded-pill shadow-inner mb-2" style="height: 8px; background: rgba(51, 104, 160, 0.12);">
                                        <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $teacherStats['completed_percent'] ?? 0 }}%; background: linear-gradient(90deg, #3368A0, #66A3BF);" aria-valuenow="{{ $teacherStats['completed_percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.74rem; line-height: 1.35;">
                                        <i class="ti ti-users me-1 text-primary"></i> Total siswa di kelas ini yang sudah menandai materi tuntas dipelajari.
                                    </div>
                                </div>

                                <a href="{{ route('admin.materials.show', $material) }}" class="btn text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift text-decoration-none" style="background: linear-gradient(135deg, #20456E 0%, #3368A0 100%); font-size: 0.85rem;">
                                    <i class="ti ti-chart-bar fs-5"></i> Kelola di Panel Admin
                                </a>
                            @else
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="text-muted small">Status Materi:</span>
                                    @if($isCompleted)
                                        <span class="badge bg-success rounded-pill px-3 py-1 font-bold" style="font-size: 0.78rem;">
                                            <i class="ti ti-check me-1"></i> Tuntas Dipelajari
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1 font-bold" style="background: #F2EFE7; color: #D97706; border: 1px solid rgba(217, 119, 6, 0.3); font-size: 0.78rem;">
                                            <i class="ti ti-clock me-1"></i> Sedang Dipelajari
                                        </span>
                                    @endif
                                </div>

                                <form action="{{ route('student.materials.complete', $material) }}" method="POST">
                                    @csrf
                                    @if($isCompleted)
                                        <button type="submit" class="btn btn-light border w-100 rounded-pill py-2.5 font-bold d-flex align-items-center justify-content-center gap-2 hover-lift" style="font-size: 0.84rem;">
                                            <i class="ti ti-rotate-2 text-secondary"></i> Batalkan Status Tuntas
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success text-white w-100 rounded-pill py-2.5 font-bold shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift" style="font-size: 0.84rem;">
                                            <i class="ti ti-circle-check fs-5"></i> Tandai Selesai Dipelajari
                                        </button>
                                    @endif
                                </form>
                            @endif
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
                                {{ strtoupper(substr($material->instructor->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">{{ $material->instructor->name ?? 'Guru Pengampu' }}</div>
                                @if($material->instructor && $material->instructor->nip)
                                    <div class="text-muted small" style="font-size: 0.74rem;">
                                        NIP: {{ $material->instructor->nip }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($material->instructor && $material->instructor->email)
                            <div class="pt-2 border-top d-flex justify-content-between text-muted small" style="border-color: rgba(51, 104, 160, 0.1) !important; font-size: 0.76rem;">
                                <span>Email:</span>
                                <span class="text-dark fw-semibold text-truncate ms-2">{{ $material->instructor->email }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar Card 3: Pintasan Pembelajaran -->
                    <div class="content-card-modern p-3 p-sm-4">
                        <h6 class="fw-bold text-dark mb-2.5" style="font-family: 'Jost', sans-serif; font-size: 0.98rem;">Pintasan Menu Belajar</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.materials.index') }}" class="btn btn-light border text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift" style="font-size: 0.82rem;">
                                <span><i class="ti ti-books text-primary me-2"></i> Semua Modul Kelas</span>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('student.assignments.index') }}" class="btn btn-light border text-start py-2 px-3 rounded-3 d-flex align-items-center justify-content-between small fw-semibold text-dark hover-lift" style="font-size: 0.82rem;">
                                <span><i class="ti ti-clipboard-list text-primary me-2"></i> Semua Tugas</span>
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

    <script>
        function openReplyForm(formId, targetUsername) {
            // Tutup form balasan lain yang sedang terbuka
            document.querySelectorAll('.reply-form-container').forEach(function(el) {
                el.classList.add('d-none');
            });
            const container = document.getElementById(formId);
            if (container) {
                container.classList.remove('d-none');
                const usernameEl = container.querySelector('.reply-username');
                if (usernameEl && targetUsername) {
                    usernameEl.textContent = targetUsername;
                }
                const inputEl = container.querySelector('.reply-input');
                if (inputEl) {
                    if (targetUsername) {
                        inputEl.value = '@' + targetUsername + ' ';
                    }
                    inputEl.focus();
                }
            }
        }

        function closeReplyForm(formId) {
            const container = document.getElementById(formId);
            if (container) {
                container.classList.add('d-none');
                const inputEl = container.querySelector('.reply-input');
                if (inputEl) {
                    inputEl.value = '';
                }
            }
        }

        // Auto-scroll ke komentar/balasan jika terdapat hash di URL
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const targetEl = document.querySelector(window.location.hash);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetEl.classList.add('bg-warning-subtle');
                    setTimeout(() => {
                        targetEl.classList.remove('bg-warning-subtle');
                    }, 2500);
                }
            }
        });
    </script>
</x-app-layout>
