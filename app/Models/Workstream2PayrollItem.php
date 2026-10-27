<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workstream2PayrollItem extends Model { protected $table='ws2_payroll_items'; protected $guarded=[]; public function run(){return $this->belongsTo('App\Models\Workstream2PayrollRun','payroll_run_id');} public function payments(){return $this->hasMany('App\Models\Workstream2PayrollPayment','payroll_item_id');} }
