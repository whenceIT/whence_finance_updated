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

    /**
     * Return an array of office IDs that have an outstanding setup-debt balance.
     *
     * A balance exists when:
     *   setup_debt_costs.amount  >  SUM(setup_debt_transactions.amount)
     *
     * Offices with no transactions at all are included (full amount still owed).
     * Uses a single aggregating query — no N+1.
     *
     * @return int[]
     */
    public static function officesWithBalance(): array
    {
        return static::query()
            ->select('office_id')
            ->selectRaw(
                'amount - COALESCE((
                    SELECT SUM(t.amount)
                    FROM setup_debt_transactions AS t
                    WHERE t.setup_debt_cost_id = setup_debt_costs.id
                      AND t.deleted_at IS NULL
                ), 0) AS remaining_balance'
            )
            ->having('remaining_balance', '>', 0)
            ->pluck('office_id')
            ->unique()
            ->values()
            ->map(function ($id) { return (int) $id; })
            ->all();
    }
}
