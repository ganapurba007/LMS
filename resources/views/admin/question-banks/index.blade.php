@extends('layouts.be.master')

@section('header_title', 'Master Data — Bank Soal')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Bank Soal &amp; Repositori Soal</h3>
        <p class="text-muted small mb-0">Kelola bank soal multi-format (Pilihan Ganda, Benar/Salah, Menjodohkan) yang siap diimpor ke kuis.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.question-banks.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm fw-bold">
            <i class="ti ti-plus"></i> Buat Soal Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-circle fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Content Card -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="ti ti-files text-primary"></i> Daftar Soal di Bank Soal
        </h5>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold">
            Total: {{ $questionBanks->total() }} Soal
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table w-100 mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase fs-7 fw-bold" style="width: 60px;">No</th>
                        <th class="text-secondary text-uppercase fs-7 fw-bold" style="min-width: 250px;">Format &amp; Pertanyaan Soal</th>
                        <th class="text-secondary text-uppercase fs-7 fw-bold" style="width: 130px;">Tipe Opsi</th>
                        <th class="text-secondary text-uppercase fs-7 fw-bold" style="min-width: 280px;">Kunci Jawaban / Pasangan Cocok</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fs-7 fw-bold" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questionBanks as $qb)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ ($questionBanks->currentPage() - 1) * $questionBanks->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="mb-1.5">
                                    @if($qb->isMatching())
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="ti ti-arrows-left-right me-1"></i> Menjodohkan
                                        </span>
                                    @elseif($qb->isTrueFalse())
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="ti ti-checkup-list me-1"></i> Benar / Salah
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="ti ti-list-check me-1"></i> Pilihan Ganda
                                        </span>
                                    @endif
                                </div>
                                <div class="fw-semibold text-dark text-wrap" style="max-width: 500px; line-height: 1.4;">
                                    {{ Str::limit($qb->question_text, 150) }}
                                </div>
                            </td>
                            <td>
                                @if($qb->isMatching())
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">
                                        {{ $qb->options_count }} Pasangan
                                    </span>
                                @elseif($qb->isTrueFalse())
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2.5 py-1">
                                        2 Opsi (B/S)
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">
                                        {{ $qb->options_count }} Opsi
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($qb->isMatching())
                                    <!-- Menjodohkan: Tampilkan Soal & Pasangannya secara langsung -->
                                    <div class="d-flex flex-column gap-1 my-1" style="max-width: 420px;">
                                        @forelse($qb->options as $opt)
                                            <div class="d-inline-flex align-items-center gap-1.5 p-1 px-2 bg-light border rounded-3" style="font-size: 0.8rem;">
                                                <span class="fw-semibold text-dark">{{ $opt->option_text }}</span>
                                                <i class="ti ti-arrow-right text-info mx-0.5"></i>
                                                <span class="fw-bold text-success">{{ $opt->match_text }}</span>
                                            </div>
                                        @empty
                                            <span class="text-muted small">Belum ada pasangan</span>
                                        @endforelse
                                    </div>
                                @elseif($qb->isTrueFalse())
                                    @php
                                        $correct = $qb->options->firstWhere('is_correct', true);
                                        $isTrue = optional($correct)->option_text === 'Benar';
                                    @endphp
                                    <span class="badge {{ $isTrue ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-3 py-1 fw-bold">
                                        <i class="ti {{ $isTrue ? 'ti-check' : 'ti-x' }} me-1"></i> Kunci: {{ optional($correct)->option_text ?? 'Belum Diatur' }}
                                    </span>
                                @else
                                    @php
                                        $correct = $qb->options->firstWhere('is_correct', true);
                                    @endphp
                                    @if($correct)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-medium" title="Kunci Jawaban Benar">
                                            <i class="ti ti-check me-1"></i> {{ Str::limit($correct->option_text, 45) }}
                                        </span>
                                    @else
                                        <span class="text-danger small">Belum diatur</span>
                                    @endif
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.question-banks.edit', $qb) }}" class="btn btn-sm btn-outline-info rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" title="Edit Soal">
                                        <i class="ti ti-edit fs-6"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 fw-semibold shadow-2xs" 
                                            onclick="openDeleteQbModal('{{ route('admin.question-banks.destroy', $qb) }}', '{{ addslashes(Str::limit($qb->question_text, 60)) }}')" 
                                            title="Hapus Soal">
                                        <i class="ti ti-trash fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <i class="ti ti-folder-off fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">Bank Soal Masih Kosong</h6>
                                    <p class="small text-muted mb-3">Belum ada soal yang tersimpan. Klik tombol di bawah untuk membuat soal baru.</p>
                                    <a href="{{ route('admin.question-banks.create') }}" class="btn btn-primary btn-sm px-3 rounded-pill">
                                        <i class="ti ti-plus me-1"></i> Buat Soal Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($questionBanks->hasPages())
        <div class="card-footer bg-white border-top d-flex justify-content-end py-3 px-4">
            {{ $questionBanks->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus (Custom Bootstrap 5 Modal) -->
<div class="modal fade" id="modalDeleteQuestionBank" tabindex="-1" aria-labelledby="modalDeleteQuestionBankLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Soal Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalQbText">Soal yang dihapus dari Bank Soal tidak dapat dikembalikan.</p>
                <form id="deleteQbForm" method="POST" action="">
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
    function openDeleteQbModal(url, questionText) {
        const form = document.getElementById('deleteQbForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalQbText');
        if (textEl && questionText) {
            textEl.innerText = `Anda akan menghapus: "${questionText}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteQuestionBank'));
        modal.show();
    }
</script>
@endsection