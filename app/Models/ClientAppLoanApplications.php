<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAppLoanApplications extends Model
{
    protected $table = "client_app_loan_applications";

      public function office()
    {
        return $this->hasOne(Office::class, 'id', 'branch');
    }

}

