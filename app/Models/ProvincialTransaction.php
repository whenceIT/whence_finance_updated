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
        'office_id',
        'transaction_date',
        'reference_number',
        'created_by',
        'payment_method',
        'contribution',
        'file_path',
        'recorded_at',
        'status',
        'approved_by',
        'approved_at',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}