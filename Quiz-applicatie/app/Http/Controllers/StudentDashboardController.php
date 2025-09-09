<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'student']);
    }

    public function index()
    {
        $student = auth()->user();
        
        // Student statistics
        $totalAttempts = QuizAttempt::where('user_id', $student->id)->count();
        $completedQuizzes = QuizAttempt::where('user_id', $student->id)
            ->distinct('quiz_id')
            ->count();
        
        $averageScore = QuizAttempt::where('user_id', $student->id)
            ->avg('score') ?? 0;
        
        $bestScore = QuizAttempt::where('user_id', $student->id)
            ->max('score') ?? 0;
        
        // Available quizzes
        $availableQuizzes = Quiz::with(['attempts' => function($query) use ($student) {
            $query->where('user_id', $student->id);
        }])
        ->withCount('questions')
        ->latest()
        ->paginate(6);
        
        // Recent attempts
        $recentAttempts = QuizAttempt::where('user_id', $student->id)
            ->with('quiz')
            ->latest()
            ->limit(5)
            ->get();
        
        // Progress over time
        $progressData = QuizAttempt::where('user_id', $student->id)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(score) as avg_score')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->limit(14)
            ->get();

        return view('student.dashboard', compact(
            'totalAttempts',
            'completedQuizzes', 
            'averageScore',
            'bestScore',
            'availableQuizzes',
            'recentAttempts',
            'progressData'
        ));
    }

    public function progress()
    {
        $student = auth()->user();
        
        // Detailed progress per quiz
        $quizProgress = Quiz::withCount('questions')
            ->with(['attempts' => function($query) use ($student) {
                $query->where('user_id', $student->id)
                      ->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function($quiz) {
                $attempts = $quiz->attempts;
                $bestAttempt = $attempts->sortByDesc('score')->first();
                $lastAttempt = $attempts->first();
                
                return [
                    'quiz' => $quiz,
                    'attempts_count' => $attempts->count(),
                    'best_score' => $bestAttempt ? $bestAttempt->score : 0,
                    'best_percentage' => $bestAttempt ? round(($bestAttempt->score / $quiz->questions_count) * 100, 1) : 0,
                    'last_attempt' => $lastAttempt,
                    'improvement' => $attempts->count() > 1 ? 
                        ($lastAttempt->score - $attempts->skip(1)->first()->score) : 0
                ];
            });

        return view('student.progress', compact('quizProgress'));
    }

    public function results()
    {
        $student = auth()->user();
        
        // All quiz attempts with detailed results
        $attempts = QuizAttempt::where('user_id', $student->id)
            ->with(['quiz' => function($query) {
                $query->withCount('questions');
            }])
            ->latest()
            ->paginate(10);

        return view('student.results', compact('attempts'));
    }
}
