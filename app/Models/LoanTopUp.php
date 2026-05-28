<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTopUp extends Model
{
    protected $table = "loan_topup";
    protected $fillable = [];
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
