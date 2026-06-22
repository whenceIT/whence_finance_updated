<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitShare extends Model
{
    protected $table = 'unit_shares';

    protected $fillable = [
        'unit',
        'amount',
        'loan_id',
        'loan_txn_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the loan that owns this unit share
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the loan transaction associated with this unit share
     */
    public function loanTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'loan_txn_id');
    }
}
