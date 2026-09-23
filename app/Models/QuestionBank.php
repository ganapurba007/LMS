<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_bank';

    protected $fillable = [
        'instructor_id',
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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionBankOption::class, 'question_bank_id');
    }

    public function getFormattedQuestionTextAttribute(): string
    {
        return static::renderFormattedText($this->question_text);
    }

    public static function renderFormattedText(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $text = trim($content);

        // 1. Render Markdown Images: ![alt](url)
        $text = preg_replace_callback('/!\[(.*?)\]\((.*?)\)/s', function ($m) {
            $alt = htmlspecialchars($m[1] ?: 'Gambar Soal', ENT_QUOTES, 'UTF-8');
            $url = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
            return "\n<div class=\"text-center my-2.5 q-media-wrap\"><img src=\"{$url}\" alt=\"{$alt}\" class=\"img-fluid rounded border shadow-xs\" style=\"max-height: 320px; object-fit: contain;\"></div>\n";
        }, $text);

        // 2. Render Markdown Tables: | col1 | col2 |
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $outputLines = [];
        $tableBuffer = [];

        $flushTable = function () use (&$tableBuffer, &$outputLines) {
            if (empty($tableBuffer)) return;

            $isHeader = true;
            $inBody = false;

            $tableHtml = '<div class="table-responsive my-2.5"><table class="table table-bordered table-sm table-striped align-middle mb-0">';

            foreach ($tableBuffer as $idx => $rowStr) {
                $trimmed = trim($rowStr);
                if (preg_match('/^\|?\s*[-:\s|]+\s*\|?$/', $trimmed)) {
                    continue;
                }

                $cells = explode('|', trim($trimmed, '|'));
                if ($isHeader && $idx === 0) {
                    $tableHtml .= '<thead class="table-light"><tr>';
                    foreach ($cells as $cell) {
                        $tableHtml .= '<th class="text-center fw-bold text-nowrap">' . trim($cell) . '</th>';
                    }
                    $tableHtml .= '</tr></thead><tbody>';
                    $isHeader = false;
                    $inBody = true;
                } else {
                    if (!$inBody) {
                        $tableHtml .= '<tbody>';
                        $inBody = true;
                    }
                    $tableHtml .= '<tr>';
                    foreach ($cells as $cell) {
                        $tableHtml .= '<td class="text-center">' . trim($cell) . '</td>';
                    }
                    $tableHtml .= '</tr>';
                }
            }

            if ($inBody) {
                $tableHtml .= '</tbody>';
            }
            $tableHtml .= '</table></div>';

            $outputLines[] = $tableHtml;
            $tableBuffer = [];
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '|') && str_ends_with($trimmed, '|')) {
                $tableBuffer[] = $trimmed;
            } else {
                $flushTable();
                $outputLines[] = $line;
            }
        }
        $flushTable();

        $finalResult = '';
        $inHtmlBlock = false;

        foreach ($outputLines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if (!$inHtmlBlock) {
                    $finalResult .= '<div class="my-1"></div>';
                }
                continue;
            }

            if (str_starts_with($trimmed, '<div') || str_starts_with($trimmed, '<table') || str_starts_with($trimmed, '<p') || str_starts_with($trimmed, '<ul') || str_starts_with($trimmed, '<ol')) {
                $finalResult .= "\n" . $line . "\n";
                $inHtmlBlock = true;
                if (str_ends_with($trimmed, '</div>') || str_ends_with($trimmed, '</table>') || str_ends_with($trimmed, '</p>') || str_ends_with($trimmed, '</ul>') || str_ends_with($trimmed, '</ol>')) {
                    $inHtmlBlock = false;
                }
            } elseif (str_ends_with($trimmed, '</div>') || str_ends_with($trimmed, '</table>') || str_ends_with($trimmed, '</p>') || str_ends_with($trimmed, '</ul>') || str_ends_with($trimmed, '</ol>')) {
                $finalResult .= "\n" . $line . "\n";
                $inHtmlBlock = false;
            } else {
                if ($inHtmlBlock) {
                    $finalResult .= $line . "\n";
                } else {
                    $finalResult .= '<div>' . $line . '</div>';
                }
            }
        }

        return trim($finalResult);
    }
}
