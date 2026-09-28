<?php

namespace Tests\Feature\Admin;

use App\Events\MaterialCreated;
use App\Models\Material;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialCrudTest extends TestCase
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

        $this->class = SchoolClass::create(['name' => 'Kelas X IPA 1']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'class_id' => null,
            'nip' => '198001012005011001',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->class->id,
        ]);

        $this->subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MAT-10',
        ]);

        $this->unassignedSubject = Subject::create([
            'name' => 'Fisika',
            'code' => 'FIS-10',
        ]);

        // Attach subject to guru
        $this->guru->subjects()->attach($this->subject->id);
    }

    public function test_guru_can_view_material_list(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.materials.index'));
        $response->assertStatus(200);
        $response->assertSee('Materi Pembelajaran');
    }

    public function test_guru_can_create_text_material_and_dispatches_event(): void
    {
        Event::fake();

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Pengenalan Aljabar',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
            'content_type' => 'text',
            'content' => 'Aljabar adalah cabang matematika...',
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'material_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.materials.index'));
        $this->assertDatabaseHas('materials', [
            'title' => 'Pengenalan Aljabar',
            'content_type' => 'text',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        Event::assertDispatched(MaterialCreated::class);
    }

    public function test_guru_can_create_document_material(): void
    {
        Event::fake();
        Storage::fake('public');

        $docPath = 'material-banks/modul_aljabar.pdf';
        Storage::disk('public')->put($docPath, 'PDF content');

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Modul PDF Aljabar',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
            'content_type' => 'document',
            'document_path' => $docPath,
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'material_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'order' => 2,
        ]);

        $response->assertRedirect(route('admin.materials.index'));

        $material = Material::where('title', 'Modul PDF Aljabar')->first();
        $this->assertNotNull($material);
        $this->assertNotNull($material->document_path);
        Storage::disk('public')->assertExists($material->document_path);
    }

    public function test_guru_can_create_youtube_material(): void
    {
        Event::fake();

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Video Pembelajaran Aljabar',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
            'content_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'material_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'order' => 3,
        ]);

        $response->assertRedirect(route('admin.materials.index'));
        $this->assertDatabaseHas('materials', [
            'title' => 'Video Pembelajaran Aljabar',
            'content_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function test_guru_cannot_create_material_for_unassigned_subject(): void
    {
        Event::fake();

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Fisika Dasar',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->unassignedSubject->id,
            'content_type' => 'text',
            'content' => 'Fisika adalah...',
        ]);

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'material_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
        ]);

        $response->assertSessionHasErrors(['material_bank_id']);
        $this->assertDatabaseMissing('materials', [
            'title' => 'Fisika Dasar',
        ]);

        Event::assertNotDispatched(MaterialCreated::class);
    }

    public function test_guru_can_update_and_delete_material(): void
    {
        $material = new Material();
        $material->title = 'Judul Lama';
        $material->content_type = 'text';
        $material->content = 'Isi lama';
        $material->subject_id = $this->subject->id;
        $material->class_id = $this->class->id;
        $material->instructor_id = $this->guru->id;
        $material->save();

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Judul Baru Update',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
            'content_type' => 'text',
            'content' => 'Isi materi baru',
        ]);

        $updateResponse = $this->actingAs($this->guru)->put(route('admin.materials.update', $material), [
            'material_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'order' => 5,
        ]);

        $updateResponse->assertRedirect(route('admin.materials.index'));
        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'title' => 'Judul Baru Update',
        ]);

        $deleteResponse = $this->actingAs($this->guru)->delete(route('admin.materials.destroy', $material));
        $deleteResponse->assertRedirect(route('admin.materials.index'));
        $this->assertDatabaseMissing('materials', [
            'id' => $material->id,
        ]);
    }

    public function test_guru_can_download_material_document(): void
    {
        Storage::fake('public');
        $filePath = 'materials/admin_doc_test.pdf';
        Storage::disk('public')->put($filePath, 'PDF doc');

        $material = Material::create([
            'title' => 'Modul Geometri',
            'content_type' => 'document',
            'document_path' => $filePath,
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
            'order' => 1,
        ]);

        $response = $this->actingAs($this->guru)->get(route('admin.materials.download', $material));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Modul_Geometri.pdf');
    }

    public function test_guru_can_preview_and_download_material_bank_document(): void
    {
        Storage::fake('public');
        $filePath = 'material-banks/doc_bank_test.pdf';
        Storage::disk('public')->put($filePath, 'PDF doc content');

        $bankItem = \App\Models\MaterialBank::create([
            'title' => 'Master Modul Bank',
            'content_type' => 'document',
            'document_path' => $filePath,
            'subject_id' => $this->subject->id,
            'instructor_id' => $this->guru->id,
        ]);

        $previewResponse = $this->actingAs($this->guru)->get(route('admin.material-banks.preview-file', $bankItem));
        $previewResponse->assertStatus(200);
        $previewResponse->assertHeader('content-type', 'application/pdf');

        $downloadResponse = $this->actingAs($this->guru)->get(route('admin.material-banks.download', $bankItem));
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('content-disposition', 'attachment; filename=Master_Modul_Bank.pdf');
    }

    public function test_siswa_cannot_access_material_crud(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.materials.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_guru_can_view_material_show_page_and_discussions(): void
    {
        $material = Material::create([
            'title' => 'Materi Diskusi Guru',
            'content_type' => 'text',
            'content' => 'Silakan diskusikan materi ini bersama.',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
            'order' => 1,
        ]);

        \App\Models\MaterialDiscussion::create([
            'material_id' => $material->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Pak guru, saya ingin bertanya tentang konsep aljabar ini.',
        ]);

        $response = $this->actingAs($this->guru)->get(route('admin.materials.show', $material));
        $response->assertStatus(200);
        $response->assertSee('Materi Diskusi Guru');
        $response->assertSee('Ruang Diskusi &amp; Tanya Jawab', false);
        $response->assertSee('Pak guru, saya ingin bertanya tentang konsep aljabar ini.');
    }

    public function test_guru_can_reply_to_student_discussion_comment_and_dispatches_notification(): void
    {
        Event::fake([\App\Events\DiscussionCommentSent::class]);

        $material = Material::create([
            'title' => 'Materi Aljabar Lanjutan',
            'content_type' => 'text',
            'content' => 'Materi aljabar lanjutan.',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
            'order' => 1,
        ]);

        $studentComment = \App\Models\MaterialDiscussion::create([
            'material_id' => $material->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Apakah rumus ini berlaku untuk semua variabel?',
        ]);

        $replyResponse = $this->actingAs($this->guru)->post(route('admin.materials.discussions', $material), [
            'comment' => 'Benar, rumus ini berlaku umum untuk semua variabel real.',
            'parent_id' => $studentComment->id,
        ]);

        $replyResponse->assertRedirect(route('admin.materials.show', $material));

        $this->assertDatabaseHas('material_discussions', [
            'material_id' => $material->id,
            'parent_id' => $studentComment->id,
            'user_id' => $this->guru->id,
            'comment' => 'Benar, rumus ini berlaku umum untuk semua variabel real.',
        ]);

        Event::assertDispatched(\App\Events\DiscussionCommentSent::class);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->siswa->id,
            'type' => 'comment',
            'title' => 'Balasan Guru: ' . $material->title,
        ]);
    }

    public function test_guru_can_delete_discussion_comment(): void
    {
        $material = Material::create([
            'title' => 'Materi Diskusi Moderasi',
            'content_type' => 'text',
            'content' => 'Materi untuk pengujian moderasi.',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
            'order' => 1,
        ]);

        $studentComment = \App\Models\MaterialDiscussion::create([
            'material_id' => $material->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Komentar spam yang melanggar aturan.',
        ]);

        $deleteResponse = $this->actingAs($this->guru)->delete(
            route('admin.materials.discussions.destroy', [$material, $studentComment])
        );

        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('material_discussions', [
            'id' => $studentComment->id,
        ]);
    }
}
