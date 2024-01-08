<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupLoanAllocation extends Model
{
    protected $table = "group_loan_allocation";

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
