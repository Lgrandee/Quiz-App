<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuestionController;

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Basic quiz routes
    Route::get('/quiz/{quiz}', [QuestionController::class, 'showQuiz'])->name('quiz.show');
    Route::post('/quiz/submit', [QuestionController::class, 'submitQuiz'])->name('quiz.submit');

    // Basic question management
    Route::resource('questions', QuestionController::class);
});

// Teacher routes
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [App\Http\Controllers\TeacherDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/students', [App\Http\Controllers\TeacherDashboardController::class, 'students'])->name('students');

    // Teacher question management
    Route::resource('questions', App\Http\Controllers\QuestionController::class);
    Route::get('/questions/create-open', [App\Http\Controllers\QuestionController::class, 'createOpen'])->name('questions.create-open');
    Route::post('/questions/store-open', [App\Http\Controllers\QuestionController::class, 'storeOpen'])->name('questions.store-open');

    // Teacher quiz management
    Route::resource('quizzes', App\Http\Controllers\QuizController::class);
});

// Student routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/progress', [App\Http\Controllers\StudentDashboardController::class, 'progress'])->name('progress');

    // Student quiz routes
    Route::get('/quizzes', [App\Http\Controllers\StudentDashboardController::class, 'quizzes'])->name('quizzes');
    Route::get('/quiz/{quiz}/take', [App\Http\Controllers\QuizAttemptController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [App\Http\Controllers\QuizAttemptController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz-attempts', [App\Http\Controllers\StudentDashboardController::class, 'results'])->name('quiz-attempts.index');
});

// Home route for authenticated users
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth');
