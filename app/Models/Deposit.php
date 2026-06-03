<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';
    public $timestamps = false;

    public function depositType()
    {
        return $this->belongsTo(DepositType::class, 'deposit_type', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}