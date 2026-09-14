<x-app-layout>
    <!-- Include Bootstrap, Tabler Icons & Custom Theme CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

    <style>
        /* Hero Section Profil Siswa (Elegan & Konsisten dengan RuangTerra) */
        .profile-hero {
            background: linear-gradient(135deg, #1e3d60 0%, #2b5788 55%, #20456E 100%);
            border-bottom: 3px solid #66A3BF;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }
        .profile-hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(102, 163, 191, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .profile-hero::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 20%;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(32, 69, 110, 0.12);
            box-shadow: 0 4px 20px rgba(32, 69, 110, 0.05);
            transition: all 0.25s ease;
        }
        .profile-card:hover {
            box-shadow: 0 8px 30px rgba(32, 69, 110, 0.09);
        }

        .avatar-circle-lg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(56, 189, 248, 0.35);
            border: 3px solid #ffffff;
        }

        .form-control-custom {
            border-radius: 10px;
            padding: 0.65rem 1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control-custom:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
            outline: none;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #3368A0 0%, #20456E 100%);
            color: #ffffff;
            border: none;
            transition: all 0.2s;
        }
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #2b5788 0%, #173252 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(32, 69, 110, 0.25);
        }

        .btn-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            border: none;
            transition: all 0.2s;
        }
        .btn-gradient-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>

    <!-- Header Hero Section -->
    <section class="profile-hero">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                            <i class="ti ti-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">
                        Profil Saya
                    </li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 shadow-sm" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.25);">
                        <i class="ti ti-user-circle text-info"></i>
                        <span class="text-white small fw-bold">Akun & Pengaturan Profil Siswa</span>
                    </div>
                    <h1 class="h2 fw-extrabold text-white mb-2" style="font-family: 'Jost', sans-serif;">
                        Profil Saya
                    </h1>
                    <p class="text-white-50 mb-0" style="font-size: 0.95rem;">
                        Kelola data diri, pantau kelas terdaftar, serta atur keamanan kata sandi akun belajarmu.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end text-start">
                    <div class="d-inline-flex align-items-center gap-3 p-3 rounded-4" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
                        <div class="avatar-circle-lg" style="width: 58px; height: 58px; font-size: 20px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-white fs-6 mb-0">{{ $user->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge rounded-pill bg-success-subtle text-success px-2 py-0.5" style="font-size: 0.72rem;">
                                    <i class="ti ti-circle-check-filled me-1"></i> Siswa Aktif
                                </span>
                                <span class="badge rounded-pill bg-light text-dark px-2 py-0.5" style="font-size: 0.72rem;">
                                    {{ $user->schoolClass->name ?? 'Siswa' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="row g-4">
            <!-- Left Column: User Summary & Security Info Card -->
            <div class="col-lg-4">
                <div class="profile-card p-4 text-center mb-4 position-sticky" style="top: 85px;">
                    <!-- User Avatar -->
                    <div class="d-flex justify-content-center mb-3">
                        <div class="avatar-circle-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>

                    <!-- User Name & Email -->
                    <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>

                    <!-- Badges -->
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                            <i class="ti ti-circle-check me-1"></i> Akun Siswa Aktif
                        </span>
                    </div>

                    <hr class="my-3" style="border-color: rgba(32, 69, 110, 0.1);">

                    <!-- Academic Metadata -->
                    <div class="text-start space-y-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 mb-2" style="background: #f8fafc;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="p-1.5 rounded-2 bg-primary-subtle text-primary">
                                    <i class="ti ti-school fs-5"></i>
                                </span>
                                <span class="small text-muted fw-semibold">Kelas</span>
                            </div>
                            <span class="fw-bold text-dark small">{{ $user->schoolClass->name ?? 'Belum Ditentukan' }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 mb-2" style="background: #f8fafc;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="p-1.5 rounded-2 bg-warning-subtle text-warning">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                                <span class="small text-muted fw-semibold">Terdaftar Sejak</span>
                            </div>
                            <span class="fw-semibold text-secondary small">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Quick Link to Student Report -->
                    <a href="{{ route('student.report.index') }}" class="btn btn-outline-primary w-100 rounded-pill py-2 small fw-bold d-flex align-items-center justify-content-center gap-2 mb-3">
                        <i class="ti ti-chart-bar"></i> Lihat Laporan Belajar Siswa
                    </a>

                    <!-- Security Tips Banner -->
                    <div class="p-3 rounded-3 text-start mt-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                        <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                            <i class="ti ti-shield-lock fs-5"></i> Tips Keamanan Akun
                        </div>
                        <p class="text-muted mb-0" style="font-size: 0.78rem; line-height: 1.4;">
                            Gunakan kata sandi unik dan jangan berikan informasi login akunmu kepada siapa pun. Selalu logout setelah selesai belajar di komputer umum.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Profile Edit Forms -->
            <div class="col-lg-8 space-y-4">
                <!-- CARD 1: Informasi Akun & Data Pribadi -->
                <div class="profile-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2.5 text-primary" style="background: rgba(14, 165, 233, 0.12);">
                                <i class="ti ti-user-circle fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Informasi Pribadi & Kontak</h5>
                                <p class="text-muted small mb-0">Perbarui nama lengkap dan alamat email aktif Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Information Form -->
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold text-dark small">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-user"></i>
                                    </span>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           class="form-control form-control-custom border-start-0 @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           autofocus
                                           placeholder="Masukkan nama lengkap">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-dark small">
                                    Alamat Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-control form-control-custom border-start-0 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           placeholder="nama@email.com">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kelas (Read-Only) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">
                                    Kelas Terdaftar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-school"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control form-control-custom border-start-0 bg-light text-muted"
                                           value="{{ $user->schoolClass->name ?? 'Belum Ditentukan' }}"
                                           disabled>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 0.76rem;">
                                    <i class="ti ti-info-circle me-1"></i> Hubungi Wali Kelas untuk pemindahan kelas.
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button & Feedback -->
                        <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-gradient-primary rounded-pill px-4 py-2 font-bold d-inline-flex align-items-center gap-2 shadow-sm hover-lift">
                                <i class="ti ti-device-floppy fs-5"></i> Simpan Perubahan
                            </button>

                            @if (session('status') === 'profile-updated')
                                <div class="alert alert-success py-1.5 px-3 rounded-pill mb-0 small d-inline-flex align-items-center gap-2 fw-semibold" role="alert">
                                    <i class="ti ti-circle-check-filled text-success fs-5"></i> Data profil berhasil diperbarui.
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- CARD 2: Keamanan & Kata Sandi -->
                <div class="profile-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2.5 text-warning" style="background: rgba(245, 158, 11, 0.12);">
                                <i class="ti ti-shield-lock fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Ubah Kata Sandi</h5>
                                <p class="text-muted small mb-0">Pastikan akun Anda terlindungi dengan kombinasi kata sandi yang aman.</p>
                            </div>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="row g-3">
                            <!-- Kata Sandi Saat Ini -->
                            <div class="col-12">
                                <label for="update_password_current_password" class="form-label fw-semibold text-dark small">
                                    Kata Sandi Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-key"></i>
                                    </span>
                                    <input type="password"
                                           id="update_password_current_password"
                                           name="current_password"
                                           class="form-control form-control-custom border-start-0 border-end-0 @error('current_password', 'updatePassword') is-invalid @enderror"
                                           autocomplete="current-password"
                                           placeholder="Masukkan kata sandi lama">
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('update_password_current_password', this)">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                @error('current_password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kata Sandi Baru -->
                            <div class="col-md-6">
                                <label for="update_password_password" class="form-label fw-semibold text-dark small">
                                    Kata Sandi Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-lock"></i>
                                    </span>
                                    <input type="password"
                                           id="update_password_password"
                                           name="password"
                                           class="form-control form-control-custom border-start-0 border-end-0 @error('password', 'updatePassword') is-invalid @enderror"
                                           autocomplete="new-password"
                                           placeholder="Minimal 8 karakter">
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('update_password_password', this)">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konfirmasi Kata Sandi Baru -->
                            <div class="col-md-6">
                                <label for="update_password_password_confirmation" class="form-label fw-semibold text-dark small">
                                    Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="ti ti-lock-check"></i>
                                    </span>
                                    <input type="password"
                                           id="update_password_password_confirmation"
                                           name="password_confirmation"
                                           class="form-control form-control-custom border-start-0 border-end-0 @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                           autocomplete="new-password"
                                           placeholder="Ketik ulang kata sandi baru">
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('update_password_password_confirmation', this)">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button & Feedback -->
                        <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-gradient-warning rounded-pill px-4 py-2 font-bold d-inline-flex align-items-center gap-2 shadow-sm hover-lift">
                                <i class="ti ti-lock-cog fs-5"></i> Perbarui Kata Sandi
                            </button>

                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success py-1.5 px-3 rounded-pill mb-0 small d-inline-flex align-items-center gap-2 fw-semibold" role="alert">
                                    <i class="ti ti-circle-check-filled text-success fs-5"></i> Kata sandi berhasil diperbarui.
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- CARD 3: Zona Berbahaya (Hapus Akun) -->
                <div class="profile-card p-4 border-danger-subtle mb-4" style="border: 1px solid rgba(239, 68, 68, 0.25);">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-danger-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2.5 text-danger" style="background: rgba(239, 68, 68, 0.12);">
                                <i class="ti ti-alert-triangle fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-danger mb-0">Zona Berbahaya</h5>
                                <p class="text-muted small mb-0">Tindakan penghapusan akun siswa bersifat permanen.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-3" style="background: #fff5f5; border: 1px solid #fed7d7;">
                        <p class="text-danger small mb-0" style="line-height: 1.5;">
                            <strong>Peringatan:</strong> Setelah akun Anda dihapus, semua data profil, riwayat pengerjaan tugas, hasil kuis, dan interaksi diskusi Anda akan dihapus secara permanen dari basis data sistem.
                        </p>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 small fw-bold d-inline-flex align-items-center gap-2 hover-lift" data-bs-toggle="modal" data-bs-target="#confirmStudentDeletionModal">
                            <i class="ti ti-trash"></i> Hapus Akun Saya
                        </button>
                    </div>

                    <!-- Modal Konfirmasi Hapus Akun -->
                    <div class="modal fade @if($errors->userDeletion->isNotEmpty()) show d-block @endif"
                         id="confirmStudentDeletionModal"
                         tabindex="-1"
                         aria-labelledby="confirmStudentDeletionModalLabel"
                         aria-hidden="true"
                         @if($errors->userDeletion->isNotEmpty()) style="background: rgba(0,0,0,0.55);" @endif>
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow-2xl">
                                <form method="post" action="{{ route('profile.destroy') }}">
                                    @csrf
                                    @method('delete')

                                    <div class="modal-header border-bottom py-3 px-4" style="background: #fff5f5;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="ti ti-alert-triangle fs-4"></i>
                                            </div>
                                            <h6 class="modal-title fw-bold text-danger mb-0" id="confirmStudentDeletionModalLabel">
                                                Konfirmasi Penghapusan Akun
                                            </h6>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeDeletionModal()"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <p class="text-dark small mb-3">
                                            Apakah Anda benar-benar yakin ingin menghapus akun ini? Seluruh data tugas, kuis, dan materi yang telah Anda kerjakan tidak akan dapat dikembalikan.
                                        </p>
                                        <div class="mb-3">
                                            <label for="student_delete_password" class="form-label fw-semibold text-dark small">
                                                Masukkan Kata Sandi untuk Konfirmasi <span class="text-danger">*</span>
                                            </label>
                                            <input type="password"
                                                   id="student_delete_password"
                                                   name="password"
                                                   class="form-control form-control-custom @error('password', 'userDeletion') is-invalid @enderror"
                                                   placeholder="Kata sandi akun Anda"
                                                   required>
                                            @error('password', 'userDeletion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top py-3 px-4 bg-light">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4 small fw-semibold" data-bs-dismiss="modal" onclick="closeDeletionModal()">
                                            Batal
                                        </button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 small fw-bold d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-trash"></i> Ya, Hapus Akun Permanen
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for Password Visibility Toggle & Modal Closing -->
    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off text-primary';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye text-muted';
            }
        }

        function closeDeletionModal() {
            const modal = document.getElementById('confirmStudentDeletionModal');
            if (modal) {
                modal.classList.remove('show', 'd-block');
                modal.style.background = '';
            }
        }
    </script>
</x-app-layout>
