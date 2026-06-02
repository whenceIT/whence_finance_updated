<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeDebt extends Model
{
    use HasFactory;

    protected $table = 'office_debts';

    protected $fillable = [
        'office_id',
        'deposit_type_id',
        'debt_status',
        'debt_month',
        'debt_year',
        'original_amount',
        'outstanding_amount',
        'notes',
        'is_setup_debt',
    ];

    protected $casts = [
        'original_amount'     => 'integer',
        'outstanding_amount'  => 'integer',
        'debt_month'          => 'integer',
        'debt_year'           => 'integer',
        'is_setup_debt'       => 'string',
    ];

    /**
     * The office this debt record belongs to.
     */
    public function office()
    {
        return $this->belongsTo(Office::class)->where('id', '!=', 67);
    }

    /**
     * The deposit type this debt record is linked to.
     */
    public function depositType()
    {
        return $this->belongsTo(DepositType::class);
    }
}
