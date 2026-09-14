<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'quiz_id',
        'score',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Get randomized questions with randomized options deterministically seeded by this attempt.
     * This guarantees questions and options are randomized across different students,
     * while remaining consistent across page refreshes for the same attempt.
     */
    public function getOrderedQuestions()
    {
        $quiz = $this->quiz ?? Quiz::find($this->quiz_id);
        if (!$quiz) {
            return collect();
        }

        $quiz->loadMissing(['questions.options']);

        $shuffledQuestions = $quiz->questions->sortBy(function ($q) {
            return md5($this->id . '_question_' . $q->id);
        })->values();

        foreach ($shuffledQuestions as $q) {
            if ($q->isMultipleChoice()) {
                $shuffledOptions = $q->options->sortBy(function ($opt) {
                    return md5($this->id . '_option_' . $opt->id);
                })->values();
                $q->setRelation('options', $shuffledOptions);
            } elseif ($q->isTrueFalse()) {
                $tfOptions = $q->options->sortBy(function ($opt) {
                    return $opt->option_text === 'Benar' ? 0 : 1;
                })->values();
                $q->setRelation('options', $tfOptions);
            }
        }

        return $shuffledQuestions;
    }

    /**
     * Formatted duration spent on this attempt in "X Jam Y Menit Z Detik"
     */
    public function getDurationFormattedAttribute(): string
    {
        if (!$this->started_at) {
            return '-';
        }

        $endTime = $this->submitted_at ?? now();
        $totalSeconds = max(0, $this->started_at->diffInSeconds($endTime));

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

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
     * Formatted duration spent as HH:MM:SS
     */
    public function getDurationHmsAttribute(): string
    {
        if (!$this->started_at) {
            return '00:00:00';
        }

        $endTime = $this->submitted_at ?? now();
        $totalSeconds = max(0, $this->started_at->diffInSeconds($endTime));

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}


