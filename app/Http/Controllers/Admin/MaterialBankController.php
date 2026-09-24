<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialBank;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MaterialBankController extends Controller
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

        $query = MaterialBank::with(['subject', 'instructor'])
            ->where('instructor_id', Auth::id());

        if ($hasSubjectRestriction) {
            $allowedSubjectIds = $userSubjects->pluck('id');
            $query->where(function ($q) use ($allowedSubjectIds) {
                $q->whereIn('subject_id', $allowedSubjectIds)
                  ->orWhereNull('subject_id');
            });
        }

        $materialBanks = $query
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

        return view('admin.material-banks.index', compact('materialBanks', 'subjects', 'search', 'subjectId'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();

        return view('admin.material-banks.create', compact('subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'content_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg', 'max:51200'],
            'document_chunk_path' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
        ]);

        $documentPath = null;
        if ($request->filled('document_chunk_path') && Storage::disk('public')->exists($request->document_chunk_path)) {
            $documentPath = $request->document_chunk_path;
        } elseif ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('material-banks', 'public');
        }

        $contentType = $request->content_type ?? 'text';
        if ($request->video_url && !$request->content && !$documentPath) {
            $contentType = 'youtube';
        } elseif ($documentPath && !$request->content && !$request->video_url) {
            $contentType = 'document';
        }

        MaterialBank::create([
            'instructor_id' => Auth::id(),
            'subject_id' => $request->subject_id,
            'title' => trim($request->title),
            'content_type' => $contentType,
            'content' => $request->content,
            'document_path' => $documentPath,
            'video_url' => $request->video_url,
        ]);

        return redirect()->route('admin.material-banks.index')
            ->with('success', 'Materi berhasil ditambahkan ke Bank Materi.');
    }

    public function edit(MaterialBank $materialBank): View
    {
        if ($materialBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke materi bank ini.');
        }

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();

        return view('admin.material-banks.edit', compact('materialBank', 'subjects'));
    }

    public function update(Request $request, MaterialBank $materialBank): RedirectResponse
    {
        if ($materialBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke materi bank ini.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'content_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg', 'max:51200'],
            'document_chunk_path' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
        ]);

        if ($request->filled('document_chunk_path') && Storage::disk('public')->exists($request->document_chunk_path)) {
            if ($materialBank->document_path && $materialBank->document_path !== $request->document_chunk_path) {
                Storage::disk('public')->delete($materialBank->document_path);
            }
            $materialBank->document_path = $request->document_chunk_path;
        } elseif ($request->hasFile('document_file')) {
            if ($materialBank->document_path) {
                Storage::disk('public')->delete($materialBank->document_path);
            }
            $materialBank->document_path = $request->file('document_file')->store('material-banks', 'public');
        }

        $contentType = $request->content_type ?? $materialBank->content_type ?? 'text';
        if ($request->video_url && !$request->content && !$materialBank->document_path) {
            $contentType = 'youtube';
        } elseif ($materialBank->document_path && !$request->content && !$request->video_url) {
            $contentType = 'document';
        }

        $materialBank->title = trim($request->title);
        $materialBank->subject_id = $request->subject_id;
        $materialBank->content_type = $contentType;
        $materialBank->content = $request->content;
        $materialBank->video_url = $request->video_url;
        $materialBank->save();

        return redirect()->route('admin.material-banks.index')
            ->with('success', 'Materi di Bank Materi berhasil diperbarui.');
    }

    public function destroy(MaterialBank $materialBank): RedirectResponse
    {
        if ($materialBank->instructor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke materi bank ini.');
        }

        if ($materialBank->document_path) {
            Storage::disk('public')->delete($materialBank->document_path);
        }

        $materialBank->delete();

        return redirect()->route('admin.material-banks.index')
            ->with('success', 'Materi di Bank Materi berhasil dihapus.');
    }

    public function getDetailJson(MaterialBank $materialBank): JsonResponse
    {
        if ($materialBank->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id' => $materialBank->id,
            'title' => $materialBank->title,
            'subject_id' => $materialBank->subject_id,
            'content_type' => $materialBank->content_type,
            'content' => $materialBank->content,
            'document_path' => $materialBank->document_path,
            'document_url' => $materialBank->document_path ? asset('storage/' . $materialBank->document_path) : null,
            'document_filename' => $materialBank->document_path ? basename($materialBank->document_path) : null,
            'video_url' => $materialBank->video_url,
        ]);
    }
}
