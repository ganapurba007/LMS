<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\MaterialDiscussion;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the Admin / Guru dashboard with comprehensive statistics,
     * recent activity, and latest comments from the material discussion forum.
     */
    public function index()
    {
        $user = Auth::user();
        $isRestrictedGuru = $user->subjects()->exists();
        $assignedSubjectIds = $isRestrictedGuru ? $user->subjects->pluck('id') : collect();

        // 1. Core Summary Metrics
        $totalClasses = SchoolClass::count();
        $totalStudents = User::whereHas('role', fn($q) => $q->where('name', 'siswa'))->count();
        $totalTeachers = User::whereHas('role', fn($q) => $q->where('name', 'guru'))->count();
        $totalSubjects = $isRestrictedGuru ? $user->subjects()->count() : Subject::count();

        // 2. Materials
        $materialQuery = Material::query();
        if ($isRestrictedGuru) {
            $materialQuery->where(function ($q) use ($assignedSubjectIds, $user) {
                $q->whereIn('subject_id', $assignedSubjectIds)
                  ->orWhere('instructor_id', $user->id);
            });
        }
        $totalMaterials = (clone $materialQuery)->count();
        $recentMaterials = (clone $materialQuery)
            ->with(['subject', 'schoolClass', 'instructor'])
            ->withCount('discussions')
            ->latest()
            ->take(5)
            ->get();

        // 3. Submissions needing grading
        $submissionQuery = AssignmentSubmission::with(['assignment.subject', 'assignment.schoolClass', 'student.schoolClass'])
            ->whereHas('assignment', function ($query) use ($isRestrictedGuru, $assignedSubjectIds, $user) {
                if ($isRestrictedGuru) {
                    $query->where(function ($q) use ($assignedSubjectIds, $user) {
                        $q->whereIn('subject_id', $assignedSubjectIds)
                          ->orWhere('instructor_id', $user->id);
                    });
                } else {
                    $query->where('instructor_id', $user->id);
                }
            });

        $ungradedSubmissions = (clone $submissionQuery)->whereNull('grade')->count();
        $recentUngradedSubmissions = (clone $submissionQuery)
            ->whereNull('grade')
            ->latest()
            ->take(5)
            ->get();

        // 4. Quizzes
        $quizQuery = Quiz::with(['subject', 'schoolClass'])
            ->where(function ($query) use ($isRestrictedGuru, $assignedSubjectIds, $user) {
                if ($isRestrictedGuru) {
                    $query->whereIn('subject_id', $assignedSubjectIds)
                          ->orWhere('instructor_id', $user->id);
                } else {
                    $query->where('instructor_id', $user->id);
                }
            });

        $activeQuizzes = (clone $quizQuery)
            ->where(function ($query) {
                $query->whereNull('deadline')
                      ->orWhere('deadline', '>=', now());
            })->count();

        $recentQuizzes = (clone $quizQuery)
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Active Assignments
        $assignmentQuery = Assignment::where(function ($query) use ($isRestrictedGuru, $assignedSubjectIds, $user) {
            if ($isRestrictedGuru) {
                $query->whereIn('subject_id', $assignedSubjectIds)
                      ->orWhere('instructor_id', $user->id);
            } else {
                $query->where('instructor_id', $user->id);
            }
        });

        $activeAssignments = (clone $assignmentQuery)
            ->where(function ($query) {
                $query->whereNull('due_date')
                      ->orWhere('due_date', '>=', now());
            })->count();

        $recentAssignments = (clone $assignmentQuery)
            ->with(['subject', 'schoolClass'])
            ->withCount('submissions')
            ->latest()
            ->take(5)
            ->get();

        // 6. Latest Discussion Comments (Ruang Diskusi Materi) - 5 Komentar Terakhir (dari Siswa)
        $discussionQuery = MaterialDiscussion::with([
            'user.role',
            'material.subject',
            'material.schoolClass',
            'parent.user'
        ]);

        if ($isRestrictedGuru) {
            $discussionQuery->whereHas('material', function ($q) use ($assignedSubjectIds, $user) {
                $q->whereIn('subject_id', $assignedSubjectIds)
                  ->orWhere('instructor_id', $user->id);
            });
        }

        $totalDiscussions = (clone $discussionQuery)->count();
        $recentDiscussions = (clone $discussionQuery)
            ->whereHas('user.role', fn($q) => $q->where('name', 'siswa'))
            ->latest()
            ->take(5)
            ->get();

        // 7. Assigned subjects for teacher overview
        $mySubjects = $user->subjects()->withCount('materials')->get();

        return view('admin.dashboard', compact(
            'totalClasses',
            'totalStudents',
            'totalTeachers',
            'totalSubjects',
            'totalMaterials',
            'ungradedSubmissions',
            'activeQuizzes',
            'activeAssignments',
            'totalDiscussions',
            'recentDiscussions',
            'recentUngradedSubmissions',
            'recentMaterials',
            'recentQuizzes',
            'recentAssignments',
            'mySubjects'
        ));
    }
}
