@extends('layouts.be.master')
@section('header_title', 'Master Data — Role')

@section('content')

{{-- Page Header --}}
<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-shield-check"></i>
        </div>
        <div>
            <h5 class="md-title">Daftar Role</h5>
        </div>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Role</span>
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

{{-- Table Card --}}
<div class="md-card">
    <div class="md-table-wrap">
        <table class="table table-hover md-table mb-0 data-table">
            <thead>
                <tr>
                    <th class="md-th-no text-center">No</th>
                    <th>Nama Role</th>
                    <th>Pengguna</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="md-td-no">{{ $loop->iteration }}</td>
                        <td>
                            <div class="md-row-name">
                                <div class="md-row-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
                                    <i class="ti ti-shield"></i>
                                </div>
                                <span class="fw-semibold">{{ ucfirst($role->name) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="md-badge teal">{{ $role->users_count }} user</span>
                        </td>
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                @if(!in_array($role->name, ['guru', 'siswa']))
                                    <button class="md-icon-btn red"
                                        onclick="openDeleteRoleModal('{{ route('admin.roles.destroy', $role) }}', '{{ addslashes($role->name) }}')"
                                        title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="md-empty-row">
                            <i class="ti ti-shield-off"></i>
                            Belum ada role terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($roles->hasPages())
        <div class="md-card-footer">{{ $roles->links() }}</div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger">
                <i class="ti ti-trash"></i>
            </div>
            <h6 class="md-modal-title">Hapus Role Ini?</h6>
            <p class="md-modal-text" id="deleteModalRoleText">Role yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteRoleForm" method="POST" action="">
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
function openDeleteRoleModal(url, name) {
    document.getElementById('deleteRoleForm').action = url;
    document.getElementById('deleteModalRoleText').innerText = `Anda akan menghapus role: "${name}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteRole')).show();
}
</script>
@endsection
