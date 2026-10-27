<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workstream2PayrollEmployee extends Model {
 protected $table='ws2_payroll_employees'; protected $guarded=[];
 public function salaryProfiles(){return $this->hasMany('App\Models\Workstream2SalaryProfile','employee_id');}
 public function currentSalary(){return $this->hasOne('App\Models\Workstream2SalaryProfile','employee_id')->where('is_current',1);}
 public function employeeComponents(){return $this->hasMany('App\Models\Workstream2EmployeeComponent','employee_id')->where('is_active',1);}
 public function office(){return $this->belongsTo('App\Office','office_id');}
}
