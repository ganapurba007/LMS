<?php

namespace App\Http\Controllers\Admin;

use App\Events\MaterialCreated;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with(['subject', 'schoolClass', 'instructor'])
            ->latest()
            ->paginate(10);

        return view('admin.materials.index', compact('materials'));
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
}
