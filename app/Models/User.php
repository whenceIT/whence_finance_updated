<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentUser
{

    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',
        'address',
        'office_id',
        'notes',
        'phone',
        'gender',
        'enable_google2fa',
        'blocked',
        'google2fa_secret',
        'time_limit',
        'from_time',
        'to_time',
        'access_days',
        'picture',
    ];
    public function payroll()
    {
        return $this->hasMany(Payroll::class, 'user_id', 'id');
    }
    public function client_users()
    {
        return $this->hasMany(ClientUser::class, 'user_id', 'id');
    }
    public function group_users()
    {
        return $this->hasMany(ClientUser::class, 'user_id', 'id');
    }
    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function cycle_dates()
    {
        return $this->hasOne(CycleDates::class, 'loan_officer_id','id');
    }

    public function loan()
    {
        return $this->hasMany(Loan::class,'loan_officer_id','id');
    }

    public function role(){
        return $this->hasOne(UserRole::class,'user_id','id','role_id');
    }



}
