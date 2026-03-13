<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoverySpecialistTarget extends Model
{
    protected $fillable = [
        'specialist_id','category','year','month','target_amount','target_cases',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];

    public function specialist(): BelongsTo { return $this->belongsTo(User::class, 'specialist_id'); }

    public static function forCurrentMonth(int $specialistId, string $category): ?static
    {
        return static::where('specialist_id', $specialistId)
            ->where('category', $category)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();
    }
}
