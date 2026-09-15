<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::all();
        $subjects = Subject::all();

        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedSubjectId = $request->get('subject_id');

        $studentsQuery = User::whereHas('role', function ($q) {
            $q->where('name', 'siswa');
        });

        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }

        $students = $studentsQuery->with(['schoolClass'])->get();

        // Calculate metrics for each student
        $totalMaterials = Material::when($selectedClassId, fn($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedSubjectId, fn($q) => $q->where('subject_id', $selectedSubjectId))
            ->count();

        foreach ($students as $student) {
            $completedMaterials = MaterialProgress::where('user_id', $student->id)
                ->where('is_completed', true)
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('material', fn($m) => $m->where('subject_id', $selectedSubjectId));
                })
                ->count();

            $student->materials_percentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;

            $avgAssignment = AssignmentSubmission::where('student_id', $student->id)
                ->whereNotNull('grade')
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('assignment', fn($a) => $a->where('subject_id', $selectedSubjectId));
                })
                ->avg('grade');

            $student->avg_assignment_grade = $avgAssignment ? round($avgAssignment, 1) : null;

            $avgQuiz = QuizAttempt::where('student_id', $student->id)
                ->whereNotNull('score')
                ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                    $q->whereHas('quiz', fn($qz) => $qz->where('subject_id', $selectedSubjectId));
                })
                ->avg('score');

            $student->avg_quiz_score = $avgQuiz ? round($avgQuiz, 1) : null;
        }

        return view('admin.reports.index', compact('students', 'classes', 'subjects', 'selectedClassId', 'selectedSubjectId', 'totalMaterials'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedClassId = $request->get('class_id');
        $selectedSubjectId = $request->get('subject_id');

        $studentsQuery = User::whereHas('role', fn($q) => $q->where('name', 'siswa'));
        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }
        $students = $studentsQuery->with('schoolClass')->get();

        $totalMaterials = Material::when($selectedClassId, fn($q) => $q->where('class_id', $selectedClassId))
            ->when($selectedSubjectId, fn($q) => $q->where('subject_id', $selectedSubjectId))
            ->count();

        $response = new StreamedResponse(function () use ($students, $totalMaterials, $selectedSubjectId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID Siswa', 'Nama Siswa', 'Email', 'Kelas', 'Progres Materi (%)', 'Rata-rata Tugas', 'Rata-rata Kuis']);

            foreach ($students as $student) {
                $completedMaterials = MaterialProgress::where('user_id', $student->id)
                    ->where('is_completed', true)
                    ->when($selectedSubjectId, function ($q) use ($selectedSubjectId) {
                        $q->whereHas('material', fn($m) => $m->where('subject_id', $selectedSubjectId));
                    })->count();

                $matPct = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;

                $avgAss = AssignmentSubmission::where('student_id', $student->id)
                    ->whereNotNull('grade')
                    ->when($selectedSubjectId, fn($q) => $q->whereHas('assignment', fn($a) => $a->where('subject_id', $selectedSubjectId)))
                    ->avg('grade');

                $avgQz = QuizAttempt::where('student_id', $student->id)
                    ->whereNotNull('score')
                    ->when($selectedSubjectId, fn($q) => $q->whereHas('quiz', fn($qz) => $qz->where('subject_id', $selectedSubjectId)))
                    ->avg('score');

                fputcsv($handle, [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->schoolClass->name ?? '-',
                    $matPct . '%',
                    $avgAss ? round($avgAss, 1) : '-',
                    $avgQz ? round($avgQz, 1) : '-',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="laporan-lms-siswa.csv"');

        return $response;
    }
}
