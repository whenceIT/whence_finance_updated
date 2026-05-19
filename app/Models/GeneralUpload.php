<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralUpload extends Model
{
    protected $table = 'general_uploads';
    
    protected $fillable = [
        'name',
        'path',
        'type',
        'file_size',
        'mime_type',
        'category',
        'uploaded_by',
        'poster',
        'general_topic_id',
        'views_count',
        'likes_count'
    ];
    
    /**
     * Get all the positions associated with the general upload.
     */
    public function positions()
    {
        return $this->belongsToMany(\App\Models\Position::class, 'general_upload_position')
                    ->withTimestamps();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    public function generalTopic()
    {
        return $this->belongsTo(GeneralTopic::class);
    }

    public function likes()
    {
        return $this->hasMany(GeneralUploadLike::class);
    }

    public function scopeUserViews($query, $userId)
    {
        return $query->whereHas('likes', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    
    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size ?? 0;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
    
    /**
     * Get type color for display
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'video' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'audio' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'book' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'paper' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'document' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'image' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
            'other' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
        ];
        
        return $colors[$this->type] ?? $colors['other'];
    }
    
    /**
     * Get icon for type
     */
    public function getIconAttribute()
    {
        $icons = [
            'video' => 'fa-video-camera',
            'audio' => 'fa-headphones',
            'book' => 'fa-book',
            'paper' => 'fa-file-text',
            'document' => 'fa-file-word-o',
            'image' => 'fa-image',
            'other' => 'fa-file'
        ];
        
        return $icons[$this->type] ?? $icons['other'];
    }
    
    public function getTypeLabelAttribute()
    {
        $labels = [
            'video' => 'Video',
            'audio' => 'Audio',
            'book' => 'Book',
            'paper' => 'Paper',
            'document' => 'Document',
            'image' => 'Image',
            'other' => 'Other'
        ];
        
        return $labels[$this->type] ?? 'Other';
    }
    
    public function getTypeIconAttribute()
    {
        $icons = [
            'video' => 'fa-video-camera',
            'audio' => 'fa-headphones',
            'book' => 'fa-file-text',
            'paper' => 'fa-file-text',
            'document' => 'fa-file-word',
            'image' => 'fa-image',
            'other' => 'fa-file'
        ];
        
        return $icons[$this->type] ?? 'fa-file';
    }
    
    /**
     * Get poster URL for video thumbnail
     */
    public function getPosterUrlAttribute()
    {
        if ($this->poster) {
            return $this->poster;
        }
        return null;
    }



    /**
     * Get assigned trainings for the current authenticated user's position
     * This is a convenience method that handles getting the current user and their position
     *
     * @param int|null $limit Maximum number of topics to return (optional)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function pushlearningContent($limit = null)
    {
        $mode = \App\Models\PlatformSetting::where('key', 'content_push_mode')->first();
        $pushMode = $mode ? $mode->value : 'manual';

        if ($pushMode == 'automatic') {
            // Fetch random General Uploads
            $data = self::getRandomUploads($limit);
        } else {
            $data = self::getManualAssignedTrainings($limit);
        }

        return $data;
    }

    public static function getRandomUploads($limit = null)
    {
        $query = GeneralUpload::inRandomOrder()
            ->select(['id', 'name', 'type', 'path', 'file_size', 'mime_type', 'uploaded_by', 'poster', 'general_topic_id', 'views_count', 'likes_count']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public static function getManualAssignedTrainings($limit = null)
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

        // Get all upload IDs the user has viewed
        $viewedUploadIds = GeneralView::where('user_id', $user->id)
            ->where('type', 'upload')
            ->pluck('item_id')
            ->toArray();

        // Build query for uploads assigned to user's position
        $query = GeneralUpload::whereRaw('EXISTS (
                SELECT 1 FROM general_upload_position pup
                INNER JOIN job_positions jp ON jp.id = pup.position_id
                WHERE pup.general_upload_id = general_uploads.id AND jp.id = ?
            )', [$userPositionId])
            ->with('positions')
            ->whereNotIn('id', $viewedUploadIds)
            ->select(['id', 'name', 'type', 'path', 'file_size', 'mime_type', 'uploaded_by', 'poster', 'general_topic_id', 'views_count', 'likes_count']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
