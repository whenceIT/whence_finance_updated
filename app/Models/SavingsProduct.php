<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsProduct extends Model
{
    protected $table = "savings_products";


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function charges()
    {
        return $this->hasMany(SavingsProductCharge::class, 'savings_product_id', 'id');
    }

    public function gl_account_savings_reference()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_savings_reference_id');
    }

    public function gl_account_overdraft_portfolio()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_overdraft_portfolio_id');
    }

    public function gl_account_savings_control()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_savings_control_id');
    }

    public function gl_account_interest_on_savings()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_interest_on_savings_id');
    }

    public function gl_account_savings_written_off()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_savings_written_off_id');
    }

    public function gl_account_income_interest()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_interest_id');
    }

    public function gl_account_income_fee()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_fee_id');
    }

    public function gl_account_income_penalty()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_income_penalty_id');
    }

}
