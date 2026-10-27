<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workstream2EmployeeComponent extends Model { protected $table='ws2_employee_components'; protected $guarded=[]; public function component(){return $this->belongsTo('App\Models\Workstream2PayrollComponent','component_id');} }
