<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTopUp extends Model
{
    protected $table = "loan_topup";


    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }


    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public $timestamps = false;

}
