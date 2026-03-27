<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralView;

class GeneralTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'poster'
    ];

    public function uploads()
    {
        return $this->hasMany(GeneralUpload::class, 'general_topic_id');
    }

    /**
     * Scope to get unviewed topics for a specific user's position
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId The ID of the user
     * @param int $positionId The position ID to filter topics by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnviewedForUserPosition($query, $userId, $positionId)
    {
        // Get all topic IDs the user has viewed
        $viewedTopicIds = GeneralView::where('user_id', $userId)
            ->where('type', 'topic')
            ->pluck('item_id')
            ->toArray();

        // Get topics with uploads that match user's position, but not viewed
        // Use 'id' instead of 'position_id' since we're querying the Position model's primary key
        return $query->whereHas('uploads', function ($uploadQuery) use ($positionId) {
                $uploadQuery->whereHas('positions', function ($positionQuery) use ($positionId) {
                    $positionQuery->where('id', $positionId);
                });
            })
            ->whereNotIn('id', $viewedTopicIds);
    }

    /**
     * Get unviewed topics for the current authenticated user's position
     * This is a convenience method that handles getting the current user and their position
     *
     * @param int|null $limit Maximum number of topics to return (optional)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnviewedForCurrentUser($limit = null)
    {
        // Get current user using Sentinel
        $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
        
        if (!$user) {
            return collect([]);
        }

        $userPositionId = $user->position_id ?? null;
        
        if (!$userPositionId) {
            return collect([]);
        }

        // Get all topic IDs the user has viewed
        $viewedTopicIds = GeneralView::where('user_id', $user->id)
            ->where('type', 'topic')
            ->pluck('item_id')
            ->toArray();

        // Build query for unviewed topics
        // Use 'id' instead of 'position_id' since we're querying the Position model's primary key
        $query = self::whereHas('uploads', function ($uploadQuery) use ($userPositionId) {
                // $uploadQuery->whereHas('positions', function ($positionQuery) use ($userPositionId) {
                //     $positionQuery->where('id', $userPositionId);
                // });
            })
            ->whereNotIn('id', $viewedTopicIds)
            ->select(['id', 'name', 'description']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
