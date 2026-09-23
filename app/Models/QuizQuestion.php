<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_bank_id',
        'question_text',
        'question_type',
    ];

    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice' || empty($this->question_type);
    }

    public function isTrueFalse(): bool
    {
        return $this->question_type === 'true_false';
    }

    public function isMatching(): bool
    {
        return $this->question_type === 'matching';
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizQuestionOption::class);
    }

    public function hasImage(): bool
    {
        return str_contains($this->question_text, '<img')
            || (bool) preg_match('/!\[.*?\]\(.*?\)/', $this->question_text);
    }

    public function hasTable(): bool
    {
        return str_contains($this->question_text, '<table')
            || (str_contains($this->question_text, '|') && (bool) preg_match('/\|.+?\|/', $this->question_text));
    }

    public function getFormattedQuestionTextAttribute(): string
    {
        return QuestionBank::renderFormattedText($this->question_text);
    }

    public function getSummaryTextAttribute(): string
    {
        return static::renderSummaryText($this->question_text);
    }

    public static function renderSummaryText(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $text = $content;

        $badgeTable = '<span class="badge bg-success-subtle text-success border border-success-subtle align-middle mx-1" style="font-size: 0.72rem; font-weight: 700;"><i class="ti ti-table me-0.5"></i> (tabel)</span>';
        $badgeImage = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle align-middle mx-1" style="font-size: 0.72rem; font-weight: 700;"><i class="ti ti-photo me-0.5"></i> (gambar)</span>';

        // 1. Replace HTML tables (wrapper div or standalone table)
        $text = preg_replace('/<div[^>]*class="[^"]*table-responsive[^"]*"[^>]*>[\s\S]*?<\/div>/i', ' ' . $badgeTable . ' ', $text);
        $text = preg_replace('/<table[\s\S]*?<\/table>/i', ' ' . $badgeTable . ' ', $text);

        // 2. Replace Markdown tables: rows starting and ending with |
        $text = preg_replace('/(?:^|\n)\s*\|.*\|[\r\n]+\s*\|[-:\s|]+\|[\r\n]+(?:\s*\|.*\|[\r\n]*)+/', ' ' . $badgeTable . ' ', $text);

        // 3. Replace Markdown Images
        $text = preg_replace('/!\[.*?\]\(.*?\)/s', ' ' . $badgeImage . ' ', $text);

        // 4. Replace HTML Images
        $text = preg_replace('/<img[^>]*\/?>/i', ' ' . $badgeImage . ' ', $text);

        // 5. Strip any other HTML tags except the badge span and i
        $text = strip_tags($text, '<span><i>');

        // 6. Normalize whitespace
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // 7. If text only had image/table and no other words
        $plain = trim(strip_tags($text));
        if ($plain === '') {
            $hasImg = str_contains($content, '<img') || preg_match('/!\[.*?\]\(.*?\)/', $content);
            $hasTbl = str_contains($content, '<table') || str_contains($content, '|');
            if ($hasImg && $hasTbl) {
                return 'Soal ' . $badgeImage . ' ' . $badgeTable;
            } elseif ($hasImg) {
                return 'Soal ' . $badgeImage;
            } elseif ($hasTbl) {
                return 'Soal ' . $badgeTable;
            }
        }

        return $text;
    }
}
