@extends('layouts.be.master')

@section('header_title', 'Master Data — Tugas Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Tugas Siswa Kelas</h3>
    <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Buat Tugas Baru
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
                        <th>Judul Tugas</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas Target</th>
                        <th>Batas Waktu (Deadline)</th>
                        <th>Pengumpulan</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $assignment->title }}</div>
                                <div class="small text-muted">{{ Str::limit($assignment->description, 60) }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $assignment->subject->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $assignment->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold text-danger">
                                    <i class="ti ti-clock me-1"></i> {{ $assignment->due_date ? $assignment->due_date->format('d M Y H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-soft-success">
                                    {{ $assignment->submissions_count }} Siswa
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-primary" title="Edit Tugas">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="openDeleteAssignmentModal('{{ route('admin.assignments.destroy', $assignment) }}', '{{ addslashes($assignment->title) }}')" 
                                            title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada tugas siswa yang dibuat. Silakan buat tugas baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($assignments->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $assignments->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Tugas -->
<div class="modal fade" id="modalDeleteAssignment" tabindex="-1" aria-labelledby="modalDeleteAssignmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Tugas Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalAssignmentText">Tugas dan file pengumpulan siswa yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteAssignmentForm" method="POST" action="">
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
    function openDeleteAssignmentModal(url, assignmentTitle) {
        const form = document.getElementById('deleteAssignmentForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalAssignmentText');
        if (textEl && assignmentTitle) {
            textEl.innerText = `Anda akan menghapus tugas: "${assignmentTitle}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteAssignment'));
        modal.show();
    }
</script>
@endsection