<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\QuestionController;

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
    return view('welcome');
})->name('home');

Auth::routes();

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->isTeacher()) {
            return redirect()->route('quizzes.index');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/student/dashboard', function () {
        $quizzes = \App\Models\Quiz::where('is_active', true)->get();
        $recentAttempts = auth()->user()->quizAttempts()->with('quiz')->latest()->take(5)->get();
        return view('student.dashboard', compact('quizzes', 'recentAttempts'));
    })->name('student.dashboard');

    // Question management routes - specific routes first
    Route::get('questions/upload-open', [QuestionController::class, 'createOpen'])->name('questions.create-open');
    Route::post('questions/store-open', [QuestionController::class, 'storeOpen'])->name('questions.store-open');
    Route::resource('questions', QuestionController::class);
    Route::get('quiz/{quiz?}', [QuestionController::class, 'showQuiz'])->name('quiz.show');
    Route::get('quiz/{quiz}/question/{questionNumber}', [QuestionController::class, 'showQuestion'])->name('quiz.question');
    Route::post('quiz/save-answer', [QuestionController::class, 'saveAnswer'])->name('quiz.save-answer');
    Route::post('quiz/submit', [QuestionController::class, 'submitQuiz'])->name('quiz.submit');

    // Quiz management routes
    Route::resource('quizzes', QuizController::class);
    Route::get('quizzes/{quiz}/upload', [QuizController::class, 'uploadForm'])->name('quizzes.upload.form');
    Route::post('quizzes/{quiz}/upload', [QuizController::class, 'uploadQuestions'])->name('quizzes.upload');

    // Quiz attempt routes
    Route::get('quiz-attempts', [QuizAttemptController::class, 'index'])->name('quiz-attempts.index');
    Route::post('quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])->name('quiz-attempts.start');
    Route::get('quiz-attempts/{attempt}/take', [QuizAttemptController::class, 'take'])->name('quiz-attempts.take');
    Route::post('quiz-attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])->name('quiz-attempts.submit');
    Route::get('quiz-attempts/{attempt}/results', [QuizAttemptController::class, 'results'])->name('quiz-attempts.results');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
