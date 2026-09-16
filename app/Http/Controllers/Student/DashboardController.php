<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('schoolClass');
        }

        if ($user->isGuru()) {
            $upcomingAssignments = Assignment::where('instructor_id', $user->id)
                ->where('due_date', '>=', now())
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get();

            $activeQuizzes = Quiz::where('instructor_id', $user->id)
                ->where('deadline', '>=', now())
                ->orderBy('deadline', 'asc')
                ->take(5)
                ->get();

            $materials = Material::where('instructor_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            $totalClassMaterials = Material::where('instructor_id', $user->id)->count();
            $completedMaterialsCount = MaterialProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();
        } else {
            $classId = $user->class_id;

            $upcomingAssignments = Assignment::where('class_id', $classId)
                ->where('due_date', '>=', now())
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get();

            $activeQuizzes = Quiz::where('class_id', $classId)
                ->where('deadline', '>=', now())
                ->orderBy('deadline', 'asc')
                ->take(5)
                ->get();

            $materials = Material::where('class_id', $classId)
                ->latest()
                ->take(5)
                ->get();

            $totalClassMaterials = Material::where('class_id', $classId)->count();
            $completedMaterialsCount = MaterialProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();
        }

        // Deduplicate subjects, instructors, and classes relations
        $allSubjectIds = collect()
            ->merge($upcomingAssignments->pluck('subject_id'))
            ->merge($activeQuizzes->pluck('subject_id'))
            ->merge($materials->pluck('subject_id'))
            ->filter()->unique()->values();

        $allInstructorIds = collect()
            ->merge($upcomingAssignments->pluck('instructor_id'))
            ->merge($activeQuizzes->pluck('instructor_id'))
            ->merge($materials->pluck('instructor_id'))
            ->filter()->unique()->values();

        $allClassIds = collect()
            ->merge($materials->pluck('class_id'))
            ->filter()->unique()->values();

        $subjectsMap = $allSubjectIds->isNotEmpty()
            ? Subject::whereIn('id', $allSubjectIds)->get()->keyBy('id')
            : collect();

        $instructorsMap = $allInstructorIds->isNotEmpty()
            ? User::whereIn('id', $allInstructorIds)->get()->keyBy('id')
            : collect();

        $classesMap = $allClassIds->isNotEmpty()
            ? SchoolClass::whereIn('id', $allClassIds)->get()->keyBy('id')
            : collect();

        $upcomingAssignments->each(function ($a) use ($subjectsMap, $instructorsMap) {
            $a->setRelation('subject', $subjectsMap->get($a->subject_id));
            $a->setRelation('instructor', $instructorsMap->get($a->instructor_id));
        });

        $activeQuizzes->each(function ($q) use ($subjectsMap, $instructorsMap) {
            $q->setRelation('subject', $subjectsMap->get($q->subject_id));
            $q->setRelation('instructor', $instructorsMap->get($q->instructor_id));
        });

        $materials->each(function ($m) use ($subjectsMap, $instructorsMap, $classesMap) {
            $m->setRelation('subject', $subjectsMap->get($m->subject_id));
            $m->setRelation('instructor', $instructorsMap->get($m->instructor_id));
            $m->setRelation('schoolClass', $classesMap->get($m->class_id));
        });

        $overallProgress = $totalClassMaterials > 0
            ? round(($completedMaterialsCount / $totalClassMaterials) * 100, 1)
            : 0;

        return view('dashboard', compact(
            'user',
            'upcomingAssignments',
            'activeQuizzes',
            'materials',
            'overallProgress',
            'totalClassMaterials',
            'completedMaterialsCount'
        ));
    }
}
