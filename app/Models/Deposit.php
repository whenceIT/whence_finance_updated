<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';
    public $timestamps = false;

    protected $fillable = ['status'];

    public function depositType()
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