@extends('layouts.be.master')
@section('header_title', 'Master Data — Koreksi & Penilaian Tugas Siswa')

@section('content')

    {{-- Page Header --}}
    <div class="md-page-header mb-4">
        <div class="md-page-title">
            <div class="md-page-icon" style="background:rgba(12,166,120,.1);color:#0ca678;">
                <i class="ti ti-checklist"></i>
            </div>
            <div>
                <h5 class="md-title">Koreksi &amp; Penilaian Tugas Siswa</h5>
            </div>
        </div>
    </div>

    {{-- Flash Alert --}}
    @if (session('success'))
        <div class="md-alert success mb-4">
            <i class="ti ti-check-circle"></i> {{ session('success') }}
            <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div class="md-alert danger mb-4">
            <i class="ti ti-alert-triangle"></i> {{ session('error') }}
            <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
        </div>
    @endif

    {{-- Filter & Search Bar --}}
    <div class="md-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="row g-2 align-items-center">
            <!-- Search Input -->
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0"
                        placeholder="Cari nama siswa, email, atau tugas..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Filter Tugas -->
            <div class="col-12 col-sm-6 col-md-3">
                <select name="assignment_id" class="form-select form-select-sm select2" onchange="this.form.submit()">
                    <option value="">-- Semua Judul Tugas --</option>
                    @foreach ($assignments as $asg)
                        <option value="{{ $asg->id }}" {{ request('assignment_id') == $asg->id ? 'selected' : '' }}>
                            {{ $asg->title }} ({{ $asg->schoolClass->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Nilai -->
            <div class="col-6 col-sm-3 col-md-2">
                <select name="status" class="form-select form-select-sm select2" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="ungraded" {{ request('status') === 'ungraded' ? 'selected' : '' }}>Belum Dinilai</option>
                    <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="col-6 col-sm-3 col-md-3 d-flex gap-2">
                <button type="submit" class="md-btn-primary flex-fill justify-content-center"
                    style="padding:.38rem .8rem;font-size:.78rem;">
                    <i class="ti ti-filter"></i> Filter
                </button>
                @if (request('search') || request('assignment_id') || request('class_id') || request('status'))
                    <a href="{{ route('admin.submissions.index') }}" class="md-btn-secondary"
                        style="padding:.38rem .7rem;font-size:.78rem;" title="Reset Filter">
                        <i class="ti ti-refresh"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Submissions Table Card --}}
    <div class="md-card">
        <div class="md-table-wrap">
            <table class="table table-hover md-table mb-0">
                <thead>
                    <tr>
                        <th class="md-th-no text-center">No</th>
                        <th>Nama Siswa</th>
                        <th class="d-none d-md-table-cell">Judul Tugas</th>
                        <th class="d-none d-sm-table-cell">Mata Pelajaran &amp; Kelas</th>
                        <th class="d-none d-lg-table-cell">Waktu Pengumpulan</th>
                        <th class="text-center" style="width: 130px;">Status / Nilai</th>
                        <th class="md-th-action text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        @php
                            $no = ($submissions->currentPage() - 1) * $submissions->perPage() + $loop->iteration;
                        @endphp
                        <tr>
                            <td class="md-td-no text-center">{{ $no }}</td>

                            {{-- Nama Siswa --}}
                            <td>
                                <div class="fw-bold text-dark" style="font-size:.84rem;">
                                    {{ $sub->student->name ?? '-' }}
                                </div>
                                <div class="text-muted" style="font-size:.73rem;">
                                    {{ $sub->student->email ?? '-' }}
                                </div>

                                {{-- Mobile summary --}}
                                <div class="d-md-none mt-1 d-flex flex-wrap gap-1">
                                    <span class="md-badge blue">{{ $sub->assignment->title ?? '-' }}</span>
                                    <span class="md-badge teal">{{ $sub->assignment->schoolClass->name ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Judul Tugas --}}
                            <td class="d-none d-md-table-cell">
                                <div class="fw-semibold text-dark" style="font-size:.82rem;">
                                    {{ $sub->assignment->title ?? '-' }}
                                </div>
                            </td>

                            {{-- Mata Pelajaran & Kelas --}}
                            <td class="d-none d-sm-table-cell">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="md-badge blue">
                                        {{ $sub->assignment->subject->name ?? '-' }}
                                    </span>
                                    <span class="md-badge teal">
                                        {{ $sub->assignment->schoolClass->name ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Waktu Pengumpulan --}}
                            <td class="d-none d-lg-table-cell">
                                <div class="text-dark fw-semibold" style="font-size:.78rem;">
                                    <i class="ti ti-clock me-1 text-primary"></i>
                                    {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y H:i') : '-' }}
                                </div>
                            </td>

                            {{-- Status / Nilai --}}
                            <td class="text-center">
                                @if (!is_null($sub->grade))
                                    <span class="md-badge teal fw-bold" style="font-size:.78rem;padding:.28rem .65rem;">
                                        <i class="ti ti-check"></i> {{ number_format($sub->grade, 1) }} <span
                                            class="fw-normal text-muted" style="font-size:.68rem;">/ 100</span>
                                    </span>
                                @else
                                    <span class="md-badge amber">
                                        <i class="ti ti-clock"></i> Belum Dinilai
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="md-td-action text-center">
                                <div class="md-action-group text-center">
                                    <a href="{{ route('admin.submissions.show', $sub) }}" class="md-icon-btn blue"
                                        title="{{ is_null($sub->grade) ? 'Koreksi & Beri Nilai' : 'Lihat & Edit Nilai' }}">
                                        <i class="ti ti-pencil-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="md-empty-row">
                                <i class="ti ti-file-off"></i>
                                Belum ada pengumpulan tugas yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($submissions->hasPages())
            <div class="md-card-footer">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    @include('admin._partials.master-data-styles')
@endsection
