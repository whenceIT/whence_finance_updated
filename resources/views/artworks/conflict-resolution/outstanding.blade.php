@extends('layouts.master')
@section('title') Outstanding @endsection
@section('content')
<section class="content">
<div class="box"><div class="box-body">
<table class="table table-bordered">
<tr><th>Ref</th><th>Client</th><th>Branch</th><th>Charge</th><th>Paid</th><th>Outstanding</th></tr>
@foreach($cases as $c)
<tr><td>{{ $c->case_reference }}</td><td>{{ $c->client_name }}</td><td>{{ optional(\App\Models\Office::find($c->branch_id))->name }}</td><td>{{ number_format($c->charge_amount,2) }}</td><td>{{ number_format($c->amount_paid,2) }}</td><td>{{ number_format(max(0,$c->charge_amount-$c->amount_paid),2) }}</td></tr>
@endforeach
</table>
{{ $cases->links() }}
</div></div>
</section>
@endsection


