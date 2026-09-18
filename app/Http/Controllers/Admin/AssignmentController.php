<?php

namespace App\Http\Controllers\Admin;

use App\Events\AssignmentCreated;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentBank;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');

        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $hasSubjectRestriction = $userSubjects->isNotEmpty();

        $query = Assignment::with(['subject', 'schoolClass', 'instructor'])
            ->withCount('submissions');

        if ($hasSubjectRestriction) {
            $allowedSubjectIds = $userSubjects->pluck('id');
            $query->whereIn('subject_id', $allowedSubjectIds);
        }

        $assignments = $query
            ->when($search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($subjectId, function ($q, $subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->when($classId, function ($q, $classId) {
                $q->where('class_id', $classId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subjects = $hasSubjectRestriction ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.assignments.index', compact('assignments', 'subjects', 'classes', 'search', 'subjectId', 'classId'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $assignmentBanks = AssignmentBank::where('instructor_id', Auth::id())->with('subject')->orderBy('title')->get();

        return view('admin.assignments.create', compact('subjects', 'classes', 'assignmentBanks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_bank_id' => ['required', 'exists:assignment_banks,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'due_date' => ['required', 'date'],
        ], [
            'assignment_bank_id.required' => 'Silakan pilih tugas dari Bank Tugas.',
            'assignment_bank_id.exists' => 'Tugas dari Bank Tugas tidak valid.',
            'class_id.required' => 'Silakan pilih kelas target penerima.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'due_date.required' => 'Batas waktu penyerahan tugas wajib diisi.',
        ]);

        $bankItem = AssignmentBank::with('subject')->findOrFail($request->assignment_bank_id);
        $user = Auth::user();

        $subjectId = $bankItem->subject_id;
        if (!$subjectId) {
            $firstSubj = $user->subjects()->first() ?? Subject::first();
            $subjectId = $firstSubj ? $firstSubj->id : null;
        }

        if ($subjectId && $user->subjects()->exists() && !$user->subjects()->where('subjects.id', $subjectId)->exists()) {
            return back()->withErrors(['assignment_bank_id' => 'Anda tidak berhak menerbitkan tugas untuk mata pelajaran ini.'])->withInput();
        }

        $assignment = new Assignment();
        $assignment->title = $bankItem->title;
        $assignment->description = $bankItem->description;
        $assignment->due_date = $request->due_date;
        $assignment->subject_id = $subjectId;
        $assignment->class_id = $request->class_id;
        $assignment->instructor_id = Auth::id();
        $assignment->save();

        event(new AssignmentCreated($assignment));

        // Buat notifikasi database untuk seluruh siswa di kelas terkait
        $students = User::where('class_id', $assignment->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        $instructorName = Auth::user()->name ?? 'Guru Pengampu';
        $subjectName = $assignment->subject->name ?? 'Mata Pelajaran';
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'new_assignment',
                'title' => 'Tugas Baru: ' . $assignment->title,
                'message' => 'Guru ' . $instructorName . ' telah menerbitkan tugas baru "' . $assignment->title . '" (' . $subjectName . ').',
                'related_url' => route('student.assignments.show', $assignment),
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas "' . $assignment->title . '" berhasil diterbitkan ke kelas.');
    }

    public function edit(Assignment $assignment)
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();

        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $assignment->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $assignmentBanks = AssignmentBank::where('instructor_id', Auth::id())->with('subject')->orderBy('title')->get();

        return view('admin.assignments.edit', compact('assignment', 'subjects', 'classes', 'assignmentBanks'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'assignment_bank_id' => ['required', 'exists:assignment_banks,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'due_date' => ['required', 'date'],
        ], [
            'assignment_bank_id.required' => 'Silakan pilih tugas dari Bank Tugas.',
            'assignment_bank_id.exists' => 'Tugas dari Bank Tugas tidak valid.',
            'class_id.required' => 'Silakan pilih kelas target penerima.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'due_date.required' => 'Batas waktu penyerahan tugas wajib diisi.',
        ]);

        $bankItem = AssignmentBank::with('subject')->findOrFail($request->assignment_bank_id);
        $user = Auth::user();

        $subjectId = $bankItem->subject_id;
        if (!$subjectId) {
            $firstSubj = $user->subjects()->first() ?? Subject::first();
            $subjectId = $firstSubj ? $firstSubj->id : null;
        }

        if ($subjectId && $user->subjects()->exists() && !$user->subjects()->where('subjects.id', $subjectId)->exists()) {
            return back()->withErrors(['assignment_bank_id' => 'Anda tidak berhak memperbarui tugas untuk mata pelajaran ini.'])->withInput();
        }

        $assignment->title = $bankItem->title;
        $assignment->description = $bankItem->description;
        $assignment->due_date = $request->due_date;
        $assignment->subject_id = $subjectId;
        $assignment->class_id = $request->class_id;
        $assignment->save();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas "' . $assignment->title . '" berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $assignment->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas siswa berhasil dihapus.');
    }
}
