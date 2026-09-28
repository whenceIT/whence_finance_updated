<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
    protected $table = 'asset_locations';

    protected $fillable = [
        'name',
        'type',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function inventories()
    {
        return $this->hasMany(BranchAssetInventory::class, 'location_id');
    }
}
