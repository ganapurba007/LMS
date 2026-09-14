@extends('layouts.be.master')

@section('header_title', 'Master Data — Mata Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Mata Pelajaran &amp; Guru Pengampu</h3>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah Mata Pelajaran
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
                        <th>Nama Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Statistik Konten</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold">{{ $subject->name }}</span>
                            </td>
                            <td>
                                @forelse($subject->instructors as $guru)
                                    <span class="badge badge-soft-primary mb-1 me-1">
                                        <i class="ti ti-user me-1"></i> {{ $guru->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small">Belum ada guru pengampu</span>
                                @endforelse
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $subject->materials_count }} Materi | {{ $subject->assignments_count }} Tugas | {{ $subject->quizzes_count }} Kuis
                                </small>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-primary" title="Edit / Alokasi Guru">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="openDeleteSubjectModal('{{ route('admin.subjects.destroy', $subject) }}', '{{ addslashes($subject->name) }}')" 
                                            title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada mata pelajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($subjects->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $subjects->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Mata Pelajaran -->
<div class="modal fade" id="modalDeleteSubject" tabindex="-1" aria-labelledby="modalDeleteSubjectLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Mapel Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalSubjectText">Mata pelajaran yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteSubjectForm" method="POST" action="">
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
    function openDeleteSubjectModal(url, subjectName) {
        const form = document.getElementById('deleteSubjectForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalSubjectText');
        if (textEl && subjectName) {
            textEl.innerText = `Anda akan menghapus mata pelajaran: "${subjectName}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteSubject'));
        modal.show();
    }
</script>
@endsection
