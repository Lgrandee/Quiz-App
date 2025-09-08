<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'score',
        'total_questions',
        'correct_answers',
        'answers',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'score' => 'decimal:2',
    ];

    /**
     * The quiz this attempt belongs to
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * The user who made this attempt
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the attempt is completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Get the percentage score
     */
    public function getPercentageAttribute(): float
    {
        if ($this->total_questions == 0) {
            return 0;
        }
        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    /**
     * Calculate and update the score
     */
    public function calculateScore(): void
    {
        $quiz = $this->quiz;
        $questions = $quiz->questions;
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $this->answers[$question->id] ?? '';
            
            if ($question->isCorrectAnswer($userAnswer)) {
                $correctAnswers++;
                $earnedPoints += $question->points;
            }
        }

        $this->update([
            'correct_answers' => $correctAnswers,
            'score' => $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0,
            'completed_at' => now(),
        ]);
    }
}
