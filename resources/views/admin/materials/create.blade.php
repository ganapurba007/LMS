@extends('layouts.be.master')
@section('header_title', 'Pembelajaran — Terbitkan Materi ke Kelas')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Terbitkan Materi ke Kelas</h5>
            <div class="text-muted small">Pilih materi dari Bank Materi dan tentukan kelas penerima.</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.material-banks.create') }}" class="md-btn-primary" style="background:#0ca678;border-color:#0ca678;">
            <i class="ti ti-plus"></i> <span>Buat Master di Bank Materi</span>
        </a>
        <a href="{{ route('admin.materials.index') }}" class="md-btn-secondary">
            <i class="ti ti-arrow-left"></i> <span>Kembali</span>
        </a>
    </div>
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
            <i class="ti ti-send"></i>
        </div>
        <h6 class="md-form-head-title mb-0">Form Terbit Materi</h6>
    </div>

    <form action="{{ route('admin.materials.store') }}" method="POST">
        @csrf

        <div class="md-form-body">
            @if(!isset($materialBanks) || $materialBanks->count() === 0)
                <div class="alert alert-warning py-3 px-4 mb-4 rounded-3 d-flex align-items-center gap-3">
                    <div style="font-size:1.8rem;"><i class="ti ti-alert-circle"></i></div>
                    <div>
                        <div class="fw-bold mb-1">Bank Materi Masih Kosong</div>
                        <div class="small mb-2">Anda belum memiliki master data materi di Bank Materi. Silakan buat master materi terlebih dahulu.</div>
                        <a href="{{ route('admin.material-banks.create') }}" class="btn btn-sm btn-warning fw-bold">
                            <i class="ti ti-plus me-1"></i> Buat Master di Bank Materi
                        </a>
                    </div>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <!-- Dropdown 1: Pilih Materi dari Bank Materi -->
                <div class="col-12 col-md-6">
                    <label for="material_bank_id" class="md-form-label fw-bold text-primary">
                        <i class="ti ti-books me-1"></i> Pilih Materi (Bank Materi) <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2 @error('material_bank_id') is-invalid @enderror" id="material_bank_id" name="material_bank_id" required>
                        <option value="">-- Pilih Materi dari Bank Materi --</option>
                        @if(isset($materialBanks))
                            @foreach($materialBanks as $mb)
                                <option value="{{ $mb->id }}" {{ old('material_bank_id') == $mb->id ? 'selected' : '' }}>
                                    {{ $mb->title }} {{ $mb->subject ? '('.$mb->subject->name.')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('material_bank_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dropdown 2: Pilih Kelas Target -->
                <div class="col-12 col-md-6">
                    <label for="class_id" class="md-form-label fw-bold text-primary">
                        <i class="ti ti-school me-1"></i> Pilih Kelas Target <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2 @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                        <option value="">-- Pilih Kelas Penerima --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Urutan Tampil (Opsional) -->
            <div class="mb-4">
                <label for="order" class="md-form-label">Urutan Tampil (Opsional)</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0" placeholder="0">
                <div class="md-form-hint">Materi dengan nomor urut lebih kecil akan berada di bagian atas list materi siswa.</div>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Preview Data Materi Terpilih -->
            <div id="mbPreviewCard" class="p-3 rounded-3 d-none mb-3" style="background:var(--tblr-body-bg,#f8fafc);border:1px solid var(--tblr-border-color,#e2e8f0);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;width:28px;height:28px;font-size:.8rem;">
                        <i class="ti ti-check"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="font-size:.85rem;color:var(--tblr-heading-color,#0f172a);">Ringkasan Materi Terpilih</h6>
                </div>
                <div class="p-3 rounded border" style="background:var(--tblr-card-bg,#ffffff);border-color:var(--tblr-border-color,#e2e8f0) !important;font-size:0.85rem;">
                    <div class="fw-bold mb-1" style="color:var(--tblr-heading-color,#0f172a);font-size:0.92rem;" id="pvTitle">-</div>
                    <div class="mb-2" style="color:var(--tblr-text-muted,#64748b);" id="pvSubject"><i class="ti ti-book me-1"></i> -</div>
                    <div class="d-flex flex-wrap gap-2" id="pvBadges"></div>
                </div>
            </div>
        </div>

        <div class="md-form-footer">
            <a href="{{ route('admin.materials.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit">
                <i class="ti ti-send me-1"></i> Terbitkan Materi ke Kelas
            </button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    $(function () {
        function updateMaterialPreview(mbId) {
            if (!mbId) {
                $('#mbPreviewCard').addClass('d-none');
                return;
            }

            $.ajax({
                url: '/admin/material-banks/' + mbId + '/json',
                type: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: function (data) {
                    $('#pvTitle').text(data.title || '-');
                    $('#pvSubject').html('<i class="ti ti-book me-1"></i> Mata Pelajaran Master');
                    
                    var badgesHtml = '';
                    if (data.video_url) {
                        badgesHtml += '<span class="badge bg-danger-subtle text-danger"><i class="ti ti-brand-youtube me-1"></i> Video YouTube</span>';
                    }
                    if (data.document_path) {
                        badgesHtml += '<span class="badge bg-warning-subtle text-warning"><i class="ti ti-file-text me-1"></i> File Lampiran (' + (data.document_filename || 'Dokumen') + ')</span>';
                    }
                    if (data.content) {
                        badgesHtml += '<span class="badge bg-info-subtle text-info"><i class="ti ti-article me-1"></i> Artikel Teks</span>';
                    }
                    $('#pvBadges').html(badgesHtml);
                    $('#mbPreviewCard').removeClass('d-none');
                },
                error: function () {
                    $('#mbPreviewCard').addClass('d-none');
                }
            });
        }

        $('#material_bank_id').on('change', function () {
            updateMaterialPreview($(this).val());
        });

        if ($('#material_bank_id').val()) {
            updateMaterialPreview($('#material_bank_id').val());
        }
    });
</script>
@endpush
