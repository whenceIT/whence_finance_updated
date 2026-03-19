<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
