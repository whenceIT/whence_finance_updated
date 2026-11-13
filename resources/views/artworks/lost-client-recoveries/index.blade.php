@extends('layouts.master')
@section('title') All Recoveries @endsection
@section('content')
<section class="content-header"><h1>All Recoveries</h1></section>
<section class="content"><div class="box"><div class="box-body">
<table class="table table-bordered"><tr><th>Ref</th><th>Client</th><th>NRC</th><th>Branch</th><th>Date</th><th>Status</th></tr>
@foreach($recoveries as $r)
<tr><td>{{ $r->recovery_reference }}</td><td>{{ $r->client_name }}</td><td>{{ $r->nrc_number }}</td><td>{{ optional(\App\Models\Office::find($r->branch_id))->name }}</td><td>{{ $r->recovery_date }}</td><td>{{ $r->repayment_status }}</td></tr>
@endforeach
</table>
{{ $recoveries->links() }}
</div></div></section>
@endsection
