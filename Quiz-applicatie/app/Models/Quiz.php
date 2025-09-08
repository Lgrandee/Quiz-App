<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'is_active',
        'time_limit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The user who created this quiz
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Questions belonging to this quiz
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Quiz attempts for this quiz
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get total points for this quiz
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }

    /**
     * Get total number of questions
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->questions->count();
    }

    /**
     * Check if this is a multiple choice quiz
     */
    public function isMultipleChoice(): bool
    {
        return $this->quiz_type === 'multiple_choice';
    }

    /**
     * Check if this is an open question quiz
     */
    public function isOpenQuestion(): bool
    {
        return $this->quiz_type === 'open_question';
    }

    /**
     * Check if this is a mixed quiz
     */
    public function isMixed(): bool
    {
        return $this->quiz_type === 'mixed';
    }
}
