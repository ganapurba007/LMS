<?php

namespace App\Http\Controllers\Admin;

use App\Events\DiscussionCommentSent;
use App\Events\MaterialCreated;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialDiscussion;
use App\Models\MaterialProgress;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with(['subject', 'schoolClass', 'instructor'])
            ->withCount('discussions')
            ->latest()
            ->paginate(10);

        return view('admin.materials.index', compact('materials'));
    }

    public function show(Material $material)
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $material->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $material->load([
            'subject',
            'schoolClass',
            'instructor',
            'discussions',
            'rootDiscussions' => function ($q) {
                $q->with(['user.role', 'replies.user.role'])->latest();
            },
        ]);

        $totalStudents = User::where('class_id', $material->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->count();

        $completedStudentsCount = MaterialProgress::where('material_id', $material->id)
            ->where('is_completed', true)
            ->count();

        return view('admin.materials.show', compact('material', 'totalStudents', 'completedStudentsCount'));
    }

    public function create()
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.materials.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'content_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg', 'max:20480'],
            'video_url' => ['nullable', 'url'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak membuat materi untuk mata pelajaran ini.'])->withInput();
        }

        $documentPath = null;
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('materials', 'public');
        }

        // Determine content_type for backward database compatibility
        $contentType = $request->content_type ?? 'text';
        if ($request->video_url && !$request->content && !$documentPath) {
            $contentType = 'youtube';
        } elseif ($documentPath && !$request->content && !$request->video_url) {
            $contentType = 'document';
        }

        $material = new Material();
        $material->title = $request->title;
        $material->content_type = $contentType;
        $material->content = $request->content;
        $material->document_path = $documentPath;
        $material->video_url = $request->video_url;
        $material->order = $request->order ?? 0;
        $material->subject_id = $request->subject_id;
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
            ->with('success', 'Materi pembelajaran berhasil ditambahkan dan notifikasi realtime dikirim.');
    }

    public function edit(Material $material)
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.materials.edit', compact('material', 'subjects', 'classes'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'content_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg', 'max:20480'],
            'video_url' => ['nullable', 'url'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak mengedit materi untuk mata pelajaran ini.'])->withInput();
        }

        if ($request->hasFile('document_file')) {
            if ($material->document_path) {
                Storage::disk('public')->delete($material->document_path);
            }
            $material->document_path = $request->file('document_file')->store('materials', 'public');
        }

        $contentType = $request->content_type ?? $material->content_type ?? 'text';
        if ($request->video_url && !$request->content && !$material->document_path) {
            $contentType = 'youtube';
        } elseif ($material->document_path && !$request->content && !$request->video_url) {
            $contentType = 'document';
        }

        $material->title = $request->title;
        $material->content_type = $contentType;
        $material->content = $request->content;
        $material->video_url = $request->video_url;
        $material->order = $request->order ?? 0;
        $material->subject_id = $request->subject_id;
        $material->class_id = $request->class_id;
        $material->save();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        if ($material->document_path) {
            Storage::disk('public')->delete($material->document_path);
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi pembelajaran berhasil dihapus.');
    }

    public function storeComment(Request $request, Material $material)
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
            'comment' => $request->comment,
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

        $flashMsg = $parentId ? 'Tanggapan/balasan guru berhasil dikirim ke siswa.' : 'Pesan diskusi guru berhasil diterbitkan.';

        return redirect()->route('admin.materials.show', $material)
            ->with('success', $flashMsg);
    }

    public function destroyComment(Material $material, MaterialDiscussion $discussion)
    {
        $user = Auth::user();
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $material->subject_id)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        if ($discussion->material_id !== $material->id) {
            abort(404);
        }

        // Hapus balasan jika komentar adalah parent
        $discussion->replies()->delete();
        $discussion->delete();

        return back()->with('success', 'Komentar diskusi berhasil dihapus.');
    }
}

