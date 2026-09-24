<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoanOldSchedule extends Model
{
    protected $table = 'payroll_loan_old_schedule';

    protected $fillable = [
        'disbursement_amount',
        'repayment_9_months',
        'repayment_12_months',
        'repayment_18_months',
        'repayment_24_months',
    ];

    protected $casts = [
        'disbursement_amount'  => 'decimal:2',
        'repayment_9_months'   => 'decimal:2',
        'repayment_12_months'  => 'decimal:2',
        'repayment_18_months'  => 'decimal:2',
        'repayment_24_months'  => 'decimal:2',
    ];

    /**
     * Lookup the monthly repayment for a given disbursement amount and term (months).
     * Returns null if no matching row or term is not available.
     */
    public static function lookupRepayment(float $amount, int $termMonths): ?float
    {
        $row = static::where('disbursement_amount', $amount)->first();

        if (!$row) {
            return null;
        }

        return match ($termMonths) {
            9  => $row->repayment_9_months,
            12 => $row->repayment_12_months,
            18 => $row->repayment_18_months,
            24 => $row->repayment_24_months,
            default => null,
        };
    }
}
