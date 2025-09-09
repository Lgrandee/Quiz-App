<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeacherDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'teacher']);
    }

    public function index()
    {
        $teacher = auth()->user();
        
        // Statistics
        $totalQuizzes = Quiz::where('created_by', $teacher->id)->count();
        $totalQuestions = Question::whereHas('quiz', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })->count();
        
        $totalAttempts = QuizAttempt::whereHas('quiz', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })->count();
        
        $totalStudents = User::where('role', 'student')->count();
        
        // Recent quizzes
        $recentQuizzes = Quiz::where('created_by', $teacher->id)
            ->with(['attempts' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->limit(5)
            ->get();
        
        // Top performing students
        $topStudents = QuizAttempt::select('user_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as attempts_count'))
            ->whereHas('quiz', function($query) use ($teacher) {
                $query->where('created_by', $teacher->id);
            })
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('avg_score', 'desc')
            ->limit(5)
            ->get();
        
        // Recent activity
        $recentAttempts = QuizAttempt::whereHas('quiz', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })
        ->with(['user', 'quiz'])
        ->latest()
        ->limit(10)
        ->get();

        return view('teacher.dashboard', compact(
            'totalQuizzes', 
            'totalQuestions', 
            'totalAttempts', 
            'totalStudents',
            'recentQuizzes',
            'topStudents',
            'recentAttempts'
        ));
    }

    public function analytics()
    {
        $teacher = auth()->user();
        
        // Quiz performance analytics
        $quizPerformance = Quiz::where('created_by', $teacher->id)
            ->withCount('attempts')
            ->with(['attempts' => function($query) {
                $query->select('quiz_id', DB::raw('AVG(score) as avg_score'))
                      ->groupBy('quiz_id');
            }])
            ->get();

        // Student progress over time
        $studentProgress = QuizAttempt::whereHas('quiz', function($query) use ($teacher) {
            $query->where('created_by', $teacher->id);
        })
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('AVG(score) as avg_score'),
            DB::raw('COUNT(*) as attempts')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->limit(30)
        ->get();

        return view('teacher.analytics', compact('quizPerformance', 'studentProgress'));
    }

    public function students()
    {
        $teacher = auth()->user();
        
        // All students with their quiz performance
        $students = User::where('role', 'student')
            ->withCount(['quizAttempts as total_attempts' => function($query) use ($teacher) {
                $query->whereHas('quiz', function($q) use ($teacher) {
                    $q->where('created_by', $teacher->id);
                });
            }])
            ->with(['quizAttempts' => function($query) use ($teacher) {
                $query->whereHas('quiz', function($q) use ($teacher) {
                    $q->where('created_by', $teacher->id);
                })
                ->select('user_id', DB::raw('AVG(score) as avg_score'))
                ->groupBy('user_id');
            }])
            ->paginate(10);

        return view('teacher.students', compact('students'));
    }
}
