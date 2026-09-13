<?php

namespace Tests\Feature\Student;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_self_report(): void
    {
        $role = Role::create(['name' => 'siswa', 'display_name' => 'Siswa']);
        $class = SchoolClass::create(['name' => 'Kelas X', 'code' => 'X-1']);
        $student = User::factory()->create([
            'role_id' => $role->id,
            'class_id' => $class->id,
        ]);

        $response = $this->actingAs($student)->get(route('student.report.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Progres Belajar Diri');
        $response->assertSee('Progres Materi');
        $response->assertSee('Rata-Rata Tugas');
        $response->assertSee('Rata-Rata Kuis');
    }

    public function test_student_can_view_subject_breakdown_and_evaluations_history(): void
    {
        $role = Role::create(['name' => 'siswa', 'display_name' => 'Siswa']);
        $class = SchoolClass::create(['name' => 'Kelas X IPA', 'code' => 'X-IPA']);
        $student = User::factory()->create([
            'role_id' => $role->id,
            'class_id' => $class->id,
        ]);

        $subject = \App\Models\Subject::create([
            'code' => 'BIO01',
            'name' => 'Biologi Modern',
        ]);

        $assignment = new \App\Models\Assignment([
            'title' => 'Tugas Struktur Sel',
            'description' => 'Kerjakan tugas sel',
            'due_date' => now()->addDays(3),
        ]);
        $assignment->class_id = $class->id;
        $assignment->subject_id = $subject->id;
        $assignment->instructor_id = $student->id;
        $assignment->save();

        \App\Models\AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'answer_text' => 'Ini jawaban tugas sel',
            'grade' => 90,
            'submitted_at' => now(),
        ]);

        $quiz = new \App\Models\Quiz([
            'title' => 'Kuis Genetika',
            'duration_minutes' => 30,
            'points_per_question' => 100,
            'deadline' => now()->addDays(7),
        ]);
        $quiz->class_id = $class->id;
        $quiz->subject_id = $subject->id;
        $quiz->instructor_id = $student->id;
        $quiz->save();

        \App\Models\QuizAttempt::create([
            'student_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 85,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('student.report.index'));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Performa per Mata Pelajaran');
        $response->assertSee('Biologi Modern');
        $response->assertSee('Tugas Struktur Sel');
        $response->assertSee('Kuis Genetika');
        $response->assertSee('90 / 100');
        $response->assertSee('85 / 100');
    }
}

