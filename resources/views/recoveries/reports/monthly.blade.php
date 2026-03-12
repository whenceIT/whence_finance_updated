@extends('layouts.master')

@section('title')
    Monthly Recovery Report
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Monthly Recovery Report</h3>
    </div>
    <div class="box-body">
        <p>Total Recovered: K {{ number_format($totalRecovered ?? 0, 2) }}</p>
        <p>Cases Resolved: {{ $resolvedCases ?? 0 }}</p>
    </div>
</div>

@endsection
