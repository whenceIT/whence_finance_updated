<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarryOver extends Model
{
    protected $table = 'carry_overs';
    public $timestamps = false;

      public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

     public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

