<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'siswa', 'display_name' => 'Siswa']);
        $class = SchoolClass::create(['name' => 'X-IPA-1']);

        $this->user = User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'class_id' => $class->id,
        ]);
    }

    public function test_user_can_view_notifications(): void
    {
        Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_material',
            'title' => 'Materi Baru',
            'message' => 'Konten materi telah ditambahkan',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
        $response->assertSee('Materi Baru');
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_assignment',
            'title' => 'Tugas Baru',
            'message' => 'Silakan kerjakan tugas ini',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', $notification->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_material',
            'title' => 'Notif 1',
            'message' => 'Message 1',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_quiz',
            'title' => 'Notif 2',
            'message' => 'Message 2',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, Notification::where('user_id', $this->user->id)->where('is_read', false)->count());
    }

    public function test_clicking_assignment_notification_redirects_directly_to_the_assignment_detail(): void
    {
        $subject = \App\Models\Subject::create(['code' => 'BIO01', 'name' => 'Biologi']);
        $assignment = \App\Models\Assignment::create([
            'title' => 'Tugas Praktikum Biologi',
            'description' => 'Amati struktur sel daun',
            'due_date' => now()->addDays(3),
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'instructor_id' => $this->user->id,
        ]);

        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_assignment',
            'title' => 'Tugas Baru Diterbitkan',
            'message' => 'Tugas praktikum biologi telah tersedia',
            'related_url' => 'http://localhost/student/assignments',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', $notification->id));

        // Harus langsung mengarahkan ke halaman detail tugas tersebut, bukan 404
        $response->assertRedirect(route('student.assignments.show', $assignment));
    }

    public function test_clicking_material_notification_redirects_directly_to_the_material_detail(): void
    {
        $subject = \App\Models\Subject::create(['code' => 'KIM01', 'name' => 'Kimia']);
        $material = \App\Models\Material::create([
            'title' => 'Struktur Atom & Sistem Periodik',
            'content_type' => 'text',
            'content' => 'Penjelasan struktur atom',
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'instructor_id' => $this->user->id,
        ]);

        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_material',
            'title' => 'Materi Baru: ' . $material->title,
            'message' => 'Materi pembelajaran baru telah ditambahkan',
            'related_url' => 'http://localhost/student/materials/' . $material->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', $notification->id));

        $response->assertRedirect(route('student.materials.show', $material));
    }

    public function test_clicking_quiz_notification_redirects_directly_to_the_quiz_detail(): void
    {
        $subject = \App\Models\Subject::create(['code' => 'MAT01', 'name' => 'Matematika']);
        $quiz = \App\Models\Quiz::create([
            'title' => 'Kuis Matriks & Vektor',
            'duration_minutes' => 30,
            'points_per_question' => 10,
            'deadline' => now()->addDays(2),
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'instructor_id' => $this->user->id,
        ]);

        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'new_quiz',
            'title' => 'Kuis Baru: ' . $quiz->title,
            'message' => 'Kuis online baru telah dibuka',
            'related_url' => 'http://localhost/student/quizzes/' . $quiz->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', $notification->id));

        $response->assertRedirect(route('student.quizzes.show', $quiz));
    }

    public function test_clicking_discussion_notification_redirects_to_discussion_list(): void
    {
        $subject = \App\Models\Subject::create(['code' => 'FIS01', 'name' => 'Fisika']);
        $material = \App\Models\Material::create([
            'title' => 'Hukum Newton I, II, III',
            'content_type' => 'text',
            'content' => 'Penjelasan hukum newton',
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'instructor_id' => $this->user->id,
        ]);

        $notification = Notification::create([
            'user_id' => $this->user->id,
            'type' => 'comment',
            'title' => 'Balasan Diskusi: ' . $material->title,
            'message' => 'Guru menanggapi pertanyaan Anda pada diskusi',
            'related_url' => 'http://localhost/student/materials/' . $material->id . '#discussion-list',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('notifications.mark-read', $notification->id));

        $response->assertRedirect(route('student.materials.show', $material) . '#discussion-list');
    }

    public function test_teacher_creating_material_creates_notification_for_students(): void
    {
        $roleGuru = Role::create(['name' => 'guru', 'display_name' => 'Guru']);
        $guru = User::create([
            'name' => 'Guru Kimia',
            'email' => 'guru.kimia@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'nip' => '198701012010011005',
        ]);
        $subject = \App\Models\Subject::create(['code' => 'KIM02', 'name' => 'Kimia 2']);
        $guru->subjects()->attach($subject->id);

        $response = $this->actingAs($guru)->post(route('admin.materials.store'), [
            'title' => 'Termokimia & Entalpi',
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'content_type' => 'text',
            'content' => 'Materi tentang perubahan entalpi reaksi',
        ]);

        $response->assertRedirect(route('admin.materials.index'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'new_material',
            'title' => 'Materi Baru: Termokimia & Entalpi',
        ]);
    }

    public function test_teacher_creating_quiz_creates_notification_for_students(): void
    {
        $roleGuru = Role::where('name', 'guru')->first() ?? Role::create(['name' => 'guru', 'display_name' => 'Guru']);
        $guru = User::create([
            'name' => 'Guru Fisika',
            'email' => 'guru.fisika@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'nip' => '198701012010011006',
        ]);
        $subject = \App\Models\Subject::create(['code' => 'FIS02', 'name' => 'Fisika 2']);
        $guru->subjects()->attach($subject->id);

        $response = $this->actingAs($guru)->post(route('admin.quizzes.store'), [
            'title' => 'Kuis Gelombang Elektromagnetik',
            'duration_minutes' => 45,
            'points_per_question' => 10,
            'deadline' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'new_quiz',
            'title' => 'Kuis Baru: Kuis Gelombang Elektromagnetik',
        ]);
    }

    public function test_posting_discussion_comment_creates_notification_for_teacher_and_participants(): void
    {
        $roleGuru = Role::where('name', 'guru')->first() ?? Role::create(['name' => 'guru', 'display_name' => 'Guru']);
        $guru = User::create([
            'name' => 'Guru Biologi',
            'email' => 'guru.biologi@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'nip' => '198701012010011007',
        ]);
        $subject = \App\Models\Subject::create(['code' => 'BIO02', 'name' => 'Biologi 2']);

        $material = \App\Models\Material::create([
            'title' => 'Sistem Ekskresi Manusia',
            'content_type' => 'text',
            'content' => 'Penjelasan fungsi ginjal',
            'subject_id' => $subject->id,
            'class_id' => $this->user->class_id,
            'instructor_id' => $guru->id,
        ]);

        // Siswa 1 (this->user) mengirim komentar pertama
        $this->actingAs($this->user)->post(route('student.materials.discussions', $material), [
            'comment' => 'Pak, apa fungsi nefron pada ginjal?',
        ]);

        // Guru harus menerima notifikasi pertanyaan siswa
        $this->assertDatabaseHas('notifications', [
            'user_id' => $guru->id,
            'type' => 'comment',
            'title' => 'Diskusi Baru: Sistem Ekskresi Manusia',
        ]);

        // Sekarang Guru membalas komentar tersebut
        $this->actingAs($guru)->post(route('student.materials.discussions', $material), [
            'comment' => 'Nefron berfungsi menyaring darah dan membentuk urin.',
        ]);

        // Siswa 1 harus menerima notifikasi balasan guru
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'comment',
            'title' => 'Balasan Guru: Sistem Ekskresi Manusia',
        ]);
    }
}
