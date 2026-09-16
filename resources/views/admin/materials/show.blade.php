@extends('layouts.be.master')
@section('header_title', 'Master Data — Detail Materi & Diskusi')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-book"></i>
        </div>
        <div>
            <h5 class="md-title">{{ $material->title }}</h5>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.materials.index') }}" class="md-btn-secondary">
            <i class="ti ti-arrow-left"></i> <span>Kembali</span>
        </a>
        <a href="{{ route('admin.materials.edit', $material) }}" class="md-btn-primary">
            <i class="ti ti-edit"></i> <span>Edit Materi</span>
        </a>
    </div>
</div>

{{-- Flash Alert --}}
@if(session('success'))
    <div class="md-alert success mb-4">
        <i class="ti ti-check-circle"></i> {{ session('success') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

{{-- Quick Metrics Overview --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="md-card p-3 h-100 d-flex align-items-center gap-3">
            <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;width:42px;height:42px;font-size:1.1rem;margin:0;">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;font-weight:600;">Siswa Kelas</div>
                <div class="fw-bold" style="font-size:1.05rem;color:var(--tblr-heading-color,#0f172a);">
                    {{ $totalStudents }} <span class="text-muted fw-normal" style="font-size:.72rem;">Siswa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="md-card p-3 h-100 d-flex align-items-center gap-3">
            <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;width:42px;height:42px;font-size:1.1rem;margin:0;">
                <i class="ti ti-circle-check"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;font-weight:600;">Sudah Membaca</div>
                <div class="fw-bold text-success" style="font-size:1.05rem;">
                    {{ $completedStudentsCount }} <span class="text-muted fw-normal" style="font-size:.72rem;">/ {{ $totalStudents }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="md-card p-3 h-100 d-flex align-items-center gap-3">
            <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;width:42px;height:42px;font-size:1.1rem;margin:0;">
                <i class="ti ti-messages"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;font-weight:600;">Total Diskusi</div>
                <div class="fw-bold text-info" style="font-size:1.05rem;">
                    {{ $material->discussions_count }} <span class="text-muted fw-normal" style="font-size:.72rem;">Pesan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="md-card p-3 h-100 d-flex align-items-center gap-3">
            <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;width:42px;height:42px;font-size:1.1rem;margin:0;">
                <i class="ti ti-sort-ascending-numbers"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.72rem;font-weight:600;">Urutan Materi</div>
                <div class="fw-bold text-warning" style="font-size:1.05rem;">
                    #{{ $material->order }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-start">
    {{-- Left Column: Konten Materi & Media --}}
    <div class="col-12 col-lg-5">
        <div class="md-form-card mb-4">
            <div class="md-form-head">
                <div class="md-form-head-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                    <i class="ti ti-file-text"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <h6 class="md-form-head-title mb-0">Konten Pembelajaran</h6>
                </div>
            </div>

            <div class="md-form-body">
                {{-- YouTube Video --}}
                @if($material->video_url)
                    @php
                        $videoUrl = $material->video_url;
                        $embedUrl = null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches)) {
                            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                        }
                    @endphp
                    @if($embedUrl)
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3 border">
                            <iframe src="{{ $embedUrl }}" title="Video Materi Pembelajaran" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                <i class="ti ti-brand-youtube text-danger fs-4"></i>
                                <span class="text-truncate small fw-semibold">{{ $material->video_url }}</span>
                            </div>
                            <a href="{{ $material->video_url }}" target="_blank" class="md-btn-secondary" style="padding:.2rem .6rem;font-size:.72rem;">
                                Buka Video
                            </a>
                        </div>
                    @endif
                @endif

                {{-- Document Attachment --}}
                @if($material->document_path)
                    <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                        <div class="d-flex align-items-center gap-2.5 overflow-hidden me-2">
                            <div class="md-page-icon" style="background:rgba(239,68,68,.1);color:#ef4444;width:36px;height:36px;font-size:1.1rem;margin:0;">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-truncate" style="font-size:.84rem;color:var(--tblr-heading-color,#0f172a);">Dokumen Lampiran</div>
                                <div class="text-muted small" style="font-size:.72rem;">{{ basename($material->document_path) }}</div>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $material->document_path) }}" target="_blank" download class="md-btn-primary" style="padding:.3rem .75rem;font-size:.75rem;">
                            <i class="ti ti-download"></i> Unduh
                        </a>
                    </div>
                @endif

                {{-- Text Content --}}
                @if($material->content)
                    <div class="p-3 rounded-3" style="background:var(--tblr-card-bg,#fff);border:1px solid var(--tblr-border-color,#e2e8f0);line-height:1.65;font-size:.85rem;color:var(--tblr-body-color);">
                        {!! $material->content !!}
                    </div>
                @elseif(!$material->video_url && !$material->document_path)
                    <div class="text-center py-4 text-muted" style="font-size:.8rem;">
                        <i class="ti ti-file-off fs-2 d-block mb-1 opacity-50"></i>
                        Belum ada isi teks, video, ataupun dokumen lampiran pada materi ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Ruang Diskusi Kelas --}}
    <div class="col-12 col-lg-7" id="discussion-card">
        <div class="md-form-card mb-4">
            <div class="md-form-head">
                <div class="md-form-head-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                    <i class="ti ti-messages"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-grow-1">
                    <h6 class="md-form-head-title mb-0">Ruang Diskusi &amp; Tanya Jawab Kelas</h6>
                    <span class="md-badge blue">
                        {{ $material->discussions_count }} Pesan
                    </span>
                </div>
            </div>

            <div class="md-form-body">
                {{-- Form Kirim Komentar Guru --}}
                <div class="p-3 rounded-3 mb-4" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                    <form action="{{ route('admin.materials.discussions', $material) }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fw-bold" style="font-size:.82rem;color:var(--tblr-heading-color,#0f172a);">{{ Auth::user()->name }}</span>
                            <span class="md-badge blue" style="font-size:.65rem;padding:.15rem .45rem;">
                                <i class="ti ti-award"></i> Guru Pengampu
                            </span>
                        </div>

                        <div class="mb-2">
                            <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="2" placeholder="Tuliskan catatan, arahan, atau tanggapan untuk siswa kelas..." required style="font-size:.82rem;resize:vertical;"></textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="md-btn-primary" style="padding:.35rem .85rem;font-size:.78rem;">
                                <i class="ti ti-send"></i> Kirim Pesan Diskusi
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Feed Komentar --}}
                <div class="d-flex flex-column gap-3" id="discussion-list">
                    @forelse($material->rootDiscussions as $disc)
                        @php
                            $isTeacherRoot = $disc->user && $disc->user->isGuru();
                        @endphp
                        <div class="p-3 rounded-3" style="background:var(--tblr-card-bg,#fff);border:1px solid var(--tblr-border-color,#e2e8f0);" id="discussion-item-{{ $disc->id }}">
                            
                            {{-- Header Komentar Induk --}}
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar text-white rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.78rem;background:{{ $isTeacherRoot ? '#206bc4' : '#64748b' }};">
                                        {{ strtoupper(substr($disc->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="fw-bold" style="font-size:.82rem;color:var(--tblr-heading-color,#0f172a);">{{ $disc->user->name ?? 'Pengguna' }}</span>
                                            @if($isTeacherRoot)
                                                <span class="md-badge blue" style="font-size:.62rem;padding:.1rem .4rem;">Guru</span>
                                            @else
                                                <span class="md-badge teal" style="font-size:.62rem;padding:.1rem .4rem;">Siswa</span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size:.68rem;">
                                            <i class="ti ti-clock"></i> {{ $disc->created_at ? $disc->created_at->diffForHumans() : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="md-icon-btn red" style="width:26px;height:26px;font-size:.75rem;" onclick="openDeleteCommentModal('{{ route('admin.materials.discussions.destroy', [$material, $disc]) }}')" title="Hapus Komentar">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>

                            {{-- Isi Komentar Induk --}}
                            <div class="py-1" style="font-size:.82rem;line-height:1.55;color:var(--tblr-body-color);">
                                {!! nl2br(e($disc->comment)) !!}
                            </div>

                            {{-- Tombol Balas --}}
                            <div class="pt-2 mt-2 border-top d-flex align-items-center gap-2">
                                <button type="button" class="md-btn-secondary" style="padding:.2rem .6rem;font-size:.72rem;" onclick="toggleReplyForm({{ $disc->id }})">
                                    <i class="ti ti-corner-down-right"></i> Balas Komentar
                                </button>
                            </div>

                            {{-- Nested Replies --}}
                            @if($disc->replies && $disc->replies->count() > 0)
                                <div class="d-flex flex-column gap-2 mt-2 ps-3 border-start" style="border-width:2px !important;border-color:var(--tblr-border-color,#e2e8f0) !important;">
                                    @foreach($disc->replies as $reply)
                                        @php
                                            $isTeacherReply = $reply->user && $reply->user->isGuru();
                                        @endphp
                                        <div class="p-2.5 rounded-3" style="background:{{ $isTeacherReply ? 'rgba(32,107,196,.06)' : 'var(--tblr-body-bg,#f8fafc)' }};border:1px solid var(--tblr-border-color,#e2e8f0);" id="discussion-item-{{ $reply->id }}">
                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar text-white rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.65rem;background:{{ $isTeacherReply ? '#206bc4' : '#64748b' }};">
                                                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                            <span class="fw-bold" style="font-size:.78rem;color:{{ $isTeacherReply ? '#206bc4' : 'var(--tblr-heading-color,#0f172a)' }};">{{ $reply->user->name ?? 'Pengguna' }}</span>
                                                            @if($isTeacherReply)
                                                                <span class="md-badge blue" style="font-size:.58rem;padding:.08rem .35rem;">Guru Pengampu</span>
                                                            @else
                                                                <span class="md-badge teal" style="font-size:.58rem;padding:.08rem .35rem;">Siswa</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted" style="font-size:.65rem;">
                                                            <i class="ti ti-clock"></i> {{ $reply->created_at ? $reply->created_at->diffForHumans() : '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="md-icon-btn red" style="width:22px;height:22px;font-size:.7rem;" onclick="openDeleteCommentModal('{{ route('admin.materials.discussions.destroy', [$material, $reply]) }}')" title="Hapus Balasan">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>

                                            <div style="font-size:.8rem;line-height:1.5;color:var(--tblr-body-color);">
                                                {!! nl2br(e($reply->comment)) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Inline Reply Form --}}
                            <div id="reply-form-{{ $disc->id }}" class="mt-2 p-2.5 rounded-3 d-none" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                                <form action="{{ route('admin.materials.discussions', $material) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $disc->id }}">
                                    
                                    <div class="d-flex align-items-center gap-1.5 mb-1.5" style="font-size:.72rem;color:var(--tblr-text-muted,#64748b);">
                                        <i class="ti ti-corner-down-right text-primary"></i>
                                        <span>Balas ke: <strong style="color:var(--tblr-heading-color,#0f172a);">{{ $disc->user->name ?? 'Siswa' }}</strong></span>
                                    </div>

                                    <div class="mb-2">
                                        <textarea name="comment" class="form-control" rows="2" placeholder="Ketik balasan untuk {{ $disc->user->name ?? 'siswa' }}..." required style="font-size:.8rem;resize:vertical;">{{ '@' . ($disc->user->name ?? 'Siswa') }} </textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-1.5">
                                        <button type="button" class="md-btn-light" style="padding:.2rem .6rem;font-size:.72rem;" onclick="toggleReplyForm({{ $disc->id }})">
                                            Batal
                                        </button>
                                        <button type="submit" class="md-btn-primary" style="padding:.2rem .75rem;font-size:.72rem;">
                                            <i class="ti ti-send"></i> Balas Siswa
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="md-empty-row py-4">
                            <i class="ti ti-messages-off"></i>
                            Belum ada pesan diskusi pada materi ini.
                            <div class="text-muted mt-1" style="font-size:.75rem;">Siswa dapat bertanya dan Anda dapat menanggapi secara langsung di forum ini.</div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Delete Comment Modal --}}
<div class="modal fade" id="modalDeleteComment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Komentar Ini?</h6>
            <p class="md-modal-text">Komentar beserta balasannya akan dihapus permanen dari ruang diskusi.</p>
            <form id="deleteCommentForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="md-modal-actions">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="md-btn-danger"><i class="ti ti-trash"></i> Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')

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
        new bootstrap.Modal(document.getElementById('modalDeleteComment')).show();
    }
}
</script>
@endsection
