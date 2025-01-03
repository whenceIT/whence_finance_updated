<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerIncome extends Model
{
    protected $table = 'admin_income';

    protected $fillable = ['id', 'amount', 'date', 'from'];


    public function income()
    {
        return $this->belongsTo(LedgerIncome::class);
    }
}

