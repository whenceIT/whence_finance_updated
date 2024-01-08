<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSchedulerRunHistory extends Model
{
    protected $table = "report_scheduler_run_history";

    public function savings_charges()
    {
        return $this->hasMany(SavingsCharge::class, 'charge_id', 'id');
    }
}
