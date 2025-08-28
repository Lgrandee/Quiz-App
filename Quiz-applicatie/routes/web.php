<?php

use Illuminate\Support\Facades\Route;
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
    return view('home');
})->name('home');

Route::get('layouts.app', function () {
    return view('layouts.app');
})->name('layouts.app');

Route::get('/questions/all', [QuestionController::class, 'allQuestions'])->name('questions.allquestions');

// Question routes
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/questions/upload', [QuestionController::class, 'create'])->name('questions.create');
Route::post('/questions/upload', [QuestionController::class, 'store'])->name('questions.store');

// Quiz routes
Route::get('/quiz', [QuestionController::class, 'showQuiz'])->name('quiz.show');
Route::post('/quiz/submit', [QuestionController::class, 'submitQuiz'])->name('quiz.submit');
