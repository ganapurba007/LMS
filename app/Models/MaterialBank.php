<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialBank extends Model
{
    use HasFactory;

    protected $table = 'material_banks';

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'title',
        'content_type',
        'content',
        'document_path',
        'video_url',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
