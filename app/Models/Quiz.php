<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_topic_id',
        'title',
        'description',
        'passing_score',
        'time_limit',
        'is_active',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    public function topic()
    {
        return $this->belongsTo(CourseTopic::class, 'course_topic_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
