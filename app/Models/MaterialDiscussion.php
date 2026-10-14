<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'parent_id',
        'user_id',
        'comment',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MaterialDiscussion::class, 'parent_id');
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaterialDiscussion::class, 'parent_id')->oldest();
    }

    public function reactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaterialDiscussionReaction::class, 'discussion_id');
    }

    public function isReactedBy(?User $user, string $type = 'thumbs_up'): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('reactions')) {
            return $this->reactions->contains(function ($r) use ($user, $type) {
                return $r->user_id === $user->id && $r->type === $type;
            });
        }

        return $this->reactions()->where('user_id', $user->id)->where('type', $type)->exists();
    }

    public function teacherReactions(): \Illuminate\Support\Collection
    {
        if ($this->relationLoaded('reactions')) {
            return $this->reactions->filter(function ($r) {
                return $r->user && $r->user->isGuru();
            });
        }

        return $this->reactions()->whereHas('user.role', function ($q) {
            $q->where('name', 'guru');
        })->with('user')->get();
    }

    public function hasTeacherReaction(): bool
    {
        return $this->teacherReactions()->isNotEmpty();
    }
}
