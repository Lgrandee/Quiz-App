<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Quiz;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::paginate(12); // 12 questions per page
        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        $quizzes = Quiz::all();
        return view('teacher.questions.upload', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048', // 2MB max
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        // Read CSV with semicolon delimiter
        $csvData = [];
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        if (empty($csvData)) {
            return redirect()->back()->with('error', 'CSV bestand is leeg of kon niet worden gelezen.');
        }

        $header = array_shift($csvData); // Remove header row

        // Validate CSV format
        if (count($header) < 4) {
            return redirect()->back()->with('error', 'CSV bestand heeft niet het juiste aantal kolommen. Verwacht minimaal 4 kolommen.');
        }

        $questionsCreated = 0;
        $errors = [];

        foreach ($csvData as $rowIndex => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Parse CSV row based on type
                $questionData = $this->parseCsvRow($row, $rowIndex + 2); // +2 because we removed header and array is 0-indexed

                if ($questionData) {
                    Question::create($questionData);
                    $questionsCreated++;
                }
            } catch (\Exception $e) {
                $errors[] = "Rij " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        $message = $questionsCreated . " vragen succesvol geüpload.";
        if (!empty($errors)) {
            $message .= " Fouten: " . implode(", ", $errors);
        }

        return redirect()->route('teacher.questions.index')->with('success', $message);
    }

    /**
     * Parse a CSV row and return question data array
     */
    private function parseCsvRow($row, $rowNumber)
    {
        // Expected format for multiple choice: vraag_id;type;vraag;antwoord_a;antwoord_b;antwoord_c;juiste_antwoord
        // Expected format for open questions: vraag_id;type;vraag;juiste_antwoord

        if (count($row) < 4) {
            throw new \Exception("Niet genoeg kolommen");
        }

        $vraagId = trim($row[0]);
        $type = trim($row[1]);
        $vraag = trim($row[2]);

        if (empty($vraag)) {
            throw new \Exception("Vraag tekst is verplicht");
        }

        $questionData = [
            'question_text' => $vraag,
            'type' => $type,
            'points' => 1,
            'quiz_id' => null, // Will be set when questions are assigned to quizzes
        ];

        if ($type === 'multiple_choice') {
            if (count($row) < 7) {
                throw new \Exception("Multiple choice vragen hebben minimaal 7 kolommen nodig");
            }

            $antwoordA = trim($row[3]);
            $antwoordB = trim($row[4]);
            $antwoordC = trim($row[5]);
            $juisteAntwoord = strtolower(trim($row[6] ?? ''));

            if (empty($antwoordA) || empty($antwoordB) || empty($antwoordC)) {
                throw new \Exception("Alle antwoord opties (A, B, C) zijn verplicht voor multiple choice");
            }

            if (!in_array($juisteAntwoord, ['a', 'b', 'c'])) {
                throw new \Exception("Juiste antwoord moet 'a', 'b', of 'c' zijn");
            }

            $questionData['options'] = [
                'a' => $antwoordA,
                'b' => $antwoordB,
                'c' => $antwoordC,
            ];
            $questionData['correct_answer'] = $juisteAntwoord;

        } elseif ($type === 'open_question') {
            $juisteAntwoord = trim($row[3]);

            if (empty($juisteAntwoord)) {
                throw new \Exception("Juiste antwoord is verplicht voor open vragen");
            }

            $questionData['correct_answer'] = $juisteAntwoord;
            $questionData['options'] = null;

        } else {
            throw new \Exception("Type moet 'multiple_choice' of 'open_question' zijn");
        }

        return $questionData;
    }

    public function show(Question $question)
    {
        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $quizzes = Quiz::all();
        return view('questions.edit', compact('question', 'quizzes'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,open_question',
            'correct_answer' => 'required|string',
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
        ]);

        $options = null;
        if ($request->type === 'multiple_choice') {
            $options = [
                'a' => $request->option_a,
                'b' => $request->option_b,
                'c' => $request->option_c,
            ];
        }

        $question->update([
            'quiz_id' => $request->quiz_id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
        ]);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully!');
    }

    public function showQuiz($quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        return view('quiz', compact('quiz'));
    }

    public function submitQuiz(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        $score = 0;
        $totalPoints = 0;
        $results = [];

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->input('answer_' . $question->id);
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

        return view('quiz_result', compact('quiz', 'score', 'totalPoints', 'percentage', 'results'));
    }
}
