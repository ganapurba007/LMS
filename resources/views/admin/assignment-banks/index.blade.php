@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Tugas')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-notebook"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Tugas Siswa</h5>
        </div>
    </div>
    <a href="{{ route('admin.assignment-banks.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Master Tugas</span>
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
        <i class="ti ti-alert-circle"></i> {{ session('error') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

{{-- Filter Form --}}
<div class="md-card mb-4 p-3">
    <form method="GET" action="{{ route('admin.assignment-banks.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari judul tugas..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="subject_id" class="form-select select2" onchange="this.form.submit()">
                <option value="">-- Semua Mata Pelajaran --</option>
                @foreach($subjects as $subj)
                    <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                        {{ $subj->name }} ({{ $subj->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="md-btn-primary w-100"><i class="ti ti-filter"></i> Filter</button>
            @if(request('search') || request('subject_id'))
                <a href="{{ route('admin.assignment-banks.index') }}" class="md-btn-secondary" title="Reset Filter"><i class="ti ti-refresh"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Judul Tugas</th>
                    <th>Mata Pelajaran</th>
                    <th class="d-none d-md-table-cell">Deskripsi / Instukri</th>
                    <th class="d-none d-lg-table-cell">Tanggal Buat</th>
                    <th class="md-th-action text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignmentBanks as $ab)
                    @php
                        $no = ($assignmentBanks->currentPage() - 1) * $assignmentBanks->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no text-center">{{ $no }}</td>
                        <td>
                            <div class="fw-bold text-dark heading-custom">{{ $ab->title }}</div>
                        </td>
                        <td>
                            @if($ab->subject)
                                <span class="md-badge blue"><i class="ti ti-book"></i> {{ $ab->subject->name }}</span>
                            @else
                                <span class="md-badge muted">Umum / Semua</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($ab->description)
                                <span class="text-muted small text-truncate d-inline-block" style="max-width:320px;">
                                    {{ Str::limit(strip_tags($ab->description), 80) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="text-muted small">{{ $ab->created_at ? $ab->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </td>
                        <td class="md-td-action text-center">
                            <div class="md-action-group justify-content-center">
                                <a href="{{ route('admin.assignment-banks.edit', $ab) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="md-icon-btn red btn-delete-ab" 
                                        data-url="{{ route('admin.assignment-banks.destroy', $ab) }}" 
                                        data-title="{{ $ab->title }}" 
                                        title="Hapus">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row text-center py-5">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap blue mb-3" style="width:60px;height:60px;margin:0 auto;display:flex;align-items:center;justify-content:center;background:rgba(32,107,196,.1);color:#206bc4;border-radius:50%;font-size:1.5rem;">
                                    <i class="ti ti-notebook"></i>
                                </div>
                                <h6 class="md-empty-title fw-bold">Belum Ada Master Bank Tugas</h6>
                                <p class="md-empty-desc text-muted small">
                                    @if(request('search') || request('subject_id'))
                                        Tidak ada tugas yang sesuai dengan pencarian/filter Anda.
                                    @else
                                        Tambahkan master tugas ke Bank Tugas agar dapat diterbitkan dengan mudah ke berbagai kelas target.
                                    @endif
                                </p>
                                <a href="{{ route('admin.assignment-banks.create') }}" class="md-btn-primary mt-2">
                                    <i class="ti ti-plus"></i> Tambah Master Tugas
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($assignmentBanks->hasPages())
        <div class="md-card-footer p-3">{{ $assignmentBanks->links() }}</div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Bank Tugas -->
<div class="modal fade" id="modalDeleteAB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content p-3 text-center">
            <div class="md-modal-icon danger mb-3" style="background:rgba(225,29,72,.1);color:#e11d48;width:52px;height:52px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                <i class="ti ti-trash"></i>
            </div>
            <h6 class="md-modal-title fw-bold">Hapus Master Tugas?</h6>
            <p class="md-modal-text mb-3 text-muted small">
                Apakah Anda yakin ingin menghapus <strong id="deleteTargetTitle"></strong> dari Bank Tugas?
            </p>

            <form id="formDeleteAB" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="md-btn-danger">
                        <i class="ti ti-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    $(function () {
        var deleteModalEl = document.getElementById('modalDeleteAB');
        var formDeleteAB = document.getElementById('formDeleteAB');
        var targetTitleEl = document.getElementById('deleteTargetTitle');

        $(document).on('click', '.btn-delete-ab', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var title = $(this).data('title') || '';

            if (formDeleteAB) formDeleteAB.action = url;
            if (targetTitleEl) targetTitleEl.textContent = title;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $(deleteModalEl).modal('show');
            }
        });
    });
</script>
@endpush
