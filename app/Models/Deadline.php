<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    use HasFactory;

    protected $table = 'deadlines';

    protected $fillable = [
        'name',
        'countdown_date',
    ];

    protected $casts = [
        'countdown_date' => 'datetime',
    ];
}