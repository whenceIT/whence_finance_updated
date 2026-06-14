<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{

    protected $table = "offices";

    protected $fillable = [
        'name',
        'parent_id',
        'external_id',
        'opening_date',
        'branch_capacity',
        'address',
        'phone',
        'email',
        'notes',
        'manager_id',
        'active',
        'default_office',
        'province_id',
        'district_id',
        'district_regional_id'
    ];

    public function parent()
    {
        return $this->hasOne(Office::class, 'id', 'parent_id');
    }

    //Province added
    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function district()
    {
        return $this->hasOne(District::class, 'id', 'district_id');
    }

    public function districtRegional()
    {
        return $this->hasOne(DistrictRegional::class, 'id', 'district_regional_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

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
        return $this->hasMany(LoanGuarantor::class, 'loan_id', 'id');
    }


    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
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

    public static function officeName($id)
    {
        return self::where('id', $id)->first(); 
    }

    public static function provinceName($id)
    {
        return self::where('id', $id)
            ->join('province', 'offices.province_id', '=', 'province.id')
            ->first(['province.name as name', 'province.id as id']);
    }
}
