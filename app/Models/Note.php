<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = "notes";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

}
