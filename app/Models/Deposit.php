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