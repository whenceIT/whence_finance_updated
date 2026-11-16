@extends('layouts.master')
@section('title') Lost Client Recoveries Profit Share @endsection
@section('content')
<section class="content-header"><h1>Profit Share Due</h1></section>
<section class="content"><div class="box"><div class="box-body">

<div class="row"><div class="col-md-6">
    <form method="GET" action="{{ route('lost-client-recoveries.profit-share') }}" style="display:inline-block;">
        <div class="input-group" style="max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Search client, NRC, reference, loan..." value="{{ $search ?? '' }}">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
        </div>
    </form>
</div></div>

<div class="row"><div class="col-md-12">
    <div class="callout callout-info" style="margin-bottom:15px;">
        <h3 style="font-size:24px;"><strong> K{{ number_format($recoveries->getCollection()->sum(function($r){ return (float) ($r->profit_share_due ?? 0); }), 2) }}</strong></h3>
        <small><strong>Total Profit Share Due</strong></small>
    </div>
</div></div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Loan ID</th>
        <th>Client</th>
        <th>NRC</th>
        <th>Branch</th>
        <th>Recovery Date</th>
        <th>Loan Principal</th>
        <th>Profit Share Due (10%)</th>
        <th>Amount Paid</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @if($recoveries->isNotEmpty())
        @foreach($recoveries as $r)
        <tr>
            <td>{{ $r->loan_id }}</td>
            <td>{{ $r->client_name }}</td>
            <td>{{ $r->nrc_number }}</td>
            <td>{{ $r->branch_name ?? '-' }}</td>
            <td>{{ $r->recovery_date }}</td>
            <td>{{ $r->loan_principal ? number_format((float)$r->loan_principal, 2) : '-' }}</td>
            <td><strong>{{ number_format((float) ($r->profit_share_due ?? 0), 2) }}</strong></td>
            <td>{{ number_format((float)$r->amount_paid, 2) }}</td>
            <td>
                @if((float) $r->amount_paid < (float) ($r->profit_share_due ?? 0))
                    <form method="POST" action="{{ route('lost-client-recoveries.received', $r->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Mark this recovery as received?')"><i class="fa fa-money"></i> Received</button>
                    </form>
                @else
                    <span class="label bg-green">Received</span>
                @endif
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="9" class="text-center">No profit share records found. Link a loan to a recovery to calculate profit share.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $recoveries->links() }}</div>

</div></div></section>
@endsection
