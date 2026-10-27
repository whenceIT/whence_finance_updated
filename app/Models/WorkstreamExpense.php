<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstreamExpense extends Model
{
    protected $table = 'ws1_expenses';

    protected $fillable = [
        'office_id',
        'created_by_id',
        'expense_type_id',
        'gl_account_id',
        'name',
        'amount',
        'date',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class, 'gl_account_id');
    }

    public function approvals()
    {
        return $this->hasMany(WorkstreamExpenseApproval::class, 'expense_id');
    }

    public function payments()
    {
        return $this->hasMany(WorkstreamExpensePayment::class, 'expense_id');
    }

    public function documents()
    {
        return $this->hasMany(WorkstreamExpenseDocument::class, 'expense_id');
    }

    public function latestPayment()
    {
        return $this->hasOne(WorkstreamExpensePayment::class, 'expense_id')->orderBy('id', 'desc');
    }
}
