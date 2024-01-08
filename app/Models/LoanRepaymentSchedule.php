<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRepaymentSchedule extends Model
{
    protected $table = "loan_repayment_schedules";

    public function charges()
    {
        return $this->hasMany(LoanCharge::class, 'loan_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class, 'loan_id', 'id')->orderBy('due_date', 'asc');
    }

    public function comments()
    {
        return $this->hasMany(LoanComment::class, 'loan_id', 'id')->orderBy('created_at', 'desc');;
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class, 'loan_id', 'id')->orderBy('date', 'asc');;
    }

    public function payments()
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id', 'id')->orderBy('collection_date', 'asc');;
    }

    public function collateral()
    {
        return $this->hasMany(Collateral::class, 'loan_id', 'id');
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class, 'loan_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }

    public function loan_purpose()
    {
        return $this->hasOne(LoanPurpose::class, 'id', 'loan_purpose_id');
    }

    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
    }

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function loan_repayment_method()
    {
        return $this->hasOne(LoanRepaymentMethod::class, 'id', 'repayment_method_id');
    }

    public function loan_disbursed_by()
    {
        return $this->hasOne(LoanDisbursedBy::class, 'id', 'loan_disbursed_by_id');
    }

    public function loan_officer()
    {
        return $this->hasOne(User::class, 'id', 'loan_officer_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_id');
    }

    public function rejected_by()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by_id');
    }

    public function withdrawn_by()
    {
        return $this->hasOne(User::class, 'id', 'withdrawn_by_id');
    }

    public function rescheduled_by()
    {
        return $this->hasOne(User::class, 'id', 'rescheduled_by_id');
    }

    public function closed_by()
    {
        return $this->hasOne(User::class, 'id', 'closed_by_id');
    }

    public function disbursed_by()
    {
        return $this->hasOne(User::class, 'id', 'disbursed_by_id');
    }
}
