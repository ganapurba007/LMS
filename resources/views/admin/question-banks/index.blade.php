@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Soal')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-help"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Soal</h5>
            <div class="text-muted small">Kelola butir soal evaluasi dan kuis untuk digunakan kembali pada berbagai kuis dan ujian.</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.question-banks.create') }}" class="md-btn-primary">
            <i class="ti ti-plus"></i>
            <span>Buat Soal</span>
        </a>
    </div>
</div>

{{-- Flash Messages --}}
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

{{-- Filter & Search Form --}}
<div class="md-card mb-4 p-3">
    <form method="GET" action="{{ route('admin.question-banks.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari isi pertanyaan soal..." value="{{ $search ?? request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Format Soal --</option>
                <option value="multiple_choice" {{ ($type ?? request('type')) == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="true_false" {{ ($type ?? request('type')) == 'true_false' ? 'selected' : '' }}>Benar / Salah</option>
                <option value="matching" {{ ($type ?? request('type')) == 'matching' ? 'selected' : '' }}>Menjodohkan</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="md-btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
            @if(request('search') || request('type'))
                <a href="{{ route('admin.question-banks.index') }}" class="md-btn-secondary" title="Reset Filter"><i class="ti ti-refresh"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Questions Table Card --}}
<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Format &amp; Pertanyaan</th>
                    <th class="d-none d-md-table-cell">Opsi</th>
                    <th class="d-none d-lg-table-cell">Kunci Jawaban</th>
                    <th class="md-th-action text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questionBanks as $qb)
                    @php
                        $no = ($questionBanks->currentPage() - 1) * $questionBanks->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no text-center">
                            <span class="badge bg-light text-dark border fw-bold" style="font-size: .76rem;">{{ $no }}</span>
                        </td>

                        {{-- Format & Pertanyaan --}}
                        <td>
                            <div class="mb-1">
                                @if($qb->isMatching())
                                    <span class="md-badge qb-badge-matching">
                                        <i class="ti ti-arrows-left-right"></i> Menjodohkan
                                    </span>
                                @elseif($qb->isTrueFalse())
                                    <span class="md-badge qb-badge-tf">
                                        <i class="ti ti-checkup-list"></i> Benar / Salah
                                    </span>
                                @else
                                    <span class="md-badge qb-badge-mc">
                                        <i class="ti ti-list-check"></i> Pilihan Ganda
                                    </span>
                                @endif
                            </div>
                            <div class="qb-question-text">
                                @if($qb->hasImage())
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size: .72rem; font-weight: 600;"><i class="ti ti-photo me-0.5"></i> (gambar)</span>
                                @endif
                                @if($qb->hasTable())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle me-1" style="font-size: .72rem; font-weight: 600;"><i class="ti ti-table me-0.5"></i> (tabel)</span>
                                @endif
                                @php
                                    $cleanText = trim(preg_replace('/!\[.*?\]\(.*?\)/', '', strip_tags($qb->question_text)));
                                @endphp
                                @if($cleanText !== '')
                                    {{ Str::limit($cleanText, 130) }}
                                @else
                                    <span class="text-muted fst-italic small">Konten {{ $qb->hasImage() ? '(gambar)' : '' }} {{ $qb->hasTable() ? '(tabel)' : '' }}</span>
                                @endif

                                @if($qb->hasImage() && $qb->getImageUrl())
                                    <div class="mt-1.5">
                                        <a href="{{ $qb->getImageUrl() }}" target="_blank" class="d-inline-block text-decoration-none" title="Klik untuk melihat gambar ukuran penuh">
                                            <img src="{{ $qb->getImageUrl() }}" alt="Gambar Soal" class="rounded border shadow-xs" style="max-height: 52px; max-width: 95px; object-fit: contain; background: #fafafa; padding: 2px;">
                                        </a>
                                    </div>
                                @endif
                            </div>
                            {{-- Mobile Answer Key Preview --}}
                            <div class="d-lg-none mt-1">
                                @if($qb->isMatching())
                                    @foreach($qb->options->take(2) as $opt)
                                        <div class="qb-pair-mobile">
                                            <span>{{ Str::limit($opt->option_text, 18) }}</span>
                                            <i class="ti ti-arrow-right" style="color:#0284c7;font-size:.7rem;"></i>
                                            <span class="qb-pair-a">{{ Str::limit($opt->match_text, 18) }}</span>
                                        </div>
                                    @endforeach
                                    @if($qb->options->count() > 2)
                                        <span class="qb-pair-more">+{{ $qb->options->count() - 2 }} pasangan lagi</span>
                                    @endif
                                @elseif($qb->isTrueFalse())
                                    @php $tf = $qb->options->firstWhere('is_correct', true); $isT = optional($tf)->option_text === 'Benar'; @endphp
                                    <span class="md-badge {{ $isT ? 'teal' : 'rose' }}" style="margin-top:.2rem;">
                                        <i class="ti ti-{{ $isT ? 'check' : 'x' }}"></i> {{ optional($tf)->option_text ?? '-' }}
                                    </span>
                                @else
                                    @php $cor = $qb->options->firstWhere('is_correct', true); @endphp
                                    @if($cor)
                                        <span class="md-badge teal" style="margin-top:.2rem;">
                                            <i class="ti ti-check"></i> {{ Str::limit($cor->option_text, 35) }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>

                        {{-- Opsi --}}
                        <td class="d-none d-md-table-cell">
                            @if($qb->isMatching())
                                <span class="md-badge qb-badge-matching">{{ $qb->options_count }} Pasangan</span>
                            @elseif($qb->isTrueFalse())
                                <span class="md-badge qb-badge-tf">2 Opsi</span>
                            @else
                                <span class="md-badge qb-badge-mc">{{ $qb->options_count }} Opsi</span>
                            @endif
                        </td>

                        {{-- Kunci Jawaban --}}
                        <td class="d-none d-lg-table-cell">
                            @if($qb->isMatching())
                                <div class="qb-pair-list">
                                    @forelse($qb->options as $opt)
                                        <div class="qb-pair">
                                            <span class="qb-pair-q">{{ Str::limit($opt->option_text, 22) }}</span>
                                            <i class="ti ti-arrow-right" style="color:#0284c7;font-size:.72rem;flex-shrink:0;"></i>
                                            <span class="qb-pair-a">{{ Str::limit($opt->match_text, 22) }}</span>
                                        </div>
                                    @empty
                                        <span class="text-muted" style="font-size:.74rem;">Belum ada pasangan</span>
                                    @endforelse
                                </div>
                            @elseif($qb->isTrueFalse())
                                @php $tf = $qb->options->firstWhere('is_correct', true); $isT = optional($tf)->option_text === 'Benar'; @endphp
                                <span class="md-badge {{ $isT ? 'teal' : 'rose' }}">
                                    <i class="ti ti-{{ $isT ? 'check' : 'x' }}"></i> Kunci: {{ optional($tf)->option_text ?? 'Belum Diatur' }}
                                </span>
                            @else
                                @php $cor = $qb->options->firstWhere('is_correct', true); @endphp
                                @if($cor)
                                    <span class="md-badge teal">
                                        <i class="ti ti-check"></i> {{ Str::limit($cor->option_text, 45) }}
                                    </span>
                                @else
                                    <span class="md-badge rose">Belum diatur</span>
                                @endif
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.question-banks.edit', $qb) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                @php
                                    $previewQbText = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($qb->question_text))), 60);
                                @endphp
                                <button class="md-icon-btn red" title="Hapus"
                                    data-action="{{ route('admin.question-banks.destroy', $qb) }}"
                                    data-text="{{ $previewQbText }}"
                                    onclick="openDeleteQbModal(this.dataset.action, this.dataset.text)">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="md-empty-row">
                            <div class="md-empty-state py-5 text-center">
                                <div class="md-empty-icon-wrap teal mb-3" style="width: 60px; height: 60px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.8rem;">
                                    <i class="ti ti-help-off"></i>
                                </div>
                                <h5 class="md-empty-title fw-bold mb-1">Bank Soal Masih Kosong</h5>
                                <p class="md-empty-desc text-muted mb-3" style="max-width: 500px; margin: 0 auto; font-size: .84rem;">
                                    @if(request('search') || request('type'))
                                        Tidak ada butir soal yang sesuai dengan filter atau kata kunci pencarian Anda.
                                    @else
                                        Belum ada butir soal di Bank Soal. Buat soal secara manual atau impor dari dokumen Word (.docx) / Markdown (.md).
                                    @endif
                                </p>
                                <div class="md-empty-action">
                                    @if(request('search') || request('type'))
                                        <a href="{{ route('admin.question-banks.index') }}" class="md-btn-secondary me-2">
                                            <i class="ti ti-x"></i> Reset Filter
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.question-banks.create') }}" class="md-btn-primary">
                                        <i class="ti ti-plus"></i> Tambah Butir Soal Baru
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questionBanks->hasPages())
        <div class="md-card-footer">
            {{ $questionBanks->links() }}
        </div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteQuestionBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Soal Ini?</h6>
            <p class="md-modal-text" id="deleteModalQbText">Soal yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteQbForm" method="POST" action="">
                @csrf @method('DELETE')
                <div class="md-modal-actions">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="md-btn-danger"><i class="ti ti-trash"></i> Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')

<style>
.qb-badge-matching {
    background: #ecfeff;
    color: #0891b2;
    border: 1px solid #a5f3fc;
    font-weight: 700;
}
.qb-badge-tf {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-weight: 700;
}
.qb-badge-mc {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-weight: 700;
}

.qb-question-text {
    font-size: .83rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.5;
    max-width: 480px;
}
.qb-pair-list {
    display: flex;
    flex-direction: column;
    gap: .3rem;
}
.qb-pair {
    display: flex;
    align-items: center;
    gap: .45rem;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: .25rem .55rem;
    font-size: .74rem;
    max-width: 340px;
}
.qb-pair-q {
    font-weight: 600;
    color: #1e293b;
    flex: 1;
}
.qb-pair-a {
    font-weight: 700;
    color: #059669;
    flex: 1;
}
.qb-pair-mobile {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .72rem;
    font-weight: 600;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    padding: .2rem .45rem;
    margin-bottom: .2rem;
}
.qb-pair-more {
    font-size: .68rem;
    font-weight: 600;
    color: #64748b;
    display: block;
}

/* Dark Mode Overrides */
[data-theme="dark"] .qb-badge-matching {
    background: rgba(8, 145, 178, 0.18);
    color: #38bdf8;
    border-color: rgba(56, 189, 248, 0.35);
}
[data-theme="dark"] .qb-badge-tf {
    background: rgba(245, 158, 11, 0.18);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.35);
}
[data-theme="dark"] .qb-badge-mc {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.35);
}
[data-theme="dark"] .qb-question-text {
    color: #f8fafc;
}
[data-theme="dark"] .qb-pair,
[data-theme="dark"] .qb-pair-mobile {
    background: rgba(15, 23, 42, 0.65);
    border-color: #334155;
}
[data-theme="dark"] .qb-pair-q {
    color: #e2e8f0;
}
[data-theme="dark"] .qb-pair-a {
    color: #34d399;
}
[data-theme="dark"] .qb-pair-more {
    color: #94a3b8;
}
</style>

<script>
function openDeleteQbModal(url, questionText) {
    document.getElementById('deleteQbForm').action = url;
    document.getElementById('deleteModalQbText').innerText = `Anda akan menghapus: "${questionText}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteQuestionBank')).show();
}
</script>
@endsection