@extends('layouts.be.master')
@section('header_title', 'Master Data — Bank Materi')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Bank Materi Pembelajaran</h5>
        </div>
    </div>
    <a href="{{ route('admin.material-banks.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Master Materi</span>
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
    <form method="GET" action="{{ route('admin.material-banks.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari judul materi..." value="{{ request('search') }}">
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
                <a href="{{ route('admin.material-banks.index') }}" class="md-btn-secondary" title="Reset Filter"><i class="ti ti-refresh"></i></a>
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
                    <th>Judul &amp; Tipe Materi</th>
                    <th>Mata Pelajaran</th>
                    <th class="d-none d-md-table-cell">Lampiran / Media</th>
                    <th class="d-none d-lg-table-cell">Tanggal Buat</th>
                    <th class="md-th-action text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materialBanks as $mb)
                    @php
                        $no = ($materialBanks->currentPage() - 1) * $materialBanks->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="md-td-no text-center">{{ $no }}</td>
                        <td>
                            <div class="fw-bold text-dark heading-custom">{{ $mb->title }}</div>
                            <div class="small text-muted-custom mt-1">
                                @if($mb->video_url)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1"><i class="ti ti-brand-youtube"></i> Video YouTube</span>
                                @endif
                                @if($mb->document_path)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle me-1"><i class="ti ti-file-text"></i> File Dokumen</span>
                                @endif
                                @if($mb->content)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="ti ti-article"></i> Konten Teks</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($mb->subject)
                                <span class="md-badge blue"><i class="ti ti-book"></i> {{ $mb->subject->name }}</span>
                            @else
                                <span class="md-badge muted">Umum / Semua</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($mb->document_path)
                                <a href="{{ asset('storage/' . $mb->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 style-font-size-xs" style="font-size:0.75rem;">
                                    <i class="ti ti-paperclip"></i> Lihat File
                                </a>
                            @elseif($mb->video_url)
                                <a href="{{ $mb->video_url }}" target="_blank" class="btn btn-sm btn-outline-danger py-0 px-2 style-font-size-xs" style="font-size:0.75rem;">
                                    <i class="ti ti-brand-youtube"></i> Buka Link
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="text-muted small">{{ $mb->created_at ? $mb->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </td>
                        <td class="md-td-action text-center">
                            <div class="md-action-group justify-content-center">
                                <a href="{{ route('admin.material-banks.edit', $mb) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button type="button" class="md-icon-btn red btn-delete-mb" 
                                        data-url="{{ route('admin.material-banks.destroy', $mb) }}" 
                                        data-title="{{ $mb->title }}" 
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
                                    <i class="ti ti-books"></i>
                                </div>
                                <h6 class="md-empty-title fw-bold">Belum Ada Master Bank Materi</h6>
                                <p class="md-empty-desc text-muted small">
                                    @if(request('search') || request('subject_id'))
                                        Tidak ada materi yang sesuai dengan pencarian/filter Anda.
                                    @else
                                        Tambahkan master materi ke Bank Materi agar dapat digunakan kembali secara fleksibel di berbagai kelas.
                                    @endif
                                </p>
                                <a href="{{ route('admin.material-banks.create') }}" class="md-btn-primary mt-2">
                                    <i class="ti ti-plus"></i> Tambah Master Materi
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($materialBanks->hasPages())
        <div class="md-card-footer p-3">{{ $materialBanks->links() }}</div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Bank Materi -->
<div class="modal fade" id="modalDeleteMB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content p-3 text-center">
            <div class="md-modal-icon danger mb-3" style="background:rgba(225,29,72,.1);color:#e11d48;width:52px;height:52px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                <i class="ti ti-trash"></i>
            </div>
            <h6 class="md-modal-title fw-bold">Hapus Master Materi?</h6>
            <p class="md-modal-text mb-3 text-muted small">
                Apakah Anda yakin ingin menghapus <strong id="deleteTargetTitle"></strong> dari Bank Materi?
            </p>

            <form id="formDeleteMB" method="POST" action="">
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
        var deleteModalEl = document.getElementById('modalDeleteMB');
        var formDeleteMB = document.getElementById('formDeleteMB');
        var targetTitleEl = document.getElementById('deleteTargetTitle');

        $(document).on('click', '.btn-delete-mb', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var title = $(this).data('title') || '';

            if (formDeleteMB) formDeleteMB.action = url;
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
