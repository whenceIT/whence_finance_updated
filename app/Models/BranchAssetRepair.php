<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetRepair extends Model
{
    protected $table = 'branch_asset_repairs';

    protected $fillable = [
        'damage_report_id',
        'repair_date',
        'repair_cost',
        'repair_provider',
        'description',
        'invoice_path',
        'date_returned',
        'condition_after',
    ];

    protected $casts = [
        'repair_date'   => 'date',
        'date_returned' => 'date',
        'repair_cost'   => 'decimal:2',
    ];

    public function damageReport()
    {
        return $this->belongsTo(BranchAssetDamageReport::class, 'damage_report_id');
    }
}
