@extends('layouts.be.master')
@section('header_title', 'Edit Mata Pelajaran — ' . $subject->name)

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Edit: <span style="color:#0891b2;">{{ $subject->name }}</span></h5>
        </div>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="md-btn-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="md-form-card">
    <div class="md-form-head">
        <div class="md-form-head-icon" style="background:rgba(8,145,178,.1);color:#0891b2;"><i class="ti ti-book"></i></div>
        <h6 class="md-form-head-title">Informasi Mata Pelajaran</h6>
    </div>
    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
        @csrf @method('PUT')
        <div class="md-form-body">

            <div class="mb-4">
                <label for="name" class="md-form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $subject->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="md-form-label">Guru Pengampu</label>
                <div class="md-check-list">
                    @forelse($gurus as $guru)
                        @php
                            $checked = is_array(old('instructor_ids'))
                                ? in_array($guru->id, old('instructor_ids'))
                                : in_array($guru->id, $assignedInstructorIds);
                        @endphp
                        <label class="md-check-item" for="guru_{{ $guru->id }}">
                            <input type="checkbox" class="form-check-input m-0"
                                id="guru_{{ $guru->id }}" name="instructor_ids[]"
                                value="{{ $guru->id }}" {{ $checked ? 'checked' : '' }}>
                            <div>
                                <div class="md-check-label">{{ $guru->name }}</div>
                                <div class="md-check-sub">{{ $guru->email }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="p-3 text-center" style="font-size:.78rem;color:var(--tblr-text-muted,#64748b);">
                            Belum ada akun guru terdaftar.
                        </div>
                    @endforelse
                </div>
                <div class="md-form-hint">Pilih satu atau beberapa guru yang mengampu mata pelajaran ini.</div>
            </div>

        </div>
        <div class="md-form-footer">
            <a href="{{ route('admin.subjects.index') }}" class="md-btn-light">Batal</a>
            <button type="submit" class="md-btn-submit"><i class="ti ti-device-floppy"></i> Update Mata Pelajaran</button>
        </div>
    </form>
</div>

@include('admin._partials.master-data-styles')
@endsection
