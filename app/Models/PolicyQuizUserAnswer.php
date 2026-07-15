<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyQuizUserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_answer',
        'is_correct'
    ];

    protected $casts = [
        'selected_answer' => 'string',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime'
    ];

    /**
     * Get the attempt this answer belongs to
     */
    public function attempt()
    {
        return $this->belongsTo(PolicyQuizAttempt::class, 'attempt_id');
    }

    /**
     * Get the question this answer is for
     */
    public function question()
    {
        return $this->belongsTo(PolicyQuizQuestion::class, 'question_id');
    }
}