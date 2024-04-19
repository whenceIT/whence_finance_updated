<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
{
    protected $table = 'general_ledger';

    protected $fillable = ['cycle_end_date', 'opening_balance', 'total_income', 'closing_balance',];

    //sh

}