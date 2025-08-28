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
        $questions = Question::all();
        return view('quiz', compact('questions'));
        $questions = Question::all();
        $score = 0;
        $results = [];
        foreach ($questions as $question) {
            $userAnswer = $request->input('answer_' . $question->id);
            $correct = strtolower($userAnswer) === strtolower($question->correct_answer);
            if ($correct) {
                $score++;
            }
            $results[] = [
                'question' => $question->question,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $correct
            ];
        }
        return view('quiz_result', compact('score', 'results', 'questions'));
    }
    /**
     * Show the form for uploading CSV
     */
    public function index()
    {
        $questions = Question::latest()->paginate(10);
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the upload form
     */
    public function create()
    {
        return view('questions.upload');
    }

    /**
     * Handle the CSV upload and processing
     */
    public function store(Request $request)
    {
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
            
            // Start database transaction
            DB::beginTransaction();
            
            $importedCount = 0;
            $errors = [];
            
            foreach ($lines as $rowIndex => $line) {
                // Skip empty lines
                if (empty(trim($line))) {
                    continue;
                }
                
                // Split by semicolon - this is the key fix!
                $rowData = explode(';', trim($line));
                
                // Debug: Let's see what we get
                if ($rowIndex < 3) { // Only log first 3 rows for debugging
                    \Log::info("Row " . ($rowIndex + 2) . " data:", $rowData);
                }
                
                // Check if we have enough columns
                if (count($rowData) < 6) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Expected 6 columns, got " . count($rowData) . " columns. Data: " . implode(' | ', $rowData);
                    continue;
                }
                
                try {
                    // Clean up the data - remove any extra quotes or spaces
                    $questionId = trim($rowData[0], " \t\n\r\0\x0B\"");
                    $question = trim($rowData[1], " \t\n\r\0\x0B\"");
                    $answerA = trim($rowData[2], " \t\n\r\0\x0B\"");
                    $answerB = trim($rowData[3], " \t\n\r\0\x0B\"");
                    $answerC = trim($rowData[4], " \t\n\r\0\x0B\"");
                    $correctAnswer = trim(strtolower($rowData[5]), " \t\n\r\0\x0B\"");
                    
                    // Validate correct answer
                    if (!in_array($correctAnswer, ['a', 'b', 'c'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Invalid correct answer '{$correctAnswer}'. Must be 'a', 'b', or 'c'";
                        continue;
                    }
                    
                    // Create or update question
                    Question::updateOrCreate(
                        ['question_id' => $questionId],
                        [
                            'question' => $question,
                            'answer_a' => $answerA,
                            'answer_b' => $answerB,
                            'answer_c' => $answerC,
                            'correct_answer' => $correctAnswer,
                        ]
                    );
                    
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

    public function showQuiz()
    {
        $questions = Question::all();
        return view('quiz', compact('questions'));
    }
}