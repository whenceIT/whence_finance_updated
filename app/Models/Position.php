<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'job_positions';

    protected $fillable = [
        'name',
    ];

    public function uploads()
    {
        return $this->belongsToMany(GeneralUpload::class, 'general_upload_position')
                    ->withPivot(['created_at', 'updated_at'])
                    ->withTimestamps();
    }
}