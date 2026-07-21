<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_quiz_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'policy_link',
        'explanation'
    ];

    protected $casts = [
        'correct_answer' => 'string',
    ];

    /**
     * Get the quiz this question belongs to
     */
    public function quiz()
    {
        return $this->belongsTo(PolicyQuiz::class, 'policy_quiz_id');
    }

    /**
     * Get user answers for this question
     */
    public function userAnswers()
    {
        return $this->hasMany(PolicyQuizUserAnswer::class, 'question_id');
    }

    /**
     * Check if answer is correct
     */
    public function isCorrectAnswer($answer)
    {
        return strtoupper($answer) === $this->correct_answer;
    }

    /**
     * Get the correct option text
     */
    public function getCorrectOptionText()
    {
        $field = 'option_' . strtolower($this->correct_answer);
        return $this->$field;
    }
}