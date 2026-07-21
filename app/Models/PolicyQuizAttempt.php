<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyQuizAttempt extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'policy_quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'score_percentage',
        'passed'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
        'score_percentage' => 'decimal:2',
    ];

    /**
     * Get the quiz for this attempt
     */
    public function quiz()
    {
        return $this->belongsTo(PolicyQuiz::class, 'policy_quiz_id');
    }

    /**
     * Get the user who made this attempt
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all answers for this attempt
     */
    public function answers()
    {
        return $this->hasMany(PolicyQuizUserAnswer::class, 'attempt_id');
    }

    /**
     * Check if attempt is still active (not completed)
     */
    public function isActive()
    {
        return is_null($this->completed_at);
    }

    /**
     * Check if time has expired
     */
    public function isTimeExpired()
    {
        if (!$this->started_at) {
            return false;
        }

        $timeLimitMinutes = $this->quiz->time_limit_minutes ?? 10;
        $expiryTime = $this->started_at->addMinutes($timeLimitMinutes);
        
        return now() > $expiryTime;
    }

    /**
     * Calculate remaining time in seconds
     */
    public function getRemainingTimeSeconds()
    {
        if (!$this->started_at || !$this->isActive()) {
            return 0;
        }

        $timeLimitMinutes = $this->quiz->time_limit_minutes ?? 10;
        $expiryTime = $this->started_at->addMinutes($timeLimitMinutes);
        $remaining = $expiryTime->diffInSeconds(now());
        
        return max(0, $remaining);
    }

    /**
     * Calculate and save score
     */
    public function calculateScore()
    {
        
        $answers = $this->answers;

        $totalQuestions = $answers->count();
        
        if ($totalQuestions === 0) {
            return 0;
        }

        $correctAnswers = $answers->where('is_correct', true)->count();
        $percentage = ($correctAnswers / $totalQuestions) * 100;
        
        $this->score_percentage = round($percentage, 2);
        $this->passed = $percentage >= $this->quiz->passing_threshold;
        $this->completed_at = now();
        $this->save();
        
        return $percentage;
    }
}