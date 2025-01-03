<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $table = 'advance_topups';

    protected $fillable = ['advance_id', 'installments', 'office_id', 'top_up_amount', 'top_up_date', 'first_name', 'last_name', 'status'];


    public function advance()
    {
        return $this->belongsTo(Advance::class);
    }
     public function office()
{
    return $this->belongsTo(Office::class, 'office_id');
}
}

