<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workstream2SalaryProfile extends Model { protected $table='ws2_salary_profiles'; protected $guarded=[]; public function employee(){return $this->belongsTo('App\Models\Workstream2PayrollEmployee','employee_id');} }
