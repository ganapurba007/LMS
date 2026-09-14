<?php

namespace Tests\Feature\Admin;

use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionBankCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guru_can_view_question_bank_list(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $response = $this->actingAs($guru)->get('/admin/question-banks');

        $response->assertStatus(200);
    }

    public function test_guru_can_create_question_with_options_and_correct_answer(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $response = $this->actingAs($guru)->post('/admin/question-banks', [
            'question_text' => 'Berapakah 1 + 1?',
            'options' => ['1', '2', '3', '4'],
            'correct_option' => 1, // '2' adalah pilihan ke-1 (index 1)
        ]);

        $response->assertRedirect('/admin/question-banks');
        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'Berapakah 1 + 1?',
            'instructor_id' => $guru->id,
        ]);

        $qb = QuestionBank::where('question_text', 'Berapakah 1 + 1?')->first();
        $this->assertCount(4, $qb->options);

        $correctOption = $qb->options()->where('is_correct', true)->first();
        $this->assertEquals('2', $correctOption->option_text);
    }

    public function test_guru_can_update_question_and_options(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $qb = new QuestionBank(['question_text' => 'Soal Lama']);
        $qb->instructor_id = $guru->id;
        $qb->save();

        QuestionBankOption::create([
            'question_bank_id' => $qb->id,
            'option_text' => 'Opsi A',
            'is_correct' => true,
        ]);

        QuestionBankOption::create([
            'question_bank_id' => $qb->id,
            'option_text' => 'Opsi B',
            'is_correct' => false,
        ]);

        $response = $this->actingAs($guru)->put("/admin/question-banks/{$qb->id}", [
            'question_text' => 'Soal Baru',
            'options' => ['Opsi X', 'Opsi Y', 'Opsi Z'],
            'correct_option' => 2, // Opsi Z adalah index 2
        ]);

        $response->assertRedirect('/admin/question-banks');
        $this->assertDatabaseHas('question_bank', [
            'id' => $qb->id,
            'question_text' => 'Soal Baru',
        ]);

        $qb->refresh();
        $this->assertCount(3, $qb->options);

        $correctOption = $qb->options()->where('is_correct', true)->first();
        $this->assertEquals('Opsi Z', $correctOption->option_text);
    }

    public function test_guru_can_delete_question_bank_item(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $qb = new QuestionBank(['question_text' => 'Soal Hapus']);
        $qb->instructor_id = $guru->id;
        $qb->save();

        $response = $this->actingAs($guru)->delete("/admin/question-banks/{$qb->id}");

        $response->assertRedirect('/admin/question-banks');
        $this->assertDatabaseMissing('question_bank', ['id' => $qb->id]);
    }

    public function test_guru_can_create_multiple_questions_in_question_bank_batch(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $batchPayload = [
            'questions' => [
                [
                    'question_text' => 'QB Batch 1: Rumus luas persegi?',
                    'question_type' => 'multiple_choice',
                    'options' => ['s x s', 's + s', '4 x s', '2 x s'],
                    'correct_option' => 0,
                ],
                [
                    'question_text' => 'QB Batch 2: Segitiga memiliki 3 sisi.',
                    'question_type' => 'true_false',
                    'correct_tf' => 'Benar',
                ],
                [
                    'question_text' => 'QB Batch 3: Jodohkan bangun datar dengan cirinya.',
                    'question_type' => 'matching',
                    'pairs' => [
                        ['premise' => 'Lingkaran', 'match' => 'Tidak memiliki sudut'],
                        ['premise' => 'Persegi', 'match' => '4 sisi sama panjang'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($guru)->post('/admin/question-banks', $batchPayload);

        $response->assertRedirect('/admin/question-banks');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'QB Batch 1: Rumus luas persegi?',
            'instructor_id' => $guru->id,
            'question_type' => 'multiple_choice',
        ]);
        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'QB Batch 2: Segitiga memiliki 3 sisi.',
            'instructor_id' => $guru->id,
            'question_type' => 'true_false',
        ]);
        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'QB Batch 3: Jodohkan bangun datar dengan cirinya.',
            'instructor_id' => $guru->id,
            'question_type' => 'matching',
        ]);
    }

    public function test_siswa_cannot_access_question_bank_crud(): void
    {
        $siswa = User::where('email', 'siswa1@lms.com')->first();

        $response = $this->actingAs($siswa)->get('/admin/question-banks');

        $response->assertRedirect('/dashboard');
    }
}

