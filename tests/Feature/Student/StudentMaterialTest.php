<?php

namespace Tests\Feature\Student;

use App\Events\DiscussionCommentSent;
use App\Models\Material;
use App\Models\MaterialDiscussion;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StudentMaterialTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected User $siswa;
    protected SchoolClass $classA;
    protected SchoolClass $classB;
    protected Subject $subject;
    protected Material $materialClassA;
    protected Material $materialClassB;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::create(['name' => 'guru']);
        $roleSiswa = Role::create(['name' => 'siswa']);

        $this->classA = SchoolClass::create(['name' => 'Kelas X IPA 1']);
        $this->classB = SchoolClass::create(['name' => 'Kelas X IPA 2']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'nip' => '198501012005011006',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->classA->id,
        ]);

        $this->subject = Subject::create([
            'name' => 'Fisika Dasar',
            'code' => 'FIS-10',
        ]);

        $this->materialClassA = new Material();
        $this->materialClassA->title = 'Hukum Newton I';
        $this->materialClassA->content_type = 'text';
        $this->materialClassA->content = 'Setiap benda akan tetap diam...';
        $this->materialClassA->subject_id = $this->subject->id;
        $this->materialClassA->class_id = $this->classA->id;
        $this->materialClassA->instructor_id = $this->guru->id;
        $this->materialClassA->save();

        $this->materialClassB = new Material();
        $this->materialClassB->title = 'Hukum Newton II';
        $this->materialClassB->content_type = 'text';
        $this->materialClassB->content = 'Percepatan sebuah benda...';
        $this->materialClassB->subject_id = $this->subject->id;
        $this->materialClassB->class_id = $this->classB->id;
        $this->materialClassB->instructor_id = $this->guru->id;
        $this->materialClassB->save();
    }

    public function test_siswa_can_view_materials_for_their_class(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.materials.index'));
        $response->assertStatus(200);
        $response->assertSee('Hukum Newton I');
        $response->assertDontSee('Hukum Newton II');
    }

    public function test_siswa_can_view_material_detail_and_toggle_complete(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.materials.show', $this->materialClassA));
        $response->assertStatus(200);
        $response->assertSee('Hukum Newton I');

        $completeResponse = $this->actingAs($this->siswa)->post(route('student.materials.complete', $this->materialClassA));
        $completeResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('material_progress', [
            'user_id' => $this->siswa->id,
            'material_id' => $this->materialClassA->id,
            'is_completed' => true,
        ]);
    }

    public function test_siswa_can_post_discussion_comment_and_dispatches_event(): void
    {
        Event::fake();

        $response = $this->actingAs($this->siswa)->post(route('student.materials.discussions', $this->materialClassA), [
            'comment' => 'Bagaimana penerapannya dalam kehidupan sehari-hari?',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('material_discussions', [
            'material_id' => $this->materialClassA->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Bagaimana penerapannya dalam kehidupan sehari-hari?',
        ]);

        Event::assertDispatched(DiscussionCommentSent::class);
    }

    public function test_user_can_reply_to_existing_discussion_comment_and_creates_nested_thread(): void
    {
        $parent = MaterialDiscussion::create([
            'material_id' => $this->materialClassA->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Pertanyaan awal tentang rumus Newton.',
        ]);

        $response = $this->actingAs($this->guru)->post(route('student.materials.discussions', $this->materialClassA), [
            'parent_id' => $parent->id,
            'comment' => 'Penjelasan balasan: Gunakan rumus F = m * a.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('material_discussions', [
            'material_id' => $this->materialClassA->id,
            'parent_id' => $parent->id,
            'user_id' => $this->guru->id,
            'comment' => 'Penjelasan balasan: Gunakan rumus F = m * a.',
        ]);

        // Verifikasi notifikasi terkirim kepada pembuat komentar induk
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->siswa->id,
            'type' => 'comment',
        ]);
    }

    public function test_material_detail_displays_nested_reply_indented_with_indonesian_relative_time(): void
    {
        $parent = MaterialDiscussion::create([
            'material_id' => $this->materialClassA->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Apakah ada tugas praktikum?',
        ]);
        $parent->forceFill(['created_at' => now()->subMinutes(15)])->save();

        $reply = MaterialDiscussion::create([
            'material_id' => $this->materialClassA->id,
            'parent_id' => $parent->id,
            'user_id' => $this->guru->id,
            'comment' => 'Tidak ada praktikum, cukup kerjakan kuis.',
        ]);
        $reply->forceFill(['created_at' => now()->subMinutes(5)])->save();

        $response = $this->actingAs($this->siswa)->get(route('student.materials.show', $this->materialClassA));
        $response->assertStatus(200);
        $response->assertSee('Apakah ada tugas praktikum?');
        $response->assertSee('Tidak ada praktikum, cukup kerjakan kuis.');
        $response->assertSee('discussion-replies');
        $response->assertSee('menit yang lalu');
        $response->assertSee('Balas');
    }

    public function test_siswa_cannot_access_material_from_another_class(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.materials.show', $this->materialClassB));
        $response->assertStatus(403);
    }

    public function test_guru_cannot_toggle_material_completion(): void
    {
        $response = $this->actingAs($this->guru)->post(route('student.materials.complete', $this->materialClassA));
        $response->assertSessionHas('error', 'Guru tidak dapat menandai penyelesaian materi.');
        $this->assertDatabaseMissing('material_progress', [
            'user_id' => $this->guru->id,
            'material_id' => $this->materialClassA->id,
        ]);
    }

    public function test_guru_cannot_post_top_level_discussion_comment(): void
    {
        $response = $this->actingAs($this->guru)->post(route('student.materials.discussions', $this->materialClassA), [
            'comment' => 'Komentar awal dari guru',
        ]);
        $response->assertSessionHas('error', 'Guru hanya dapat membalas komentar diskusi yang sudah ada.');
        $this->assertDatabaseMissing('material_discussions', [
            'material_id' => $this->materialClassA->id,
            'user_id' => $this->guru->id,
            'comment' => 'Komentar awal dari guru',
        ]);
    }

    public function test_guru_can_delete_discussion_comment(): void
    {
        $comment = MaterialDiscussion::create([
            'material_id' => $this->materialClassA->id,
            'user_id' => $this->siswa->id,
            'comment' => 'Komentar yang akan dihapus guru',
        ]);

        $response = $this->actingAs($this->guru)->delete(route('student.materials.discussions.destroy', [$this->materialClassA, $comment]));
        $response->assertSessionHas('success', 'Komentar diskusi berhasil dihapus.');
        $this->assertDatabaseMissing('material_discussions', [
            'id' => $comment->id,
        ]);
    }
}
