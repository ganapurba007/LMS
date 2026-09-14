<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['subject', 'schoolClass', 'instructor'])
            ->withCount('questions')
            ->latest()
            ->paginate(10);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.quizzes.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
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
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak membuat kuis untuk mata pelajaran ini.'])->withInput();
        }

        $quiz = new Quiz();
        $quiz->title = $request->title;
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
            ->with('success', 'Kuis berhasil dibuat. Silakan tambahkan atau impor soal ke dalam kuis.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'questions.questionBank', 'subject', 'schoolClass']);
        $questionBanks = QuestionBank::with('options')->get();

        return view('admin.quizzes.show', compact('quiz', 'questionBanks'));
    }

    public function edit(Quiz $quiz)
    {
        $user = Auth::user();
        $subjects = $user->subjects()->exists() ? $user->subjects : Subject::all();
        $classes = SchoolClass::all();

        return view('admin.quizzes.edit', compact('quiz', 'subjects', 'classes'));
    }

    public function update(Request $request, Quiz $quiz)
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
        if ($user->subjects()->exists() && !$user->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return back()->withErrors(['subject_id' => 'Anda tidak berhak mengedit kuis untuk mata pelajaran ini.'])->withInput();
        }

        $quiz->title = $request->title;
        $quiz->duration_minutes = $request->duration_minutes;
        $quiz->points_per_question = $request->points_per_question;
        $quiz->deadline = $request->deadline;
        $quiz->subject_id = $request->subject_id;
        $quiz->class_id = $request->class_id;
        $quiz->save();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Pengaturan kuis berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil dihapus.');
    }

    public function importQuestions(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_bank_ids' => ['required', 'array'],
            'question_bank_ids.*' => ['exists:question_bank,id'],
        ]);

        DB::transaction(function () use ($request, $quiz) {
            foreach ($request->question_bank_ids as $qbId) {
                // Avoid duplicating if already imported
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
                ]);

                foreach ($qb->options as $opt) {
                    QuizQuestionOption::create([
                        'quiz_question_id' => $qq->id,
                        'option_text' => $opt->option_text,
                        'is_correct' => $opt->is_correct,
                    ]);
                }
            }
        });

        return back()->with('success', 'Soal berhasil diimpor dari Bank Soal.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_text' => ['required', 'string'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string'],
            'correct_option' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $quiz) {
            $qq = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_bank_id' => null,
                'question_text' => $request->question_text,
            ]);

            foreach ($request->options as $index => $optionText) {
                QuizQuestionOption::create([
                    'quiz_question_id' => $qq->id,
                    'option_text' => $optionText,
                    'is_correct' => ($index == $request->correct_option),
                ]);
            }
        });

        return back()->with('success', 'Soal kuis baru berhasil ditambahkan.');
    }

    public function destroyQuestion(Quiz $quiz, QuizQuestion $question)
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(403);
        }

        $question->delete();

        return back()->with('success', 'Soal kuis berhasil dihapus.');
    }
}
