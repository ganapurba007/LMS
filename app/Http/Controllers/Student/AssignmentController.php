<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->isGuru()) {
            $query = Assignment::where('instructor_id', $user->id)
                ->with(['subject', 'instructor', 'schoolClass']);
            $allClassAssignments = Assignment::where('instructor_id', $user->id)->get();
        } else {
            $query = Assignment::where('class_id', $user->class_id)
                ->with(['subject', 'instructor', 'schoolClass']);
            $allClassAssignments = Assignment::where('class_id', $user->class_id)->get();
        }

        // Filter pencarian judul, deskripsi, mata pelajaran, atau guru
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('instructor', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter spesifik mata pelajaran
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        $allSubmissions = AssignmentSubmission::where('student_id', $user->id)->get()->keyBy('assignment_id');
        $submittedIds = $allSubmissions->keys()->toArray();

        // Filter status pengumpulan
        $status = $request->input('status');
        if ($status === 'submitted') {
            $query->whereIn('id', $submittedIds);
        } elseif ($status === 'unsubmitted') {
            $query->whereNotIn('id', $submittedIds);
        } elseif ($status === 'graded') {
            $gradedIds = $allSubmissions->whereNotNull('grade')->keys()->toArray();
            $query->whereIn('id', $gradedIds);
        }

        $assignments = $query->orderBy('due_date', 'asc')
            ->paginate(12)
            ->withQueryString();

        $submissions = $allSubmissions;

        // Hitung statistik penugasan kelas siswa
        $totalAssignments = $allClassAssignments->count();
        $submittedCount = count(array_intersect($submittedIds, $allClassAssignments->pluck('id')->toArray()));
        $unsubmittedCount = max(0, $totalAssignments - $submittedCount);
        $gradedCount = $allSubmissions->whereIn('assignment_id', $allClassAssignments->pluck('id'))->whereNotNull('grade')->count();
        $progressPercent = $totalAssignments > 0 ? round(($submittedCount / $totalAssignments) * 100) : 0;

        // Daftar mata pelajaran yang memiliki tugas di kelas ini
        $subjects = Subject::whereIn('id', $allClassAssignments->pluck('subject_id')->unique())
            ->orderBy('name')
            ->get();

        return view('student.assignments.index', compact(
            'assignments',
            'submissions',
            'totalAssignments',
            'submittedCount',
            'unsubmittedCount',
            'gradedCount',
            'progressPercent',
            'subjects'
        ));
    }

    public function show(Assignment $assignment): View
    {
        $user = Auth::user();
        if ($user->isGuru() && $assignment->instructor_id !== $user->id) {
            abort(403, 'Tugas ini bukan milik Anda.');
        }
        if ($user->isSiswa() && $assignment->class_id !== $user->class_id) {
            abort(403, 'Tugas ini tidak ditujukan untuk kelas Anda.');
        }

        $assignment->load(['subject', 'instructor', 'schoolClass']);

        $submission = AssignmentSubmission::where('student_id', $user->id)
            ->where('assignment_id', $assignment->id)
            ->first();

        $teacherStats = null;
        if ($user->isGuru()) {
            $totalClassStudents = User::where('class_id', $assignment->class_id)
                ->whereHas('role', fn ($q) => $q->where('name', 'siswa'))
                ->count();
            $submittedStudentsCount = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->count();
            $gradedStudentsCount = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->whereNotNull('grade')
                ->count();
            $teacherStats = [
                'total_students' => $totalClassStudents,
                'submitted_count' => $submittedStudentsCount,
                'graded_count' => $gradedStudentsCount,
                'submitted_percent' => $totalClassStudents > 0 ? round(($submittedStudentsCount / $totalClassStudents) * 100) : 0,
            ];
        }

        return view('student.assignments.show', compact('assignment', 'submission', 'teacherStats'));
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isGuru()) {
            return redirect()->route('student.assignments.show', $assignment)
                ->with('error', 'Guru tidak dapat mengumpulkan tugas.');
        }
        if ($user->isSiswa() && $assignment->class_id !== $user->class_id) {
            abort(403, 'Anda tidak berhak mengumpulkan tugas ini.');
        }

        // Cegah pengumpulan jika siswa sudah pernah mengumpulkan tugas ini (hanya 1 kali pengumpulan)
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return redirect()->route('student.assignments.show', $assignment)
                ->with('error', 'Anda sudah mengumpulkan jawaban untuk tugas ini. Pengumpulan tugas hanya dapat dilakukan 1 (satu) kali.');
        }

        // Cegah pengumpulan jika batas waktu telah lewat
        if ($assignment->due_date && $assignment->due_date->isPast()) {
            return redirect()->route('student.assignments.show', $assignment)
                ->with('error', 'Batas waktu pengerjaan tugas telah berakhir. Pengumpulan jawaban telah ditutup.');
        }

        $request->validate([
            'answer_text' => ['required', 'string', 'max:5000'],
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
            'answer_text' => trim($request->answer_text),
            'submitted_at' => now(),
        ]);

        // Buat notifikasi untuk guru pengampu tugas
        if ($assignment->instructor_id) {
            Notification::create([
                'user_id' => $assignment->instructor_id,
                'type' => 'new_assignment',
                'title' => 'Tugas Dikumpulkan: ' . $assignment->title,
                'message' => 'Siswa ' . $user->name . ' telah mengumpulkan jawaban untuk tugas "' . $assignment->title . '".',
                'related_url' => route('admin.submissions.index'),
                'is_read' => false,
            ]);
        }

        return redirect()->route('student.assignments.show', $assignment)
            ->with('success', 'Tugas Anda berhasil dikumpulkan.');
    }
}

