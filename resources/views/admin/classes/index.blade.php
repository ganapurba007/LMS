@extends('layouts.be.master')
@section('header_title', 'Master Data — Kelas')

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;">
            <i class="ti ti-school"></i>
        </div>
        <div>
            <h5 class="md-title">Daftar Kelas</h5>
        </div>
    </div>
    <a href="{{ route('admin.classes.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Kelas</span>
    </a>
</div>

@if(session('success'))
    <div class="md-alert success mb-4">
        <i class="ti ti-check-circle"></i> {{ session('success') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0 data-table">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Nama Kelas</th>
                    <th>Siswa</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="md-row-name">
                                <div class="md-row-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;">
                                    <i class="ti ti-door"></i>
                                </div>
                                <span class="fw-semibold">{{ $class->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="md-badge teal">
                                <i class="ti ti-users"></i> {{ $class->students_count }} Siswa
                            </span>
                        </td>
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.classes.edit', $class) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button class="md-icon-btn red" title="Hapus"
                                    onclick="openDeleteClassModal('{{ route('admin.classes.destroy', $class) }}', '{{ addslashes($class->name) }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="md-empty-row">
                            <i class="ti ti-door-off"></i>
                            Belum ada kelas terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($classes->hasPages())
        <div class="md-card-footer">{{ $classes->links() }}</div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Kelas Ini?</h6>
            <p class="md-modal-text" id="deleteModalClassText">Kelas yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteClassForm" method="POST" action="">
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

<script>
function openDeleteClassModal(url, name) {
    document.getElementById('deleteClassForm').action = url;
    document.getElementById('deleteModalClassText').innerText = `Anda akan menghapus kelas: "${name}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteClass')).show();
}
</script>
@endsection
