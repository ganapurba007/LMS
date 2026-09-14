@extends('layouts.be.master')

@section('header_title', 'Master Data — Kuis Evaluation')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Kuis &amp; Ujian Online</h3>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Buat Kuis Baru
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
                        <th>Judul Kuis</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas Target</th>
                        <th>Durasi &amp; Poin</th>
                        <th>Jumlah Soal</th>
                        <th>Batas Waktu</th>
                        <th class="pe-4 text-end" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $quiz->title }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $quiz->subject->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ $quiz->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold"><i class="ti ti-clock me-1"></i> {{ $quiz->duration_minutes }} Menit</div>
                                <div class="small text-muted">{{ $quiz->points_per_question }} Poin/Soal</div>
                            </td>
                            <td>
                                <span class="badge badge-soft-success">
                                    {{ $quiz->questions_count }} Soal
                                </span>
                            </td>
                            <td>
                                <div class="small text-danger fw-semibold">
                                    {{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') : '-' }}
                                </div>
                            </td>
                            <td class="pe-4 text-end align-middle text-nowrap">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('admin.quizzes.students', $quiz) }}" class="btn btn-sm btn-outline-info rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" title="Hasil & Status Siswa">
                                        <i class="ti ti-users fs-6"></i>
                                    </a>
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-warning rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" title="Kelola Soal Kuis">
                                        <i class="ti ti-list-check fs-6"></i>
                                    </a>
                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" title="Edit Kuis">
                                        <i class="ti ti-edit fs-6"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" 
                                            onclick="openDeleteQuizModal('{{ route('admin.quizzes.destroy', $quiz) }}', '{{ addslashes($quiz->title) }}')" 
                                            title="Hapus Kuis">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada kuis yang dibuat. Silakan buat kuis baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($quizzes->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Kuis -->
<div class="modal fade" id="modalDeleteQuiz" tabindex="-1" aria-labelledby="modalDeleteQuizLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Kuis Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalQuizText">Kuis dan data pengerjaan siswa yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteQuizForm" method="POST" action="">
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
    function openDeleteQuizModal(url, quizTitle) {
        const form = document.getElementById('deleteQuizForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalQuizText');
        if (textEl && quizTitle) {
            textEl.innerText = `Anda akan menghapus kuis: "${quizTitle}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteQuiz'));
        modal.show();
    }
</script>
@endsection