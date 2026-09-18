@extends('layouts.be.master')
@section('header_title', 'Edit Role — ' . ucfirst($role->name))

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(32,107,196,.1);color:#206bc4;">
            <i class="ti ti-shield-check"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Role: <span style="color:#3368A0;">{{ ucfirst($role->name) }}</span></h5>
        </div>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(32,107,196,.1);color:#206bc4;"><i class="ti ti-shield"></i></div>
        <h6 class="md-form-head-title">Informasi Role</h6>
    </div>
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf @method('PUT')
        <div class="md-form-body">
            <div class="mb-3">
                <label for="name" class="md-form-label">Nama Role <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $role->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="md-form-footer">
            <a href="{{ route('admin.roles.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit"><i class="ti ti-device-floppy"></i> Update Role</button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
