<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeLoan extends Model
{
    use SoftDeletes;

    protected $table = 'office_loans';

    protected $fillable = [
        'office_id',
        'staff_id',
        'principal',
        'interest',
        'status',
        'approved_at',
        'disbursed_at',
    ];

    protected $casts = [
        'principal'    => 'float',
        'interest'     => 'float',
        'approved_at'  => 'datetime',
        'disbursed_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Statuses
    // -------------------------------------------------------------------------

    const STATUS_PENDING       = 'pending';
    const STATUS_APPROVED      = 'approved';
    const STATUS_DISBURSED     = 'disbursed';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_FULLY_PAID    = 'fully_paid';
    const STATUS_DECLINED      = 'declined';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING        => 'Pending',
            self::STATUS_APPROVED       => 'Approved',
            self::STATUS_DISBURSED      => 'Disbursed',
            self::STATUS_PARTIALLY_PAID => 'Partially Paid',
            self::STATUS_FULLY_PAID     => 'Fully Paid',
            self::STATUS_DECLINED       => 'Declined',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            self::STATUS_PENDING        => 'label-warning',
            self::STATUS_APPROVED       => 'label-info',
            self::STATUS_DISBURSED      => 'label-primary',
            self::STATUS_PARTIALLY_PAID => 'label-warning',
            self::STATUS_FULLY_PAID     => 'label-success',
            self::STATUS_DECLINED       => 'label-danger',
        ][$this->status] ?? 'label-default';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The branch/office this loan was issued to.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * The staff member responsible for creating/managing this loan.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * All transactions (disbursements + repayments) for this loan.
     */
    public function transactions()
    {
        return $this->hasMany(OfficeLoanTransaction::class, 'loan_id')->orderBy('created_at', 'asc');
    }

    /**
     * Only approved transactions — used for balance calculations.
     */
    public function approvedTransactions()
    {
        return $this->hasMany(OfficeLoanTransaction::class, 'loan_id')
                    ->where('status', OfficeLoanTransaction::STATUS_APPROVED);
    }

    // -------------------------------------------------------------------------
    // Business logic / Balance calculations
    // -------------------------------------------------------------------------

    /**
     * Total amount payable = principal + interest.
     */
    public function getTotalPayableAttribute(): float
    {
        return $this->principal + $this->interest;
    }

    /**
     * Total repaid = sum of approved credit transactions.
     * Pending and declined repayments do NOT count.
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->approvedTransactions()->sum('credit');
    }

    /**
     * Outstanding balance = total payable - total paid.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return max(0, $this->total_payable - $this->total_paid);
    }

    /**
     * Recalculate and persist the correct status based on outstanding balance.
     * Called after any repayment is approved.
     *
     * Statuses that can be auto-updated are disbursed, partially_paid, fully_paid.
     * pending / approved / declined are NOT touched here.
     */
    public function recalculateStatus(): void
    {
        if (!in_array($this->status, [
            self::STATUS_DISBURSED,
            self::STATUS_PARTIALLY_PAID,
            self::STATUS_FULLY_PAID,
        ])) {
            return;
        }

        if ($this->outstanding_balance <= 0) {
            $this->status = self::STATUS_FULLY_PAID;
        } elseif ($this->total_paid > 0) {
            $this->status = self::STATUS_PARTIALLY_PAID;
        } else {
            $this->status = self::STATUS_DISBURSED;
        }

        $this->save();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_APPROVED,
            self::STATUS_DISBURSED,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function scopeByOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId);
    }
}
