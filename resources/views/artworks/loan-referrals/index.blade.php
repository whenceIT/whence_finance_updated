@extends('layouts.master')
@section('title') All Referrals @endsection
@section('content')
<section class="content-header"><h1>All Referrals</h1></section>
<section class="content"><div class="box"><div class="box-body">
<table class="table table-bordered"><tr><th>Ref</th><th>Client</th><th>NRC</th><th>Branch</th><th>Dept</th><th>Date</th><th>Status</th></tr>
@foreach($referrals as $r)
<tr><td>{{ $r->referral_reference }}</td><td>{{ $r->client_name }}</td><td>{{ $r->nrc_number }}</td><td>{{ optional(\App\Models\Office::find($r->branch_id))->name }}</td><td>{{ $r->referring_department }}</td><td>{{ $r->referral_date }}</td><td>{{ $r->loan_status }}</td></tr>
@endforeach
</table>
{{ $referrals->links() }}
</div></div></section>
@endsection
