<?php

namespace Tests\Feature\Admin;

use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected User $siswa;
    protected SchoolClass $class;
    protected Subject $subject;
    protected Subject $unassignedSubject;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::create(['name' => 'guru']);
        $roleSiswa = Role::create(['name' => 'siswa']);

        $this->class = SchoolClass::create(['name' => 'Kelas XII IPA 1']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'class_id' => null,
            'nip' => '198201012005011003',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->class->id,
        ]);

        $this->subject = Subject::create([
            'name' => 'Sejarah',
            'code' => 'SEJ-12',
        ]);

        $this->unassignedSubject = Subject::create([
            'name' => 'Geografi',
            'code' => 'GEO-12',
        ]);

        $this->guru->subjects()->attach($this->subject->id);
    }

    public function test_guru_can_view_quiz_list(): void
    {
        Quiz::create([
            'title' => 'Kuis Evaluation',
            'duration_minutes' => 30,
            'points_per_question' => 10,
            'deadline' => now()->addDays(3),
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        $response = $this->actingAs($this->guru)->get(route('admin.quizzes.index'));
        $response->assertStatus(200);
        $response->assertSee('Kuis Evaluation');
    }

    public function test_guru_can_create_quiz_and_redirects_to_show(): void
    {
        $deadline = now()->addDays(5)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store'), [
            'title' => 'Kuis Sejarah Proklamasi',
            'duration_minutes' => 45,
            'points_per_question' => 10,
            'deadline' => $deadline,
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
        ]);

        $quiz = Quiz::where('title', 'Kuis Sejarah Proklamasi')->first();
        $this->assertNotNull($quiz);
        $response->assertRedirect(route('admin.quizzes.show', $quiz));
        $this->assertEquals($this->guru->id, $quiz->instructor_id);
    }

    public function test_guru_cannot_create_quiz_for_unassigned_subject(): void
    {
        $deadline = now()->addDays(5)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store'), [
            'title' => 'Kuis Geografi Peta',
            'duration_minutes' => 30,
            'points_per_question' => 5,
            'deadline' => $deadline,
            'subject_id' => $this->unassignedSubject->id,
            'class_id' => $this->class->id,
        ]);

        $response->assertSessionHasErrors(['subject_id']);
        $this->assertDatabaseMissing('quizzes', [
            'title' => 'Kuis Geografi Peta',
        ]);
    }

    public function test_guru_can_import_questions_from_question_bank(): void
    {
        $qb = new QuestionBank();
        $qb->question_text = 'Siapa teks proklamasi?';
        $qb->instructor_id = $this->guru->id;
        $qb->save();

        QuestionBankOption::create(['question_bank_id' => $qb->id, 'option_text' => 'Ir. Soekarno', 'is_correct' => true]);
        QuestionBankOption::create(['question_bank_id' => $qb->id, 'option_text' => 'Moh. Hatta', 'is_correct' => false]);

        $quiz = new Quiz();
        $quiz->title = 'Kuis Tes Impor';
        $quiz->duration_minutes = 30;
        $quiz->points_per_question = 10;
        $quiz->deadline = now()->addDays(2);
        $quiz->subject_id = $this->subject->id;
        $quiz->class_id = $this->class->id;
        $quiz->instructor_id = $this->guru->id;
        $quiz->save();

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.import-questions', $quiz), [
            'question_bank_ids' => [$qb->id],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_bank_id' => $qb->id,
            'question_text' => 'Siapa teks proklamasi?',
        ]);
        $this->assertDatabaseHas('quiz_question_options', [
            'option_text' => 'Ir. Soekarno',
            'is_correct' => true,
        ]);
    }

    public function test_guru_can_store_and_delete_custom_question(): void
    {
        $quiz = new Quiz();
        $quiz->title = 'Kuis Soal Manual';
        $quiz->duration_minutes = 20;
        $quiz->points_per_question = 10;
        $quiz->deadline = now()->addDays(2);
        $quiz->subject_id = $this->subject->id;
        $quiz->class_id = $this->class->id;
        $quiz->instructor_id = $this->guru->id;
        $quiz->save();

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store-question', $quiz), [
            'question_text' => 'Kapan Indonesia merdeka?',
            'options' => ['17 Agustus 1945', '18 Agustus 1945', '10 November 1945', '20 Mei 1908'],
            'correct_option' => 0,
        ]);

        $response->assertSessionHasNoErrors();
        $qq = QuizQuestion::where('quiz_id', $quiz->id)->first();
        $this->assertNotNull($qq);
        $this->assertEquals('Kapan Indonesia merdeka?', $qq->question_text);

        $this->assertDatabaseHas('quiz_question_options', [
            'quiz_question_id' => $qq->id,
            'option_text' => '17 Agustus 1945',
            'is_correct' => true,
        ]);

        // Delete question
        $deleteResponse = $this->actingAs($this->guru)->delete(route('admin.quizzes.destroy-question', [$quiz, $qq]));
        $deleteResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('quiz_questions', ['id' => $qq->id]);
    }

    public function test_guru_can_update_and_delete_quiz(): void
    {
        $quiz = new Quiz();
        $quiz->title = 'Kuis Lama';
        $quiz->duration_minutes = 15;
        $quiz->points_per_question = 5;
        $quiz->deadline = now()->addDays(1);
        $quiz->subject_id = $this->subject->id;
        $quiz->class_id = $this->class->id;
        $quiz->instructor_id = $this->guru->id;
        $quiz->save();

        $newDeadline = now()->addDays(4)->format('Y-m-d H:i:s');

        $updateResponse = $this->actingAs($this->guru)->put(route('admin.quizzes.update', $quiz), [
            'title' => 'Kuis Update Baru',
            'duration_minutes' => 60,
            'points_per_question' => 20,
            'deadline' => $newDeadline,
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
        ]);

        $updateResponse->assertRedirect(route('admin.quizzes.index'));
        $this->assertDatabaseHas('quizzes', [
            'id' => $quiz->id,
            'title' => 'Kuis Update Baru',
            'duration_minutes' => 60,
        ]);

        $deleteResponse = $this->actingAs($this->guru)->delete(route('admin.quizzes.destroy', $quiz));
        $deleteResponse->assertRedirect(route('admin.quizzes.index'));
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }

    public function test_guru_can_create_true_false_question(): void
    {
        $quiz = Quiz::create([
            'title' => 'Kuis Benar Salah',
            'duration_minutes' => 15,
            'points_per_question' => 10,
            'deadline' => now()->addDays(2),
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store-question', $quiz), [
            'question_type' => 'true_false',
            'question_text' => 'Bumi mengelilingi matahari.',
            'tf_correct_answer' => 'Benar',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_type' => 'true_false',
            'question_text' => 'Bumi mengelilingi matahari.',
        ]);

        $question = QuizQuestion::where('quiz_id', $quiz->id)->first();
        $this->assertCount(2, $question->options);

        $benarOpt = $question->options->firstWhere('option_text', 'Benar');
        $salahOpt = $question->options->firstWhere('option_text', 'Salah');

        $this->assertTrue((bool)$benarOpt->is_correct);
        $this->assertFalse((bool)$salahOpt->is_correct);
    }

    public function test_guru_can_create_matching_question(): void
    {
        $quiz = Quiz::create([
            'title' => 'Kuis Menjodohkan',
            'duration_minutes' => 25,
            'points_per_question' => 20,
            'deadline' => now()->addDays(3),
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store-question', $quiz), [
            'question_type' => 'matching',
            'question_text' => 'Jodohkan negara berikut dengan ibukotanya.',
            'matching_pairs' => [
                ['premise' => 'Indonesia', 'match' => 'Jakarta'],
                ['premise' => 'Jepang', 'match' => 'Tokyo'],
                ['premise' => 'Perancis', 'match' => 'Paris'],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_type' => 'matching',
            'question_text' => 'Jodohkan negara berikut dengan ibukotanya.',
        ]);

        $question = QuizQuestion::where('quiz_id', $quiz->id)->first();
        $this->assertCount(3, $question->options);
        $this->assertDatabaseHas('quiz_question_options', [
            'quiz_question_id' => $question->id,
            'option_text' => 'Indonesia',
            'match_text' => 'Jakarta',
        ]);
        $this->assertDatabaseHas('quiz_question_options', [
            'quiz_question_id' => $question->id,
            'option_text' => 'Jepang',
            'match_text' => 'Tokyo',
        ]);
    }

    public function test_guru_can_create_multiple_questions_simultaneously_batch(): void
    {
        $quiz = Quiz::create([
            'title' => 'Kuis Ujian Batch Lengkap',
            'duration_minutes' => 60,
            'points_per_question' => 25,
            'deadline' => now()->addDays(5),
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        $batchPayload = [
            'questions' => [
                // Soal 1: Pilihan Ganda
                [
                    'question_text' => 'Soal Batch 1: Ibukota Indonesia?',
                    'question_type' => 'multiple_choice',
                    'options' => ['Jakarta', 'Bandung', 'Surabaya', 'Semarang'],
                    'correct_option' => 0,
                ],
                // Soal 2: Benar / Salah
                [
                    'question_text' => 'Soal Batch 2: Air mendidih pada 100C.',
                    'question_type' => 'true_false',
                    'correct_tf' => 'Benar',
                ],
                // Soal 3: Menjodohkan
                [
                    'question_text' => 'Soal Batch 3: Jodohkan singkatan komputer.',
                    'question_type' => 'matching',
                    'pairs' => [
                        ['premise' => 'CPU', 'match' => 'Processor'],
                        ['premise' => 'RAM', 'match' => 'Memory'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->guru)->post(route('admin.quizzes.store-question', $quiz), $batchPayload);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        // Pastikan ketiga butir soal tersimpan dalam database untuk kuis ini
        $this->assertEquals(3, QuizQuestion::where('quiz_id', $quiz->id)->count());

        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_text' => 'Soal Batch 1: Ibukota Indonesia?',
            'question_type' => 'multiple_choice',
        ]);
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_text' => 'Soal Batch 2: Air mendidih pada 100C.',
            'question_type' => 'true_false',
        ]);
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_text' => 'Soal Batch 3: Jodohkan singkatan komputer.',
            'question_type' => 'matching',
        ]);
    }

    public function test_siswa_cannot_access_quiz_crud(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.quizzes.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_guru_can_view_quiz_student_results_and_reset_attempt(): void
    {
        $quiz = Quiz::create([
            'title' => 'Kuis Uji Reset',
            'duration_minutes' => 30,
            'points_per_question' => 10,
            'deadline' => now()->addDays(3),
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        $attempt = \App\Models\QuizAttempt::create([
            'student_id' => $this->siswa->id,
            'quiz_id' => $quiz->id,
            'score' => 80,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(5),
        ]);

        // Guru dapat membuka halaman hasil & status siswa
        $response = $this->actingAs($this->guru)->get(route('admin.quizzes.students', $quiz));
        $response->assertStatus(200);
        $response->assertSee($this->siswa->name);
        $response->assertSee('80');

        // Guru dapat melakukan reset pengerjaan siswa
        $resetResponse = $this->actingAs($this->guru)->delete(route('admin.quizzes.students.reset', [$quiz, $this->siswa]));
        $resetResponse->assertSessionHas('success');

        // Pastikan attempt terhapus dari database
        $this->assertDatabaseMissing('quiz_attempts', [
            'id' => $attempt->id,
        ]);
    }
}

