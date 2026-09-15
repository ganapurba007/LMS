@extends('layouts.be.master')

@section('header_title', 'Detail Materi & Ruang Diskusi — ' . $material->title)

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
    <div>
        <div class="d-flex align-items-center gap-1.5 mb-1">
            <span class="badge badge-soft-primary rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                <i class="ti ti-school me-1"></i> {{ $material->schoolClass->name ?? 'Kelas' }}
            </span>
            <span class="badge badge-soft-info rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                <i class="ti ti-book me-1"></i> {{ $material->subject->name ?? 'Mata Pelajaran' }}
            </span>
        </div>
        <h5 class="fw-bold m-0 heading-custom" style="font-size: 1.15rem;">{{ $material->title }}</h5>
        <p class="text-muted-custom mb-0" style="font-size: 0.8rem;">Kelola konten materi dan pantau serta balas pertanyaan dan diskusi dari siswa kelas.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.materials.index') }}" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 fw-semibold px-2.5 py-1.5" style="font-size: 0.8rem;">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 fw-semibold px-2.5 py-1.5" style="font-size: 0.8rem;">
            <i class="ti ti-edit"></i> Edit Materi
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 rounded-3" role="alert">
        <div class="d-flex align-items-center py-0.5" style="font-size: 0.85rem;">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 rounded-3" role="alert">
        <div class="d-flex align-items-center py-0.5" style="font-size: 0.85rem;">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics Overview Cards -->
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-hover mb-0 p-2.5 h-100">
            <div class="d-flex align-items-center gap-2.5">
                <div class="avatar-icon-box avatar-icon-primary flex-shrink-0" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="ti ti-users"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted-custom fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Siswa Kelas</div>
                    <div class="fw-bold heading-custom mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $totalStudents }} <span class="text-muted-custom fw-normal" style="font-size: 0.7rem;">Siswa</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-hover mb-0 p-2.5 h-100">
            <div class="d-flex align-items-center gap-2.5">
                <div class="avatar-icon-box avatar-icon-success flex-shrink-0" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="ti ti-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted-custom fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Sudah Membaca</div>
                    <div class="fw-bold text-success mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $completedStudentsCount }} <span class="text-muted-custom fw-normal" style="font-size: 0.7rem;">/ {{ $totalStudents }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-hover mb-0 p-2.5 h-100">
            <div class="d-flex align-items-center gap-2.5">
                <div class="avatar-icon-box avatar-icon-info flex-shrink-0" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="ti ti-messages"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted-custom fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Total Diskusi</div>
                    <div class="fw-bold text-info mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $material->discussions->count() }} <span class="text-muted-custom fw-normal" style="font-size: 0.7rem;">Komentar</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-hover mb-0 p-2.5 h-100">
            <div class="d-flex align-items-center gap-2.5">
                <div class="avatar-icon-box avatar-icon-warning flex-shrink-0" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="ti ti-sort-ascending-numbers"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted-custom fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Urutan Materi</div>
                    <div class="fw-bold text-warning mb-0" style="font-size: 0.95rem; line-height: 1.2;">Urutan ke-{{ $material->order }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-start">
    
    <!-- 1. Left Column: Konten & Lampiran Materi -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header py-2.5 px-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
                    <i class="ti ti-file-text text-primary fs-5"></i> Konten Materi
                </h6>
                <span class="badge badge-soft-secondary rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    {{ strtoupper($material->content_type ?? 'MODUL') }}
                </span>
            </div>
            <div class="card-body p-3">
                
                @if($material->video_url)
                    @php
                        $videoUrl = $material->video_url;
                        $embedUrl = null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches)) {
                            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                        }
                    @endphp
                    @if($embedUrl)
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3 border shadow-2xs">
                            <iframe src="{{ $embedUrl }}" title="Video Materi Pembelajaran" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="p-2.5 rounded-3 subtle-well mb-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                <i class="ti ti-brand-youtube text-danger fs-4"></i>
                                <span class="text-truncate small fw-semibold">{{ $material->video_url }}</span>
                            </div>
                            <a href="{{ $material->video_url }}" target="_blank" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                Buka Video
                            </a>
                        </div>
                    @endif
                @endif

                @if($material->document_path)
                    <div class="p-3 rounded-3 mb-3 subtle-well d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2.5 overflow-hidden me-2">
                            <div class="avatar-icon-box avatar-icon-danger flex-shrink-0" style="width: 38px; height: 38px;">
                                <i class="ti ti-file-type-pdf fs-4"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold heading-custom text-truncate" style="font-size: 0.85rem;">Dokumen Lampiran</div>
                                <div class="text-muted-custom small" style="font-size: 0.72rem;">File pendukung pembelajaran</div>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $material->document_path) }}" target="_blank" download class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.78rem;">
                            <i class="ti ti-download"></i> Unduh
                        </a>
                    </div>
                @endif

                @if($material->content)
                    <div class="material-text-content subtle-well p-3 mb-0" style="font-size: 0.88rem; line-height: 1.65; color: var(--tblr-body-color);">
                        {!! nl2br(e($material->content)) !!}
                    </div>
                @elseif(!$material->video_url && !$material->document_path)
                    <div class="empty-state py-4 text-muted-custom small">
                        <i class="ti ti-file-off fs-2 opacity-50 mb-1 d-block"></i>
                        Tidak ada isi teks atau lampiran pada materi ini.
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- 2. Right Column: Ruang Diskusi Interaktif Guru & Siswa -->
    <div class="col-lg-7">
        <div class="card mb-4" id="discussion-card">
            
            <!-- Card Header -->
            <div class="card-header py-2.5 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-icon-box avatar-icon-primary" style="width: 32px; height: 32px;">
                        <i class="ti ti-messages fs-5"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0" style="font-size: 0.95rem;">Ruang Diskusi &amp; Tanya Jawab</h6>
                        <div class="text-muted-custom" style="font-size: 0.72rem;">Forum tanya-jawab materi bersama siswa kelas</div>
                    </div>
                </div>
                <span class="badge badge-soft-primary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                    {{ $material->discussions->count() }} Komentar
                </span>
            </div>

            <div class="card-body p-3 p-md-3.5">
                
                <!-- 1. Form Kirim Tanggapan / Diskusi Baru oleh Guru -->
                <div class="p-3 subtle-well mb-4">
                    <form action="{{ route('admin.materials.discussions', $material) }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar bg-primary text-white rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'G', 0, 1)) }}
                            </div>
                            <span class="fw-bold heading-custom" style="font-size: 0.82rem;">{{ Auth::user()->name }}</span>
                            <span class="badge badge-soft-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                <i class="ti ti-award me-0.5"></i> Guru Pengampu
                            </span>
                        </div>

                        <div class="mb-2">
                            <textarea name="comment" 
                                      class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Tuliskan catatan, arahan, atau tanggapan untuk siswa kelas..." 
                                      required 
                                      style="font-size: 0.84rem; resize: vertical;"></textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.8rem;">
                                <i class="ti ti-send"></i> Kirim Pesan Diskusi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. Feed Komentar & Balasan Diskusi Siswa -->
                <div class="d-flex flex-column gap-3" id="discussion-list">
                    @forelse($material->rootDiscussions as $disc)
                        @php
                            $isTeacherRoot = $disc->user && $disc->user->isGuru();
                        @endphp
                        <div class="dashboard-feed-item d-flex flex-column gap-2" id="discussion-item-{{ $disc->id }}">
                            
                            <!-- Header Komentar Induk -->
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar {{ $isTeacherRoot ? 'bg-primary' : 'bg-secondary' }} text-white rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($disc->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="fw-bold heading-custom" style="font-size: 0.85rem;">{{ $disc->user->name ?? 'Pengguna' }}</span>
                                            @if($isTeacherRoot)
                                                <span class="badge badge-soft-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Guru</span>
                                            @else
                                                <span class="badge badge-soft-secondary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Siswa</span>
                                            @endif
                                        </div>
                                        <div class="text-muted-custom" style="font-size: 0.7rem;">
                                            <i class="ti ti-clock me-0.5"></i> {{ $disc->created_at ? $disc->created_at->diffForHumans() : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Aksi Hapus Komentar -->
                                <button type="button" 
                                        class="btn btn-sm btn-link text-danger p-0 opacity-75 hover:opacity-100" 
                                        onclick="openDeleteCommentModal('{{ route('admin.materials.discussions.destroy', [$material, $disc]) }}')" 
                                        title="Hapus Komentar">
                                    <i class="ti ti-trash fs-5"></i>
                                </button>
                            </div>

                            <!-- Isi Komentar Induk -->
                            <div class="py-1" style="font-size: 0.85rem; line-height: 1.55; color: var(--tblr-body-color);">
                                {!! nl2br(e($disc->comment)) !!}
                            </div>

                            <!-- Tombol Balas Komentar -->
                            <div class="d-flex align-items-center gap-2 pt-1 border-top" style="border-color: var(--tblr-border-color) !important;">
                                <button type="button" 
                                        class="btn btn-sm btn-soft-primary rounded-pill px-2.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" 
                                        style="font-size: 0.74rem;" 
                                        onclick="toggleReplyForm({{ $disc->id }})">
                                    <i class="ti ti-corner-down-right"></i> Balas Komentar Ini
                                </button>
                            </div>

                            <!-- Nested Thread Balasan (Replies) -->
                            @if($disc->replies && $disc->replies->count() > 0)
                                <div class="d-flex flex-column gap-2 mt-1 ps-3 border-start" style="border-width: 2.5px !important; border-color: var(--tblr-border-color) !important;">
                                    @foreach($disc->replies as $reply)
                                        @php
                                            $isTeacherReply = $reply->user && $reply->user->isGuru();
                                        @endphp
                                        <div class="p-2.5 rounded-3 {{ $isTeacherReply ? 'badge-soft-primary' : 'subtle-well' }}" id="discussion-item-{{ $reply->id }}">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar {{ $isTeacherReply ? 'bg-primary' : 'bg-secondary' }} text-white rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 24px; height: 24px; font-size: 0.68rem;">
                                                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                            <span class="fw-bold {{ $isTeacherReply ? 'text-primary' : 'heading-custom' }}" style="font-size: 0.8rem;">{{ $reply->user->name ?? 'Pengguna' }}</span>
                                                            @if($isTeacherReply)
                                                                <span class="badge bg-primary text-white rounded-pill px-1.5 py-0.2" style="font-size: 0.6rem;">Guru Pengampu</span>
                                                            @else
                                                                <span class="badge badge-soft-secondary rounded-pill px-1.5 py-0.2" style="font-size: 0.6rem;">Siswa</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted-custom" style="font-size: 0.68rem;">
                                                            <i class="ti ti-clock me-0.5"></i> {{ $reply->created_at ? $reply->created_at->diffForHumans() : '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" 
                                                        class="btn btn-sm btn-link text-danger p-0 opacity-75 hover:opacity-100" 
                                                        onclick="openDeleteCommentModal('{{ route('admin.materials.discussions.destroy', [$material, $reply]) }}')" 
                                                        title="Hapus Balasan">
                                                    <i class="ti ti-trash fs-6"></i>
                                                </button>
                                            </div>

                                            <div style="font-size: 0.82rem; line-height: 1.5; color: var(--tblr-body-color);">
                                                {!! nl2br(e($reply->comment)) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Inline Reply Form (Tersembunyi Default) -->
                            <div id="reply-form-{{ $disc->id }}" class="mt-2 p-2.5 rounded-3 subtle-well d-none">
                                <form action="{{ route('admin.materials.discussions', $material) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $disc->id }}">
                                    
                                    <div class="d-flex align-items-center gap-1.5 mb-1.5">
                                        <i class="ti ti-corner-down-right text-primary"></i>
                                        <span class="small fw-semibold text-muted-custom" style="font-size: 0.75rem;">
                                            Balas ke: <strong class="heading-custom">{{ $disc->user->name ?? 'Siswa' }}</strong>
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <textarea name="comment" 
                                                  class="form-control rounded-3" 
                                                  rows="2" 
                                                  placeholder="Ketik balasan untuk {{ $disc->user->name ?? 'siswa' }}..." 
                                                  required 
                                                  style="font-size: 0.82rem; resize: vertical;">{{ '@' . ($disc->user->name ?? 'Siswa') }} </textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-1.5">
                                        <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" onclick="toggleReplyForm({{ $disc->id }})">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.75rem;">
                                            <i class="ti ti-send"></i> Balas Siswa
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="empty-state py-4">
                            <div class="avatar-icon-box avatar-icon-primary mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                                <i class="ti ti-messages-off"></i>
                            </div>
                            <div class="fw-semibold heading-custom" style="font-size: 0.85rem;">Belum ada komentar diskusi pada materi ini.</div>
                            <div class="small text-muted-custom mt-0.5" style="font-size: 0.75rem;">Siswa dapat bertanya dan Anda dapat menanggapi secara langsung di forum ini.</div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Modal Konfirmasi Hapus Komentar Diskusi -->
<div class="modal fade" id="modalDeleteComment" tabindex="-1" aria-labelledby="modalDeleteCommentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: var(--tblr-card-bg); color: var(--tblr-body-color);">
            <div class="modal-body text-center p-4">
                <div class="avatar-icon-box avatar-icon-danger mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="ti ti-trash"></i>
                </div>
                <h6 class="fw-bold heading-custom mb-1" style="font-size: 0.95rem;">Hapus Komentar Ini?</h6>
                <p class="text-muted-custom mb-3" style="font-size: 0.78rem;">Komentar beserta balasannya akan dihapus permanen dari ruang diskusi.</p>
                <form id="deleteCommentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" style="font-size: 0.78rem;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3.5 fw-bold shadow-sm" style="font-size: 0.78rem;">
                            <i class="ti ti-trash me-1"></i> Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleReplyForm(discId) {
        const form = document.getElementById('reply-form-' + discId);
        if (form) {
            form.classList.toggle('d-none');
            if (!form.classList.contains('d-none')) {
                const textarea = form.querySelector('textarea');
                if (textarea) textarea.focus();
            }
        }
    }

    function openDeleteCommentModal(url) {
        const form = document.getElementById('deleteCommentForm');
        if (form) {
            form.action = url;
            const modal = new bootstrap.Modal(document.getElementById('modalDeleteComment'));
            modal.show();
        }
    }
</script>
@endsection
