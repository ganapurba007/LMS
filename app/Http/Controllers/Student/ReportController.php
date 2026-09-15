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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $classId = $user->class_id;

        // Material Progress
        $totalMaterials = Material::when($classId, fn($q) => $q->where('class_id', $classId))->count();
        $completedMaterials = MaterialProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
        $materialProgressPercent = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100, 1) : 0;

        // Assignments Report
        $submissions = AssignmentSubmission::with('assignment.subject')
            ->where('student_id', $user->id)
            ->latest()
            ->get();
        $gradedSubmissions = $submissions->whereNotNull('grade');
        $avgAssignmentScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('grade'), 1) : 0;

        // Quizzes Report
        $quizAttempts = QuizAttempt::with('quiz.subject')
            ->where('student_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest()
            ->get();
        $avgQuizScore = $quizAttempts->count() > 0 ? round($quizAttempts->avg('score'), 1) : 0;

        // Overall GPA / Combined average
        $totalEvaluations = $gradedSubmissions->count() + $quizAttempts->count();
        $sumEvaluations = $gradedSubmissions->sum('grade') + $quizAttempts->sum('score');
        $overallScore = $totalEvaluations > 0 ? round($sumEvaluations / $totalEvaluations, 1) : 0;

        // Predicate calculation
        if ($overallScore >= 85) {
            $gradePredicate = 'Sangat Baik (A)';
            $predicateClass = 'text-success';
            $predicateBadgeBg = '#dcfce7';
            $predicateBadgeColor = '#15803d';
        } elseif ($overallScore >= 75) {
            $gradePredicate = 'Baik (B)';
            $predicateClass = 'text-primary';
            $predicateBadgeBg = '#e0f2fe';
            $predicateBadgeColor = '#0369a1';
        } elseif ($overallScore >= 65) {
            $gradePredicate = 'Cukup (C)';
            $predicateClass = 'text-warning';
            $predicateBadgeBg = '#fef3c7';
            $predicateBadgeColor = '#b45309';
        } else {
            $gradePredicate = 'Perlu Peningkatan (D)';
            $predicateClass = 'text-danger';
            $predicateBadgeBg = '#fee2e2';
            $predicateBadgeColor = '#b91c1c';
        }

        // Subject Breakdown (Rekap per Mata Pelajaran)
        $subjects = Subject::whereHas('materials', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orWhereHas('assignments', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orWhereHas('quizzes', fn($q) => $q->when($classId, fn($sq) => $sq->where('class_id', $classId)))
            ->orderBy('name')
            ->get();

        $subjectBreakdown = [];
        foreach ($subjects as $subject) {
            $subMaterialsTotal = Material::when($classId, fn($q) => $q->where('class_id', $classId))->where('subject_id', $subject->id)->count();
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

