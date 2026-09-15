<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $classId = $user->class_id;

        $upcomingAssignments = Assignment::when($classId, fn($q) => $q->where('class_id', $classId))
            ->where('due_date', '>=', now())
            ->with(['subject', 'instructor'])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $activeQuizzes = Quiz::when($classId, fn($q) => $q->where('class_id', $classId))
            ->where('deadline', '>=', now())
            ->with(['subject', 'instructor'])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        $materials = Material::when($classId, fn($q) => $q->where('class_id', $classId))
            ->with(['subject', 'instructor'])
            ->latest()
            ->take(5)
            ->get();

        $totalClassMaterials = Material::when($classId, fn($q) => $q->where('class_id', $classId))->count();
        $completedMaterialsCount = MaterialProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

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
