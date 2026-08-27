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
            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">By Asset Category (sorted easiest to hardest to sell)</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Category</th><th>Total Assets</th><th>Sold</th><th>Sell-Through Rate</th><th>Avg Days To Sell</th></tr></thead>
                        <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row['category'] }}</td>
                                <td>{{ number_format($row['total']) }}</td>
                                <td>{{ number_format($row['sold']) }}</td>
                                <td>
                                    <span class="label {{ $row['sell_through'] >= 50 ? 'label-success' : 'label-warning' }}">{{ $row['sell_through'] }}%</span>
                                </td>
                                <td>{{ $row['avg_days'] !== null ? $row['avg_days'] . ' days' : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-muted"><small>Lower average days to sell and higher sell-through rate indicate assets that are easier to liquidate.</small></p>
        </div>
    </div>

    @include('collateral.reports._report_modal')
@stop
