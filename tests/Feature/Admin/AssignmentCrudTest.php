<?php

namespace Tests\Feature\Admin;

use App\Events\AssignmentCreated;
use App\Models\Assignment;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AssignmentCrudTest extends TestCase
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

        $this->class = SchoolClass::create(['name' => 'Kelas XI IPA 2']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'class_id' => null,
            'nip' => '198101012005011002',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->class->id,
        ]);

        $this->subject = Subject::create([
            'name' => 'Biologi',
            'code' => 'BIO-11',
        ]);

        $this->unassignedSubject = Subject::create([
            'name' => 'Kimia',
            'code' => 'KIM-11',
        ]);

        $this->guru->subjects()->attach($this->subject->id);
    }

    public function test_guru_can_view_assignment_list(): void
    {
        $response = $this->actingAs($this->guru)->get(route('admin.assignments.index'));
        $response->assertStatus(200);
        $response->assertSee('Tugas Siswa');
    }

    public function test_guru_can_create_assignment_and_dispatches_event(): void
    {
        Event::fake();

        $bankItem = \App\Models\AssignmentBank::create([
            'title' => 'Tugas Struktur Sel',
            'description' => 'Kerjakan latihan soal halaman 45',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
        ]);

        $dueDate = now()->addDays(7)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->guru)->post(route('admin.assignments.store'), [
            'assignment_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'due_date' => $dueDate,
        ]);

        $response->assertRedirect(route('admin.assignments.index'));
        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Struktur Sel',
            'subject_id' => $this->subject->id,
            'class_id' => $this->class->id,
            'instructor_id' => $this->guru->id,
        ]);

        Event::assertDispatched(AssignmentCreated::class);
    }

    public function test_guru_cannot_create_assignment_for_unassigned_subject(): void
    {
        Event::fake();

        $bankItem = \App\Models\AssignmentBank::create([
            'title' => 'Tugas Reaksi Redoks',
            'description' => 'Jawablah soal berikut...',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->unassignedSubject->id,
        ]);

        $dueDate = now()->addDays(7)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->guru)->post(route('admin.assignments.store'), [
            'assignment_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'due_date' => $dueDate,
        ]);

        $response->assertSessionHasErrors(['assignment_bank_id']);
        $this->assertDatabaseMissing('assignments', [
            'title' => 'Tugas Reaksi Redoks',
        ]);

        Event::assertNotDispatched(AssignmentCreated::class);
    }

    public function test_guru_can_update_and_delete_assignment(): void
    {
        $assignment = new Assignment();
        $assignment->title = 'Tugas Awal';
        $assignment->description = 'Deskripsi awal';
        $assignment->due_date = now()->addDays(3);
        $assignment->subject_id = $this->subject->id;
        $assignment->class_id = $this->class->id;
        $assignment->instructor_id = $this->guru->id;
        $assignment->save();

        $bankItem = \App\Models\AssignmentBank::create([
            'title' => 'Tugas Revisi Terbaru',
            'description' => 'Deskripsi diperbarui',
            'instructor_id' => $this->guru->id,
            'subject_id' => $this->subject->id,
        ]);

        $newDueDate = now()->addDays(10)->format('Y-m-d H:i:s');

        $updateResponse = $this->actingAs($this->guru)->put(route('admin.assignments.update', $assignment), [
            'assignment_bank_id' => $bankItem->id,
            'class_id' => $this->class->id,
            'due_date' => $newDueDate,
        ]);

        $updateResponse->assertRedirect(route('admin.assignments.index'));
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Revisi Terbaru',
        ]);

        $deleteResponse = $this->actingAs($this->guru)->delete(route('admin.assignments.destroy', $assignment));
        $deleteResponse->assertRedirect(route('admin.assignments.index'));
        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);
    }

    public function test_siswa_cannot_access_assignment_crud(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.assignments.index'));
        $response->assertRedirect(route('dashboard'));
    }
}
