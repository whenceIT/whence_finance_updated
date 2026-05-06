<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'vehicle_type',
        'vehicle_model',
        'assigned_to',
        'office_id',
        'color',
        'date_purchased',
        'insurance_expire_date',
        'current_value',
        'white_book',
        'vehicle_status',
        'last_maintenance',
    ];

    protected $casts = [
        'office_id' => 'integer',
        'insurance_expire_date' => 'date',
        'date_purchased' => 'date',
        'current_value' => 'decimal:2',
        'white_book' => 'string',
        'last_maintenance' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }
}
