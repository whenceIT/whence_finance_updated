<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAppLoanApplications extends Model
{
    protected $table = "client_app_loan_applications";
     public $timestamps = false;

      public function office()
    {
        return $this->hasOne(Office::class, 'id', 'branch');
    }

    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}

}

