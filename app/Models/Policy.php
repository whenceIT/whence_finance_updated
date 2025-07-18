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
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the URL to access the policy file
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return $this->attributes['file_url'] ?? Storage::disk('spaces')->url($this->file_path);
    }
}
