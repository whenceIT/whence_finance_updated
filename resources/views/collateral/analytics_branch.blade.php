@extends('layouts.master')
@section('title', 'Collateral Branch Analytics')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Branch Analytics</h3>
        </div>
        <div class="box-body">
            @if($hasHigherScope)
                <form method="get" action="{{ route('collateral.analytics.branch') }}" class="form-inline" style="margin-bottom: 15px;">
                    <div class="form-group" style="margin-right: 10px;">
                        <select name="office_id" class="form-control input-sm">
                            <option value="">Select Office</option>
                            @foreach($offices ?? [] as $office)
                                <option value="{{ $office->id }}"{{ $officeId == $office->id ? ' selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            @endif

            <div class="row">
                @foreach($statusBreakdown as $status)
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-archive"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ ucfirst($status->status) }}</span>
                                <span class="info-box-number">{{ number_format($status->total, 2) }}</span>
                                <span class="progress-description">Items: {{ $status->count }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Reassessment Needed</h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr><th>Name</th><th>Type</th><th>Initial Price</th><th>Current Worth</th><th>Percentage</th></tr>
                        </thead>
                        <tbody>
                        @forelse($reassessmentList as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ optional($item->type)->name }}</td>
                                <td>{{ number_format($item->initial_price, 2) }}</td>
                                <td>{{ number_format($item->current_worth, 2) }}</td>
                                <td>{{ number_format(($item->current_worth / $item->initial_price) * 100, 2) }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No reassessment needed.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection