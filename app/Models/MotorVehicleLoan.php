<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotorVehicleLoan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_id',
        'vehicle_id',
        'client_id',
        'vehicle_value',
        'ltv_percent',
        'requested_amount',
        'approved_amount',
        'status',
        'remarks',
        'loan_consultant_id',
        'originating_branch_id',
        'branch_assessor_id',
        'district_id',
        'province_id',
        'vehicle_status',
        'custody_status',
        'current_storage_location',
        'current_custodian_id',
        'current_custodian_phone',
        'current_custodian_nrc',
        'current_custodian_alternative_contact',
        'referral_branch_id',
        'referral_officer_id',
        'referral_date',
        'referral_notes',
        'parent_loan_id',
        'is_top_up',
        'is_reloan',
        'loan_band',
        'incentive_status'
    ];

    protected $casts = [
        'is_top_up' => 'boolean',
        'is_reloan' => 'boolean',
        'referral_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanConsultant()
    {
        return $this->belongsTo(User::class, 'loan_consultant_id');
    }

    public function originatingBranch()
    {
        return $this->belongsTo(Office::class, 'originating_branch_id');
    }

    public function branchAssessor()
    {
        return $this->belongsTo(User::class, 'branch_assessor_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function currentCustodian()
    {
        return $this->belongsTo(User::class, 'current_custodian_id');
    }

    public function referralBranch()
    {
        return $this->belongsTo(Office::class, 'referral_branch_id');
    }

    public function referralOfficer()
    {
        return $this->belongsTo(User::class, 'referral_officer_id');
    }

    public function parentLoan()
    {
        return $this->belongsTo(MotorVehicleLoan::class, 'parent_loan_id');
    }

    public function childLoans()
    {
        return $this->hasMany(MotorVehicleLoan::class, 'parent_loan_id');
    }

    public function workflowStages()
    {
        return $this->hasMany(MotorVehicleLoanWorkflowStage::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(MotorVehicleLoanStatusHistory::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(MotorVehicleAuditLog::class);
    }

    public function complianceScreenings()
    {
        return $this->hasMany(ComplianceScreening::class);
    }
}
