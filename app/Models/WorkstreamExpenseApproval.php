<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstreamExpenseApproval extends Model
{
    protected $table = 'ws1_expense_approvals';

    protected $fillable = [
        'expense_id',
        'action',
        'acted_by_id',
        'comments',
        'acted_at',
    ];

    protected $dates = ['acted_at'];

    public function expense()
    {
        return $this->belongsTo(WorkstreamExpense::class, 'expense_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by_id');
    }
}
