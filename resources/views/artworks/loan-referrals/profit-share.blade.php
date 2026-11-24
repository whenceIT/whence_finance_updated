@extends('layouts.master')
@section('title') Referrals Profit Share Due @endsection
@section('content')
<section class="content-header"><h1>Referrals Profit Share Due</h1></section>
<section class="content"><div class="box"><div class="box-body">
<style>
    .profit-share-not-disbursed {
        color: #a94442;
        font-weight: bold;
        animation: profit-share-pulse 1.5s infinite;
    }

    @keyframes profit-share-pulse {
        50% { opacity: 0.4; }
    }
</style>

<form method="GET" action="{{ route('loan-referrals.profit-share') }}" style="margin-bottom:15px;">
    <div class="input-group" style="max-width:320px;">
        <input type="text" name="search" class="form-control" placeholder="Search client, NRC, ref or loan..." value="{{ $search ?? '' }}">
        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
    </div>
</form>

<div class="callout callout-info" style="margin-bottom:15px;">
    <h3 style="font-size:24px;"><strong>K{{ number_format($items->getCollection()->sum(function($item){ return (float) ($item->profit_share_due ?? 0); }), 2) }}</strong></h3>
    <small><strong>Total Profit Share Due</strong></small>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Ref</th>
        <th>Loan ID</th>
        <th>Client</th>
        <th>NRC</th>
        <th>Contact</th>
        <th>Branch</th>
        <th>Referral Date</th>
        <th>Loan Status</th>
        <th>Profit Share Due (20%)</th>
        <th>Referral Count</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @if($items->isNotEmpty())
        @foreach($items as $item)
        <tr>
            <td>{{ $item->referral_reference }}</td>
            <td><a href="{{ url('loan/' . $item->loan_id . '/show') }}">{{ $item->loan_id }}</a></td>
            <td>{{ trim($item->first_name . ' ' . $item->last_name) }}</td>
            <td>{{ $item->nrc_number ?: 'N/A' }}</td>
            <td>{{ $item->contact_number ?: 'N/A' }}</td>
            <td>{{ $item->branch_name ?: 'N/A' }}</td>
            <td>{{ $item->referral_date ? \Illuminate\Support\Carbon::parse($item->referral_date)->format('Y-m-d') : 'N/A' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $item->loan_status ?: 'N/A')) }}</td>
            <td>
                @if((float) ($item->profit_share_due ?? 0) <= 0)
                    <span class="profit-share-not-disbursed">Not disbursed</span>
                @else
                    <strong>{{ number_format((float) $item->profit_share_due, 2) }}</strong>
                @endif
            </td>
            <td>{{ $item->loan_referral_count }} / 3</td>
            <td>
                @if((float) ($item->profit_share_due ?? 0) <= 0)
                <span class="profit-share-not-disbursed">Not disbursed</span>
                @else
                <form method="POST" action="{{ route('loan-referrals.received', [$item->referral_id, $item->loan_id]) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Mark this referral profit share as received?')">
                        <i class="fa fa-money"></i> Received
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="11" class="text-center">No referral profit share records found.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $items->links() }}</div>
</div></div></section>
@endsection
