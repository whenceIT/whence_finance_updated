<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'office_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Restrict shares to a reporting period, mirroring RecoveryCase::scopeForPeriod.
     */
    public function scopeForPeriod(Builder $query, string $period, ?string $dateFrom = null, ?string $dateTo = null): Builder
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            return $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateFrom)->startOfDay(),
                \Carbon\Carbon::parse($dateTo)->endOfDay(),
            ]);
        }

        return match ($period) {
            'week'    => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month'   => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'quarter' => $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]),
            'year'    => $query->whereYear('created_at', now()->year),
            default   => $query,
        };
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanTransaction(): BelongsTo
    {
        return $this->belongsTo(LoanTransaction::class, 'loan_txn_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
