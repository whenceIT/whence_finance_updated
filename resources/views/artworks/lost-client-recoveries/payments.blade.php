@extends('layouts.master')
@section('title') Lost Client Recoveries Payments @endsection
@section('content')
<section class="content-header"><h1>Payments Received</h1></section>
<section class="content"><div class="box"><div class="box-body">

<div class="row"><div class="col-md-6">
    <form method="GET" action="{{ route('lost-client-recoveries.payments') }}" style="display:inline-block;">
        <div class="input-group" style="max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Search client, NRC, reference, loan..." value="{{ $search ?? '' }}">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
        </div>
    </form>
</div></div>

<div class="row"><div class="col-md-12">
    <div class="callout callout-info" style="margin-bottom:15px;">
        <h3 style="font-size:24px;"><strong>Total Payments Received: K{{ number_format($totalPaid ?? 0, 2) }}</strong></h3>
        <small><strong>{{ $recoveries->total() }}</strong> recoveries with payments</small>
    </div>
</div></div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Ref</th>
        <th>Client</th>
        <th>NRC</th>
        <th>Branch</th>
        <th>Recovery Date</th>
        <th>Loan ID</th>
        <th>Amount Paid</th>
    </tr>
    </thead>
    <tbody>
    @if($recoveries->isNotEmpty())
        @foreach($recoveries as $r)
        <tr>
            <td>{{ $r->recovery_reference }}</td>
            <td>{{ $r->client_name }}</td>
            <td>{{ $r->nrc_number }}</td>
            <td>{{ $r->branch_name ?? '-' }}</td>
            <td>{{ $r->recovery_date }}</td>
            <td>{{ $r->loan_id }}</td>
            <td>{{ number_format((float)$r->amount_paid, 2) }}</td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="7" class="text-center">No payments found.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $recoveries->links() }}</div>

</div></div></section>
@endsection
