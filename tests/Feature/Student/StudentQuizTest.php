<?php

namespace Tests\Feature\Student;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentQuizTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private SchoolClass $class;
    private Quiz $quiz;
    private QuizQuestion $question;
    private QuizQuestionOption $optionCorrect;
    private QuizQuestionOption $optionWrong;

    protected function setUp(): void
    {
        parent::setUp();

        $studentRole = Role::create(['name' => 'siswa', 'display_name' => 'Siswa']);
        $teacherRole = Role::create(['name' => 'guru', 'display_name' => 'Guru']);

        $this->class = SchoolClass::create([
            'name' => 'Kelas X IPA 1',
            'code' => 'X-IPA-1',
        ]);

        $this->student = User::factory()->create([
            'role_id' => $studentRole->id,
            'class_id' => $this->class->id,
        ]);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
        ]);

        $subject = Subject::create([
            'code' => 'MTK01',
            'name' => 'Matematika',
        ]);

        $this->quiz = new Quiz([
            'title' => 'Kuis Harian 1',
            'duration_minutes' => 30,
            'points_per_question' => 100,
            'deadline' => now()->addDays(7),
        ]);
        $this->quiz->class_id = $this->class->id;
        $this->quiz->subject_id = $subject->id;
        $this->quiz->instructor_id = $teacher->id;
        $this->quiz->save();

        $this->question = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_text' => 'Berapakah 5 + 5?',
        ]);

        $this->optionCorrect = QuizQuestionOption::create([
            'quiz_question_id' => $this->question->id,
            'option_text' => '10',
            'is_correct' => true,
        ]);

        $this->optionWrong = QuizQuestionOption::create([
            'quiz_question_id' => $this->question->id,
            'option_text' => '12',
            'is_correct' => false,
        ]);
    }

    public function test_siswa_can_view_quizzes_for_their_class(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.quizzes.index'));
        $response->assertStatus(200);
        $response->assertSee('Kuis Harian 1');
    }

    public function test_siswa_can_start_and_submit_quiz_with_scoring(): void
    {
        // Start quiz
        $startResponse = $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));
        $startResponse->assertRedirect(route('student.quizzes.attempt', $this->quiz));

        $this->assertDatabaseHas('quiz_attempts', [
            'student_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
        ]);

        // Submit correct answer
        $submitResponse = $this->actingAs($this->student)->post(route('student.quizzes.submit', $this->quiz), [
            'answers' => [
                $this->question->id => $this->optionCorrect->id,
            ],
        ]);

        $submitResponse->assertRedirect(route('student.quizzes.result', $this->quiz));

        $this->assertDatabaseHas('quiz_attempts', [
            'student_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
            'score' => 100,
        ]);

        $this->assertDatabaseHas('quiz_answers', [
            'quiz_question_id' => $this->question->id,
            'selected_option_id' => $this->optionCorrect->id,
        ]);
    }

    public function test_siswa_can_view_quiz_result_preview_showing_selected_and_correct_answers(): void
    {
        $attempt = QuizAttempt::create([
            'student_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
            'score' => 100,
            'started_at' => now()->subMinutes(10),
            'submitted_at' => now(),
        ]);

        \App\Models\QuizAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $this->question->id,
            'selected_option_id' => $this->optionCorrect->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.quizzes.result', $this->quiz));
        $response->assertStatus(200);
        $response->assertSee('Hasil &amp; Preview Kuis', false);
        $response->assertSee('Berapakah 5 + 5?');
        $response->assertSee('Jawaban Anda');
        $response->assertSee('Jawaban Benar');
    }

    public function test_siswa_cannot_access_quiz_from_another_class(): void
    {
        $otherClass = SchoolClass::create(['name' => 'Kelas XI IPA 2', 'code' => 'XI-IPA-2']);
        $otherQuiz = new Quiz([
            'title' => 'Kuis Rahasia',
            'duration_minutes' => 30,
            'deadline' => now()->addDays(7),
        ]);
        $otherQuiz->class_id = $otherClass->id;
        $otherQuiz->subject_id = $this->quiz->subject_id;
        $otherQuiz->instructor_id = $this->quiz->instructor_id;
        $otherQuiz->save();

        $response = $this->actingAs($this->student)->get(route('student.quizzes.show', $otherQuiz));
        $response->assertStatus(403);
    }

    public function test_quiz_timer_decreases_accurately_on_page_refresh(): void
    {
        // Siswa memulai kuis (durasi 30 menit)
        $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));

        $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        // Simulasikan 10 menit telah berlalu (halaman ditutup/berpindah)
        $attempt->update([
            'started_at' => now()->subMinutes(10),
        ]);

        // Siswa me-refresh / membuka kembali halaman kuis
        $response = $this->actingAs($this->student)->get(route('student.quizzes.attempt', $this->quiz));
        $response->assertStatus(200);

        // Sisa waktu seharusnya berkurang dari 1800 detik menjadi ~1200 detik (20 menit tersisa)
        $remainingSeconds = $response->viewData('remainingSeconds');
        $this->assertLessThanOrEqual(1201, $remainingSeconds);
        $this->assertGreaterThanOrEqual(1195, $remainingSeconds);
    }

    public function test_quiz_auto_submits_when_attempt_timer_has_expired(): void
    {
        $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));

        $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        // Simulasikan waktu pengerjaan telah lewat 31 menit (melebihi durasi 30 menit)
        $attempt->update([
            'started_at' => now()->subMinutes(31),
        ]);

        // Saat siswa kembali/refresh halaman kuis setelah waktu habis
        $response = $this->actingAs($this->student)->get(route('student.quizzes.attempt', $this->quiz));

        // Harus otomatis diarahkan ke halaman hasil dengan pesan bahwa kuis sudah dikumpulkan
        $response->assertRedirect(route('student.quizzes.result', $this->quiz));
        $response->assertSessionHas('info');

        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at);
    }

    public function test_save_answer_returns_expired_when_time_exceeded(): void
    {
        $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));

        $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        // Simulasikan waktu habis saat mencoba mengirim jawaban
        $attempt->update([
            'started_at' => now()->subMinutes(35),
        ]);

        $response = $this->actingAs($this->student)->postJson(route('student.quizzes.save-answer', $this->quiz), [
            'question_id' => $this->question->id,
            'option_id' => $this->optionCorrect->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'expired']);
    }

    public function test_siswa_can_view_quiz_result_with_duration_and_palette(): void
    {
        $attempt = QuizAttempt::create([
            'student_id' => $this->student->id,
            'quiz_id' => $this->quiz->id,
            'score' => 100,
            'started_at' => now()->subMinutes(12)->subSeconds(35),
            'submitted_at' => now(),
        ]);

        \App\Models\QuizAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $this->question->id,
            'selected_option_id' => $this->optionCorrect->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.quizzes.result', $this->quiz));
        $response->assertStatus(200);
        $response->assertSee('Waktu Pengerjaan');
        $response->assertSee('12 Menit');
        $response->assertSee('Navigasi Soal');
        $response->assertSee('Jawaban Benar');
    }

    public function test_siswa_can_submit_quiz_with_unanswered_questions_without_integrity_error(): void
    {
        // Tambahkan soal kedua
        $question2 = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_text' => 'Berapakah 2 + 2?',
        ]);
        $opt2Correct = QuizQuestionOption::create([
            'quiz_question_id' => $question2->id,
            'option_text' => '4',
            'is_correct' => true,
        ]);

        // Start quiz
        $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));

        // Submit quiz HANYA menjawab soal 1, soal 2 dikosongkan (unanswered)
        $submitResponse = $this->actingAs($this->student)->post(route('student.quizzes.submit', $this->quiz), [
            'answers' => [
                $this->question->id => $this->optionCorrect->id,
                // $question2->id sengaja tidak dijawab
            ],
        ]);

        $submitResponse->assertRedirect(route('student.quizzes.result', $this->quiz));

        // Attempt terupdate dengan skor 50% (1 dari 2 benar)
        $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        $this->assertEquals(50, $attempt->score);
        $this->assertNotNull($attempt->submitted_at);

        // Halaman result dapat diakses dengan normal
        $resultResponse = $this->actingAs($this->student)->get(route('student.quizzes.result', $this->quiz));
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Skor: 50 / 100');
    }

    public function test_siswa_can_attempt_and_submit_true_false_and_matching_questions(): void
    {
        // 1. Create a true_false question
        $tfQuestion = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'true_false',
            'question_text' => 'Air mendidih pada suhu 100 derajat Celcius.',
        ]);
        $tfOptBenar = QuizQuestionOption::create([
            'quiz_question_id' => $tfQuestion->id,
            'option_text' => 'Benar',
            'is_correct' => true,
        ]);
        QuizQuestionOption::create([
            'quiz_question_id' => $tfQuestion->id,
            'option_text' => 'Salah',
            'is_correct' => false,
        ]);

        // 2. Create a matching question
        $matchQuestion = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'matching',
            'question_text' => 'Jodohkan istilah berikut.',
        ]);
        $optPair1 = QuizQuestionOption::create([
            'quiz_question_id' => $matchQuestion->id,
            'option_text' => 'CPU',
            'match_text' => 'Central Processing Unit',
            'is_correct' => false,
        ]);
        $optPair2 = QuizQuestionOption::create([
            'quiz_question_id' => $matchQuestion->id,
            'option_text' => 'RAM',
            'match_text' => 'Random Access Memory',
            'is_correct' => false,
        ]);

        // Siswa starts quiz
        $this->actingAs($this->student)->post(route('student.quizzes.start', $this->quiz));

        // Siswa auto-saves answer for matching via AJAX
        $saveResponse = $this->actingAs($this->student)->postJson(route('student.quizzes.save-answer', $this->quiz), [
            'question_id' => $matchQuestion->id,
            'match_answers' => [
                $optPair1->id => 'Central Processing Unit',
                $optPair2->id => 'Random Access Memory',
            ],
        ]);
        $saveResponse->assertStatus(200);
        $saveResponse->assertJson(['status' => 'saved']);

        // Siswa submits quiz: original MC question + TF question + Matching question
        $submitResponse = $this->actingAs($this->student)->post(route('student.quizzes.submit', $this->quiz), [
            'answers' => [
                $this->question->id => $this->optionCorrect->id,
                $tfQuestion->id => $tfOptBenar->id,
            ],
            'matching_answers' => [
                $matchQuestion->id => [
                    $optPair1->id => 'Central Processing Unit',
                    $optPair2->id => 'Random Access Memory',
                ],
            ],
        ]);

        $submitResponse->assertRedirect(route('student.quizzes.result', $this->quiz));

        // Semua 3 soal dijawab benar sempurna => Skor 100
        $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        $this->assertEquals(100, $attempt->score);

        // Halaman result menampilkan format dan preview
        $resultResponse = $this->actingAs($this->student)->get(route('student.quizzes.result', $this->quiz));
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Menjodohkan');
        $resultResponse->assertSee('Benar / Salah');
        $resultResponse->assertSee('Central Processing Unit');
        $resultResponse->assertSee('Random Access Memory');
    }

    public function test_matching_question_scores_proportionally(): void
    {
        // Kuis khusus dengan 1 soal menjodohkan (4 pasangan)
        $matchingQuiz = Quiz::create([
            'title' => 'Kuis Menjodohkan Proporsional',
            'duration_minutes' => 20,
            'points_per_question' => 100,
            'deadline' => now()->addDays(3),
            'class_id' => $this->class->id,
            'subject_id' => $this->quiz->subject_id,
            'instructor_id' => $this->quiz->instructor_id,
        ]);

        $qMatch = QuizQuestion::create([
            'quiz_id' => $matchingQuiz->id,
            'question_type' => 'matching',
            'question_text' => 'Jodohkan 4 negara dengan ibukotanya.',
        ]);

        $p1 = QuizQuestionOption::create(['quiz_question_id' => $qMatch->id, 'option_text' => 'A', 'match_text' => 'Alpha']);
        $p2 = QuizQuestionOption::create(['quiz_question_id' => $qMatch->id, 'option_text' => 'B', 'match_text' => 'Beta']);
        $p3 = QuizQuestionOption::create(['quiz_question_id' => $qMatch->id, 'option_text' => 'C', 'match_text' => 'Charlie']);
        $p4 = QuizQuestionOption::create(['quiz_question_id' => $qMatch->id, 'option_text' => 'D', 'match_text' => 'Delta']);

        // Siswa starts quiz
        $this->actingAs($this->student)->post(route('student.quizzes.start', $matchingQuiz));

        // Siswa menjawab 2 benar (A & B) dan 2 salah (C & D)
        $submitResponse = $this->actingAs($this->student)->post(route('student.quizzes.submit', $matchingQuiz), [
            'matching_answers' => [
                $qMatch->id => [
                    $p1->id => 'Alpha',    // Benar
                    $p2->id => 'Beta',     // Benar
                    $p3->id => 'Salah C',  // Salah
                    $p4->id => 'Salah D',  // Salah
                ],
            ],
        ]);

        $submitResponse->assertRedirect(route('student.quizzes.result', $matchingQuiz));

        $attempt = QuizAttempt::where('quiz_id', $matchingQuiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        // 2 benar dari 4 pasangan = 50%
        $this->assertEquals(50, $attempt->score);
    }

    public function test_matching_questions_scored_per_item_in_mixed_quiz(): void
    {
        // Kuis campuran: 1 Pilihan Ganda (1 item) + 1 Menjodohkan (4 pasangan = 4 item). Total = 5 butir soal.
        $mixedQuiz = Quiz::create([
            'title' => 'Kuis Campuran PG & Menjodohkan',
            'duration_minutes' => 30,
            'points_per_question' => 100,
            'deadline' => now()->addDays(2),
            'class_id' => $this->class->id,
            'subject_id' => $this->quiz->subject_id,
            'instructor_id' => $this->quiz->instructor_id,
        ]);

        $mcQuestion = QuizQuestion::create([
            'quiz_id' => $mixedQuiz->id,
            'question_type' => 'multiple_choice',
            'question_text' => 'Berapa 10 / 2?',
        ]);
        $mcCorrect = QuizQuestionOption::create([
            'quiz_question_id' => $mcQuestion->id,
            'option_text' => '5',
            'is_correct' => true,
        ]);
        QuizQuestionOption::create([
            'quiz_question_id' => $mcQuestion->id,
            'option_text' => '2',
            'is_correct' => false,
        ]);

        $matchQuestion = QuizQuestion::create([
            'quiz_id' => $mixedQuiz->id,
            'question_type' => 'matching',
            'question_text' => 'Jodohkan istilah.',
        ]);
        $p1 = QuizQuestionOption::create(['quiz_question_id' => $matchQuestion->id, 'option_text' => 'P1', 'match_text' => 'M1']);
        $p2 = QuizQuestionOption::create(['quiz_question_id' => $matchQuestion->id, 'option_text' => 'P2', 'match_text' => 'M2']);
        $p3 = QuizQuestionOption::create(['quiz_question_id' => $matchQuestion->id, 'option_text' => 'P3', 'match_text' => 'M3']);
        $p4 = QuizQuestionOption::create(['quiz_question_id' => $matchQuestion->id, 'option_text' => 'P4', 'match_text' => 'M4']);

        // Siswa mulai kuis
        $this->actingAs($this->student)->post(route('student.quizzes.start', $mixedQuiz));

        // Siswa menjawab:
        // - PG Benar (1 item benar)
        // - Menjodohkan: 3 Benar (P1, P2, P3), 1 Salah (P4) (3 item benar)
        // Total benar: 1 + 3 = 4 dari 5 item => Skor = (4 / 5) * 100 = 80
        $submitResponse = $this->actingAs($this->student)->post(route('student.quizzes.submit', $mixedQuiz), [
            'answers' => [
                $mcQuestion->id => $mcCorrect->id,
            ],
            'matching_answers' => [
                $matchQuestion->id => [
                    $p1->id => 'M1', // Benar
                    $p2->id => 'M2', // Benar
                    $p3->id => 'M3', // Benar
                    $p4->id => 'Salah', // Salah
                ],
            ],
        ]);

        $submitResponse->assertRedirect(route('student.quizzes.result', $mixedQuiz));

        $attempt = QuizAttempt::where('quiz_id', $mixedQuiz->id)
            ->where('student_id', $this->student->id)
            ->first();

        $this->assertEquals(80, $attempt->score);
    }

    public function test_total_questions_count_and_types_summary_for_mixed_quizzes(): void
    {
        $quiz = Quiz::create([
            'title' => 'Kuis Variatif',
            'duration_minutes' => 45,
            'points_per_question' => 100,
            'deadline' => now()->addDays(3),
            'class_id' => $this->class->id,
            'subject_id' => $this->quiz->subject_id,
            'instructor_id' => $this->quiz->instructor_id,
        ]);

        // 1 MC (1 item)
        $mc = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'multiple_choice',
            'question_text' => 'Soal 1',
        ]);
        QuizQuestionOption::create(['quiz_question_id' => $mc->id, 'option_text' => 'A', 'is_correct' => true]);
        QuizQuestionOption::create(['quiz_question_id' => $mc->id, 'option_text' => 'B', 'is_correct' => false]);

        // 1 TF (1 item)
        $tf = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'true_false',
            'question_text' => 'Soal 2',
        ]);
        QuizQuestionOption::create(['quiz_question_id' => $tf->id, 'option_text' => 'Benar', 'is_correct' => true]);
        QuizQuestionOption::create(['quiz_question_id' => $tf->id, 'option_text' => 'Salah', 'is_correct' => false]);

        // 1 Matching with 4 pairs (4 items)
        $match = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => 'matching',
            'question_text' => 'Soal 3',
        ]);
        QuizQuestionOption::create(['quiz_question_id' => $match->id, 'option_text' => 'P1', 'match_text' => 'M1']);
        QuizQuestionOption::create(['quiz_question_id' => $match->id, 'option_text' => 'P2', 'match_text' => 'M2']);
        QuizQuestionOption::create(['quiz_question_id' => $match->id, 'option_text' => 'P3', 'match_text' => 'M3']);
        QuizQuestionOption::create(['quiz_question_id' => $match->id, 'option_text' => 'P4', 'match_text' => 'M4']);

        // Quiz has 3 question rows, but 1 + 1 + 4 = 6 total scorable question items!
        $this->assertEquals(3, $quiz->questions()->count());
        $this->assertEquals(6, $quiz->total_questions_count);
        $this->assertEquals(6, $quiz->total_scorable_items);
        $this->assertStringContainsString('Campuran', $quiz->question_types_summary);

        $this->assertEquals('45 Menit 0 Detik', $quiz->formatted_duration);
        $this->assertEquals('00:45:00', $quiz->duration_hms);

        // Test student index view shows total_questions_count
        $response = $this->actingAs($this->student)->get(route('student.quizzes.index'));
        $response->assertStatus(200);
        $response->assertSee('6 Butir');

        // Test student show view shows total_questions_count and formatted duration
        $responseShow = $this->actingAs($this->student)->get(route('student.quizzes.show', $quiz));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('6 Butir Soal');
        $responseShow->assertSee('45 Menit 0 Detik');
        $responseShow->assertDontSee('Soal & Opsi Jawaban Diacak Otomatis');
    }

    public function test_questions_and_options_are_randomized_per_student_attempt(): void
    {
        $studentRole = Role::firstOrCreate(['name' => 'siswa'], ['display_name' => 'Siswa']);
        $student2 = User::factory()->create([
            'role_id' => $studentRole->id,
            'class_id' => $this->class->id,
        ]);

        $quiz = Quiz::create([
            'title' => 'Kuis Randomisasi Anti Curang',
            'duration_minutes' => 45,
            'points_per_question' => 100,
            'deadline' => now()->addDays(3),
            'class_id' => $this->class->id,
            'subject_id' => $this->quiz->subject_id,
            'instructor_id' => $this->quiz->instructor_id,
        ]);

        // Create 10 distinct MC questions with 4 options each
        for ($i = 1; $i <= 10; $i++) {
            $q = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_type' => 'multiple_choice',
                'question_text' => "Pertanyaan Nomor {$i}",
            ]);
            for ($optIdx = 1; $optIdx <= 4; $optIdx++) {
                QuizQuestionOption::create([
                    'quiz_question_id' => $q->id,
                    'option_text' => "Pilihan {$optIdx} untuk Soal {$i}",
                    'is_correct' => ($optIdx === 1),
                ]);
            }
        }

        // Student 1 starts quiz
        $this->actingAs($this->student)->post(route('student.quizzes.start', $quiz));
        $attempt1 = QuizAttempt::where('student_id', $this->student->id)->where('quiz_id', $quiz->id)->first();
        $orderedQuestions1 = $attempt1->getOrderedQuestions();

        // Student 2 starts quiz
        $this->actingAs($student2)->post(route('student.quizzes.start', $quiz));
        $attempt2 = QuizAttempt::where('student_id', $student2->id)->where('quiz_id', $quiz->id)->first();
        $orderedQuestions2 = $attempt2->getOrderedQuestions();

        // Both attempts have all 10 questions
        $this->assertCount(10, $orderedQuestions1);
        $this->assertCount(10, $orderedQuestions2);

        // Check that question order is not identical (with 10 items, chance of identical random order is 1/3,628,800)
        $qIds1 = $orderedQuestions1->pluck('id')->toArray();
        $qIds2 = $orderedQuestions2->pluck('id')->toArray();
        $this->assertNotEquals($qIds1, $qIds2, 'Question order should be different between two student attempts.');

        // Check consistency on reload for student 1
        $reloadOrderedQuestions1 = $attempt1->getOrderedQuestions();
        $this->assertEquals($qIds1, $reloadOrderedQuestions1->pluck('id')->toArray(), 'Question order should remain consistent on reload for the same student attempt.');

        // Student 1 renders attempt page
        $res = $this->actingAs($this->student)->get(route('student.quizzes.attempt', $quiz));
        $res->assertStatus(200);
        $res->assertSee('Kuis Randomisasi Anti Curang');
    }
}




