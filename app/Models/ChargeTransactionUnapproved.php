<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeTransactionUnapproved extends Model
{
    protected $table = 'charge_transactions_unapproved';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}

