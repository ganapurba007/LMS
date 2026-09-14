<?php

namespace Tests\Feature\Student;

use App\Models\Assignment;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected User $siswa;
    protected SchoolClass $classA;
    protected SchoolClass $classB;
    protected Subject $subject;
    protected Assignment $assignmentClassA;
    protected Assignment $assignmentClassB;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGuru = Role::create(['name' => 'guru']);
        $roleSiswa = Role::create(['name' => 'siswa']);

        $this->classA = SchoolClass::create(['name' => 'Kelas X IPA 1']);
        $this->classB = SchoolClass::create(['name' => 'Kelas X IPA 2']);

        $this->guru = User::factory()->create([
            'role_id' => $roleGuru->id,
            'nip' => '198601012005011007',
        ]);

        $this->siswa = User::factory()->create([
            'role_id' => $roleSiswa->id,
            'class_id' => $this->classA->id,
        ]);

        $this->subject = Subject::create([
            'name' => 'Kimia Dasar',
            'code' => 'KIM-10',
        ]);

        $this->assignmentClassA = new Assignment();
        $this->assignmentClassA->title = 'Tugas Tabel Periodik';
        $this->assignmentClassA->description = 'Sebutkan 10 unsur golongan 1A';
        $this->assignmentClassA->due_date = now()->addDays(5);
        $this->assignmentClassA->subject_id = $this->subject->id;
        $this->assignmentClassA->class_id = $this->classA->id;
        $this->assignmentClassA->instructor_id = $this->guru->id;
        $this->assignmentClassA->save();

        $this->assignmentClassB = new Assignment();
        $this->assignmentClassB->title = 'Tugas Ikatan Kimia';
        $this->assignmentClassB->description = 'Jelaskan ikatan kovalen';
        $this->assignmentClassB->due_date = now()->addDays(5);
        $this->assignmentClassB->subject_id = $this->subject->id;
        $this->assignmentClassB->class_id = $this->classB->id;
        $this->assignmentClassB->instructor_id = $this->guru->id;
        $this->assignmentClassB->save();
    }

    public function test_siswa_can_view_assignments_for_their_class(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.assignments.index'));
        $response->assertStatus(200);
        $response->assertSee('Tugas Tabel Periodik');
        $response->assertDontSee('Tugas Ikatan Kimia');
    }

    public function test_siswa_can_view_assignment_detail_and_submit_answer(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.assignments.show', $this->assignmentClassA));
        $response->assertStatus(200);
        $response->assertSee('Tugas Tabel Periodik');

        $submitResponse = $this->actingAs($this->siswa)->post(route('student.assignments.submit', $this->assignmentClassA), [
            'answer_text' => '1. Hidrogen, 2. Litium, 3. Natrium...',
        ]);

        $submitResponse->assertRedirect(route('student.assignments.show', $this->assignmentClassA));
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $this->assignmentClassA->id,
            'student_id' => $this->siswa->id,
            'answer_text' => '1. Hidrogen, 2. Litium, 3. Natrium...',
        ]);
    }

    public function test_siswa_cannot_access_assignment_from_another_class(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('student.assignments.show', $this->assignmentClassB));
        $response->assertStatus(403);
    }

    public function test_siswa_cannot_submit_when_assignment_is_overdue(): void
    {
        $this->assignmentClassA->due_date = now()->subDays(1);
        $this->assignmentClassA->save();

        $response = $this->actingAs($this->siswa)->post(route('student.assignments.submit', $this->assignmentClassA), [
            'answer_text' => 'Jawaban setelah batas waktu lewat',
        ]);

        $response->assertRedirect(route('student.assignments.show', $this->assignmentClassA));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $this->assignmentClassA->id,
            'student_id' => $this->siswa->id,
            'answer_text' => 'Jawaban setelah batas waktu lewat',
        ]);
    }

    public function test_siswa_cannot_submit_assignment_more_than_once(): void
    {
        // Pengumpulan pertama (berhasil)
        $firstSubmit = $this->actingAs($this->siswa)->post(route('student.assignments.submit', $this->assignmentClassA), [
            'answer_text' => 'Jawaban pertama siswa',
        ]);
        $firstSubmit->assertRedirect(route('student.assignments.show', $this->assignmentClassA));
        $firstSubmit->assertSessionHas('success');

        // Pengumpulan kedua (harus ditolak karena hanya 1 kali)
        $secondSubmit = $this->actingAs($this->siswa)->post(route('student.assignments.submit', $this->assignmentClassA), [
            'answer_text' => 'Mencoba kirim jawaban kedua',
        ]);
        $secondSubmit->assertRedirect(route('student.assignments.show', $this->assignmentClassA));
        $secondSubmit->assertSessionHas('error');

        // Pastikan jawaban di database tidak berubah menjadi jawaban kedua
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $this->assignmentClassA->id,
            'student_id' => $this->siswa->id,
            'answer_text' => 'Jawaban pertama siswa',
        ]);
        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $this->assignmentClassA->id,
            'student_id' => $this->siswa->id,
            'answer_text' => 'Mencoba kirim jawaban kedua',
        ]);
    }
}
