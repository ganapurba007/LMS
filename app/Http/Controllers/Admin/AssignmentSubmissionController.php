<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssignmentSubmissionController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengumpulan tugas siswa dengan filter pencarian, tugas, kelas, dan status penilaian.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->query('search');
        $assignmentId = $request->query('assignment_id');
        $classId = $request->query('class_id');
        $status = $request->query('status'); // 'graded', 'ungraded', or null

        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $isRestrictedGuru = $userSubjects->isNotEmpty();
        $allowedSubjectIds = $isRestrictedGuru ? $userSubjects->pluck('id') : collect();

        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        $subjectsMap = $subjects->keyBy('id');
        $classesMap = $classes->keyBy('id');

        $query = AssignmentSubmission::with([
            'assignment',
            'student',
        ]);

        // Filter jika guru hanya mengajar mata pelajaran tertentu
        if ($isRestrictedGuru) {
            $query->whereHas('assignment', function ($q) use ($allowedSubjectIds) {
                $q->whereIn('subject_id', $allowedSubjectIds);
            });
        }

        // Filter pencarian nama siswa, email, atau judul tugas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('assignment', function ($aq) use ($search) {
                    $aq->where('title', 'like', "%{$search}%");
                });
            });
        }

        // Filter spesifik tugas
        if ($assignmentId) {
            $query->where('assignment_id', $assignmentId);
        }

        // Filter spesifik kelas
        if ($classId) {
            $query->whereHas('assignment', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        // Filter status penilaian
        if ($status === 'graded') {
            $query->whereNotNull('grade');
        } elseif ($status === 'ungraded') {
            $query->whereNull('grade');
        }

        $submissions = $query->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        $submissions->each(function ($sub) use ($subjectsMap, $classesMap) {
            if ($sub->assignment) {
                $sub->assignment->setRelation('subject', $subjectsMap->get($sub->assignment->subject_id));
                $sub->assignment->setRelation('schoolClass', $classesMap->get($sub->assignment->class_id));
            }
            if ($sub->student) {
                $sub->student->setRelation('schoolClass', $classesMap->get($sub->student->class_id));
            }
        });

        // Data filter assignments & classes
        $assignmentsQuery = Assignment::query();
        if ($isRestrictedGuru) {
            $assignmentsQuery->whereIn('subject_id', $allowedSubjectIds);
        }
        $assignments = $assignmentsQuery->latest('id')->get();
        $assignments->each(function ($a) use ($classesMap) {
            $a->setRelation('schoolClass', $classesMap->get($a->class_id));
        });

        return view('admin.submissions.index', compact(
            'submissions',
            'assignments',
            'classes',
            'search',
            'assignmentId',
            'classId',
            'status'
        ));
    }

    /**
     * Menampilkan detail pengumpulan tugas siswa untuk dikoreksi dan dinilai.
     */
    public function show(AssignmentSubmission $submission): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();

        // Otorisasi jika guru memiliki mata pelajaran terbatas
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $submission->assignment->subject_id)) {
            abort(403, 'Anda tidak berhak mengakses pengumpulan tugas mata pelajaran ini.');
        }

        $submission->load(['assignment.subject', 'assignment.schoolClass', 'student']);
        if ($submission->student && $submission->assignment?->schoolClass) {
            $submission->student->setRelation('schoolClass', $submission->assignment->schoolClass);
        }

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Menyimpan nilai dan umpan balik koreksi tugas siswa.
     */
    public function grade(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $request->validate([
            'grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ], [
            'grade.required' => 'Nilai tugas wajib diisi.',
            'grade.numeric' => 'Nilai tugas harus berupa angka.',
            'grade.min' => 'Nilai minimal adalah 0.',
            'grade.max' => 'Nilai maksimal adalah 100.',
            'feedback.max' => 'Catatan umpan balik maksimal 2000 karakter.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $submission->assignment->subject_id)) {
            return back()->withErrors(['grade' => 'Anda tidak berhak menilai tugas untuk mata pelajaran ini.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $submission->grade = $request->grade;
            $submission->feedback = $request->feedback;
            $submission->graded_at = now();
            $submission->save();

            // Kirim notifikasi ke siswa bahwa tugas telah dinilai
            Notification::create([
                'user_id' => $submission->student_id,
                'type' => 'new_assignment',
                'title' => 'Nilai Tugas: ' . $submission->assignment->title,
                'message' => 'Tugas "' . $submission->assignment->title . '" telah dinilai dengan perolehan nilai ' . number_format($submission->grade, 1) . '.',
                'related_url' => route('student.assignments.show', $submission->assignment),
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('admin.submissions.index')
                ->with('success', 'Nilai dan umpan balik tugas siswa berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['grade' => 'Gagal menyimpan nilai: ' . $e->getMessage()])->withInput();
        }
    }
}
