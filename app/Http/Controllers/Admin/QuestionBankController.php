<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $type = $request->query('type');

        $questionBanks = QuestionBank::with('options')
            ->withCount('options')
            ->where('instructor_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where('question_text', 'like', "%{$search}%");
            })
            ->when($type, function ($query, $type) {
                $query->where('question_type', $type);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.question-banks.index', compact('questionBanks', 'search', 'type'));
    }

    public function create(): View
    {
        return view('admin.question-banks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Batch / Bulk Questions Processing
        if ($request->has('questions') && is_array($request->input('questions')) && count($request->input('questions')) > 0) {
            $createdCount = 0;

            DB::transaction(function () use ($request, &$createdCount) {
                foreach ($request->input('questions') as $qData) {
                    $qType = $qData['question_type'] ?? 'multiple_choice';
                    $qText = trim($qData['question_text'] ?? '');
                    if ($qText === '') {
                        if ($qType === 'matching') {
                            $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
                        } else {
                            continue;
                        }
                    }

                    if ($qType === 'true_false') {
                        $correctTf = $qData['correct_tf'] ?? ($qData['tf_correct_answer'] ?? 'Benar');
                        $qb = QuestionBank::create([
                            'instructor_id' => Auth::id(),
                            'question_text' => $qText,
                            'question_type' => 'true_false',
                        ]);

                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Benar',
                            'is_correct' => ($correctTf === 'Benar'),
                        ]);
                        QuestionBankOption::create([
                            'question_bank_id' => $qb->id,
                            'option_text' => 'Salah',
                            'is_correct' => ($correctTf === 'Salah'),
                        ]);
                        $createdCount++;
                    } elseif ($qType === 'matching') {
                        $pairs = $qData['pairs'] ?? ($qData['matching_pairs'] ?? []);
                        if (is_array($pairs) && count($pairs) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => Auth::id(),
                                'question_text' => $qText,
                                'question_type' => 'matching',
                            ]);

                            foreach ($pairs as $pair) {
                                if (!empty($pair['premise']) && !empty($pair['match'])) {
                                    QuestionBankOption::create([
                                        'question_bank_id' => $qb->id,
                                        'option_text' => trim($pair['premise']),
                                        'match_text' => trim($pair['match']),
                                        'is_correct' => true,
                                    ]);
                                }
                            }
                            $createdCount++;
                        }
                    } else {
                        // Multiple Choice
                        $options = $qData['options'] ?? [];
                        $filteredOptions = [];
                        $rawCorrectOpt = isset($qData['correct_option']) ? (int)$qData['correct_option'] : 0;
                        $correctOptText = $options[$rawCorrectOpt] ?? null;

                        foreach ($options as $idx => $optText) {
                            $t = trim($optText);
                            if ($t !== '') {
                                $filteredOptions[] = [
                                    'text' => $t,
                                    'is_correct' => ($idx === $rawCorrectOpt || ($correctOptText !== null && $t === trim($correctOptText)))
                                ];
                            }
                        }

                        if (count($filteredOptions) >= 2) {
                            $qb = QuestionBank::create([
                                'instructor_id' => Auth::id(),
                                'question_text' => $qText,
                                'question_type' => 'multiple_choice',
                            ]);

                            $hasCorrect = false;
                            foreach ($filteredOptions as $fOpt) {
                                if ($fOpt['is_correct']) {
                                    $hasCorrect = true;
                                    break;
                                }
                            }
                            if (!$hasCorrect) {
                                $filteredOptions[0]['is_correct'] = true;
                            }

                            foreach ($filteredOptions as $fOpt) {
                                QuestionBankOption::create([
                                    'question_bank_id' => $qb->id,
                                    'option_text' => $fOpt['text'],
                                    'is_correct' => $fOpt['is_correct'],
                                ]);
                            }
                            $createdCount++;
                        }
                    }
                }
            });

            if ($createdCount > 0) {
                return redirect()->route('admin.question-banks.index')->with('success', $createdCount . ' butir soal berhasil ditambahkan ke Bank Soal.');
            }

            return back()->with('error', 'Tidak ada butir soal yang valid untuk disimpan.');
        }

        // 2. Single Question Fallback
        $questionType = $request->input('question_type', 'multiple_choice');

        if ($questionType === 'true_false') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'correct_tf' => ['required', 'in:Benar,Salah'],
            ]);

            DB::transaction(function () use ($request) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);
            });
        } elseif ($questionType === 'matching') {
            $qText = trim($request->input('question_text', ''));
            if ($qText === '') {
                $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
            }
            $request->validate([
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.premise' => ['required', 'string'],
                'pairs.*.match' => ['required', 'string'],
            ]);

            DB::transaction(function () use ($request, $qText) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => $qText,
                    'question_type' => 'matching',
                ]);

                foreach ($request->pairs as $pair) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }
            });
        } else {
            $request->validate([
                'question_text' => ['required', 'string'],
                'options' => ['required', 'array', 'min:2'],
                'options.*' => ['required', 'string'],
                'correct_option' => ['required', 'integer', 'min:0'],
            ]);

            DB::transaction(function () use ($request) {
                $questionBank = QuestionBank::create([
                    'instructor_id' => Auth::id(),
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                foreach ($request->options as $index => $optionText) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($optionText),
                        'is_correct' => (int) $index === (int) $request->correct_option,
                    ]);
                }
            });
        }

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal berhasil ditambahkan ke Bank Soal.');
    }

    public function edit(QuestionBank $questionBank): View
    {
        $questionBank->load('options');

        return view('admin.question-banks.edit', compact('questionBank'));
    }

    public function update(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $questionType = $request->input('question_type', $questionBank->question_type ?? 'multiple_choice');

        if ($questionType === 'true_false') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'correct_tf' => ['required', 'in:Benar,Salah'],
            ]);

            DB::transaction(function () use ($request, $questionBank) {
                $questionBank->update([
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                $questionBank->options()->delete();

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $questionBank->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);
            });
        } elseif ($questionType === 'matching') {
            $qText = trim($request->input('question_text', ''));
            if ($qText === '') {
                $qText = 'Jodohkanlah item berikut dengan pasangannya yang benar:';
            }
            $request->validate([
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.premise' => ['required', 'string'],
                'pairs.*.match' => ['required', 'string'],
            ]);

            DB::transaction(function () use ($request, $questionBank, $qText) {
                $questionBank->update([
                    'question_text' => $qText,
                    'question_type' => 'matching',
                ]);

                $questionBank->options()->delete();

                foreach ($request->pairs as $pair) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }
            });
        } else {
            $request->validate([
                'question_text' => ['required', 'string'],
                'options' => ['required', 'array', 'min:2'],
                'options.*' => ['required', 'string'],
                'correct_option' => ['required', 'integer', 'min:0'],
            ]);

            DB::transaction(function () use ($request, $questionBank) {
                $questionBank->update([
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                $questionBank->options()->delete();

                foreach ($request->options as $index => $optionText) {
                    QuestionBankOption::create([
                        'question_bank_id' => $questionBank->id,
                        'option_text' => trim($optionText),
                        'is_correct' => (int) $index === (int) $request->correct_option,
                    ]);
                }
            });
        }

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal di Bank Soal berhasil diperbarui.');
    }

    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $questionBank->delete();

        return redirect()->route('admin.question-banks.index')->with('success', 'Soal di Bank Soal berhasil dihapus.');
    }
}

