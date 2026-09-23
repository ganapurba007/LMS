# Database Schema — RuangTerra (LMS)

Dokumen ini merupakan **sumber kebenaran tunggal (*single source of truth*)** untuk struktur basis data aktual RuangTerra berdasarkan seluruh migrasi di [`database/migrations/`](../database/migrations).

---

## 1. Skema Tabel Aktual (ASCII Tree)

```
roles
├── id (bigint, unsigned, auto-increment, PK)
├── name (string) — misal: "guru", "siswa"
└── created_at, updated_at (timestamp, nullable)

classes
├── id (bigint, unsigned, auto-increment, PK)
├── name (string) — misal: "Kelas 10A"
└── created_at, updated_at (timestamp, nullable)

subjects (mata pelajaran)
├── id (bigint, unsigned, auto-increment, PK)
├── name (string) — misal: "Matematika", "Geografi"
└── created_at, updated_at (timestamp, nullable)

users
├── id (bigint, unsigned, auto-increment, PK)
├── name (string)
├── email (string, UNIQUE)
├── nip (string, nullable, UNIQUE) — hanya diisi untuk guru
├── email_verified_at (timestamp, nullable)
├── password (string)
├── role_id (bigint, unsigned, nullable, FK → roles.id, ON DELETE CASCADE)
├── class_id (bigint, unsigned, nullable, FK → classes.id, ON DELETE SET NULL)
├── remember_token (string, nullable)
└── created_at, updated_at (timestamp, nullable)

password_reset_tokens
├── email (string, PK)
├── token (string)
└── created_at (timestamp, nullable)

sessions
├── id (string, PK)
├── user_id (bigint, unsigned, nullable, INDEX)
├── ip_address (string 45, nullable)
├── user_agent (text, nullable)
├── payload (longtext)
└── last_activity (integer, INDEX)

subject_user (pivot: guru ↔ mata pelajaran, many-to-many)
├── id (bigint, unsigned, auto-increment, PK)
├── subject_id (bigint, unsigned, FK → subjects.id, ON DELETE CASCADE)
├── user_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
└── created_at, updated_at (timestamp, nullable)

material_banks (bank materi induk guru)
├── id (bigint, unsigned, auto-increment, PK)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── subject_id (bigint, unsigned, nullable, FK → subjects.id, ON DELETE SET NULL)
├── title (string)
├── content_type (enum: 'text', 'document', 'youtube', default: 'text')
├── content (longtext, nullable)
├── document_path (string, nullable)
├── video_url (string, nullable)
└── created_at, updated_at (timestamp, nullable)

materials (materi pembelajaran per kelas)
├── id (bigint, unsigned, auto-increment, PK)
├── class_id (bigint, unsigned, FK → classes.id, ON DELETE CASCADE)
├── subject_id (bigint, unsigned, FK → subjects.id, ON DELETE CASCADE)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── title (string)
├── content_type (enum: 'text', 'document', 'youtube', default: 'text')
├── content (text, nullable)
├── document_path (string, nullable)
├── video_url (string, nullable)
├── order (integer, default: 0)
├── created_at, updated_at (timestamp, nullable)
└── INDEXES:
    ├── idx_materials_class_order (class_id, order)
    └── idx_materials_instructor_order (instructor_id, order)

material_progress (pencatatan progres belajar modul per siswa)
├── id (bigint, unsigned, auto-increment, PK)
├── user_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── material_id (bigint, unsigned, FK → materials.id, ON DELETE CASCADE)
├── is_completed (boolean, default: false)
├── completed_at (timestamp, nullable)
├── created_at, updated_at (timestamp, nullable)
├── UNIQUE: (user_id, material_id)
└── INDEX: idx_mat_prog_user_completed (user_id, is_completed)

material_discussions (ruang diskusi realtime materi)
├── id (bigint, unsigned, auto-increment, PK)
├── material_id (bigint, unsigned, FK → materials.id, ON DELETE CASCADE)
├── parent_id (bigint, unsigned, nullable, FK → material_discussions.id, ON DELETE CASCADE)
├── user_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── comment (text)
└── created_at, updated_at (timestamp, nullable)

assignment_banks (bank tugas induk guru)
├── id (bigint, unsigned, auto-increment, PK)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── subject_id (bigint, unsigned, nullable, FK → subjects.id, ON DELETE SET NULL)
├── title (string)
├── description (longtext, nullable)
└── created_at, updated_at (timestamp, nullable)

assignments (tugas kelas)
├── id (bigint, unsigned, auto-increment, PK)
├── class_id (bigint, unsigned, FK → classes.id, ON DELETE CASCADE)
├── subject_id (bigint, unsigned, FK → subjects.id, ON DELETE CASCADE)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── title (string)
├── description (text)
├── due_date (datetime)
└── created_at, updated_at (timestamp, nullable)

assignment_submissions (pengumpulan & penilaian tugas essay)
├── id (bigint, unsigned, auto-increment, PK)
├── assignment_id (bigint, unsigned, FK → assignments.id, ON DELETE CASCADE)
├── student_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── answer_text (text)
├── submitted_at (datetime)
├── grade (decimal 5,2, nullable) — skala 0.00 s.d 100.00
├── feedback (text, nullable)
├── graded_at (datetime, nullable)
├── created_at, updated_at (timestamp, nullable)
├── UNIQUE: (assignment_id, student_id)
└── INDEX: idx_asg_sub_student_grade (student_id, grade)

question_bank (bank butir soal induk guru)
├── id (bigint, unsigned, auto-increment, PK)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── question_text (text)
├── question_type (string, default: 'multiple_choice') — multiple_choice / true_false / matching
└── created_at, updated_at (timestamp, nullable)

question_bank_options (opsi & pasangan butir soal bank soal)
├── id (bigint, unsigned, auto-increment, PK)
├── question_bank_id (bigint, unsigned, FK → question_bank.id, ON DELETE CASCADE)
├── option_text (text) — teks opsi PG / pernyataan / premis menjodohkan
├── match_text (text, nullable) — pasangan jawaban (khusus tipe matching)
├── is_correct (boolean, default: false)
└── created_at, updated_at (timestamp, nullable)

quizzes (jadwal & pengaturan kuis kelas)
├── id (bigint, unsigned, auto-increment, PK)
├── class_id (bigint, unsigned, FK → classes.id, ON DELETE CASCADE)
├── subject_id (bigint, unsigned, FK → subjects.id, ON DELETE CASCADE)
├── instructor_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── title (string)
├── points_per_question (integer, default: 10)
├── deadline (datetime)
├── duration_minutes (integer)
├── created_at, updated_at (timestamp, nullable)
└── INDEXES:
    ├── idx_quizzes_class_deadline (class_id, deadline)
    └── idx_quizzes_instructor_deadline (instructor_id, deadline)

quiz_questions (butir soal kuis terdaftar)
├── id (bigint, unsigned, auto-increment, PK)
├── quiz_id (bigint, unsigned, FK → quizzes.id, ON DELETE CASCADE)
├── question_bank_id (bigint, unsigned, nullable, FK → question_bank.id, ON DELETE SET NULL)
├── question_text (text, nullable)
├── question_type (string, default: 'multiple_choice') — multiple_choice / true_false / matching
└── created_at, updated_at (timestamp, nullable)

quiz_question_options (opsi & pasangan butir kuis terisolasi)
├── id (bigint, unsigned, auto-increment, PK)
├── quiz_question_id (bigint, unsigned, FK → quiz_questions.id, ON DELETE CASCADE)
├── option_text (text)
├── match_text (text, nullable) — pasangan jawaban (khusus tipe matching)
├── is_correct (boolean, default: false)
└── created_at, updated_at (timestamp, nullable)

quiz_attempts (sesi pengerjaan kuis siswa)
├── id (bigint, unsigned, auto-increment, PK)
├── student_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── quiz_id (bigint, unsigned, FK → quizzes.id, ON DELETE CASCADE)
├── score (decimal 5,2, nullable) — skala 0.00 s.d 100.00
├── started_at (datetime)
├── submitted_at (datetime, nullable)
├── created_at, updated_at (timestamp, nullable)
├── UNIQUE: (student_id, quiz_id)
└── INDEXES:
    ├── idx_quiz_attempts_quiz_submitted (quiz_id, submitted_at)
    └── idx_quiz_attempts_student_submitted (student_id, submitted_at)

quiz_answers (jawaban butir per attempt pengerjaan)
├── id (bigint, unsigned, auto-increment, PK)
├── quiz_attempt_id (bigint, unsigned, FK → quiz_attempts.id, ON DELETE CASCADE)
├── quiz_question_id (bigint, unsigned, FK → quiz_questions.id, ON DELETE CASCADE)
├── selected_option_id (bigint, unsigned, nullable, FK → quiz_question_options.id, ON DELETE CASCADE)
├── answer_data (json, nullable) — array pasangan [option_id => match_text] untuk matching
└── created_at, updated_at (timestamp, nullable)

notifications (notifikasi sistem & broadcast)
├── id (bigint, unsigned, auto-increment, PK)
├── user_id (bigint, unsigned, FK → users.id, ON DELETE CASCADE)
├── type (string) — comment, new_material, new_assignment, new_quiz
├── title (string)
├── message (text)
├── related_url (string, nullable)
├── is_read (boolean, default: false)
├── read_at (timestamp, nullable)
├── created_at, updated_at (timestamp, nullable)
└── INDEX: idx_notif_user_read_created (user_id, is_read, created_at)
```

---

## 2. Entity Relationship Diagram (ERD)

```
Class (classes) ──< User (siswa, via class_id)
      │
      ├──< Material (materials) ──< MaterialProgress (material_progress)
      │                        └──< MaterialDiscussion (material_discussions, self-referencing via parent_id)
      ├──< Assignment (assignments) ──< AssignmentSubmission (assignment_submissions)
      └──< Quiz (quizzes) ──< QuizQuestion (quiz_questions) ──< QuizQuestionOption (quiz_question_options)
                │
                └──< QuizAttempt (quiz_attempts) ──< QuizAnswer (quiz_answers)

Subject (subjects) ──< Material (materials, via subject_id)
Subject (subjects) ──< Assignment (assignments, via subject_id)
Subject (subjects) ──< Quiz (quizzes, via subject_id)
Subject (subjects) ──>< User (guru) [many-to-many via pivot subject_user]

User (guru) ──< MaterialBank (material_banks)
User (guru) ──< AssignmentBank (assignment_banks)
User (guru) ──< QuestionBank (question_bank) ──< QuestionBankOption (question_bank_options)

QuizQuestion (quiz_questions) ──> QuestionBank (question_bank, FK nullable auto-sync)
Role (roles) ──< User (users, via role_id)
User (users) ──< Notification (notifications, via user_id)
```

---

## 3. Catatan Arsitektur & Aturan Integritas Data

1. **Multi-Format Soal**:
   - `question_type`: Menampung `'multiple_choice'`, `'true_false'`, atau `'matching'`.
   - `match_text`: Digunakan pada tabel opsi untuk mencatat pasangan jawaban format Menjodohkan.
   - `answer_data`: Format JSON pada `quiz_answers` menyimpan relasi pasangan yang dipilih siswa.
2. **Snapshot Immutability Kuis**:
   - Saat soal diimpor dari `question_bank` atau dibuat di kuis, seluruh butir opsi selalu diduplikasi secara independen ke `quiz_question_options`. Hal ini mencegah mutasi pada kuis aktif apabila master bank soal di kemudian hari diubah/dihapus oleh guru.
3. **Threaded Discussions**:
   - `parent_id` pada `material_discussions` mengimplementasikan pola self-referencing hierarchy untuk mendukung balasan diskusi bertingkat (*threaded replies*).
4. **Indeks Performa Terintegrasi**:
   - Diterapkan indeks gabungan (*composite indexes*) pada seluruh filter query frekuensi tinggi (notifikasi unread, pencapaian materi, submission nilai tugas, dan pengurutan deadline kuis).
