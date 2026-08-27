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
            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">Loan Consultants / Managers Ranked By Seizure Rate</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Total Collateral</th>
                                <th>Seized</th>
                                <th>Seizure Rate</th>
                                <th>Valuation Shortfall</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($byUser as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['position'] }}</td>
                                <td>{{ $row['office'] }}</td>
                                <td>{{ number_format($row['total']) }}</td>
                                <td>{{ number_format($row['seized']) }}</td>
                                <td>
                                    <span class="label {{ $row['seizure_rate'] >= 50 ? 'label-danger' : ($row['seizure_rate'] >= 25 ? 'label-warning' : 'label-success') }}">{{ $row['seizure_rate'] }}%</span>
                                </td>
                                <td>{{ number_format($row['shortfall'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-muted"><small>Valuation Shortfall shows the total amount by which actual vetted valuations fell short of the indicated (approved/current) loan collateral value &mdash; useful for targeting retraining.</small></p>
        </div>
    </div>
@stop
