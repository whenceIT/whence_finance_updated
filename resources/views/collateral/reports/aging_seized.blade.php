@extends('layouts.master')
@section('title', $report['label'])
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ $report['label'] }}</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-info btn-sm" id="open-reports-modal"><i class="fa fa-bar-chart"></i> Generate Reports</button>
                <a href="{{ route('collateral.reports.form', $type) }}" class="btn btn-default btn-sm">Adjust Filters</a>
                <a href="{{ route('collateral.reports.index') }}" class="btn btn-default btn-sm">All Reports</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-6 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Assets In Seizure</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['count']) }}</h3>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Estimated Value</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['value'], 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">Aging Buckets (Days In Seized Status)</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Age Bucket</th><th>Count</th><th>Estimated Value</th></tr></thead>
                        <tbody>
                        @foreach($buckets as $label => $bucket)
                            <tr>
                                <td><strong>{{ $label }}</strong></td>
                                <td>{{ number_format($bucket['count']) }}</td>
                                <td>{{ number_format($bucket['value'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">Asset Detail</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Name</th><th>Office</th><th>Type</th><th>Seized At</th><th>Days</th><th>Est. Value</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            @php
                                $days = $item->seized_at ? Carbon\Carbon::parse($item->seized_at)->diffInDays(now()) : 0;
                            @endphp
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ optional(optional($item->loan)->office)->name ?? 'Unknown' }}</td>
                                <td>{{ optional($item->type)->name }}</td>
                                <td>{{ optional($item->seized_at)->format('Y-m-d') }}</td>
                                <td>{{ $days }}</td>
                                <td>{{ number_format($item->vetted_valuation > 0 ? $item->vetted_valuation : $item->current_worth, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No seized assets found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('collateral.reports._report_modal')
@stop
