<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Show all questions in a quiz form
     */

    /**
     * Handle quiz submission and show results
     */
    public function submitQuiz(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        
        // Get answers from session
        $sessionAnswers = session('quiz_answers_' . $quizId, []);
        
        $score = 0;
        $totalPoints = 0;
        $results = [];
        
        foreach ($quiz->questions as $question) {
            $userAnswer = $sessionAnswers[$question->id] ?? null;
            $totalPoints += $question->points;
            
            $correct = $question->isCorrectAnswer($userAnswer);
            if ($correct) {
                $score += $question->points;
            }
            
            $results[] = [
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $correct
            ];
        }
        
        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 1) : 0;
        
        // Clear session answers
        session()->forget('quiz_answers_' . $quizId);
        
        return view('quiz_result', compact('quiz', 'score', 'totalPoints', 'percentage', 'results'));
    }
    /**
     * Show the form for uploading CSV
     */
    public function index()
    {
        $questions = Question::with('quiz')->latest()->paginate(8);
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for uploading questions via CSV.
     */
    public function create()
    {
        return view('questions.upload');
    }

    /**
     * Show the form for uploading open questions via CSV.
     */
    public function createOpen()
    {
        return view('questions.upload-open');
    }

    /**
     * Handle the CSV upload and processing (Mixed format with type column)
     */
    public function store(Request $request)
    {
        // Check if this is actually an open questions upload
        $file = $request->file('csv_file');
        if ($file) {
            $csvContent = file_get_contents($file->path());
            $lines = explode("\n", $csvContent);
            $headerLine = trim($lines[0] ?? '');
            
            // If header doesn't contain 'type' column, treat as open questions but use default title
            if (strpos(strtolower($headerLine), 'type') === false) {
                // Add default quiz title for open questions upload via regular route
                $request->merge([
                    'quiz_title' => 'Open Vragen Quiz - ' . date('Y-m-d H:i'),
                    'quiz_description' => 'Automatisch geïmporteerde open vragen'
                ]);
                return $this->storeOpen($request);
            }
        }
        
        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            // Get the uploaded file
            $file = $request->file('csv_file');
            
            // Read the CSV file content as string
            $csvContent = file_get_contents($file->path());
            
            // Split into lines
            $lines = explode("\n", $csvContent);
            
            // Remove empty lines
            $lines = array_filter($lines, function($line) {
                return trim($line) !== '';
            });
            
            // Remove the header row
            array_shift($lines);
            
            // Find or create a default quiz for imported questions
            $defaultQuiz = \App\Models\Quiz::firstOrCreate(
                ['title' => 'Geïmporteerde Vragen'],
                [
                    'title' => 'Geïmporteerde Vragen',
                    'description' => 'Vragen geüpload via CSV import',
                    'is_active' => true,
                    'time_limit' => 30,
                    'created_by' => auth()->id() ?? 1,
                ]
            );

            // Start database transaction
            DB::beginTransaction();
            
            $importedCount = 0;
            $errors = [];
            
            foreach ($lines as $rowIndex => $line) {
                // Skip empty lines
                if (empty(trim($line))) {
                    continue;
                }
                
                // Split by semicolon
                $rowData = explode(';', trim($line));
                
                // Check if we have enough columns (minimum 4 for open questions, 7 for multiple choice)
                if (count($rowData) < 4) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Expected at least 4 columns, got " . count($rowData) . " columns. Data: " . implode(' | ', $rowData);
                    continue;
                }
                
                try {
                    // Clean up the data - remove any extra quotes or spaces
                    $questionId = trim($rowData[0], " \t\n\r\0\x0B\"");
                    $questionType = trim(strtolower($rowData[1]), " \t\n\r\0\x0B\"");
                    $question = trim($rowData[2], " \t\n\r\0\x0B\"");
                    
                    // Validate question type
                    if (!in_array($questionType, ['multiple_choice', 'open_question'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Invalid question type '{$questionType}'. Must be 'multiple_choice' or 'open_question'";
                        continue;
                    }
                    
                    if ($questionType === 'multiple_choice') {
                        // Multiple choice questions need 7 columns
                        if (count($rowData) < 7) {
                            $errors[] = "Row " . ($rowIndex + 2) . ": Multiple choice questions need 7 columns, got " . count($rowData);
                            continue;
                        }
                        
                        $answerA = trim($rowData[3], " \t\n\r\0\x0B\"");
                        $answerB = trim($rowData[4], " \t\n\r\0\x0B\"");
                        $answerC = trim($rowData[5], " \t\n\r\0\x0B\"");
                        $correctAnswer = trim(strtolower($rowData[6]), " \t\n\r\0\x0B\"");
                        
                        // Validate correct answer for multiple choice
                        if (!in_array($correctAnswer, ['a', 'b', 'c'])) {
                            $errors[] = "Row " . ($rowIndex + 2) . ": Invalid correct answer '{$correctAnswer}'. Must be 'a', 'b', or 'c'";
                            continue;
                        }
                    } else {
                        // Open questions need 4 columns
                        if (count($rowData) < 4) {
                            $errors[] = "Row " . ($rowIndex + 2) . ": Open questions need 4 columns, got " . count($rowData);
                            continue;
                        }
                        
                        $correctAnswer = trim($rowData[3], " \t\n\r\0\x0B\"");
                        
                        // Validate that open question has an answer
                        if (empty($correctAnswer)) {
                            $errors[] = "Row " . ($rowIndex + 2) . ": Open question must have a correct answer";
                            continue;
                        }
                    }
                    
                    // Create question with proper model structure
                    if ($questionType === 'multiple_choice') {
                        Question::create([
                            'quiz_id' => $defaultQuiz->id,
                            'question_text' => $question,
                            'type' => 'multiple_choice',
                            'options' => [
                                'a' => $answerA,
                                'b' => $answerB,
                                'c' => $answerC,
                            ],
                            'correct_answer' => $correctAnswer,
                            'points' => 1,
                            'order' => $importedCount + 1,
                        ]);
                    } else {
                        Question::create([
                            'quiz_id' => $defaultQuiz->id,
                            'question_text' => $question,
                            'type' => 'open_question',
                            'options' => null,
                            'correct_answer' => $correctAnswer,
                            'points' => 1,
                            'order' => $importedCount + 1,
                        ]);
                    }
                    
                    $importedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Prepare success message
            if ($importedCount > 0) {
                $message = "Successfully imported {$importedCount} questions! 🎉";
                if (!empty($errors)) {
                    $message .= " However, there were some errors with " . count($errors) . " rows.";
                }
                return redirect()->route('questions.index')->with('success', $message);
            } else {
                $errorMessage = "No questions were imported. ";
                if (!empty($errors)) {
                    $errorMessage .= "Errors found: " . implode(' | ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $errorMessage .= " and " . (count($errors) - 3) . " more errors.";
                    }
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing CSV: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Open Questions CSV upload and processing
     */
    public function storeOpen(Request $request)
    {
        // Validate the uploaded file and quiz details
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'quiz_title' => 'required|string|max:255',
            'quiz_description' => 'nullable|string|max:1000',
        ]);

        try {
            // Get the uploaded file
            $file = $request->file('csv_file');
            
            // Read the CSV file content as string
            $csvContent = file_get_contents($file->path());
            
            // Split into lines
            $lines = explode("\n", $csvContent);
            
            // Remove empty lines
            $lines = array_filter($lines, function($line) {
                return trim($line) !== '';
            });
            
            // Remove the header row
            array_shift($lines);
            
            // Start database transaction
            DB::beginTransaction();
            
            // Create a new quiz specifically for open questions
            $quiz = \App\Models\Quiz::create([
                'title' => $request->quiz_title,
                'description' => $request->quiz_description ?: 'Open vragen quiz geüpload via CSV',
                'is_active' => true,
                'time_limit' => 30,
                'created_by' => auth()->id(),
            ]);
            
            $importedCount = 0;
            $errors = [];
            
            foreach ($lines as $rowIndex => $line) {
                // Skip empty lines
                if (empty(trim($line))) {
                    continue;
                }
                
                // Split by semicolon
                $rowData = explode(';', trim($line));
                
                // Check if we have enough columns (minimum 3: vraag_id, vraag, juiste_antwoord)
                if (count($rowData) < 3) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Expected at least 3 columns, got " . count($rowData) . " columns";
                    continue;
                }
                
                try {
                    // Clean up the data
                    $questionId = trim($rowData[0], " \t\n\r\0\x0B\"");
                    $question = trim($rowData[1], " \t\n\r\0\x0B\"");
                    $correctAnswer = trim($rowData[2], " \t\n\r\0\x0B\"");
                    $points = isset($rowData[3]) ? (int)trim($rowData[3], " \t\n\r\0\x0B\"") : 1;
                    
                    // Validate required fields
                    if (empty($question) || empty($correctAnswer)) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Question and correct answer are required";
                        continue;
                    }
                    
                    // Ensure points is at least 1
                    if ($points < 1) {
                        $points = 1;
                    }
                    
                    // Create open question
                    Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $question,
                        'type' => 'open_question',
                        'options' => null,
                        'correct_answer' => $correctAnswer,
                        'points' => $points,
                        'order' => $importedCount + 1,
                    ]);
                    
                    $importedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Prepare success message
            if ($importedCount > 0) {
                $message = "Successfully imported {$importedCount} open questions into quiz '{$quiz->title}'! 🎉";
                if (!empty($errors)) {
                    $message .= " However, there were some errors with " . count($errors) . " rows.";
                }
                return redirect()->route('questions.index')->with('success', $message);
            } else {
                $errorMessage = "No questions were imported. ";
                if (!empty($errors)) {
                    $errorMessage .= "Errors found: " . implode(' | ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $errorMessage .= " and " . (count($errors) - 3) . " more errors.";
                    }
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing CSV: ' . $e->getMessage());
        }
    }

    public function showQuiz($quizId = null)
    {
        if ($quizId) {
            $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        } else {
            // Find the default imported questions quiz
            $quiz = \App\Models\Quiz::with('questions')
                ->where('title', 'Geïmporteerde Vragen')
                ->firstOrFail();
        }
        
        // Redirect to single question view
        return redirect()->route('quiz.question', ['quiz' => $quiz->id, 'questionNumber' => 1]);
    }

    public function showQuestion($quizId, $questionNumber = 1)
    {
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        $totalQuestions = $quiz->questions->count();
        
        if ($questionNumber < 1 || $questionNumber > $totalQuestions) {
            return redirect()->route('quiz.question', ['quiz' => $quizId, 'questionNumber' => 1]);
        }
        
        $question = $quiz->questions[$questionNumber - 1];
        $currentQuestion = $questionNumber;
        
        // Get saved answers from session
        $answers = session('quiz_answers_' . $quizId, []);
        
        return view('quiz-single', compact('quiz', 'question', 'currentQuestion', 'totalQuestions', 'answers'));
    }

    public function saveAnswer(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $questionNumber = $request->input('question_number');
        $answer = $request->input('answer');
        $currentAnswers = json_decode($request->input('current_answers', '[]'), true);
        
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        $question = $quiz->questions[$questionNumber - 1];
        
        // Save answer in session
        $answers = session('quiz_answers_' . $quizId, []);
        $answers[$question->id] = $answer;
        session(['quiz_answers_' . $quizId => $answers]);
        
        return response()->json(['success' => true]);
    }
}