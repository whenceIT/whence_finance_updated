<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['advance_id', 'amount_paid', 'last_update_date'];

    public function advance()
    {
        return $this->belongsTo(Advance::class);
    }
}

