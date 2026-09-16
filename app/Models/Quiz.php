<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'points_per_question',
        'deadline',
        'duration_minutes',
        'subject_id',
        'class_id',
        'instructor_id',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'points_per_question' => 'integer',
            'duration_minutes' => 'integer',
        ];
    }

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

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Alias for attempts() relation
     */
    public function quizAttempts(): HasMany
    {
        return $this->attempts();
    }

    /**
     * Total scorable items / question units.
     * Multiple Choice & True/False count as 1 item.
     * Matching (Menjodohkan) counts as 1 item per option/pair.
     */
    public function getTotalQuestionsCountAttribute(): int
    {
        if ($this->relationLoaded('questions')) {
            $this->questions->loadMissing('options');
        } else {
            $this->load('questions.options');
        }
        $count = 0;
        foreach ($this->questions as $q) {
            if ($q->isMatching()) {
                $count += $q->options->count();
            } else {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * Alias for total scorable items
     */
    public function getTotalScorableItemsAttribute(): int
    {
        return $this->total_questions_count;
    }

    /**
     * Get summary text of question types present in the quiz
     */
    public function getQuestionTypesSummaryAttribute(): string
    {
        if (!$this->relationLoaded('questions')) {
            $this->load('questions');
        }
        if ($this->questions->isEmpty()) {
            return 'Pilihan Ganda';
        }

        $types = $this->questions->map(function ($q) {
            if ($q->isMatching()) return 'Menjodohkan';
            if ($q->isTrueFalse()) return 'Benar / Salah';
            return 'Pilihan Ganda';
        })->unique();

        if ($types->count() > 1) {
            return 'Campuran (' . $types->implode(', ') . ')';
        }

        return $types->first() ?? 'Pilihan Ganda';
    }

    /**
     * Format duration into "X Jam Y Menit Z Detik"
     */
    public function getFormattedDurationAttribute(): string
    {
        $totalMinutes = (int)($this->duration_minutes ?? 0);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $seconds = 0;

        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours} Jam";
        }
        if ($minutes > 0 || $hours > 0) {
            $parts[] = "{$minutes} Menit";
        }
        $parts[] = "{$seconds} Detik";

        return implode(' ', $parts);
    }

    /**
     * Format duration into "HH:MM:SS" (e.g. 00:30:00 or 01:30:00)
     */
    public function getDurationHmsAttribute(): string
    {
        $totalMinutes = (int)($this->duration_minutes ?? 0);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $seconds = 0;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}


