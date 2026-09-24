@extends('layouts.be.master')
@section('header_title', 'Master Data — Edit Bank Materi')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Master Materi</h5>
        </div>
    </div>
    <a href="{{ route('admin.material-banks.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> <span>Kembali</span>
    </a>
</div>

{{-- Flash Alert --}}
@if($errors->any())
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-triangle"></i> Terjadi kesalahan pada form pengisian. Silakan periksa kolom yang ditandai.
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-file-text"></i>
        </div>
        <h6 class="md-form-head-title mb-0">Informasi Master Bank Materi</h6>
    </div>

    <form action="{{ route('admin.material-banks.update', $materialBank) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="md-form-body">
            <!-- Judul Materi -->
            <div class="mb-3">
                <label for="title" class="md-form-label">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $materialBank->title) }}" required placeholder="Misal: Modul Pembelajaran Pemrograman Web Lanjut">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mapel -->
            <div class="mb-4">
                <label for="subject_id" class="md-form-label">Mata Pelajaran (Opsional)</label>
                <select class="form-select select2 @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
                    <option value="">-- Pilih Mata Pelajaran (Umum) --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $materialBank->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }})
                        </option>
                    @endforeach
                </select>
                <div class="md-form-hint">Dapat dikosongkan jika materi bersifat umum untuk berbagai mata pelajaran.</div>
                @error('subject_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="p-3 mb-4 rounded-3" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;width:30px;height:30px;font-size:.85rem;">
                        <i class="ti ti-layers-intersect"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="font-size:.85rem;color:var(--tblr-heading-color,#0f172a);">Konten &amp; Media Master Materi</h6>
                </div>

                <!-- Teks / Artikel -->
                <div class="mb-3">
                    <label class="md-form-label">Teks / Artikel Materi</label>
                    <textarea class="form-control tinymce @error('content') is-invalid @enderror" id="content" name="content" rows="6" placeholder="Tuliskan isi materi master di sini...">{{ old('content', $materialBank->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <!-- YouTube Video -->
                    <div class="col-12 col-md-6">
                        <label for="video_url" class="md-form-label"><i class="ti ti-brand-youtube text-danger me-1"></i> URL Video YouTube (Opsional)</label>
                        <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" name="video_url" value="{{ old('video_url', $materialBank->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                        @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dokumen File -->
                    <div class="col-12 col-md-6">
                        <label for="document_file" class="md-form-label"><i class="ti ti-file-download text-warning me-1"></i> File Dokumen Lampiran (Maks 50MB)</label>
                        <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.png,.jpg,.jpeg">
                        
                        @if($materialBank->document_path)
                            @php
                                $ext = strtolower(pathinfo($materialBank->document_path, PATHINFO_EXTENSION));
                                $isPdf = $ext === 'pdf';
                                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
                                $canPreview = $isPdf || $isImage;
                            @endphp
                            <div class="mt-2 d-flex align-items-center gap-2">
                                @if($canPreview)
                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 btn-preview-current-file d-inline-flex align-items-center gap-1" 
                                            data-url="{{ asset('storage/' . $materialBank->document_path) }}" 
                                            data-name="{{ basename($materialBank->document_path) }}" 
                                            data-type="{{ $isPdf ? 'pdf' : 'image' }}"
                                            style="font-size: 0.75rem;">
                                        <i class="ti ti-eye"></i> Pratinjau
                                    </button>
                                @endif
                                <a href="{{ asset('storage/' . $materialBank->document_path) }}" download class="btn btn-sm btn-primary py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="ti ti-download"></i> Unduh
                                </a>
                            </div>
                        @endif
                        <div class="md-form-hint mt-1.5 text-muted small">Upload file baru jika ingin mengganti dokumen lampiran (PDF, DOCX, PPTX, XLSX, PNG, JPG).</div>
                        @error('document_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.material-banks.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-device-floppy"></i> Perbarui Master Materi
            </button>
        </div>
    </form>
</div>

<!-- Modal Pratinjau Dokumen Saat Ini -->
<div class="modal fade" id="modalPreviewDocEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="background: var(--tblr-bg-surface, #ffffff); border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4" style="background: var(--tblr-bg-surface-secondary, #f8fafc); border-color: var(--tblr-border-color, #e2e8f0) !important;">
                <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(32, 107, 196, 0.12); color: #206bc4; width: 36px; height: 36px;">
                        <i class="ti ti-file-text" style="font-size: 1.1rem;"></i>
                    </div>
                    <div class="text-truncate" style="min-width: 0;">
                        <h6 class="modal-title fw-bold text-dark text-truncate mb-0" style="font-size: 0.92rem;">Pratinjau Dokumen Lampiran</h6>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div id="previewEditPdfWrap" class="d-none">
                    <iframe id="previewEditPdfIframe" src="" style="width: 100%; height: 500px; border: 1px solid var(--tblr-border-color, #e2e8f0); border-radius: 8px;"></iframe>
                </div>
                <div id="previewEditImgWrap" class="text-center d-none">
                    <img id="previewEditImgEl" src="" alt="Pratinjau Gambar" class="img-fluid rounded-3 border shadow-xs" style="max-height: 500px; object-fit: contain; border-color: var(--tblr-border-color, #e2e8f0) !important;">
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between" style="border-color: var(--tblr-border-color, #e2e8f0) !important; background: var(--tblr-bg-surface-secondary, #f8fafc);">
                <a id="previewEditDocDownload" href="" download class="btn btn-sm btn-primary">
                    <i class="ti ti-download me-1"></i> Unduh File
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script src="{{ asset('js/chunked-uploader.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initChunkedFileInput({
        input: '#document_file',
        targetFolder: 'material-banks',
        hiddenInputName: 'document_chunk_path',
        originalNameInputName: 'original_filename',
        uploadUrl: '{{ route('admin.upload.chunk') }}',
        cancelUrl: '{{ route('admin.upload.chunk.cancel') }}'
    });

    // Preview File Saat Ini
    var modalDocEl = document.getElementById('modalPreviewDocEdit');
    if (modalDocEl) {
        var modalDoc = new bootstrap.Modal(modalDocEl);
        document.querySelectorAll('.btn-preview-current-file').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var fileUrl = this.getAttribute('data-url');
                var fileType = this.getAttribute('data-type');

                document.getElementById('previewEditDocDownload').href = fileUrl;

                var pdfWrap = document.getElementById('previewEditPdfWrap');
                var imgWrap = document.getElementById('previewEditImgWrap');
                var pdfIframe = document.getElementById('previewEditPdfIframe');
                var imgEl = document.getElementById('previewEditImgEl');

                pdfWrap.classList.add('d-none');
                imgWrap.classList.add('d-none');
                pdfIframe.src = '';
                imgEl.src = '';

                if (fileType === 'pdf') {
                    pdfIframe.src = fileUrl;
                    pdfWrap.classList.remove('d-none');
                } else if (fileType === 'image') {
                    imgEl.src = fileUrl;
                    imgWrap.classList.remove('d-none');
                }

                modalDoc.show();
            });
        });

        modalDocEl.addEventListener('hidden.bs.modal', function() {
            var pdfIframe = document.getElementById('previewEditPdfIframe');
            var imgEl = document.getElementById('previewEditImgEl');
            if (pdfIframe) pdfIframe.src = '';
            if (imgEl) imgEl.src = '';
        });
    }
});
</script>
@endpush
