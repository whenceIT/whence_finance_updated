<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the policies for the category.
     */
    public function policies()
    {
        return $this->hasMany(Policy::class, 'category_id');
    }

    /**
     * Get active categories ordered by sort_order.
     */
    public static function getActiveCategories()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get category by slug.
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }
}
