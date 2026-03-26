<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralView extends Model
{
    protected $fillable = [
        'type',
        'user_id',
        'item_id'
    ];

    /**
     * Get the user who viewed the content.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Record a view for a specific item.
     *
     * @param string $type The type of content (upload, topic, course, etc.)
     * @param int $itemId The ID of the content
     * @param int|null $userId The ID of the user who viewed. If null, uses current user.
     * @return bool Whether the view was recorded (true) or already exists (false)
     */
    public static function recordView($type, $itemId, $userId = null)
    {
        if ($userId === null) {
            $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
            if (!$user) {
                return false;
            }
            $userId = $user->id;
        }

        // Check if already viewed by this user
        $exists = self::where('type', $type)
            ->where('item_id', $itemId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return false; // Already viewed
        }

        // Record the view
        self::create([
            'type' => $type,
            'user_id' => $userId,
            'item_id' => $itemId
        ]);

        return true; // New view recorded
    }

    /**
     * Get unique view count for a specific item.
     *
     * @param string $type The type of content
     * @param int $itemId The ID of the content
     * @return int Number of unique views
     */
    public static function getViewCount($type, $itemId)
    {
        return self::where('type', $type)
            ->where('item_id', $itemId)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get all viewers for a specific item.
     *
     * @param string $type The type of content
     * @param int $itemId The ID of the content
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getViewers($type, $itemId)
    {
        return self::with('user')
            ->where('type', $type)
            ->where('item_id', $itemId)
            ->get();
    }
}
