<?php

namespace App\Http\Controllers\Student;

use App\Events\DiscussionCommentSent;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialDiscussion;
use App\Models\MaterialProgress;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $classId = $user->class_id;

        // Base query untuk materi kelas siswa
        $query = Material::where('class_id', $classId)
            ->with(['subject', 'instructor', 'schoolClass']);

        // Filter pencarian judul, mata pelajaran, atau guru pengampu
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
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

        // ID materi yang telah diselesaikan oleh siswa saat ini
        $completedIds = MaterialProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        // Filter berdasarkan status penyelesaian
        if ($request->input('status') === 'completed') {
            $query->whereIn('id', $completedIds);
        } elseif ($request->input('status') === 'uncompleted') {
            $query->whereNotIn('id', $completedIds);
        }

        $materials = $query->orderBy('order', 'asc')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Hitung statistik progres belajar materi siswa
        $allClassMaterialIds = Material::where('class_id', $classId)->pluck('id');
        $totalMaterials = $allClassMaterialIds->count();
        $completedCount = count(array_intersect($completedIds, $allClassMaterialIds->toArray()));
        $uncompletedCount = max(0, $totalMaterials - $completedCount);
        $progressPercent = $totalMaterials > 0 ? round(($completedCount / $totalMaterials) * 100) : 0;

        // Daftar mata pelajaran yang memiliki materi di kelas ini
        $subjects = \App\Models\Subject::whereIn('id', Material::where('class_id', $classId)->pluck('subject_id')->unique())
            ->orderBy('name')
            ->get();

        return view('student.materials.index', compact(
            'materials',
            'completedIds',
            'totalMaterials',
            'completedCount',
            'uncompletedCount',
            'progressPercent',
            'subjects'
        ));
    }

    public function show(Material $material)
    {
        $user = Auth::user();
        if ($user->isSiswa() && $material->class_id !== $user->class_id) {
            abort(403, 'Materi ini tidak ditujukan untuk kelas Anda.');
        }

        $material->load([
            'subject',
            'instructor',
            'discussions',
            'rootDiscussions' => function ($q) {
                $q->with(['user.role', 'replies.user.role'])->latest();
            },
        ]);

        $isCompleted = MaterialProgress::where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->where('is_completed', true)
            ->exists();

        return view('student.materials.show', compact('material', 'isCompleted'));
    }

    public function toggleComplete(Material $material)
    {
        $user = Auth::user();
        if ($user->isSiswa() && $material->class_id !== $user->class_id) {
            abort(403);
        }

        $progress = MaterialProgress::firstOrNew([
            'user_id' => $user->id,
            'material_id' => $material->id,
        ]);

        $progress->is_completed = !$progress->is_completed;
        $progress->completed_at = $progress->is_completed ? now() : null;
        $progress->save();

        return back()->with('success', $progress->is_completed ? 'Materi berhasil ditandai selesai.' : 'Status penyelesaian materi dibatalkan.');
    }

    public function storeComment(Request $request, Material $material)
    {
        $user = Auth::user();
        if ($user->isSiswa() && $material->class_id !== $user->class_id) {
            abort(403);
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
                // Posisikan balasan di thread induk (1 level nesting agar rapi di seluruh layar)
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
        $targetUrl = route('student.materials.show', $material) . '#discussion-item-' . $discussion->id;

        $notifiedUserIds = [$user->id];

        // 1. Jika membalas komentar orang lain, kirim notifikasi langsung kepada pembuat komentar yang dibalas
        if ($parent && $parent->user_id !== $user->id) {
            $isGuruReply = $user->isGuru();
            Notification::create([
                'user_id' => $parent->user_id,
                'type' => 'comment',
                'title' => ($isGuruReply ? 'Balasan Guru: ' : 'Balasan Komentar: ') . $material->title,
                'message' => ($isGuruReply ? 'Guru ' : '') . $user->name . ' membalas komentar Anda di materi "' . $material->title . '": "' . $commentSnippet . '"',
                'related_url' => $targetUrl,
                'is_read' => false,
            ]);
            $notifiedUserIds[] = $parent->user_id;
        }

        // 2. Jika yang berkomentar adalah siswa dan guru belum ternotifikasi, beri tahu guru pengampu materi
        if ($user->isSiswa() && $material->instructor_id && !in_array($material->instructor_id, $notifiedUserIds)) {
            Notification::create([
                'user_id' => $material->instructor_id,
                'type' => 'comment',
                'title' => 'Diskusi Baru: ' . $material->title,
                'message' => $user->name . ' mengirim tanggapan di ruang diskusi materi "' . $material->title . '": "' . $commentSnippet . '"',
                'related_url' => $targetUrl,
                'is_read' => false,
            ]);
            $notifiedUserIds[] = $material->instructor_id;
        }

        // 3. Beri tahu peserta lain yang pernah berkomentar di materi ini (balasan diskusi)
        $previousParticipantIds = MaterialDiscussion::where('material_id', $material->id)
            ->whereNotIn('user_id', $notifiedUserIds)
            ->pluck('user_id')
            ->unique();

        foreach ($previousParticipantIds as $participantId) {
            Notification::create([
                'user_id' => $participantId,
                'type' => 'comment',
                'title' => ($user->isGuru() ? 'Balasan Guru: ' : 'Balasan Diskusi: ') . $material->title,
                'message' => ($user->isGuru() ? 'Guru ' : '') . $user->name . ' menanggapi diskusi pada materi "' . $material->title . '": "' . $commentSnippet . '"',
                'related_url' => $targetUrl,
                'is_read' => false,
            ]);
        }

        $flashMessage = $parentId ? 'Balasan komentar berhasil dikirim.' : 'Komentar diskusi berhasil dikirim.';

        return back()->with('success', $flashMessage);
    }
}
