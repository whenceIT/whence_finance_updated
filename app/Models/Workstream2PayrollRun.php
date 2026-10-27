<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workstream2PayrollRun extends Model { protected $table='ws2_payroll_runs'; protected $guarded=[]; public function items(){return $this->hasMany('App\Models\Workstream2PayrollItem','payroll_run_id');} public function approvals(){return $this->hasMany('App\Models\Workstream2PayrollApproval','payroll_run_id');} }
