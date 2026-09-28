<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetIndividualItem extends Model
{
    protected $table = 'branch_asset_individual_items';

    protected $fillable = [
        'inventory_id',
        'serial_number',
        'assigned_to',
        'condition',
        'notes',
    ];

    public function inventory()
    {
        return $this->belongsTo(BranchAssetInventory::class, 'inventory_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
