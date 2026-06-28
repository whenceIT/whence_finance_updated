<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecoveryFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
