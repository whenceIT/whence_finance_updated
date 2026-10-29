@extends('layouts.master')
@section('content')
<section class="content-header">
    <h1>Payroll and Remuneration</h1>
</section>
<section class="content">
    @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
    <p><a class="btn btn-primary" href="{{url('workstream2/payroll/runs/create')}}">Create Payroll Run</a> <a class="btn btn-default" href="{{url('workstream2/payroll/employees')}}">Employees</a> <a class="btn btn-default" href="{{url('workstream2/payroll/approvals')}}">Approvals</a> <a class="btn btn-default" href="{{url('workstream2/payroll/dashboard')}}">Dashboard</a></p>
    <div class="box">
        <div class="box-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Payroll #</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Gross</th>
                        <th>Deductions</th>
                        <th>Net</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)<tr>
                        <td>{{$run->payroll_number}}</td>
                        <td>{{$run->period_start}} – {{$run->period_end}}</td>
                        <td>{{ucfirst(str_replace('_',' ',$run->status))}}</td>
                        <td>{{number_format($run->total_gross,2)}}</td>
                        <td>{{number_format($run->total_deductions,2)}}</td>
                        <td>{{number_format($run->total_net,2)}}</td>
                        <td><a href="{{url('workstream2/payroll/runs/'.$run->id)}}">View</a></td>
                    </tr>@empty<tr>
                        <td colspan="7">No payroll runs yet.</td>
                    </tr>@endforelse
                </tbody>
            </table>{{$runs->links()}}
        </div>
    </div>
</section>
@endsection