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
                        <p style="margin:0; font-size:12px; color:#6b7280;">Sales With Buyer</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['sold_with_buyer']) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Flagged (Potential COI)</p>
                        <h3 style="margin:4px 0 0; font-size:24px; color:#d9534f;">{{ number_format($summary['flagged']) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Phone Matches</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['phone_matches']) }}</h3>
                    </div>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="small-box" style="background:#fff; border:1px solid #e6e9ef; border-radius:8px; padding:14px;">
                        <p style="margin:0; font-size:12px; color:#6b7280;">Name Matches</p>
                        <h3 style="margin:4px 0 0; font-size:24px;">{{ number_format($summary['name_matches']) }}</h3>
                    </div>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h4 class="box-title" style="font-size: 14px;">Flagged Sales &mdash; Buyer Matched To Employee</h4></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Collateral</th>
                                <th>Buyer Name</th>
                                <th>Buyer Phone</th>
                                <th>Office</th>
                                <th>Sold By</th>
                                <th>Sold At</th>
                                <th>Matched Employee</th>
                                <th>Position</th>
                                <th>Match</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($flags as $row)
                            <tr>
                                <td>{{ $row['collateral_name'] }} <small class="text-muted">(#{{ $row['collateral_id'] }})</small></td>
                                <td>{{ $row['buyer_name'] }}</td>
                                <td>{{ $row['buyer_phone'] }}</td>
                                <td>{{ $row['office'] }}</td>
                                <td>{{ $row['sold_by'] }}</td>
                                <td>{{ $row['sold_at'] }}</td>
                                <td><span class="label label-danger">{{ $row['matched_employee'] }}</span></td>
                                <td>{{ $row['matched_position'] }}</td>
                                <td>
                                    <span class="label {{ $row['match_type'] === 'phone' ? 'label-warning' : 'label-info' }}">{{ strtoupper($row['match_type']) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">No conflicts of interest detected for the selected criteria.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-muted"><small>Phone matches are exact normalised comparisons and carry the highest confidence. Name matches use a case-insensitive contains comparison and should be reviewed manually. Confirm whether the matched employee is a relative or has a legitimate relationship to the buyer before action.</small></p>
        </div>
    </div>

    @include('collateral.reports._report_modal')
@stop
