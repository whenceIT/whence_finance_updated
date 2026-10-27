@extends('layouts.app')
@section('content')
<section class="content-header"><h1>Payroll Approvals</h1></section><section class="content">
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
<table class="table table-bordered"><thead><tr><th>Payroll</th><th>Period</th><th>Net Total</th><th>Created By</th><th>Decision</th></tr></thead><tbody>
@forelse($runs as $run)<tr><td><a href="{{url('workstream2/payroll/runs/'.$run->id)}}">{{$run->payroll_number}}</a></td><td>{{$run->period_start}} – {{$run->period_end}}</td><td>{{number_format($run->total_net,2)}}</td><td>{{$run->created_by_id}}</td><td><form method="POST" action="{{url('workstream2/payroll/runs/'.$run->id.'/decision')}}">{{csrf_field()}}<input type="text" class="form-control input-sm" name="comments" placeholder="Comments"><button class="btn btn-xs btn-success" name="decision" value="approved">Approve</button> <button class="btn btn-xs btn-danger" name="decision" value="declined">Decline</button></form></td></tr>@empty<tr><td colspan="5">No pending payroll runs.</td></tr>@endforelse
</tbody></table>{{$runs->links()}}</section>@endsection
