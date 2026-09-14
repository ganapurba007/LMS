@extends('layouts.be.master')

@section('header_title', 'Hasil & Status Siswa — ' . $quiz->title)

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
    <div>
        <div class="d-flex align-items-center gap-1.5 mb-1">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                <i class="ti ti-school me-1"></i> {{ $quiz->schoolClass->name ?? 'Kelas' }}
            </span>
            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                <i class="ti ti-book me-1"></i> {{ $quiz->subject->name ?? 'Mata Pelajaran' }}
            </span>
        </div>
        <h5 class="fw-bold m-0 text-dark" style="font-size: 1.1rem;">Hasil &amp; Status Siswa: {{ $quiz->title }}</h5>
        <p class="text-muted mb-0" style="font-size: 0.8rem;">Pantau progres pengerjaan kuis siswa per kelas dan lakukan reset pengerjaan bila diperlukan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 fw-semibold px-2.5 py-1.5" style="font-size: 0.8rem;">
            <i class="ti ti-arrow-left"></i> Kembali ke Kuis
        </a>
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 fw-bold shadow-sm px-2.5 py-1.5" style="font-size: 0.8rem;">
            <i class="ti ti-list-check"></i> Kelola Soal
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 rounded-3" role="alert">
        <div class="d-flex align-items-center py-0.5" style="font-size: 0.85rem;">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 rounded-3" role="alert">
        <div class="d-flex align-items-center py-0.5" style="font-size: 0.85rem;">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quiz Details Overview Cards -->
<div class="row g-2 mb-3">
    @php
        $totalStudents = $students->count();
        $submittedCount = $attempts->filter(fn($a) => !is_null($a->submitted_at))->count();
        $inProgressCount = $attempts->filter(fn($a) => is_null($a->submitted_at))->count();
        $notStartedCount = $totalStudents - $attempts->count();
        $avgScore = $submittedCount > 0 ? round($attempts->filter(fn($a) => !is_null($a->submitted_at))->avg('score'), 1) : 0;
        $maxPossibleScore = $quiz->questions->count() * $quiz->points_per_question;
        $isDeadlinePast = $quiz->deadline && now()->greaterThan($quiz->deadline);
    @endphp

    <div class="col-6 col-md-3">
        <div class="card shadow-2xs border-0 rounded-3 p-2 px-2.5 h-100 bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="ti ti-users fs-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Total Siswa Kelas</div>
                    <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $totalStudents }} <span class="text-muted fw-normal" style="font-size: 0.7rem;">Siswa</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-2xs border-0 rounded-3 p-2 px-2.5 h-100 bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="ti ti-circle-check fs-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Sudah Selesai</div>
                    <div class="fw-bold text-success mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $submittedCount }} <span class="text-muted fw-normal" style="font-size: 0.7rem;">/ {{ $totalStudents }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-2xs border-0 rounded-3 p-2 px-2.5 h-100 bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-warning-subtle text-warning-emphasis rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="ti ti-clock fs-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Sedang / Belum</div>
                    <div class="fw-bold text-warning-emphasis mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $inProgressCount }} <span class="text-muted fw-normal" style="font-size: 0.7rem;">Proses | {{ $notStartedCount }} Belum</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-2xs border-0 rounded-3 p-2 px-2.5 h-100 bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="ti ti-award fs-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted fw-semibold" style="font-size: 0.7rem; line-height: 1.1;">Rata-rata Nilai</div>
                    <div class="fw-bold text-info mb-0" style="font-size: 0.95rem; line-height: 1.2;">{{ $avgScore }} <span class="text-muted fw-normal" style="font-size: 0.7rem;">/ {{ $maxPossibleScore > 0 ? $maxPossibleScore : 100 }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.9rem;">
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
            <table class="table table-vcenter table-hover card-table data-table w-100 mb-0 align-middle" style="font-size: 0.82rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-secondary text-uppercase fw-bold" style="width: 50px; font-size: 0.7rem;">No</th>
                        <th class="text-secondary text-uppercase fw-bold" style="min-width: 180px; font-size: 0.7rem;">Nama Siswa</th>
                        <th class="text-secondary text-uppercase fw-bold" style="width: 160px; font-size: 0.7rem;">Status</th>
                        <th class="text-secondary text-uppercase fw-bold" style="min-width: 200px; font-size: 0.7rem;">Waktu Pengerjaan</th>
                        <th class="text-secondary text-uppercase fw-bold" style="width: 110px; font-size: 0.7rem;">Durasi</th>
                        <th class="text-secondary text-uppercase fw-bold text-center" style="width: 130px; font-size: 0.7rem;">Nilai / Skor</th>
                        <th class="pe-3 text-end text-secondary text-uppercase fw-bold" style="width: 120px; font-size: 0.7rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $attempt = $attempts->get($student->id);
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold text-secondary" style="font-size: 0.8rem;">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.85rem;">{{ $student->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $student->email }}</div>
                            </td>
                            <td>
                                @if(!$attempt)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5 fw-medium" style="font-size: 0.72rem;">
                                        <i class="ti ti-minus me-0.5"></i> Belum Mengerjakan
                                    </span>
                                @elseif(!$attempt->submitted_at)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        <i class="ti ti-clock me-0.5"></i> Sedang Mengerjakan
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.72rem;">
                                        <i class="ti ti-circle-check me-0.5"></i> Selesai
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($attempt)
                                    <div style="font-size: 0.78rem; line-height: 1.3;">
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
                                        {{ $attempt->started_at->diffInMinutes($attempt->submitted_at) }} Menit
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
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.8rem;">
                                        {{ $attempt->score }} <span class="fw-normal text-muted" style="font-size: 0.7rem;">/ {{ $maxPossibleScore > 0 ? $maxPossibleScore : 100 }}</span>
                                    </span>
                                @elseif($attempt)
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                        Belum Submit
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.78rem;">-</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                @if($attempt)
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" 
                                            style="font-size: 0.75rem;"
                                            onclick="openResetAttemptModal('{{ route('admin.quizzes.students.reset', [$quiz, $student]) }}', '{{ addslashes($student->name) }}', {{ $isDeadlinePast ? 'true' : 'false' }})" 
                                            title="Reset Pengerjaan Siswa">
                                        <i class="ti ti-rotate-2"></i>
                                        <span>Reset</span>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-light text-muted rounded-3 px-2 py-1 border-0 opacity-50" style="font-size: 0.75rem;" disabled title="Siswa belum mengerjakan kuis">
                                        <i class="ti ti-rotate-2"></i>
                                        <span>Reset</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted" style="font-size: 0.82rem;">
                                <i class="ti ti-users-minus fs-2 text-secondary opacity-50 mb-1 d-block"></i>
                                Belum ada siswa terdaftar di kelas {{ $quiz->schoolClass->name ?? '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reset Pengerjaan Siswa -->
<div class="modal fade" id="modalResetAttempt" tabindex="-1" aria-labelledby="modalResetAttemptLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-warning-subtle text-warning-emphasis rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="ti ti-rotate-2 fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Reset Pengerjaan Siswa?</h6>
                <p class="text-muted mb-3" id="resetModalStudentText" style="font-size: 0.78rem;">Siswa akan dapat mengerjakan ulang kuis dari awal.</p>
                <div id="resetModalDeadlineWarning" class="alert alert-warning border-0 p-2 small mb-3 text-start d-none rounded-3" style="font-size: 0.78rem;">
                    <i class="ti ti-alert-triangle me-1"></i> <strong>Perhatian:</strong> Batas waktu (deadline) kuis ini sudah berakhir. Agar siswa bisa mengerjakan ulang, perpanjang batas waktu di menu Edit Kuis.
                </div>
                <form id="resetAttemptForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" style="font-size: 0.78rem;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3.5 fw-bold shadow-sm text-dark" style="font-size: 0.78rem;">
                            <i class="ti ti-rotate-2 me-1"></i> Ya, Reset
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
