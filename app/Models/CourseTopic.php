<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_material_id',
        'topic_name',
        'topic_type',
        'file_path',
        'video_file_path',
        'audio_file_path',
        'pdf_file_path',
        'ppt_file_path',
        'document_file_path',
        'file_name',
        'duration',
        'sort_order',
        'is_active',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the training material that owns the topic.
     */
    public function trainingMaterial()
    {
        return $this->belongsTo(TrainingMaterial::class);
    }

    /**
     * Get the quiz for this topic.
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class, 'course_topic_id');
    }

    /**
     * Get the icon based on topic type.
     */
    public function getIconAttribute()
    {
        switch ($this->topic_type) {
            case 'video':
                return 'fa-play-circle';
            case 'pdf':
                return 'fa-file-pdf-o';
            case 'ppt':
                return 'fa-file-powerpoint-o';
            case 'document':
                return 'fa-file-word-o';
            default:
                return 'fa-file-o';
        }
    }

    /**
     * Scope for active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Increment view count.
     *
     * @return bool
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
        return true;
    }
}
