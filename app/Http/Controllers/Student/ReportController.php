<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Menampilkan rekapitulasi laporan hasil belajar siswa (progres materi, nilai tugas, dan skor kuis).
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Validasi akses khusus siswa
        if ($user->isGuru()) {
            return redirect()->route('dashboard')->with('error', 'Menu Laporan Diri hanya ditujukan untuk Siswa.');
        }

        $classId = $user->class_id;

        // 1. Progres Modul Materi Pembelajaran
        $totalMaterials = Material::when($classId, fn($q) => $q->where('class_id', $classId))->count();
        $completedMaterials = MaterialProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
        $materialProgressPercent = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;

        // 2. Rekapitulasi Tugas Siswa
        $submissions = AssignmentSubmission::with(['assignment.subject', 'assignment.schoolClass'])
            ->where('student_id', $user->id)
            ->latest('submitted_at')
            ->get();
        $gradedSubmissions = $submissions->whereNotNull('grade');
        $avgAssignmentScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('grade'), 1) : 0;

        // 3. Rekapitulasi Kuis & Ujian
        $quizAttempts = QuizAttempt::with(['quiz.subject', 'quiz.schoolClass'])
            ->where('student_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->get();
        $avgQuizScore = $quizAttempts->count() > 0 ? round($quizAttempts->avg('score'), 1) : 0;

        // 4. Indeks Prestasi Gabungan (Overall Score)
        $totalEvaluations = $gradedSubmissions->count() + $quizAttempts->count();
        $sumEvaluations = (float) $gradedSubmissions->sum('grade') + (float) $quizAttempts->sum('score');
        $overallScore = $totalEvaluations > 0 ? round($sumEvaluations / $totalEvaluations, 1) : 0;

        // 5. Predikat Hasil Belajar
        if ($overallScore >= 85) {
            $gradePredicate = 'Sangat Baik (A)';
            $predicateClass = 'text-success';
            $predicateBadgeBg = 'rgba(12, 166, 120, 0.12)';
            $predicateBadgeColor = '#0ca678';
        } elseif ($overallScore >= 75) {
            $gradePredicate = 'Baik (B)';
            $predicateClass = 'text-primary';
            $predicateBadgeBg = 'rgba(32, 107, 196, 0.12)';
            $predicateBadgeColor = '#206bc4';
        } elseif ($overallScore >= 65) {
            $gradePredicate = 'Cukup (C)';
            $predicateClass = 'text-warning';
            $predicateBadgeBg = 'rgba(245, 158, 11, 0.12)';
            $predicateBadgeColor = '#d97706';
        } else {
            $gradePredicate = 'Perlu Peningkatan (D)';
            $predicateClass = 'text-danger';
            $predicateBadgeBg = 'rgba(239, 68, 68, 0.12)';
            $predicateBadgeColor = '#ef4444';
        }

        // 6. Rekapitulasi Performa per Mata Pelajaran (Subject Breakdown)
        $subjects = Subject::whereHas('materials', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orWhereHas('assignments', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orWhereHas('quizzes', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orderBy('name')
            ->get();

        $subjectBreakdown = [];
        foreach ($subjects as $subject) {
            $subMaterialsTotal = Material::when($classId, fn($q) => $q->where('class_id', $classId))
                ->where('subject_id', $subject->id)
                ->count();
            
            $subMaterialsCompleted = MaterialProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereHas('material', fn($q) => $q->where('subject_id', $subject->id))
                ->count();
            
            $subMatProgress = $subMaterialsTotal > 0 ? round(($subMaterialsCompleted / $subMaterialsTotal) * 100) : 0;

            $subAssignments = $submissions->filter(fn($s) => $s->assignment && $s->assignment->subject_id === $subject->id && !is_null($s->grade));
            $subAvgAssignment = $subAssignments->count() > 0 ? round($subAssignments->avg('grade'), 1) : null;

            $subQuizzes = $quizAttempts->filter(fn($q) => $q->quiz && $q->quiz->subject_id === $subject->id && !is_null($q->score));
            $subAvgQuiz = $subQuizzes->count() > 0 ? round($subQuizzes->avg('score'), 1) : null;

            $subjectBreakdown[] = [
                'subject' => $subject,
                'materials_total' => $subMaterialsTotal,
                'materials_completed' => $subMaterialsCompleted,
                'materials_progress' => $subMatProgress,
                'avg_assignment' => $subAvgAssignment,
                'avg_quiz' => $subAvgQuiz,
            ];
        }

        return view('student.report.index', compact(
            'totalMaterials',
            'completedMaterials',
            'materialProgressPercent',
            'submissions',
            'avgAssignmentScore',
            'quizAttempts',
            'avgQuizScore',
            'overallScore',
            'gradePredicate',
            'predicateClass',
            'predicateBadgeBg',
            'predicateBadgeColor',
            'subjectBreakdown'
        ));
    }
}
