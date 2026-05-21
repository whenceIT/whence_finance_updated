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
        'debt_status',
        'original_amount',
        'outstanding_amount',
        'notes',
    ];

    protected $casts = [
        'original_amount'     => 'integer',
        'outstanding_amount'  => 'integer',
    ];

    /**
     * The office this debt record belongs to.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
