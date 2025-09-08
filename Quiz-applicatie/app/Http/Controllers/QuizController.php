<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of quizzes
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            $quizzes = Quiz::where('created_by', $user->id)->with('questions')->get();
            return view('teacher.quizzes.index', compact('quizzes'));
        } else {
            $quizzes = Quiz::where('is_active', true)->with('questions')->get();
            return view('student.quizzes.index', compact('quizzes'));
        }
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create()
    {
        $this->authorize('create', Quiz::class);
        return view('teacher.quizzes.create');
    }

    /**
     * Store a newly created quiz
     */
    public function store(Request $request)
    {
        $this->authorize('create', Quiz::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz created successfully!');
    }

    /**
     * Display the specified quiz
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions', 'creator');
        
        if (Auth::user()->isTeacher()) {
            return view('teacher.quizzes.show', compact('quiz'));
        } else {
            return view('student.quizzes.show', compact('quiz'));
        }
    }

    /**
     * Show the form for editing the specified quiz
     */
    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        return view('teacher.quizzes.edit', compact('quiz'));
    }

    /**
     * Update the specified quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'time_limit' => $request->time_limit,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('quizzes.show', $quiz)->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Show form for uploading questions
     */
    public function uploadForm(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        return view('teacher.quizzes.upload', compact('quiz'));
    }

    /**
     * Handle file upload for questions (JSON/CSV)
     */
    public function uploadQuestions(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $request->validate([
            'file' => 'required|file|mimes:json,csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        try {
            $content = file_get_contents($file->getRealPath());
            
            if ($extension === 'json') {
                $this->processJsonFile($content, $quiz);
            } elseif (in_array($extension, ['csv', 'txt'])) {
                $this->processCsvFile($content, $quiz);
            }

            return redirect()->route('quizzes.show', $quiz)->with('success', 'Questions uploaded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Process JSON file format
     */
    private function processJsonFile(string $content, Quiz $quiz)
    {
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON format');
        }

        foreach ($data as $index => $questionData) {
            $this->createQuestionFromData($questionData, $quiz, $index + 1);
        }
    }

    /**
     * Process CSV file format
     */
    private function processCsvFile(string $content, Quiz $quiz)
    {
        $lines = str_getcsv($content, "\n");
        $header = str_getcsv(array_shift($lines));
        
        foreach ($lines as $index => $line) {
            $data = str_getcsv($line);
            $questionData = array_combine($header, $data);
            $this->createQuestionFromData($questionData, $quiz, $index + 1);
        }
    }

    /**
     * Create question from parsed data
     */
    private function createQuestionFromData(array $data, Quiz $quiz, int $order)
    {
        $type = isset($data['options']) && !empty($data['options']) ? 'multiple_choice' : 'open_question';
        
        $options = null;
        if ($type === 'multiple_choice') {
            if (is_string($data['options'])) {
                $options = json_decode($data['options'], true) ?: explode('|', $data['options']);
            } else {
                $options = $data['options'];
            }
        }

        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $data['question'] ?? $data['question_text'],
            'type' => $type,
            'options' => $options,
            'correct_answer' => $data['correct_answer'],
            'points' => $data['points'] ?? 1,
            'order' => $order,
        ]);
    }
}
