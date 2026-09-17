<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Lupa Kata Sandi Guru / Admin — {{ config('app.name', 'RuangTerra') }}</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
  <!-- Tabler Icons Webfont CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

  <!-- Custom Template CSS -->
  <link rel="stylesheet" href="{{ asset('template/be/assets/css/custom.css') }}">
</head>
<body>

  <div class="error-page-wrapper py-4">
    <div class="auth-card-container" style="max-width: 440px; width: 100%;">

      <!-- Brand Logo -->
      <div class="text-center mb-4">
        <a href="{{ route('admin.login') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none">
          <div class="sidebar-brand-icon">
            <img src="{{ asset('images/icon.png') }}" alt="{{ config('app.name', 'RuangTerra') }} Icon" style="width: 38px; height: 38px; object-fit: contain;">
          </div>
          <span class="fs-3 fw-bold heading-custom">{{ config('app.name', 'RuangTerra') }}</span>
        </a>
        <div class="small text-white mt-1 fw-bold tracking-wide">PANEL KHUSUS GURU & ADMINISTRATOR</div>
      </div>

      <!-- Session Status -->
      @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show small mb-3 border-0 shadow-sm" role="alert">
          <i class="ti ti-check me-1"></i> {{ session('status') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <!-- Error Message Banner -->
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small mb-3 border-0 shadow-sm" role="alert">
          <div class="d-flex align-items-start gap-2">
            <i class="ti ti-alert-triangle fs-5 mt-0.5"></i>
            <div>
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <!-- Auth Card -->
      <div class="card shadow-sm border mb-0">
        <div class="card-body p-4 p-sm-5">
          <div class="text-center mb-4">
            <div class="avatar-icon-box avatar-icon-primary mx-auto mb-2" style="width: 52px; height: 52px; font-size: 1.6rem;">
              <i class="ti ti-key"></i>
            </div>
            <h2 class="h4 fw-bold heading-custom mb-1">Lupa Kata Sandi?</h2>
            <p class="text-muted-custom small mb-0">Masukkan email akun Guru yang terdaftar untuk menerima tautan reset kata sandi.</p>
          </div>

          <form action="{{ route('admin.password.email') }}" method="POST">
            @csrf

            <div class="mb-4">
              <label class="form-label small fw-semibold" for="email">Alamat Email Guru</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted-custom"><i class="ti ti-mail"></i></span>
                <input type="email" id="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="guru@lms.com" value="{{ old('email') }}" required autofocus>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-auth-submit w-100 py-2.5 fw-semibold shadow-sm" data-loading-text="Mengirim Tautan Reset...">
              <i class="ti ti-send me-1"></i> Kirim Tautan Reset
            </button>
          </form>
        </div>
      </div>

      <!-- Footer text -->
      <div class="text-center mt-4">
        <p class="text-muted-custom small mb-0">
          <a href="{{ route('admin.login') }}" class="fw-semibold text-decoration-none text-white">
            <i class="ti ti-arrow-left me-1"></i> Kembali ke Login Guru
          </a>
        </p>
      </div>

      <!-- Theme Toggle Switch -->
      <div class="text-center mt-3">
        <button type="button" class="theme-toggle-btn mx-auto" id="theme-toggle" aria-label="Ganti Tema">
          <i class="ti ti-moon fs-5"></i>
        </button>
      </div>

    </div>
  </div>

  <style>
  .btn-auth-submit {
      transition: all 0.26s cubic-bezier(0.4, 0, 0.2, 1) !important;
      position: relative;
      cursor: pointer;
  }
  .btn-auth-submit:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(32, 107, 196, 0.35) !important;
      filter: brightness(1.08);
  }
  .btn-auth-submit:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(32, 107, 196, 0.2) !important;
  }
  .btn-auth-submit:disabled,
  .btn-auth-submit.disabled {
      opacity: 0.72 !important;
      cursor: not-allowed !important;
      transform: none !important;
      box-shadow: none !important;
      pointer-events: none !important;
  }
  </style>

  <!-- Bootstrap 5 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom Scripts -->
  <script src="{{ asset('template/be/assets/js/theme-toggle.js') }}"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('form').forEach(function (form) {
          form.addEventListener('submit', function (e) {
              if (this.checkValidity()) {
                  const btn = this.querySelector('button[type="submit"]');
                  if (btn && !btn.disabled) {
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
