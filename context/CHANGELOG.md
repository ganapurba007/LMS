# CHANGELOG — RuangTerra

> Catat setiap perubahan kode di sini selama implementasi.

## [Fase 47] Quiz Student Results Monitoring, Attempt Reset & UI Refinement — 2026-09-14

### Ditambahkan & Diperbarui
- **Fitur Monitoring Hasil Siswa per Kelas & Reset Pengerjaan Kuis**:
  - **Halaman Monitoring Hasil Siswa ([`resources/views/admin/quizzes/students.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/quizzes/students.blade.php))**:
    - Menyajikan data pengerjaan seluruh siswa di kelas kuis bersangkutan (Total Siswa, Sudah Selesai, Sedang Mengerjakan, Belum Mengerjakan, Rata-rata Nilai Kelas).
    - Menampilkan tabel lengkap dengan status pengerjaan, timestamp mulai/selesai, durasi pengerjaan, serta perolehan skor siswa.
    - Dilengkapi **DataTable Cerdas** (`data-table`) untuk pencarian instan (*live search*), paginasi, dan pengurutan kolom.
  - **Fitur Reset Pengerjaan Siswa**:
    - Tombol **Reset Pengerjaan** dengan konfirmasi **Modal Bootstrap 5 Custom** (`#modalResetAttempt`).
    - Mengizinkan guru mengosongkan riwayat pengerjaan siswa sehingga siswa dapat mengikuti ulang kuis dari awal selama batas waktu belum berakhir.
    - Dilengkapi notifikasi peringatan otomatis jika deadline kuis telah berakhir.
- **Penyederhanaan Format Soal Menjodohkan (Opsi Teks Instruksi)**:
  - Pada format *Menjodohkan*, form instruksi soal disembunyikan dan diubah menjadi opsional.
  - Backend controller ([`QuestionBankController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuestionBankController.php) & [`QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuizController.php)) secara otomatis memberikan teks default `"Jodohkanlah item berikut dengan pasangannya yang benar:"` jika dikosongkan.
- **Standarisasi Modal Konfirmasi Hapus & Penataan UI Backend**:
  - Seluruh konfirmasi hapus bawaan browser `confirm()` di halaman Master Data (*Kelas, Mata Pelajaran, Kuis, Tugas, Materi, Role*) telah diganti menggunakan **Modal Bootstrap 5 Custom** yang rapi & informatif.
  - Menata tombol aksi (Edit, Hapus, Hasil Siswa) dengan ikon Tabler, badge warna, dan spacing tipografi yang presisi.
- **Pengujian & Regresi**:
  - **150 passed (528 assertions)** 100% green.

## [Fase 46] Smart Tab Switching Auto-Spawn & Custom Bootstrap Delete Modals — 2026-09-14

### Ditambahkan & Diperbarui
- **Fitur Auto-Spawn Cerdas saat Berpindah Format Soal**:
  - **Penyimpanan Semua Soal Sekaligus**: Ketika guru mengisikan soal (Pilihan Ganda) lalu mengeklik tab format lain (*Benar/Salah* atau *Menjodohkan*), sistem cerdas mendeteksi isi soal yang sudah ada, mempertahankan soal lama sebagai **Soal #1**, dan **secara otomatis menambahkan kartu baru (Soal #2)** untuk format baru yang dipilih.
  - Guru dapat membuat soal *Pilihan Ganda*, *Benar/Salah*, dan *Menjodohkan* secara berurutan hanya dengan mengeklik tab format, dan **seluruh soal akan otomatis tersimpan sekaligus dalam satu kali klik tombol Simpan**.
- **Penggantian Konfirmasi Hapus bawaan Browser dengan Modal Bootstrap**:
  - Seluruh konfirmasi hapus bawaan JS/browser (`confirm(...)`) diganti dengan **Custom Bootstrap 5 Modal** modern yang menarik:
    - **Modal Hapus Bank Soal ([`index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/index.blade.php))**: `#modalDeleteQuestionBank` lengkap dengan teks konfirmasi spesifik dan tombol *Ya, Hapus*.
    - **Modal Hapus Soal Kuis ([`show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/quizzes/show.blade.php))**: `#modalDeleteQuizQuestion` untuk menghapus soal kuis terdaftar.
    - **Modal Hapus Kartu Form Builder ([`create.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/create.blade.php) & [`show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/quizzes/show.blade.php))**: `#modalConfirmDeleteCard` dan `#modalAlertQb` untuk mengganti seluruh popup `alert()` & `confirm()`.
- **Pengujian & Regresi**:
  - **149 passed (523 assertions)** 100% green.

## [Fase 45] Question Bank UI Refinement & Matching Pair Simplification — 2026-09-14

### Ditambahkan & Diperbarui
- **Penyederhanaan & Penataan Tampilan Format Soal Menjodohkan (Matching)**:
  - **Halaman Daftar Bank Soal ([`resources/views/admin/question-banks/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/index.blade.php))**:
    - Kolom kunci jawaban pada soal tipe *Menjodohkan* kini menampilkan seluruh daftar pasangan premis & pasangan jawaban secara langsung dan rapi (contoh: `Premis ➔ Pasangan Jawaban`), tidak lagi menampilkan 1 opsi tunggal yang membingungkan.
    - Menata tata letak tabel agar sepenuhnya responsif di perangkat seluler dan desktop dengan badge format warna-warni yang kontras.
  - **Halaman Tambah Bank Soal ([`resources/views/admin/question-banks/create.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/create.blade.php))**:
    - Memperbarui antarmuka pengisian soal format *Menjodohkan* sehingga hanya fokus pada *Teks Pertanyaan / Instruksi* dan *Baris Pasangan Jawaban* (Premis ➔ Pasangan) secara bersih.
    - Responsivitas penuh dengan penyesuaian kolom grid (`col-12` pada layar seluler dan `col-md-5` dengan ikon panah di layar komputer).
  - **Halaman Edit Bank Soal ([`resources/views/admin/question-banks/edit.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/edit.blade.php))**:
    - Memperbarui halaman edit agar mendukung penuh ketiga format soal (*Pilihan Ganda*, *Benar/Salah*, dan *Menjodohkan*) secara dinamis.
    - Guru dapat mengedit soal menjodohkan dengan menambah/menghapus baris pasangan langsung di halaman edit.
- **Pengujian & Regresi**:
  - **149 passed (523 assertions)** 100% green.

## [Fase 44] Batch Question Creation & Quick Paste Parser (Buat & Simpan Banyak Soal Sekaligus) — 2026-09-14

### Ditambahkan & Diperbarui
- **Fitur Batch Question Builder di Detail Kuis & Bank Soal**:
  - **Multi-Question Form Builder**: Guru tidak lagi harus mengisi dan menyimpan soal satu per satu.
  - Tombol **"+ Tambah Butir Soal Lagi"**: Guru dapat menambah form butir soal sebanyak yang diinginkan dalam satu halaman dengan nomor butir otomatis dan switcher tipe format soal (*Pilihan Ganda*, *Benar/Salah*, *Menjodohkan*) yang independen untuk setiap butir.
  - Fitur **Duplikasi Butir Soal**: Tombol salin butir soal untuk mempercepat pembuatan variasi soal serupa.
  - Fitur **Hapus Butir Soal**: Tombol hapus kartu butir soal dinamis dengan proteksi minimal 1 kartu soal.
  - **Input Cepat Berbasis Teks (Modal ⚡ Input Cepat)**:
    - Parser teks cerdas regex di sisi klien yang secara otomatis mengenali format teks soal dari Word/Notepad:
      - *Pilihan Ganda*: Mendeteksi `1. Pertanyaan`, `A. Pilihan`, `B. Pilihan`, `Kunci: A`.
      - *Benar / Salah*: Mendeteksi `2. Pernyataan`, `Kunci: Benar` atau `Kunci: Salah`.
      - *Menjodohkan*: Mendeteksi format premis dan pasangan `Premis = Pasangan`.
    - Dilengkapi tombol **"Muat Contoh Format"** dan **"Konversi & Masukkan ke Form"** yang langsung memetakan teks ke kartu form builder.
  - Tombol **"Simpan Semua Soal (X Butir)"**: Mengirim seluruh butir soal dalam satu kali request dengan payload array `questions: [...]`.
- **Backend Batch Processing & Data Integrity**:
  - [`app/Http/Controllers/Admin/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuizController.php) (`storeQuestion`):
    - Mendukung penerimaan payload array `questions: [...]` maupun payload tunggal (kompatibel penuh ke belakang).
    - Membungkus proses penyimpanan dalam `DB::transaction` sehingga jika ada kegagalan, tidak ada data parsial tertinggal.
    - Filter sanitasi opsi kosong (`array_values(array_filter(...))`) otomatis mengabaikan baris opsi kosong.
  - [`app/Http/Controllers/Admin/QuestionBankController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuestionBankController.php) (`store`):
    - Mendukung penyimpanan batch banyak butir soal sekaligus ke Bank Soal dengan pemetaan kategori mata pelajaran yang konsisten.
- **Pengujian Otomatis & Regresi**:
  - Menambahkan test batch di [`tests/Feature/Admin/QuizCrudTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Admin/QuizCrudTest.php) (`test_guru_can_create_multiple_questions_simultaneously_batch`).
  - Menambahkan test batch di [`tests/Feature/Admin/QuestionBankCrudTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Admin/QuestionBankCrudTest.php) (`test_guru_can_create_multiple_questions_in_question_bank_batch`).
  - **149 passed (523 assertions)** 100% green.

## [Fase 43] Multi-Format Quiz Support (Pilihan Ganda, Benar/Salah, Menjodohkan) — 2026-09-14

### Ditambahkan & Diperbarui
- **Dukungan Tiga Format Soal Kuis Lengkap**:
  - **Pilihan Ganda (Multiple Choice)**: Format standar 2-5 opsi dengan 1 kunci jawaban benar.
  - **Benar / Salah (True / False)**: Format pernyataan dengan 2 pilihan pasti ("Benar" atau "Salah") dilengkapi switch interaktif.
  - **Menjodohkan (Matching Pairs)**: Format pasangan premis/pernyataan dan pasangan jawaban yang dapat ditambah/dikurangi secara dinamis oleh guru.
- **Skema Basis Data & Model Eloquent**:
  - Migrasi [`database/migrations/2026_09_14_000002_add_question_types_and_matching_support.php`](file:///c:/laragon/www/KELAS/lms_dani/database/migrations/2026_09_14_000002_add_question_types_and_matching_support.php):
    - Menambahkan kolom `question_type` (`multiple_choice`, `true_false`, `matching`) pada tabel `quiz_questions` dan `question_bank`.
    - Menambahkan kolom `match_text` pada tabel `quiz_question_options` dan `question_bank_options`.
    - Menambahkan kolom `answer_data` (tipe JSON) pada tabel `quiz_answers` untuk menyimpan pasangan jawaban siswa.
  - Model [`app/Models/QuizQuestion.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/QuizQuestion.php) & [`app/Models/QuestionBank.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/QuestionBank.php):
    - Menambahkan atribut fillable: `question_type`, `instructor_id`.
    - Menambahkan helper method: `isMultipleChoice()`, `isTrueFalse()`, `isMatching()`.
  - Model [`app/Models/QuizQuestionOption.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/QuizQuestionOption.php) & [`app/Models/QuestionBankOption.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/QuestionBankOption.php):
    - Menambahkan atribut fillable: `match_text`.
  - Model [`app/Models/QuizAnswer.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/QuizAnswer.php):
    - Menambahkan atribut fillable `answer_data` dengan array casting `['answer_data' => 'array']`.
- **Portal Guru / Admin (Pembuatan & Impor Soal)**:
  - **Detail Kuis Guru ([`resources/views/admin/quizzes/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/quizzes/show.blade.php))**:
    - Format switcher interaktif dengan tab pills: *Pilihan Ganda*, *Benar / Salah*, dan *Menjodohkan*.
    - Form dinamis untuk *Benar / Salah* dengan pemilihan kunci cepat (radio card).
    - Form dinamis untuk *Menjodohkan* dengan tombol tambah baris pasangan dan hapus baris.
    - Menampilkan badge format soal (`Pilihan Ganda`, `Benar / Salah`, `Menjodohkan`) pada setiap kartu soal.
    - Menampilkan tabel pratinjau pasangan premis & kunci cocok untuk soal tipe menjodohkan.
  - **Controller Kuis Admin ([`app/Http/Controllers/Admin/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuizController.php))**:
    - Method `storeQuestion`: Mendukung validasi dan penyimpanan spesifik untuk tipe `true_false` dan `matching`.
    - Method `importQuestions`: Mengimpor seluruh metadata format dan pasangan jawaban dari Bank Soal ke kuis aktif.
  - **Bank Soal Guru ([`app/Http/Controllers/Admin/QuestionBankController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuestionBankController.php), [`resources/views/admin/question-banks/create.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/create.blade.php), [`resources/views/admin/question-banks/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/question-banks/index.blade.php))**:
    - Guru dapat membuat soal tipe Pilihan Ganda, Benar/Salah, dan Menjodohkan di bank soal dengan UI responsif dan badge format di daftar tabel.
- **Portal Siswa (Pengerjaan Kuis Interaktif)**:
  - **Lembar Pengerjaan ([`resources/views/student/quizzes/attempt.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/attempt.blade.php))**:
    - **Adaptive Question Card**: Menyesuaikan tampilan berdasarkan format soal:
      - Pilihan Ganda: Kartu pilihan A/B/C/D/E interaktif.
      - Benar / Salah: Dua tombol seleksi besar dengan ikon centang/silang.
      - Menjodohkan: Antarmuka tabel premis dengan select dropdown pasangan acak (shuffled) untuk mencegah tebak urutan.
    - **Auto-Save AJAX Cerdas**: Menyimpan pilihan secara instan saat siswa memilih opsi atau mencocokkan dropdown (`matching_answers` payload).
  - **Engine Penilaian Otomatis & Submit ([`app/Http/Controllers/Student/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/QuizController.php))**:
    - Mendukung penilaian proporsional untuk soal tipe Menjodohkan (misal: 2 dari 4 pasang benar = 50% poin pada soal tersebut).
    - Menangani penyimpanan auto-save maupun final submit (termasuk kasus waktu habis otomatis).
- **Halaman Hasil & Review Siswa ([`resources/views/student/quizzes/result.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/result.blade.php))**:
    - **Badge Status Format**: Setiap soal menampilkan format soal dan status ketercapaian (*Benar Sempurna*, *Benar Sebagian*, *Salah*, atau *Tidak Dijawab*).
    - **Review Kartu Benar/Salah**: Menampilkan pilihan siswa vs kunci jawaban dengan badge berwarna tegas.
    - **Tabel Review Menjodohkan**: Menampilkan kolom premis, pasangan yang dipilih siswa (dengan ikon centang/silang), dan kunci jawaban yang benar.
    - **Statistik & Navigasi**: Palet nomor soal dan penghitungan akurasi mendukung penilaian parsial soal menjodohkan.
- **Pengujian Otomatis & Regresi**:
  - Menambahkan test case di [`tests/Feature/Admin/QuizCrudTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Admin/QuizCrudTest.php) dan [`tests/Feature/Student/StudentQuizTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Student/StudentQuizTest.php).
  - **147 Passed (511 assertions)** tanpa satupun kegagalan / error.
  - Verifikasi end-to-end browser subagent mencakup pengerjaan kuis kombinasi 3 format soal dan konfirmasi skor 100/100 pada halaman review.

## [Fase 42] Dedicated Student Profile Page & Strict Admin Portal Security — 2026-09-14

### Ditambahkan & Diperbarui
- **Halaman Profil Mandiri Khusus Siswa ([`resources/views/student/profile/edit.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/profile/edit.blade.php))**:
  - **Layout Frontend Terpisah**: Mengganti template bawaan yang sebelumnya menggunakan layout admin backend (`layouts.be.master`) dengan `<x-app-layout>` bertema RuangTerra tanpa menu/sidebar master data admin.
  - **Hero Header Profil Siswa**:
    - Breadcrumbs navigasi: *Dashboard / Profil Saya*.
    - Badge kategori *Akun & Pengaturan Profil Siswa*.
    - Heading utama dan ringkasan akun dengan badge *Siswa Aktif*.
  - **Kartu Identitas & Metadata Siswa**:
    - Lingkaran avatar besar dengan inisial nama siswa berkilau gradien.
    - Status akun aktif dan email.
    - Informasi akademik terdaftar: Kelas (`X-IPA-1`), NIS, dan tanggal terdaftar.
    - Tautan cepat langsung menuju *Laporan Belajar Siswa*.
    - Kartu tips keamanan akun belajar.
  - **Formulir Data Pribadi & Kontak**:
    - Input Nama Lengkap (dapat diedit).
    - Input Alamat Email (dapat diedit).
    - Field Kelas dan NIS ditampilkan secara informatif (read-only) dengan penjelas bahwa perubahan kelas dikelola pihak sekolah.
    - Tombol simpan perubahan berkilau dengan indikator status tersimpan.
  - **Formulir Keamanan & Kata Sandi Interaktif**:
    - Input kata sandi saat ini, kata sandi baru, dan konfirmasi kata sandi baru.
    - **Toggle Show/Hide Password**: Dilengkapi tombol ikon mata interaktif (`ti-eye` / `ti-eye-off`) untuk melihat/menyembunyikan teks kata sandi.
  - **Zona Berbahaya (Hapus Akun)**:
    - Peringatan detail mengenai konsekuensi penghapusan akun secara permanen.
    - Modal konfirmasi berbasis Bootstrap dengan validasi kata sandi wajib sebelum penghapusan.
- **Pemisahan Controller Profil ([`app/Http/Controllers/ProfileController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/ProfileController.php))**:
  - Method `edit()` secara otomatis mengembalikan `view('student.profile.edit')` untuk siswa, dan mempertahankan `view('profile.edit')` dengan layout backend untuk guru.
- **Pembatasan Akses Ketat Siswa ke Portal Admin**:
  - **Blokir Halaman Login Admin ([`app/Http/Controllers/Admin/Auth/AdminAuthenticatedSessionController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/Auth/AdminAuthenticatedSessionController.php))**: Siswa yang sedang login dan membuka `/admin/login` tidak lagi di-logout diam-diam, melainkan langsung dicegah dan dialihkan ke dashboard siswa dengan pesan peringatan: *"Akses ditolak. Anda login sebagai Siswa dan tidak diizinkan masuk ke portal admin."*
  - **Blokir Reset Password Admin ([`app/Http/Controllers/Admin/Auth/AdminPasswordResetLinkController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/Auth/AdminPasswordResetLinkController.php))**: Siswa yang membuka `/admin/forgot-password` langsung dicegah dan dialihkan ke dashboard siswa.
  - **Pengamanan Rute Akses `/admin` ([`routes/web.php`](file:///c:/laragon/www/KELAS/lms_dani/routes/web.php))**: Rute shortcut `/admin` memverifikasi peran pengguna terlebih dahulu sebelum mengarahkan ke dashboard admin.
  - **Pembaruan Middleware Role ([`app/Http/Middleware/EnsureUserHasRole.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Middleware/EnsureUserHasRole.php))**: Menyeragamkan pesan error akses ditolak ke portal admin.
  - **Banner Notifikasi Global ([`resources/views/layouts/app.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/layouts/app.blade.php))**: Menambahkan wadah alert dismissal untuk `session('error')` dan `session('success')` di bawah navbar.
- **Pengujian & Regresi**:
  - Menambahkan test case di [`tests/Feature/ProfileTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/ProfileTest.php) dan [`tests/Feature/AuthMiddlewareTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/AuthMiddlewareTest.php).
  - 100% PHPUnit test suite (**143 passed, 488 assertions**) lolos tanpa regresi.
  - Verifikasi browser lengkap mencakup screenshot profil mandiri, toggle visibilitas password, dan pencegahan pengalihan akses portal admin.

## [Fase 41] Discussion & QA Section Aesthetic Refinement & Compact Proportioning — 2026-09-14

### Ditambahkan & Diperbarui
- **Perampingan & Proporsi Kompak Ruang Diskusi ([`resources/views/student/materials/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/materials/show.blade.php))**:
  - **Dimensi & Padding Seimbang**: Mengganti padding tebal `p-4 p-md-5` menjadi `p-3 p-md-3.5`, menghilangkan ruang kosong berlebih agar selaras rapi dengan sidebar kanan.
  - **Header Card Kompak**: Menggunakan icon 28px proporsional, heading bersih `font-size: 0.98rem`, dan pill badge jumlah diskusi yang elegan.
  - **Desain Bubble Komentar Modern**:
    - Bubble utama berlatar putih bersih dengan border halus `#e2e8f0` dan bayangan lembut saat di-hover.
    - Komentar guru dilengkapi strip aksen biru di sisi kiri (`border-left: 3.5px solid #3b82f6`) dan badge pill `Guru` dengan ikon topi toga.
    - Komentar siswa dilengkapi badge pill `Siswa` yang rapi.
    - Avatar berukuran proporsional: 34px untuk komentar utama dan 28px untuk balasan.
    - Tombol aksi **Balas** dibuat diskret dan minimalis ala media sosial tanpa garis pemisah tebal yang memotong kartu.
  - **Scrollbox Terbatas & Halus**: Mengatur `max-height: 400px` dengan scrollbar tipis kustom (`scrollbar-width: thin;`) agar daftar komentar tidak mendominasi tinggi halaman.
  - **Formulir Balasan & Komentar Baru Minimalis**:
    - Form balasan inline dikemas dalam container halus dengan badge mention dan input group `form-control-sm`.
    - Form komentar baru utama di bagian bawah didesain ringkas dengan input rounded-3, tombol submit gradient bertema, serta catatan informasi kecil.
- **Automated Testing & Regresi**:
  - Seluruh rangkaian test suite (**141 passed, 478 assertions**) lulus 100%.

## [Fase 40] Threaded Discussion Replies (Social Media Style) & Indonesian Localization — 2026-09-14

### Ditambahkan & Diperbarui
- **Struktur Diskusi Bersarang / Threaded Replies ([`database/migrations/2026_09_14_000001_add_parent_id_to_material_discussions_table.php`](file:///c:/laragon/www/KELAS/lms_dani/database/migrations/2026_09_14_000001_add_parent_id_to_material_discussions_table.php))**:
  - Menambahkan kolom `parent_id` (foreign key nullable cascade on delete) pada tabel `material_discussions`.
  - Memperbarui model [`app/Models/MaterialDiscussion.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/MaterialDiscussion.php) dengan relasi `parent()` dan `replies()`.
  - Memperbarui model [`app/Models/Material.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Material.php) dengan relasi `rootDiscussions()` (hanya komentar utama ber-`parent_id IS NULL`).
- **Tampilan Balasan Komentar Menjorok ke Kanan Seperti di Media Sosial ([`resources/views/student/materials/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/materials/show.blade.php))**:
  - Posisi balasan diletakkan tepat di bawah komentar induk dan menjorok ke kanan (`ms-4 ms-md-5 ps-3 border-start border-2 border-primary-subtle`) menyerupai thread di media sosial (Reddit/Instagram/Twitter).
  - Avatar balasan berukuran proporsional (32px), dilengkapi bubble komentar tersendiri, badge role pengguna (Guru/Siswa), dan tombol aksi **Balas**.
  - **Inline Reply Form**: Setiap thread komentar memiliki form inline mandiri yang dapat dibuka melalui tombol "Balas", lengkap dengan badge `Membalas @NamaUser`, tombol batal (`x`), input text field, dan tombol submit.
- **Keterangan Waktu Bahasa Indonesia**:
  - Konfigurasi `\Carbon\Carbon::setLocale('id')` di [`app/Providers/AppServiceProvider.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Providers/AppServiceProvider.php) dan `config/app.php` serta `.env`.
  - Tampilan waktu pada setiap komentar dan balasan menggunakan format relatif bahasa Indonesia (e.g. *"5 menit yang lalu"*, *"2 jam yang lalu"*, *"1 hari yang lalu"*), dilengkapi tooltip hover format tanggal lengkap bahasa Indonesia (*"Senin, 14 September 2026 09:30 WIB"*).
- **Target Notifikasi Balasan Terfokus ([`app/Http/Controllers/Student/MaterialController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/MaterialController.php))**:
  - Ketika sebuah komentar dibalas, sistem langsung mengirim notifikasi khusus kepada pembuat komentar yang dibalas: *"X membalas komentar Anda di materi [Judul]"*.
  - Tautan notifikasi mengarah tepat ke elemen balasan (`#discussion-item-{id}`) dengan smooth scrolling.
- **Automated Testing & Regresi ([`tests/Feature/Student/StudentMaterialTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Student/StudentMaterialTest.php))**:
  - Menambahkan test case pengiriman balasan dengan `parent_id`, tampilan balasan menjorok di halaman detail materi, dan verifikasi keterangan waktu bahasa Indonesia (*"menit yang lalu"*).
  - Seluruh test suite lengkap (**141 passed, 478 assertions**) lulus 100%.

## [Fase 39] Complete Database Synchronization for Notifications (Material, Assignment, Quiz & Discussion Replies) — 2026-09-14

### Ditambahkan & Diperbarui
- **Sinkronisasi Data Riil Notifikasi Sesuai Database ([`database/seeders/NotificationSeeder.php`](file:///c:/laragon/www/KELAS/lms_dani/database/seeders/NotificationSeeder.php))**:
  - Mengganti seluruh data statis/dummy notifikasi lama dengan data faktual yang ada di database kelas siswa:
    - **Materi**: Menampilkan judul riil materi (misal: *"Konspirasi"*) dan nama mata pelajaran, dengan tautan langsung ke modul detail (`student/materials/{id}`).
    - **Tugas**: Menampilkan judul riil tugas (misal: *"Tugas Mandiri"*) dan mata pelajaran, dengan tautan langsung ke detail tugas (`student/assignments/{id}`).
    - **Kuis**: Menampilkan judul riil kuis (misal: *"Kuis minggu ke-2"* / *"cek"*), dengan tautan langsung ke petunjuk kuis (`student/quizzes/{id}`).
    - **Ruang Diskusi (Balasan Komentar)**: Mengirimkan notifikasi riil saat ada guru atau teman sekelas yang menanggapi diskusi pada materi terkait, dengan tautan langsung ke anchor forum diskusi (`student/materials/{id}#discussion-list`).
- **Otomasi Penerbitan Notifikasi di Controller**:
  - **Materi Baru ([`app/Http/Controllers/Admin/MaterialController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/MaterialController.php))**: Saat guru mempublikasikan materi baru, sistem otomatis membuat notifikasi untuk seluruh siswa di kelas terkait dengan judul riil materi.
  - **Kuis Baru ([`app/Http/Controllers/Admin/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/QuizController.php))**: Saat guru membuat kuis baru, notifikasi otomatis terbit untuk seluruh siswa di kelas tersebut.
  - **Tanggapan Ruang Diskusi ([`app/Http/Controllers/Student/MaterialController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/MaterialController.php))**: Saat komentar baru diposting, notifikasi dikirimkan ke guru pengampu materi serta seluruh siswa yang pernah berpartisipasi dalam diskusi materi tersebut.
  - **Pengumpulan & Penilaian Tugas ([`app/Http/Controllers/Student/AssignmentController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/AssignmentController.php), [`app/Http/Controllers/Admin/AssignmentSubmissionController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/AssignmentSubmissionController.php))**: Guru diberi tahu saat tugas dikumpulkan oleh siswa, dan siswa diberi tahu saat nilai tugas telah dimasukkan oleh guru.
- **Pembaruan Model & Resolver URL ([`app/Models/Notification.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Notification.php), [`app/Models/Material.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Material.php), [`app/Models/Quiz.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Quiz.php))**:
  - Menambahkan dukungan resolving tautan notifikasi diskusi tipe `comment` langsung menuju `#discussion-list`.
  - Memperbarui `$fillable` pada model `Material` dan `Quiz` untuk mendukung atribut `class_id`, `subject_id`, dan `instructor_id`.
- **Automated Testing & Regresi ([`tests/Feature/NotificationTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/NotificationTest.php))**:
  - Menambahkan test case untuk redirection notifikasi materi, kuis, dan diskusi serta pengujian otomatis penerbitan notifikasi saat guru menambah materi/kuis dan saat komentar diskusi dibalas.
  - Seluruh rangkaian test suite lengkap (**139 tests, 469 assertions**) di PHPUnit lulus 100%.

## [Fase 38] Assignment Evaluation Layout Refinement & One-Time Submission Enforcement — 2026-09-14

### Ditambahkan & Diperbarui
- **Perapian & Perampingan Tampilan "Hasil Penilaian & Koreksi Guru" ([`resources/views/student/assignments/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/assignments/show.blade.php))**:
  - **Dimensi Compact & Proporsional**: Mengganti tampilan tipografi raksasa `display-5` dan padding tebal `p-4 p-md-5` menjadi kartu evaluasi yang ringkas, rapi, dan seimbang (`p-3 p-md-3.5`).
  - **Metric Strip Nilai Akhir**: Menampilkan perolehan nilai dalam rounded box terstruktur (`1.65rem`) dengan badge tuntas dinilai, informasi waktu penilaian (`graded_at`), serta nama guru pengampu yang menilai.
  - **Catatan & Umpan Balik Guru**: Kotak catatan guru dikemas dalam callout berlatar lembut dengan border hijau elegan, teks terstruktur yang mudah dibaca, dan tidak lagi memakan ruang vertikal secara berlebihan.
  - **Harmonisasi Lembar Tugas**: Menyesuaikan padding kartu petunjuk soal agar seragam dan rapi di seluruh layar.
- **Kebijakan Pengumpulan Tugas Hanya 1 (Satu) Kali**:
  - **Validasi Backend ([`app/Http/Controllers/Student/AssignmentController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/AssignmentController.php))**:
    - Menambahkan pengecekan database pada method `submit()`. Jika siswa telah memiliki riwayat submission pada tugas tersebut, request pengumpulan kedua langsung diblokir dan dialihkan dengan pesan peringatan bahwa tugas hanya dapat dikumpulkan 1 kali.
  - **Penguncian Formulir Frontend ([`resources/views/student/assignments/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/assignments/show.blade.php))**:
    - Ketika tugas telah dikumpulkan, badge header berganti menjadi `Sudah Dikumpulkan (Terkunci)`.
    - Alert pesan revisi diganti dengan konfirmasi pengumpulan terkunci.
    - Textarea jawaban otomatis berstatus `disabled readonly` dengan border hijau lembut.
    - Tombol kirim/perbarui digantikan oleh tombol disabled `Telah Dikumpulkan (Pengumpulan 1x)` dengan icon gembok terkunci.
- **Automated Testing & Regresi ([`tests/Feature/Student/StudentAssignmentTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Student/StudentAssignmentTest.php))**:
  - Menambahkan test case `test_siswa_cannot_submit_assignment_more_than_once` untuk memverifikasi bahwa pengumpulan berulang ditolak dan jawaban awal siswa tetap terjaga utuh.
  - Seluruh rangkaian test suite lengkap (**133 tests, 456 assertions**) di PHPUnit lulus 100%.

## [Fase 37] Footer Styling Enhancement, Brand Icon & Typography Standardization — 2026-09-13

### Ditambahkan & Diperbarui
- **Penyelarasan Identitas Brand di Footer ([`resources/views/layouts/app.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/layouts/app.blade.php))**:
  - **Penyesuaian Icon Resmi**: Mengganti icon lingkaran generik `ti-school` dengan icon brand resmi RuangTerra (`asset('images/icon.png')`) berdimensi presisi, filter drop-shadow cyan bercahaya, serta efek hover transform interaktif (zoom & tilt).
  - **Standarisasi Tipografi Brand**: Mengadopsi font `Jost` dengan bobot ultra-bold (900), teks **Ruang** putih bersih, **Terra** berwarna cyan neon (`#38bdf8`) dengan glow text-shadow, dan subjudul `Learning Platform` berhuruf kapital dengan tracking elegan (1.8px) yang identik dengan logo navbar header.
- **Redesain Modern High-End Footer ([`public/css/theme-custom.css`](file:///c:/laragon/www/KELAS/lms_dani/public/css/theme-custom.css))**:
  - **Modern Dark Gradient Background**: Mengganti warna flat kaku `#0b1727` dengan gradasi midnight navy mewah (`linear-gradient(180deg, #091424 0%, #040912 100%)`).
  - **Aksen Border & Cahaya Atas**: Mengganti border atas solid tebal dengan border tipis elegan `1px solid rgba(56, 189, 248, 0.22)` disertai garis berkas cahaya gradien cyan di bagian atas (`.arsha-footer::before`) dan bayangan elevasi yang dalam (`box-shadow: 0 -12px 35px rgba(0, 0, 0, 0.45)`).
  - **Interactive Navigation Links**: Tautan navigasi (`.footer-nav-link`) kini memiliki transisi hover geser kanan halus dengan indikator panah cyan interaktif.
  - **Glassmorphic Contact Badges**: Informasi kontak (kampus, email, telepon) dilengkapi icon dalam rounded container glassmorphic dengan warna aksen cyan RuangTerra.
  - **PWA Mobile Safe Padding**: Penyesuaian padding bawah footer (`padding-bottom: calc(2rem + 65px)`) pada perangkat mobile agar tidak terhalang oleh *Mobile Bottom Nav Bar*.
  - **Pill Back-to-Top Button**: Tombol "Kembali ke Atas" diperbarui menjadi pill glassmorphic modern dengan micro-interaction hover glow.
- **Automated Testing & Regresi**:
  - Seluruh rangkaian test suite lengkap (**132 tests, 448 assertions**) di PHPUnit tetap lulus 100%.

## [Fase 36] Notification Direct Redirection, Dynamic Host Resolution & 404 Prevention — 2026-09-13

### Ditambahkan & Diperbarui
- **Perbaikan Navigasi & Tautan Notifikasi Langsung ke Detail Tugas ([`app/Models/Notification.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Notification.php), [`app/Http/Controllers/NotificationController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/NotificationController.php))**:
  - **Dynamic Host Resolution (`resolved_url`)**: Mengatasi masalah error 404 ketika notifikasi diklik pada konfigurasi Laragon virtual host / subpath dengan mem-parsing path dan menyelaraskannya secara dinamis terhadap domain request aktif saat ini.
  - **Direct Resource Redirection**:
    - Ketika siswa mengklik notifikasi tugas (`new_assignment`), sistem secara cerdas langsung mengarahkan browser ke halaman detail tugas terkait ([`student.assignments.show`](file:///c:/laragon/www/KELAS/lms_dani/routes/web.php)) alih-alih daftar umum atau URL mati.
    - Begitu pula untuk notifikasi materi (`new_material`) dan kuis (`new_quiz`), link langsung mengarah ke detail modul atau halaman panduan kuis yang bersangkutan.
- **Penerbitan Notifikasi Otomatis Guru ([`app/Http/Controllers/Admin/AssignmentController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/AssignmentController.php))**:
  - Saat guru membuat tugas baru, sistem otomatis membuat record notifikasi di database untuk seluruh siswa di kelas terkait dengan tautan langsung ke detail tugas (`route('student.assignments.show', $assignment)`).
- **Pembaruan Interface Navigasi ([`resources/views/layouts/navigation.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/layouts/navigation.blade.php))**:
  - Menggunakan atribut `href="{{ $notif->resolved_url }}"` pada dropdown notifikasi desktop dan drawer mobile.
- **Pembaruan Model & Seeder ([`app/Models/Assignment.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Models/Assignment.php), [`database/seeders/NotificationSeeder.php`](file:///c:/laragon/www/KELAS/lms_dani/database/seeders/NotificationSeeder.php))**:
  - Menambahkan `subject_id`, `class_id`, dan `instructor_id` ke dalam `$fillable` pada model `Assignment`.
  - Memperbarui `NotificationSeeder` agar tautan notifikasi awal siswa langsung mengarah ke resource spesifik kelas.
- **Automated Testing & Regresi**:
  - Menambahkan test case `test_clicking_assignment_notification_redirects_directly_to_the_assignment_detail` pada [`NotificationTest`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/NotificationTest.php).
  - Seluruh rangkaian test suite lengkap (**132 tests, 448 assertions**) di PHPUnit lulus 100%.

## [Fase 35] Exam Lockdown, Fullscreen Enforcement, Anti Tab-Switch & Exit Prevention — 2026-09-13

### Ditambahkan & Diperbarui
- **Sistem Penguncian Pengerjaan Kuis & Anti-Kecurangan ([`resources/views/student/quizzes/attempt.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/attempt.blade.php))**:
  - **Enforcement Mode Layar Penuh (Fullscreen Lockdown)**:
    - Saat kuis dimulai, siswa disambut dengan modal instruksi wajib Layar Penuh (*Fullscreen Exam Gate Modal*).
    - Browser dipaksa masuk ke mode Fullscreen (`document.documentElement.requestFullscreen()`).
    - Jika siswa keluar dari layar penuh (menekan tombol Esc atau F11), overlay darurat langsung mengunci seluruh konten kuis hingga tombol *"Kembalikan ke Layar Penuh"* diklik.
  - **Deteksi Pembukaan Tab Baru / Berpindah Jendela (Page Visibility & Window Blur Monitoring)**:
    - Memanfaatkan Page Visibility API (`visibilitychange` & `document.hidden`) serta event `window.blur` untuk mendeteksi seketika saat siswa membuka tab baru, beralih ke tab lain di browser, atau melakukan Alt+Tab ke aplikasi lain.
    - Setiap pelanggaran perpindahan tab memicu **suara peringatan alarm beep** (via browser Web Audio API sintetis) dan memunculkan **Modal Peringatan Keras Pelanggaran** dengan indikator counter pelanggaran (1/3, 2/3, 3/3).
    - Data pelanggaran dicatat dan disinkronkan secara persisten di `localStorage` (`quiz_violation_{quiz_id}_{attempt_id}`) sehingga tidak dapat di-reset dengan me-refresh halaman.
    - **Auto-Submit Pelanggaran Ketat (Maksimal Toleransi 1 Kali)**: Batas toleransi ditetapkan maksimal **1 kali**. Jika siswa terdeteksi berpindah tab, beralih jendela/aplikasi, atau keluar dari mode layar penuh (Esc/F11), sistem akan langsung membunyikan alarm peringatan, menampilkan modal status penyerahan, dan mengumpulkan kuis secara otomatis ke server dalam 1.5 detik.
    - **Penyembunyian Total Navigasi & Header Website (Immersive Focus)**:
      - Seluruh elemen navbar utama (`nav`, `.edusite-header`, `.arsha-header`), menu navigasi, tombol akun, breadcrumb luar, mobile bottom nav, serta footer disembunyikan sepenuhnya (`display: none !important; height: 0 !important;`).
      - Bar informasi kuis (`quiz-attempt-hero`) diposisikan langsung menempel di koordinat paling atas layar (`top: 0 !important;`), memastikan seluruh viewport dalam mode fullscreen terfokus 100% hanya pada lembar pengerjaan kuis tanpa distraksi menu luar.
    - **Kunci Tombol Back Browser (History Lock)**: Mencegah navigasi tombol Back di browser menggunakan HTML5 History API (`pushState` & `popstate`), disertai toast peringatan bahwa navigasi keluar dilarang.
    - **Kunci Refresh & Close Tab (`beforeunload`)**: Mencegah penutupan tab atau perubahan URL manual sebelum kuis diselesaikan secara resmi.
    - **Blokir Shortcut & Klik Kanan**: Menonaktifkan klik kanan (`contextmenu`), text selection (`user-select: none`), jalan pintas keyboard DevTools (`F12`, `Ctrl+Shift+I`, `Ctrl+Shift+J`), `Ctrl+U`, copy/paste (`Ctrl+C`, `Ctrl+V`, `Ctrl+X`), serta shortcut tab baru (`Ctrl+T`, `Ctrl+N`).
  - **Penanganan Kuis Tanpa Butir Soal & Proteksi Akses**:
    - Validasi pada controller (`QuizController::start` & `attempt`) untuk mencegah siswa memulai kuis yang belum memiliki butir soal dari guru.
    - Halaman panduan kuis (`show.blade.php`) menampilkan status informatif *"Soal Ujian Belum Tersedia"* dan menonaktifkan tombol pengerjaan jika kuis masih kosong.
    - Halaman pengerjaan (`attempt.blade.php`) dilengkapi empty state terstruktur dengan tombol kembali ke daftar kuis dan tidak memicu lockdown jika tidak ada soal.
    - Menambahkan 3 butir soal geografi pada kuis uji coba *"cek"* di database lokal.
    - **Perbaikan Render Tampilan Kuis**: Memperbaiki tag penutup `</style>` yang terlewat pada stylesheet pengerjaan kuis sehingga seluruh antarmuka kuis dan modal ter-render dengan sempurna di browser.
    - **Penanganan Soal Belum Terjawab (Unanswered Questions & Auto-Submit Safe)**:
      - Menambahkan migrasi database `make_selected_option_id_nullable_in_quiz_answers_table` untuk mengubah kolom `selected_option_id` pada tabel `quiz_answers` menjadi `nullable()`.
      - Memperbarui logika penyerahan kuis pada [`QuizController::submit`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/QuizController.php) agar jika siswa belum menjawab butir soal tertentu saat pengumpulan mandiri ataupun auto-submit darurat, nilai `null` dapat tersimpan tanpa memicu *Integrity constraint violation (1048)*.
      - Memastikan jawaban tersimpan yang pernah masuk melalui auto-save AJAX tidak tertimpa kosong jika form diserahkan secara otomatis.
- **Automated Testing & Regresi**:
  - Penambahan test case `test_siswa_can_submit_quiz_with_unanswered_questions_without_integrity_error` di `StudentQuizTest`.
  - Seluruh rangkaian test suite lengkap (**131 tests, 446 assertions**) di PHPUnit lulus 100%.

## [Fase 34] Redesign of Student Learning Progress Report (Laporan Diri Siswa) — 2026-09-13

### Ditambahkan & Diperbarui
- **Redesain Menyeluruh Laporan Progres Belajar Diri Siswa ([`resources/views/student/report/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/report/index.blade.php))**:
  - **Dedicated Hero Section (`report-hero`)**: Banner visual modern dengan gradien khas RuangTerra (`#1e3d60` -> `#20456E`), background glow dekoratif, breadcrumb navigation, identitas siswa, kelas, tanggal pembaruan, dan status pill predikat nilai akhir (*Sangat Baik / Baik / Cukup*).
  - **Proporsi Visual Seimbang & Rapi (Tidak Terlalu Besar & Tidak Dempet)**:
    - Judul hero diatur ke `fs-4 fw-bold`, ukuran icon box proporsional (`42px`), serta padding vertikal hero disesuaikan ke `1.75rem` sehingga nyaman dan tidak mendominasi layar.
    - 4 Kartu Metrik Ringkasan Prestasi (*Progres Materi*, *Rata-Rata Tugas*, *Rata-Rata Kuis*, dan *Nilai Keseluruhan*) dirancang seragam dengan mini progress meter bar di setiap kartu, pembagian vertikal rapi, dan jarak antar elemen berbatas garis tipis dashed yang bernapas lega.
    - Tabel Rekapitulasi Performa per Mata Pelajaran dilengkapi nomor urut rata tengah, icon penanda mapel, badge kode mapel, progress bar modul berwarna gradien, badge nilai tugas & kuis yang kontras lembut, serta tombol *"Buka Materi"* yang interaktif.
    - Dua kolom detail (*Riwayat & Nilai Tugas* dan *Riwayat & Skor Kuis*) ditata simetris dengan jarak vertikal antar baris yang rapi (`mb-1` pada judul, subtext tanggal dengan icon, badge nilai berborder tipis, dan tombol aksi link).
- **Peningkatan Controller Laporan Siswa ([`app/Http/Controllers/Student/ReportController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/ReportController.php))**:
  - Menghitung predikat nilai otomatis (A, B, C, D) berbasis skor gabungan.
  - Menghitung metrik analitik breakdown per mata pelajaran (progres materi, rerata tugas, rerata kuis).
- **Automated Testing ([`tests/Feature/Student/StudentReportTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Student/StudentReportTest.php))**:
  - Penambahan test case `test_student_can_view_subject_breakdown_and_evaluations_history`.
  - Seluruh 130 tests (440 assertions) lulus 100%.

## [Fase 33] Real-Time Persistent Server Timer, Question Navigation Palette, Color Status Legend & Enhanced Quiz Guidelines — 2026-09-13


### Ditambahkan & Diperbarui
- **Penghitungan Waktu Kuis Persisten Mutlak di Server ([`app/Http/Controllers/Student/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/QuizController.php))**:
  - Waktu pengerjaan kuis dihitung secara akurat menggunakan **UNIX Epoch Integer Timestamp** (`$attempt->started_at->getTimestamp()`), bebas dari ambiguitas tanda minus Carbon `diffInSeconds` atau perbedaan zona waktu database.
  - Formula server: `$remainingSeconds = max(0, ($startTimestamp + $durationSeconds) - time())`.
  - Ketika siswa keluar dari halaman kuis, menutup tab, berpindah halaman, maupun me-refresh browser, **waktu pengerjaan tetap terus berjalan mundur** secara nyata tanpa ter-reset ke durasi awal.
  - Frontend (`attempt.blade.php`) menyinkronkan target waktu selesai dengan server dan `localStorage`, serta menghitung mundur berbasis waktu riil (`Date.now()`).
  - Jika waktu pengerjaan telah habis saat siswa kembali ke halaman kuis, sistem secara otomatis menghitung skor dari jawaban yang tersimpan, menandai kuis selesai (`submitted_at = now()`), dan mengarahkan siswa ke halaman hasil kuis dengan alert informatif.
  - Jika waktu kuis habis saat siswa masih membuka tab kuis, auto-submit dipicu di frontend dan divalidasi juga di backend (`saveAnswer` menolak pengisian setelah expired).
- **Auto-Save Jawaban Real-Time via AJAX ([`routes/web.php`](file:///c:/laragon/www/KELAS/lms_dani/routes/web.php) & [`QuizController::saveAnswer`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/QuizController.php))**:
  - Route baru `student.quizzes.save-answer` untuk menyimpan pilihan jawaban secara instan via AJAX begitu siswa memilih radio opsi.
  - Micro-feedback status sinkronisasi jawaban (*Menyimpan...* -> *Jawaban Tersimpan Otomatis*) pada header ujian.
- **Tampilan Satu Halaman Satu Soal & Panel Navigasi Nomor Soal ([`resources/views/student/quizzes/attempt.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/attempt.blade.php))**:
  - **Sistem Stepper Satu Halaman Satu Soal (Fit Viewport)**: Soal kuis disajikan bertahap satu per satu dengan layout compact dan proporsional (ukuran font, spasi opsi, dan padding kartu dioptimalkan agar pas dalam satu layar tanpa perlu scroll ke bawah), dilengkapi tombol navigasi *"← Sebelumnya"* dan *"Selanjutnya →"*, serta tombol *"Selesai & Kumpulkan"* pada baris bawah kartu soal.
  - **Panel Navigasi Nomor Soal Interaktif (Question Palette)**: Siswa dapat melompat ke nomor soal mana pun secara instan dengan mengklik kotak nomor di sidebar kanan.
  - **Pembeda 3 Warna Status Nomor Soal**:
    - 🔵 **Warna Biru (`#2563EB`)**: Menandakan nomor soal yang **sedang dibuka / sedang dikerjakan saat ini**, dengan efek *glow ring* biru aktif (`box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.35)`).
    - 🟢 **Warna Hijau (`#10B981`)**: Menandakan nomor soal yang **Sudah Dijawab**.
    - ⚪ **Warna Abu-abu Netral (`#F1F5F9` / `#CBD5E1`)**: Menandakan nomor soal yang **Belum Dijawab**.
  - **Keterangan Warna (Legend)** di bawah navigasi soal:
    - Box legenda dengan 3 indikator warna:
      - 🔵 **Sedang Dikerjakan**: Badge nomor soal aktif saat ini (misal: *No. 1*).
      - 🟢 **Sudah Dijawab**: Counter dinamis jumlah soal terjawab.
      - ⚪ **Belum Dijawab**: Counter dinamis jumlah soal yang belum terjawab.
    - Progress bar persentase penyelesaian kuis.
  - Timer countdown di frontend disinkronkan dengan target timestamp server (`Date.now() + remainingSeconds * 1000`) dan otomatis melakukan submit jika waktu habis.
- **Redesain Halaman Hasil & Preview Kuis Serupa Layout Pengerjaan ([`resources/views/student/quizzes/result.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/result.blade.php))**:
  - **Tampilan Konsisten 1:1 dengan Lembar Kuis**: Mengusung layout modern 2-kolom dengan sticky header bar, kartu soal compact satu halaman satu soal (stepper) dengan tombol *"← Sebelumnya"* dan *"Selanjutnya →"*, serta panel navigasi nomor soal di sebelah kanan.
  - **Penghitungan & Penampilan Waktu Pengerjaan**:
    - Menghitung durasi aktual pengerjaan kuis secara presisi dari selisih `attempt->started_at` dan `attempt->submitted_at` (`$durationSeconds`).
    - Ditampilkan secara elegan dan mencolok pada **Header Bar** (Badge *"Waktu Pengerjaan: X Menit Y Detik"*) dan pada **Sidebar Ringkasan Hasil**.
  - **Panel Navigasi Nomor Soal Interaktif (Review Palette)**:
    - Siswa dapat melompat ke pembahasan nomor mana pun dengan mengklik kotak nomor soal.
    - Pembeda warna status hasil kuis:
      - 🔵 **Ring Biru Glowing**: Nomor soal yang sedang dibuka/dilihat.
      - 🟢 **Warna Hijau (`#10B981`)**: Soal dijawab **Benar**.
      - 🔴 **Warna Merah (`#EF4444`)**: Soal dijawab **Salah**.
      - ⚪ **Warna Abu-abu Netral (`#F1F5F9`)**: Soal **Tidak Dijawab**.
    - Dilengkapi **Legenda Keterangan Warna** dinamis yang memperlihatkan counter jumlah soal benar, salah, tidak dijawab, dan nomor yang sedang dilihat.
  - **Tampilan Review Opsi Jawaban**:
    - Opsi yang dipilih siswa dan benar ditandai dengan badge *"Jawaban Anda & Jawaban Benar"* (hijau).
    - Opsi yang dipilih siswa namun salah ditandai *"Jawaban Anda"* (merah).
    - Opsi kunci jawaban benar ditandai dengan *"Jawaban Benar"* (hijau outline).
- **Penyempurnaan Petunjuk & Tata Tertib Pengerjaan Kuis ([`resources/views/student/quizzes/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/show.blade.php))**:
  - Kartu petunjuk didesain sangat lega, mewah, dan terstruktur dengan 6 kartu poin ketentuan:
    1. Waktu berjalan real-time di server (tetap berjalan meski tab ditutup).
    2. Penyimpanan otomatis (*Auto-Save*) jawaban setiap kali opsi diklik.
    3. Tampilan satu halaman satu soal beserta mini legenda visual 3 warna: biru (sedang dikerjakan), hijau (sudah dijawab), dan abu-abu (belum dijawab).
    4. Auto-submit otomatis ke sistem saat waktu mencapai 00:00.
    5. Kestabilan koneksi internet & daya baterai.
    6. Tombol kumpulkan jawaban kuis setelah yakin dengan hasil pengerjaan.

### Diuji & Diverifikasi
- Menambahkan test suite baru di [`tests/Feature/Student/StudentQuizTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Student/StudentQuizTest.php):
  1. `test_quiz_timer_decreases_accurately_on_page_refresh`
  2. `test_quiz_auto_submits_when_attempt_timer_has_expired`
  3. `test_save_answer_returns_expired_when_time_exceeded`
  4. `test_siswa_can_view_quiz_result_with_duration_and_palette`
- Seluruh **129 automated unit & feature tests** di Laravel lulus 100% (433 assertions).


## [Fase 32] Redesign & Visual Enhancement of Student Quiz Pages (Kuis Online) — 2026-09-12

### Ditambahkan & Diperbarui
- **Perombakan Halaman Katalog Kuis Siswa ([`resources/views/student/quizzes/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/index.blade.php))**:
  - **Page Hero Header**: Desain banner modern RuangTerra dengan gradien `#20456E` - `#3368A0`, breadcrumb navigasi, pill status pencapaian, judul kuis, serta search bar responsif.
  - **Widget Pencapaian Kuis Saya**: Progress bar pencapaian kelulusan kuis, counter total kuis, selesai, dan belum ikut kuis dengan layout kartu lega (*spacious*).
  - **Toolbar Filter Interaktif & Pengalih Tampilan (Grid & List View)**:
    - Spacing dan padding kartu toolbar yang longgar dan nyaman (`padding: 1.25rem 1.5rem !important;`), tidak sempit.
    - Filter pills mata pelajaran dinamis dengan counter jumlah kuis.
    - Filter status kuis (*Semua Status*, *Selesai*, *Sedang Dikerjakan*, *Belum Dikerjakan*).
    - Tombol *View Mode Switcher* (Grid View vs List View) dengan preferensi tersimpan di `localStorage`.
    - Live client-side instant search judul, mapel, atau guru.
  - **Grid View & List View Modern**:
    - Grid View: Kartu kuis bergaya modern dengan watermark piala, pill durasi waktu, butir soal, badge tenggat waktu dengan indikasi urgensi, avatar guru pengampu, serta badge status bergradien.
    - List View: Tabel modern dengan baris bergaris halus, pill tenggat waktu, dan tombol aksi terintegrasi.
- **Perombakan Halaman Petunjuk & Detail Kuis ([`resources/views/student/quizzes/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/show.blade.php))**:
  - Hero header dengan badge mapel, kelas, guru pengampu, dan status capsule yang lapang (*spacious*).
  - Layout 2 kolom terstruktur: Petunjuk dan tata tertib kuis, kartu status pengerjaan, tombol aksi mulai kuis yang elegan, 4 kotak parameter kuis (durasi, jumlah butir, bobot poin, format pilihan ganda), profil guru, serta kartu deadline dengan penanda `WIB`.
  - Sistem penguncian waktu habis (*overdue*): Jika batas waktu kuis telah terlewat dan belum pernah dikerjakan, tombol terkunci otomatis dengan status *"Waktu Habis — Kuis Ditutup"*.
- **Perombakan Halaman Pengerjaan Kuis Interaktif ([`resources/views/student/quizzes/attempt.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/attempt.blade.php))**:
  - Sticky countdown timer header dengan indikator sisa waktu yang jelas dan animasi pulsa peringatan saat waktu tersisa kurang dari 5 menit.
  - Kartu butir soal yang lega, tipografi nyaman dibaca (`fs-5`, line-height 1.7), dan opsi jawaban berupa ubin interaktif (*option tiles*) yang ergonomis dengan efek hover dan active state halus.
- **Perombakan Halaman Hasil & Pembahasan Kuis ([`resources/views/student/quizzes/result.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/quizzes/result.blade.php))**:
  - Hero header dengan skor kuis, kartu showcase perolehan nilai, ringkasan jawaban benar/salah, serta review pembahasan butir soal yang informatif.
- **Peningkatan Controller & Keamanan ([`app/Http/Controllers/Student/QuizController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/QuizController.php))**:
  - Menambahkan filter pencarian kata kunci, filter mapel (`subject_id`), filter status kuis, penghitungan metrics pencapaian kuis, serta proteksi penolakan mulai kuis jika waktu telah berakhir.

### Diuji & Diverifikasi
- Seluruh 125 pengujian otomatis PHPUnit lulus 100% (417 assertions).
- Pengujian browser subagent: Grid View, List View toggle, filter toolbar, dan halaman petunjuk kuis teruji sempurna dan bebas dari elemen sempit (*not cramped*).

## [Fase 31] Redesign & Visual Enhancement of Student Assignment Pages (Tugas Kelas) — 2026-09-12

### Ditambahkan & Diperbarui
- **Perombakan Halaman Tugas Kelas Siswa ([`resources/views/student/assignments/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/assignments/index.blade.php))**:
  - **Page Hero Header**: Desain banner modern RuangTerra dengan gradien `#20456E` - `#3368A0`, breadcrumb navigasi, pill status pengumpulan (*x Terkumpul*), judul modul tugas, serta search bar responsif.
  - **Widget Ringkasan Progres Tugas**: Kartu ringkasan terintegrasi dengan progress bar (`progressPercent%`), counter total tugas, sudah dikumpulkan, dan belum dikumpulkan dengan visual badge modern.
  - **Toolbar Filter Interaktif & Pengalih Tampilan (Grid & List View)**:
    - Spacing dan padding kartu toolbar yang nyaman (`padding: 1.25rem 1.5rem !important;`), tidak mepet ke tepi.
    - Filter pills mata pelajaran dengan counter dinamis.
    - Filter status penyerahan (*Semua Status*, *Belum Mengumpulkan*, *Sudah Dikumpulkan*, *Sudah Dinilai*).
    - Tombol *View Mode Switcher* (Grid View vs List View) dengan preferensi tersimpan di `localStorage`.
    - Live client-side instant search saat siswa mengetikkan kata kunci tugas.
  - **Grid View & List View Modern**:
    - Grid View: Kartu tugas bergaya modern dengan watermark icon, badge tenggat waktu dengan indikasi urgensi (warna merah untuk mendekati deadline), avatar guru pengampu, serta badge status bergradien.
    - List View: Tabel modern dengan baris bergaris halus, pill tenggat waktu, dan tombol aksi terintegrasi.
- **Perombakan Halaman Detail & Pengumpulan Tugas ([`resources/views/student/assignments/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/assignments/show.blade.php))**:
  - Mengganti layout dasar dengan Page Hero Header bergradien RuangTerra, badge mapel, kelas, dan status penyerahan/penilaian yang lapang (*spacious* dan tidak sempit).
  - Memperbaiki pemformatan tanggal zona waktu PHP agar tidak menghasilkan string `370750` melainkan string `WIB` yang bersih.
  - Memperjelas kontras teks batas waktu (*deadline*) dengan pill berkontras tinggi pada hero banner.
  - **Sistem Kunci Tenggat Waktu (Overdue Lock)**: Jika batas waktu telah lewat, formulir pengumpulan tugas terkunci secara otomatis, textarea menjadi *disabled*, dan tombol kirim dinonaktifkan dengan label *"Waktu Habis"* serta dicegah di sisi backend controller.
  - Layout 2 kolom terstruktur:
    - **Kolom Utama**: Detail instruksi tugas dengan styling dokumen profesional, kartu nilai & feedback guru (jika tugas sudah diperiksa), serta formulir pengumpulan tugas responsif dengan textarea modern dan catatan waktu pengiriman terakhir.
    - **Sidebar Kanan**: Kartu informasi tenggat waktu (*Deadline Alert*), panduan & checklist pengumpulan tugas siswa, profil guru pengampu (avatar, kontak, NIP), dan tombol navigasi kembali.
- **Peningkatan Controller & Keamanan ([`app/Http/Controllers/Student/AssignmentController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/AssignmentController.php))**:
  - Menambahkan validasi penolakan pengumpulan tugas yang telah melewati tenggat waktu (`isPast()`) di sisi backend.
  - Menambahkan dukungan filter pencarian keyword judul dan deskripsi tugas, filter mapel (`subject_id`), filter status pengumpulan, penghitungan metrics progres tugas, serta data mata pelajaran untuk filter pills.

### Diuji & Diverifikasi
- Seluruh 124 pengujian otomatis PHPUnit lulus 100% (413 assertions).
- Pengujian interaktif browser: Grid View, List View toggle, filter toolbar, dan halaman detail serta formulir penyerahan tugas teruji sempurna.

> Riwayat Fase 1-30 diarsipkan di CHANGELOG-archive.md

