<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentBank;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignmentBankController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->query('search');
        $subjectId = $request->query('subject_id');

        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $hasSubjectRestriction = $userSubjects->isNotEmpty();

        $query = AssignmentBank::with(['subject', 'instructor'])
            ->where('instructor_id', Auth::id());

        if ($hasSubjectRestriction) {
            $allowedSubjectIds = $userSubjects->pluck('id');
            $query->where(function ($q) use ($allowedSubjectIds) {
                $q->whereIn('subject_id', $allowedSubjectIds)
                  ->orWhereNull('subject_id');
            });
        }

        $assignmentBanks = $query
            ->when($search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($subjectId, function ($q, $subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subjects = $hasSubjectRestriction ? $userSubjects : Subject::orderBy('name')->get();

        return view('admin.assignment-banks.index', compact('assignmentBanks', 'subjects', 'search', 'subjectId'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();

        return view('admin.assignment-banks.create', compact('subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'description' => ['nullable', 'string'],
        ]);

        AssignmentBank::create([
            'instructor_id' => Auth::id(),
            'subject_id' => $request->subject_id,
            'title' => trim($request->title),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.assignment-banks.index')
            ->with('success', 'Tugas berhasil ditambahkan ke Bank Tugas.');
    }

    public function edit(AssignmentBank $assignmentBank): View
    {
        if ($assignmentBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke bank tugas ini.');
        }

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();

        return view('admin.assignment-banks.edit', compact('assignmentBank', 'subjects'));
    }

    public function update(Request $request, AssignmentBank $assignmentBank): RedirectResponse
    {
        if ($assignmentBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke bank tugas ini.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'description' => ['nullable', 'string'],
        ]);

        $oldTitle = $assignmentBank->title;

        $assignmentBank->title = trim($request->title);
        $assignmentBank->subject_id = $request->subject_id;
        $assignmentBank->description = $request->description;
        $assignmentBank->save();

        // Otomatis sinkronkan perubahan ke tugas yang sudah diterbitkan di kelas
        \App\Models\Assignment::where('instructor_id', Auth::id())
            ->where(function ($q) use ($oldTitle, $assignmentBank) {
                $q->where('title', $oldTitle)
                  ->orWhere('title', $assignmentBank->title);
            })
            ->get()
            ->each(function ($assignment) use ($assignmentBank) {
                $assignment->title = $assignmentBank->title;
                $assignment->description = $assignmentBank->description;
                if ($assignmentBank->subject_id) {
                    $assignment->subject_id = $assignmentBank->subject_id;
                }
                $assignment->save();
            });

        return redirect()->route('admin.assignment-banks.index')
            ->with('success', 'Tugas di Bank Tugas berhasil diperbarui dan disinkronkan.');
    }

    public function destroy(AssignmentBank $assignmentBank): RedirectResponse
    {
        if ($assignmentBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke bank tugas ini.');
        }

        $assignmentBank->delete();

        return redirect()->route('admin.assignment-banks.index')
            ->with('success', 'Tugas di Bank Tugas berhasil dihapus.');
    }

    public function getDetailJson(AssignmentBank $assignmentBank): JsonResponse
    {
        if ($assignmentBank->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id' => $assignmentBank->id,
            'title' => $assignmentBank->title,
            'subject_id' => $assignmentBank->subject_id,
            'subject_name' => $assignmentBank->subject ? $assignmentBank->subject->name : 'Umum / Semua',
            'description' => $assignmentBank->description,
        ]);
    }
}
