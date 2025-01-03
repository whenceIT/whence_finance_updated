<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave_days';

    protected $fillable = ['user_id', 'commencement_date', 'return_date', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
