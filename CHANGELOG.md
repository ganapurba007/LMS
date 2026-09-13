# CHANGELOG — RuangTerra

> Catat setiap perubahan kode di sini selama implementasi.

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

## [Fase 30] Redesign & Visual Enhancement of Student Courses, Toolbar Spacing & Material Detail Page — 2026-09-12

### Ditambahkan & Diperbarui
- **Perombakan Halaman Courses / Materi Siswa ([`resources/views/student/materials/index.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/materials/index.blade.php))**:
  - **Page Hero Header**: Desain banner modern dengan gradien khas RuangTerra (`#20456E` - `#3368A0`), breadcrumb navigasi, badge kelas siswa, judul modul, serta search bar terintegrasi.
  - **Widget Ringkasan Progres Materi**: Menampilkan bar progres pencapaian materi (`progressPercent%`), counter total modul, modul selesai, dan modul yang belum ditinjau secara real-time.
  - **Toolbar Filter Interaktif & Pengalih Tampilan**:
    - Memperbaiki padding dan margin kartu toolbar (`padding: 1.25rem 1.5rem`) agar rapi dan tidak mepet ke tepi.
    - Filter pills mata pelajaran dengan counter dinamis (*Semua Mapel*, *Geografi*, dll.).
    - Filter status penyelesaian (*Semua Status*, *Selesai*, *Belum Ditinjau*).
    - Tombol *View Mode Switcher* untuk beralih secara instan antara **Tampilan Kartu (Grid View)** dan **Tampilan Daftar (List View / Modern Table)** dengan penyimpanan preferensi di `localStorage`.
    - Live client-side instant search saat siswa mengetikkan kata kunci.
- **Perombakan Halaman Detail Materi ([`resources/views/student/materials/show.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/student/materials/show.blade.php))**:
  - Mengganti header lama dengan Page Hero Header bergradien RuangTerra, badge mapel & kelas, tombol kembali, serta pill status penyelesaian.
  - Layout 2 kolom terstruktur:
    - **Kolom Utama**: Pemutar Video Pembelajaran responsif 16:9, artikel bacaan modul WYSIWYG dengan tipografi nyaman dibaca, kartu berkas unduhan dokumen PDF, serta Ruang Diskusi interaktif real-time dengan pesan bergelembung (*chat bubbles*) dan pembeda peran Guru/Siswa.
    - **Sidebar Kanan**: Kartu status & checkpoint progres penyelesaian modul, tombol toggle tandai selesai, kartu profil guru pengampu (avatar, NIP, email), dan pintasan navigasi cepat ke tugas serta kuis kelas.
- **Peningkatan Controller ([`app/Http/Controllers/Student/MaterialController.php`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Student/MaterialController.php))**:
  - Menambahkan dukungan filter pencarian judul, guru, dan mata pelajaran, filter subject ID, status penyelesaian, serta kalkulasi persentase progres belajar.

### Diuji & Diverifikasi
- Seluruh 124 pengujian otomatis PHPUnit lulus 100% (413 assertions).
- Pengujian interaktif browser: Grid View, List View toggle, filter toolbar spacing, dan halaman detail materi teruji sempurna.

## [Fase 29] Authentication UI Enhancements (Eye Toggle Icon & Submit Auto-Disable) — 2026-09-12

### Ditambahkan & Diperbarui
- **Icon Mata (Show/Hide Password & Konfirmasi)**:
  - Ditambahkan tombol toggle mata interaktif (`ti-eye` / `ti-eye-off`) pada seluruh input password dan konfirmasi password di:
    - Login Siswa & Guru (`resources/views/auth/login.blade.php`)
    - Pendaftaran Siswa Baru (`resources/views/auth/register.blade.php`)
    - Login Backend Guru / Admin (`resources/views/admin/auth/login.blade.php`)
    - Lupa Kata Sandi & Reset Password (`resources/views/auth/reset-password.blade.php`, `resources/views/admin/auth/forgot-password.blade.php`, `resources/views/auth/confirm-password.blade.php`)
- **Efek Hover & State Auto-Disable Tombol Submit**:
  - Ditambahkan styling hover terangkat (`translateY(-2px)`) dan efek glow pada `.btn-auth-submit`.
  - Mengimplementasikan handler JavaScript submit otomatis: tombol menjadi `disabled` seketika saat form dikirim (setelah validasi HTML5 lolos) dengan indikator animasi spinner dan teks loading dinamis untuk mencegah pengiriman formulir ganda (*double-submission*).

### Diuji & Diverifikasi
- Seluruh 124 pengujian PHPUnit lulus 100% (413 assertions).
- Diverifikasi langsung melalui subagent browser: toggle mata berfungsi mulus, form input teks dan tombol disable beroperasi sesuai spesifikasi.

## [Fase 28] Dedicated Backend & Frontend Authentication Architecture — 2026-09-12

### Ditambahkan & Diperbarui
- **Autentikasi Khusus Backend (`admin/login` & `admin/forgot-password`)**:
  - Dibuat [`AdminAuthenticatedSessionController`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/Auth/AdminAuthenticatedSessionController.php) dan [`AdminPasswordResetLinkController`](file:///c:/laragon/www/KELAS/lms_dani/app/Http/Controllers/Admin/Auth/AdminPasswordResetLinkController.php).
  - Tampilan login backend [`resources/views/admin/auth/login.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/auth/login.blade.php) bergaya Tabler admin theme:
    - Judul: **Masuk Panel Guru**
    - **Tidak ada tautan/tombol registrasi** sama sekali.
    - **Siswa diblokir dari login backend**: jika siswa mencoba masuk, autentikasi ditolak dengan pesan error *"Akses ditolak. Halaman login ini hanya untuk Guru dan Administrator. Siswa silakan login melalui portal utama."*.
    - Hanya akun dengan role `guru` yang dapat masuk, langsung diarahkan ke `/admin/dashboard`.
  - Halaman lupa kata sandi backend [`resources/views/admin/auth/forgot-password.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/admin/auth/forgot-password.blade.php) dengan proteksi hanya untuk email Guru.
- **Autentikasi Frontend (`login`)**:
  - Halaman [`resources/views/auth/login.blade.php`](file:///c:/laragon/www/KELAS/lms_dani/resources/views/auth/login.blade.php) tetap melayani login Guru & Siswa.
  - Tautan registrasi *"Daftar Siswa Baru"* (`route('register')`) dan lupa kata sandi (`route('password.request')`) tetap aktif dan berfungsi normal.

### Diuji & Diverifikasi
- Dibuat unit test suite [`tests/Feature/Admin/AdminAuthTest.php`](file:///c:/laragon/www/KELAS/lms_dani/tests/Feature/Admin/AdminAuthTest.php).
- Seluruh 124 PHPUnit tests passed (413 assertions, 100% PASS).

## [Fase 27] Enable Frontend Registration & Application Rebrand to RuangTerra — 2026-09-12

### Ditambahkan & Diperbarui
- **Akses Halaman Register di Front End (`routes/auth.php`)**:
  - Mengubah route `GET /register` dari redirect ke login menjadi memanggil `RegisteredUserController::create` yang merender form pendaftaran siswa beserta pilihan kelas.
  - Pengguna di halaman login kini dapat mengklik tautan **Daftar Siswa Baru** dan langsung masuk ke form registrasi tanpa di-redirect.
- **Rebrand Aplikasi Menjadi RuangTerra**:
  - Memperbarui nama aplikasi menjadi `RuangTerra` pada konfigurasi `.env`, `config/app.php`, manifest PWA, layout navigasi, header/footer, dan halaman autentikasi.

### Diuji & Diverifikasi
- Seluruh 114 PHPUnit tests passed (100% PASS).
- Verifikasi langsung via browser: tautan "Daftar Siswa Baru" pada halaman login sukses membuka form registrasi dengan seluruh field (Nama, Email, Pilih Kelas, Password, Konfirmasi Password).

## [Fase 26] Direct Hero Section Attachment & Modern Glassmorphism Statistics Cards Overhaul — 2026-09-11

### Ditambahkan & Diperbarui
- **Hero Section Menempel Direct ke Header (`layouts/app.blade.php` & `dashboard.blade.php`)**:
  - Menghapus header box terpisah (`<x-slot name="header">`) dari `dashboard.blade.php` dan menyesuaikan `@if(isset($header) && trim($header) !== '')` pada layout agar tidak merender elemen header kosong.
  - Hero section Arsha kini menempel langsung secara seamless di bawah top navbar tanpa celah (gap 0px).
  - Teks assertion pengujian `Dashboard Siswa` dan nama kelas dipindahkan ke dalam Hero Welcome Badge Pill pada area banner Hero.
- **Penyempurnaan UI/UX (Clean, Elegan & Tidak Ramai) (`dashboard.blade.php` & `theme-custom.css`)**:
  - Mengeliminasi ornamen SVG stroke yang menumpuk dan menggantinya dengan pencahayaan gradien radial halus (`.bg-abstract-catalog`, `.bg-abstract-quizzes`, `.bg-abstract-assignments`).
  - Mempertahankan motif latar abstrak yang lembut, merapikan hirarki visual teks, menyelaraskan kartu materi, kuis, dan tugas agar tampilan lebih bersih (*clean UI*), tidak ramai, namun tetap menarik (*engaging UX*).

### Diuji & Diverifikasi
- Seluruh 111 PHPUnit feature tests passed (100% PASS).

## [Fase 25] Replacement of Plain White Backgrounds with Warm Light Theme (#F2EFE7) & Footer Social Media Icon Styling — 2026-09-11

### Ditambahkan & Diperbarui
- **Penggantian Warna Latar Belakang Putih dengan `#F2EFE7`**:
  - `public/css/theme-custom.css`: Mengubah `.arsha-header`, `.arsha-icon-box`, `.arsha-course-card`, dan `.mobile-bottom-nav` dari latar polos putih menjadi warna warm light `#F2EFE7`.
  - `resources/views/layouts/navigation.blade.php`: Mengganti `bg-white` pada header navbar container, dropdown user button, dan mobile menu container dengan `style="background-color: #F2EFE7;"`.
  - `resources/views/layouts/app.blade.php`: Mengganti `bg-white` pada header halaman dan Pre-footer CTA card dengan `style="background-color: #F2EFE7;"`.
  - `resources/views/dashboard.blade.php`: Mengganti seluruh kartu komponen, search box, floating glassmorphism badges, filter mapel pills, dan kontainer kuis/tugas dari `bg-white` & `bg-light` menjadi `#F2EFE7`.
- **Rapikan & Beautify Ikon Media Sosial Footer (`layouts/app.blade.php` & `theme-custom.css`)**:
  - Dibuatkan styling tombol bundar khusus `.social-icon-btn` untuk Instagram, YouTube, Discord, dan WhatsApp dengan animasi hover lifting, glow shadows, dan gradien warna resmi tiap platform.

### Diuji & Diverifikasi
- Seluruh 111 PHPUnit feature tests passed (100% PASS).

## [Fase 24] Dedicated Laptop Header Display, Micro-Animations & Arsha Footer Overhaul — 2026-09-11

### Ditambahkan & Diperbarui
- **Dedicated Desktop Navigation Rule (`public/css/theme-custom.css` & `layouts/navigation.blade.php`)**:
  - Menambahkan aturan CSS `.arsha-nav-desktop` (`display: flex !important`) untuk menjamin 100% menu navigasi utama (`Dashboard`, `Courses / Materi`, `Tugas Kelas`, `Kuis Online`, `Laporan Diri`) tampil terbuka di layar laptop/desktop tanpa memunculkan hamburger icon.
  - Membatasi hamburger icon hanya untuk tampilan layar handphone (<768px) melalui `.arsha-hamburger-btn`.
- **Tambahan Micro-Animations (`public/css/theme-custom.css`)**:
  - `@keyframes pulse-glow` pada lonceng notifikasi dan elemen aktif.
  - `@keyframes shine-sweep` untuk efek kilau pada kartu materi.
  - Aturan `.hover-lift` (`translateY(-8px) scale(1.01)`) pada elemen interaktif.
- **Redesain Pre-Footer CTA & Arsha Premium Footer (`layouts/app.blade.php`)**:
  - **Seksi Pre-Footer CTA**: Banner gradien navy (`#1e3a8a` ke `#3368A0`) *"Siap Meraih Prestasi Terbaik & Nilai Impianmu?"* dengan kartu quick-start belajar, 3 badge fasilitas, dan background particle blur.
  - **Arsha Premium 4-Column Footer**: Footer gelap (`#0b1727`) dengan border atas `#66A3BF`, logo `ARSHA LMS SMA`, ikon media sosial bernavigasi animasi (Instagram, YouTube, Discord, WhatsApp), rumpun bidang studi, kontak bantuan 24/7, dan tombol Kembali ke Atas.

### Diuji & Diverifikasi
- Seluruh 111 PHPUnit feature tests passed (100% PASS).

## [Fase 23] Enhanced Rich Media & Laptop Header Optimization — 2026-09-11

### Ditambahkan & Diperbarui
- **Aset Gambar Pembelajaran High-Res (`public/images/`)**:
  - `public/images/hero-sma.jpg`: Gambar definisi tinggi siswa SMA belajar kelompok secara interaktif dengan laptop & buku di ruang kelas modern.
  - `public/images/quiz-achievement.jpg`: Gambar pencapaian kuis online siswa SMA dengan piala & badge prestasi.
- **Navigasi Header Terbuka untuk Laptop/Desktop (`resources/views/layouts/navigation.blade.php`)**:
  - Memastikan seluruh menu utama (`Dashboard`, `Courses / Materi`, `Tugas Kelas`, `Kuis Online`, `Laporan Diri`) tampil langsung secara penuh tanpa menguncup ke hamburger icon di layar laptop (`hidden md:flex lg:flex`).
  - Menambahkan indikator lonceng notifikasi aktif & badge `SMA Edition`.
- **Pengayaan Antarmuka Dashboard Siswa (`resources/views/dashboard.blade.php`)**:
  - Integration Gambar Hero HD dengan floating badges glassmorphism.
  - Baris Filter Pills Mata Pelajaran Populer (Matematika, IPA/Fisika, Biologi, Bahasa Inggris, Informatika).
  - Banner Showcase Kuis Interaktif dengan gambar pencapaian kuis.
  - Tampilan kartu materi & tugas yang lebih padat, kaya visual, tapi tetap clean & user-friendly.

### Diuji & Diverifikasi
- Automated PHPUnit Test suite (111 tests passed, 372 assertions).

## [Fase 22] Arsha Theme Overhaul (Youth & High School / SMA Design) — 2026-09-11

### Ditambahkan
- **Integrasi Arsha Bootstrap Template & Visual Interaktif SMA**:
  - `public/css/theme-custom.css`: Menambahkan styling khusus Arsha Hero Area (`.arsha-hero`), Arsha Header (`.arsha-header`, `.arsha-sitename`), Icon Service Boxes (`.arsha-icon-box`), Course Portfolio Grid (`.arsha-course-card`), dan Counter Band (`.arsha-counter-section`).
  - Animasi melayang keyframes `@keyframes img-float` (`.animated-float`) untuk ilustrasi vektor SVG interaktif di area Hero.
  - Integrasi pustaka **AOS (Animate On Scroll)** via CDN di `resources/views/layouts/app.blade.php` dengan durasi 800ms (`AOS.init({ duration: 800, once: true })`).
- **Pembaruan Visual Frontend & Header**:
  - Navigation Header (`resources/views/layouts/navigation.blade.php`) menggunakan brand logo animasi `ARSHA LMS` dengan font Jost & Poppins.
  - Dashboard Siswa (`resources/views/dashboard.blade.php`) diperbarui total dengan tampilan Arsha Bootstrap Template:
    - Hero Area interaktif dengan pesan motivasi belajar SMA & ilustrasi vektor SVG melayang (`animated-float`).
    - 4 Kartu Fitur & Services (Modul Interaktif, Tugas Essay, Kuis Realtime, Laporan Diri) dengan `data-aos="fade-up"`.
    - Katalog Courses / Materi Pelajaran dalam format grid `data-aos="zoom-in"`.
    - Band statistik counter animasi `data-aos="fade-up"`.
    - Timeline Kuis Online Aktif dan Tugas Perlu Dikumpulkan.

### Diuji & Diverifikasi
- Seluruh unit & feature test suite (111 passed, 372 assertions) 100% lulus tanpa hambatan.

## [Fase 21] Front-End Overhaul, Visual UI Improvements & PRD v1.9 Backend Alignment — 2026-09-11

### Ditambahkan
- **PWA Mobile-First Foundation (Frontend)**:
  - File `public/manifest.json` (Web App Manifest dengan `theme_color: #66A3BF`).
  - File `public/sw.js` (PWA Service Worker dengan strategi network-first untuk halaman dinamis dan cache-first untuk aset statis).
  - Tampilan Mobile Bottom Navigation Bar khusus untuk antarmuka siswa di perangkat ponsel (`layouts/app.blade.php`).
- **Skema Warna Frontend Baru (`PRD v1.9 §2.1 & §3.9`)**:
  - File CSS `public/css/theme-custom.css` berisi variabel CSS warna utama (`#66A3BF`), aksen dark (`#3368A0`), soft mint (`#C8DFDB`), dan warm background (`#F2EFE7`).
- **Audit & Penyesuaian Dashboard Admin Guru (`PRD v1.9 §3.2b`)**:
  - `Admin\DashboardController` (`app/Http/Controllers/Admin/DashboardController.php`) untuk menghitung 4 statistik ringkasan dinamis: Total Kelas, Total Siswa, Tugas Belum Dikoreksi, dan Kuis Aktif.
  - Penayangan 4 kartu statistik di `resources/views/admin/dashboard.blade.php` lengkap dengan link navigasi ke modul master terkait.
- **Skema Notifikasi Database (`PRD v1.9 §4`)**:
  - File migrasi `database/migrations/2026_09_11_000018_create_notifications_table.php` (`user_id`, `type`, `title`, `message`, `related_url`, `is_read`, `read_at`).
  - Model `app/Models/Notification.php` dan penambahan relasi `notifications()` di Model `User`.

### Diubah & Diperbaiki
- **Isolasi Tema Backend vs Frontend**:
  - Mengembalikan tampilan **Dashboard Admin Guru** 100% menggunakan tema asli dari `public/template/be/index.html` dengan mencabut `theme-custom.css` dari `layouts/be/header.blade.php`.
  - Mengisolasi palet warna baru (`#66A3BF`, `#3368A0`, `#C8DFDB`, `#F2EFE7`) khusus pada **Frontend Siswa & Public** (`layouts/app.blade.php`).
- **Perbaikan Query SQL pada Admin Dashboard**:
  - Mengoreksi nama kolom pada `DashboardController`: `score` menjadi `grade` pada tabel `assignment_submissions`.
  - Mengoreksi nama kolom pada `DashboardController`: `due_date` menjadi `deadline` pada tabel `quizzes`.
- **Manajemen Alert & Persistence (`localStorage`)**:
  - Semua alert flash notifikasi otomatis tertutup (4 detik untuk info/success, 7 detik untuk warning/danger).
  - Tombol close (`x`) alert menyimpan state penutupan di `localStorage` per ID alert agar tidak muncul kembali saat halaman di-refresh.
- **Auto-Init Select2 Dropdown**:
  - Inisialisasi otomatis Select2 dengan Bootstrap 5 theme (`window.initSelect2()`) untuk seluruh `<select class="select2">` tanpa perlu membuat skrip berulang di setiap view Blade.
- **Kontras Tombol Mode Terang (Light Mode)**:
  - Menambahkan border yang jelas pada `.btn-light` dan `.btn-white` di `theme-custom.css` agar tetap terlihat bersih dan kontras baik di mode light maupun dark.
- **Input Materi Pembelajaran Simultaneous**:
  - Halaman `admin.materials.create` dan `edit` diperbarui agar mendukung pengisian **Artikel Teks (TinyMCE 6)**, **Link YouTube Embed**, dan **Upload File Dokumen** secara bersamaan tanpa perlu memilih tipe melalui dropdown.
- **Autentikasi & Guard Redirection**:
  - `Route::fallback()` dan rute akar `/` otomatis mengarah ke `/dashboard`.
  - Permintaan ke rute `/register` otomatis diarahkan ke `/login`.
  - Proses Logout langsung mengarahkan pengguna kembali ke `/login`.

## [Fase 20] Polish & Audit Final — 2026-09-11

### Ditambahkan
- Final audit Eager Loading (`with()`) pada seluruh Controller untuk mencegah N+1 Query.
- Verifikasi konsistensi UI Tabler Blade & Bootstrap 5 pada antarmuka admin, guru, dan siswa.
- Full automated test suite (111 tests, 370 assertions) 100% PASS.

## [Fase 19] Testing & Security Audit — 2026-09-11

### Ditambahkan
- Feature Test `tests/Feature/SecurityAuditTest.php` untuk memverifikasi:
  - Resilience terhadap serangan SQL Injection pada parameter pencarian/filter query (`' OR '1'='1' --`).
  - Pembersihan/Escaping XSS (`e()` dan `{!! nl2br(e(...)) !!}`) pada render diskusi dan materi.
  - Otorisasi Role Middleware (`EnsureUserHasRole` & `role:guru`) yang mengisolasi rute admin dari siswa.
  - Proteksi otentikasi Guest pada seluruh rute terlindungi (`/dashboard`, `/student/*`, `/admin/*`).

## [Fase 18] Student Profil & Laporan Diri — 2026-09-11

### Ditambahkan
- `Student\ReportController` (`app/Http/Controllers/Student/ReportController.php`) untuk menghitung dan menyajikan rekapitulasi progres materi (`material_progress`), rata-rata nilai tugas, rata-rata skor kuis, dan performa evaluasi gabungan siswa.
- Blade View `resources/views/student/report/index.blade.php` dengan ringkasan kartu metrik dan tabel riwayat nilai tugas & skor kuis siswa.
- Feature Test `tests/Feature/Student/StudentReportTest.php` untuk memverifikasi penayangan laporan progres belajar diri siswa.

### Diubah
- `routes/web.php`: Menambahkan rute `student.report.index`.

## [Fase 17] Student Kuis (Timer Vanilla JS + Preview/Review Jawaban) — 2026-09-11

### Ditambahkan
- `Student\QuizController` (`app/Http/Controllers/Student/QuizController.php`) untuk pengelolaan pengerjaan kuis online siswa (`index`, `show`, `start`, `attempt`, `submit`, `result`).
- Interactive Vanilla JS Countdown Timer yang menghitung mundur sisa waktu secara otomatis dan melakukan auto-submit jika durasi kuis habis.
- Perhitungan skor kuis berbasis persentase jawaban benar dan pencatatan riwayat `QuizAttempt` & `QuizAnswer`.
- Penayangan Preview & Review Jawaban (`resources/views/student/quizzes/result.blade.php`) sesuai User Request: menampilkan skor, daftar seluruh soal, jawaban yang dipilih siswa ("Jawaban Anda"), dan jawaban yang benar ("Jawaban Benar").
- Blade Views UI di `resources/views/student/quizzes/`: `index.blade.php`, `show.blade.php`, `attempt.blade.php`, dan `result.blade.php`.
- Feature Test `tests/Feature/Student/StudentQuizTest.php` untuk memverifikasi pengerjaan kuis, pengolesan skor otomatis, preview review jawaban, dan proteksi otorisasi antar-kelas.

### Diubah
- `routes/web.php`: Menambahkan rute `student.quizzes.index`, `show`, `start`, `attempt`, `submit`, dan `result`.

## [Fase 16] Student Tugas — 2026-09-11

### Ditambahkan
- `Student\AssignmentController` (`app/Http/Controllers/Student/AssignmentController.php`) untuk menyajikan daftar tugas kelas siswa (`index`), petunjuk pengerjaan (`show`), serta pengumpulan/pembaharuan jawaban tugas (`submit`).
- Penayangan status pengumpulan (Belum Dikumpulkan / Sudah Dikumpulkan / Sudah Dinilai Guru), nilai (`grade`), dan umpan balik (`feedback`).
- Blade Views Tabler UI di `resources/views/student/assignments/`: `index.blade.php` dan `show.blade.php`.
- Feature Test `tests/Feature/Student/StudentAssignmentTest.php` untuk memverifikasi isolasi tugas kelas siswa, pengumpulan jawaban tugas, dan proteksi otorisasi antar-kelas.

### Diubah
- `routes/web.php`: Menambahkan rute `student.assignments.index`, `show`, dan `submit`.

## [Fase 15] Student Materi & Diskusi Realtime — 2026-09-11

### Ditambahkan
- `Student\MaterialController` (`app/Http/Controllers/Student/MaterialController.php`) untuk pengelolaan penayangan materi kelas siswa, penandaan progres selesai (`toggleComplete`), dan penambahan komentar di ruang diskusi (`storeComment`).
- Dispatching Event Realtime `DiscussionCommentSent` (`event(new DiscussionCommentSent($discussion))`) pada channel privat `material.{material_id}` saat komentar dikirim.
- Blade Views Tabler UI di `resources/views/student/materials/`: `index.blade.php` (daftar materi per kelas) dan `show.blade.php` (penayang konten teks/dokumen/video & ruang diskusi).
- Feature Test `tests/Feature/Student/StudentMaterialTest.php` untuk memverifikasi isolasi materi per kelas siswa, toggle penandaan selesai (`material_progress`), pengiriman komentar diskusi realtime, dan proteksi otorisasi antar-kelas.

### Diubah
- `routes/web.php`: Menambahkan rute `student.materials.index`, `show`, `complete`, dan `discussions`.

## [Fase 14] Student Dashboard — 2026-09-11

### Ditambahkan
- `Student\DashboardController` (`app/Http/Controllers/Student/DashboardController.php`) untuk menyajikan metrik portal siswa: nama kelas siswa, hitungan & persentase progres penyelesaian materi, daftar tugas mendatang (deadline >= now()), kuis evaluasi aktif, dan materi pembelajaran terbaru.
- Blade View Tabler UI `resources/views/dashboard.blade.php` dengan visual ringkasan siswa yang responsif & modern.
- Feature Test `tests/Feature/Student/StudentDashboardTest.php` untuk memverifikasi penayangan dashboard siswa dan pengalihan guest ke halaman login.

### Diubah
- `routes/web.php`: Mengarahkan rute `/dashboard` ke `Student\DashboardController::class`.

## [Fase 13] Admin Laporan & Rekapitulasi Analytics — 2026-09-11

### Ditambahkan
- `ReportController` (`app/Http/Controllers/Admin/ReportController.php`) untuk menyajikan rekapitulasi progres materi (`material_progress`), rata-rata nilai tugas (`assignment_submissions`), dan rata-rata skor kuis (`quiz_attempts`) per siswa.
- Fitur Filter per Kelas dan Mata Pelajaran serta Unduh Berkas Laporan CSV (`exportCsv`).
- Blade View Tabler UI `resources/views/admin/reports/index.blade.php` dengan progress bar visual penyelesaian materi.
- Feature Test `tests/Feature/Admin/ReportTest.php` untuk memverifikasi halaman laporan analytics, ekspor CSV, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute `reports.index` dan `reports.export-csv` di dalam grup `/admin`.

## [Fase 12] Admin Koreksi Tugas — 2026-09-11

### Ditambahkan
- `AssignmentSubmissionController` (`app/Http/Controllers/Admin/AssignmentSubmissionController.php`) untuk pengelolaan daftar pengumpulan tugas siswa, penayangan jawaban siswa, serta pemberian nilai (0-100) dan umpan balik/catatan guru (`grade`).
- Otorisasi mata pelajaran: Guru hanya dapat menilai pengumpulan tugas pada mata pelajaran yang diampunya (`subject_user`).
- Blade Views Tabler UI di `resources/views/admin/submissions/`: `index.blade.php` (dengan filter dropdown berdasarkan tugas) dan `show.blade.php` (form penilaian & feedback).
- Feature Test `tests/Feature/Admin/AssignmentSubmissionGradeTest.php` untuk memverifikasi penayangan daftar submission, proses penilaian & feedback guru, validasi otorisasi mata pelajaran, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute `submissions.index`, `submissions.show`, dan `submissions.grade` di dalam grup `/admin`.

## [Fase 11] Master — Kuis CRUD & Question Management — 2026-09-11

### Ditambahkan
- `QuizController` (`app/Http/Controllers/Admin/QuizController.php`) untuk pengelolaan CRUD Kuis Evaluasi (Judul, Durasi Pengerjaan, Poin per Soal, Batas Waktu/Deadline, Mata Pelajaran, dan Kelas Target).
- Fitur Pengelolaan Soal Kuis:
  - Impor batch dari Bank Soal (`importQuestions`) beserta opsi & kunci jawaban.
  - Penambahan soal manual khusus kuis (`storeQuestion`) dan opsi dinamis.
  - Penghapusan soal kuis (`destroyQuestion`).
- Proteksi otorisasi mata pelajaran: Guru hanya dapat membuat/mengubah kuis pada mata pelajaran yang diampunya (`subject_user`).
- Blade Views Tabler UI di `resources/views/admin/quizzes/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`.
- Feature Test `tests/Feature/Admin/QuizCrudTest.php` untuk memverifikasi pembuatan kuis, impor soal dari Bank Soal, penambahan/penghapusan soal manual, validasi otorisasi mata pelajaran, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `quizzes` serta rute penanganan `import-questions`, `questions`, dan `destroy-question` di dalam grup `/admin`.

## [Fase 10] Master — Tugas CRUD + Broadcast Event — 2026-09-11

### Ditambahkan
- `AssignmentController` (`app/Http/Controllers/Admin/AssignmentController.php`) untuk pengelolaan CRUD Tugas Siswa (Judul, Deskripsi/Petunjuk, Batas Waktu/Deadline, Mata Pelajaran, dan Kelas Target).
- Proteksi otorisasi mata pelajaran: Guru hanya dapat menambahkan/mengubah tugas pada mata pelajaran yang diampunya (`subject_user`).
- Dispatching Event Realtime `AssignmentCreated` (`event(new AssignmentCreated($assignment))`) pada channel `class.{class_id}` saat tugas baru dibuat.
- Blade Views Tabler UI di `resources/views/admin/assignments/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/AssignmentCrudTest.php` untuk memverifikasi pembuatan tugas, validasi otorisasi mata pelajaran guru, penyiaran event realtime `assignment.created`, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `assignments` di dalam grup `/admin`.

## [Fase 9] Master — Materi CRUD + Broadcast Event — 2026-09-11

### Ditambahkan
- `MaterialController` (`app/Http/Controllers/Admin/MaterialController.php`) untuk pengelolaan CRUD Materi Pembelajaran (Teks/HTML, Dokumen Upload, dan Video YouTube).
- Proteksi otorisasi mata pelajaran: Guru hanya dapat menambahkan/mengubah materi pada mata pelajaran yang diampunya (`subject_user`).
- Dispatching Event Realtime `MaterialCreated` (`event(new MaterialCreated($material))`) pada channel `class.{class_id}` saat materi baru dibuat.
- Blade Views Tabler UI di `resources/views/admin/materials/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/MaterialCrudTest.php` untuk memverifikasi pembuatan materi per tipe, pengunggahan berkas dokumen, validasi otorisasi mata pelajaran guru, penyiaran event realtime, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `materials` di dalam grup `/admin`.

## [Fase 8] Master — Bank Soal CRUD — 2026-09-11

### Ditambahkan
- `QuestionBankController` (`app/Http/Controllers/Admin/QuestionBankController.php`) untuk pengelolaan CRUD Bank Soal pilihan ganda beserta opsi jawaban dinamis dan penandaan jawaban benar.
- Blade Views Tabler UI di `resources/views/admin/question-banks/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/QuestionBankCrudTest.php` untuk memverifikasi pembuatan soal dengan opsi & jawaban benar, pengeditan, penghapusan cascade opsi, dan proteksi role siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `question-banks` di dalam grup `/admin`.

## [Fase 7] Master — Kelas CRUD — 2026-09-11

### Ditambahkan
- `SchoolClassController` (`app/Http/Controllers/Admin/SchoolClassController.php`) untuk pengelolaan CRUD Kelas sekolah.
- Blade Views Tabler UI di `resources/views/admin/classes/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/SchoolClassCrudTest.php` untuk memverifikasi daftar kelas, penambahan & pengeditan kelas, validasi keunikan nama kelas, penghapusan, dan pembatasan akses siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `classes` di dalam grup `/admin`.

## [Fase 6] Master — Mata Pelajaran CRUD + Guru Pengampu — 2026-09-11

### Ditambahkan
- `SubjectController` (`app/Http/Controllers/Admin/SubjectController.php`) untuk pengelolaan CRUD Mata Pelajaran dan penetapan guru pengampu (many-to-many via pivot `subject_user`).
- Blade Views Tabler UI di `resources/views/admin/subjects/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/SubjectCrudTest.php` untuk memverifikasi daftar mata pelajaran, pembuatan & pengeditan mata pelajaran, sinkronisasi pivot `subject_user`, dan pembatasan akses siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `subjects` di dalam grup `/admin`.

## [Fase 5] Master — User (Daftar & Assign Role) — 2026-09-11

### Ditambahkan
- `UserController` (`app/Http/Controllers/Admin/UserController.php`) untuk melihat daftar seluruh pengguna, pencarian kata kunci (Nama, Email, NIP), filter berdasarkan role, serta pengalokasian role, kelas, dan NIP.
- Blade Views Tabler UI di `resources/views/admin/users/`: `index.blade.php` dan `edit.blade.php`.
- Feature Test `tests/Feature/Admin/UserCrudTest.php` untuk memverifikasi pencarian, filter role, pengalokasian role/kelas, validasi NIP unik, dan pembatasan akses siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `users` (`index`, `edit`, `update`) di dalam grup `/admin`.

## [Fase 4] Master — Role CRUD — 2026-09-11

### Ditambahkan
- `RoleController` (`app/Http/Controllers/Admin/RoleController.php`) dengan fungsi CRUD lengkap untuk pengelolaan role pengguna.
- Blade Views Tabler UI di `resources/views/admin/roles/`: `index.blade.php`, `create.blade.php`, `edit.blade.php`.
- Feature Test `tests/Feature/Admin/RoleCrudTest.php` untuk menguji daftar role, penambahan role unik, pengeditan, proteksi role sistem (`guru`/`siswa`), dan pembatasan akses siswa.

### Diubah
- `routes/web.php`: Menambahkan rute resource `roles` di dalam grup `/admin`.

## [Fase 3] Pusher & Echo Setup — 2026-09-11

### Ditambahkan
- Paket `pusher/pusher-php-server` (^7.3) dan konfigurasi `config/broadcasting.php`.
- Definisi channel otoritas privat di `routes/channels.php`: `class.{classId}` (materi & tugas) dan `material.{materialId}` (diskusi).
- Event Classes realtime yang mengimplementasikan `ShouldBroadcast`:
  - `DiscussionCommentSent` (`app/Events/DiscussionCommentSent.php`) pada channel `material.{id}`.
  - `MaterialCreated` (`app/Events/MaterialCreated.php`) pada channel `class.{id}`.
  - `AssignmentCreated` (`app/Events/AssignmentCreated.php`) pada channel `class.{id}`.
- Feature Test `tests/Feature/PusherBroadcastTest.php` untuk memverifikasi pemanggilan event broadcasting realtime.

## [Fase 2] Auth & Middleware — 2026-09-11

### Ditambahkan
- Middleware `EnsureUserHasRole` (`app/Http/Middleware/EnsureUserHasRole.php`) yang meregistrasi alias `'role'` di `bootstrap/app.php`. Meredirect siswa yang mencoba akses `/admin/*` ke `/dashboard` (bukan 403) dan guest ke `/login`.
- View Dashboard Admin/Guru (`resources/views/admin/dashboard.blade.php`).
- Feature Test `tests/Feature/AuthMiddlewareTest.php` untuk menguji pendaftaran siswa dengan kelas, kegagalan tanpa kelas, redirect login per role, proteksi middleware `/admin/*`, dan logout.

### Diubah
- `RegisteredUserController.php`: Mengirim `$classes` ke view register, memvalidasi `class_id`, dan otomatis meng-assign role `siswa`.
- `resources/views/auth/register.blade.php`: Menambahkan dropdown pilihan kelas (`<select name="class_id">`).
- `AuthenticatedSessionController.php`: Redirect Guru ke `/admin/dashboard` dan Siswa ke `/dashboard`.
- `routes/web.php`: Menambahkan route `/dashboard` dan group `/admin/dashboard` dengan middleware `['auth', 'role:guru']`.
- `resources/views/layouts/navigation.blade.php`: Menyesuaikan link navigasi dan dropdown user (Profil Saya, Logout) berdasarkan role.

## [Fase 1] Foundation — 2026-09-11

### Ditambahkan
- Migration untuk 17 tabel & pivot: `roles`, `classes`, `subjects`, `subject_user`, `materials`, `material_progress`, `material_discussions`, `assignments`, `assignment_submissions`, `question_bank`, `question_bank_options`, `quizzes`, `quiz_questions`, `quiz_question_options`, `quiz_attempts`, `quiz_answers`.
- Eloquent Models beserta relasinya: `Role`, `SchoolClass` (tabel `classes`), `Subject`, `Material`, `MaterialProgress`, `MaterialDiscussion`, `Assignment`, `AssignmentSubmission`, `QuestionBank`, `QuestionBankOption`, `Quiz`, `QuizQuestion`, `QuizQuestionOption`, `QuizAttempt`, `QuizAnswer`.
- Seeders: `RoleSeeder` (`guru`, `siswa`), `SchoolClassSeeder` (3 kelas), `SubjectSeeder` (3 mata pelajaran), `UserSeeder` (1 Guru `guru@lms.com`, 3 Siswa), dan `DatabaseSeeder`.
- Feature Test `tests/Feature/FoundationTest.php` untuk memverifikasi keutuhan tabel, seeder, unique constraints, dan relasi Eloquent.

### Diubah
- Model `User.php`: Tambah `$fillable` (`nip`, `role_id`, `class_id`), method helper `isGuru()` / `isSiswa()`, serta relasi ke Role, SchoolClass, Subject, Material, Assignment, Quiz, QuestionBank, dan Submissions/Attempts.
