<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class DailyIncome extends Model
{
    protected $table = 'creative_daily_incomes';

    protected $fillable = [
        'branch_id',
        'income_date',
        'income_category',
        'income_source',
        'reference_number',
        'amount',
        'description',
        'recorded_by_user_id',
        'payment_method',
        'received_from',
        'metadata',
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected $dates = ['income_date'];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Office::class, 'branch_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by_user_id');
    }

    public static function getCategories()
    {
        return [
            'Loan Interest' => 'Loan Interest',
            'Loan Principal' => 'Loan Principal',
            'Loan Fees' => 'Loan Fees',
            'Investment Income' => 'Investment Income',
            'Service Charges' => 'Service Charges',
            'Penalty Income' => 'Penalty Income',
            'Commission Income' => 'Commission Income',
            'Rental Income' => 'Rental Income',
            'Other Income' => 'Other Income',
        ];
    }

    public static function getSources()
    {
        return [
            'Loan Repayments' => 'Loan Repayments',
            'New Loan Disbursements' => 'New Loan Disbursements',
            'Investment Returns' => 'Investment Returns',
            'Service Fees' => 'Service Fees',
            'Late Payment Penalties' => 'Late Payment Penalties',
            'Insurance Commissions' => 'Insurance Commissions',
            'Rental Collections' => 'Rental Collections',
            'Other' => 'Other',
        ];
    }
}