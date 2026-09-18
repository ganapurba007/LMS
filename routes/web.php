<?php

use App\Http\Controllers\Admin\AssignmentBankController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MaterialBankController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ReportController as StudentReportController;
use App\Http\Controllers\Admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\AdminPasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Alias & Shortcut Akses Backend / Admin
Route::get('admin', function () {
    if (auth()->check() && ! auth()->user()->isGuru()) {
        return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki akses ke portal admin.');
    }
    return redirect()->route('admin.dashboard');
});

// Rute Autentikasi Khusus Backend (Admin / Guru)
Route::prefix('admin')->as('admin.')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [AdminPasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [AdminPasswordResetLinkController::class, 'store'])->name('password.email');
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Route Dashboard Siswa
Route::get('/dashboard', [StudentDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route Siswa Materials, Assignments, Quizzes, Report & Discussion
Route::middleware(['auth'])->prefix('student')->as('student.')->group(function () {
    Route::get('materials', [StudentMaterialController::class, 'index'])->name('materials.index');
    Route::get('materials/{material}', [StudentMaterialController::class, 'show'])->name('materials.show');
    Route::post('materials/{material}/complete', [StudentMaterialController::class, 'toggleComplete'])->name('materials.complete');
    Route::post('materials/{material}/toggle-complete', [StudentMaterialController::class, 'toggleComplete'])->name('materials.toggle-complete');
    Route::post('materials/{material}/discussions', [StudentMaterialController::class, 'storeComment'])->name('materials.discussions');
    Route::delete('materials/{material}/discussions/{discussion}', [StudentMaterialController::class, 'destroyComment'])->name('materials.discussions.destroy');

    Route::get('assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');

    Route::get('quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
    Route::post('quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
    Route::get('quizzes/{quiz}/attempt', [StudentQuizController::class, 'attempt'])->name('quizzes.attempt');
    Route::post('quizzes/{quiz}/save-answer', [StudentQuizController::class, 'saveAnswer'])->name('quizzes.save-answer');
    Route::post('quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('quizzes/{quiz}/result', [StudentQuizController::class, 'result'])->name('quizzes.result');

    Route::get('report', [StudentReportController::class, 'index'])->name('report.index');
});

// Route Group Admin / Guru
Route::middleware(['auth', 'role:guru'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Role CRUD
    Route::resource('roles', RoleController::class);

    // Master User (List & Assign Role)
    Route::match(['get', 'post'], 'users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);

    // Master Mata Pelajaran CRUD
    Route::resource('subjects', SubjectController::class);

    // Master Kelas CRUD
    Route::resource('classes', SchoolClassController::class);

    // Master Bank Soal CRUD
    Route::resource('question-banks', QuestionBankController::class);

    // Master Bank Materi CRUD & JSON API
    Route::get('material-banks/{materialBank}/json', [MaterialBankController::class, 'getDetailJson'])->name('material-banks.json');
    Route::resource('material-banks', MaterialBankController::class);

    // Master Bank Tugas CRUD & JSON API
    Route::get('assignment-banks/{assignmentBank}/json', [AssignmentBankController::class, 'getDetailJson'])->name('assignment-banks.json');
    Route::resource('assignment-banks', AssignmentBankController::class);

    // Master Materi CRUD & Diskusi
    Route::resource('materials', MaterialController::class);
    Route::post('materials/{material}/discussions', [MaterialController::class, 'storeComment'])->name('materials.discussions');
    Route::delete('materials/{material}/discussions/{discussion}', [MaterialController::class, 'destroyComment'])->name('materials.discussions.destroy');

    // Master Tugas CRUD
    Route::resource('assignments', AssignmentController::class);

    // Master Kuis CRUD & Question Management
    Route::resource('quizzes', QuizController::class);
    Route::get('quizzes/{quiz}/students', [QuizController::class, 'students'])->name('quizzes.students');
    Route::get('quizzes/{quiz}/students/{student}/answers', [QuizController::class, 'getStudentAnswers'])->name('quizzes.students.answers');
    Route::delete('quizzes/{quiz}/students/{student}/reset', [QuizController::class, 'resetStudentAttempt'])->name('quizzes.students.reset');
    Route::post('quizzes/{quiz}/import-questions', [QuizController::class, 'importQuestions'])->name('quizzes.import-questions');
    Route::post('quizzes/{quiz}/questions', [QuizController::class, 'storeQuestion'])->name('quizzes.store-question');
    Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('quizzes.destroy-question');

    // Admin Koreksi / Penilaian Tugas
    Route::get('submissions', [AssignmentSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [AssignmentSubmissionController::class, 'show'])->name('submissions.show');
    Route::post('submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade'])->name('submissions.grade');

    // Admin Laporan & Rekapitulasi Nilai
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('reports/export-csv', [ReportController::class, 'exportExcel'])->name('reports.export-csv');
});

// Profile & Notification Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

require __DIR__.'/auth.php';

// Fallback route for non-existent routes -> redirect to dashboard
Route::fallback(function () {
    return redirect()->route('dashboard');
});
