<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class DailyExpense extends Model
{
    protected $table = 'creative_daily_expenses';

    protected $fillable = [
        'branch_id',
        'expense_date',
        'expense_category',
        'expense_type',
        'reference_number',
        'amount',
        'description',
        'recorded_by_user_id',
        'approved_by_user_id',
        'payment_method',
        'paid_to',
        'status',
        'metadata',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected $dates = ['expense_date'];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Office::class, 'branch_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_user_id');
    }

    public static function getCategories()
    {
        return [
            'Staff Salaries' => 'Staff Salaries',
            'Office Rent' => 'Office Rent',
            'Utilities' => 'Utilities',
            'Transportation' => 'Transportation',
            'Marketing' => 'Marketing',
            'Office Supplies' => 'Office Supplies',
            'Equipment Maintenance' => 'Equipment Maintenance',
            'Insurance' => 'Insurance',
            'Professional Fees' => 'Professional Fees',
            'Training' => 'Training',
            'Travel' => 'Travel',
            'Communication' => 'Communication',
            'Bank Charges' => 'Bank Charges',
            'Depreciation' => 'Depreciation',
            'Other Expenses' => 'Other Expenses',
        ];
    }

    public static function getTypes()
    {
        return [
            'Fixed' => 'Fixed',
            'Variable' => 'Variable',
            'One-time' => 'One-time',
            'Recurring' => 'Recurring',
        ];
    }
}