<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeBlockingHistory extends Model
{
    use HasFactory;

    protected $table = 'office_blocking_histories';

    protected $fillable = [
        'office_id',
        'blocked_count',
        'reason',
        'last_blocked_at',
    ];

    protected $casts = [
        'last_blocked_at' => 'datetime',
        'blocked_count'   => 'integer',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }
}
