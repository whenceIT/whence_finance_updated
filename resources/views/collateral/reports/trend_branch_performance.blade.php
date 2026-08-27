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
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">Branch / Office Performance</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Office / Branch</th>
                                <th>Total Collateral</th>
                                <th>Seizure Volume</th>
                                <th>Sold</th>
                                <th>Liquidation Value</th>
                                <th>Proceeds</th>
                                <th>Variance</th>
                                <th>Branch Gen.</th>
                                <th>Supervisor Gen.</th>
                                <th>Withinhere Gen.</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($byOffice as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ number_format($row['total']) }}</td>
                                <td>{{ number_format($row['seized']) }}</td>
                                <td>{{ number_format($row['sold']) }}</td>
                                <td>{{ number_format($row['liquidation_value'], 2) }}</td>
                                <td>{{ number_format($row['proceeds'], 2) }}</td>
                                <td>{{ number_format($row['variance'], 2) }}</td>
                                <td>{{ number_format($row['branch_generated']) }}</td>
                                <td>{{ number_format($row['supervisor_generated']) }}</td>
                                <td>{{ number_format($row['withinhere']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">No data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-muted"><small>Seizure Volume counts collateral that reached any post-seizure stage. Sales source breakdown shows how the collateral was generated (branch staff, collateral supervisor, or Withinhere).</small></p>
        </div>
    </div>
@stop
