<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'question',
        'answer_a',
        'answer_b',
        'answer_c',
        'correct_answer',
    ];
    

    public function getCorrectAnswerAttribute()
    {
        return match ($this->attributes['correct_answer']) {
            'a' => $this->attributes['answer_a'],
            'b' => $this->attributes['answer_b'],
            'c' => $this->attributes['answer_c'],
            default => null,
        };
    }

 }
