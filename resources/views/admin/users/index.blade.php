@extends('layouts.be.master')

@section('header_title', 'Master Data — User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Daftar User</h3>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="ti ti-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<!-- Users Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table w-100 mb-0 data-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 70px;">No</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Role</th>
                        <th class="pe-4 text-end no-sort" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-icon-box avatar-icon-primary me-3">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-muted">{{ $user->email }}</span></td>
                            <td>
                                @if($user->nip)
                                    <span class="font-monospace fw-medium">{{ $user->nip }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->isGuru())
                                    <span class="badge badge-soft-primary"><i class="ti ti-school me-1"></i> Guru</span>
                                @elseif($user->isSiswa())
                                    <span class="badge badge-soft-success"><i class="ti ti-user-check me-1"></i> Siswa</span>
                                @else
                                    <span class="badge badge-soft-secondary">{{ ucfirst($user->role->name ?? 'None') }}</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Assign Role / Edit">
                                    <i class="ti ti-edit me-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer d-flex justify-content-end py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
