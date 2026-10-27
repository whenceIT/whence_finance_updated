<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstreamExpensePayment extends Model
{
    protected $table = 'ws1_expense_payments';

    protected $fillable = [
        'expense_id',
        'payment_method',
        'reference_number',
        'amount',
        'status',
        'notes',
        'recorded_by_id',
        'recorded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function expense()
    {
        return $this->belongsTo(WorkstreamExpense::class, 'expense_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
