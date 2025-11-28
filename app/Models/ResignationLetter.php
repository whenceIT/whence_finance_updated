<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resignation_date',
        'reason',
        'letter_path',
        'status',
        'manager_id',
        'manager_approved_at',
        'admin_id',
        'admin_approved_at',
        'manager_comment',
        'admin_comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
