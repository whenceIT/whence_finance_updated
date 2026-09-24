<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DepositMonthExemption extends Model
{
    use HasFactory;

    protected $table = 'deposit_month_exemptions';

    protected $fillable = [
        'office_id',
        'deposit_type_id',
        'no_months_exclude',
        'months',
    ];

    protected $casts = [
        'office_id' => 'integer',
        'deposit_type_id' => 'integer',
        'no_months_exclude' => 'integer',
        'months' => 'array',
    ];

    private const MONTH_NAMES = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    private static function monthValues($month, ?int $year): array
    {
        $now = Carbon::now();
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;

        if (is_numeric($month)) {
            $monthNumber = (int) $month;

            if ($monthNumber < 1 || $monthNumber > 12) {
                throw new \InvalidArgumentException('Month must be between 1 and 12.');
            }

            $monthName = self::MONTH_NAMES[$monthNumber];
        } else {
            $monthName = trim((string) $month);
            $monthName = (string) preg_replace('/\s+\d{4}$/', '', $monthName);
            $monthNumber = array_search(
                strtolower($monthName),
                array_map('strtolower', self::MONTH_NAMES),
                true
            );

            if ($monthNumber === false) {
                throw new \InvalidArgumentException('Month must be a valid month name or number.');
            }
        }

        return [(string) $monthNumber, $monthNumber, $monthName . ' ' . $year];
    }

    private static function applyMonthFilter($query, $month, ?int $year): void
    {
        [$monthString, $monthNumber, $monthYear] = self::monthValues($month, $year);

        $query->where(function ($query) use ($monthString, $monthNumber, $monthYear) {
            $query->whereJsonContains('months', $monthString)
                ->orWhereJsonContains('months', $monthNumber)
                ->orWhereJsonContains('months', $monthYear);
        });
    }

    private static function applyDepositTypeFilter($query, int $depositTypeId): void
    {
        $query->where(function ($query) use ($depositTypeId) {
            $query->where('deposit_type_id', $depositTypeId)
                ->orWhereNull('deposit_type_id');
        });
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function depositType()
    {
        return $this->belongsTo(DepositType::class);
    }

    public static function office_name($id)
    {
        $officeId = self::where('id', $id)->first()->office_id;
     
        return Office::where('id', $officeId)->first()->name ?? '-';
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

    /**
     * Get all offices exempted for a given deposit type, month, and year.
     *
     * @param int $depositTypeId
     * @param string $month Month name (e.g., "January")
     * @param int $year Year (e.g., 2026)
     * @return \Illuminate\Support\Collection
     */
    public static function getExemptedOffices(int $depositTypeId, string $month, int $year)
    {
        $exemptions = self::query();
        self::applyDepositTypeFilter($exemptions, $depositTypeId);
        self::applyMonthFilter($exemptions, $month, $year);

        $exemptions = $exemptions->with('office')->get();

        return $exemptions->pluck('office')->filter()->unique('id');
    }

    public static function isExempted(int $depositTypeId, int $officeId, ?string $month = null, ?int $year = null)
    {
        $exemption = self::query()
            ->where('office_id', $officeId);
        self::applyDepositTypeFilter($exemption, $depositTypeId);
        self::applyMonthFilter($exemption, $month, $year);

        return $exemption->first() !== null;
    }

    public static function getExemptedOfficesCurrentMonth(int $depositTypeId)
    {
        $exemptions = self::query();
        self::applyDepositTypeFilter($exemptions, $depositTypeId);
        self::applyMonthFilter($exemptions, null, null);

        $exemptions = $exemptions->with('office')->get();

        return $exemptions->pluck('office')->filter()->unique('id');
    }
}
