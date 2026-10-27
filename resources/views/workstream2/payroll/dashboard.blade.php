@extends('layouts.app')
@section('content')
<section class="content-header"><h1>Payroll Dashboard</h1></section><section class="content">
<div class="row"><div class="col-md-4"><div class="small-box bg-aqua"><div class="inner"><h3>{{$employees}}</h3><p>Active employees in scope</p></div></div></div><div class="col-md-4"><div class="small-box bg-yellow"><div class="inner"><h3>{{$pending}}</h3><p>Runs awaiting approval</p></div></div></div></div>
<p><a class="btn btn-primary" href="{{url('workstream2/payroll/runs/create')}}">Create Payroll Run</a> <a class="btn btn-default" href="{{url('workstream2/payroll')}}">View Runs</a></p>
<table class="table table-bordered"><thead><tr><th>Payroll</th><th>Period</th><th>Status</th><th>Net</th></tr></thead><tbody>@forelse($runs as $run)<tr><td><a href="{{url('workstream2/payroll/runs/'.$run->id)}}">{{$run->payroll_number}}</a></td><td>{{$run->period_start}} – {{$run->period_end}}</td><td>{{$run->status}}</td><td>{{number_format($run->total_net,2)}}</td></tr>@empty<tr><td colspan="4">No payroll runs.</td></tr>@endforelse</tbody></table>
</section>@endsection
