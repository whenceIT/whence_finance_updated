<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $table = "clients";

    protected $fillable = [
        'approved_dormant',
        'nrc_number',
        'tpin',
        'address_line1',
        'address_line2',
        'city',
        'employer',
        'employer_address',
        'business_name',
        'business_type',
        'annual_income',
        'phone_primary',
        'phone_secondary',
        'email_primary',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_phone',
        'next_of_kin_address',
        'guarantor_name',
        'guarantor_nrc',
        'guarantor_phone',
        'guarantor_address',
        'guarantor_employer',
        'guarantor_relationship',
    ];

    public function identifications()
    {
        return $this->hasMany(ClientIdentification::class, 'client_id', 'id');
    }

    public function next_of_kin()
    {
        return $this->hasMany(ClientNextOfKin::class, 'client_id', 'id');
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

    public function borrower()
    {
        return $this->hasOne(Borrower::class, 'id', 'borrower_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'client_id', 'id');
    }

    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }
    public function staff()
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }
    public function pictureUrl()
    {
        if (!empty($this->picture)) {
            $spaceName = "wfssystem";
            $baseUrl = "https://$spaceName.nyc3.digitaloceanspaces.com/";
            return $baseUrl . $this->picture;
        }
        return asset('public/uploads/image.png');
    }

    public function vehicles()
{
    return $this->hasMany(Vehicle::class);
}

public function motorVehicleLoans()
    {
        return $this->hasMany(Loan::class)->where('loan_product_id', 0);
    }
}
