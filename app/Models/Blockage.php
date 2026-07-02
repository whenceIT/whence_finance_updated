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
        'reason'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }
}
