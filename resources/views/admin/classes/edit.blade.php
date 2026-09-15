@extends('layouts.be.master')
@section('header_title', 'Edit Kelas — ' . $class->name)

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;">
            <i class="ti ti-school"></i>
        </div>
        <div>
            <h5 class="md-title">Edit Kelas: <span style="color:#8b5cf6;">{{ $class->name }}</span></h5>
        </div>
    </div>
    <a href="{{ route('admin.classes.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;"><i class="ti ti-door"></i></div>
        <h6 class="md-form-head-title">Informasi Kelas</h6>
    </div>
    <form method="POST" action="{{ route('admin.classes.update', $class) }}">
        @csrf @method('PUT')
        <div class="md-form-body">
            <div class="mb-3">
                <label for="name" class="md-form-label">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $class->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="md-form-footer">
            <a href="{{ route('admin.classes.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit"><i class="ti ti-device-floppy"></i> Update Kelas</button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
