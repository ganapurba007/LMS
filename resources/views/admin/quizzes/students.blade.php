@extends('layouts.be.master')

@section('header_title', 'Hasil & Status Siswa — ' . $quiz->title)

@section('content')
@include('admin._partials.master-data-styles')

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                <i class="ti ti-users"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-1.5 mb-1 flex-wrap">
                    <span class="md-badge blue">
                        <i class="ti ti-school me-1"></i> {{ $quiz->schoolClass->name ?? 'Kelas' }}
                    </span>
                    <span class="md-badge info" style="background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.2);">
                        <i class="ti ti-book me-1"></i> {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
                    </span>
                </div>
                <h5 class="md-title">Hasil &amp; Status Siswa: {{ $quiz->title }}</h5>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quizzes.index') }}" class="md-btn-secondary">
                <i class="ti ti-arrow-left"></i> <span>Kembali</span>
            </a>
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="md-btn-primary">
                <i class="ti ti-list-check"></i> <span>Kelola Soal</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert md-alert success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert md-alert danger alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle fs-5 me-2 flex-shrink-0"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quiz Details Overview Cards -->
    @php
        $totalStudents = $students->count();
        $submittedCount = $attempts->filter(fn($a) => !is_null($a->submitted_at))->count();
        $inProgressCount = $attempts->filter(fn($a) => is_null($a->submitted_at))->count();
        $notStartedCount = $totalStudents - $attempts->count();
        $avgScore = $submittedCount > 0 ? round($attempts->filter(fn($a) => !is_null($a->submitted_at))->avg('score'), 1) : 0;
        $isDeadlinePast = $quiz->deadline && now()->greaterThan($quiz->deadline);
    @endphp

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #eff6ff; color: #2563eb;">
                        <i class="ti ti-users fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Total Siswa Kelas</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $totalStudents }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">Siswa</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #ecfdf5; color: #059669;">
                        <i class="ti ti-circle-check fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Sudah Selesai</div>
                        <div class="fw-bold text-success mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $submittedCount }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ {{ $totalStudents }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #fffbeb; color: #d97706;">
                        <i class="ti ti-clock fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Sedang / Belum</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $inProgressCount }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">Proses | {{ $notStartedCount }} Belum</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card md-card p-3 h-100">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #f0fdfa; color: #0d9488;">
                        <i class="ti ti-award fs-4"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted fw-semibold" style="font-size: 0.72rem; line-height: 1.1;">Rata-rata Nilai</div>
                        <div class="fw-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">{{ $avgScore }} <span class="text-muted fw-normal" style="font-size: 0.72rem;">/ 100</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card md-card">
        <div class="card-header bg-white border-bottom py-3 px-3.5 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.92rem;">
                    <i class="ti ti-user-check text-primary fs-5"></i> Daftar Siswa &amp; Hasil Kuis
                </h6>
                <div class="text-muted mt-0.5" style="font-size: 0.78rem;">
                    Batas Waktu (Deadline): <span class="fw-semibold text-dark">{{ $quiz->deadline ? $quiz->deadline->format('d M Y H:i') : '-' }}</span>
                    @if($isDeadlinePast)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1 px-2 py-0.5" style="font-size: 0.68rem;">Deadline Berakhir</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 px-2 py-0.5" style="font-size: 0.68rem;">Aktif</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table md-table table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="md-th-no text-center" style="width: 55px;">NO</th>
                            <th style="min-width: 180px;">NAMA SISWA</th>
                            <th style="width: 160px;">STATUS</th>
                            <th style="min-width: 200px;">WAKTU PENGERJAAN</th>
                            <th style="width: 110px;">DURASI</th>
                            <th class="text-center" style="width: 130px;">NILAI / SKOR</th>
                            <th class="md-th-action text-center" style="width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $attempt = $attempts->get($student->id);
                            @endphp
                            <tr>
                                <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ $student->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $student->email }}</div>
                                </td>
                                <td>
                                    @if(!$attempt)
                                        <span class="md-badge gray">
                                            <i class="ti ti-minus me-0.5"></i> Belum Mengerjakan
                                        </span>
                                    @elseif(!$attempt->submitted_at)
                                        <span class="md-badge amber">
                                            <i class="ti ti-clock me-0.5"></i> Sedang Mengerjakan
                                        </span>
                                    @else
                                        <span class="md-badge green">
                                            <i class="ti ti-circle-check me-0.5"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt)
                                        <div style="font-size: 0.78rem; line-height: 1.35;">
                                            <div class="text-dark"><i class="ti ti-playstation-square text-primary me-1"></i> Mulai: {{ $attempt->started_at ? $attempt->started_at->format('d M Y H:i') : '-' }}</div>
                                            @if($attempt->submitted_at)
                                                <div class="text-muted"><i class="ti ti-checkbox text-success me-1"></i> Selesai: {{ $attempt->submitted_at->format('d M Y H:i') }}</div>
                                            @else
                                                <div class="text-warning fw-semibold"><i class="ti ti-loader text-warning me-1"></i> Dalam Proses...</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt && $attempt->submitted_at && $attempt->started_at)
                                        <span class="text-dark fw-semibold" style="font-size: 0.78rem;">
                                            {{ $attempt->duration_formatted }}
                                        </span>
                                    @elseif($attempt && $attempt->started_at)
                                        <span class="text-warning fw-semibold" style="font-size: 0.78rem;">
                                            {{ $attempt->started_at->diffForHumans(null, true) }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($attempt && !is_null($attempt->submitted_at))
                                        <span class="md-badge blue fw-bold" style="font-size: 0.82rem; padding: 0.3rem 0.65rem;">
                                            {{ $attempt->score }} <span class="fw-normal text-muted" style="font-size: 0.7rem;">/ 100</span>
                                        </span>
                                    @elseif($attempt)
                                        <span class="md-badge amber">
                                            Belum Submit
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                    @endif
                                </td>
                                <td class="md-td-action text-center">
                                    <div class="md-action-group text-center">
                                        @if($attempt)
                                            <button type="button" 
                                                    class="md-icon-btn red" 
                                                    onclick="openResetAttemptModal('{{ route('admin.quizzes.students.reset', [$quiz, $student]) }}', '{{ addslashes($student->name) }}', {{ $isDeadlinePast ? 'true' : 'false' }})" 
                                                    title="Reset Pengerjaan Siswa">
                                                <i class="ti ti-rotate-2"></i>
                                            </button>
                                        @else
                                            <button type="button" class="md-icon-btn gray" disabled title="Siswa belum mengerjakan kuis" style="opacity: 0.4; cursor: not-allowed;">
                                                <i class="ti ti-rotate-2"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="md-empty-row">
                                    <div class="md-empty-state">
                                        <div class="md-empty-icon-wrap purple">
                                            <i class="ti ti-users-minus"></i>
                                        </div>
                                        <div class="md-empty-title">Belum Ada Siswa Terdaftar</div>
                                        <div class="md-empty-desc">Belum ada siswa yang terdaftar pada kelas <strong>{{ $quiz->schoolClass->name ?? 'ini' }}</strong>.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reset Pengerjaan Siswa -->
<div class="modal fade" id="modalResetAttempt" tabindex="-1" aria-labelledby="modalResetAttemptLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-body">
                <div class="md-modal-icon amber">
                    <i class="ti ti-rotate-2"></i>
                </div>
                <h5 class="md-modal-title">Reset Pengerjaan?</h5>
                <p class="md-modal-text" id="resetModalStudentText">Siswa akan dapat mengerjakan ulang kuis dari awal.</p>
                <div id="resetModalDeadlineWarning" class="alert md-alert warning p-2.5 small mb-3 text-start d-none" style="font-size: 0.78rem;">
                    <i class="ti ti-alert-triangle me-1"></i> <strong>Perhatian:</strong> Batas waktu (deadline) kuis ini sudah berakhir. Agar siswa bisa mengerjakan ulang, perpanjang batas waktu di menu Edit Kuis.
                </div>
                <form id="resetAttemptForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="md-btn-danger">
                            <i class="ti ti-rotate-2"></i> <span>Ya, Reset</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openResetAttemptModal(url, studentName, isDeadlinePast) {
        const form = document.getElementById('resetAttemptForm');
        form.action = url;
        const textEl = document.getElementById('resetModalStudentText');
        if (textEl && studentName) {
            textEl.innerText = `Anda akan menghapus hasil & riwayat pengerjaan kuis untuk siswa "${studentName}".`;
        }
        const warningEl = document.getElementById('resetModalDeadlineWarning');
        if (warningEl) {
            if (isDeadlinePast) {
                warningEl.classList.remove('d-none');
            } else {
                warningEl.classList.add('d-none');
            }
        }
        const modal = new bootstrap.Modal(document.getElementById('modalResetAttempt'));
        modal.show();
    }
</script>
@endsection
