<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    protected $table = 'collaterals';

    public const CATEGORIES = [
        'electronics'   => 'Electronics',
        'furniture'     => 'Furniture',
        'appliances'    => 'Appliances',
        'vehicles'      => 'Vehicles',
        'jewelry'       => 'Jewelry & Gold',
        'real_estate'   => 'Real Estate',
        'machinery'     => 'Machinery & Equipment',
        'agriculture'   => 'Agriculture & Livestock',
        'construction'  => 'Construction Material',
        'clothing'      => 'Clothing & Textiles',
        'tools'         => 'Tools',
        'other'         => 'Other',
    ];

    public static function categoryOptions(): array
    {
        return self::CATEGORIES;
    }

    protected $fillable = [
        'name',
        'category',
        'serial_num',
        'description',
        'initial_price',
        'current_worth',
        'approved_value',
        'sold_price',
        'buyer_name',
        'buyer_phone',
        'buyer_nrc',
        'penalty',
        'disposal_costs',
        'loan_id',
        'collateral_type_id',
        'created_by_id',
        'status',
        'condition',
        'date_purchased',
        'date_resold',
        'province_id',
        'district_id',
        'office_id',
        'stage',
        'stage_icon',
        'new_approval_status',
        'pledged_at',
        'seized_at',
        'valuated_at',
        'listed_at',
        'sold_at',
        'written_off_at',
        'released_at',
        'release_requested_at',
        'vetted_valuation',
        'vetted_valuation_cost',
        'vetted_valuation_by',
        'vetted_valuation_status',
        'vvc_items',
    ];

    protected $casts = [
        'date_purchased' => 'date',
        'date_resold'    => 'date',
        'pledged_at'     => 'date',
        'seized_at'      => 'date',
        'valuated_at'    => 'date',
        'listed_at'      => 'date',
        'sold_at'        => 'date',
        'written_off_at' => 'date',
        'released_at'    => 'date',
        'release_requested_at' => 'date',
        'initial_price'  => 'decimal:2',
        'current_worth'  => 'decimal:2',
        'approved_value' => 'decimal:2',
        'sold_price'     => 'decimal:2',
        'penalty'        => 'decimal:2',
        'disposal_costs' => 'array',
        'vetted_valuation'      => 'decimal:2',
        'vetted_valuation_cost' => 'decimal:2',
        'vetted_valuation_by'   => 'integer',
        'vetted_valuation_status' => 'integer',
        'vvc_items'             => 'array',
    ];

    public function type()
    {
        return $this->hasOne(CollateralType::class, 'id', 'collateral_type_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function vettedBy()
    {
        return $this->hasOne(User::class, 'id', 'vetted_valuation_by');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function statusChanges()
    {
        return $this->hasMany(CollateralStatusChangeRequest::class, 'collateral_id', 'id');
    }

    public function alerts()
    {
        return $this->hasMany(CollateralAlert::class, 'collateral_id', 'id');
    }

    public function auditTrail()
    {
        return $this->hasMany(AuditTrail::class, 'user_id', 'created_by_id')
                    ->where('module', 'collateral');
    }
}
