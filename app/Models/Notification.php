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
        $url = $this->related_url;
        if (empty($url)) {
            $user = auth()->user();
            return ($user && $user->isGuru()) ? route('admin.dashboard') : route('dashboard');
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        // If path contains specific ID (e.g. /student/assignments/1 or /student/materials/1 or /student/quizzes/4)
        if (preg_match('#/(student|admin)/[a-z]+/\d+#i', $path, $matches)) {
            $cleanPath = ltrim($matches[0], '/');
            $fullUrl = url($cleanPath);
            if (isset($parsed['query'])) {
                $fullUrl .= '?' . $parsed['query'];
            }
            if (isset($parsed['fragment'])) {
                $fullUrl .= '#' . $parsed['fragment'];
            }
            return $fullUrl;
        }

        // Fast fallback resolution if URL is generic (e.g. /student/assignments)
        $user = auth()->user();

        if ($this->type === 'new_assignment' && $user && $user->isSiswa()) {
            if (preg_match('#/assignments/(\d+)#', $url, $m)) {
                return route('student.assignments.show', $m[1]);
            }
            $latestId = Assignment::where('class_id', $user->class_id)->latest('id')->value('id');
            if ($latestId) {
                return route('student.assignments.show', $latestId);
            }
            return route('student.assignments.index');
        }

        if ($this->type === 'new_material' && $user && $user->isSiswa()) {
            if (preg_match('#/materials/(\d+)#', $url, $m)) {
                return route('student.materials.show', $m[1]);
            }
            $latestId = Material::where('class_id', $user->class_id)->latest('id')->value('id');
            if ($latestId) {
                return route('student.materials.show', $latestId);
            }
            return route('student.materials.index');
        }

        if ($this->type === 'new_quiz' && $user && $user->isSiswa()) {
            if (preg_match('#/quizzes/(\d+)#', $url, $m)) {
                return route('student.quizzes.show', $m[1]);
            }
            $latestId = Quiz::where('class_id', $user->class_id)->latest('id')->value('id');
            if ($latestId) {
                return route('student.quizzes.show', $latestId);
            }
            return route('student.quizzes.index');
        }

        if ($this->type === 'comment') {
            $fragment = '#discussion-list';
            if (preg_match('/(#discussion-[a-zA-Z0-9_-]+)/', $url, $fragMatches)) {
                $fragment = $fragMatches[1];
            }
            if (preg_match('#/materials/(\d+)#', $url, $m)) {
                return route('student.materials.show', $m[1]) . $fragment;
            }
            $latestId = ($user && $user->isSiswa())
                ? Material::where('class_id', $user->class_id)->latest('id')->value('id')
                : Material::latest('id')->value('id');
            if ($latestId) {
                return route('student.materials.show', $latestId) . $fragment;
            }
            return route('student.materials.index');
        }

        if (preg_match('#/(student|admin)/.*#', $path, $matches)) {
            $cleanPath = ltrim($matches[0], '/');
            $fullUrl = url($cleanPath);
            if (isset($parsed['query'])) {
                $fullUrl .= '?' . $parsed['query'];
            }
            if (isset($parsed['fragment'])) {
                $fullUrl .= '#' . $parsed['fragment'];
            }
            return $fullUrl;
        }

        if (str_starts_with($url, '/')) {
            return url(ltrim($url, '/'));
        }

        return $url;
    }
}
