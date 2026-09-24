# Product Requirements Document (PRD)

## Learning Management System (LMS) — Full Laravel

|             |                       |
| ----------- | --------------------- |
| **Author**  | Gana Purba Kusuma     |
| **Status**  | Ready for Development |
| **Version** | 1.9                   |

---

## 1. Overview

### 1.1 Tujuan

Membangun LMS sederhana untuk portfolio, menunjukkan penerapan clean code practice (Form Request, Service Layer, Policy, Testing, N+1 optimization) pada domain bisnis baru — berbeda dari HRIS dan Job Tracker yang sudah ada.

### 1.2 Target User

- **Guru (Instructor/Admin)**: mengelola kelas, materi, tugas, kuis, bank soal, dan menilai jawaban siswa. Akses lewat dashboard admin.
- **Siswa (Student)**: mengakses materi, mengerjakan tugas dan kuis sesuai kelasnya. Akses lewat halaman frontend (setelah login).

### 1.3 Batasan Scope

- Materi berupa teks, upload dokumen (PDF/file), atau link YouTube (embed)
- Tugas berupa jawaban essay/teks dari siswa (bukan upload file)
- Kuis hanya bisa dikerjakan **1 kali** oleh siswa
- Siswa dikelompokkan per **kelas** (1 siswa = 1 kelas); siswa **memilih kelas sendiri saat registrasi** (dropdown)
- **Guru/Admin hanya dapat ditambahkan langsung dari database** (via seeder atau tinker) — tidak ada halaman register untuk guru
- Materi, tugas, dan kuis langsung diasosiasikan ke **kelas** — tidak ada abstraksi "Course"
- Siswa **otomatis** dapat mengakses materi/tugas/kuis yang tersedia untuk kelasnya

---

## 2. Tech Stack

| Layer                     | Teknologi                                                      |
| ------------------------- | -------------------------------------------------------------- |
| Backend + Admin Dashboard | Laravel (Blade)                                                |
| Frontend Siswa            | Laravel (Blade) — 1 aplikasi yang sama, beda layout/tema       |
| Database                  | MySQL                                                          |
| Autentikasi               | Laravel Breeze (session-based, Blade stack)                    |
| Timer Kuis                | Vanilla JS (countdown client-side, submit otomatis saat habis) |
| Realtime                  | Laravel Echo + Pusher (WebSocket) — untuk diskusi & notifikasi |

**Keputusan arsitektur**: full Laravel Blade dipilih karena seluruh konten LMS berada di balik login (tidak butuh SEO), sehingga kelebihan SPA/Vue tidak relevan. Pendekatan ini memaksimalkan kecepatan development dan kemudahan maintenance dengan 1 codebase. **Laravel Breeze** dipilih sebagai starter kit autentikasi. **Vanilla JS** digunakan untuk countdown timer kuis (tanpa dependency tambahan). **Laravel Echo + Pusher** digunakan untuk: (a) komentar diskusi materi realtime, (b) notifikasi siswa saat ada materi/tugas baru, dan (c) notifikasi guru di backend saat ada komentar baru di ruang diskusi.

### 2.1 Design System & PWA Specification

| Layer / Scope             | Spesifikasi & Nilai                                                                                        |
| ------------------------- | ---------------------------------------------------------------------------------------------------------- |
| **Backend (Admin Guru)**  | Menggunakan tema asli dari template [`public/template/be/index.html`](file:///c:/laragon/www/KELAS/lms_dani/public/template/be/index.html) tanpa penimpaan warna |
| **Frontend (Siswa/Public)**| Halaman siswa & landing page dengan warna utama **`#66A3BF`**, aksen **`#3368A0`**, mint **`#C8DFDB`**, background **`#F2EFE7`** |
| **Pendekatan Layout**     | **User-Friendly, Clean, & PWA Mobile-First** — Tampilan responsif seluler khusus Frontend                 |
| **PWA Capabilities**      | Web App Manifest (`manifest.json`), Service Worker (`sw.js`), Installable Standalone App, Offline Caching |

---

## 3. Functional Requirements

### 3.1 Autentikasi & Role

| ID     | Requirement                                                                                                                                                                           |
| ------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-1.0 | Siswa dapat mendaftar (register) di `/register` dengan mengisi nama, memilih kelas (dropdown), email, dan password. Role **otomatis di-set `siswa`** — tidak ada pilihan role di form |
| FR-1.1 | Satu halaman login (`/login`) digunakan oleh **semua user** (guru dan siswa) dengan email & password                                                                                  |
| FR-1.2 | Setelah login, sistem mendeteksi role dan melakukan redirect: role `guru` → `/admin/dashboard`, role `siswa` → `/dashboard`                                                           |
| FR-1.3 | Role dikelola lewat tabel `roles`; pengecekan role via relasi `user->role->name`, dibatasi lewat middleware/Policy                                                                    |
| FR-1.4 | Dashboard admin (`/admin/*`) hanya bisa diakses role `guru`                                                                                                                           |
| FR-1.5 | Siswa yang mencoba mengakses `/admin/*` langsung diarahkan ke `/dashboard` (redirect, bukan 403)                                                                                      |
| FR-1.6 | User dapat meminta reset password via halaman **Forgot Password** (`/forgot-password`) menggunakan email — sistem mengirim link reset ke email                                        |
| FR-1.7 | User dapat membuat password baru lewat link reset yang dikirim ke email (**Reset Password**)                                                                                          |
| FR-1.8 | User yang sudah login dapat **mengganti password** dari halaman profil/pengaturan dengan mengisi password lama dan password baru                                                      |

---

### 3.2 Admin — Menu Master

Menu **Master** adalah kelompok navigasi utama di admin dashboard, berisi sub-menu berikut:

| ID     | Sub-menu       | Requirement                                                                                                            |
| ------ | -------------- | ---------------------------------------------------------------------------------------------------------------------- |
| FR-2.1 | Siswa          | Guru dapat melihat daftar siswa beserta kelas yang dipilih siswa saat registrasi (read-only)                           |
| FR-2.2 | Kelas          | Guru dapat membuat, mengedit, menghapus kelas (nama kelas)                                                             |
| FR-2.3 | Materi         | Guru dapat membuat, mengedit, menghapus materi (CRUD) — detail di §3.3                                                 |
| FR-2.4 | Tugas          | Guru dapat membuat, mengedit, menghapus tugas (CRUD) — detail di §3.4                                                  |
| FR-2.5 | Bank Soal      | Guru dapat membuat, mengedit, menghapus soal pilihan ganda beserta kunci jawaban — detail di §3.6                      |
| FR-2.6 | Koreksi Tugas  | Guru dapat melihat seluruh submission tugas siswa per kelas dan memberikan nilai + feedback — detail di §3.4           |
| FR-2.7 | Role           | Admin dapat membuat, mengedit, menghapus **nama role** (CRUD role saja — misal: tambah role "admin", ubah nama, hapus) |
| FR-2.8 | User           | Admin dapat melihat daftar semua user, melihat role saat ini, dan **mengubah role** yang ditetapkan ke user tertentu   |
| FR-2.9 | Mata Pelajaran | Admin dapat membuat, mengedit, menghapus mata pelajaran dan **menetapkan guru** yang mengampu mata pelajaran tersebut  |

---

### 3.2b Admin — Dashboard Guru (Tampilan Setelah Login)

> Guru yang login dari `/login` di-redirect ke `/admin/dashboard`. Ini adalah halaman pertama yang dilihat guru setelah masuk.

#### Ringkasan Statistik (Kartu Ringkasan)

| Kartu                 | Data yang Ditampilkan                                         |
| --------------------- | ------------------------------------------------------------- |
| Total Kelas           | Jumlah kelas yang terdaftar di sistem                         |
| Total Siswa           | Jumlah siswa aktif di seluruh kelas                           |
| Tugas Belum Dikoreksi | Jumlah submission tugas yang belum diberi nilai oleh guru ini |
| Kuis Aktif            | Jumlah kuis milik guru ini yang deadline-nya belum terlewat   |

#### Navigasi Menu Admin (Sidebar/Navbar)

| Menu Utama    | Sub-menu / Deskripsi                                                              |
| ------------- | --------------------------------------------------------------------------------- |
| **Dashboard** | Halaman ringkasan statistik (di atas)                                             |
| **Master**    | Siswa, Kelas, Materi, Tugas, Bank Soal, Koreksi Tugas, Role, User, Mata Pelajaran |
| **Kuis**      | Daftar kuis milik guru; buat kuis baru, kelola soal & pengaturan                  |
| **Laporan**   | Laporan Materi, Laporan Tugas, Laporan Kuis                                       |
| **Logout**    | Keluar dari sesi dan kembali ke halaman `/login`                                  |

#### FR Admin Dashboard

| ID      | Requirement                                                                                  |
| ------- | -------------------------------------------------------------------------------------------- |
| FR-2b.1 | Guru melihat 4 kartu ringkasan statistik di halaman dashboard admin                          |
| FR-2b.2 | Kartu "Tugas Belum Dikoreksi" hanya menghitung submission tugas milik guru yang sedang login |
| FR-2b.3 | Kartu "Kuis Aktif" hanya menghitung kuis milik guru yang sedang login                        |
| FR-2b.4 | Sidebar/navbar menampilkan seluruh menu navigasi admin sesuai tabel di atas                  |

---

### 3.3 Admin — Materi

| ID     | Requirement                                                                                                                                                                                                 |
| ------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-3.1 | Guru memilih **kelas** DAN **mata pelajaran** tujuan saat membuat materi (mata pelajaran dibatasi hanya yang diampu guru tersebut, lihat FR-2.9)                                                            |
| FR-3.2 | Materi mendukung 3 tipe konten: **teks** (rich text), **dokumen** (upload PDF/file), atau **link YouTube** (embed otomatis)                                                                                 |
| FR-3.3 | Guru dapat menentukan urutan tampil materi                                                                                                                                                                  |
| FR-3.4 | Guru hanya dapat mengelola materi yang dibuat oleh dirinya sendiri                                                                                                                                          |
| FR-3.5 | Setiap materi memiliki **ruang diskusi realtime**: guru dan siswa (di kelas yang sama) dapat memposting komentar/pertanyaan; komentar baru muncul langsung tanpa reload halaman (via Laravel Echo + Pusher) |
| FR-3.6 | Guru dapat menghapus komentar diskusi yang tidak sesuai                                                                                                                                                     |
| FR-3.7 | Upload file lampiran pada Bank Materi mendukung **Fast Chunked Upload** (2.5MB per chunk) dengan real-time progress bar persentase (0%–100%) untuk mencegah terjadinya timeout server pada file berukuran besar (15MB–50MB+), mendukung format PDF, DOCX, PPTX, XLSX, dan Gambar (menolak ZIP, RAR, TXT) |

---

### 3.3b Admin — Koreksi Tugas

> Dapat diakses lewat sub-menu **Koreksi Tugas** di Menu Master (FR-2.6) atau dari halaman detail tugas.

| ID      | Requirement                                                                                |
| ------- | ------------------------------------------------------------------------------------------ |
| FR-3b.1 | Guru memilih kelas dan tugas untuk melihat daftar submission siswa                         |
| FR-3b.2 | Guru dapat melihat jawaban essay tiap siswa beserta status (belum dinilai / sudah dinilai) |
| FR-3b.3 | Guru dapat memberikan nilai (angka) dan feedback teks untuk setiap submission              |
| FR-3b.4 | Setelah dinilai, siswa dapat melihat nilai dan feedback dari halaman tugas mereka          |

---

### 3.4 Admin — Tugas

| ID     | Requirement                                                                            |
| ------ | -------------------------------------------------------------------------------------- |
| FR-4.1 | Guru memilih **kelas** DAN **mata pelajaran** tujuan saat membuat tugas                |
| FR-4.2 | Guru mengisi judul tugas, deskripsi/soal essay, dan batas waktu pengumpulan (deadline) |
| FR-4.3 | Guru dapat melihat daftar jawaban siswa per kelas untuk setiap tugas                   |
| FR-4.4 | Guru dapat memberikan nilai dan feedback untuk setiap jawaban siswa                    |
| FR-4.5 | Guru hanya dapat mengelola tugas yang dibuat oleh dirinya sendiri                      |

---

### 3.5 Admin — Kuis

| ID     | Requirement                                                                                                                                                                                                                 |
| ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-5.1 | Guru memilih **kelas** DAN **mata pelajaran** tujuan saat membuat kuis                                                                                                                                                       |
| FR-5.2 | Guru mengisi judul kuis                                                                                                                                                                                                     |
| FR-5.3 | Guru dapat menambah soal ke kuis dengan dua cara: (a) buat soal baru langsung, atau (b) pilih dari Bank Soal                                                                                                                |
| FR-5.4 | Guru mengisi **poin per soal** (`points_per_question`). Total nilai dihitung secara proporsional skala 0–100 berdasarkan butir soal dan unit pasangan yang benar                                                           |
| FR-5.5 | Guru mengatur **batas waktu pengumpulan kuis** (deadline): tanggal & jam. Siswa yang melewati deadline dianggap tidak mengerjakan                                                                                           |
| FR-5.6 | Guru mengatur **durasi pengerjaan** (dalam menit): **countdown timer** tampil di halaman kuis siswa, diimplementasikan dengan Vanilla JS. Timer mulai berjalan sejak siswa masuk, dan form submit otomatis saat waktu habis |
| FR-5.7 | Guru hanya dapat mengelola kuis yang dibuat oleh dirinya sendiri                                                                                                                                                            |
| FR-5.8 | Kuis mendukung kombinasi 3 format soal (*Pilihan Ganda*, *Benar / Salah*, *Menjodohkan*) dengan kalkulasi penilaian skor otomatis proporsional (0–100)                                                                     |
| FR-5.9 | Guru dapat melihat rekap dan detail jawaban seluruh siswa per kelas (`/admin/quizzes/{quiz}/students`), serta dapat mereset riwayat pengerjaan siswa (*Reset Attempt*) jika diperlukan                                       |

---

### 3.6 Admin — Bank Soal

| ID     | Requirement                                                                                                                                                                             |
| ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-6.1 | Guru dapat membuat, mengedit, dan menghapus soal pilihan ganda                                                                                                                          |
| FR-6.2 | Setiap soal memiliki: teks pertanyaan, minimal 2 pilihan jawaban, dan 1 jawaban yang ditandai sebagai benar                                                                             |
| FR-6.3 | Soal dari bank soal dapat dipilih dan dimasukkan ke dalam kuis manapun                                                                                                                  |
| FR-6.4 | Guru dapat membuat, mengedit, dan menghapus soal format **Benar / Salah** (`true_false`) dengan 2 opsi pasti ("Benar" dan "Salah") serta menandai 1 kunci jawaban yang benar          |
| FR-6.5 | Guru dapat membuat, mengedit, dan menghapus soal format **Menjodohkan** (`matching`) dengan minimal 2 baris pasangan premis dan target pencocokan (`match_text`)                       |
| FR-6.6 | Guru dapat menambahkan banyak butir soal sekaligus (**Batch Question Builder**) dalam satu form multi-tab dinamis dan menyimpannya secara simultan ke Bank Soal maupun Kuis            |
| FR-6.7 | Guru dapat mengimpor butir-butir soal secara otomatis dari dokumen Word (`.docx`), PDF (`.pdf`), atau teks (`.txt`) via Document Question Parser Service terintegrasi                 |
| FR-6.8 | Teks soal mendukung format kaya (media): Tabel Markdown/HTML, Gambar Markdown/HTML, upload gambar via TinyMCE/AJAX, serta indikator badge `(tabel)` dan `(gambar)`                    |
| FR-6.9 | Import & Deteksi naskah soal dari dokumen Word/PDF/Teks mendukung **Chunked Upload** otomatis pada file besar (>1MB) sebelum diparse, menjamin ekstraksi butir soal tanpa terkena batas `max_execution_time` server |

---

### 3.7 Admin — Laporan

| ID     | Sub-laporan    | Requirement                                                                                                                     |
| ------ | -------------- | ------------------------------------------------------------------------------------------------------------------------------- |
| FR-7.1 | Laporan Materi | Guru dapat melihat daftar kelas; di dalam setiap kelas terdapat daftar siswa beserta status penyelesaian materi (selesai/belum) |
| FR-7.2 | Laporan Tugas  | Guru dapat melihat nilai tugas siswa dikelompokkan per kelas                                                                    |
| FR-7.3 | Laporan Kuis   | Guru dapat melihat nilai kuis siswa dikelompokkan per kelas                                                                     |

---

### 3.8 Frontend

Frontend adalah tampilan **di luar** admin dashboard (`/admin/*`) — dipakai oleh **siswa** untuk belajar sehari-hari, dan oleh **guru** sebagai tampilan ringan untuk memantau kelas & notifikasi (terpisah dari kerja CRUD berat yang tetap dilakukan di `/admin/*`).

#### 3.8.0 Navigasi Menu Frontend

| Role      | Menu Navigasi                                                                                                                                    |
| --------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Siswa** | **Dashboard**, **Notifikasi**, **Kelas Saya** (→ pilih Mata Pelajaran → Materi / Tugas / Kuis), **Profil & Laporan Diri**, Logout                    |
| **Guru**  | **Dashboard**, **Kelas Saya** (→ pilih Kelas & Mata Pelajaran yang diampu → lihat Materi / Tugas / Kuis yang sudah dibuat, ikut diskusi), **Notifikasi**, Logout |

**Catatan**: menu berat seperti CRUD Materi/Tugas/Kuis/Bank Soal, Laporan, dan Koreksi Tugas **tetap dikerjakan di admin dashboard** (`/admin/*`, §3.2). Menu **Kelas Saya** di frontend, untuk **aktivitas melihat/membaca konten** (materi, tugas, kuis, diskusi), **memakai Blade view yang SAMA** baik diakses siswa maupun guru — dibedakan lewat `@can` directive (guru pemilik konten melihat tombol Edit/Hapus tambahan, siswa tidak). Ini menghindari duplikasi 2 halaman terpisah untuk konten yang sama; lihat §3.9.3 untuk detail pola implementasinya.

---

#### 3.8.1 Halaman Dashboard — Siswa

| ID      | Requirement                                                                                                                                 |
| ------- | ------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-8.0a | Siswa dapat melihat dashboard berisi: nama kelas, **materi terakhir yang dipelajari** (judul + mata pelajaran + link lanjut baca), jumlah materi belum selesai, tugas belum dikerjakan, kuis belum dikerjakan |
| FR-8.0b | Setiap item ringkasan di dashboard dapat diklik langsung menuju halaman yang relevan                                                        |
| FR-8.0c | Siswa menerima **notifikasi realtime** (via Pusher) saat guru menambahkan materi baru di kelas & mata pelajaran yang diikutinya             |
| FR-8.0d | Siswa menerima **notifikasi realtime** (via Pusher) saat guru menambahkan tugas baru di kelas & mata pelajaran yang diikutinya              |

#### 3.8.1b Halaman Dashboard — Guru (Frontend, Bukan Admin)

| ID       | Requirement                                                                                                          |
| -------- | ---------------------------------------------------------------------------------------------------------------------- |
| FR-8.0f  | Guru (di frontend, bukan `/admin/dashboard`) melihat ringkasan: daftar kelas & mata pelajaran yang diampu, jumlah komentar diskusi baru yang belum dibalas |
| FR-8.0g  | Guru menerima **notifikasi realtime** (via Pusher) saat ada komentar/pertanyaan baru dari siswa di materi miliknya   |

#### 3.8.1c Menu Notifikasi

| ID      | Requirement                                                                                                                                       |
| ------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-8.0h | Menu **Notifikasi** (siswa & guru) menampilkan daftar notifikasi terurut terbaru, mencakup: komentar baru di diskusi, materi baru, dan tugas baru |
| FR-8.0i | Notifikasi yang belum dibaca ditandai visual berbeda (misal badge/bold); klik notifikasi mengarah langsung ke konten terkait dan menandainya sebagai sudah dibaca |
| FR-8.0j | Badge jumlah notifikasi belum dibaca tampil di ikon lonceng navbar, update realtime tanpa reload                                                  |

#### 3.8.1d Menu "Kelas Saya"

| ID      | Requirement                                                                                                                                               |
| ------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-8.0k | **Siswa**: membuka "Kelas Saya" → tampil daftar **Mata Pelajaran** yang tersedia untuk kelasnya → klik salah satu mata pelajaran → masuk ke daftar Materi / Tugas / Kuis khusus mata pelajaran itu |
| FR-8.0l | **Guru**: membuka "Kelas Saya" → tampil daftar **Kelas + Mata Pelajaran** yang diampunya → klik salah satu → lihat Materi / Tugas / Kuis yang sudah dibuat untuk kombinasi kelas & mapel tersebut, serta dapat membalas diskusi dari sana |
| FR-8.0m | Daftar Mata Pelajaran yang tampil untuk siswa hanya yang **benar-benar punya konten** (minimal 1 materi/tugas/kuis) untuk kelas siswa tersebut — mata pelajaran kosong tidak ditampilkan |

---

#### 3.8.2 Materi (Siswa)

| ID     | Requirement                                                                                                                                                        |
| ------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| FR-8.1 | Siswa dapat melihat daftar materi untuk kelas & mata pelajaran yang sedang dipilih (dari menu Kelas Saya)                                                          |
| FR-8.2 | Siswa dapat membaca/mengakses materi dan menandainya sebagai selesai                                                                                               |
| FR-8.3 | Siswa dapat berpartisipasi di ruang diskusi pada setiap materi (baca & posting komentar); komentar baru muncul **realtime tanpa reload** via Laravel Echo + Pusher |

#### 3.8.3 Tugas (Siswa)

| ID     | Requirement                                                                           |
| ------ | ------------------------------------------------------------------------------------- |
| FR-8.4 | Siswa dapat melihat daftar tugas untuk kelas & mata pelajaran yang sedang dipilih     |
| FR-8.5 | Siswa dapat melihat detail tugas dan mengisi jawaban essay                            |
| FR-8.6 | Siswa dapat submit jawaban tugas — **hanya 1 kali**, tidak bisa diedit setelah submit |
| FR-8.7 | Siswa dapat melihat nilai dan feedback tugas setelah dikoreksi guru                   |

#### 3.8.4 Kuis (Siswa)

| ID      | Requirement                                                                                                                                                        |
| ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| FR-8.8  | Siswa dapat melihat daftar kuis untuk kelas & mata pelajaran yang sedang dipilih (termasuk status: belum/sudah/expired)                                            |
| FR-8.9  | Siswa dapat mengerjakan kuis — **hanya 1 kali kesempatan**; **countdown timer** (Vanilla JS) tampil di layar dan form otomatis ter-submit saat waktu habis         |
| FR-8.10 | Siswa tidak dapat mengerjakan kuis jika sudah melewati deadline                                                                                                    |
| FR-8.11 | Siswa dapat melihat **halaman hasil kuis** setelah submit, berisi: skor total yang diperoleh                                                                       |
| FR-8.12 | Halaman hasil kuis menampilkan **review setiap soal**: teks pertanyaan, jawaban yang dipilih siswa, jawaban yang benar, dan status (benar/salah) untuk setiap soal |

#### 3.8.5 Profil & Laporan Diri

| ID      | Requirement                                                                                                                                                     |
| ------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| FR-8.13 | Siswa dapat melihat halaman profil/laporan diri yang berisi: daftar materi yang sudah diselesaikan, nilai seluruh tugas, dan skor seluruh kuis miliknya sendiri |
| FR-8.14 | Data pada halaman profil hanya menampilkan data milik siswa yang sedang login (tidak bisa lihat data siswa lain)                                                |
| FR-8.15 | Navbar frontend siswa menampilkan tombol **Logout** yang mengakhiri sesi dan mengarahkan ke halaman `/login`                                                    |

---

### 3.9 Frontend — Spesifikasi Teknis & PWA

#### 3.9.1 Struktur Folder View

```
resources/views/
├── layouts/
│   ├── admin.blade.php          ← layout admin, pakai tema public/template/be/
│   └── frontend.blade.php       ← layout frontend siswa/guru, warna sesuai §2.1
├── frontend/
│   ├── dashboard/
│   │   ├── student.blade.php    ← FR-8.0a–8.0d
│   │   └── teacher.blade.php    ← FR-8.0f–8.0g
│   ├── notifications/
│   │   └── index.blade.php      ← FR-8.0h–8.0j
│   ├── my-class/
│   │   ├── subjects.blade.php   ← daftar mapel (siswa) / kelas+mapel (guru), FR-8.0k–8.0m
│   │   ├── materials/
│   │   │   ├── index.blade.php  ← list materi per mapel, FR-8.1
│   │   │   └── show.blade.php   ← DIPAKAI BERSAMA siswa & guru (lihat §3.9.3), FR-8.2–8.3
│   │   ├── assignments/
│   │   │   ├── index.blade.php  ← FR-8.4
│   │   │   └── show.blade.php   ← form submit (siswa) / lihat jawaban (guru), FR-8.5–8.7
│   │   └── quizzes/
│   │       ├── index.blade.php  ← FR-8.8
│   │       ├── take.blade.php   ← halaman pengerjaan + timer JS, FR-8.9–8.10
│   │       └── result.blade.php ← FR-8.11–8.12
│   └── profile/
│       └── index.blade.php      ← FR-8.13–8.14
└── auth/                         ← bawaan Laravel Breeze (login, register, forgot/reset password)
```

#### 3.9.2 Daftar Route Frontend

| Route | Method | Akses | Keterangan |
|---|---|---|---|
| `/dashboard` | GET | siswa, guru | Redirect ke view berbeda sesuai role (FR-1.2) |
| `/notifikasi` | GET | siswa, guru | FR-8.0h |
| `/notifikasi/{id}/read` | POST | siswa, guru | Tandai 1 notifikasi sebagai dibaca |
| `/kelas-saya` | GET | siswa, guru | FR-8.0k / FR-8.0l |
| `/kelas-saya/{subject}/materi` | GET | siswa, guru | FR-8.1 |
| `/kelas-saya/materi/{material}` | GET | siswa, guru | View sama, `@can` membedakan aksi (FR-8.2–8.3) |
| `/kelas-saya/materi/{material}/selesai` | POST | siswa | Tandai materi selesai (FR-8.2) |
| `/kelas-saya/materi/{material}/diskusi` | POST | siswa, guru | Kirim komentar (FR-3.5) |
| `/kelas-saya/{subject}/tugas` | GET | siswa, guru | FR-8.4 |
| `/kelas-saya/tugas/{assignment}` | GET | siswa, guru | FR-8.5 |
| `/kelas-saya/tugas/{assignment}/submit` | POST | siswa | FR-8.6 |
| `/kelas-saya/{subject}/kuis` | GET | siswa, guru | FR-8.8 |
| `/kelas-saya/kuis/{quiz}/mulai` | GET/POST | siswa | Mulai attempt, catat `started_at` (FR-8.9) |
| `/kelas-saya/kuis/{quiz}/submit` | POST | siswa | Submit jawaban, hitung skor (FR-8.9) |
| `/kelas-saya/kuis/{quiz}/hasil` | GET | siswa | FR-8.11–8.12 |
| `/profil` | GET | siswa | FR-8.13 |
| `/profil/ganti-password` | POST | siswa, guru | FR-1.8 |

#### 3.9.3 Pola Shared View (Siswa & Guru)

Untuk menghindari duplikasi, halaman **melihat konten** (materi/tugas/kuis) memakai **1 Blade view yang sama**, dibedakan lewat Policy:

```blade
{{-- resources/views/frontend/my-class/materials/show.blade.php --}}
@extends('layouts.frontend')

@section('content')
<h1>{{ $material->title }}</h1>
<div>{!! $material->content !!}</div>

@can('update', $material)
    {{-- Cuma tampil untuk guru pemilik materi ini --}}
    <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-warning">Edit di Admin</a>
@endcan

@if (auth()->user()->isSiswa())
    {{-- Cuma tampil untuk siswa --}}
    <form action="{{ route('materials.complete', $material) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">Tandai Selesai</button>
    </form>
@endif

@include('frontend.my-class.materials._discussion', ['material' => $material])
@endsection
```

Controller yang menangani route ini **satu**, tidak dipecah jadi `StudentMaterialController` dan `TeacherMaterialController` terpisah.

#### 3.9.4 PWA — Konfigurasi

**`public/manifest.json`**
```json
{
  "name": "LMS - Learning Management System",
  "short_name": "LMS",
  "start_url": "/dashboard",
  "display": "standalone",
  "background_color": "#F2EFE7",
  "theme_color": "#3368A0",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
```

**`public/sw.js`** — Service Worker dengan strategi **cache-first untuk assets statis**, **network-first untuk halaman dinamis** (supaya data materi/tugas/kuis tetap update, tapi CSS/JS/icon di-cache untuk load cepat & offline shell):
```javascript
const CACHE_NAME = 'lms-static-v1';
const STATIC_ASSETS = ['/css/frontend.css', '/js/frontend.js', '/manifest.json'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.destination === 'document') {
        // Network-first untuk halaman (butuh data terbaru)
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
    } else {
        // Cache-first untuk CSS/JS/gambar
        event.respondWith(
            caches.match(event.request).then((cached) => cached || fetch(event.request))
        );
    }
});
```

**Registrasi di layout frontend** (`layouts/frontend.blade.php`):
```blade
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#3368A0">
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
</script>
```

#### 3.9.5 Design Token (CSS Variables)

```css
/* resources/css/frontend.css */
:root {
    --color-primary: #66A3BF;
    --color-accent: #3368A0;
    --color-mint: #C8DFDB;
    --color-bg: #F2EFE7;
}

body {
    background-color: var(--color-bg);
}

.btn-primary {
    background-color: var(--color-accent);
}
```

#### 3.9.6 Functional Requirements — PWA & Teknis

| ID | Requirement |
|---|---|
| FR-9.1 | Frontend dapat di-install sebagai aplikasi standalone di HP (via `manifest.json`) |
| FR-9.2 | Service Worker meng-cache asset statis (CSS/JS/icon) untuk mempercepat load dan menyediakan offline shell |
| FR-9.3 | Halaman dinamis (materi/tugas/kuis) **tidak** di-cache penuh — selalu ambil data terbaru dari server saat online (network-first) |
| FR-9.4 | Warna frontend konsisten memakai 4 token di §2.1 lewat CSS Variables, bukan hardcode hex di tiap file |
| FR-9.5 | Layout frontend mobile-first: dirancang dari breakpoint terkecil (320px) dahulu, diperluas ke `sm`/`md`/`lg` |

---

## 4. Data Model

> Skema aktual ada di SCHEMA.md — bagian ini rencana awal, mungkin tidak 100% akurat.

```
roles
├── id, name (misal: "guru", "siswa"), created_at, updated_at

subjects (mata pelajaran)
├── id, name, created_at, updated_at

classes
├── id, name (misal: "Kelas 10A"), created_at, updated_at

users
├── id, name, email, nip (string, nullable, UNIQUE — hanya diisi untuk guru), password,
│   role_id (FK → roles), class_id (FK → classes, nullable untuk guru),
│   created_at, updated_at

subject_user (pivot: guru ↔ mata pelajaran, many-to-many)
├── id, subject_id (FK → subjects), user_id (FK → users), created_at, updated_at

material_banks (bank materi)
├── id, instructor_id (FK → users), title, content_type (text/document/youtube),
│   content (text, nullable), document_path (string, nullable), video_url (string, nullable),
│   created_at, updated_at

materials (materi)
├── id, class_id (FK → classes), subject_id (FK → subjects), instructor_id (FK → users),
│   title, content_type (text/document/youtube),
│   content (text, nullable), document_path (string, nullable), video_url (string, nullable),
│   order, created_at, updated_at

material_progress
├── id, user_id (FK → users), material_id (FK → materials),
│   is_completed, completed_at, created_at, updated_at
│   UNIQUE(user_id, material_id)

material_discussions (ruang diskusi materi)
├── id, material_id (FK → materials), user_id (FK → users),
│   parent_id (FK → material_discussions, nullable — untuk threaded replies),
│   comment, created_at, updated_at

assignment_banks (bank tugas)
├── id, instructor_id (FK → users), title, description, created_at, updated_at

assignments (tugas)
├── id, class_id (FK → classes), subject_id (FK → subjects), instructor_id (FK → users),
│   title, description, due_date, created_at, updated_at

assignment_submissions
├── id, assignment_id (FK), student_id (FK → users),
│   answer_text, submitted_at, grade, feedback, graded_at
│   UNIQUE(assignment_id, student_id)

question_bank (bank soal)
├── id, instructor_id (FK → users), question_text,
│   question_type (string, default: "multiple_choice" — multiple_choice/true_false/matching),
│   created_at, updated_at

question_bank_options
├── id, question_bank_id (FK), option_text,
│   match_text (text, nullable — teks pasangan untuk tipe matching),
│   is_correct (boolean, default: false), created_at, updated_at

quizzes (kuis)
├── id, class_id (FK → classes), subject_id (FK → subjects), instructor_id (FK → users),
│   title, points_per_question, deadline (datetime), duration_minutes,
│   created_at, updated_at

quiz_questions
├── id, quiz_id (FK → quizzes),
│   question_bank_id (FK → question_bank, nullable — diisi jika soal dari bank soal / auto-sync),
│   question_text (text, nullable),
│   question_type (string, default: "multiple_choice" — multiple_choice/true_false/matching),
│   created_at, updated_at

quiz_question_options
├── id, quiz_question_id (FK), option_text,
│   match_text (text, nullable — teks pasangan untuk tipe matching),
│   is_correct (boolean, default: false), created_at, updated_at
│   (seluruh opsi disalin independen ke tabel ini sebagai immutable snapshot kuis)

quiz_attempts
├── id, student_id (FK → users), quiz_id (FK → quizzes),
│   score (decimal 5,2, nullable), started_at, submitted_at,
│   created_at, updated_at
│   UNIQUE(student_id, quiz_id)

quiz_answers
├── id, quiz_attempt_id (FK), quiz_question_id (FK),
│   selected_option_id (FK → quiz_question_options, nullable — untuk multiple_choice & true_false),
│   answer_data (json, nullable — array pasangan [option_id => match_text] untuk tipe matching),
│   created_at, updated_at

notifications
├── id, user_id (FK → users, penerima), type (comment/new_material/new_assignment/new_quiz),
│   title, message, related_url, is_read (boolean, default false), read_at (nullable),
│   created_at, updated_at
```

## Entity Relationship

```
Class (kelas) ──< User (siswa, via class_id)
      │
      ├──< Material ──< MaterialProgress
      │              └──< MaterialDiscussion (self-referencing via parent_id)
      ├──< Assignment ──< AssignmentSubmission
      └──< Quiz ──< QuizQuestion ──< QuizQuestionOption
                │
                └──< QuizAttempt ──< QuizAnswer

Subject (mata pelajaran) ──< Material   (via subject_id)
Subject (mata pelajaran) ──< Assignment (via subject_id)
Subject (mata pelajaran) ──< Quiz       (via subject_id)
Subject (mata pelajaran) ──>< User (guru) [many-to-many via subject_user]

QuizQuestion ──> QuestionBank (opsional/auto-sync)
QuestionBank ──< QuestionBankOption

Role ──< User (via role_id)

User (guru)  ──< MaterialBank (sebagai instructor_id)
User (guru)  ──< AssignmentBank (sebagai instructor_id)
User (guru)  ──< QuestionBank (sebagai instructor_id)
User (guru)  ──< Material (sebagai instructor_id)
User (guru)  ──< Assignment (sebagai instructor_id)
User (guru)  ──< Quiz (sebagai instructor_id)
User (guru)  ──< MaterialDiscussion

User (siswa) ──< MaterialProgress
User (siswa) ──< MaterialDiscussion
User (siswa) ──< AssignmentSubmission
User (siswa) ──< QuizAttempt ──< QuizAnswer
```

**Catatan penting**: kombinasi **Kelas + Mata Pelajaran** inilah yang menentukan konten mana yang dilihat siswa saat memilih "Kelas Saya" → pilih Mata Pelajaran. Materi/Tugas/Kuis yang tampil = yang `class_id`-nya sama dengan kelas siswa DAN `subject_id`-nya sama dengan mata pelajaran yang dipilih.

---

## 5. Keamanan

| Area                    | Penerapan                                                                                                                                                                                                                             |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **SQL Injection**       | Seluruh query menggunakan **Eloquent ORM** atau **Query Builder parameter binding** — raw query (`DB::statement`, `whereRaw`) hanya diizinkan dengan placeholder `?` atau named binding; string interpolasi langsung ke query dilarang |
| **XSS**                 | Output ke Blade selalu menggunakan `{{ }}` (auto-escape HTML); penggunaan `{!! !!}` (unescaped) hanya boleh untuk konten rich text materi yang sudah diproses oleh library sanitizer (misal: `HTMLPurifier`); komentar diskusi selalu di-escape |
| **CSRF**                | Semua form POST/PUT/PATCH/DELETE wajib menyertakan `@csrf`; Laravel `VerifyCsrfToken` middleware aktif di semua route web — tidak ada pengecualian kecuali route Pusher webhook jika diperlukan                                        |
| **File Upload**         | Upload dokumen materi divalidasi: ekstensi whitelist (`pdf,doc,docx,ppt,pptx,xlsx`), max size (misal 10MB), disimpan di `storage/app/private/` (bukan `public/`), diakses via signed URL `Storage::temporaryUrl()` — bukan path langsung |
| **Rate Limiting**       | Route login (`/login`) dan forgot password (`/forgot-password`) dilindungi Laravel `RateLimiter` (bawaan Breeze) — max 5 attempt per menit per IP; route kuis submit juga di-rate-limit untuk mencegah double-submit race condition    |
| **Security Headers**    | Middleware menambahkan header: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`. HTTPS enforced di production via `APP_URL` + `TrustProxies`                      |
| **Mass Assignment**     | `student_id`, `instructor_id`, `class_id` di relasi TIDAK masuk `$fillable`, selalu diisi dari `auth()->id()` atau route parameter                                                                                                    |
| **Register auto-role**  | Field `role` di-hardcode ke `siswa` di dalam `RegisterService` (hanya untuk siswa) — guru/admin ditambahkan langsung via seeder/tinker, tidak ada form register guru                                                                  |
| Forgot & Reset Password | Menggunakan fitur bawaan Breeze: token reset disimpan di tabel `password_reset_tokens`, link dikirim via email (Laravel Mail/SMTP)                                                                                                    |
| Blokir Backend Siswa    | Middleware `role:guru` dipasang di route group `/admin/*`; siswa yang mencoba akses di-redirect ke `/dashboard` (bukan 403)                                                                                                           |
| Redirect by Role        | `AuthenticatedSessionController` mendeteksi role setelah login: guru → `/admin/dashboard`, siswa → `/dashboard`                                                                                                                       |
| Otorisasi               | Policy per model (`MaterialPolicy`, `AssignmentPolicy`, `QuizPolicy`, `MaterialDiscussionPolicy`, dll) — guru hanya kelola resource miliknya, siswa hanya akses resource kelas mereka                                                 |
| Diskusi Materi          | Siswa hanya bisa posting komentar di materi kelas sendiri (validasi `class_id`). Guru bisa hapus komentar manapun di materi miliknya                                                                                                  |
| Duplikasi submission    | Unique constraint di level database (`assignment_submissions`, `quiz_attempts`) — bukan cuma validasi aplikasi                                                                                                                        |
| N+1 Prevention          | Eager loading wajib untuk material + progress + discussions, quiz + questions + attempts                                                                                                                                              |
| Validasi kondisional    | `MaterialRequest` memvalidasi: `content` wajib jika `content_type=text`, `document_path` wajib jika `content_type=document`, `video_url` wajib (format URL YouTube valid) jika `content_type=youtube`, menggunakan rule `required_if` |
| Validasi mata pelajaran | `MaterialRequest`, `AssignmentRequest`, `QuizRequest` memvalidasi `subject_id` yang dipilih **harus ada** di `subject_user` milik guru yang sedang login — mencegah guru membuat konten untuk mata pelajaran yang bukan ampuannya     |
| Deadline & Timer kuis   | Validasi server-side: cek deadline sebelum siswa bisa memulai kuis; cek durasi saat submit (`submitted_at − started_at ≤ duration_minutes`)                                                                                           |

---

## 6. Milestone Pengerjaan

| Fase                           | Deliverable                                                                                                                                              |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1. Foundation                  | Migration seluruh tabel, Model + relasi, Seeder data dummy (termasuk akun guru awal)                                                                     |
| 2. Auth & Middleware           | Install Breeze; register siswa (dropdown kelas), login, forgot password, reset password, ganti password, role middleware, redirect by role, Policy dasar |
| 3. Pusher & Echo Setup         | Install Laravel Echo + Pusher SDK, konfigurasi `.env`, buat channel & event class dasar                                                                  |
| 4. Master — Role               | CRUD Role (nama role)                                                                                                                                    |
| 5. Master — User               | Daftar semua user + assign/ubah role per user                                                                                                            |
| 6. Master — Mata Pelajaran     | CRUD Mata Pelajaran + penetapan guru pengampu (many-to-many)                                                                                             |
| 7. Master — Kelas              | CRUD Kelas                                                                                                                                               |
| 8. Master — Bank Soal          | CRUD Bank Soal (soal + pilihan jawaban)                                                                                                                  |
| 9. Master — Materi             | CRUD Materi (teks, dokumen, YouTube) + urutan tampil + broadcast event saat materi baru                                                                  |
| 10. Master — Tugas             | CRUD Tugas + deadline + broadcast event saat tugas baru                                                                                                  |
| 11. Admin — Kuis               | Buat kuis, tambah soal (baru / dari bank soal), atur poin/deadline/durasi                                                                                |
| 12. Admin — Koreksi Tugas      | Halaman koreksi submission per kelas: lihat jawaban, beri nilai + feedback                                                                               |
| 13. Admin — Laporan            | Laporan materi (progress per siswa), laporan tugas & kuis (nilai per kelas)                                                                              |
| 14. Student — Dashboard        | Dashboard siswa: ringkasan kelas, materi/tugas/kuis yang belum dikerjakan + notifikasi realtime                                                          |
| 15. Student — Materi & Diskusi | List & baca materi, tandai selesai, ruang diskusi realtime + notifikasi komentar ke guru                                                                 |
| 16. Student — Tugas            | Submit jawaban essay (1x), lihat nilai & feedback setelah dikoreksi                                                                                      |
| 17. Student — Kuis             | Kerjakan kuis (1x, timer Vanilla JS, auto-submit), lihat hasil: skor total + review jawaban per soal                                                     |
| 18. Student — Profil & Laporan | Halaman profil/laporan diri: materi selesai, nilai tugas, skor kuis                                                                                      |
| 19. Testing & Security         | PHPUnit: otorisasi (Policy), business logic (submission, deadline, diskusi), **security** (CSRF, XSS output escaping, Mass Assignment, rate limit login, file upload extension) |
| 20. Polish & N+1 Audit         | Audit query via Debugbar, styling dasar                                                                                                                  |

---

## 7. Success Criteria

- [ ] Siswa dapat mendaftar (register) dengan memilih kelas dari dropdown; role otomatis `siswa`
- [ ] Guru/admin hanya bisa ditambahkan via seeder atau tinker — tidak ada form register guru
- [ ] Forgot password mengirim link reset ke email user
- [ ] User dapat membuat password baru lewat link reset
- [ ] User yang login dapat mengganti password dari halaman profil
- [ ] Siswa yang mencoba akses `/admin/*` di-redirect ke dashboard siswa (bukan 403)
- [ ] Dashboard siswa menampilkan ringkasan materi/tugas/kuis yang belum dikerjakan, termasuk materi terakhir dipelajari
- [ ] Menu "Kelas Saya" siswa menampilkan daftar Mata Pelajaran, dan hanya mata pelajaran yang punya konten untuk kelasnya yang tampil
- [ ] Guru tidak bisa membuat materi/tugas/kuis untuk mata pelajaran yang bukan ampuannya (validasi `subject_id` terhadap `subject_user`)
- [ ] Menu Notifikasi menampilkan update realtime (komentar, materi baru, tugas baru) dengan badge unread yang akurat
- [ ] Guru hanya bisa kelola materi/tugas/kuis miliknya sendiri (Policy teruji)
- [ ] Guru bisa menilai & memberi feedback submission tugas dari halaman Koreksi Tugas
- [ ] Siswa tidak bisa submit tugas/kuis lebih dari 1 kali (constraint DB + validasi)
- [ ] Siswa tidak bisa mengerjakan kuis setelah deadline terlewat (validasi server-side)
- [ ] Skor kuis terhitung otomatis berdasarkan poin per soal × jawaban benar
- [ ] Halaman hasil kuis menampilkan review setiap soal: jawaban siswa, jawaban benar, dan status benar/salah
- [ ] Siswa hanya bisa posting diskusi di materi kelas sendiri
- [ ] Halaman profil siswa hanya menampilkan data milik siswa yang sedang login
- [ ] Tidak ada N+1 query di halaman dashboard, list materi, diskusi & laporan
- [ ] Semua relasi FK tidak fillable langsung dari request (aman dari Mass Assignment)
- [ ] Minimal ada test untuk skenario otorisasi utama (siswa akses resource kelas lain → ditolak)
- [ ] **SQL Injection**: tidak ada string interpolasi langsung ke query; raw query hanya dengan binding
- [ ] **XSS**: output komentar diskusi di-escape (`{{ }}`); rich text materi diproses HTMLPurifier sebelum disimpan
- [ ] **CSRF**: semua form memiliki `@csrf`; request tanpa token ditolak 419
- [ ] **File upload**: hanya ekstensi whitelist yang diterima; file disimpan di `storage/private/`, tidak bisa diakses langsung via URL publik
- [ ] **Rate limiting**: login gagal 6x berturut-turut dari IP yang sama → di-block sementara
- [ ] **Security headers**: response mengandung `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`

---

## 8. Development Guidelines

> Seluruh pengerjaan proyek ini **wajib mengacu pada [`rules.md`](./rules.md)**. Berikut penerapan konkretnya dalam konteks LMS ini:

### 8.1 Think Before Coding

- Sebelum implementasi fitur apapun, **nyatakan asumsi secara eksplisit** di komentar kode atau PR description.
- Jika ada ambiguitas (misal: apakah 1 guru bisa mengajar di banyak kelas?), **tanyakan dulu** — jangan menebak dan langsung kode.
- Jika ada interpretasi ganda pada requirement (misal: "laporan" bisa berarti rekap per siswa atau per kelas), **sajikan opsi**nya.
- Contoh pertanyaan wajib sebelum implementasi:
  - "Apakah satu guru bisa membuat materi untuk kelas yang berbeda?"
  - "Apakah kuis yang sudah expired masih ditampilkan di list siswa?"

### 8.2 Simplicity First

Panduan ini **secara langsung mengatur** pilihan teknis di proyek ini:

| Keputusan                     | Penerapan di LMS ini                                                                      |
| ----------------------------- | ----------------------------------------------------------------------------------------- |
| No abstraksi untuk single-use | Tidak ada lapisan `Repository` — gunakan Eloquent langsung di `Service`                   |
| No fitur yang tidak diminta   | Tidak ada notifikasi email untuk komentar diskusi (tidak diminta)                         |
| No konfigurasi berlebihan     | `content_type` di materi cukup 3 nilai tetap (text/document/youtube), bukan sistem plugin |
| Minimal kode                  | Jika sebuah controller bisa < 50 baris, jangan pecah jadi lebih dari 1 class              |

### 8.3 Surgical Changes

Saat mengedit kode yang sudah ada:

- **Jangan refactor** komponen yang tidak terkait dengan task saat ini.
- **Jangan ubah** format/style file yang tidak disentuh.
- **Setiap baris yang diubah harus bisa ditelusuri** ke satu FR atau bug tertentu di PRD ini.
- Jika menemukan dead code / N+1 yang tidak terkait task — **catat di CHANGELOG.md**, jangan langsung hapus/fix.

### 8.4 Goal-Driven Execution

Setiap fase milestone harus didefinisikan sebagai **verifiable goal** sebelum dikerjakan:

```
Fase X: [Nama Fase]
→ Goal    : [Apa yang harus bisa dilakukan setelah fase ini selesai]
→ Verify  : [Cara mengecek — test, curl, atau UI flow]
→ Done when: [Kriteria selesai yang konkret]
```

**Contoh untuk Fase 2 (Auth & Middleware):**

```
→ Goal    : Siswa bisa register, login, dan di-redirect ke /dashboard. Guru login dan di-redirect ke /admin/dashboard.
→ Verify  : php artisan test --filter AuthTest
→ Done when: Semua test hijau; siswa tidak bisa akses /admin/dashboard secara manual
```

- **Sinkronisasi Dokumentasi**: Jika task menambah fitur/scope/kolom DB baru, update FR terkait di `context/PRD.md` (Amandemen) dan/atau `context/SCHEMA.md` di sesi yang sama sebelum task dianggap selesai.


---

## 9. Amandemen

Bagian ini mendokumentasikan evolusi spesifikasi dan penyesuaian fungsionalitas dari rancangan scope awal PRD berdasarkan catatan implementasi aktual pada [`CHANGELOG.md`](./CHANGELOG.md).

| Tanggal / Fase | Perubahan dari Scope Asli | Alasan / Rationale |
| :--- | :--- | :--- |
| **2026-09-14**<br>Fase 40 | **Threaded Discussion Replies**: Penambahan kolom `parent_id` pada tabel `material_discussions` dan antarmuka balasan bersarang (social media thread style). | Meningkatkan interaktivitas tanya jawab materi antara guru dan siswa agar diskusi berjalan kontekstual dan rapi. |
| **2026-09-14**<br>Fase 42 | **Dedicated Student Profile & Strict Portal Security**: Halaman profil mandiri khusus siswa (`student.profile.edit`) dengan tema konsisten tanpa menu admin, serta proteksi ketat pencegahan akses siswa ke halaman login/reset password admin. | Memisahkan konteks visual pengguna, mencegah kebocoran antarmuka admin, dan memperketat segregasi hak akses antar role. |
| **2026-09-14**<br>Fase 43 | **Multi-Format Quiz Support**: Penambahan format soal *Benar / Salah* (`true_false`) dan *Menjodohkan* (`matching`) pada Bank Soal dan Kuis, penambahan kolom `question_type`, `match_text`, dan `answer_data` (JSON). | Mengakomodasi variasi instrumen evaluasi pembelajaran yang lebih beragam melampaui format pilihan ganda standar. |
| **2026-09-14**<br>Fase 44 | **Batch Question Builder & Quick Paste Parser**: Form dinamis multi-butir soal sekaligus, parser dokumen Word (`.docx`), PDF (`.pdf`), teks (`.txt`), dan modal input cepat regex (`Modal ⚡ Input Cepat`). | Mempercepat produktivitas guru dalam menyusun atau memindahkan naskah soal ujian puluhan butir dalam 1 kali proses simpan. |
| **2026-09-14**<br>Fase 45–46 | **Smart Tab Auto-Spawn & UI Refinement**: Tampilan pasangan menjodohkan ringkas, deteksi otomatis perpindahan tab tipe format (auto-spawn kartu baru), dan standardisasi modal konfirmasi Bootstrap 5. | Menyempurnakan alur kerja penyusunan soal campuran (hybrid) tanpa kehilangan draf soal yang sedang diketik. |
| **2026-09-14**<br>Fase 47 | **Monitoring Hasil Siswa & Reset Attempt Kuis**: Halaman monitoring progres kelas per kuis (`/admin/quizzes/{quiz}/students`), modal AJAX review jawaban detail, dan fitur **Reset Pengerjaan Siswa** (`resetStudentAttempt`). | Memberikan visibilitas penuh kepada guru atas ketuntasan belajar siswa dan kontrol reset pengerjaan jika terjadi kendala teknis / kebutuhan remedial. |
| **2026-09-18**<br>Fase Master Bank | **Master Bank Materi & Bank Tugas**: Penambahan tabel `material_banks` dan `assignment_banks` beserta modul CRUD Bank Materi dan Bank Tugas terpusat. | Memungkinkan guru membuat bank materi dan bank tugas yang dapat digunakan kembali (*reusable*) lintas kelas tanpa input ulang. |
| **2026-09-23**<br>Fase 48 | **Fast Chunked File Upload (Bank Materi & Bank Soal)**: Implementasi pemecahan file besar di sisi client (2.5MB per chunk) via asynchronous endpoints (`/admin/upload/chunk`), real-time UI progress bar, penolakan format arsip/raw text (ZIP, RAR, TXT), dan pencegahan form submit crash. | Mengatasi kendala HTTP timeout (504/max_execution_time) saat guru mengunggah dokumen/modul besar (15MB–50MB+) tanpa perlu menambah durasi timeout server global. |