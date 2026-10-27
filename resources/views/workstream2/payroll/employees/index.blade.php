@extends('layouts.app')
@section('content')
<section class="content-header"><h1>Payroll Employees</h1></section><section class="content">
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
<p><a class="btn btn-primary" href="{{url('workstream2/payroll/employees/create')}}">Add Employee</a></p>
<table class="table table-bordered"><thead><tr><th>Code</th><th>Name</th><th>Office</th><th>Status</th><th>Basic Salary</th><th>Salary</th></tr></thead><tbody>
@forelse($employees as $e)<tr><td>{{$e->employee_code}}</td><td>{{$e->first_name}} {{$e->last_name}}</td><td>{{optional($e->office)->name ?: $e->office_id}}</td><td>{{$e->status}}</td><td>{{$e->currentSalary ? number_format($e->currentSalary->basic_salary,2).' '.$e->currentSalary->currency : 'Not set'}}</td><td><a href="{{url('workstream2/payroll/employees/'.$e->id.'/salary')}}">Salary</a> | <a href="{{url('workstream2/payroll/employees/'.$e->id.'/components')}}">Components</a></td></tr>@empty<tr><td colspan="6">No employees yet.</td></tr>@endforelse
</tbody></table>{{$employees->links()}}</section>@endsection
