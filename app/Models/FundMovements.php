<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FundMovements extends Model
{
    protected $table = "fund_movements";

     public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function account()
    {
          return $this->hasOne(BankAccount::class, 'id', 'source_account');
    }

     public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }


}