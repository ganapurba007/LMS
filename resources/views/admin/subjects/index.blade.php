@extends('layouts.be.master')
@section('header_title', 'Master Data — Mata Pelajaran')

@section('content')

<div class="md-page-header mb-4">
    <div class="md-page-title">
        <div class="md-page-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <h5 class="md-title">Mata Pelajaran</h5>
        </div>
    </div>
    <a href="{{ route('admin.subjects.create') }}" class="md-btn-primary">
        <i class="ti ti-plus"></i>
        <span>Tambah Mapel</span>
    </a>
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
                    <th class="md-th-no text-center">No</th>
                    <th>Mata Pelajaran</th>
                    <th class="d-none d-md-table-cell">Guru Pengampu</th>
                    <th class="d-none d-lg-table-cell">Konten</th>
                    <th class="md-th-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr>
                        <td class="md-td-no text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="md-row-name">
                                <div class="md-row-icon" style="background:rgba(8,145,178,.1);color:#0891b2;">
                                    <i class="ti ti-book"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.82rem;">{{ $subject->name }}</div>
                                    {{-- Guru visible only on mobile --}}
                                    <div class="d-md-none mt-1">
                                        @forelse($subject->instructors as $guru)
                                            <span class="md-badge blue" style="font-size:.58rem;">{{ $guru->name }}</span>
                                        @empty
                                            <span style="font-size:.68rem;color:var(--tblr-text-muted,#64748b);">Belum ada guru</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                                @forelse($subject->instructors as $guru)
                                    <span class="md-badge blue"><i class="ti ti-user"></i> {{ $guru->name }}</span>
                                @empty
                                    <span style="font-size:.74rem;color:var(--tblr-text-muted,#64748b);">Belum ada guru pengampu</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <div class="md-stat-row">
                                <span class="md-stat"><i class="ti ti-file-text"></i> {{ $subject->materials_count }} Materi</span>
                                <span class="md-stat"><i class="ti ti-clipboard"></i> {{ $subject->assignments_count }} Tugas</span>
                                <span class="md-stat"><i class="ti ti-help-circle"></i> {{ $subject->quizzes_count }} Kuis</span>
                            </div>
                        </td>
                        <td class="md-td-action">
                            <div class="md-action-group">
                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="md-icon-btn blue" title="Edit / Alokasi Guru">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <button class="md-icon-btn red" title="Hapus"
                                    onclick="openDeleteSubjectModal('{{ route('admin.subjects.destroy', $subject) }}', '{{ addslashes($subject->name) }}')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="md-empty-row">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap blue">
                                    <i class="ti ti-books-off"></i>
                                </div>
                                <div class="md-empty-title">Belum Ada Mata Pelajaran</div>
                                <div class="md-empty-desc">
                                    Daftarkan mata pelajaran dan alokasikan guru pengampu untuk mengelola materi dan kuis kelas.
                                </div>
                                <div class="md-empty-action">
                                    <a href="{{ route('admin.subjects.create') }}" class="md-btn-primary" style="font-size:.78rem;padding:.35rem .85rem;">
                                        <i class="ti ti-plus"></i> Tambah Mata Pelajaran
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
        <div class="md-card-footer">{{ $subjects->links() }}</div>
    @endif
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="modalDeleteSubject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="md-modal-content">
            <div class="md-modal-icon danger"><i class="ti ti-trash"></i></div>
            <h6 class="md-modal-title">Hapus Mata Pelajaran?</h6>
            <p class="md-modal-text" id="deleteModalSubjectText">Mata pelajaran yang dihapus tidak dapat dikembalikan.</p>
            <form id="deleteSubjectForm" method="POST" action="">
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
function openDeleteSubjectModal(url, name) {
    document.getElementById('deleteSubjectForm').action = url;
    document.getElementById('deleteModalSubjectText').innerText = `Anda akan menghapus mata pelajaran: "${name}". Tindakan ini tidak dapat dibatalkan.`;
    new bootstrap.Modal(document.getElementById('modalDeleteSubject')).show();
}
</script>
@endsection
