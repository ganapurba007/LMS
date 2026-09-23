# Laporan Audit Gap: PRD vs Kode Aktual (Kuis, Bank Soal, & Data Model)

Dokumen ini memuat hasil audit komparasi antara spesifikasi pada [`context/PRD.md`](./PRD.md) (khususnya §3.5 Kuis, §3.6 Bank Soal, dan §4 Data Model) terhadap implementasi aktual di codebase ([`app/Models/`](../app/Models), [`database/migrations/`](../database/migrations), [`app/Http/Controllers/Admin/`](../app/Http/Controllers/Admin), dan [`app/Http/Controllers/Student/`](../app/Http/Controllers/Student)).

---

## 1. Tabel Perbandingan & Pemetaan Gap

| Area PRD | Yang tertulis di PRD | Yang ada di kode aktual | Status |
| :--- | :--- | :--- | :--- |
| **Tipe & Format Soal**<br>(§3.5, §3.6) | Hanya mendukung 1 jenis soal: **Pilihan Ganda** (FR-6.1, FR-6.2: teks pertanyaan, min. 2 opsi, 1 kunci benar). | Mendukung **3 jenis format soal**:<br>1. `multiple_choice` (Pilihan Ganda)<br>2. `true_false` (Benar / Salah)<br>3. `matching` (Menjodohkan pasangan premis ➔ target). | **GAP BESAR (Extended)**<br>PRD belum mencatat tipe *True/False* dan *Matching*. |
| **Skema Tabel `question_bank`**<br>(§4 Data Model) | Kolom: `id`, `instructor_id` (FK), `question_text`, `timestamps`. | Kolom: `id`, `instructor_id` (FK), `question_text`, **`question_type`** (`string`, default `'multiple_choice'`), `timestamps`. | **GAP SKEMA**<br>Field `question_type` belum tercatat di PRD §4. |
| **Skema Tabel `question_bank_options`**<br>(§4 Data Model) | Kolom: `id`, `question_bank_id` (FK), `option_text`, `is_correct`. | Kolom: `id`, `question_bank_id` (FK), `option_text`, **`match_text`** (`text`, nullable untuk pasangan menjodohkan), `is_correct` (`boolean`), `timestamps`. | **GAP SKEMA**<br>Field `match_text` belum tercatat di PRD §4. |
| **Skema Tabel `quiz_questions`**<br>(§4 Data Model) | Kolom: `id`, `quiz_id` (FK), `question_bank_id` (FK, nullable), `question_text` (nullable), `timestamps`. | Kolom: `id`, `quiz_id` (FK), `question_bank_id` (FK, nullable), `question_text` (nullable), **`question_type`** (`string`, default `'multiple_choice'`), `timestamps`. | **GAP SKEMA**<br>Field `question_type` belum tercatat di PRD §4. |
| **Skema Tabel `quiz_question_options`**<br>(§4 Data Model) | Kolom: `id`, `quiz_question_id` (FK), `option_text`, `is_correct`.<br>Catatan PRD: hanya digunakan jika `question_bank_id IS NULL`. | Kolom: `id`, `quiz_question_id` (FK), `option_text`, **`match_text`** (`text`, nullable), `is_correct` (`boolean`), `timestamps`.<br>Aktual: opsi selalu diduplikasi/diisolasi ke tabel ini baik soal baru maupun hasil impor bank soal. | **GAP SKEMA & RELASI**<br>Field `match_text` ada, dan isolasi opsi snapshot kuis selalu aktif. |
| **Skema Tabel `quiz_answers`**<br>(§4 Data Model) | Kolom: `id`, `quiz_attempt_id` (FK), `quiz_question_id` (FK), `selected_option_id` (FK, wajib). | Kolom: `id`, `quiz_attempt_id` (FK), `quiz_question_id` (FK), **`selected_option_id`** (FK, **nullable**), **`answer_data`** (`json`, nullable untuk menyimpan pasangan array jawaban menjodohkan), `timestamps`. | **GAP SKEMA**<br>`selected_option_id` nullable dan ada field `answer_data` (JSON). |
| **Skema Tabel `quiz_attempts`**<br>(§4 Data Model) | Kolom: `id`, `student_id` (FK), `quiz_id` (FK), `score`, `started_at`, `submitted_at`, `UNIQUE(student_id, quiz_id)`. | Sesuai dengan PRD (`id`, `student_id`, `quiz_id`, `score`, `started_at`, `submitted_at`, `unique(student_id, quiz_id)`). Dilengkapi method helper durasi (`duration_formatted`, `duration_hms`). | **SESUAI (Matched)** |
| **Perhitungan Nilai Kuis (Scoring)**<br>(§3.5 FR-5.4) | Total nilai = `points_per_question` × jumlah soal yang dijawab benar (asumsi perhitungan flat sederhana). | Skor akhir dihitung secara proporsional skala 0–100 (`earnedPoints / totalScorableItems * 100`). Untuk tipe Menjodohkan, setiap pasangan premis dihitung sebagai 1 unit skor (*scorable item*). | **GAP LOGIKA BISNIS**<br>Formula nilai aktual menggunakan sistem persentase proporsional multi-format. |
| **Alur Pembuatan Soal di Kuis**<br>(§3.5 FR-5.3) | Input soal baru kuis terpisah dari Bank Soal (hanya mengisi `quiz_questions` dengan `question_bank_id = null`). | Saat guru membuat butir soal baru di halaman kuis, sistem otomatis menyimpannya ke `question_bank` sekaligus mengaitkannya ke `quiz_questions` (auto-sync ke bank soal). | **GAP PERILAKU**<br>Soal kuis baru selalu diarsipkan otomatis ke Bank Soal guru. |
| **Fitur Input Massal & Naskah Soal**<br>(§3.6) | Hanya form pembuatan/edit 1 butir soal per submit form. | 1. **Batch Question Builder**: Buat banyak soal sekaligus dalam 1 form dinamis multi-tab.<br>2. **Parser Dokumen**: Impor otomatis dari file Word (.docx), PDF (.pdf), dan teks (.txt).<br>3. **Download Template Naskah Soal** (.docx dan .txt). | **GAP FITUR TAMBAHAN (Extended)**<br>Fitur parser & batch builder belum tercatat di PRD. |
| **Media & Format Soal (Tabel/Gambar)**<br>(§3.6) | Hanya teks biasa (`question_text`). | Mendukung rendering **Tabel Markdown/HTML**, **Gambar Soal Markdown/HTML**, upload gambar via TinyMCE/AJAX (`uploadImage`), dan badge ringkasan `(tabel)` / `(gambar)`. | **GAP FITUR TAMBAHAN (Extended)** |
| **Monitoring & Reset Attempt Kuis**<br>(§1.3, §3.5, §3.8.4) | Kuis hanya bisa dikerjakan 1 kali oleh siswa (kaku). Tidak ada fitur reset attempt oleh guru. | 1. Halaman monitoring kelas (`/admin/quizzes/{quiz}/students`) menampilkan live data statistik, waktu mulai/selesai, dan durasi.<br>2. Modal AJAX detail tinjauan jawaban siswa.<br>3. Fitur **Reset Pengerjaan Siswa** (`resetStudentAttempt`) untuk mengizinkan siswa mengulang jika terjadi kendala. | **GAP FITUR TAMBAHAN (Extended)** |
| **Bank Materi & Bank Tugas Tambahan**<br>(§4 Data Model, §3.3, §3.4) | Hanya terdapat tabel `materials` dan `assignments`. | Terdapat tabel dan modul master **`material_banks`** dan **`assignment_banks`** yang terintegrasi ke form materi & tugas. | **GAP SKEMA TAMBAHAN**<br>Tabel bank materi & bank tugas telah dibuat pada Fase 42. |

---

## 2. Rincian Temuan Utama

### A. Divergensi Format Soal (Question Types)
- Pada **PRD.md §3.6**, Bank Soal didefinisikan secara sempit hanya untuk soal *Pilihan Ganda* dengan opsi `option_text` dan penanda boolean `is_correct`.
- Pada **Kode Aktual** (Fase 43–47), sistem telah berevolusi menjadi **Multi-Format Quiz Engine**:
  1. `multiple_choice`: Pilihan ganda reguler (A, B, C, D, dsb).
  2. `true_false`: Pilihan Benar / Salah otomatis dengan 2 opsi statis.
  3. `matching`: Format menjodohkan premis dan target jawaban menggunakan kolom `match_text` di tabel opsi.

### B. Perbedaan Skema Tabel Soal & Jawaban
1. **`quiz_questions` & `question_bank`**:
   - Memiliki kolom `question_type VARCHAR(255) DEFAULT 'multiple_choice'`.
2. **`quiz_question_options` & `question_bank_options`**:
   - Memiliki kolom `match_text TEXT NULL` untuk menyimpan pasangan jawaban format menjodohkan.
3. **`quiz_answers`**:
   - Kolom `selected_option_id` diubah menjadi `NULLABLE` (karena soal tipe Menjodohkan tidak memilih 1 opsi tunggal).
   - Ditambahkan kolom `answer_data JSON NULL` untuk menyimpan *key-value pair* pasangan jawaban siswa (`[option_id => match_text]`).

### C. Mekanisme Isolasi Data Kuis & Bank Soal
- Di PRD §4 tertulis: *"quiz_question_options hanya digunakan jika soal bukan dari bank soal"*.
- Pada implementasi aktual ([`QuizController::importQuestions`](../app/Http/Controllers/Admin/QuizController.php) & [`QuizController::storeQuestion`](../app/Http/Controllers/Admin/QuizController.php)), seluruh butir opsi dari Bank Soal **selalu disalin secara independen** ke `quiz_question_options`.
- **Manfaat Arsitektur Aktual**: Kuis bersifat *immutable snapshot* — jika guru di kemudian hari mengubah atau menghapus butir soal di Bank Soal, data soal dan opsi pada kuis yang sudah/sedang berjalan tidak akan rusak (*orphaned / mutated*).

### D. Penambahan Bank Materi (`material_banks`) & Bank Tugas (`assignment_banks`)
- Selain Bank Soal, kode aktual telah mengimplementasikan Bank Materi dan Bank Tugas pada migrasi `2026_09_18_000001` & `2026_09_18_000002` yang belum tercantum di PRD §3.2, §3.3, §3.4, maupun §4.

---

## 3. Kesimpulan & Rekomendasi

1. **Kode Aktual Lebih Maju dari Dokumen PRD**: Fitur kuis, bank soal, dan bank materi/tugas di kode aktual jauh lebih kaya fitur (*feature-rich*) dan fleksibel dibandingkan rancangan awal PRD v1.9.
2. **Status Integritas Kode**: Seluruh perubahan skema dan controller sudah didukung oleh test suite (162 passed / 602 assertions).
3. **Langkah Lanjutan Dokumentasi (Jika disetujui)**:
   - Perbarui `context/PRD.md` §3.5 & §3.6 untuk mencantumkan format *True/False*, *Matching*, *Batch Builder*, dan *Document Parser*.
   - Perbarui `context/PRD.md` §4 (Data Model) atau jadikan `context/SCHEMA.md` sebagai rujukan utama skema teknis terkini.
