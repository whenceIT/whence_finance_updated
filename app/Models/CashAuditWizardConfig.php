<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAuditWizardConfig extends Model
{
    protected $table = 'cash_audit_wizard_config';

    protected $fillable = [
        'is_active',
        'target_office_id',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function targetOffice()
    {
        return $this->belongsTo(Office::class, 'target_office_id');
    }

    /**
     * Get the single active config row, or null.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }
}
