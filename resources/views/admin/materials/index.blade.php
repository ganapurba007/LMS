@extends('layouts.be.master')

@section('header_title', 'Master Data — Materi Pembelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Materi Pembelajaran Kelas</h3>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah Materi Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="ti ti-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table w-100 mb-0 data-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 70px;">No</th>
                        <th>Judul Materi</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas Target</th>
                        <th>Tipe Konten</th>
                        <th>Instruktur</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $material->title }}</div>
                                <div class="small text-muted">Urutan: {{ $material->order }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $material->subject->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $material->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($material->content)
                                        <span class="badge badge-soft-success" title="Ada Isi Teks"><i class="ti ti-file-text me-1"></i> Teks</span>
                                    @endif
                                    @if($material->video_url)
                                        <span class="badge badge-soft-danger" title="Ada Video YouTube"><i class="ti ti-brand-youtube me-1"></i> YouTube</span>
                                    @endif
                                    @if($material->document_path)
                                        <span class="badge badge-soft-warning" title="Ada File Lampiran"><i class="ti ti-file-download me-1"></i> Dokumen</span>
                                    @endif
                                    @if(!$material->content && !$material->video_url && !$material->document_path)
                                        <span class="badge badge-soft-secondary">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $material->instructor->name ?? '-' }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-sm btn-outline-primary" title="Edit Materi">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="openDeleteMaterialModal('{{ route('admin.materials.destroy', $material) }}', '{{ addslashes($material->title) }}')" 
                                            title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Materi pembelajaran masih kosong. Silakan buat materi baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($materials->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $materials->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Materi -->
<div class="modal fade" id="modalDeleteMaterial" tabindex="-1" aria-labelledby="modalDeleteMaterialLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Materi Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalMaterialText">Materi pembelajaran yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteMaterialForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                            <i class="ti ti-trash me-1"></i> Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteMaterialModal(url, materialTitle) {
        const form = document.getElementById('deleteMaterialForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalMaterialText');
        if (textEl && materialTitle) {
            textEl.innerText = `Anda akan menghapus materi: "${materialTitle}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteMaterial'));
        modal.show();
    }
</script>
@endsection