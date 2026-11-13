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
        <h4><i class="fa fa-info"></i> Profit Share Summary</h4>
        <p>
            Recoveries with Loans: <strong>{{ $recoveries->total() }}</strong> &nbsp;|&nbsp;
            Total Interest Paid: <strong>K{{ number_format($recoveries->getCollection()->sum(function($r){ return (float) ($r->total_interest_paid ?? 0); }), 2) }}</strong> &nbsp;|&nbsp;
            Total Profit Share Due (10%): <strong>K{{ number_format($recoveries->getCollection()->sum(function($r){ return (float) ($r->profit_share_due ?? 0); }), 2) }}</strong> &nbsp;|&nbsp;
            Total Paid: <strong>K{{ number_format($recoveries->getCollection()->sum(function($r){ return (float) ($r->amount_paid ?? 0); }), 2) }}</strong> &nbsp;|&nbsp;
            Total Outstanding: <strong>K{{ number_format($recoveries->getCollection()->sum(function($r){ return (float) ($r->profit_outstanding ?? 0); }), 2) }}</strong>
        </p>
        <p class="text-muted"><small>Profit share is calculated as 10% of total interest paid on the linked loan.</small></p>
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
        <th>Loan Account</th>
        <th>Loan Principal</th>
        <th>Total Interest Paid</th>
        <th>Profit Share Due (10%)</th>
        <th>Amount Paid</th>
        <th>Outstanding</th>
        <th>Payable</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @if($recoveries->isNotEmpty())
        @foreach($recoveries as $r)
            <?php
                $payable = (float) ($r->profit_outstanding ?? 0) > 0 && $r->repayment_status === 'Paid';
                $payableClass = $payable ? 'bg-green' : 'bg-red';
            ?>
        <tr>
            <td>{{ $r->recovery_reference }}</td>
            <td>{{ $r->client_name }}</td>
            <td>{{ $r->nrc_number }}</td>
            <td>{{ $r->branch_name ?? '-' }}</td>
            <td>{{ $r->recovery_date }}</td>
            <td>{{ $r->loan_account ?? '-' }}</td>
            <td>{{ $r->loan_principal ? number_format((float)$r->loan_principal, 2) : '-' }}</td>
            <td>{{ number_format((float) ($r->total_interest_paid ?? 0), 2) }}</td>
            <td><strong>{{ number_format((float) ($r->profit_share_due ?? 0), 2) }}</strong></td>
            <td>{{ number_format((float)$r->amount_paid, 2) }}</td>
            <td>{{ number_format((float) ($r->profit_outstanding ?? 0), 2) }}</td>
            <td><span class="label {{ $payableClass }}">{{ $payable ? 'Payable' : 'Not Due' }}</span></td>
            <td>
                @if($payable)
                    <a href="#" class="btn btn-xs btn-success"><i class="fa fa-money"></i> Mark Paid</a>
                @else
                    <a href="#" class="btn btn-xs btn-default" disabled><i class="fa fa-clock-o"></i> Pending</a>
                @endif
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="13" class="text-center">No profit share records found. Link a loan to a recovery to calculate profit share.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $recoveries->links() }}</div>

</div></div></section>
@endsection
