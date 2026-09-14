@extends('layouts.be.master')

@section('header_title', 'Master Data — Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar Kelas Pembelajaran</h3>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Tambah Kelas Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="ti ti-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table w-100 mb-0 data-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 70px;">No</th>
                        <th>Nama Kelas</th>
                        <th>Jumlah Siswa Terdaftar</th>
                        <th class="pe-4 text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold">{{ $class->name }}</span>
                            </td>
                            <td>
                                <span class="badge badge-soft-success">
                                    <i class="ti ti-users me-1"></i> {{ $class->students_count }} Siswa
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="openDeleteClassModal('{{ route('admin.classes.destroy', $class) }}', '{{ addslashes($class->name) }}')" 
                                            title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada kelas terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($classes->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $classes->links() }}
        </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Kelas -->
<div class="modal fade" id="modalDeleteClass" tabindex="-1" aria-labelledby="modalDeleteClassLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ti ti-trash fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Hapus Kelas Ini?</h5>
                <p class="text-muted small mb-4" id="deleteModalClassText">Kelas yang dihapus tidak dapat dikembalikan.</p>
                <form id="deleteClassForm" method="POST" action="">
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
    function openDeleteClassModal(url, className) {
        const form = document.getElementById('deleteClassForm');
        form.action = url;
        const textEl = document.getElementById('deleteModalClassText');
        if (textEl && className) {
            textEl.innerText = `Anda akan menghapus kelas: "${className}". Tindakan ini tidak dapat dibatalkan.`;
        }
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteClass'));
        modal.show();
    }
</script>
@endsection
