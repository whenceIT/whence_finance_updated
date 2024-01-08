<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table = "loan_products";
    public $timestamps = false;

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
