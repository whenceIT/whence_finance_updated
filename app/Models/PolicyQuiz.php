<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'passing_threshold',
        'time_limit_minutes',
        'max_questions',
        'open_date',
        'close_date',
        'active',
        'created_by'
    ];

    protected $casts = [
        'open_date' => 'datetime',
        'close_date' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Get the user who created the quiz
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all questions for this quiz
     */
    public function questions()
    {
        return $this->hasMany(PolicyQuizQuestion::class, 'policy_quiz_id');
    }

    /**
     * Get all attempts for this quiz
     */
    public function attempts()
    {
        return $this->hasMany(PolicyQuizAttempt::class, 'policy_quiz_id');
    }

    /**
     * Check if quiz is currently open
     */
    public function isOpen()
    {
        $now = now();
        return $this->active && 
               $this->open_date <= $now && 
               $this->close_date >= $now;
    }

    /**
     * Get active questions (randomized)
     */
    public function getActiveQuestions()
    {
        return $this->questions()
            ->inRandomOrder()
            ->limit($this->max_questions)
            ->get();
    }

    /**
     * Get user's active attempt
     */
    public function getUserAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNull('completed_at')
            ->first();
    }
}