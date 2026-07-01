<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetupDebtTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'setup_debt_cost_id',
        'office_id',
        'amount',
        'transaction_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function setupDebtCost()
    {
        return $this->belongsTo(SetupDebtCost::class, 'setup_debt_cost_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
