@extends('layouts.master')

@section('title')
    Attribution Report
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Attribution Report</h3>
    </div>
    <div class="box-body">
        <p>Recoveries Department: K {{ number_format($deptAmount ?? 0, 2) }}</p>
        <p>Origin Branch: K {{ number_format($originAmount ?? 0, 2) }}</p>
    </div>
</div>

@endsection
