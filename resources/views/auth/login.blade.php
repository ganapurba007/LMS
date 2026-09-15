<x-guest-layout>
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden my-auto">
        <div class="row g-0">
            
            <!-- Left Branding Column (Codepolitan / Udemy Style) -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                <div class="position-relative z-1">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <img src="{{ asset('images/icon.png') }}" alt="RuangTerra Icon" style="width: 42px; height: 42px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.15));">
                        <span class="fs-4 fw-bold text-white tracking-wide">RuangTerra</span>
                    </div>

                    <span class="badge rounded-pill bg-white text-dark fw-bold px-3 py-2 mb-3 shadow-sm" style="color: #3368A0 !important;">
                        <i class="ti ti-sparkles me-1 text-warning"></i> Platform E-Learning Interaktif
                    </span>

                    <h2 class="display-6 fw-extrabold text-white mb-3">Tingkatkan Keahlian Belajar Tanpa Batas</h2>
                    <p class="text-white-50 lead fs-6">Satu portal terintegrasi untuk mengakses materi pembelajaran, mengumpulkan tugas kelas, dan menguji kemampuan lewat kuis online interaktif.</p>
                </div>

                <div class="position-relative z-1 pt-4 border-top border-white border-opacity-25">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-group d-flex">
                            <span class="badge bg-warning rounded-pill text-dark fw-bold px-3 py-2 me-2">Akun Guru & Siswa</span>
                        </div>
                        <div class="small text-white-50">Gunakan email & kata sandi terdaftar untuk masuk.</div>
                    </div>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-lg-6 p-4 p-md-5 bg-white">
                <div class="d-lg-none text-center mb-4">
                    <div class="d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('images/icon.png') }}" alt="RuangTerra Icon" style="width: 38px; height: 38px; object-fit: contain;">
                        <span class="fs-3 fw-bold" style="color: #3368A0;">RuangTerra</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Selamat Datang Kembali! 👋</h3>
                    <p class="text-muted small">Masuk ke portal LMS untuk mengakses ruang belajar dan dashboard.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-3" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="email">{{ __('Alamat Email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-mail"></i></span>
                            <input type="email" id="email" class="form-control bg-light border-start-0 ps-1 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@domain.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-dark small mb-0" for="password">{{ __('Kata Sandi') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small text-decoration-none fw-semibold" style="color: #3368A0;">{{ __('Lupa Kata Sandi?') }}</a>
                            @endif
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-lock"></i></span>
                            <input type="password" id="password" class="form-control bg-light border-start-0 border-end-0 ps-1 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="btn btn-light border border-start-0 text-muted toggle-password" data-target="password" title="Lihat Kata Sandi">
                                <i class="ti ti-eye fs-5"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label text-muted small" for="remember_me">{{ __('Ingat saya di perangkat ini') }}</label>
                    </div>

                    <button type="submit" class="btn btn-auth-submit w-100 py-3 text-white fw-bold shadow-sm rounded-3 mb-3" style="background-color: #66A3BF; border: none;" data-loading-text="Memverifikasi Akun...">
                        <i class="ti ti-login me-1"></i> {{ __('Masuk Sekarang') }}
                    </button>
                </form>

                <div class="text-center pt-3 border-top">
                    <p class="text-muted small mb-2">Belum memiliki akun siswa? 
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #3368A0;">Daftar Siswa Baru</a>
                    </p>
                    <p class="text-muted small mb-0">Login khusus Guru & Admin: 
                        <a href="{{ route('admin.login') }}" class="fw-bold text-decoration-none" style="color: #206bc4;"><i class="ti ti-shield-lock me-1"></i>Portal Admin</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
