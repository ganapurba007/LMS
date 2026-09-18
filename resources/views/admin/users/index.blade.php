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

@if(session('error'))
    <div class="md-alert danger mb-4">
        <i class="ti ti-alert-circle"></i> {{ session('error') }}
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
                                <button type="button" class="md-icon-btn amber btn-reset-password" 
                                        data-url="{{ route('admin.users.reset-password', $user) }}" 
                                        data-name="{{ $user->name }}" 
                                        title="Reset Password">
                                    <i class="ti ti-key"></i>
                                </button>
                                @if(auth()->id() !== $user->id && (!auth()->user()->isGuru() || $user->isSiswa()))
                                    <button type="button" class="md-icon-btn red btn-delete-user" 
                                            data-url="{{ route('admin.users.destroy', $user) }}" 
                                            data-name="{{ $user->name }}" 
                                            title="Hapus User">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="md-empty-row">
                            <div class="md-empty-state">
                                <div class="md-empty-icon-wrap blue">
                                    <i class="ti ti-users-off"></i>
                                </div>
                                <div class="md-empty-title">Tidak Ada Pengguna Ditemukan</div>
                                <div class="md-empty-desc">
                                    @if(request('search') || request('role_id'))
                                        Tidak ada data akun pengguna yang cocok dengan kriteria pencarian atau filter yang dipilih.
                                    @else
                                        Belum ada data pengguna yang terdaftar di dalam sistem LMS.
                                    @endif
                                </div>
                                @if(request('search') || request('role_id'))
                                    <div class="md-empty-action">
                                        <a href="{{ route('admin.users.index') }}" class="md-btn-secondary" style="font-size:.78rem;padding:.35rem .8rem;">
                                            <i class="ti ti-x"></i> Reset Filter
                                        </a>
                                    </div>
                                @endif
                            </div>
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

<!-- Modal Konfirmasi Reset Password -->
<div class="modal fade" id="modalResetConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-icon warning" style="background:rgba(245,158,11,.1);color:#d97706;">
                <i class="ti ti-key"></i>
            </div>
            <h6 class="md-modal-title">Reset Password Pengguna?</h6>
            <p class="md-modal-text mb-3">
                Password untuk <strong id="resetTargetName"></strong> akan direset menjadi karakter acak baru.
            </p>

            <form id="formResetPassword" method="POST" action="">
                @csrf
                <div class="md-modal-actions">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitReset" class="md-btn-primary" style="background:#d97706;border-color:#d97706;">
                        <i class="ti ti-key me-1"></i> Ya, Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hasil Reset Password -->
<div class="modal fade" id="modalPasswordResetSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-icon" style="background:rgba(12,166,120,.1);color:#0ca678;width:52px;height:52px;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                <i class="ti ti-circle-check"></i>
            </div>
            <h6 class="md-modal-title">Password Berhasil Direset</h6>
            <p class="md-modal-text mb-3">
                Password baru untuk <strong id="resetSuccessUserName">{{ session('reset_user_name', '') }}</strong>:
            </p>
            
            <div class="d-flex align-items-center justify-content-between p-2 mb-3 rounded border bg-light font-monospace" style="font-size: 0.95rem;">
                <span id="resetPasswordVal" class="fw-bold text-dark me-2">{{ session('reset_new_password', '') }}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 border-0" onclick="copyResetPassword()" title="Salin Password">
                    <i class="ti ti-copy" id="resetCopyIcon"></i>
                </button>
            </div>

            <div class="alert alert-info py-2 px-3 mb-3 text-start" style="font-size: 0.75rem;">
                <i class="ti ti-info-circle me-1"></i> Harap salin password ini sekarang. Password tidak akan ditampilkan lagi setelah modal ditutup.
            </div>

            <div class="md-modal-actions">
                <button type="button" class="md-btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus User -->
<div class="modal fade" id="modalDeleteConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content md-modal-content">
            <div class="md-modal-icon danger" style="background:rgba(225,29,72,.1);color:#e11d48;width:52px;height:52px;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                <i class="ti ti-trash"></i>
            </div>
            <h6 class="md-modal-title">Hapus Akun Pengguna?</h6>
            <p class="md-modal-text mb-3">
                Apakah Anda yakin ingin menghapus data akun <strong id="deleteTargetName"></strong>? Data yang dihapus tidak dapat dikembalikan.
            </p>

            <form id="formDeleteUser" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="md-modal-actions">
                    <button type="button" class="md-btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="md-btn-danger">
                        <i class="ti ti-trash me-1"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    function confirmResetPassword(actionUrl, userName) {
        var form = document.getElementById('formResetPassword');
        if (form) form.action = actionUrl;
        var nameEl = document.getElementById('resetTargetName');
        if (nameEl) nameEl.textContent = userName;
        var modalEl = document.getElementById('modalResetConfirm');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $(modalEl).modal('show');
        }
    }

    $(function () {
        var confirmModalEl = document.getElementById('modalResetConfirm');
        var successModalEl = document.getElementById('modalPasswordResetSuccess');
        var deleteModalEl = document.getElementById('modalDeleteConfirm');
        var formReset = document.getElementById('formResetPassword');
        var formDelete = document.getElementById('formDeleteUser');
        var targetNameEl = document.getElementById('resetTargetName');
        var deleteTargetNameEl = document.getElementById('deleteTargetName');
        var successUserNameEl = document.getElementById('resetSuccessUserName');
        var resetPasswordValEl = document.getElementById('resetPasswordVal');
        var btnSubmitReset = document.getElementById('btnSubmitReset');

        @if(session('reset_new_password'))
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(successModalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $(successModalEl).modal('show');
            }
        @endif

        // Delegated click handler for delete user buttons
        $(document).on('click', '.btn-delete-user', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name') || '';

            if (formDelete) formDelete.action = url;
            if (deleteTargetNameEl) deleteTargetNameEl.textContent = name;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $(deleteModalEl).modal('show');
            }
        });

        // Delegated click handler for reset password buttons (works with DataTables redraw)
        $(document).on('click', '.btn-reset-password', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name') || '';

            if (formReset) formReset.action = url;
            if (targetNameEl) targetNameEl.textContent = name;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(confirmModalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $(confirmModalEl).modal('show');
            }
        });

        // AJAX submit for reset password form
        if (formReset) {
            $(formReset).on('submit', function (e) {
                e.preventDefault();
                var actionUrl = formReset.action;
                if (!actionUrl) return;

                var origBtnContent = btnSubmitReset ? btnSubmitReset.innerHTML : '';
                if (btnSubmitReset) {
                    btnSubmitReset.disabled = true;
                    btnSubmitReset.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
                }

                var formData = new FormData(formReset);

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (data) {
                        if (successUserNameEl) successUserNameEl.textContent = data.user_name || '';
                        if (resetPasswordValEl) resetPasswordValEl.textContent = data.new_password || '';

                        var shown = false;
                        function showSuccess() {
                            if (shown) return;
                            shown = true;
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                bootstrap.Modal.getOrCreateInstance(successModalEl).show();
                            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                                $(successModalEl).modal('show');
                            }
                        }

                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var cModal = bootstrap.Modal.getInstance(confirmModalEl) || bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                            $(confirmModalEl).one('hidden.bs.modal', showSuccess);
                            cModal.hide();
                            setTimeout(showSuccess, 300);
                        } else {
                            $(confirmModalEl).modal('hide');
                            setTimeout(showSuccess, 300);
                        }
                    },
                    error: function (xhr) {
                        console.warn('AJAX reset failed, submitting normally:', xhr);
                        formReset.submit();
                    },
                    complete: function () {
                        if (btnSubmitReset) {
                            btnSubmitReset.disabled = false;
                            btnSubmitReset.innerHTML = origBtnContent;
                        }
                    }
                });
            });
        }
    });

    function copyResetPassword() {
        var textEl = document.getElementById('resetPasswordVal');
        if (!textEl) return;
        var text = textEl.innerText || textEl.textContent;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(showCopySuccess).catch(function () {
                fallbackCopyText(text);
            });
        } else {
            fallbackCopyText(text);
        }
    }

    function fallbackCopyText(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {}
        document.body.removeChild(textArea);
    }

    function showCopySuccess() {
        var icon = document.getElementById('resetCopyIcon');
        if (icon) {
            icon.className = 'ti ti-check text-success';
            setTimeout(function () { icon.className = 'ti ti-copy'; }, 2000);
        }
    }
</script>
@endpush
