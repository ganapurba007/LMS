@extends('layouts.be.master')
@section('header_title', 'Edit User — ' . $user->name)

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        @php
            $pal  = ['#206bc4','#0ca678','#d97706','#9333ea','#e11d48','#0891b2'];
            $uBg  = $pal[abs(crc32($user->name)) % count($pal)];
            $init = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($user->name)), 0, 2))));
        @endphp
        <div class="md-user-avatar" style="background:{{ $uBg }};width:40px;height:40px;font-size:.85rem;border-radius:10px;">{{ $init }}</div>
        <div>
            <h5 class="md-title">Edit Pengguna</h5>
        </div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(12,166,120,.1);color:#0ca678;"><i class="ti ti-user-edit"></i></div>
        <h6 class="md-form-head-title">Data Pengguna</h6>
    </div>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="md-form-body">
            <div class="mb-3">
                <label for="name" class="md-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="md-form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="nip" class="md-form-label">NIP <span class="text-danger">*</span></label>
                <input type="text" id="nip" name="nip"
                    class="form-control @error('nip') is-invalid @enderror"
                    value="{{ old('nip', $user->nip) }}" placeholder="123456789...">
                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="md-form-footer">
            <a href="{{ route('admin.users.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit"><i class="ti ti-device-floppy"></i> Update Pengguna</button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
