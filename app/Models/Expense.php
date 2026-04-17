<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    protected $fillable = [
        'office_id',
        'created_by_id',
        'expense_type_id',
        'name',
        'amount',
        'date',
        'year',
        'month',
        'gl_account_id',
        'notes',
        'is_attribution',
    ];

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function type()
    {
        return $this->hasOne(ExpenseType::class, 'id', 'expense_type_id');
    }

    public function gl_account()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_id');
    }
}
