<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositMonthExemption extends Model
{
    use HasFactory;

    protected $table = 'deposit_month_exemptions';

    protected $fillable = [
        'office_id',
        'deposit_type_id',
        'no_months_exclude',
    ];

    protected $casts = [
        'office_id' => 'integer',
        'deposit_type_id' => 'integer',
        'no_months_exclude' => 'integer',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function depositType()
    {
        return $this->belongsTo(DepositType::class);
    }

    /**
     * Calculate the monthly required deposit amount for an office and deposit type,
     * accounting for exemptions.
     *
     * @param int|null $officeId Office ID (null = apply to all offices)
     * @param \App\Models\DepositType $type Deposit type model
     * @param int $overallPeriodMonths Total months in the period being analyzed
     * @return int Monthly required amount (accounting for exemptions)
     */
    public static function get_months_exempted(?int $officeId, \App\Models\DepositType $type): int
    {
        // If no specific office, use default monthly amount
        if ($officeId === null) {
            return 0;
        }

        // Check if there's an exemption for this office/deposit type combination
        $exemption = self::where('office_id', $officeId)
            ->where('deposit_type_id', $type->id)
            ->first();

            
        if (!$exemption) {
            return 0;
        }

        // If exemption exists, adjust: no_months_exclude reduces effective period
       return $exemption->no_months_exclude ?? 0;

    }
}
