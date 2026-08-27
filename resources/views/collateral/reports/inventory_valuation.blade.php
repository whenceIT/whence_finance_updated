@extends('layouts.master')
@section('title', $report['label'])
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ $report['label'] }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('collateral.reports.form', $type) }}" class="btn btn-default btn-sm">Adjust Filters</a>
                <a href="{{ route('collateral.reports.index') }}" class="btn btn-default btn-sm">All Reports</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Total Assets</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['count']) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Estimated Value</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['estimated_value'], 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Current Worth</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['current_worth'], 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Approved Value</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['approved_value'], 2) }}</h3>
                    </div>
                </div>
            </div>

            @foreach([
                'byProvince' => 'By Province',
                'byDistrict' => 'By District',
                'byOffice'   => 'By Office / Branch',
                'byType'     => 'By Asset Type',
                'byCategory' => 'By Category',
                'byAge'      => 'By Age In Inventory',
            ] as $var => $title)
                <div class="box box-default" style="margin-top: 16px;">
                    <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">{{ $title }}</h4></div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>{{ $title }}</th><th>Count</th><th>Estimated Value</th></tr></thead>
                            <tbody>
                            @forelse($$var as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td>{{ number_format($row['count']) }}</td>
                                    <td>{{ number_format($row['value'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No data.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
