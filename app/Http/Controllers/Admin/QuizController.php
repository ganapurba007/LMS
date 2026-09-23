<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->query('search');
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');

        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $hasSubjectRestriction = $userSubjects->isNotEmpty();

        $subjects = $hasSubjectRestriction ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        $subjectsMap = $subjects->keyBy('id');
        $classesMap = $classes->keyBy('id');

        $query = Quiz::with(['instructor', 'questions.options'])
            ->withCount('questions');

        if ($hasSubjectRestriction) {
            $allowedSubjectIds = $userSubjects->pluck('id');
            $query->whereIn('subject_id', $allowedSubjectIds);
        }

        $quizzes = $query
            ->when($search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($subjectId, function ($q, $subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->when($classId, function ($q, $classId) {
                $q->where('class_id', $classId);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $quizzes->each(function ($quiz) use ($subjectsMap, $classesMap) {
            $quiz->setRelation('subject', $subjectsMap->get($quiz->subject_id));
            $quiz->setRelation('schoolClass', $classesMap->get($quiz->class_id));
        });

        return view('admin.quizzes.index', compact('quizzes', 'subjects', 'classes', 'search', 'subjectId', 'classId'));
    }

    public function create(): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.quizzes.create', compact('subjects', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'points_per_question' => ['required', 'integer', 'min:1'],
            'deadline' => ['required', 'date'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $request->subject_id)) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak membuat kuis untuk mata pelajaran ini.'])->withInput();
        }

        $quiz = new Quiz();
        $quiz->title = trim($request->title);
        $quiz->duration_minutes = $request->duration_minutes;
        $quiz->points_per_question = $request->points_per_question;
        $quiz->deadline = $request->deadline;
        $quiz->subject_id = $request->subject_id;
        $quiz->class_id = $request->class_id;
        $quiz->instructor_id = Auth::id();
        $quiz->save();

        // Buat notifikasi database untuk seluruh siswa di kelas terkait
        $students = User::where('class_id', $quiz->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })->get();

        $quiz->loadMissing('subject');
        $instructorName = Auth::user()->name ?? 'Guru Pengampu';
        $subjectName = $quiz->subject->name ?? 'Mata Pelajaran';
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'new_quiz',
                'title' => 'Kuis Baru: ' . $quiz->title,
                'message' => 'Guru ' . $instructorName . ' telah membuka kuis baru "' . $quiz->title . '" (' . $subjectName . ').',
                'related_url' => route('student.quizzes.show', $quiz),
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Kuis berhasil dibuat. Silakan tambahkan atau impor butir soal ke dalam kuis.');
    }

    public function show(Quiz $quiz): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $quiz->load(['questions.options', 'questions.questionBank', 'subject', 'schoolClass']);
        $questionBanks = QuestionBank::with('options')->get();

        return view('admin.quizzes.show', compact('quiz', 'questionBanks'));
    }

    public function edit(Quiz $quiz): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $subjects = $userSubjects->isNotEmpty() ? $userSubjects : Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.quizzes.edit', compact('quiz', 'subjects', 'classes'));
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'points_per_question' => ['required', 'integer', 'min:1'],
            'deadline' => ['required', 'date'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $request->subject_id)) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak mengedit kuis untuk mata pelajaran ini.'])->withInput();
        }

        $quiz->title = trim($request->title);
        $quiz->duration_minutes = $request->duration_minutes;
        $quiz->points_per_question = $request->points_per_question;
        $quiz->deadline = $request->deadline;
        $quiz->subject_id = $request->subject_id;
        $quiz->class_id = $request->class_id;
        $quiz->save();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Pengaturan kuis berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil dihapus.');
    }

    public function importQuestions(Request $request, Quiz $quiz): RedirectResponse
    {
        $request->validate([
            'question_bank_ids' => ['required', 'array'],
            'question_bank_ids.*' => ['exists:question_bank,id'],
        ]);

        DB::transaction(function () use ($request, $quiz) {
            foreach ($request->question_bank_ids as $qbId) {
                $exists = $quiz->questions()->where('question_bank_id', $qbId)->exists();
                if ($exists) {
                    continue;
                }

                $qb = QuestionBank::with('options')->find($qbId);
                if (!$qb) {
                    continue;
                }

                $qq = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_bank_id' => $qb->id,
                    'question_text' => $qb->question_text,
                    'question_type' => $qb->question_type ?? 'multiple_choice',
                ]);

                foreach ($qb->options as $opt) {
                    QuizQuestionOption::create([
                        'quiz_question_id' => $qq->id,
                        'option_text' => $opt->option_text,
                        'match_text' => $opt->match_text,
                        'is_correct' => $opt->is_correct,
                    ]);
                }
            }
        });

        return back()->with('success', 'Soal berhasil diimpor dari Bank Soal.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $instructorId = Auth::id() ?? $quiz->instructor_id;

        // 1. Batch / Bulk Questions Processing
        if ($request->has('questions') && is_array($request->input('questions')) && count($request->input('questions')) > 0) {
            $createdCount = 0;

            DB::transaction(function () use ($request, $quiz, &$createdCount, $instructorId) {
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

                        // Save to Question Bank
                        $qb = QuestionBank::create([
                            'instructor_id' => $instructorId,
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

                        // Save to Quiz Question
                        $qq = QuizQuestion::create([
                            'quiz_id' => $quiz->id,
                            'question_bank_id' => $qb->id,
                            'question_text' => $qText,
                            'question_type' => 'true_false',
                        ]);

                        QuizQuestionOption::create([
                            'quiz_question_id' => $qq->id,
                            'option_text' => 'Benar',
                            'is_correct' => ($correctTf === 'Benar'),
                        ]);
                        QuizQuestionOption::create([
                            'quiz_question_id' => $qq->id,
                            'option_text' => 'Salah',
                            'is_correct' => ($correctTf === 'Salah'),
                        ]);
                        $createdCount++;
                    } elseif ($qType === 'matching') {
                        $pairs = $qData['pairs'] ?? ($qData['matching_pairs'] ?? []);
                        if (is_array($pairs) && count($pairs) >= 2) {
                            // Save to Question Bank
                            $qb = QuestionBank::create([
                                'instructor_id' => $instructorId,
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

                            // Save to Quiz Question
                            $qq = QuizQuestion::create([
                                'quiz_id' => $quiz->id,
                                'question_bank_id' => $qb->id,
                                'question_text' => $qText,
                                'question_type' => 'matching',
                            ]);

                            foreach ($pairs as $pair) {
                                if (!empty($pair['premise']) && !empty($pair['match'])) {
                                    QuizQuestionOption::create([
                                        'quiz_question_id' => $qq->id,
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

                            // Save to Question Bank
                            $qb = QuestionBank::create([
                                'instructor_id' => $instructorId,
                                'question_text' => $qText,
                                'question_type' => 'multiple_choice',
                            ]);

                            foreach ($filteredOptions as $fOpt) {
                                QuestionBankOption::create([
                                    'question_bank_id' => $qb->id,
                                    'option_text' => $fOpt['text'],
                                    'is_correct' => $fOpt['is_correct'],
                                ]);
                            }

                            // Save to Quiz Question
                            $qq = QuizQuestion::create([
                                'quiz_id' => $quiz->id,
                                'question_bank_id' => $qb->id,
                                'question_text' => $qText,
                                'question_type' => 'multiple_choice',
                            ]);

                            foreach ($filteredOptions as $fOpt) {
                                QuizQuestionOption::create([
                                    'quiz_question_id' => $qq->id,
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
                return back()->with('success', $createdCount . ' butir soal kuis berhasil disimpan dan ditambahkan ke Bank Soal.');
            }

            return back()->with('error', 'Tidak ada butir soal yang valid untuk disimpan.');
        }

        // 2. Single Question Processing (Existing Fallback)
        if ($request->has('tf_correct_answer') && !$request->has('correct_tf')) {
            $request->merge(['correct_tf' => $request->input('tf_correct_answer')]);
        }
        if ($request->has('matching_pairs') && !$request->has('pairs')) {
            $request->merge(['pairs' => $request->input('matching_pairs')]);
        }

        $questionType = $request->input('question_type', 'multiple_choice');

        if ($questionType === 'true_false') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'correct_tf' => ['required', 'in:Benar,Salah'],
            ]);

            DB::transaction(function () use ($request, $quiz, $instructorId) {
                $qb = QuestionBank::create([
                    'instructor_id' => $instructorId,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $qb->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuestionBankOption::create([
                    'question_bank_id' => $qb->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);

                $qq = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_bank_id' => $qb->id,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'true_false',
                ]);

                QuizQuestionOption::create([
                    'quiz_question_id' => $qq->id,
                    'option_text' => 'Benar',
                    'is_correct' => ($request->correct_tf === 'Benar'),
                ]);

                QuizQuestionOption::create([
                    'quiz_question_id' => $qq->id,
                    'option_text' => 'Salah',
                    'is_correct' => ($request->correct_tf === 'Salah'),
                ]);
            });
        } elseif ($questionType === 'matching') {
            $request->validate([
                'question_text' => ['required', 'string'],
                'pairs' => ['required', 'array', 'min:2'],
                'pairs.*.premise' => ['required', 'string'],
                'pairs.*.match' => ['required', 'string'],
            ]);

            DB::transaction(function () use ($request, $quiz, $instructorId) {
                $qb = QuestionBank::create([
                    'instructor_id' => $instructorId,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'matching',
                ]);

                foreach ($request->pairs as $pair) {
                    QuestionBankOption::create([
                        'question_bank_id' => $qb->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }

                $qq = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_bank_id' => $qb->id,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'matching',
                ]);

                foreach ($request->pairs as $pair) {
                    QuizQuestionOption::create([
                        'quiz_question_id' => $qq->id,
                        'option_text' => trim($pair['premise']),
                        'match_text' => trim($pair['match']),
                        'is_correct' => true,
                    ]);
                }
            });
        } else {
            // Default: Multiple Choice
            $request->validate([
                'question_text' => ['required', 'string'],
                'options' => ['required', 'array', 'min:2'],
                'options.*' => ['required', 'string'],
                'correct_option' => ['required', 'integer', 'min:0'],
            ]);

            DB::transaction(function () use ($request, $quiz, $instructorId) {
                $qb = QuestionBank::create([
                    'instructor_id' => $instructorId,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                foreach ($request->options as $index => $optionText) {
                    QuestionBankOption::create([
                        'question_bank_id' => $qb->id,
                        'option_text' => trim($optionText),
                        'is_correct' => ((int) $index === (int) $request->correct_option),
                    ]);
                }

                $qq = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_bank_id' => $qb->id,
                    'question_text' => trim($request->question_text),
                    'question_type' => 'multiple_choice',
                ]);

                foreach ($request->options as $index => $optionText) {
                    QuizQuestionOption::create([
                        'quiz_question_id' => $qq->id,
                        'option_text' => trim($optionText),
                        'is_correct' => ((int) $index === (int) $request->correct_option),
                    ]);
                }
            });
        }

        return back()->with('success', 'Soal kuis baru berhasil ditambahkan dan disimpan ke Bank Soal.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question): RedirectResponse
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(403);
        }

        $question->delete();

        return back()->with('success', 'Soal kuis berhasil dihapus.');
    }

    public function students(Quiz $quiz): View
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        // Load quiz relations — schoolClass is loaded here once
        $quiz->load(['subject', 'schoolClass', 'instructor']);

        // Load students with role only; schoolClass is already known from $quiz->schoolClass
        // and assigned via setRelation() to avoid a duplicate SELECT classes query.
        $students = User::where('class_id', $quiz->class_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'siswa');
            })
            ->with('role')
            ->orderBy('name')
            ->get();

        // Assign the already-loaded schoolClass from quiz to each student — zero extra queries
        $quizClass = $quiz->schoolClass;
        $students->each(fn($s) => $s->setRelation('schoolClass', $quizClass));

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->get()
            ->keyBy('student_id');

        // Analytics: Compute per-question statistics across all submitted attempts
        $submittedAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->whereNotNull('submitted_at')
            ->get();
        $submittedCount = $submittedAttempts->count();

        $quizQuestions = $quiz->questions()->with('options')->get();
        $attemptIds = $submittedAttempts->pluck('id');
        $allAnswers = \App\Models\QuizAnswer::whereIn('quiz_attempt_id', $attemptIds)
            ->get()
            ->groupBy('quiz_question_id');

        $questionStats = $quizQuestions->map(function ($question) use ($submittedAttempts, $allAnswers, $submittedCount) {
            $answers = $allAnswers->get($question->id, collect());
            $correctStudentsCount = 0;
            $incorrectStudentsCount = 0;

            foreach ($submittedAttempts as $att) {
                $ans = $answers->firstWhere('quiz_attempt_id', $att->id);
                if ($question->isMatching()) {
                    $pairsAnswer = is_array($ans?->answer_data) ? $ans->answer_data : [];
                    $totalPairs = $question->options->count();
                    $correctPairs = 0;
                    if ($totalPairs > 0 && !empty($pairsAnswer)) {
                        foreach ($question->options as $opt) {
                            if (isset($pairsAnswer[$opt->id]) && trim((string)$pairsAnswer[$opt->id]) === trim((string)$opt->match_text)) {
                                $correctPairs++;
                            }
                        }
                    }
                    if ($totalPairs > 0 && $correctPairs === $totalPairs) {
                        $correctStudentsCount++;
                    } else {
                        $incorrectStudentsCount++;
                    }
                } else {
                    $selectedOptId = $ans?->selected_option_id;
                    $correctOpt = $question->options->firstWhere('is_correct', true);
                    if ($selectedOptId && $correctOpt && $selectedOptId == $correctOpt->id) {
                        $correctStudentsCount++;
                    } else {
                        $incorrectStudentsCount++;
                    }
                }
            }

            $correctPct = $submittedCount > 0 ? round(($correctStudentsCount / $submittedCount) * 100, 1) : 0;
            $incorrectPct = $submittedCount > 0 ? round(($incorrectStudentsCount / $submittedCount) * 100, 1) : 0;

            $correctAnswerText = '';
            if ($question->isMatching()) {
                $correctAnswerText = $question->options->map(fn($o) => $o->option_text . ' → ' . $o->match_text)->implode(', ');
            } else {
                $correctOpt = $question->options->firstWhere('is_correct', true);
                $correctAnswerText = $correctOpt ? $correctOpt->option_text : '-';
            }

            return [
                'question' => $question,
                'correct_count' => $correctStudentsCount,
                'incorrect_count' => $incorrectStudentsCount,
                'correct_pct' => $correctPct,
                'incorrect_pct' => $incorrectPct,
                'correct_answer_text' => $correctAnswerText,
            ];
        });

        // 5 Soal terbanyak dijawab salah
        $mostIncorrectQuestions = $submittedCount > 0 
            ? $questionStats->sortByDesc('incorrect_count')->take(5)->values()
            : collect();

        // 5 Soal terbanyak dijawab benar
        $mostCorrectQuestions = $submittedCount > 0 
            ? $questionStats->sortByDesc('correct_count')->take(5)->values()
            : collect();

        return view('admin.quizzes.students', compact(
            'quiz', 'students', 'attempts', 'mostIncorrectQuestions', 'mostCorrectQuestions', 'submittedCount'
        ));
    }

    public function getStudentAnswers(Quiz $quiz, User $student): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke kuis ini.'], 403);
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Siswa belum memiliki riwayat pengerjaan kuis ini.'], 404);
        }

        $questions = $quiz->questions()->with('options')->get();
        $answers = $attempt->answers()->get()->keyBy('quiz_question_id');

        $details = [];
        foreach ($questions as $index => $question) {
            $ans = $answers->get($question->id);
            $isAnswered = !is_null($ans) && (!is_null($ans->selected_option_id) || !empty($ans->answer_data));

            $item = [
                'number' => $index + 1,
                'id' => $question->id,
                'question_text' => $question->formatted_question_text,
                'question_type' => $question->question_type ?? 'multiple_choice',
                'is_answered' => $isAnswered,
                'is_correct' => false,
                'selected_option_id' => $ans ? $ans->selected_option_id : null,
                'answer_data' => $ans ? $ans->answer_data : null,
                'options' => [],
            ];

            if ($question->isMatching()) {
                $pairsAnswer = is_array($ans?->answer_data) ? $ans->answer_data : [];
                $totalPairs = $question->options->count();
                $correctPairs = 0;
                $optionsList = [];
                foreach ($question->options as $opt) {
                    $studentPair = $pairsAnswer[$opt->id] ?? null;
                    $isPairCorrect = !is_null($studentPair) && trim((string)$studentPair) === trim((string)$opt->match_text);
                    if ($isPairCorrect) {
                        $correctPairs++;
                    }
                    $optionsList[] = [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'correct_match' => $opt->match_text,
                        'student_match' => $studentPair,
                        'is_pair_correct' => $isPairCorrect,
                    ];
                }
                $item['is_correct'] = ($totalPairs > 0 && $correctPairs === $totalPairs);
                $item['correct_pairs_count'] = $correctPairs;
                $item['total_pairs_count'] = $totalPairs;
                $item['options'] = $optionsList;
            } else {
                $correctOpt = $question->options->firstWhere('is_correct', true);
                $item['is_correct'] = ($ans && $ans->selected_option_id && $correctOpt && $ans->selected_option_id == $correctOpt->id);
                $optionsList = [];
                foreach ($question->options as $opt) {
                    $optionsList[] = [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'is_correct' => (bool)$opt->is_correct,
                        'is_selected' => ($ans && $ans->selected_option_id == $opt->id),
                    ];
                }
                $item['options'] = $optionsList;
            }

            $details[] = $item;
        }

        return response()->json([
            'student' => [
                'name' => $student->name,
                'email' => $student->email,
            ],
            'attempt' => [
                'score' => $attempt->score,
                'duration' => $attempt->duration_formatted,
                'started_at' => $attempt->started_at ? $attempt->started_at->format('d M Y, H:i') . ' WIB' : '-',
                'submitted_at' => $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') . ' WIB' : 'Belum Selesai',
            ],
            'questions' => $details,
        ]);
    }

    public function resetStudentAttempt(Quiz $quiz, User $student): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->loadMissing('subjects');
        }
        $userSubjects = $user ? $user->subjects : collect();
        if ($userSubjects->isNotEmpty() && !$userSubjects->contains('id', $quiz->subject_id)) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt) {
            return back()->with('error', 'Siswa "' . $student->name . '" belum memiliki riwayat pengerjaan kuis ini.');
        }

        DB::transaction(function () use ($attempt) {
            $attempt->answers()->delete();
            $attempt->delete();
        });

        $msg = 'Riwayat pengerjaan kuis untuk siswa "' . $student->name . '" berhasil direset.';
        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            $msg .= ' Catatan: Batas waktu (deadline) kuis ini sudah berakhir. Silakan perpanjang deadline kuis pada menu Edit Kuis jika ingin siswa mengerjakan ulang.';
        }

        return back()->with('success', $msg);
    }
}

