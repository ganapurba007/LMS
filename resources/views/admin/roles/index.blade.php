@extends('layouts.be.master')

@section('header_title', 'Master Data — Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Role</h3>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah Role Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="ti ti-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="ti ti-alert-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table w-100 mb-0 data-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 80px;">No</th>
                        <th>Nama Role</th>
                        <th>Jumlah Pengguna</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold">{{ ucfirst($role->name) }}</span>
                            </td>
                            <td>
                                    <span class="badge badge-soft-success">{{ $role->users_count }} user</span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>

                                    @if(!in_array($role->name, ['guru', 'siswa']))
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
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
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada role terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($roles->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $roles->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Role -->
<div class="modal fade" id="modalDeleteRole" tabindex="-1" aria-labelledby="modalDeleteRoleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Role Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalRoleText">Role yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteRoleForm" method="POST" action="">
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
    function openDeleteRoleModal(url, roleName) {
        const form = document.getElementById('deleteRoleForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalRoleText');
        if (textEl && roleName) {
            textEl.innerText = `Anda akan menghapus role: "${roleName}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteRole'));
        modal.show();
    }
</script>
@endsection
