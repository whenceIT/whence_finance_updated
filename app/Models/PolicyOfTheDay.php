<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyOfTheDay extends Model
{
    protected $table = 'policy_of_the_day';
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'full_content',
        'policy_id',
        'created_by',
        'scheduled_date',
        'is_active',
        'is_random',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_active' => 'boolean',
        'is_random' => 'boolean',
    ];

    /**
     * Get the policy this policy of the day is based on
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'policy_id');
    }

    /**
     * Get the user who created this policy of the day
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active policies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for today's scheduled policies
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    /**
     * Scope for random policies
     */
    public function scopeRandom($query)
    {
        return $query->where('is_random', true);
    }

      /**
       * Get the policy of the day for display
       * Priority: Scheduled for today > Random > Any active
       */
      public static function getTodaysPolicy()
      {
          // First try scheduled for today - limit to 1
          return static::active()->whereRaw("DATE(created_at) = CURDATE()")->orderBy('created_at', 'desc')->limit(1)->first();

          
      }
}
