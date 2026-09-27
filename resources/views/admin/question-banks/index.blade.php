@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Soal')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-folders"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Soal</h5>
            <div class="text-muted small">Kelola butir soal evaluasi dan kuis yang dikelompokkan per 10 soal dalam folder terstruktur.</div>
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
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari isi pertanyaan soal..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Format Soal --</option>
                <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>Benar / Salah</option>
                <option value="matching" {{ request('type') == 'matching' ? 'selected' : '' }}>Menjodohkan</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="md-btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
            @if(request('search') || request('type') || request('folder'))
                <a href="{{ route('admin.question-banks.index') }}" class="md-btn-secondary" title="Reset Filter"><i class="ti ti-refresh"></i></a>
            @endif
        </div>
    </form>
</div>

@if($totalQuestions > 0)
    {{-- Folder Navigation & Quick Filter Bar --}}
    <div class="md-card mb-4 p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="qb-folder-stat-badge">
                    <i class="ti ti-folder text-cyan fs-5"></i>
                    <span><strong>{{ $totalFolders }}</strong> Folder</span>
                </div>
                <div class="qb-folder-stat-badge">
                    <i class="ti ti-help-circle text-teal fs-5"></i>
                    <span><strong>{{ $totalQuestions }}</strong> Total Soal</span>
                </div>
                <span class="text-muted small d-none d-sm-inline">(10 soal / folder)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" onclick="expandAllFolders()" style="font-size: .78rem; font-weight: 600;">
                    <i class="ti ti-arrows-maximize"></i> Buka Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" onclick="collapseAllFolders()" style="font-size: .78rem; font-weight: 600;">
                    <i class="ti ti-arrows-minimize"></i> Tutup Semua
                </button>
            </div>
        </div>

        {{-- Folder Tabs / Pills --}}
        <div class="qb-folder-tabs-wrapper">
            <button type="button" class="qb-folder-tab {{ empty(request('folder')) ? 'active' : '' }}" onclick="filterFolderTab('all', this)">
                <i class="ti ti-folders"></i> Semua Folder ({{ $totalQuestions }})
            </button>
            @foreach($folders as $f)
                <button type="button" class="qb-folder-tab {{ request('folder') == $f->number ? 'active' : '' }}" onclick="filterFolderTab('{{ $f->number }}', this)">
                    <i class="ti ti-folder"></i> {{ $f->name }}
                    <span class="qb-tab-count">{{ $f->count }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Folder Group Sections --}}
    <div class="qb-folders-container d-flex flex-column gap-3">
        @foreach($folders as $folder)
            <div class="md-card qb-folder-card" id="folder-card-{{ $folder->number }}" data-folder-num="{{ $folder->number }}">
                {{-- Folder Header --}}
                <div class="qb-folder-header d-flex align-items-center justify-content-between p-3" onclick="toggleFolder({{ $folder->number }})">
                    <div class="d-flex align-items-center gap-2.5 flex-wrap">
                        <div class="qb-folder-icon-wrap">
                            <i class="ti ti-folder-filled"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="qb-folder-title mb-0">{{ $folder->name }}</h6>
                                <span class="badge bg-cyan-subtle text-cyan border border-cyan-subtle" style="font-size: .75rem; font-weight: 700;">
                                    {{ $folder->range_label }}
                                </span>
                            </div>
                            <div class="qb-folder-subtitle text-muted mt-0.5" style="font-size: .76rem;">
                                Berisi <strong>{{ $folder->count }} butir soal</strong>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-none d-md-flex align-items-center gap-1 me-2">
                            @if($folder->mc_count > 0)
                                <span class="md-badge qb-badge-mc" style="font-size: .7rem; padding: .2rem .45rem;">
                                    {{ $folder->mc_count }} PG
                                </span>
                            @endif
                            @if($folder->tf_count > 0)
                                <span class="md-badge qb-badge-tf" style="font-size: .7rem; padding: .2rem .45rem;">
                                    {{ $folder->tf_count }} B/S
                                </span>
                            @endif
                            @if($folder->matching_count > 0)
                                <span class="md-badge qb-badge-matching" style="font-size: .7rem; padding: .2rem .45rem;">
                                    {{ $folder->matching_count }} Jodohkan
                                </span>
                            @endif
                        </div>
                        <span class="qb-toggle-btn">
                            <i class="ti ti-chevron-down qb-chevron" id="chevron-{{ $folder->number }}"></i>
                        </span>
                    </div>
                </div>

                {{-- Folder Body (Question Table) --}}
                <div class="qb-folder-body" id="folder-body-{{ $folder->number }}">
                    <div class="md-table-wrap border-top">
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
                                @foreach($folder->questions as $qb)
                                    @php
                                        $globalNo = (($folder->number - 1) * 10) + $loop->iteration;
                                    @endphp
                                    <tr>
                                        <td class="md-td-no text-center">
                                            <span class="badge bg-light text-dark border fw-bold" style="font-size: .76rem;">{{ $globalNo }}</span>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- Empty State --}}
    <div class="md-card">
        <div class="md-empty-state py-5 text-center">
            <div class="md-empty-icon-wrap teal mb-3" style="width: 60px; height: 60px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1.8rem;">
                <i class="ti ti-folders-off"></i>
            </div>
            <h5 class="md-empty-title fw-bold mb-1">Bank Soal Masih Kosong</h5>
            <p class="md-empty-desc text-muted mb-3" style="max-width: 500px; margin: 0 auto; font-size: .84rem;">
                @if(request('search') || request('type'))
                    Tidak ada butir soal yang sesuai dengan filter atau kata kunci pencarian Anda.
                @else
                    Belum ada butir soal di Bank Soal. Buat soal secara manual atau impor dari dokumen Word (.docx) / Markdown (.md) untuk langsung dikelompokkan dalam folder otomatis.
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
    </div>
@endif

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
/* ── FOLDER STYLES ── */
.qb-folder-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .75rem;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: .8rem;
    color: #334155;
    font-weight: 500;
}
.qb-folder-tabs-wrapper {
    display: flex;
    align-items: center;
    gap: .5rem;
    overflow-x: auto;
    padding-bottom: .25rem;
}
.qb-folder-tabs-wrapper::-webkit-scrollbar {
    height: 4px;
}
.qb-folder-tabs-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.qb-folder-tab {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .4rem .85rem;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 700;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all .16s ease;
    white-space: nowrap;
}
.qb-folder-tab:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.qb-folder-tab.active {
    background: #0891b2;
    color: #ffffff;
    border-color: #0891b2;
    box-shadow: 0 2px 8px rgba(8, 145, 178, 0.25);
}
.qb-folder-tab .qb-tab-count {
    font-size: .7rem;
    background: rgba(0,0,0,0.08);
    padding: .1rem .4rem;
    border-radius: 12px;
}
.qb-folder-tab.active .qb-tab-count {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

.qb-folder-card {
    border-radius: 10px;
    overflow: hidden;
    transition: box-shadow .2s ease;
}
.qb-folder-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}
.qb-folder-header {
    cursor: pointer;
    background: #fafbfc;
    transition: background .15s ease;
    user-select: none;
}
.qb-folder-header:hover {
    background: #f1f5f9;
}
.qb-folder-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    box-shadow: 0 2px 6px rgba(6, 182, 212, 0.25);
    flex-shrink: 0;
}
.qb-folder-title {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
}
.qb-toggle-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #64748b;
    transition: all .2s ease;
}
.qb-folder-header:hover .qb-toggle-btn {
    border-color: #0891b2;
    color: #0891b2;
}
.qb-chevron {
    transition: transform .25s ease;
}
.qb-chevron.rotated {
    transform: rotate(180deg);
}
.qb-folder-body {
    transition: max-height .3s ease-in-out;
}

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
[data-theme="dark"] .qb-folder-stat-badge {
    background: rgba(30, 41, 59, 0.7);
    border-color: #334155;
    color: #cbd5e1;
}
[data-theme="dark"] .qb-folder-tab {
    background: rgba(30, 41, 59, 0.6);
    border-color: #334155;
    color: #94a3b8;
}
[data-theme="dark"] .qb-folder-tab:hover {
    background: rgba(51, 65, 85, 0.8);
    color: #f8fafc;
}
[data-theme="dark"] .qb-folder-tab.active {
    background: #0891b2;
    color: #ffffff;
    border-color: #0891b2;
}
[data-theme="dark"] .qb-folder-header {
    background: rgba(30, 41, 59, 0.5);
}
[data-theme="dark"] .qb-folder-header:hover {
    background: rgba(51, 65, 85, 0.5);
}
[data-theme="dark"] .qb-folder-title {
    color: #f8fafc;
}
[data-theme="dark"] .qb-toggle-btn {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}
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
function toggleFolder(folderNum) {
    const body = document.getElementById(`folder-body-${folderNum}`);
    const chevron = document.getElementById(`chevron-${folderNum}`);
    if (!body) return;

    if (body.style.display === 'none') {
        body.style.display = 'block';
        if (chevron) chevron.classList.remove('rotated');
    } else {
        body.style.display = 'none';
        if (chevron) chevron.classList.add('rotated');
    }
}

function expandAllFolders() {
    document.querySelectorAll('.qb-folder-body').forEach(el => el.style.display = 'block');
    document.querySelectorAll('.qb-chevron').forEach(el => el.classList.remove('rotated'));
}

function collapseAllFolders() {
    document.querySelectorAll('.qb-folder-body').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.qb-chevron').forEach(el => el.classList.add('rotated'));
}

function filterFolderTab(folderNum, tabEl) {
    // Update active tab styling
    document.querySelectorAll('.qb-folder-tab').forEach(el => el.classList.remove('active'));
    if (tabEl) tabEl.classList.add('active');

    // Show/hide folder cards
    const cards = document.querySelectorAll('.qb-folder-card');
    cards.forEach(card => {
        if (folderNum === 'all' || card.getAttribute('data-folder-num') === String(folderNum)) {
            card.style.display = '';
            // Auto open the selected folder
            const cardNum = card.getAttribute('data-folder-num');
            const body = document.getElementById(`folder-body-${cardNum}`);
            const chevron = document.getElementById(`chevron-${cardNum}`);
            if (body) body.style.display = 'block';
            if (chevron) chevron.classList.remove('rotated');
        } else {
            card.style.display = 'none';
        }
    });
}

function openDeleteQbModal(url, questionText) {
    document.getElementById('deleteQbForm').action = url;
    document.getElementById('deleteModalQbText').innerText = `Anda akan menghapus: "${questionText}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteQuestionBank')).show();
}
</script>
@endsection