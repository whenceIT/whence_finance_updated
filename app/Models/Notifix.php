<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifix extends Model
{
    protected $table = 'notifix';

    protected $fillable = [
        'user_id',
        'positions',
        'note',
    ];

    protected $casts = [
        'positions' => 'array',
        'note' => 'array',
    ];

    /**
     * Get the user that owns the notifix record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}