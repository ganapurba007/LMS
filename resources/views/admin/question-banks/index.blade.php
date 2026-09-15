@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Soal')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-database"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Soal</h5>
        </div>
    </div>
    <a href="{{ route('admin.question-banks.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Buat Soal</span>
    </a>
</div>

{{-- Flash --}}
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

{{-- Card --}}
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
                        <td class="md-td-no text-center">{{ $no }}</td>

                        {{-- Format & Pertanyaan --}}
                        <td>
                            <div class="mb-1">
                                @if($qb->isMatching())
                                    <span class="md-badge" style="background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.2);">
                                        <i class="ti ti-arrows-left-right"></i> Menjodohkan
                                    </span>
                                @elseif($qb->isTrueFalse())
                                    <span class="md-badge" style="background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.2);">
                                        <i class="ti ti-checkup-list"></i> Benar / Salah
                                    </span>
                                @else
                                    <span class="md-badge blue">
                                        <i class="ti ti-list-check"></i> Pilihan Ganda
                                    </span>
                                @endif
                            </div>
                            <div class="qb-question-text">{{ Str::limit($qb->question_text, 130) }}</div>
                            {{-- Kunci jawaban on mobile --}}
                            <div class="d-lg-none mt-1">
                                @if($qb->isMatching())
                                    @foreach($qb->options->take(2) as $opt)
                                        <div class="qb-pair-mobile">
                                            <span>{{ Str::limit($opt->option_text, 18) }}</span>
                                            <i class="ti ti-arrow-right" style="color:#0891b2;font-size:.7rem;"></i>
                                            <span style="color:#0ca678;font-weight:700;">{{ Str::limit($opt->match_text, 18) }}</span>
                                        </div>
                                    @endforeach
                                    @if($qb->options->count() > 2)
                                        <span style="font-size:.65rem;color:var(--tblr-text-muted,#64748b);">+{{ $qb->options->count() - 2 }} pasangan lagi</span>
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
                                <span class="md-badge" style="background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.2);">{{ $qb->options_count }} Pasangan</span>
                            @elseif($qb->isTrueFalse())
                                <span class="md-badge amber">2 Opsi</span>
                            @else
                                <span class="md-badge blue">{{ $qb->options_count }} Opsi</span>
                            @endif
                        </td>

                        {{-- Kunci Jawaban --}}
                        <td class="d-none d-lg-table-cell">
                            @if($qb->isMatching())
                                <div class="qb-pair-list">
                                    @forelse($qb->options as $opt)
                                        <div class="qb-pair">
                                            <span class="qb-pair-q">{{ Str::limit($opt->option_text, 22) }}</span>
                                            <i class="ti ti-arrow-right" style="color:#0891b2;font-size:.72rem;flex-shrink:0;"></i>
                                            <span class="qb-pair-a">{{ Str::limit($opt->match_text, 22) }}</span>
                                        </div>
                                    @empty
                                        <span style="font-size:.74rem;color:var(--tblr-text-muted,#64748b);">Belum ada pasangan</span>
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
                                    <span style="font-size:.74rem;color:#ef4444;">Belum diatur</span>
                                @endif
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.question-banks.edit', $qb) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button class="md-icon-btn red" title="Hapus"
                                    onclick="openDeleteQbModal('{{ route('admin.question-banks.destroy', $qb) }}', '{{ addslashes(Str::limit($qb->question_text, 60)) }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="md-empty-row">
                            <i class="ti ti-database-off"></i>
                            Bank soal masih kosong.
                            <a href="{{ route('admin.question-banks.create') }}" class="md-btn-primary mt-2" style="font-size:.75rem;padding:.35rem .8rem;">
                                <i class="ti ti-plus"></i> Buat Soal Sekarang
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($questionBanks->hasPages())
        <div class="md-card-footer">{{ $questionBanks->links() }}</div>
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
.qb-question-text {
    font-size: .8rem; font-weight: 600;
    color: var(--tblr-heading-color, #0f172a);
    line-height: 1.45; max-width: 480px;
}
.qb-pair-list { display: flex; flex-direction: column; gap: .25rem; }
.qb-pair {
    display: flex; align-items: center; gap: .4rem;
    background: var(--tblr-body-bg, #f4f6fa);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 6px; padding: .2rem .5rem;
    font-size: .72rem; max-width: 320px;
}
.qb-pair-q { font-weight: 600; color: var(--tblr-heading-color, #0f172a); flex: 1; }
.qb-pair-a { font-weight: 700; color: #0ca678; flex: 1; }
.qb-pair-mobile {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .7rem; font-weight: 600;
    background: var(--tblr-body-bg, #f4f6fa);
    border: 1px solid var(--tblr-border-color, #e2e8f0);
    border-radius: 5px; padding: .15rem .4rem; margin-bottom: .15rem;
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