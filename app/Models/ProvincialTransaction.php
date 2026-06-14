<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvincialTransaction extends Model
{
    protected $table = 'provincial_transactions';
    
    protected $fillable = [
        'title',
        'description',
        'amount',
        'type',
        'province_id',
        'transaction_date',
        'reference_number',
        'created_by',
        'payment_method',
        'file_path',
        'recorded_at',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'recorded_at' => 'datetime',
    ];
    
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}