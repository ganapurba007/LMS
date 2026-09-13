<?php

namespace App\Http\Controllers\Admin;

use App\Events\AssignmentCreated;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['subject', 'schoolClass', 'instructor'])
            ->withCount('submissions')
            ->latest()
            ->paginate(10);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.assignments.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak membuat tugas untuk mata pelajaran ini.'])->withInput();
        }

        $assignment = new Assignment();
        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;
        $assignment->subject_id = $request->subject_id;
        $assignment->class_id = $request->class_id;
        $assignment->instructor_id = Auth::id();
        $assignment->save();

        event(new AssignmentCreated($assignment));

        // Buat notifikasi database untuk seluruh siswa di kelas terkait
        $students = User::where('class_id', $assignment->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        $instructorName = $user->name ?? 'Guru Pengampu';
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
            ->with('success', 'Tugas siswa berhasil dibuat dan notifikasi realtime dikirim.');
    }

    public function edit(Assignment $assignment)
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.assignments.edit', compact('assignment', 'subjects', 'classes'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak mengedit tugas untuk mata pelajaran ini.'])->withInput();
        }

        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;
        $assignment->subject_id = $request->subject_id;
        $assignment->class_id = $request->class_id;
        $assignment->save();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas siswa berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas siswa berhasil dihapus.');
    }
}
