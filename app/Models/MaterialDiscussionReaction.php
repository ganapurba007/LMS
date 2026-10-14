<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialDiscussionReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'type',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(MaterialDiscussion::class, 'discussion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
