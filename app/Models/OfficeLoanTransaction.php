<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLoanTransaction extends Model
{
    protected $table = 'office_loan_transactions';

    protected $fillable = [
        'loan_id',
        'office_id',
        'debit',
        'credit',
        'approved_by',
        'status',
        'notes',
        'approved_at',
        'transaction_type',
    ];

    protected $casts = [
        'debit'       => 'float',
        'credit'      => 'float',
        'approved_at' => 'datetime',
        'transaction_type' => 'string',
    ];

    // -------------------------------------------------------------------------
    // Statuses
    // -------------------------------------------------------------------------

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
        ];
    }

    // -------------------------------------------------------------------------
    // Transaction Types
    // -------------------------------------------------------------------------

    const TYPE_DISBURSEMENT     = 'disbursement';
    const TYPE_INTEREST_INITIAL = 'interest_initial';
    const TYPE_PENALTY          = 'penalty';
    const TYPE_TOPUP            = 'topup';
    const TYPE_WAIVER           = 'waiver';

    public static function transactionTypes(): array
    {
        return [
            self::TYPE_DISBURSEMENT     => 'Disbursement',
            self::TYPE_INTEREST_INITIAL => 'Interest Initial',
            self::TYPE_PENALTY          => 'Penalty',
            self::TYPE_TOPUP            => 'Top-up',
            self::TYPE_WAIVER           => 'Waiver',
        ];
    }

    public function getTransactionTypeLabelAttribute(): string
    {
        return self::transactionTypes()[$this->transaction_type] ?? ucfirst(str_replace('_', ' ', $this->transaction_type));
    }

    public function getTransactionTypeBadgeClassAttribute(): string
    {
        return [
            self::TYPE_DISBURSEMENT     => 'label-primary',
            self::TYPE_INTEREST_INITIAL => 'label-info',
            self::TYPE_PENALTY          => 'label-danger',
            self::TYPE_TOPUP            => 'label-warning',
            self::TYPE_WAIVER           => 'label-success',
        ][$this->transaction_type] ?? 'label-default';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            self::STATUS_PENDING  => 'label-warning',
            self::STATUS_APPROVED => 'label-success',
            self::STATUS_DECLINED => 'label-danger',
        ][$this->status] ?? 'label-default';
    }

    /**
     * Determine the human-readable type of this transaction.
     * A disbursement has debit > 0; a repayment has credit > 0.
     */
    public function getTypeAttribute(): string
    {
        if ($this->debit > 0) {
            return 'Disbursement';
        }
        if ($this->credit > 0) {
            return 'Repayment';
        }
        return 'Transaction';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The RTI loan this transaction belongs to.
     */
    public function loan()
    {
        return $this->belongsTo(OfficeLoan::class, 'loan_id');
    }

    /**
     * The office/branch involved in this transaction.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * The user who approved or declined this transaction.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRepayments($query)
    {
        return $query->where('credit', '>', 0);
    }

    public function scopeDisbursements($query)
    {
        return $query->where('debit', '>', 0);
    }
}
