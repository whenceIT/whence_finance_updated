<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralUploadLike extends Model
{
    use HasFactory;

    protected $table = 'general_upload_likes';

    protected $fillable = [
        'user_id',
        'general_upload_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generalUpload()
    {
        return $this->belongsTo(GeneralUpload::class);
    }
}
