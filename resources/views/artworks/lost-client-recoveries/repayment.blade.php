@extends('layouts.master')
@section('title') Lost Client Recoveries Repayment @endsection
@section('content')
<section class="content-header"><h1>Repayment Tracking</h1></section>
<section class="content"><div class="box"><div class="box-body">

<div class="row"><div class="col-md-6">
    <form method="GET" action="{{ route('lost-client-recoveries.repayment') }}" style="display:inline-block;">
        <div class="input-group" style="max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Search client, NRC, reference, loan..." value="{{ $search ?? '' }}">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
        </div>
    </form>
</div><div class="col-md-6" style="text-align:right;">
    <form method="GET" action="{{ route('lost-client-recoveries.repayment') }}" style="display:inline-block;">
        <select name="status" class="form-control" style="width:auto; display:inline-block;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" {{ ($status ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <input type="hidden" name="search" value="{{ $search ?? '' }}">
    </form>
</div></div>

<div class="row"><div class="col-md-12">
    <div class="callout callout-info" style="margin-bottom:15px;">
        <h4><i class="fa fa-info"></i> Summary</h4>
        <p>Total Recoveries: <strong>{{ $recoveries->total() }}</strong> &nbsp;|&nbsp;
        Unpaid: <strong>{{ $recoveries->getCollection()->where('repayment_status', 'Unpaid')->count() }}</strong> &nbsp;|&nbsp;
        Partial: <strong>{{ $recoveries->getCollection()->where('repayment_status', 'Partial')->count() }}</strong> &nbsp;|&nbsp;
        Paid: <strong>{{ $recoveries->getCollection()->where('repayment_status', 'Paid')->count() }}</strong></p>
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
        <th>Repayment Status</th>
        <th>Amount Paid</th>
        <th>Outstanding</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @if($recoveries->isNotEmpty())
        @foreach($recoveries as $r)
            <?php
                $outstanding = round(max(0, (float) ($r->loan_principal ?? 0) - (float) $r->amount_paid), 2);
                $badgeClass = $r->repayment_status === 'Paid' ? 'bg-green' : ($r->repayment_status === 'Partial' ? 'bg-yellow' : 'bg-red');
            ?>
        <tr>
            <td>{{ $r->recovery_reference }}</td>
            <td>{{ $r->client_name }}</td>
            <td>{{ $r->nrc_number }}</td>
            <td>{{ $r->branch_name ?? '-' }}</td>
            <td>{{ $r->recovery_date }}</td>
            <td>{{ $r->loan_account ?? '-' }}</td>
            <td>{{ $r->loan_principal ? number_format((float)$r->loan_principal, 2) : '-' }}</td>
            <td><span class="label {{ $badgeClass }}">{{ $r->repayment_status }}</span></td>
            <td>{{ $r->amount_paid ? number_format((float)$r->amount_paid, 2) : '0.00' }}</td>
            <td>{{ $outstanding ? number_format($outstanding, 2) : '0.00' }}</td>
            <td>
                <a href="{{ route('lost-client-recoveries.repayment') }}" class="btn btn-xs btn-default">View</a>
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="11" class="text-center">No recoveries found.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $recoveries->links() }}</div>

</div></div></section>
@endsection
