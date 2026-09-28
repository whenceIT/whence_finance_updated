<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetDamageReport extends Model
{
    protected $table = 'branch_asset_damage_reports';

    protected $fillable = [
        'office_id',
        'category_id',
        'quantity_affected',
        'reported_date',
        'description',
        'photo',
        'status',
        'reported_by',
        'notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function category()
    {
        return $this->belongsTo(BranchAssetCategory::class, 'category_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function repair()
    {
        return $this->hasOne(BranchAssetRepair::class, 'damage_report_id');
    }
}
