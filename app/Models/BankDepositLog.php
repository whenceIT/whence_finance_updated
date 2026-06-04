<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositLog extends Model
{
    use HasFactory;

    protected $table = 'bank_deposit_log';
    public $timestamps = false;

    protected $fillable = [
        'deposit_type',
        'office_id',
        'user_id',
        'amount',
        'deposit_method',
        'reference_number',
        'created_date',
    ];

    public function depositType()
    {
        return $this->belongsTo(DepositType::class, 'deposit_type', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}