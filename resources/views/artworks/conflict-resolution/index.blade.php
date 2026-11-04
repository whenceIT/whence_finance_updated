@extends('layouts.master')
@section('title') Conflict Resolution Cases @endsection
@section('content')
<section class="content">
<div class="box"><div class="box-body">
<table class="table table-bordered">
<tr><th>Ref</th><th>Client</th><th>Branch</th><th>Type</th><th>Date Reported</th><th>Description</th><th>Status</th><th>Charge</th><th>Paid</th></tr>
@foreach($cases as $c)
<tr><td>{{ $c->case_reference }}</td><td>{{ $c->client_name }}</td><td>{{ optional(\App\Models\Office::find($c->branch_id))->name }}</td><td>{{ $c->complaint_type }}</td><td>{{ $c->date_reported }}</td><td>{{ \Illuminate\Support\Str::limit($c->complaint_description,40) }}</td><td>{{ $c->status }}</td><td>{{ number_format($c->charge_amount,2) }}</td><td>{{ number_format($c->amount_paid,2) }}</td></tr>
@endforeach
</table>
{{ $cases->links() }}
</div></div>
</section>
@endsection



