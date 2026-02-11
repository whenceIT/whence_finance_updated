<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'material_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'duration',
        'department',
        'category',
        'target_role',
        'created_by',
        'is_active',
        'is_featured',
        'view_count',
        'download_count',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active materials.
     *
     * @param \Illuminate\Database\Eloquent.Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured materials.
     *
     * @param \Illuminate\Database\Eloquent.Builder $query
     * @return \Illuminate\Database\Eloquent.Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by department.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter by material type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    /**
     * Scope a query to filter by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('categories', function ($q) use ($category) {
            $q->where('name', 'like', '%' . $category . '%');
        });
    }

    /**
     * Scope a query to filter by category ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategoryId($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('course_category_id', $categoryId);
        });
    }

    /**
     * Scope a query to filter by target role.
     *
     * @param \Illuminate\Database\Eloquent.Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target_role', 'all')
              ->orWhere('target_role', $role);
        });
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

    /**
     * Increment download count.
     *
     * @return bool
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        return true;
    }

    /**
     * Get file size in human readable format.
     *
     * @return string
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        if ($bytes == 0) {
            return '0 B';
        }
        
        $bytes = (int) $bytes;
        $exp = floor(log($bytes, 1024));
        
        for ($i = 0; $i < count($units); $i++) {
            if ($bytes >= pow(1024, $i + 1)) {
                $bytes /= pow(1024, $i + 1);
                continue;
            }
        }
        
        return round($bytes, 2) . ' ' . $units[$exp];
    }

    /**
     * Get duration in human readable format.
     *
     * @return string
     */
    public function getHumanDurationAttribute()
    {
        if (!$this->duration) {
            return 'N/A';
        }
        
        $seconds = $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }
        
        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * Get icon based on material type.
     *
     * @return string
     */
    public function getIconAttribute()
    {
        switch ($this->material_type) {
            case 'document':
                return 'fa-file-pdf-o';
            case 'audio':
                return 'fa-headphones';
            case 'video':
                return 'fa-video-camera';
            default:
                return 'fa-file-o';
        }
    }

    /**
     * Get color based on material type.
     *
     * @return string
     */
    public function getTypeColorAttribute()
    {
        switch ($this->material_type) {
            case 'document':
                return '#4a90e2';
            case 'audio':
                return '#50c878';
            case 'video':
                return '#ff6b6b';
            default:
                return '#7f8c8d';
        }
    }

    /**
     * Get enrollments for this training material.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'training_material_id');
    }

    /**
     * Check if a user is enrolled in this training material.
     *
     * @param int $userId
     * @return bool
     */
    public function isEnrolled($userId)
    {
        return $this->enrollments()->where('user_id', $userId)->exists();
    }

    /**
     * Get topics for this training material.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(CourseTopic::class)->active()->ordered();
    }

    /**
     * Get all topics (including inactive) for this training material.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allTopics()
    {
        return $this->hasMany(CourseTopic::class)->ordered();
    }

    /**
     * Get the categories that belong to this training material.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(CourseCategory::class, 'course_category_training_material');
    }

    /**
     * Get the category name (for backward compatibility).
     * Returns the first category's name if multiple exist.
     *
     * @return string
     */
    public function getCategoryNameAttribute()
    {
        return $this->categories->first() ? $this->categories->first()->name : null;
    }
}
