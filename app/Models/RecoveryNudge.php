<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryNudge extends Model
{
    protected $fillable = [
        'recovery_case_id', 'sent_by', 'channel', 'phone_number',
        'message', 'status', 'gateway_response',
    ];

    public function recoveryCase(): BelongsTo { return $this->belongsTo(RecoveryCase::class); }
    public function sentBy(): BelongsTo       { return $this->belongsTo(User::class, 'sent_by'); }
}
