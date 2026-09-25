<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchAssetInventory extends Model
{
    protected $table = 'branch_asset_inventories';

    protected $fillable = [
        // ── Asset register identity ───────────────────────────────────────
        'asset_id',           // WFS-xxxx unique identifier from the register
        'location_id',        // FK → asset_locations (physical location name)
        'office_id',          // FK → offices (branch link — nullable)
        'category_id',        // FK → branch_asset_categories

        // ── Item detail (from assets.md columns) ─────────────────────────
        'item_description',   // e.g. "Office Table", "Toyota Axio"
        'serial_number',      // Asset / Serial No.
        'unit_cost',          // Unit Cost (K)
        'total_value',        // Total Value (K)

        // ── Quantity & condition tracking (from Goa.md spec) ─────────────
        'total',
        'working',
        'damaged',
        'missing',
        'under_repair',

        // ── Condition / allocation metadata (from assets.md columns) ─────
        'condition_text',     // Free-text condition as recorded in the register
        'allocated_to',       // Who the item is allocated to
        'remarks',            // General remarks
        'action_required',    // Action Required column
        'valuation_basis',    // Valuation Basis column
        'source_ref',         // Source row reference

        // ── Verification metadata ─────────────────────────────────────────
        'last_verified_at',
        'last_verified_by',
    ];

    protected $casts = [
        'last_verified_at' => 'datetime',
        'unit_cost'        => 'decimal:2',
        'total_value'      => 'decimal:2',
    ];

    // ── Computed attributes ───────────────────────────────────────────────────

    /**
     * Condition percentage: working items as % of total.
     * Returns null when total is 0 (no inventory recorded yet).
     */
    public function getConditionPercentageAttribute(): ?float
    {
        if ((int) $this->total === 0) {
            return null;
        }
        return round(($this->working / $this->total) * 100, 1);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function location()
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function category()
    {
        return $this->belongsTo(BranchAssetCategory::class, 'category_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'last_verified_by');
    }

    public function individualItems()
    {
        return $this->hasMany(BranchAssetIndividualItem::class, 'inventory_id');
    }
}
