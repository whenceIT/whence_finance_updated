<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProvisioningCriteria extends Model
{
    protected $table = "loan_provisioning_criteria";

    public function gl_account_liability()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_liability_id');
    }

    public function gl_account_expense()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_expense_id');
    }


}
