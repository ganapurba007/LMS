@extends('layouts.be.master')
@section('header_title', 'Master Data — Materi Pembelajaran')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Materi Pembelajaran</h5>
        </div>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Materi</span>
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
        <i class="ti ti-alert-triangle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

{{-- Filter & Search Bar --}}
<div class="md-card mb-4 p-3">
    <form method="GET" action="{{ route('admin.materials.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari judul materi..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="subject_id" class="form-select form-select-sm select2" onchange="this.form.submit()">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($subjects as $subj)
                    <option value="{{ $subj->id }}" {{ (string)$subjectId === (string)$subj->id ? 'selected' : '' }}>
                        {{ $subj->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="class_id" class="form-select form-select-sm select2" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" {{ (string)$classId === (string)$cls->id ? 'selected' : '' }}>
                        {{ $cls->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="md-btn-primary w-100 justify-content-center" style="padding:.35rem .6rem;font-size:.78rem;">
                <i class="ti ti-filter"></i> Filter
            </button>
            @if($search || $subjectId || $classId)
                <a href="{{ route('admin.materials.index') }}" class="md-btn-secondary" style="padding:.35rem .6rem;font-size:.78rem;" title="Reset Filter">
                    <i class="ti ti-rotate-clockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Card Table --}}
<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0">
            <thead>
                <tr>
                    <th class="md-th-no">No</th>
                    <th>Judul &amp; Informasi Materi</th>
                    <th class="d-none d-md-table-cell">Mata Pelajaran</th>
                    <th class="d-none d-sm-table-cell">Kelas</th>
                    <th class="d-none d-lg-table-cell">Media</th>
                    <th class="d-none d-xl-table-cell">Instruktur</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $material)
                    @php
                        $no = ($materials->currentPage() - 1) * $materials->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no">{{ $no }}</td>

                        {{-- Judul & Informasi --}}
                        <td>
                            <a href="{{ route('admin.materials.show', $material) }}" class="fw-bold text-decoration-none d-block mb-1" style="color:var(--tblr-heading-color,#0f172a);font-size:.84rem;">
                                {{ $material->title }}
                            </a>
                            <div class="d-flex align-items-center flex-wrap gap-1.5" style="font-size:.73rem;">
                                <span class="text-muted">Urutan: #{{ $material->order }}</span>
                                <span class="text-muted">•</span>
                                @if($material->discussions_count > 0)
                                    <a href="{{ route('admin.materials.show', $material) }}#discussion-card" class="md-badge blue text-decoration-none">
                                        <i class="ti ti-messages"></i> {{ $material->discussions_count }} Diskusi
                                    </a>
                                @else
                                    <span class="md-badge" style="background:rgba(100,116,139,.1);color:#64748b;">
                                        <i class="ti ti-message"></i> 0 Diskusi
                                    </span>
                                @endif

                                {{-- Responsive Mobile Badges --}}
                                <div class="d-md-none mt-1 w-100 d-flex flex-wrap gap-1">
                                    <span class="md-badge blue">{{ $material->subject->name ?? '-' }}</span>
                                    <span class="md-badge teal">{{ $material->schoolClass->name ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Mata Pelajaran --}}
                        <td class="d-none d-md-table-cell">
                            <span class="md-badge blue">
                                {{ $material->subject->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Kelas --}}
                        <td class="d-none d-sm-table-cell">
                            <span class="md-badge teal">
                                {{ $material->schoolClass->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Media --}}
                        <td class="d-none d-lg-table-cell">
                            <div class="d-flex flex-wrap gap-1">
                                @if($material->content)
                                    <span class="md-badge teal" title="Isi Teks / Artikel"><i class="ti ti-file-text"></i> Teks</span>
                                @endif
                                @if($material->video_url)
                                    <span class="md-badge rose" title="Video YouTube"><i class="ti ti-brand-youtube"></i> Video</span>
                                @endif
                                @if($material->document_path)
                                    <span class="md-badge amber" title="File Dokumen Lampiran"><i class="ti ti-file-download"></i> Dokumen</span>
                                @endif
                                @if(!$material->content && !$material->video_url && !$material->document_path)
                                    <span class="text-muted" style="font-size:.75rem;">-</span>
                                @endif
                            </div>
                        </td>

                        {{-- Instruktur --}}
                        <td class="d-none d-xl-table-cell">
                            <span style="font-size:.78rem;font-weight:600;color:var(--tblr-heading-color,#0f172a);">
                                {{ $material->instructor->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.materials.show', $material) }}" class="md-icon-btn teal" title="Lihat Detail & Ruang Diskusi">
                                    <i class="ti ti-messages"></i>
                                </a>
                                <a href="{{ route('admin.materials.edit', $material) }}" class="md-icon-btn blue" title="Edit Materi">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="md-icon-btn red" title="Hapus Materi"
                                    onclick="openDeleteMaterialModal('{{ route('admin.materials.destroy', $material) }}', '{{ addslashes($material->title) }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="md-empty-row">
                            <i class="ti ti-books-off"></i>
                            Materi pembelajaran belum tersedia.
                            <a href="{{ route('admin.materials.create') }}" class="md-btn-primary mt-2" style="font-size:.75rem;padding:.35rem .8rem;">
                                <i class="ti ti-plus"></i> Tambah Materi Pertama
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($materials->hasPages())
        <div class="md-card-footer">
            {{ $materials->links() }}
        </div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Materi Ini?</h6>
            <p class="md-modal-text" id="deleteModalMaterialText">Materi dan lampiran file yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteMaterialForm" method="POST" action="">
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
function openDeleteMaterialModal(url, title) {
    document.getElementById('deleteMaterialForm').action = url;
    document.getElementById('deleteModalMaterialText').innerText = `Anda akan menghapus materi: "${title}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteMaterial')).show();
}
</script>
@endsection