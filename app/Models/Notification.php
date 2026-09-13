<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolve target URL dynamically to prevent 404 on custom host/domain
     * and redirect directly to the specific resource (e.g. assignment detail).
     */
    public function getResolvedUrlAttribute(): string
    {
        $user = $this->user ?? auth()->user();

        // 1. Tangani notifikasi tugas siswa
        if ($this->type === 'new_assignment' && $user && $user->isSiswa()) {
            if ($this->related_url && preg_match('#/assignments/(\d+)#', $this->related_url, $matches)) {
                $assignment = Assignment::where('id', $matches[1])
                    ->where('class_id', $user->class_id)
                    ->first();
                if ($assignment) {
                    return route('student.assignments.show', $assignment);
                }
            }

            $latestAssignment = Assignment::where('class_id', $user->class_id)->latest()->first();
            if ($latestAssignment) {
                return route('student.assignments.show', $latestAssignment);
            }

            return route('student.assignments.index');
        }

        // 2. Tangani notifikasi materi siswa
        if ($this->type === 'new_material' && $user && $user->isSiswa()) {
            if ($this->related_url && preg_match('#/materials/(\d+)#', $this->related_url, $matches)) {
                $material = Material::where('id', $matches[1])
                    ->where('class_id', $user->class_id)
                    ->first();
                if ($material) {
                    return route('student.materials.show', $material);
                }
            }

            $latestMaterial = Material::where('class_id', $user->class_id)->latest()->first();
            if ($latestMaterial) {
                return route('student.materials.show', $latestMaterial);
            }

            return route('student.materials.index');
        }

        // 3. Tangani notifikasi kuis siswa
        if ($this->type === 'new_quiz' && $user && $user->isSiswa()) {
            if ($this->related_url && preg_match('#/quizzes/(\d+)#', $this->related_url, $matches)) {
                $quiz = Quiz::where('id', $matches[1])
                    ->where('class_id', $user->class_id)
                    ->first();
                if ($quiz) {
                    return route('student.quizzes.show', $quiz);
                }
            }

            $latestQuiz = Quiz::where('class_id', $user->class_id)->latest()->first();
            if ($latestQuiz) {
                return route('student.quizzes.show', $latestQuiz);
            }

            return route('student.quizzes.index');
        }

        // 4. Tangani URL umum dengan menyesuaikan host aplikasi saat ini
        if ($this->related_url) {
            $parsed = parse_url($this->related_url);
            if (isset($parsed['path'])) {
                $path = $parsed['path'];
                if (preg_match('#/(student|admin)/.*#', $path, $matches)) {
                    $cleanPath = ltrim($matches[0], '/');
                    $url = url($cleanPath);
                    if (isset($parsed['query'])) {
                        $url .= '?' . $parsed['query'];
                    }
                    return $url;
                }
            }

            if (str_starts_with($this->related_url, '/')) {
                return url(ltrim($this->related_url, '/'));
            }

            if (!str_starts_with($this->related_url, 'http://') && !str_starts_with($this->related_url, 'https://')) {
                return url($this->related_url);
            }

            return $this->related_url;
        }

        return $user && $user->isGuru() ? route('admin.dashboard') : route('dashboard');
    }
}
