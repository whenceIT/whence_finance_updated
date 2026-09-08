<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalMatrix extends Model
{
    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'level',
        'required_approver_id',
        'branch_id',
        'district_id',
        'province_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function requiredApprover()
    {
        return $this->belongsTo(User::class, 'required_approver_id');
    }

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
