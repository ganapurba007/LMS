<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#66A3BF">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('tabler/static/logo-small.svg') }}">

        <title>{{ config('app.name', 'RuangTerra') }}</title>

        <!-- Google Fonts: Montserrat & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tabler Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

        <!-- AOS (Animate On Scroll) -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <!-- Custom Theme CSS -->
        <link rel="stylesheet" href="{{ asset('css/theme-custom.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen pb-16 lg:pb-0 bg-geometric-canvas">
            @include('layouts.navigation')

            <!-- Global Flash Messages (Error / Success) -->
            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" data-aos="fade-down">
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm rounded-4 py-3 px-4 mb-0 border-0" role="alert" style="background: #fef2f2; border-left: 5px solid #ef4444 !important; color: #991b1b;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; background: rgba(239, 68, 68, 0.15);">
                            <i class="ti ti-alert-triangle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block fw-bold" style="font-size: 0.95rem;">Perhatian</strong>
                            <span style="font-size: 0.88rem;">{{ session('error') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" data-aos="fade-down">
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm rounded-4 py-3 px-4 mb-0 border-0" role="alert" style="background: #ecfdf5; border-left: 5px solid #10b981 !important; color: #065f46;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.15);">
                            <i class="ti ti-circle-check fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block fw-bold" style="font-size: 0.95rem;">Berhasil</strong>
                            <span style="font-size: 0.88rem;">{{ session('success') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @if(isset($header) && trim($header) !== '')
                <header class="shadow-sm border-b" style="background-color: #F2EFE7; border-color: rgba(102, 163, 191, 0.2);">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- RuangTerra Premium Modern Footer Bar -->
            <footer class="arsha-footer">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="row g-4 mb-5">
                        <!-- Col 1: Brand & Social Media -->
                        <div class="col-lg-5">
                            <a href="{{ Auth::user() && Auth::user()->isGuru() ? route('admin.dashboard') : route('dashboard') }}" class="arsha-footer-brand mb-3">
                                <img src="{{ asset('images/icon.png') }}"
                                     alt="RuangTerra Icon"
                                     class="footer-brand-icon">
                                <div>
                                    <div class="footer-brand-title">
                                        Ruang<span>Terra</span>
                                    </div>
                                    <div class="footer-brand-subtitle">
                                        Learning Platform
                                    </div>
                                </div>
                            </a>
                            <p class="small text-slate-400 pe-lg-4 mb-4" style="line-height: 1.65; max-width: 440px;">
                                Platform E-Learning SMA terpadu untuk mengakses modul materi interaktif, mengumpulkan tugas kelas, dan mengikuti kuis online dengan pengalaman belajar modern yang menyenangkan.
                            </p>

                            <!-- Tidy & Beautiful Social Media Badges -->
                            <div class="d-flex align-items-center gap-2.5 mt-2">
                                <a href="#" class="social-icon-btn instagram" title="Instagram">
                                    <i class="ti ti-brand-instagram"></i>
                                </a>
                                <a href="#" class="social-icon-btn youtube" title="YouTube">
                                    <i class="ti ti-brand-youtube"></i>
                                </a>
                                <a href="#" class="social-icon-btn discord" title="Discord">
                                    <i class="ti ti-brand-discord"></i>
                                </a>
                                <a href="#" class="social-icon-btn whatsapp" title="WhatsApp">
                                    <i class="ti ti-brand-whatsapp"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Col 2: Navigasi Utama -->
                        <div class="col-6 col-lg-3">
                            <h5 class="footer-widget-title">Navigasi Utama</h5>
                            <ul class="list-unstyled d-flex flex-column gap-2.5 mb-0">
                                <li><a href="{{ route('dashboard') }}" class="footer-nav-link"><i class="ti ti-chevron-right"></i> Home / Dashboard</a></li>
                                <li><a href="{{ route('student.materials.index') }}" class="footer-nav-link"><i class="ti ti-chevron-right"></i> Courses / Materi</a></li>
                                <li><a href="{{ route('student.assignments.index') }}" class="footer-nav-link"><i class="ti ti-chevron-right"></i> Tugas Kelas</a></li>
                                <li><a href="{{ route('student.quizzes.index') }}" class="footer-nav-link"><i class="ti ti-chevron-right"></i> Kuis Online</a></li>
                                <li><a href="{{ route('student.report.index') }}" class="footer-nav-link"><i class="ti ti-chevron-right"></i> Laporan Diri</a></li>
                            </ul>
                        </div>

                        <!-- Col 3: Dukungan & Bantuan -->
                        <div class="col-lg-4">
                            <h5 class="footer-widget-title">Bantuan & Kontak</h5>
                            <div class="d-flex flex-column gap-3 mb-3">
                                <div class="footer-contact-item">
                                    <div class="footer-contact-icon"><i class="ti ti-mail"></i></div>
                                    <div class="pt-0.5">danisetyowati8@gmail.com</div>
                                </div>
                                <div class="footer-contact-item">
                                    <div class="footer-contact-icon"><i class="ti ti-phone"></i></div>
                                    <div class="pt-0.5">+62 858 2465 8011</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Copyright Bar & Scroll Top -->
                    <div class="border-top pt-4 d-flex flex-wrap align-items-center justify-content-between text-center text-md-start small text-slate-400 gap-3" style="border-top-color: rgba(255, 255, 255, 0.08) !important;">
                        <div>
                            &copy; {{ date('Y') }} <strong class="text-white" style="font-family: 'Jost', sans-serif; letter-spacing: 0.3px;">Ruang<span style="color: #38bdf8;">Terra</span></strong>. All rights reserved.
                        </div>
                        <div class="d-flex align-items-center gap-3 mx-auto mx-md-0">
                            <button type="button" onclick="window.scrollTo({top:0, behavior:'smooth'});" class="footer-back-to-top" title="Kembali ke Bagian Atas">
                                <i class="ti ti-arrow-up"></i>
                                <span>Kembali ke Atas</span>
                            </button>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Mobile Bottom Nav Bar (Mobile-First PWA) -->
        <nav class="mobile-bottom-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-smart-home fs-4"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('student.materials.index') }}" class="{{ request()->routeIs('student.materials.*') ? 'active' : '' }}">
                <i class="ti ti-book fs-4"></i>
                <span>Materi</span>
            </a>
            <a href="{{ route('student.assignments.index') }}" class="{{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                <i class="ti ti-clipboard-list fs-4"></i>
                <span>Tugas</span>
            </a>
            <a href="{{ route('student.quizzes.index') }}" class="{{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
                <i class="ti ti-help-hexagon fs-4"></i>
                <span>Kuis</span>
            </a>
        </nav>

        <!-- AOS JS -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 800,
                        once: true,
                        easing: 'ease-in-out'
                    });
                }
            });
        </script>

        <!-- Service Worker Registration -->
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    console.log('PWA ServiceWorker registered');
                });
            });
        }
        </script>
    </body>
</html>
