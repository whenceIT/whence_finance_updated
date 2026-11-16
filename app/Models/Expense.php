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
        'admin_approved_by_id',
'admin_approved_at',
'admin_approval_notes',

'risk_approved_by_id',
'risk_approved_at',
'risk_approval_notes',

'payment_status',
'payment_reference',
'payment_response',

'payment_phone',
'payment_bank_id',
'payment_account_number',
'payment_operator',
'withinhere_user_id',
'total_deducted',
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
