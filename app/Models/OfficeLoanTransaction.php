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
    ];

    protected $casts = [
        'debit'       => 'float',
        'credit'      => 'float',
        'approved_at' => 'datetime',
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
