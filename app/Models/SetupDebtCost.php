<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetupDebtCost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'office_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function transactions()
    {
        return $this->hasMany(SetupDebtTransaction::class, 'setup_debt_cost_id');
    }

    public function totalPaid()
    {
        return $this->transactions()->sum('amount');
    }

    public function balance()
    {
        return $this->amount - $this->totalPaid();
    }
}
