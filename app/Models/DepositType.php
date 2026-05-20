<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositType extends Model
{
    use HasFactory;

    protected $table = 'deposit_types';

    protected $fillable = [
        'name',
        'bank',
        'gl_account',
        'sort_order',
    ];

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'deposit_type', 'id');
    }
}
// 1. Building and Infrastructure Contributions
// 2. Statutory Deposits
// 3. Administration Fee Deposits
// 4. Salary Deposits
// 5. Savings Deposits

// 1. Building and Infrastructure 
// 2. ⁠Debt Repayment(For Branches in Debt)

// 3. ⁠Administration Fee
// 4. ⁠Statutory Deposits 

// 5. ⁠Salaries 
// 6. ⁠Savings 
