@extends('layouts.be.master')
@section('header_title', 'Master Data — Kuis & Ujian Online')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
            <i class="ti ti-help-hexagon"></i>
        </div>
        <div>
            <h5 class="md-title">Kuis &amp; Ujian Online</h5>
        </div>
    </div>
    <a href="{{ route('admin.quizzes.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Buat Kuis Baru</span>
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
    <form method="GET" action="{{ route('admin.quizzes.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari judul kuis..." value="{{ $search }}">
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
                <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary" style="padding:.35rem .6rem;font-size:.78rem;" title="Reset Filter">
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
                    <th>Judul Kuis</th>
                    <th class="d-none d-md-table-cell">Mata Pelajaran</th>
                    <th class="d-none d-sm-table-cell">Kelas Target</th>
                    <th class="d-none d-lg-table-cell">Durasi &amp; Poin</th>
                    <th class="d-none d-xl-table-cell text-center">Jumlah Soal</th>
                    <th class="d-none d-md-table-cell">Batas Waktu</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                    @php
                        $no = ($quizzes->currentPage() - 1) * $quizzes->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no">{{ $no }}</td>

                        {{-- Judul Kuis --}}
                        <td>
                            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="fw-bold text-decoration-none d-block mb-1" style="color:var(--tblr-heading-color,#0f172a);font-size:.84rem;">
                                {{ $quiz->title }}
                            </a>
                            <div class="d-flex align-items-center flex-wrap gap-1.5" style="font-size:.73rem;">
                                <span class="text-muted"><i class="ti ti-clock"></i> {{ $quiz->formatted_duration }}</span>
                                <span class="text-muted">•</span>
                                <span class="text-muted">{{ $quiz->points_per_question }} Poin/Soal</span>

                                {{-- Responsive Mobile Badges --}}
                                <div class="d-md-none mt-1 w-100 d-flex flex-wrap gap-1">
                                    <span class="md-badge blue">{{ $quiz->subject->name ?? '-' }}</span>
                                    <span class="md-badge teal">{{ $quiz->schoolClass->name ?? '-' }}</span>
                                    <span class="md-badge amber">{{ $quiz->questions_count }} Soal</span>
                                </div>
                            </div>
                        </td>

                        {{-- Mata Pelajaran --}}
                        <td class="d-none d-md-table-cell">
                            <span class="md-badge blue">
                                {{ $quiz->subject->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Kelas Target --}}
                        <td class="d-none d-sm-table-cell">
                            <span class="md-badge teal">
                                {{ $quiz->schoolClass->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Durasi & Poin --}}
                        <td class="d-none d-lg-table-cell">
                            <div style="font-size:.78rem;font-weight:600;color:var(--tblr-heading-color,#0f172a);">
                                <i class="ti ti-clock me-1 text-primary"></i> {{ $quiz->formatted_duration }}
                            </div>
                            <div class="text-muted small" style="font-size:.72rem;">
                                {{ $quiz->points_per_question }} Poin/Soal
                            </div>
                        </td>

                        {{-- Jumlah Soal --}}
                        <td class="d-none d-xl-table-cell text-center">
                            <span class="md-badge amber">
                                <i class="ti ti-list-numbers"></i>
                                {{ $quiz->questions_count === $quiz->total_questions_count ? $quiz->total_questions_count . ' Soal' : $quiz->questions_count . ' No (' . $quiz->total_questions_count . ' Butir)' }}
                            </span>
                        </td>

                        {{-- Batas Waktu --}}
                        <td class="d-none d-md-table-cell">
                            @if($quiz->deadline)
                                @php $isPast = now()->greaterThan($quiz->deadline); @endphp
                                <span class="md-badge {{ $isPast ? 'rose' : 'teal' }}">
                                    <i class="ti ti-calendar"></i> {{ $quiz->deadline->format('d M Y H:i') }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:.75rem;">-</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.quizzes.students', $quiz) }}" class="md-icon-btn teal" title="Hasil & Status Siswa">
                                    <i class="ti ti-users"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="md-icon-btn amber" title="Kelola Butir Soal Kuis" style="color:#d97706;border-color:rgba(245,158,11,.2);background:rgba(245,158,11,.06);">
                                    <i class="ti ti-list-check"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="md-icon-btn blue" title="Edit Kuis">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="md-icon-btn red" title="Hapus Kuis"
                                    onclick="openDeleteQuizModal('{{ route('admin.quizzes.destroy', $quiz) }}', '{{ addslashes($quiz->title) }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="md-empty-row">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap purple">
                                    <i class="ti ti-help-hexagon"></i>
                                </div>
                                <div class="md-empty-title">Belum Ada Kuis Ujian</div>
                                <div class="md-empty-desc">
                                    @if(request('search') || request('subject_id') || request('class_id'))
                                        Tidak ada kuis yang cocok dengan filter pencarian Anda.
                                    @else
                                        Buat paket soal kuis pilihan ganda, benar-salah, atau menjodohkan dengan batasan waktu ujian otomatis.
                                    @endif
                                </div>
                                <div class="md-empty-action">
                                    @if(request('search') || request('subject_id') || request('class_id'))
                                        <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary me-2" style="font-size:.78rem;padding:.35rem .8rem;">
                                            <i class="ti ti-x"></i> Reset Filter
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.quizzes.create') }}" class="md-btn-primary" style="font-size:.78rem;padding:.35rem .85rem;">
                                        <i class="ti ti-plus"></i> Buat Kuis Pertama
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($quizzes->hasPages())
        <div class="md-card-footer">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteQuiz" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Kuis Ini?</h6>
            <p class="md-modal-text" id="deleteModalQuizText">Kuis dan data pengerjaan siswa yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteQuizForm" method="POST" action="">
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
function openDeleteQuizModal(url, quizTitle) {
    document.getElementById('deleteQuizForm').action = url;
    document.getElementById('deleteModalQuizText').innerText = `Anda akan menghapus kuis: "${quizTitle}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteQuiz')).show();
}
</script>
@endsection