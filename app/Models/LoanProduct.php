<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table = "loan_products";
    public $timestamps = false;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'fund_id',
        'currency_id',
        'decimals',
        'minimum_principal',
        'default_principal',
        'maximum_principal',
        'minimum_loan_term',
        'default_loan_term',
        'maximum_loan_term',
        'repayment_frequency',
        'repayment_frequency_type',
        'minimum_interest_rate',
        'default_interest_rate',
        'maximum_interest_rate',
        'interest_rate_type',
        'grace_on_interest_charged',
        'grace_on_principal',
        'grace_on_interest_payment',
        'allow_custom_grace',
        'allow_standing_instuctions',
        'interest_method',
        'armotization_method',
        'interest_calculation_period_type',
        'year_days',
        'month_days',
        'loan_transaction_strategy',
        'include_in_cycle',
        'lock_guarantee',
        'allocate_overpayments',
        'allow_additional_charges',
        'accounting_rule',
        'npa_days',
        'arrears_grace_days',
        'npa_suspend_income',
        'gl_account_fund_source_id',
        'gl_account_loan_portfolio_id',
        'gl_account_receivable_interest_id',
        'gl_account_receivable_fee_id',
        'gl_account_receivable_penalty_id',
        'gl_account_loan_over_payments_id',
        'gl_account_suspended_income_id',
        'gl_account_income_interest_id',
        'gl_account_income_fee_id',
        'gl_account_income_penalty_id',
        'gl_account_income_recovery_id',
        'gl_account_loans_written_off_id',
        'is_motor_vehicle',
        'service_fee',
        'processing_fee',
        'insurance_fee',
        'valuation_fee',
        'inspection_fee',
        'penalty_rate',
        'recovery_charges',
        'effective_date',
        'version',
        'is_active',
        'approved_by_id',
        'approved_at',
    ];

    protected $casts = [
        'is_motor_vehicle' => 'boolean',
        'is_active' => 'boolean',
        'allow_custom_grace' => 'boolean',
        'allow_standing_instuctions' => 'boolean',
        'include_in_cycle' => 'boolean',
        'lock_guarantee' => 'boolean',
        'allocate_overpayments' => 'boolean',
        'allow_additional_charges' => 'boolean',
        'npa_suspend_income' => 'boolean',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function charges()
    {
        return $this->hasMany(LoanProductCharge::class, 'loan_product_id', 'id');
    }

    public function gl_account_loan_portfolio()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_loan_portfolio_id');
    }

    public function gl_account_fund_source()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_fund_source_id');
    }

    public function gl_account_receivable_interest()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_receivable_interest_id');
    }

    public function gl_account_receivable_fee()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_receivable_fee_id');
    }

    public function gl_account_receivable_penalty()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_receivable_penalty_id');
    }

    public function gl_account_loan_overpayment()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_loan_over_payments_id');
    }

    public function gl_account_income_interest()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_interest_id');
    }

    public function gl_account_suspended_income()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_suspended_income_id');
    }

    public function gl_account_income_fee()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_fee_id');
    }

    public function gl_account_income_penalty()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_penalty_id');
    }

    public function gl_account_income_recovery()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_recovery_id');
    }

    public function gl_account_loans_written_off()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_loans_written_off_id');
    }
}
