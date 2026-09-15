@extends('layouts.be.master')
@section('header_title', 'Master Data — Tugas Siswa')

@section('content')
@include('admin._partials.master-data-styles')

<div class="col-md-12">
    {{-- Page Header --}}
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                <i class="ti ti-clipboard-list"></i>
            </div>
            <div>
                <h5 class="md-title">Daftar Tugas Siswa</h5>
            </div>
        </div>
        <a href="{{ route('admin.assignments.create') }}" class="md-btn-primary">
            <i class="ti ti-plus"></i>
            <span>Buat Tugas Baru</span>
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

    <div class="md-card">
        <div class="md-table-wrap">
            <table class="table table-hover md-table mb-0 data-table w-100">
                <thead>
                    <tr>
                        <th class="md-th-no text-center">No</th>
                        <th>Judul Tugas</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas Target</th>
                        <th>Batas Waktu (Deadline)</th>
                        <th class="text-center" style="width: 130px;">Pengumpulan</th>
                        <th class="md-th-action text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: .85rem;">
                                    {{ $assignment->title }}
                                </div>
                                <div class="text-muted" style="font-size: .73rem;">
                                    {{ Str::limit(strip_tags($assignment->description), 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="md-badge blue">
                                    {{ $assignment->subject->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="md-badge teal">
                                    {{ $assignment->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($assignment->due_date)
                                    @php $isPast = now()->greaterThan($assignment->due_date); @endphp
                                    <span class="md-badge {{ $isPast ? 'rose' : 'teal' }}">
                                        <i class="ti ti-calendar"></i> {{ $assignment->due_date->format('d M Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: .75rem;">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.submissions.index', ['assignment_id' => $assignment->id]) }}" 
                                   class="md-badge info text-decoration-none" style="background:rgba(8,145,178,.1);color:#0891b2;border:1px solid rgba(8,145,178,.2);">
                                    <i class="ti ti-users"></i> {{ $assignment->submissions_count ?? $assignment->submissions->count() }} Siswa
                                </a>
                            </td>
                            <td class="md-td-action text-center">
                                <div class="md-action-group text-center">
                                    <a href="{{ route('admin.submissions.index', ['assignment_id' => $assignment->id]) }}" class="md-icon-btn teal" title="Lihat Pengumpulan Siswa">
                                        <i class="ti ti-users"></i>
                                    </a>
                                    <a href="{{ route('admin.assignments.edit', $assignment) }}" class="md-icon-btn blue" title="Edit Tugas">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="md-icon-btn red" title="Hapus Tugas"
                                            onclick="openDeleteAssignmentModal('{{ route('admin.assignments.destroy', $assignment) }}', '{{ addslashes($assignment->title) }}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="md-empty-row">
                                <i class="ti ti-clipboard-off"></i>
                                Belum ada tugas siswa yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteAssignment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Tugas Ini?</h6>
            <p class="md-modal-text" id="deleteModalAssignmentText">Tugas dan seluruh riwayat pengumpulan siswa akan dihapus permanen.</p>
            <form id="deleteAssignmentForm" method="POST" action="">
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

<script>
function openDeleteAssignmentModal(url, title) {
    document.getElementById('deleteAssignmentForm').action = url;
    document.getElementById('deleteModalAssignmentText').innerText = `Anda akan menghapus tugas: "${title}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteAssignment')).show();
}
</script>
@endsection