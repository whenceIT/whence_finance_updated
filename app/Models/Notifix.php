<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifix extends Model
{
    protected $table = 'notifix';

    protected $fillable = [
        'user_id',
        'positions',
        'note',
        'office_id',
        'district_id',
        'province_id',
        'to_id',
        'unread',
    ];

    protected $casts = [
        'positions' => 'array',
        'note' => 'array',
        'unread' => 'boolean',
    ];

    /**
     * Get the user that owns the notifix record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification count for a specific user.
     *
     * @param int $userId
     * @return int
     */
    public static function getUserNotificationCount($userId)
    {
        return self::where('user_id', $userId)->count();
    }
}