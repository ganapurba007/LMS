<x-guest-layout>
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden my-auto">
        <div class="row g-0">
            
            <!-- Left Branding Column (Codepolitan / Udemy Style) -->
            <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative" style="background: linear-gradient(135deg, #3368A0 0%, #66A3BF 100%);">
                <div class="position-relative z-1">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <img src="{{ asset('images/icon.png') }}" alt="RuangTerra Icon" style="width: 42px; height: 42px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.15));">
                        <span class="fs-4 fw-bold text-white tracking-wide">RuangTerra</span>
                    </div>

                    <span class="badge rounded-pill bg-white text-dark fw-bold px-3 py-2 mb-3 shadow-sm" style="color: #3368A0 !important;">
                        <i class="ti ti-user-plus me-1 text-warning"></i> Pendaftaran Siswa
                    </span>

                    <h2 class="display-6 fw-extrabold text-white mb-3">Gabung Bersama Komunitas Pembelajar</h2>
                    <p class="text-white-50 lead fs-6">Pilih kelasmu dan nikmati akses mudah ke seluruh materi pelajaran, tugas, serta evaluasi kuis interaktif.</p>
                </div>

            </div>

            <!-- Right Form Column -->
            <div class="col-lg-7 p-4 p-md-5 bg-white">
                <div class="d-lg-none text-center mb-4">
                    <div class="d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('images/icon.png') }}" alt="RuangTerra Icon" style="width: 38px; height: 38px; object-fit: contain;">
                        <span class="fs-3 fw-bold" style="color: #3368A0;">RuangTerra</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Buat Akun Siswa Baru 🚀</h3>
                    <p class="text-muted small">Lengkapi data diri di bawah ini untuk memulai pembelajaran.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="name">{{ __('Nama Lengkap') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-user"></i></span>
                            <input type="text" id="name" class="form-control bg-light border-start-0 ps-1 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Lengkap Anda">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-danger small" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="email">{{ __('Alamat Email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-mail"></i></span>
                            <input type="email" id="email" class="form-control bg-light border-start-0 ps-1 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@domain.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                    </div>

                    <!-- Class Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small" for="class_id">{{ __('Pilih Kelas') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-school"></i></span>
                            <select id="class_id" class="form-select select2 bg-light border-start-0 ps-1 @error('class_id') is-invalid @enderror" name="class_id" required data-placeholder="-- Pilih Kelas Anda --">
                                <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>-- Pilih Kelas Anda --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('class_id')" class="mt-1 text-danger small" />
                    </div>

                    <!-- Password -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="password">{{ __('Kata Sandi') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-lock"></i></span>
                                <input type="password" id="password" class="form-control bg-light border-start-0 border-end-0 ps-1 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                                <button type="button" class="btn btn-light border border-start-0 text-muted toggle-password" data-target="password" title="Lihat Kata Sandi">
                                    <i class="ti ti-eye fs-5"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small" for="password_confirmation">{{ __('Konfirmasi Sandi') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="ti ti-lock-check"></i></span>
                                <input type="password" id="password_confirmation" class="form-control bg-light border-start-0 border-end-0 ps-1 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                                <button type="button" class="btn btn-light border border-start-0 text-muted toggle-password" data-target="password_confirmation" title="Lihat Konfirmasi Sandi">
                                    <i class="ti ti-eye fs-5"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-danger small" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth-submit w-100 py-3 text-white fw-bold shadow-sm rounded-3 mb-3" style="background-color: #66A3BF; border: none;" data-loading-text="Mendaftarkan Akun...">
                        <i class="ti ti-user-plus me-1"></i> {{ __('Daftar Sekarang') }}
                    </button>
                </form>

                <div class="text-center pt-3 border-top">
                    <p class="text-muted small mb-0">Sudah memiliki akun? 
                        <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #3368A0;">Masuk di Sini</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
