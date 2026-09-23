# CHANGELOG Archive — RuangTerra (Fase 1–30)

Dokumen ini memuat arsip ringkasan riwayat implementasi awal proyek RuangTerra dari Fase 1 hingga Fase 30.

---

### [Fase 1] Foundation — 2026-09-11
Inisialisasi 17 tabel migrasi basis data, model Eloquent beserta relasi, dan database seeder untuk entitas utama (Role, Class, Subject, User, Material, Assignment, Quiz, Question Bank). Menyiapkan pondasi basis data awal dengan validasi relasi dan unique constraint yang teruji via `FoundationTest`.

### [Fase 2] Auth & Middleware — 2026-09-11
Implementasi registrasi siswa dengan dropdown pemilihan kelas serta pengalihan role otomatis ke `siswa`. Middleware `EnsureUserHasRole` (`role:guru`) diterapkan untuk mengisolasi dashboard admin dari akses siswa dan guest.

### [Fase 3] Pusher & Echo Setup — 2026-09-11
Konfigurasi WebSocket real-time menggunakan `pusher/pusher-php-server` dan channel privat untuk diskusi materi (`material.{id}`) serta pemberitahuan kelas (`class.{id}`). Event broadcasting `DiscussionCommentSent`, `MaterialCreated`, dan `AssignmentCreated` diintegrasikan dan diverifikasi via pengujian.

### [Fase 4] Master — Role CRUD — 2026-09-11
Penyediaan modul CRUD Role admin dengan proteksi terhadap role bawaan sistem (`guru` dan `siswa`). Dilengkapi antarmuka Tabler Blade dan pengujian validasi otorisasi.

### [Fase 5] Master — User (Daftar & Assign Role) — 2026-09-11
Modul manajemen pengguna untuk pencarian, pemfilteran role, dan pengalokasian role, kelas, serta NIP guru. Dilengkapi pembatasan akses ketat bagi peran non-guru.

### [Fase 6] Master — Mata Pelajaran CRUD + Guru Pengampu — 2026-09-11
Modul CRUD mata pelajaran sekolah beserta alokasi relasi *many-to-many* guru pengampu melalui tabel pivot `subject_user`. Guru hanya dapat mengelola konten untuk mata pelajaran yang diampunya.

### [Fase 7] Master — Kelas CRUD — 2026-09-11
Pengelolaan data kelas sekolah dengan validasi keunikan nama kelas dan antarmuka manajemen data berbasis Tabler admin.

### [Fase 8] Master — Bank Soal CRUD — 2026-09-11
Pengelolaan Bank Soal untuk butir soal pilihan ganda beserta opsi jawaban dinamis dan penandaan kunci jawaban benar. Penghapusan soal dikonfigurasikan cascade ke seluruh opsi terkait.

### [Fase 9] Master — Materi CRUD + Broadcast Event — 2026-09-11
Modul pembuatan dan pengeditan materi belajar multi-format (artikel teks, unggahan dokumen, embed YouTube) dengan otorisasi guru per mata pelajaran. Pembuatan materi baru secara otomatis memancarkan event real-time `MaterialCreated` ke channel kelas siswa.

### [Fase 10] Master — Tugas CRUD + Broadcast Event — 2026-09-11
Modul CRUD tugas siswa dengan pengaturan tenggat waktu (deadline) dan otorisasi guru pengampu mata pelajaran. Pembuatan tugas memancarkan event real-time `AssignmentCreated` ke seluruh siswa di kelas terkait.

### [Fase 11] Master — Kuis CRUD & Question Management — 2026-09-11
Modul pengelolaan kuis evaluasi dengan pengaturan durasi pengerjaan, poin per butir, deadline, serta fitur impor soal dari Bank Soal maupun penambahan soal mandiri. Dilindungi otorisasi guru pengampu dan divalidasi via `QuizCrudTest`.

### [Fase 12] Admin Koreksi Tugas — 2026-09-11
Halaman peninjauan pengumpulan tugas siswa per kelas untuk pemberian nilai (skala 0–100) dan umpan balik catatan guru. Akses penilaian dibatasi hanya untuk guru pengampu mata pelajaran terkait.

### [Fase 13] Admin Laporan & Rekapitulasi Analytics — 2026-09-11
Halaman rekapitulasi progres penyelesaian materi, rerata nilai tugas, dan rerata skor kuis per siswa dengan filter kelas dan mata pelajaran. Dilengkapi fitur unduh laporan rekap dalam format CSV (`exportCsv`).

### [Fase 14] Student Dashboard — 2026-09-11
Dashboard ringkasan belajar siswa yang menyajikan nama kelas, persentase progres materi, daftar tugas aktif, dan kuis yang tersedia. Rute `/dashboard` diarahkan secara otomatis berdasarkan sesi autentikasi siswa.

### [Fase 15] Student Materi & Diskusi Realtime — 2026-09-11
Antarmuka penampil materi pelajaran (teks, PDF, YouTube), tombol penanda progres selesai (`material_progress`), dan forum diskusi real-time pada setiap materi. Komentar baru otomatis disiarkan via WebSocket tanpa perlu memuat ulang halaman.

### [Fase 16] Student Tugas — 2026-09-11
Antarmuka siswa untuk meninjau instruksi tugas, status pengumpulan, serta formulir submit jawaban essay. Menampilkan skor nilai dan catatan evaluasi guru setelah tugas diperiksa.

### [Fase 17] Student Kuis (Timer Vanilla JS + Preview/Review Jawaban) — 2026-09-11
Lembar pengerjaan kuis siswa dengan timer hitung mundur Vanilla JS, auto-submit saat waktu habis, dan kalkulasi skor instan. Halaman hasil kuis menampilkan rincian jawaban siswa bersanding dengan kunci jawaban yang benar.

### [Fase 18] Student Profil & Laporan Diri — 2026-09-11
Halaman rekapitulasi progres belajar mandiri siswa yang menampilkan seluruh capaian materi selesai, riwayat nilai tugas, dan skor perolehan kuis.

### [Fase 19] Testing & Security Audit — 2026-09-11
Audit pengujian keamanan komprehensif mencakup mitigasi SQL Injection, escaping XSS pada diskusi/materi, segregasi role middleware, dan pengamanan rute dari akses guest.

### [Fase 20] Polish & Audit Final — 2026-09-11
Audit optimasi query database (eager loading `with()`) untuk mencegah masalah N+1 Query serta standardisasi komponen antarmuka Tabler Blade dan Bootstrap 5 (100% pass pada 111 automated tests).

### [Fase 21] Front-End Overhaul, Visual UI Improvements & PRD v1.9 Backend Alignment — 2026-09-11
Implementasi PWA (Web App Manifest, Service Worker, Mobile Bottom Navigation), skema warna tema (`#66A3BF`, `#3368A0`, `#C8DFDB`, `#F2EFE7`), tabel `notifications`, dan penyelarasan kartu statistik admin dashboard.

### [Fase 22] Arsha Theme Overhaul (Youth & High School / SMA Design) — 2026-09-11
Penyegaran visual antarmuka siswa berbasis Arsha Bootstrap template dengan ilustrasi vektor melayang, animasi AOS (Animate On Scroll), dan tata letak modern bernuansa SMA.

### [Fase 23] Enhanced Rich Media & Laptop Header Optimization — 2026-09-11
Penambahan aset grafis definisi tinggi, penyesuaian navigasi header agar tampil penuh pada layar laptop tanpa tombol hamburger, serta penataan filter mata pelajaran.

### [Fase 24] Dedicated Laptop Header Display, Micro-Animations & Arsha Footer Overhaul — 2026-09-11
Penyempurnaan aturan navigasi desktop, penambahan mikro-animasi (hover lift, shine sweep, pulse glow), dan pembaruan footer modern 4-kolom Arsha dengan tautan media sosial.

### [Fase 25] Replacement of Plain White Backgrounds with Warm Light Theme (#F2EFE7) & Footer Social Media Icon Styling — 2026-09-11
Standardisasi warna latar belakang hangat `#F2EFE7` di seluruh halaman siswa serta penyempurnaan styling tombol media sosial dengan animasi hover interaktif.

### [Fase 26] Direct Hero Section Attachment & Modern Glassmorphism Statistics Cards Overhaul — 2026-09-11
Integrasi mulus banner Hero langsung di bawah navbar tanpa celah (gap 0px) dan pembersihan elemen ornamen visual agar antarmuka lebih bersih (*clean UI*).

### [Fase 27] Enable Frontend Registration & Application Rebrand to RuangTerra — 2026-09-12
Pembukaan rute registrasi siswa baru secara publik pada `/register` dan peluncuran identitas rebrand resmi aplikasi menjadi **RuangTerra**.

### [Fase 28] Dedicated Backend & Frontend Authentication Architecture — 2026-09-12
Pemisahan rute dan arsitektur autentikasi khusus backend (`/admin/login` dan `/admin/forgot-password`) bergaya Tabler dengan pemblokiran akun siswa dari panel guru.

### [Fase 29] Authentication UI Enhancements (Eye Toggle Icon & Submit Auto-Disable) — 2026-09-12
Penambahan tombol ikon mata (show/hide password) di seluruh form autentikasi serta proteksi auto-disable tombol submit disertai spinner loading untuk mencegah pengiriman ganda.

### [Fase 30] Redesign & Visual Enhancement of Student Courses, Toolbar Spacing & Material Detail Page — 2026-09-12
Redesain katalog materi siswa dengan toolbar filter lega, pengalih tampilan Grid/List View dengan penyimpanan preferensi di localStorage, serta halaman baca materi 2-kolom yang dilengkapi video player dan unduhan dokumen PDF.
