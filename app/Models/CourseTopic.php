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
        'duration',
        'sort_order',
        'is_active',
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
}
