@extends('layouts.be.master')
@section('header_title', 'Master Data — User')

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;">
            <i class="ti ti-users"></i>
        </div>
        <div>
            <h5 class="md-title">Daftar Pengguna</h5>
        </div>
    </div>
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
                    <th class="text-center">No</th>
                    <th>Pengguna</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th class="d-none d-lg-table-cell">NIP</th>
                    <th>Role</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $pal   = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2'];
                        $uBg   = $pal[abs(crc32($user->name)) % count($pal)];
                        $init  = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($user->name)), 0, 2))));
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="md-row-name">
                                <div class="md-user-avatar" style="background:{{ $uBg }};">{{ $init }}</div>
                                <div>
                                    <div class="md-user-name">{{ $user->name }}</div>
                                    <div class="md-user-email d-md-none">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span style="font-size:.78rem;color:var(--tblr-text-muted,#64748b);">{{ $user->email }}</span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            @if($user->nip)
                                <span class="font-monospace" style="font-size:.78rem;">{{ $user->nip }}</span>
                            @else
                                <span class="md-stat">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->isGuru())
                                <span class="md-badge blue"><i class="ti ti-school"></i> Guru</span>
                            @elseif($user->isSiswa())
                                <span class="md-badge teal"><i class="ti ti-user-check"></i> Siswa</span>
                            @else
                                <span class="md-badge muted">{{ ucfirst($user->role->name ?? 'None') }}</span>
                            @endif
                        </td>
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.users.edit', $user) }}" class="md-icon-btn blue" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row">
                            <i class="ti ti-users-off"></i>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="md-card-footer">{{ $users->links() }}</div>
    @endif
</div>

@include('admin._partials.master-data-styles')
@endsection
