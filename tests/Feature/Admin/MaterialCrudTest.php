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

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'title' => 'Pengenalan Aljabar',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'content_type' => 'text',
            'content' => 'Aljabar adalah cabang matematika...',
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

        $file = UploadedFile::fake()->create('modul_aljabar.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'title' => 'Modul PDF Aljabar',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'content_type' => 'document',
            'document_file' => $file,
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

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'title' => 'Video Pembelajaran Aljabar',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'content_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
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

        $response = $this->actingAs($this->guru)->post(route('admin.materials.store'), [
            'title' => 'Fisika Dasar',
            'subject_id' => $this->unassignedSubject->id,
            'class_id' => $this->class->id,
            'content_type' => 'text',
            'content' => 'Fisika adalah...',
        ]);

        $response->assertSessionHasErrors(['subject_id']);
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

        $updateResponse = $this->actingAs($this->guru)->put(route('admin.materials.update', $material), [
            'title' => 'Judul Baru Update',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'content_type' => 'text',
            'content' => 'Isi materi baru',
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
