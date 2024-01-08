<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    protected $table = "loan_applications";


    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class, 'loan_application_id', 'id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_id');
    }

    public function declined_by()
    {
        return $this->hasOne(User::class, 'id', 'declined_by_id');
    }
}
