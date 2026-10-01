<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockage extends Model
{
    use HasFactory;

    protected $table = 'blockages';

    protected $fillable = [
        'office_id',
        'reason',
        'time_to_unlock'
    ];

    protected $casts = [
        'time_to_unlock' => 'datetime',
    ];

    /**
     * Auto-update the office blocking history log whenever a new Blockage is created.
     * Increments blocked_count and refreshes the reason + last_blocked_at timestamp.
     */
    protected static function booted(): void
    {
        static::created(function (Blockage $blockage) {
            OfficeBlockingHistory::updateOrCreate(
                ['office_id' => $blockage->office_id],
                [
                    'reason'          => $blockage->reason,
                    'last_blocked_at' => $blockage->created_at ?? now(),
                ]
            );

            // Increment the counter separately so updateOrCreate doesn't reset it to 1.
            OfficeBlockingHistory::where('office_id', $blockage->office_id)
                ->increment('blocked_count');
        });
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function isUnlocked(): bool
    {
        return $this->time_to_unlock && now()->gte($this->time_to_unlock);
    }
}
