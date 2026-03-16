<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryGuarantor extends Model
{
    protected $fillable = [
        'recovery_case_id', 'name', 'phone', 'alt_phone',
        'id_number', 'address', 'employer', 'relationship_to_client',
        'contact_attempts', 'last_contacted_at', 'last_outcome', 'notes',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'contact_attempts'  => 'integer',
    ];

    public function recoveryCase(): BelongsTo
    {
        return $this->belongsTo(RecoveryCase::class);
    }
}

// ─────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkipTraceLead extends Model
{
    protected $fillable = [
        'recovery_case_id', 'created_by', 'lead_type',
        'description', 'new_phone', 'new_address', 'source',
        'verified', 'led_to_location', 'cost_incurred', 'notes',
    ];

    protected $casts = [
        'verified'         => 'boolean',
        'led_to_location'  => 'boolean',
        'cost_incurred'    => 'decimal:2',
    ];

    public function recoveryCase(): BelongsTo
    {
        return $this->belongsTo(RecoveryCase::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

// ─────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalFiling extends Model
{
    protected $fillable = [
        'recovery_case_id', 'created_by', 'filing_type',
        'reference_number', 'law_firm', 'filed_date',
        'hearing_date', 'court_name', 'outcome',
        'cost', 'amount_recovered', 'notes',
    ];

    protected $casts = [
        'filed_date'       => 'date',
        'hearing_date'     => 'date',
        'cost'             => 'decimal:2',
        'amount_recovered' => 'decimal:2',
    ];

    public function recoveryCase(): BelongsTo
    {
        return $this->belongsTo(RecoveryCase::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

// ─────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name', 'code', 'province', 'town',
        'manager_name', 'phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function originCases(): HasMany
    {
        return $this->hasMany(RecoveryCase::class, 'origin_branch_id');
    }

    public function supportingCases(): HasMany
    {
        return $this->hasMany(RecoveryCase::class, 'supporting_branch_id');
    }
}
