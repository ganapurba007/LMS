<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userClass = null;
        if ($user) {
            $user->loadMissing('schoolClass');
            $userClass = $user->schoolClass;
        }

        // Load all attempts for the current user once to eliminate duplicate queries
        $userAttemptsMap = QuizAttempt::where('student_id', $user->id)
            ->get()
            ->keyBy('quiz_id');

        if ($user->isGuru()) {
            $baseQuery = Quiz::where('instructor_id', $user->id);
        } else {
            $baseQuery = Quiz::where('class_id', $user->class_id);
        }

        $allClassQuizzes = (clone $baseQuery)->select('id', 'subject_id', 'class_id')->get();

        $totalQuizzes = $allClassQuizzes->count();
        $completedCount = 0;
        $inProgressCount = 0;
        $unattemptedCount = 0;
        $completedQuizIds = [];
        $inProgressQuizIds = [];
        $unattemptedQuizIds = [];

        foreach ($allClassQuizzes as $qItem) {
            $att = $userAttemptsMap->get($qItem->id);
            if ($att && $att->submitted_at) {
                $completedCount++;
                $completedQuizIds[] = $qItem->id;
            } elseif ($att) {
                $inProgressCount++;
                $inProgressQuizIds[] = $qItem->id;
            } else {
                $unattemptedCount++;
                $unattemptedQuizIds[] = $qItem->id;
            }
        }

        $query = (clone $baseQuery)->with(['subject', 'instructor', 'questions.options']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', function($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        // Filter status
        if ($request->input('status') === 'completed') {
            $query->whereIn('id', $completedQuizIds);
        } elseif ($request->input('status') === 'in_progress') {
            $query->whereIn('id', $inProgressQuizIds);
        } elseif ($request->input('status') === 'unattempted') {
            $query->whereIn('id', $unattemptedQuizIds);
        }

        $quizzes = $query->latest()->paginate(12)->withQueryString();
        $quizzes->each(function ($q) use ($userAttemptsMap, $userClass) {
            $att = $userAttemptsMap->get($q->id);
            $q->setRelation('attempts', $att ? collect([$att]) : collect());
            if ($userClass && $q->class_id === $userClass->id) {
                $q->setRelation('schoolClass', $userClass);
            }
        });

        $progressPercent = $totalQuizzes > 0 ? round(($completedCount / $totalQuizzes) * 100) : 0;

        // Daftar mapel untuk filter pills
        $subjectIds = $allClassQuizzes->pluck('subject_id')->filter()->unique();
        $subjects = $subjectIds->isNotEmpty()
            ? \App\Models\Subject::whereIn('id', $subjectIds)->orderBy('name')->get()
            : collect();

        return view('student.quizzes.index', compact(
            'quizzes',
            'totalQuizzes',
            'completedCount',
            'inProgressCount',
            'unattemptedCount',
            'progressPercent',
            'subjects'
        ));
    }

    public function show(Quiz $quiz)
    {
        $user = Auth::user();
        $userClass = null;
        if ($user) {
            $user->loadMissing('schoolClass');
            $userClass = $user->schoolClass;
        }
        if ($user->isGuru() && $quiz->instructor_id !== $user->id) {
            abort(403, 'Kuis ini bukan milik Anda.');
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $quiz->load(['subject', 'instructor', 'questions.options']);
        if ($userClass && $quiz->class_id === $userClass->id) {
            $quiz->setRelation('schoolClass', $userClass);
        } else {
            $quiz->loadMissing('schoolClass');
        }
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        $teacherStats = null;
        if ($user->isGuru()) {
            $totalClassStudents = \App\Models\User::where('class_id', $quiz->class_id)
                ->whereHas('role', fn ($q) => $q->where('name', 'siswa'))
                ->count();
            $completedAttemptsCount = QuizAttempt::where('quiz_id', $quiz->id)
                ->whereNotNull('submitted_at')
                ->count();
            $teacherStats = [
                'total_students' => $totalClassStudents,
                'completed_count' => $completedAttemptsCount,
                'completed_percent' => $totalClassStudents > 0 ? round(($completedAttemptsCount / $totalClassStudents) * 100) : 0,
            ];
        }

        return view('student.quizzes.show', compact('quiz', 'attempt', 'teacherStats'));
    }

    public function start(Quiz $quiz)
    {
        $user = Auth::user();
        if ($user->isGuru()) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Guru tidak dapat mengikuti atau mengerjakan kuis.');
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        if ($attempt && $attempt->submitted_at) {
            return redirect()->route('student.quizzes.result', $quiz);
        }

        // Cegah pengerjaan jika kuis belum memiliki soal
        if ($quiz->questions()->count() === 0) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Kuis ini belum memiliki butir soal dari guru pengampu.');
        }

        // Cegah pengerjaan jika kuis melewati batas waktu dan belum pernah dimulai
        if (!$attempt && $quiz->deadline && $quiz->deadline->isPast()) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Batas waktu kuis telah berakhir. Kuis tidak dapat dimulai lagi.');
        }

        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'student_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
            ]);
        }

        return redirect()->route('student.quizzes.attempt', $quiz);
    }

    public function attempt(Quiz $quiz)
    {
        $user = Auth::user();
        $userClass = null;
        if ($user) {
            $user->loadMissing('schoolClass');
            $userClass = $user->schoolClass;
        }
        if ($user->isGuru()) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Guru tidak dapat mengikuti atau mengerjakan kuis.');
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $quiz->loadMissing(['subject', 'instructor']);
        if ($userClass && $quiz->class_id === $userClass->id) {
            $quiz->setRelation('schoolClass', $userClass);
        } else {
            $quiz->loadMissing('schoolClass');
        }

        if ($quiz->questions()->count() === 0) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Kuis ini belum memiliki butir soal dari guru pengampu.');
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$attempt) {
            return redirect()->route('student.quizzes.show', $quiz);
        }

        if ($attempt->submitted_at) {
            return redirect()->route('student.quizzes.result', $quiz);
        }

        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now()]);
            $attempt->refresh();
        }

        $attempt->setRelation('quiz', $quiz);
        $orderedQuestions = $attempt->getOrderedQuestions();
        $quiz->setRelation('questions', $orderedQuestions);

        // Calculate remaining seconds based on fixed started_at timestamp in database
        $durationSeconds = ($quiz->duration_minutes ?? 30) * 60;
        $startTimestamp = $attempt->started_at->getTimestamp();
        $endTimestamp = $startTimestamp + $durationSeconds;
        $currentTimestamp = time();
        $remainingSeconds = max(0, $endTimestamp - $currentTimestamp);

        // Jika waktu pengerjaan telah habis saat siswa membuka/kembali ke halaman kuis
        if ($remainingSeconds <= 0) {
            $questions = $quiz->questions()->with('options')->get();
            $existingAnswers = $attempt->answers()->get();
            $earnedPoints = 0.0;
            $totalScorableItems = 0;

            foreach ($questions as $question) {
                $ans = $existingAnswers->firstWhere('quiz_question_id', $question->id);

                if ($question->isMatching()) {
                    $totalPairs = $question->options->count();
                    $totalScorableItems += $totalPairs;

                    $pairsAnswer = ($ans && is_array($ans->answer_data)) ? $ans->answer_data : [];
                    if ($totalPairs > 0 && !empty($pairsAnswer)) {
                        foreach ($question->options as $opt) {
                            if (isset($pairsAnswer[$opt->id]) && trim($pairsAnswer[$opt->id]) === trim($opt->match_text)) {
                                $earnedPoints += 1.0;
                            }
                        }
                    }
                } else {
                    $totalScorableItems += 1;
                    if ($ans && $ans->selected_option_id) {
                        $cOpt = $question->options->firstWhere('is_correct', true);
                        if ($cOpt && $cOpt->id === $ans->selected_option_id) {
                            $earnedPoints += 1.0;
                        }
                    }
                }
            }

            $score = $totalScorableItems > 0 ? round(($earnedPoints / $totalScorableItems) * 100, 2) : 0;
            $attempt->update([
                'score' => $score,
                'submitted_at' => now(),
            ]);

            return redirect()->route('student.quizzes.result', $quiz)
                ->with('info', 'Waktu pengerjaan kuis telah habis! Jawaban Anda telah otomatis dikumpulkan.');
        }

        $savedAnswers = $attempt->answers()->pluck('selected_option_id', 'quiz_question_id')->toArray();
        $savedMatchingAnswers = $attempt->answers()->whereNotNull('answer_data')->pluck('answer_data', 'quiz_question_id')->toArray();

        return view('student.quizzes.attempt', compact('quiz', 'attempt', 'remainingSeconds', 'savedAnswers', 'savedMatchingAnswers'));
    }

    public function saveAnswer(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        if ($user->isGuru()) {
            return response()->json(['error' => 'Guru tidak dapat mengerjakan kuis.'], 403);
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$attempt || $attempt->submitted_at) {
            return response()->json(['error' => 'Kuis sudah selesai atau belum dimulai.'], 400);
        }

        if (!$attempt->started_at) {
            $attempt->update(['started_at' => now()]);
            $attempt->refresh();
        }

        $durationSeconds = ($quiz->duration_minutes ?? 30) * 60;
        $startTimestamp = $attempt->started_at->getTimestamp();
        $endTimestamp = $startTimestamp + $durationSeconds;
        if (time() > $endTimestamp + 10) {
            return response()->json(['status' => 'expired'], 200);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'option_id' => 'nullable|exists:quiz_question_options,id',
            'answer_data' => 'nullable|array',
        ]);

        QuizAnswer::updateOrCreate(
            [
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['option_id'] ?? null,
                'answer_data' => $validated['answer_data'] ?? null,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        if ($user->isGuru()) {
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'Guru tidak dapat mengerjakan kuis.');
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$attempt) {
            return redirect()->route('student.quizzes.show', $quiz);
        }

        if ($attempt->submitted_at) {
            return redirect()->route('student.quizzes.result', $quiz);
        }

        $answers = $request->input('answers', []);
        $matchingAnswers = $request->input('matching_answers', []);
        $questions = $quiz->questions()->with('options')->get();
        $existingAnswers = $attempt->answers()->get()->keyBy('quiz_question_id');

        $earnedPoints = 0.0;
        $totalScorableItems = 0;

        foreach ($questions as $question) {
            if ($question->isMatching()) {
                // Matching Question Answer
                $pairsAnswer = $matchingAnswers[$question->id] ?? ($existingAnswers->has($question->id) ? $existingAnswers->get($question->id)->answer_data : []);
                if (!is_array($pairsAnswer)) {
                    $pairsAnswer = [];
                }

                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'quiz_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => null,
                        'answer_data' => $pairsAnswer,
                    ]
                );

                $totalPairs = $question->options->count();
                $totalScorableItems += $totalPairs;

                if ($totalPairs > 0 && !empty($pairsAnswer)) {
                    foreach ($question->options as $opt) {
                        if (isset($pairsAnswer[$opt->id]) && trim($pairsAnswer[$opt->id]) === trim($opt->match_text)) {
                            $earnedPoints += 1.0;
                        }
                    }
                }
            } else {
                // Multiple Choice or True/False Question Answer
                if (isset($answers[$question->id]) && !empty($answers[$question->id])) {
                    $selectedOptionId = (int)$answers[$question->id];
                } elseif ($existingAnswers->has($question->id)) {
                    $selectedOptionId = $existingAnswers->get($question->id)->selected_option_id;
                } else {
                    $selectedOptionId = null;
                }

                QuizAnswer::updateOrCreate(
                    [
                        'quiz_attempt_id' => $attempt->id,
                        'quiz_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'answer_data' => null,
                    ]
                );

                $totalScorableItems += 1;

                if ($selectedOptionId) {
                    $correctOption = $question->options->firstWhere('is_correct', true);
                    if ($correctOption && $correctOption->id === $selectedOptionId) {
                        $earnedPoints += 1.0;
                    }
                }
            }
        }

        $score = $totalScorableItems > 0 ? round(($earnedPoints / $totalScorableItems) * 100, 2) : 0;

        $attempt->update([
            'score' => $score,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.quizzes.result', $quiz)->with('success', 'Kuis berhasil dikumpulkan!');
    }

    public function result(Quiz $quiz)
    {
        $user = Auth::user();
        $userClass = null;
        if ($user) {
            $user->loadMissing('schoolClass');
            $userClass = $user->schoolClass;
        }
        if ($user->isGuru() && $quiz->instructor_id !== $user->id) {
            abort(403, 'Kuis ini bukan milik Anda.');
        }
        if ($user->isSiswa() && $quiz->class_id !== $user->class_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }

        $quiz->loadMissing(['subject', 'instructor']);
        if ($userClass && $quiz->class_id === $userClass->id) {
            $quiz->setRelation('schoolClass', $userClass);
        } else {
            $quiz->loadMissing('schoolClass');
        }

        $attempt = QuizAttempt::with('answers')
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if (!$attempt->submitted_at) {
            return redirect()->route('student.quizzes.attempt', $quiz);
        }

        $attempt->setRelation('quiz', $quiz);
        $orderedQuestions = $attempt->getOrderedQuestions();
        $quiz->setRelation('questions', $orderedQuestions);
        $answersMap = $attempt->answers->keyBy('quiz_question_id');

        return view('student.quizzes.result', compact('quiz', 'attempt', 'answersMap'));
    }
}
