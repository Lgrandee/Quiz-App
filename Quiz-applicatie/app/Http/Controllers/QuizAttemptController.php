<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Quiz $quiz)
    {
        $user = Auth::user();
        
        // Check if user is a student
        if (!$user->isStudent()) {
            return redirect()->back()->with('error', 'Only students can take quizzes.');
        }

        // Check if quiz is active
        if (!$quiz->is_active) {
            return redirect()->back()->with('error', 'This quiz is not currently available.');
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => now(),
            'total_questions' => $quiz->total_questions,
            'answers' => [],
        ]);

        return redirect()->route('quiz-attempts.take', $attempt);
    }

    /**
     * Display the quiz taking interface
     */
    public function take(QuizAttempt $attempt)
    {
        // Check if user owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already completed
        if ($attempt->isCompleted()) {
            return redirect()->route('quiz-attempts.results', $attempt);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions;

        return view('student.quiz.take', compact('attempt', 'quiz', 'questions'));
    }

    /**
     * Submit quiz answers
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        // Check if user owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if already completed
        if ($attempt->isCompleted()) {
            return redirect()->route('quiz-attempts.results', $attempt);
        }

        $answers = $request->input('answers', []);
        
        // Update attempt with answers
        $attempt->update([
            'answers' => $answers,
        ]);

        // Calculate score
        $attempt->calculateScore();

        return redirect()->route('quiz-attempts.results', $attempt);
    }

    /**
     * Show quiz results
     */
    public function results(QuizAttempt $attempt)
    {
        // Check if user owns this attempt or is the quiz creator
        $user = Auth::user();
        if ($attempt->user_id !== $user->id && $attempt->quiz->created_by !== $user->id) {
            abort(403);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions;
        $answers = $attempt->answers;

        // Prepare detailed results
        $detailedResults = [];
        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? '';
            $isCorrect = $question->isCorrectAnswer($userAnswer);
            
            $detailedResults[] = [
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ];
        }

        return view('student.quiz.results', compact('attempt', 'quiz', 'detailedResults'));
    }

    /**
     * Show all attempts for a user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $attempts = QuizAttempt::where('user_id', $user->id)
                ->with('quiz')
                ->orderBy('created_at', 'desc')
                ->get();
            return view('student.attempts.index', compact('attempts'));
        } else {
            // Teachers can see all attempts for their quizzes
            $attempts = QuizAttempt::whereHas('quiz', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })->with(['quiz', 'user'])->orderBy('created_at', 'desc')->get();
            
            return view('teacher.attempts.index', compact('attempts'));
        }
    }
}
