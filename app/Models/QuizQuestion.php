<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'question_type',
        'sort_order',
        'points',
        'explanation',
    ];

    protected $casts = [
        'question_type' => 'string',
        'sort_order' => 'integer',
        'points' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }
}
