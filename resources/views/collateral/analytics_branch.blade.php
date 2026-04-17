@extends('layouts.master')
@section('title', 'Collateral Branch Analytics')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Branch Analytics</h3>
        </div>
        <div class="box-body">

            <div class="row">
                @foreach($statusOptions as $status)
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon @if($status == 'active') bg-white @elseif($status == 'sold') bg-success @elseif($status == 'defaulted') bg-danger @elseif($status == 'repossessed') bg-warning @else bg-aqua @endif"><i class="fa fa-archive"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ ucfirst($status) }}</span>
                                <span class="info-box-number">{{ number_format($statusTotals->where('status', $status)->first()->total ?? 0, 2) }}</span>
                                <span class="progress-description">Items: {{ $statusTotals->where('status', $status)->first()->count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Collateral by Condition</h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr><th>Condition</th><th>Current Worth</th><th>Sold Price</th><th>Penalty</th><th>Count</th></tr>
                        </thead>
                        <tbody>
                        @forelse($conditionTotals as $row)
                            <tr>
                                <td>{{ ucfirst($row->condition) }}</td>
                                <td>{{ number_format($row->total, 2) }}</td>
                                <td>{{ number_format($row->total_sold ?? 0, 2) }}</td>
                                <td>{{ number_format($row->total_penalty ?? 0, 2) }}</td>
                                <td>{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No condition data available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Branch Information</h3></div>
                <div class="box-body">
                    <p><strong>Office:</strong> {{ $office->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection