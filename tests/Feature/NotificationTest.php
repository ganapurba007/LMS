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
}
