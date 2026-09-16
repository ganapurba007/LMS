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
     *
     * Subjects & classes are loaded ONCE and distributed to all collections
     * via setRelation() to eliminate duplicate queries.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('role');
        }

        // 1. User subjects & restriction status (Single Query)
        $mySubjects = $user ? $user->subjects()->withCount('materials')->get() : collect();
        $isRestrictedGuru = $mySubjects->isNotEmpty();
        $assignedSubjectIds = $isRestrictedGuru ? $mySubjects->pluck('id') : collect();

        // 2. Core Summary Metrics
        $totalClasses   = SchoolClass::count();
        $totalStudents  = User::whereHas('role', fn($q) => $q->where('name', 'siswa'))->count();
        $totalTeachers  = User::whereHas('role', fn($q) => $q->where('name', 'guru'))->count();
        $totalSubjects  = $isRestrictedGuru ? $mySubjects->count() : Subject::count();

        // 3. Materials (no subject/class eager load here — handled below)
        $materialBaseQuery = Material::query();
        if ($isRestrictedGuru) {
            $materialBaseQuery->where(function ($q) use ($assignedSubjectIds, $user) {
                $q->whereIn('subject_id', $assignedSubjectIds)
                  ->orWhere('instructor_id', $user->id);
            });
        }
        $totalMaterials  = (clone $materialBaseQuery)->count();
        $recentMaterials = (clone $materialBaseQuery)
            ->withCount('discussions')
            ->latest()
            ->take(5)
            ->get();

        // 4. Submissions needing grading (no nested subject/class eager load here)
        $submissionBaseQuery = AssignmentSubmission::query()
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

        $ungradedSubmissions        = (clone $submissionBaseQuery)->whereNull('grade')->count();
        $recentUngradedSubmissions  = (clone $submissionBaseQuery)
            ->whereNull('grade')
            ->with(['assignment', 'student'])   // only base relations; subject/class set below
            ->latest()
            ->take(5)
            ->get();

        // 5. Quizzes (no schoolClass eager load here — handled below)
        $quizBaseQuery = Quiz::query()
            ->where(function ($query) use ($isRestrictedGuru, $assignedSubjectIds, $user) {
                if ($isRestrictedGuru) {
                    $query->whereIn('subject_id', $assignedSubjectIds)
                          ->orWhere('instructor_id', $user->id);
                } else {
                    $query->where('instructor_id', $user->id);
                }
            });

        $activeQuizzes = (clone $quizBaseQuery)
            ->where(function ($query) {
                $query->whereNull('deadline')
                      ->orWhere('deadline', '>=', now());
            })->count();

        $recentQuizzes = (clone $quizBaseQuery)
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Active Assignments (no subject/class eager load here — handled below)
        $assignmentBaseQuery = Assignment::query()
            ->where(function ($query) use ($isRestrictedGuru, $assignedSubjectIds, $user) {
                if ($isRestrictedGuru) {
                    $query->whereIn('subject_id', $assignedSubjectIds)
                          ->orWhere('instructor_id', $user->id);
                } else {
                    $query->where('instructor_id', $user->id);
                }
            });

        $activeAssignments = (clone $assignmentBaseQuery)
            ->where(function ($query) {
                $query->whereNull('due_date')
                      ->orWhere('due_date', '>=', now());
            })->count();

        $recentAssignments = (clone $assignmentBaseQuery)
            ->withCount('submissions')
            ->latest()
            ->take(5)
            ->get();

        // 7. Latest Discussion Comments (Ruang Diskusi Materi)
        $discussionBaseQuery = MaterialDiscussion::query();
        if ($isRestrictedGuru) {
            $discussionBaseQuery->whereHas('material', function ($q) use ($assignedSubjectIds, $user) {
                $q->whereIn('subject_id', $assignedSubjectIds)
                  ->orWhere('instructor_id', $user->id);
            });
        }
        $totalDiscussions  = (clone $discussionBaseQuery)->count();
        $recentDiscussions = (clone $discussionBaseQuery)
            ->whereHas('user.role', fn($q) => $q->where('name', 'siswa'))
            ->with(['user', 'material'])
            ->latest()
            ->take(5)
            ->get();

        // ─────────────────────────────────────────────────────────────────
        // DEDUPLICATION: Collect all unique IDs across every collection,
        // then load Subject & SchoolClass exactly ONCE each, and distribute
        // via setRelation() — eliminating all duplicate queries.
        // ─────────────────────────────────────────────────────────────────

        $allSubjectIds = collect()
            ->merge($recentMaterials->pluck('subject_id'))
            ->merge($recentAssignments->pluck('subject_id'))
            ->merge($recentUngradedSubmissions->map(fn($s) => optional($s->assignment)->subject_id))
            ->filter()->unique()->values();

        $allClassIds = collect()
            ->merge($recentMaterials->pluck('class_id'))
            ->merge($recentAssignments->pluck('class_id'))
            ->merge($recentQuizzes->pluck('class_id'))
            ->merge($recentUngradedSubmissions->map(fn($s) => optional($s->student)->class_id))
            ->filter()->unique()->values();

        // Single query each — no more duplicates
        $subjectsMap = $allSubjectIds->isNotEmpty()
            ? Subject::whereIn('id', $allSubjectIds)->get()->keyBy('id')
            : collect();

        $classesMap = $allClassIds->isNotEmpty()
            ? SchoolClass::whereIn('id', $allClassIds)->get()->keyBy('id')
            : collect();

        // Assign to recentMaterials
        $recentMaterials->each(function ($mat) use ($subjectsMap, $classesMap) {
            $mat->setRelation('subject', $subjectsMap->get($mat->subject_id));
            $mat->setRelation('schoolClass', $classesMap->get($mat->class_id));
        });

        // Assign to recentAssignments
        $recentAssignments->each(function ($a) use ($subjectsMap, $classesMap) {
            $a->setRelation('subject', $subjectsMap->get($a->subject_id));
            $a->setRelation('schoolClass', $classesMap->get($a->class_id));
        });

        // Assign to recentQuizzes
        $recentQuizzes->each(function ($q) use ($classesMap) {
            $q->setRelation('schoolClass', $classesMap->get($q->class_id));
        });

        // Assign nested relations on recentUngradedSubmissions
        $recentUngradedSubmissions->each(function ($sub) use ($subjectsMap, $classesMap) {
            if ($sub->assignment) {
                $sub->assignment->setRelation('subject', $subjectsMap->get($sub->assignment->subject_id));
            }
            if ($sub->student) {
                $sub->student->setRelation('schoolClass', $classesMap->get($sub->student->class_id));
            }
        });

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
