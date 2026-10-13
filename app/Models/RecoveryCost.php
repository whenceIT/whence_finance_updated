<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecoveryCost extends Model
{
    use SoftDeletes;

    protected $table = 'recovery_costs';

    protected $fillable = [
        'name',
        'description',
        'amount',
        'case_id',
        'office_id',
        'recorded_by',
        'cost_date',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'cost_date' => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function recoveryCase()
    {
        return $this->belongsTo(RecoveryCase::class, 'case_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
