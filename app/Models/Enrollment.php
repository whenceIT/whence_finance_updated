<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_material_id',
        'enrolled_at',
        'completed_at',
        'progress',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainingMaterial()
    {
        return $this->belongsTo(TrainingMaterial::class);
    }
}
