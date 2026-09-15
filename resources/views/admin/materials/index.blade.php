@extends('layouts.be.master')

@section('header_title', 'Master Data — Materi Pembelajaran')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 heading-custom">Daftar Materi Pembelajaran Kelas</h3>
        <p class="text-muted-custom small mb-0">Kelola modul pembelajaran dan ruang diskusi bersama siswa</p>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
        <i class="ti ti-plus"></i> Tambah Materi Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
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
                            <td class="ps-4 fw-bold heading-custom">{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.materials.show', $material) }}" class="fw-bold heading-custom text-decoration-none">
                                    {{ $material->title }}
                                </a>
                                <div class="d-flex align-items-center gap-2 mt-1 small">
                                    <span class="text-muted-custom">Urutan: {{ $material->order }}</span>
                                    @if($material->discussions_count > 0)
                                        <a href="{{ route('admin.materials.show', $material) }}#discussion-card" class="badge badge-soft-primary text-decoration-none">
                                            <i class="ti ti-messages me-1"></i>{{ $material->discussions_count }} Diskusi
                                        </a>
                                    @else
                                        <span class="badge badge-soft-secondary">
                                            <i class="ti ti-message me-1"></i>0 Diskusi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $material->subject->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-info">
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
                            <td><span class="heading-custom">{{ $material->instructor->name ?? '-' }}</span></td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.materials.show', $material) }}" class="btn btn-sm btn-soft-primary" title="Lihat Detail & Ruang Diskusi">
                                        <i class="ti ti-messages"></i>
                                    </a>
                                    <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-sm btn-light" title="Edit Materi">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-soft-danger" 
                                            onclick="openDeleteMaterialModal('{{ route('admin.materials.destroy', $material) }}', '{{ addslashes($material->title) }}')" 
                                            title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted-custom">Materi pembelajaran masih kosong. Silakan buat materi baru.</td>
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
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: var(--tblr-card-bg); color: var(--tblr-body-color);">
            <div class="modal-body text-center p-4">
                <div class="avatar-icon-box avatar-icon-danger mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.5rem;">
                    <i class="ti ti-trash"></i>
                </div>
                <h5 class="fw-bold heading-custom mb-1">Hapus Materi Ini?</h5>
                <p class="text-muted-custom small mb-4" id="deleteModalMaterialText">Materi pembelajaran yang dihapus tidak dapat dikembalikan.</p>
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