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

@if(session('success'))
    <div class="md-alert success mb-4">
        <i class="ti ti-check-circle"></i> {{ session('success') }}
        <button class="md-alert-close" onclick="this.closest('.md-alert').remove()"><i class="ti ti-x"></i></button>
    </div>
@endif

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
            <button type="button" class="md-btn-warning me-auto" data-bs-toggle="modal" data-bs-target="#modalResetConfirm">
                <i class="ti ti-key"></i> Reset Password
            </button>
            <button type="submit" class="md-btn-submit"><i class="ti ti-device-floppy"></i> Update Pengguna</button>
        </div>
    </form>
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
                Password untuk <strong id="resetTargetName">{{ $user->name }}</strong> akan direset menjadi karakter acak baru.
            </p>

            <form id="formResetPassword" method="POST" action="{{ route('admin.users.reset-password', $user) }}">
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
                Password baru untuk <strong id="resetSuccessUserName">{{ session('reset_user_name', $user->name) }}</strong>:
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

@include('admin._partials.master-data-styles')
@endsection

@push('scripts')
<script>
    $(function () {
        var confirmModalEl = document.getElementById('modalResetConfirm');
        var successModalEl = document.getElementById('modalPasswordResetSuccess');
        var formReset = document.getElementById('formResetPassword');
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
