<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetVerification extends Model
{
    protected $table = 'branch_asset_verifications';

    protected $fillable = [
        'office_id',
        'period',
        'requested_by',
        'submitted_by',
        'submitted_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
