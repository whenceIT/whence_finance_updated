<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoanOldSchedule extends Model
{
    protected $table = 'payroll_loan_old_schedule';
    protected $guarded = [];

    protected $casts = [
        'disbursement_amount'  => 'decimal:2',
        'repayment_9_months'   => 'decimal:2',
        'repayment_12_months'  => 'decimal:2',
        'repayment_18_months'  => 'decimal:2',
        'repayment_24_months'  => 'decimal:2',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        for ($m = 1; $m <= 60; $m++) {
            $key = 'repayment_' . $m . '_months';
            if (!isset($this->casts[$key])) {
                $this->casts[$key] = 'decimal:2';
            }
        }
    }

    /**
     * Lookup the monthly repayment for a given disbursement amount and term (months).
     * Supports any term from 1 to 60 months.
     * Returns null if no matching row or term is not available.
     */
    public static function lookupRepayment(float $amount, int $termMonths): ?float
    {
        $row = static::where('disbursement_amount', $amount)->first();

        if (!$row) {
            return null;
        }

        $column = 'repayment_' . $termMonths . '_months';
        return $row->$column ?? null;
    }
}
