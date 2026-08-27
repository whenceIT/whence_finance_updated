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
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Assets Sold</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($items->count()) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Total Book Value</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($totalValue, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Actual Proceeds</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($totalProceeds, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Variance (Gain/Loss)</p>
                        <h3 style="margin:4px 0 0; font-size:24px; color: {{ $variance < 0 ? '#d9534f' : '#5cb85c' }};">{{ number_format($variance, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">By Month</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Month</th><th>Sold</th><th>Book Value</th><th>Proceeds</th><th>Variance</th></tr></thead>
                        <tbody>
                        @forelse($byMonth as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ number_format($row['count']) }}</td>
                                <td>{{ number_format($row['value'], 2) }}</td>
                                <td>{{ number_format($row['proceeds'], 2) }}</td>
                                <td>{{ number_format($row['variance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">By Asset Type</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Type</th><th>Sold</th><th>Book Value</th><th>Proceeds</th><th>Variance</th></tr></thead>
                        <tbody>
                        @forelse($byType as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ number_format($row['count']) }}</td>
                                <td>{{ number_format($row['value'], 2) }}</td>
                                <td>{{ number_format($row['proceeds'], 2) }}</td>
                                <td>{{ number_format($row['variance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('collateral.reports._report_modal')
@stop
