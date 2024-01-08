<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportScheduler extends Model
{
    protected $table = "report_scheduler";

    public function savings_charges()
    {
        return $this->hasMany(SavingsCharge::class, 'charge_id', 'id');
    }
}
