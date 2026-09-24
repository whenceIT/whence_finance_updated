<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockage extends Model
{
    use HasFactory;

    protected $table = 'blockages';

    protected $fillable = [
        'office_id',
        'reason',
        'time_to_unlock'
    ];

    protected $casts = [
        'time_to_unlock' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function isUnlocked(): bool
    {
        return $this->time_to_unlock && now()->gte($this->time_to_unlock);
    }
}
