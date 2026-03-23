<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryPayment extends Model
{

    protected $fillable = [
        'recovery_case_id','transaction_id','recorded_by','receipt_number','amount','payment_method',
        'payment_date','payment_reference','bank_name','recoveries_dept_amount','origin_branch_amount',
        'supporting_branch_amount','is_settlement','outstanding_before','outstanding_after','notes',
        'status',
    ];

    protected $casts = [
        'payment_date'             => 'date',
        'amount'                   => 'decimal:2',
        'recoveries_dept_amount'   => 'decimal:2',
        'origin_branch_amount'     => 'decimal:2',
        'supporting_branch_amount' => 'decimal:2',
        'is_settlement'            => 'boolean',
        'status'                   => 'integer',
    ];

    protected $attributes = [
        'status' => 0,
    ];

    public function recoveryCase(): BelongsTo { return $this->belongsTo(RecoveryCase::class); }
    public function transaction(): BelongsTo   { return $this->belongsTo(\App\Models\LoanTransaction::class); }
    public function recordedBy(): BelongsTo   { return $this->belongsTo(User::class, 'recorded_by'); }

    /**
     * Calculate and populate attribution amounts from the parent case percentages.
     */
    public static function createWithAttribution(RecoveryCase $case, array $data): static
    {
        $data['recoveries_dept_amount']   = $data['amount'] * ($case->recoveries_dept_attribution_pct / 100);
        $data['origin_branch_amount']     = $data['amount'] * ($case->origin_branch_attribution_pct / 100);
        $data['supporting_branch_amount'] = $data['amount'] * ($case->supporting_branch_attribution_pct / 100);
        return static::create($data);
    }

    public static function generateReceiptNumber(): string
    {
        return 'RCP-' . strtoupper(uniqid());
    }

    /**
     * Scope to include all payments including those with status = 1 (archived).
     */
    public function scopeWithArchived($query)
    {
        return $query->withoutGlobalScopes();
    }

    /**
     * Scope to get only archived payments (status != 1).
     */
    public function scopeArchived($query)
    {
        return $query->withoutGlobalScopes()->where('status', '!=', 1);
    }
}