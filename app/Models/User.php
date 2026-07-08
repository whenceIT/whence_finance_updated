<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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
        'province_id',
        'notes',
        'phone',
        'gender',
        'nrc_id',
        'enable_google2fa',
        'blocked',
        'google2fa_secret',
        'time_limit',
        'from_time',
        'to_time',
        'access_days',
        'picture',
        'status',
        'salutation',
        'employment_type',
        'mobile_number',
        'personal_email',
        'current_address',
        'emergency_contact_name',
        'emergency_phone',
        'relation_to_emergency',
        'reports_to',
        'confirmation_date',
        'qualification',
        'school_university',
        'level_of_education',
        'year_completed',
        'major',
        'has_completed_profile',
        'date_of_birth',
        'date_of_joining',
        'company',
        'employee_number',
        'department',
        'designation',
        'branch',
        'salary_currency',
        'salary_mode',
        'bank_name',
        'bank_account_number',
        'marital_status',
        'health_details',
        'health_insurance_provider',
        'health_insurance_number',
        'external_company',
        'external_designation',
        'external_contact',
        'external_total_experience',
        'internal_branch',
        'internal_designation',
        'internal_from_date',
        'internal_to_date',
        'position_id',
        'tpin',
        'ssn',
        'nhima',
        'salary_details',
        'daily_learning',
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

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function cycle_dates()
    {
        return $this->hasOne(CycleDates::class, 'loan_officer_id', 'id');
    }

    public function loan()
    {
        return $this->hasMany(Loan::class, 'loan_officer_id', 'id');
    }

    public function role()
    {
        return $this->hasOne(UserRole::class, 'user_id', 'id', 'role_id');
    }
    public function advances()
    {
        return $this->hasMany(Advance::class, 'user_id', 'id');
    }

    public function leave()
    {
        return $this->hasMany(Leave::class, 'user_id', 'id');
    }


    public function dual_role()
    {
        return $this->hasOne(DualRole::class, 'user_id', 'id', 'role_id');
    }

    public function userPolicyResponses()
    {
        return $this->hasMany(UserPolicyResponse::class);
    }

    public function inductionChecklist()
    {
        return $this->hasMany(InductionChecklist::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function getPositionNameAttribute()
    {
        $position = \Illuminate\Support\Facades\DB::table('job_positions')->where('id', $this->position_id)->first();
        return $position ? $position->name : '';
    }
}