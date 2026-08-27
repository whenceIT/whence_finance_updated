@extends('layouts.master')
@section('title', 'Collateral Reports')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral Analytics &amp; Reporting</h3>
        </div>
        <div class="box-body">
            <p class="text-muted">Select a report to generate. Each report opens a form where you can scope the data by date, branch, district, province, type and category.</p>
            <div class="row">
                @foreach($reports as $key => $report)
                    <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 16px;">
                        <div class="small-box" style="background: #f7f9fc; border: 1px solid #e6e9ef; border-radius: 10px; padding: 18px; height: 100%;">
                            <div style="font-size: 22px; color: #667eea; margin-bottom: 8px;">
                                <i class="fa {{ $report['icon'] }}"></i>
                            </div>
                            <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: #2c3e50;">{{ $report['label'] }}</h4>
                            <p style="font-size: 12px; color: #6b7280; min-height: 48px;">{{ $report['description'] }}</p>
                            <a href="{{ route('collateral.reports.form', $key) }}" class="btn btn-primary btn-sm">Generate</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop
