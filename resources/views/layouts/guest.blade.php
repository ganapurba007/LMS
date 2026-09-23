<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#66A3BF">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

    <title>{{ config('app.name', 'RuangTerra') }}</title>

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Tabler Icons Webfont CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Select2 CSS CDN & Bootstrap 5 Theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
    /* Auth Buttons Hover & Disabled States */
    .btn-auth-submit {
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative;
        cursor: pointer;
    }
    .btn-auth-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(51, 104, 160, 0.35) !important;
        filter: brightness(1.08);
    }
    .btn-auth-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 3px 10px rgba(51, 104, 160, 0.2) !important;
    }
    .btn-auth-submit:disabled,
    .btn-auth-submit.disabled {
        opacity: 0.72 !important;
        cursor: not-allowed !important;
        transform: none !important;
        box-shadow: none !important;
        pointer-events: none !important;
    }
    /* Toggle password button */
    .toggle-password {
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .toggle-password:hover {
        color: #3368A0 !important;
        background-color: #e2e8f0 !important;
    }
    </style>
</head>
<body style="background-color: var(--color-warm-light, #F2EFE7); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem 1rem;">

    <div style="width: 100%; max-width: 1100px;">
        {{ $slot }}
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- PWA Service Worker Registration & Select2 Init -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js');
        });
    }

    window.initSelect2 = function(targetSelector) {
        if (typeof $ === 'undefined' || !$.fn || !$.fn.select2) return;
        var $targets = targetSelector ? $(targetSelector) : $('.select2, select.select2');
        $targets.each(function() {
            var $el = $(this);
            if ($el.hasClass('select2-hidden-accessible') || $el.data('no-select2')) {
                return;
            }
            var placeholder = $el.data('placeholder') || $el.attr('placeholder') || $el.find('option[value=""]').first().text() || 'Pilih...';
            $el.select2({
                theme: 'bootstrap-5',
                width: $el.data('width') ? $el.data('width') : '100%',
                placeholder: placeholder,
                allowClear: $el.data('allow-clear') !== undefined ? $el.data('allow-clear') : true,
                dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(document.body)
            });
        });
    };

    $(document).ready(function() {
        window.initSelect2();
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Universal Password Eye Toggle Handler
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                if (!input) return;

                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.className = 'ti ti-eye-off fs-5';
                    }
                    this.setAttribute('title', 'Sembunyikan Kata Sandi');
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.className = 'ti ti-eye fs-5';
                    }
                    this.setAttribute('title', 'Lihat Kata Sandi');
                }
            });
        });

        // Form Submit Disable Button & Loading Spinner
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (this.checkValidity()) {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn && !btn.disabled) {
                        // Gunakan setTimeout 0 agar event form submit tetap terpicu browser
                        setTimeout(function () {
                            btn.disabled = true;
                            btn.classList.add('disabled');
                            const loadingText = btn.dataset.loadingText || 'Memproses...';
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText;
                        }, 10);
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
