<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'options',
        'correct_answer',
        'points',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * The quiz this question belongs to
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Check if this is a multiple choice question
     */
    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    /**
     * Check if this is an open question
     */
    public function isOpenQuestion(): bool
    {
        return $this->type === 'open_question';
    }

    /**
     * Check if the given answer is correct
     */
    public function isCorrectAnswer(?string $answer): bool
    {
        // Return false if no answer is provided
        if ($answer === null || trim($answer) === '') {
            return false;
        }
        
        if ($this->isMultipleChoice()) {
            return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
        }
        
        // For open questions, do case-insensitive comparison ignoring spaces
        $normalizedAnswer = strtolower(preg_replace('/\s+/', '', trim($answer)));
        $normalizedCorrect = strtolower(preg_replace('/\s+/', '', trim($this->correct_answer)));
        
        return $normalizedAnswer === $normalizedCorrect;
    }

    /**
     * Calculate similarity between user answer and correct answer for open questions
     */
    public function calculateAnswerSimilarity(?string $answer): float
    {
        // Return 0 if no answer is provided
        if ($answer === null || trim($answer) === '') {
            return 0.0;
        }
        
        if ($this->isMultipleChoice()) {
            return $this->isCorrectAnswer($answer) ? 1.0 : 0.0;
        }

        $normalizedAnswer = strtolower(preg_replace('/\s+/', '', trim($answer)));
        $normalizedCorrect = strtolower(preg_replace('/\s+/', '', trim($this->correct_answer)));

        if (empty($normalizedAnswer) || empty($normalizedCorrect)) {
            return 0.0;
        }

        if ($normalizedAnswer === $normalizedCorrect) {
            return 1.0;
        }

        // Calculate Levenshtein distance
        $distance = levenshtein($normalizedAnswer, $normalizedCorrect);
        $maxLength = max(strlen($normalizedAnswer), strlen($normalizedCorrect));
        
        return ($maxLength - $distance) / $maxLength;
    }
}
