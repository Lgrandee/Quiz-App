<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\StudentDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

Route::middleware('auth')->group(function () {
    // Main dashboard redirect
    Route::get('/dashboard', function () {
        if (auth()->user()->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');
});

// Teacher Routes
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students');
    
    // Question management - Teachers only
    Route::get('questions/upload-open', [QuestionController::class, 'createOpen'])->name('questions.create-open');
    Route::post('questions/store-open', [QuestionController::class, 'storeOpen'])->name('questions.store-open');
    Route::resource('questions', QuestionController::class);
    
    // Quiz management - Teachers only
    Route::resource('quizzes', QuizController::class);
    Route::get('quizzes/{quiz}/upload', [QuizController::class, 'uploadForm'])->name('quizzes.upload.form');
    Route::post('quizzes/{quiz}/upload', [QuizController::class, 'uploadQuestions'])->name('quizzes.upload');
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/progress', [StudentDashboardController::class, 'progress'])->name('progress');
    Route::get('/results', [StudentDashboardController::class, 'results'])->name('results');
    
    // Quiz taking - Students only
    Route::get('quiz/{quiz?}', [QuestionController::class, 'showQuiz'])->name('quiz.show');
    Route::get('quiz/{quiz}/question/{questionNumber}', [QuestionController::class, 'showQuestion'])->name('quiz.question');
    Route::post('quiz/save-answer', [QuestionController::class, 'saveAnswer'])->name('quiz.save-answer');
    Route::post('quiz/submit', [QuestionController::class, 'submitQuiz'])->name('quiz.submit');
    
    // Quiz attempt routes - Students only
    Route::get('quiz-attempts', [QuizAttemptController::class, 'index'])->name('quiz-attempts.index');
    Route::post('quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])->name('quiz-attempts.start');
    Route::get('quiz-attempts/{attempt}/take', [QuizAttemptController::class, 'take'])->name('quiz-attempts.take');
    Route::post('quiz-attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('quiz-attempts.submit');
    Route::get('quiz-attempts/{attempt}/results', [QuizAttemptController::class, 'results'])->name('quiz-attempts.results');
});

// Home route for authenticated users
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth');
