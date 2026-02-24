<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_url',
        'file_name',
        'file_size',
        'file_type',
        'category_id',
        'access_level',
        'created_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Access level constants
     */
    const ACCESS_ALL = 'all';
    const ACCESS_MANAGERIAL = 'managerial';

    /**
     * Get available access levels
     */
    public static function getAccessLevels()
    {
        return [
            self::ACCESS_ALL => 'All Staff',
            self::ACCESS_MANAGERIAL => 'Managerial Only',
        ];
    }

    /**
     * Get the URL to access the policy file
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return $this->attributes['file_url'] ?? Storage::disk('spaces')->url($this->file_path);
    }

    /**
     * Get the user policy responses for the policy.
     */
    public function userPolicyResponses()
    {
        return $this->hasMany(UserPolicyResponse::class);
    }

    /**
     * Get the category that the policy belongs to.
     */
    public function category()
    {
        return $this->belongsTo(PolicyCategory::class, 'category_id');
    }

    /**
     * Check if policy is accessible by a specific user role.
     * 
     * @param mixed $userRole
     * @return bool
     */
    public function isAccessibleBy($userRole)
    {
        if ($this->access_level === self::ACCESS_ALL) {
            return true;
        }

        // Check if user has managerial role
        $managerialRoles = ['manager', 'admin', 'supervisor', 'director', 'ceo'];
        return in_array(strtolower($userRole), $managerialRoles);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by access level.
     */
    public function scopeByAccessLevel($query, $accessLevel)
    {
        return $query->where('access_level', $accessLevel);
    }

    /**
     * Get the user that created the policy.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
