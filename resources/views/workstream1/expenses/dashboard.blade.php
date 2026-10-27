@extends('layouts.master')
@section('title', 'Workstream 1 Expense Dashboard')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title">Workstream 1 — Expense Dashboard</h3>
        <div class="box-tools pull-right"><a href="{{ url('workstream1/expenses') }}" class="btn btn-default btn-sm">Expense Register</a></div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3"><div class="small-box bg-yellow"><div class="inner"><h3>{{ $summary['pending_count'] }}</h3><p>Pending Approval</p></div></div></div>
            <div class="col-md-3"><div class="small-box bg-green"><div class="inner"><h3>{{ $summary['approved_count'] }}</h3><p>Approved</p></div></div></div>
            <div class="col-md-3"><div class="small-box bg-red"><div class="inner"><h3>{{ $summary['declined_count'] }}</h3><p>Declined</p></div></div></div>
            <div class="col-md-3"><div class="small-box bg-blue"><div class="inner"><h3>{{ number_format((float)$summary['month_total'], 2) }}</h3><p>Pending + Approved this month (ZMW)</p></div></div></div>
        </div>
        @if(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::hasAccess('workstream1.expenses.approve'))
            <p><a href="{{ url('workstream1/expenses/approvals') }}" class="btn btn-warning">Review Pending Approvals</a></p>
        @endif
    </div>
</div>
@endsection
