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

    public function test_guru_can_create_and_update_five_options_multiple_choice_question(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        // 1. Create with 5 options (A, B, C, D, E) where E (index 4) is correct
        $response = $this->actingAs($guru)->post('/admin/question-banks', [
            'question_text' => 'Berapa jumlah propinsi di pulau Jawa?',
            'options' => ['4', '5', '6', '7', '8'],
            'correct_option' => 4, // '8' (Opsi E)
        ]);

        $response->assertRedirect('/admin/question-banks');
        $qb = QuestionBank::where('question_text', 'Berapa jumlah propinsi di pulau Jawa?')->first();
        $this->assertNotNull($qb);
        $this->assertCount(5, $qb->options);

        $correctOption = $qb->options()->where('is_correct', true)->first();
        $this->assertEquals('8', $correctOption->option_text);

        // 2. Update with 5 options (A, B, C, D, E) where D (index 3) is correct
        $updateResponse = $this->actingAs($guru)->put("/admin/question-banks/{$qb->id}", [
            'question_text' => 'Soal 5 Opsi Diperbarui',
            'options' => ['Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E'],
            'correct_option' => 3, // Opsi D
        ]);

        $updateResponse->assertRedirect('/admin/question-banks');
        $qb->refresh();
        $this->assertEquals('Soal 5 Opsi Diperbarui', $qb->question_text);
        $this->assertCount(5, $qb->options);

        $updatedCorrect = $qb->options()->where('is_correct', true)->first();
        $this->assertEquals('Opsi D', $updatedCorrect->option_text);
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

    public function test_guru_can_parse_questions_from_uploaded_document(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $content = "1. Apa ibukota Indonesia?\nA. Jakarta\nB. Surabaya\nC. Bandung\nD. Medan\nKunci: A\n\n2. Matahari terbit dari timur.\nKunci: Benar";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('soal.txt', $content);

        $response = $this->actingAs($guru)->postJson('/admin/question-banks/parse-document', [
            'document_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);
        $response->assertJsonPath('questions.0.question_text', 'Apa ibukota Indonesia?');
        $response->assertJsonPath('questions.0.correct_option', 0);
        $response->assertJsonPath('questions.1.question_type', 'true_false');
        $response->assertJsonPath('questions.1.correct_tf', 'Benar');
    }

    public function test_guru_can_upload_editor_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $guru = User::where('email', 'guru@lms.com')->first();

        $image = \Illuminate\Http\UploadedFile::fake()->image('gambar_soal.png', 300, 300);

        $response = $this->actingAs($guru)->postJson('/admin/upload-editor-image', [
            'file' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['location', 'url', 'success']);
    }

    public function test_guru_can_import_and_directly_save_document_to_question_bank(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $content = "1. Apa kepanjangan dari PHP?\nA. Hypertext Preprocessor\nB. Personal Home Page\nC. Private Hosting Protocol\nD. Preprocessed Hypertext\nKunci: A\n\n2. Bumi itu bulat.\nKunci: Benar";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('naskah_soal.txt', $content);

        $response = $this->actingAs($guru)->post('/admin/question-banks/import-document', [
            'document_file' => $file,
        ]);

        $response->assertRedirect('/admin/question-banks');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'Apa kepanjangan dari PHP?',
            'instructor_id' => $guru->id,
            'question_type' => 'multiple_choice',
        ]);

        $this->assertDatabaseHas('question_bank', [
            'question_text' => 'Bumi itu bulat.',
            'instructor_id' => $guru->id,
            'question_type' => 'true_false',
        ]);
    }

    public function test_guru_can_download_question_template_docx_and_txt(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        // Download docx template
        $responseDocx = $this->actingAs($guru)->get('/admin/question-banks/download-template?format=docx');
        $responseDocx->assertStatus(200);
        $responseDocx->assertHeader('content-disposition', 'attachment; filename=Template_Format_Soal_LMS.docx');

        // Download txt template
        $responseTxt = $this->actingAs($guru)->get('/admin/question-banks/download-template?format=txt');
        $responseTxt->assertStatus(200);
        $responseTxt->assertHeader('content-disposition', 'attachment; filename="Template_Format_Soal_LMS.txt"');
        $this->assertStringContainsString('FORMAT PENULISAN NASKAH SOAL KUIS', $responseTxt->getContent());
    }

    public function test_guru_can_import_markdown_document_with_bulleted_options_and_5_options(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        $content = "1. Sumber daya alam merupakan segala sesuatu yang berasal dari alam dan dapat dimanfaatkan untuk memenuhi kebutuhan manusia. Berdasarkan sifatnya, minyak bumi, batu bara, dan gas alam termasuk sumber daya alam ....\n\n   - A. dapat diperbarui\n\n   - B. tidak dapat diperbarui\n\n   - C. tidak dapat dimanfaatkan\n\n   - D. hayati\n\n   - E. permanen\n\nKunci B\n\n2. Perhatikan kondisi berikut!\n\nSebuah daerah mengalami penebangan hutan secara berlebihan.\n\nHubungan yang paling tepat antara pemanfaatan SDA dengan permasalahan tersebut adalah ....\n\n   - A. penebangan hutan meningkatkan kemampuan tanah menyerap air B. berkurangnya hutan dapat mengurangi fungsi vegetasi dalam menahan air dan tanah\n\n   - C. banjir terjadi karena jumlah penduduk berkurang\n\n   - D. tanah longsor hanya disebabkan oleh curah hujan\n\n   - E. hutan tidak memiliki hubungan\n\n   - Kunci B";
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('Template_Format_Soal_LMS.md', $content);

        $response = $this->actingAs($guru)->post('/admin/question-banks/import-document', [
            'document_file' => $file,
        ]);

        $response->assertRedirect('/admin/question-banks');
        $response->assertSessionHas('success');

        // Pastikan 2 butir soal berhasil masuk ke database
        $this->assertEquals(2, \App\Models\QuestionBank::where('instructor_id', $guru->id)->count());

        // Verifikasi soal #1, #2 (inline options), #8 (trailing key), dan #10
        $this->assertDatabaseHas('question_bank', [
            'instructor_id' => $guru->id,
            'question_type' => 'multiple_choice',
        ]);

        $q1 = \App\Models\QuestionBank::where('instructor_id', $guru->id)->where('question_text', 'like', '%Sumber daya alam%')->first();
        $this->assertNotNull($q1);
        $this->assertCount(5, $q1->options);
        $this->assertTrue($q1->options[1]->is_correct); // Kunci B

        $q2 = \App\Models\QuestionBank::where('instructor_id', $guru->id)->where('question_text', 'like', '%Hubungan yang paling tepat antara pemanfaatan SDA%')->first();
        $this->assertNotNull($q2);
        $this->assertCount(5, $q2->options);
        $this->assertTrue($q2->options[1]->is_correct); // Kunci B
    }

    public function test_guru_can_import_docx_document_with_word_numbering_and_inline_options(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();
        $docxPath = base_path('Template_Format_Soal_LMS.docx');

        if (!file_exists($docxPath)) {
            $this->markTestSkipped('File Template_Format_Soal_LMS.docx tidak ditemukan.');
        }

        $file = new \Illuminate\Http\UploadedFile($docxPath, 'Template_Format_Soal_LMS.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);

        $response = $this->actingAs($guru)->post('/admin/question-banks/import-document', [
            'document_file' => $file,
        ]);

        $response->assertRedirect('/admin/question-banks');
        $response->assertSessionHas('success');

        // Pastikan seluruh 10 butir soal dari Word docx berhasil masuk ke database
        $this->assertEquals(10, \App\Models\QuestionBank::where('instructor_id', $guru->id)->count());

        $q1 = \App\Models\QuestionBank::where('instructor_id', $guru->id)->where('question_text', 'like', '%Sumber daya alam%')->first();
        $this->assertNotNull($q1);
        $this->assertCount(5, $q1->options);
        $this->assertTrue($q1->options[1]->is_correct); // Kunci B
    }

    public function test_guru_views_paginated_question_bank_list(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        // Buat 25 butir soal (dengan pagination 10 per halaman, total 3 halaman)
        for ($i = 1; $i <= 25; $i++) {
            $qb = QuestionBank::create([
                'instructor_id' => $guru->id,
                'question_type' => 'multiple_choice',
                'question_text' => "Pertanyaan butir nomor {$i}",
            ]);
            QuestionBankOption::create([
                'question_bank_id' => $qb->id,
                'option_text' => 'Pilihan Jawaban',
                'is_correct' => true,
            ]);
        }

        $response = $this->actingAs($guru)->get('/admin/question-banks');

        $response->assertStatus(200);
        $response->assertViewHas('questionBanks');

        $paginator = $response->viewData('questionBanks');
        $this->assertEquals(25, $paginator->total());
        $this->assertEquals(10, $paginator->perPage());
        $this->assertEquals(3, $paginator->lastPage());
        $this->assertEquals(1, $paginator->currentPage());

        $response->assertSee('Pertanyaan butir nomor 1');
        $response->assertSee('Pertanyaan butir nomor 10');
    }

    public function test_guru_views_paginated_question_bank_with_images_on_page_3(): void
    {
        $guru = User::where('email', 'guru@lms.com')->first();

        // Create 25 questions, some with markdown images and html images
        for ($i = 1; $i <= 25; $i++) {
            $imageSnippet = ($i % 2 === 0) ? "\n\n![Diagram Soal](/storage/question-images/diagram_{$i}.png)" : '';
            $qb = QuestionBank::create([
                'instructor_id' => $guru->id,
                'question_type' => 'multiple_choice',
                'question_text' => "Pertanyaan butir nomor {$i}" . $imageSnippet,
            ]);
            QuestionBankOption::create([
                'question_bank_id' => $qb->id,
                'option_text' => 'Pilihan Jawaban',
                'is_correct' => true,
            ]);
        }

        $responsePage3 = $this->actingAs($guru)->get('/admin/question-banks?page=3');
        $responsePage3->assertStatus(200);
        $responsePage3->assertSee('Pertanyaan butir nomor 21');
        $responsePage3->assertSee('(gambar)');
    }
}

