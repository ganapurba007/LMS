<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content_type',
        'content',
        'document_path',
        'video_url',
        'order',
        'subject_id',
        'class_id',
        'instructor_id',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(MaterialDiscussion::class);
    }

    public function rootDiscussions(): HasMany
    {
        return $this->hasMany(MaterialDiscussion::class)->whereNull('parent_id');
    }
}
