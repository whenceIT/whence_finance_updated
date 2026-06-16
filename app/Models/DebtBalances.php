<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtBalances extends Model
{
    use HasFactory;

    protected $table = 'debt_balances';

    protected $fillable = [
        'office_id',
        'deposit_type_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * Get the office that owns this debt balance
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the deposit type for this debt balance
     */
    public function depositType()
    {
        return $this->belongsTo(DepositType::class);
    }
}
