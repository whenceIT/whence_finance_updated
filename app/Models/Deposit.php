<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';
    public $timestamps = false;

    protected $fillable = ['status', 'deposit_type', 'office', 'amount', 'debt', 'date'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('approved', function ($builder) {
            $builder->where('status', 1);
        });
    }

    public function depositTypeInfo()
    {
        return $this->belongsTo(DepositType::class, 'deposit_type', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bankDepositLog()
    {
        return $this->hasOne(BankDepositLog::class, 'deposit_id', 'id');
    }

    public function getBankDepositLogAmountAttribute()
    {
        return $this->bankDepositLog?->amount;
    }

    public function getBankDepositLogMethodAttribute()
    {
        return $this->bankDepositLog?->deposit_method;
    }

    public function getBankDepositLogReferenceNumberAttribute()
    {
        return $this->bankDepositLog?->reference_number;
    }

    public function getBankDepositLogIdAttribute()
    {
        return $this->bankDepositLog?->id;
    }

    public function getBankDepositLogUserIdAttribute()
    {
        return $this->bankDepositLog?->user_id;
    }

    public function getBankDepositLogUserFirstNameAttribute()
    {
        return $this->bankDepositLog?->user?->first_name;
    }

    public function getBankDepositLogUserLastNameAttribute()
    {
        return $this->bankDepositLog?->user?->last_name;
    }

    public function getBankDepositLogCreatedDateAttribute()
    {
        return $this->bankDepositLog?->created_date;
    }

    public function getDepositTypeNameAttribute()
    {
        return $this->depositTypeInfo?->name;
    }

    public function getMonthlyAmountAttribute()
    {
        return $this->depositTypeInfo?->monthly_amount;
    }

    public function getBankAttribute()
    {
        return $this->depositTypeInfo?->bank;
    }

    public function getGlAccountAttribute()
    {
        return $this->depositTypeInfo?->gl_account;
    }

    public function getOfficeNameAttribute()
    {
        return $this->office?->name;
    }

    public static function depositStatusByMonth($office_id, $month)
    {
        // Validate month format
        if (!preg_match('/^\d{1,2}-\d{4}$/', $month)) {
            throw new \InvalidArgumentException('Month must be in format MM-YYYY');
        }
        
        $parts = explode('-', $month);
        $monthNum = (int) $parts[0];
        $year = (int) $parts[1];
        
        // Validate month range
        if ($monthNum < 1 || $monthNum > 12) {
            throw new \InvalidArgumentException('Month must be between 1 and 12');
        }
        
        $depositTypes = [3, 1, 5];

        // Get all deposit types in one query
        $depositTypeModels = \App\Models\DepositType::whereIn('id', $depositTypes)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id');
        
        // Get office in one query
        $office = \App\Models\Office::find($office_id);
        
        // Get summary data
        $results = self::select([
            'dt.id as deposit_type_id',
            'dt.name as deposit_type_name',
            'dt.monthly_amount as monthly_required',
            \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(d.amount), 0) as total_received')
        ])
        ->from('deposits as d')
        ->leftJoin('deposit_types as dt', 'd.deposit_type', '=', 'dt.id')
        ->where('d.office', $office_id)
        ->whereIn('d.deposit_type', $depositTypes)
        ->whereYear('d.date', $year)
        ->whereMonth('d.date', $monthNum)
        ->groupBy('dt.id', 'dt.name', 'dt.monthly_amount')
        ->orderBy('dt.sort_order')
        ->get()
        ->keyBy('deposit_type_id');
        
        // Build status array
        $status = [];
        foreach ($depositTypes as $typeId) {
            $depositType = $depositTypeModels->get($typeId);
            
            if (!$depositType) {
                continue; // Skip if deposit type doesn't exist
            }
            
            $result = $results->get($typeId);
            
            if ($result) {
                $received = (float) $result->total_received;
                $required = (float) ($result->monthly_required ?? 0);
                
                $depositStatus = 'unpaid';
                if ($received >= $required && $required > 0) {
                    $depositStatus = 'fully paid';
                } elseif ($received > 0 && $received < $required) {
                    $depositStatus = 'partially paid';
                }
                
                $status[] = [
                    'status' => $depositStatus,
                    'office_name' => $office?->name,
                    'office_id' => $office_id,
                    'deposit_type_name' => $result->deposit_type_name,
                    'deposit_type_id' => $result->deposit_type_id,
                    'received' => $received,
                    'required' => $required,
                    'remaining' => max(0, $required - $received),
                ];
            } else {
                // No deposits found for this type
                $status[] = [
                    'status' => 'unpaid',
                    'office_name' => $office?->name,
                    'office_id' => $office_id,
                    'deposit_type_name' => $depositType->name,
                    'deposit_type_id' => $typeId,
                    'received' => 0,
                    'required' => (float) ($depositType->monthly_amount ?? 0),
                    'remaining' => (float) ($depositType->monthly_amount ?? 0),
                ];
            }
        }
        
        return $status;
    }


    public static function otherReceived($types = [4, 6, 2], $officeIds = null, $dateFrom = null, $dateTo = null)
    {
        $query = self::whereIn('deposit_type', $types);
        if ($officeIds !== null) {
            $query->whereIn('office', $officeIds);
        }
        if ($dateFrom !== null && $dateTo !== null) {
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }
        return $query->sum('amount');
    }

    public static function mandatoryReceived($types = [3, 1, 5], $officeIds = null, $dateFrom = null, $dateTo = null)
    {
        $query = self::whereIn('deposit_type', $types);
        if ($officeIds !== null) {
            $query->whereIn('office', $officeIds);
        }
        if ($dateFrom !== null && $dateTo !== null) {
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }
        return $query->sum('amount');
    }
}