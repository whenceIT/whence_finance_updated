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
        'uploaded_by',
        'poster'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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
}
