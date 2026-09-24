@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Materi')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Materi Pembelajaran</h5>
        </div>
    </div>
    <a href="{{ route('admin.material-banks.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Master Materi</span>
    </a>
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
        <i class="ti ti-alert-circle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

{{-- Filter Form --}}
<div class="md-card mb-4 p-3">
    <form method="GET" action="{{ route('admin.material-banks.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari judul materi..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="subject_id" class="form-select select2" onchange="this.form.submit()">
                <option value="">-- Semua Mata Pelajaran --</option>
                @foreach($subjects as $subj)
                    <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                        {{ $subj->name }} ({{ $subj->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="md-btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
            @if(request('search') || request('subject_id'))
                <a href="{{ route('admin.material-banks.index') }}" class="md-btn-secondary" title="Reset Filter"><i class="ti ti-refresh"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Judul Materi</th>
                    <th>Mata Pelajaran</th>
                    <th class="d-none d-md-table-cell">Lampiran / Media</th>
                    <th class="d-none d-lg-table-cell">Tanggal Buat</th>
                    <th class="md-th-action text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materialBanks as $mb)
                    @php
                        $no = ($materialBanks->currentPage() - 1) * $materialBanks->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no text-center">{{ $no }}</td>
                        <td>
                            <a href="javascript:void(0)" class="fw-bold heading-custom text-decoration-none btn-preview-mb"
                               style="color: var(--tblr-heading-color, inherit);"
                               data-id="{{ $mb->id }}"
                               data-title="{{ $mb->title }}"
                               data-subject="{{ $mb->subject ? $mb->subject->name : 'Umum / Semua Mata Pelajaran' }}"
                               data-type="{{ $mb->content_type }}"
                               data-doc="{{ $mb->document_path ? asset('storage/' . $mb->document_path) : '' }}"
                               data-video="{{ $mb->video_url ?? '' }}"
                               data-edit="{{ route('admin.material-banks.edit', $mb) }}">
                                {{ $mb->title }}
                            </a>
                        </td>
                        <td>
                            @if($mb->subject)
                                <span class="md-badge blue"><i class="ti ti-book"></i> {{ $mb->subject->name }}</span>
                            @else
                                <span class="md-badge muted">Umum / Semua</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            <!-- Raw content storage for reliable preview rendering -->
                            <div class="d-none" id="mb-content-raw-{{ $mb->id }}">{!! $mb->content !!}</div>
                            
                            @if($mb->document_path)
                                @php
                                    $docExt = strtolower(pathinfo($mb->document_path, PATHINFO_EXTENSION));
                                    $docIcon = 'ti-file-text text-primary';
                                    if ($docExt === 'pdf') $docIcon = 'ti-file-type-pdf text-danger';
                                    elseif (in_array($docExt, ['doc', 'docx'])) $docIcon = 'ti-file-type-doc text-primary';
                                    elseif (in_array($docExt, ['xls', 'xlsx'])) $docIcon = 'ti-file-spreadsheet text-success';
                                    elseif (in_array($docExt, ['ppt', 'pptx'])) $docIcon = 'ti-presentation text-warning';
                                    elseif (in_array($docExt, ['jpg', 'jpeg', 'png', 'webp'])) $docIcon = 'ti-photo text-info';
                                @endphp
                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 btn-preview-mb d-inline-flex align-items-center gap-1.5"
                                        style="font-size:0.75rem;"
                                        title="Pratinjau File"
                                        data-id="{{ $mb->id }}"
                                        data-title="{{ $mb->title }}"
                                        data-subject="{{ $mb->subject ? $mb->subject->name : 'Umum / Semua Mata Pelajaran' }}"
                                        data-type="{{ $mb->content_type }}"
                                        data-doc="{{ asset('storage/' . $mb->document_path) }}"
                                        data-video="{{ $mb->video_url ?? '' }}"
                                        data-edit="{{ route('admin.material-banks.edit', $mb) }}">
                                    <i class="ti {{ $docIcon }} flex-shrink-0" style="font-size:0.95rem;"></i>
                                    <span>Lihat File</span>
                                </button>
                            @elseif($mb->video_url)
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5 btn-preview-mb d-inline-flex align-items-center gap-1.5"
                                        style="font-size:0.75rem;"
                                        title="Putar Video YouTube"
                                        data-id="{{ $mb->id }}"
                                        data-title="{{ $mb->title }}"
                                        data-subject="{{ $mb->subject ? $mb->subject->name : 'Umum / Semua Mata Pelajaran' }}"
                                        data-type="{{ $mb->content_type }}"
                                        data-doc=""
                                        data-video="{{ $mb->video_url }}"
                                        data-edit="{{ route('admin.material-banks.edit', $mb) }}">
                                    <i class="ti ti-brand-youtube flex-shrink-0"></i>
                                    <span>Putar Video</span>
                                </button>
                            @elseif($mb->content)
                                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5 btn-preview-mb d-inline-flex align-items-center gap-1.5"
                                        style="font-size:0.75rem;"
                                        title="Baca Konten Teks"
                                        data-id="{{ $mb->id }}"
                                        data-title="{{ $mb->title }}"
                                        data-subject="{{ $mb->subject ? $mb->subject->name : 'Umum / Semua Mata Pelajaran' }}"
                                        data-type="{{ $mb->content_type }}"
                                        data-doc=""
                                        data-video=""
                                        data-edit="{{ route('admin.material-banks.edit', $mb) }}">
                                    <i class="ti ti-article flex-shrink-0"></i>
                                    <span>Baca Teks</span>
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="text-muted small">{{ $mb->created_at ? $mb->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </td>
                        <td class="md-td-action text-center">
                            <div class="md-action-group justify-content-center">
                                <a href="{{ route('admin.material-banks.edit', $mb) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="md-icon-btn red btn-delete-mb" 
                                        data-url="{{ route('admin.material-banks.destroy', $mb) }}" 
                                        data-title="{{ $mb->title }}" 
                                        title="Hapus">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row text-center py-5">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap blue mb-3" style="width:60px;height:60px;margin:0 auto;display:flex;align-items:center;justify-content:center;background:rgba(32,107,196,.1);color:#206bc4;border-radius:50%;font-size:1.5rem;">
                                    <i class="ti ti-books"></i>
                                </div>
                                <h6 class="md-empty-title fw-bold">Belum Ada Master Bank Materi</h6>
                                <p class="md-empty-desc text-muted small">
                                    @if(request('search') || request('subject_id'))
                                        Tidak ada materi yang sesuai dengan pencarian/filter Anda.
                                     @else
                                        Tambahkan master materi ke Bank Materi agar dapat digunakan kembali secara fleksibel di berbagai kelas.
                                    @endif
                                </p>
                                <a href="{{ route('admin.material-banks.create') }}" class="md-btn-primary mt-2">
                                    <i class="ti ti-plus"></i> Tambah Master Materi
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($materialBanks->hasPages())
        <div class="md-card-footer p-3">{{ $materialBanks->links() }}</div>
    @endif
</div>

<!-- Modal Pratinjau Master Bank Materi -->
<div class="modal fade" id="modalPreviewMB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="background: var(--tblr-bg-surface, #ffffff); border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4" style="background: var(--tblr-bg-surface-secondary, #f8fafc); border-color: var(--tblr-border-color, #e2e8f0) !important;">
                <div class="d-flex align-items-center gap-2.5 text-truncate" style="min-width: 0;">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(32, 107, 196, 0.12); color: #206bc4; width: 38px; height: 38px;">
                        <i class="ti ti-books" style="font-size: 1.2rem;"></i>
                    </div>
                    <div class="text-truncate" style="min-width: 0;">
                        <h6 class="modal-title fw-bold text-dark text-truncate mb-0" id="previewMbTitle" style="font-size: 0.95rem;">Pratinjau Materi</h6>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4" id="previewMbBody">
                <!-- Video Container -->
                <div id="previewMbVideoWrap" class="mb-3 d-none">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-xs border" style="border-color: var(--tblr-border-color, #e2e8f0) !important;">
                        <iframe id="previewMbVideoIframe" src="" title="Video Materi" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Document Container -->
                <div id="previewMbDocWrap" class="mb-3 d-none">
                    <!-- PDF Viewer -->
                    <div id="previewMbPdfWrap" class="d-none">
                        <iframe id="previewMbPdfIframe" src="" style="width: 100%; height: 500px; border: 1px solid var(--tblr-border-color, #e2e8f0); border-radius: 8px;"></iframe>
                    </div>
                    <!-- Image Viewer -->
                    <div id="previewMbImgWrap" class="text-center d-none">
                        <img id="previewMbImgEl" src="" alt="Pratinjau Gambar" class="img-fluid rounded-3 border shadow-xs" style="max-height: 480px; border-color: var(--tblr-border-color, #e2e8f0) !important;">
                    </div>
                    <!-- Office Docs Fallback (Word/Excel/PPT) -->
                    <div id="previewMbOfficeWrap" class="text-center p-4 rounded-3 border d-none" style="background: var(--tblr-bg-surface-secondary, #f8fafc); border-color: var(--tblr-border-color, #e2e8f0) !important;">
                        <i class="ti ti-file-text text-primary mb-2" style="font-size: 2.5rem; display: inline-block;"></i>
                        <h6 class="fw-bold mb-1 text-dark">File Dokumen Lampiran</h6>
                        <p class="text-muted small mb-3">Dokumen ini siap diunduh untuk melihat isinya secara lengkap.</p>
                        <a id="previewMbOfficeDownloadBtn" href="" download class="btn btn-sm btn-primary">
                            <i class="ti ti-download me-1"></i> Unduh File Ini
                        </a>
                    </div>
                </div>

                <!-- Content Text Container (Terlihat Jelas di Mode Dark & Light) -->
                <div id="previewMbContentWrap" class="d-none">
                    <div class="p-3 p-md-4 rounded-3 border preview-material-content article-body tinymce-content" id="previewMbContent" style="line-height: 1.7; font-size: 0.9rem;">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between" style="border-color: var(--tblr-border-color, #e2e8f0) !important; background: var(--tblr-bg-surface-secondary, #f8fafc);">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a id="previewMbEditBtn" href="" class="btn btn-primary btn-sm">
                    <i class="ti ti-edit me-1"></i> Edit Master Materi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Bank Materi -->
<div class="modal fade" id="modalDeleteMB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content p-3 text-center">
            <div class="md-modal-icon danger mb-3" style="background:rgba(225,29,72,.1);color:#e11d48;width:52px;height:52px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                <i class="ti ti-trash"></i>
            </div>
            <h6 class="md-modal-title fw-bold">Hapus Master Materi?</h6>
            <p class="md-modal-text mb-3 text-muted small">
                Apakah Anda yakin ingin menghapus <strong id="deleteTargetTitle"></strong> dari Bank Materi?
            </p>

            <form id="formDeleteMB" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="md-btn-danger">
                        <i class="ti ti-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.preview-material-content {
    background: #ffffff !important;
    color: #1e293b !important;
    border-color: #e2e8f0 !important;
}
.preview-material-content,
.preview-material-content *,
.preview-material-content p,
.preview-material-content span,
.preview-material-content div,
.preview-material-content li,
.preview-material-content h1,
.preview-material-content h2,
.preview-material-content h3,
.preview-material-content h4,
.preview-material-content h5,
.preview-material-content h6 {
    color: #1e293b !important;
}
.preview-material-content a {
    color: #206bc4 !important;
}

[data-bs-theme="dark"] .preview-material-content,
[data-theme="dark"] .preview-material-content,
body.theme-dark .preview-material-content,
body.dark-mode .preview-material-content {
    background: #182433 !important;
    color: #f8fafc !important;
    border-color: #2d3f53 !important;
}
[data-bs-theme="dark"] .preview-material-content *,
[data-theme="dark"] .preview-material-content *,
body.theme-dark .preview-material-content *,
body.dark-mode .preview-material-content * {
    color: #f8fafc !important;
    background-color: transparent !important;
}
[data-bs-theme="dark"] .preview-material-content a,
[data-theme="dark"] .preview-material-content a,
body.theme-dark .preview-material-content a,
body.dark-mode .preview-material-content a {
    color: #60a5fa !important;
}
</style>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    $(function () {
        // 1. Delete Modal Handler
        var deleteModalEl = document.getElementById('modalDeleteMB');
        var formDeleteMB = document.getElementById('formDeleteMB');
        var targetTitleEl = document.getElementById('deleteTargetTitle');

        $(document).on('click', '.btn-delete-mb', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var title = $(this).data('title') || '';

            if (formDeleteMB) formDeleteMB.action = url;
            if (targetTitleEl) targetTitleEl.textContent = title;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $(deleteModalEl).modal('show');
            }
        });

        // 2. In-Page Preview Modal Handler
        var previewModalEl = document.getElementById('modalPreviewMB');
        var previewTitle = document.getElementById('previewMbTitle');
        var previewEditBtn = document.getElementById('previewMbEditBtn');

        var videoWrap = document.getElementById('previewMbVideoWrap');
        var videoIframe = document.getElementById('previewMbVideoIframe');

        var docWrap = document.getElementById('previewMbDocWrap');
        var docPdfWrap = document.getElementById('previewMbPdfWrap');
        var docPdfIframe = document.getElementById('previewMbPdfIframe');
        var docImgWrap = document.getElementById('previewMbImgWrap');
        var docImgEl = document.getElementById('previewMbImgEl');
        var docOfficeWrap = document.getElementById('previewMbOfficeWrap');
        var docOfficeDownloadBtn = document.getElementById('previewMbOfficeDownloadBtn');

        var contentWrap = document.getElementById('previewMbContentWrap');
        var contentEl = document.getElementById('previewMbContent');

        function parseYouTubeEmbed(url) {
            if (!url) return '';
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            return (match && match[2].length === 11) ? 'https://www.youtube.com/embed/' + match[2] : url;
        }

        $(document).on('click', '.btn-preview-mb', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var mbId = $btn.data('id') || $btn.attr('data-id');
            var title = $btn.data('title') || $btn.attr('data-title') || 'Pratinjau Materi';
            var videoUrl = $btn.data('video') || $btn.attr('data-video') || '';
            var docUrl = $btn.data('doc') || $btn.attr('data-doc') || '';
            var editUrl = $btn.data('edit') || $btn.attr('data-edit') || '';

            // Ambil konten teks dari elemen DOM tersembunyi
            var rawEl = mbId ? document.getElementById('mb-content-raw-' + mbId) : null;
            var rawContent = rawEl ? rawEl.innerHTML.trim() : '';

            if (previewTitle) previewTitle.textContent = title;

            if (previewEditBtn) {
                if (editUrl) {
                    previewEditBtn.href = editUrl;
                    previewEditBtn.classList.remove('d-none');
                } else {
                    previewEditBtn.classList.add('d-none');
                }
            }

            // Reset view state
            if (videoWrap) videoWrap.classList.add('d-none');
            if (videoIframe) videoIframe.src = '';
            if (docWrap) docWrap.classList.add('d-none');
            if (docPdfWrap) docPdfWrap.classList.add('d-none');
            if (docPdfIframe) docPdfIframe.src = '';
            if (docImgWrap) docImgWrap.classList.add('d-none');
            if (docImgEl) docImgEl.src = '';
            if (docOfficeWrap) docOfficeWrap.classList.add('d-none');
            if (contentWrap) contentWrap.classList.add('d-none');
            if (contentEl) contentEl.innerHTML = '';

            // Document (hanya tampilkan preview file jika ada dokumen)
            if (docUrl) {
                if (docWrap) docWrap.classList.remove('d-none');

                var ext = docUrl.split('.').pop().toLowerCase().split('?')[0];
                if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                    if (docImgEl) docImgEl.src = docUrl;
                    if (docImgWrap) docImgWrap.classList.remove('d-none');
                } else if (ext === 'pdf') {
                    if (docPdfIframe) docPdfIframe.src = docUrl;
                    if (docPdfWrap) docPdfWrap.classList.remove('d-none');
                } else {
                    if (docOfficeDownloadBtn) docOfficeDownloadBtn.href = docUrl;
                    if (docOfficeWrap) docOfficeWrap.classList.remove('d-none');
                }
            } else if (videoUrl) {
                // Video YouTube
                if (videoIframe) videoIframe.src = parseYouTubeEmbed(videoUrl);
                if (videoWrap) videoWrap.classList.remove('d-none');
            } else if (rawContent && rawContent !== '') {
                // Text content
                if (contentEl) contentEl.innerHTML = rawContent;
                if (contentWrap) contentWrap.classList.remove('d-none');
            }

            if (previewModalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(previewModalEl).show();
                } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                    $(previewModalEl).modal('show');
                }
            }
        });

        // Clear video & iframe on modal close so audio doesn't keep playing
        if (previewModalEl) {
            previewModalEl.addEventListener('hidden.bs.modal', function () {
                if (videoIframe) videoIframe.src = '';
                if (docPdfIframe) docPdfIframe.src = '';
            });
        }
    });
</script>
@endpush

