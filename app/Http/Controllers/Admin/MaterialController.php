<?php

namespace App\Http\Controllers\Admin;

use App\Events\DiscussionCommentSent;
use App\Events\MaterialCreated;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialBank;
use App\Models\MaterialDiscussion;
use App\Models\MaterialProgress;
use App\Models\Notification;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(Request $request): View
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

        $query = Material::with(['subject', 'schoolClass', 'instructor'])
            ->withCount('discussions');

        if ($hasSubjectRestriction) {
            $allowedSubjectIds = $userSubjects->pluck('id');
            $query->whereIn('subject_id', $allowedSubjectIds);
        }

        $materials = $query
            ->when($search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($subjectId, function ($q, $subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->when($classId, function ($q, $classId) {
                $q->where('class_id', $classId);
            })
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $subjects = $hasSubjectRestriction ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.materials.index', compact('materials', 'subjects', 'classes', 'search', 'subjectId', 'classId'));
    }

    public function show(Material $material): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();

        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $material->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        // Load relations without nested .role — roles are assigned below via setRelation()
        $material->load([
            'subject',
            'schoolClass',
            'instructor',
            'rootDiscussions' => function ($q) {
                $q->with(['user', 'replies.user'])->latest();
            },
        ]);
        $material->loadCount('discussions');

        // Preload ALL roles once (roles table is tiny) and distribute to every
        // discussion user via setRelation() — eliminates duplicate role queries.
        $rolesMap = Role::all()->keyBy('id');

        $material->rootDiscussions->each(function ($disc) use ($rolesMap) {
            if ($disc->user) {
                $disc->user->setRelation('role', $rolesMap->get($disc->user->role_id));
            }
            $disc->replies->each(function ($reply) use ($rolesMap) {
                if ($reply->user) {
                    $reply->user->setRelation('role', $rolesMap->get($reply->user->role_id));
                }
            });
        });

        $totalStudents = User::where('class_id', $material->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->count();

        $completedStudentsCount = MaterialProgress::where('material_id', $material->id)
            ->where('is_completed', true)
            ->count();

        return view('admin.materials.show', compact('material', 'totalStudents', 'completedStudentsCount'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $materialBanks = MaterialBank::where('instructor_id', Auth::id())->with('subject')->orderBy('title')->get();

        return view('admin.materials.create', compact('subjects', 'classes', 'materialBanks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'material_bank_id' => ['required', 'exists:material_banks,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ], [
            'material_bank_id.required' => 'Silakan pilih materi dari Bank Materi.',
            'material_bank_id.exists' => 'Materi dari Bank Materi tidak valid.',
            'class_id.required' => 'Silakan pilih kelas target penerima.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        $bankItem = MaterialBank::with('subject')->findOrFail($request->material_bank_id);
        $user = Auth::user();

        $subjectId = $bankItem->subject_id;
        if (!$subjectId) {
            $firstSubj = $user->subjects()->first() ?? Subject::first();
            $subjectId = $firstSubj ? $firstSubj->id : null;
        }

        if ($subjectId && $user->subjects()->exists() && !$user->subjects()->where('subjects.id', $subjectId)->exists()) {
            return back()->withErrors(['material_bank_id' => 'Anda tidak berhak menerbitkan materi untuk mata pelajaran ini.'])->withInput();
        }

        $documentPath = null;
        if ($bankItem->document_path && Storage::disk('public')->exists($bankItem->document_path)) {
            $filename = basename($bankItem->document_path);
            $documentPath = 'materials/' . Str::random(20) . '_' . $filename;
            Storage::disk('public')->copy($bankItem->document_path, $documentPath);
        }

        $material = new Material();
        $material->title = $bankItem->title;
        $material->content_type = $bankItem->content_type ?? 'text';
        $material->content = $bankItem->content;
        $material->document_path = $documentPath;
        $material->video_url = $bankItem->video_url;
        $material->order = $request->order ?? 0;
        $material->subject_id = $subjectId;
        $material->class_id = $request->class_id;
        $material->instructor_id = Auth::id();
        $material->save();

        event(new MaterialCreated($material));

        // Buat notifikasi database untuk seluruh siswa di kelas terkait
        $students = User::where('class_id', $material->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        $instructorName = Auth::user()->name ?? 'Guru Pengampu';
        $subjectName = $material->subject->name ?? 'Mata Pelajaran';
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'new_material',
                'title' => 'Materi Baru: ' . $material->title,
                'message' => 'Guru ' . $instructorName . ' telah menerbitkan materi baru "' . $material->title . '" (' . $subjectName . ').',
                'related_url' => route('student.materials.show', $material),
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi "' . $material->title . '" berhasil diterbitkan ke kelas.');
    }

    public function edit(Material $material): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();

        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $material->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $materialBanks = MaterialBank::where('instructor_id', Auth::id())->with('subject')->orderBy('title')->get();

        return view('admin.materials.edit', compact('material', 'subjects', 'classes', 'materialBanks'));
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $request->validate([
            'material_bank_id' => ['required', 'exists:material_banks,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ], [
            'material_bank_id.required' => 'Silakan pilih materi dari Bank Materi.',
            'material_bank_id.exists' => 'Materi dari Bank Materi tidak valid.',
            'class_id.required' => 'Silakan pilih kelas target penerima.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        $bankItem = MaterialBank::with('subject')->findOrFail($request->material_bank_id);
        $user = Auth::user();

        $subjectId = $bankItem->subject_id;
        if (!$subjectId) {
            $firstSubj = $user->subjects()->first() ?? Subject::first();
            $subjectId = $firstSubj ? $firstSubj->id : null;
        }

        if ($subjectId && $user->subjects()->exists() && !$user->subjects()->where('subjects.id', $subjectId)->exists()) {
            return back()->withErrors(['material_bank_id' => 'Anda tidak berhak memperbarui materi untuk mata pelajaran ini.'])->withInput();
        }

        // Handle Document copying from Bank Materi
        $documentPath = $material->document_path;
        if ($bankItem->document_path) {
            if ($material->document_path && $material->document_path !== $bankItem->document_path && Storage::disk('public')->exists($material->document_path)) {
                Storage::disk('public')->delete($material->document_path);
            }

            if (Storage::disk('public')->exists($bankItem->document_path)) {
                $filename = basename($bankItem->document_path);
                $documentPath = 'materials/' . Str::random(20) . '_' . $filename;
                Storage::disk('public')->copy($bankItem->document_path, $documentPath);
            }
        } else {
            if ($material->document_path && Storage::disk('public')->exists($material->document_path)) {
                Storage::disk('public')->delete($material->document_path);
            }
            $documentPath = null;
        }

        $material->title = $bankItem->title;
        $material->content_type = $bankItem->content_type ?? 'text';
        $material->content = $bankItem->content;
        $material->document_path = $documentPath;
        $material->video_url = $bankItem->video_url;
        $material->order = $request->order ?? 0;
        $material->subject_id = $subjectId;
        $material->class_id = $request->class_id;
        $material->save();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi "' . $material->title . '" berhasil diperbarui.');
    }

    public function destroy(Material $material): RedirectResponse
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $material->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        if ($material->document_path) {
            Storage::disk('public')->delete($material->document_path);
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi pembelajaran berhasil dihapus.');
    }

    public function storeComment(Request $request, Material $material): RedirectResponse
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $material->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:material_discussions,id'],
        ]);

        $parent = null;
        $parentId = null;
        if ($request->filled('parent_id')) {
            $parent = MaterialDiscussion::find($request->parent_id);
            if ($parent && $parent->material_id === $material->id) {
                $parentId = $parent->parent_id ?? $parent->id;
            } else {
                $parent = null;
            }
        }

        $discussion = MaterialDiscussion::create([
            'material_id' => $material->id,
            'parent_id' => $parentId,
            'user_id' => $user->id,
            'comment' => trim($request->comment),
        ]);

        event(new DiscussionCommentSent($discussion));

        $commentSnippet = Str::limit($request->comment, 60);
        $studentTargetUrl = route('student.materials.show', $material) . '#discussion-item-' . $discussion->id;
        $notifiedUserIds = [$user->id];

        // 1. Notifikasi ke pembuat komentar yang dibalas oleh guru
        if ($parent && $parent->user_id !== $user->id) {
            Notification::create([
                'user_id' => $parent->user_id,
                'type' => 'comment',
                'title' => 'Balasan Guru: ' . $material->title,
                'message' => 'Guru ' . $user->name . ' membalas komentar Anda di materi "' . $material->title . '": "' . $commentSnippet . '"',
                'related_url' => $studentTargetUrl,
                'is_read' => false,
            ]);
            $notifiedUserIds[] = $parent->user_id;
        }

        // 2. Notifikasi ke seluruh siswa peserta diskusi lainnya
        $previousParticipantIds = MaterialDiscussion::where('material_id', $material->id)
            ->whereNotIn('user_id', $notifiedUserIds)
            ->pluck('user_id')
            ->unique();

        foreach ($previousParticipantIds as $participantId) {
            Notification::create([
                'user_id' => $participantId,
                'type' => 'comment',
                'title' => 'Balasan Guru: ' . $material->title,
                'message' => 'Guru ' . $user->name . ' memberikan tanggapan di diskusi materi "' . $material->title . '": "' . $commentSnippet . '"',
                'related_url' => $studentTargetUrl,
                'is_read' => false,
            ]);
        }

        $flashMsg = $parentId ? 'Tanggapan balasan guru berhasil dikirim.' : 'Pesan diskusi guru berhasil diterbitkan.';

        return redirect()->route('admin.materials.show', $material)
            ->with('success', $flashMsg);
    }

    public function destroyComment(Material $material, MaterialDiscussion $discussion): RedirectResponse
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $material->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        if ($discussion->material_id !== $material->id) {
            abort(404);
        }

        $discussion->replies()->delete();
        $discussion->delete();

        return back()->with('success', 'Komentar diskusi berhasil dihapus.');
    }
}


