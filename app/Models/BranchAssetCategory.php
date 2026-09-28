<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetCategory extends Model
{
    protected $table = 'branch_asset_categories';

    protected $fillable = [
        'name',
        'icon',
        'allows_individual_tracking',
        'active',
    ];

    protected $casts = [
        'allows_individual_tracking' => 'boolean',
        'active' => 'boolean',
    ];

    public function inventories()
    {
        return $this->hasMany(BranchAssetInventory::class, 'category_id');
    }

    public function damageReports()
    {
        return $this->hasMany(BranchAssetDamageReport::class, 'category_id');
    }
}
