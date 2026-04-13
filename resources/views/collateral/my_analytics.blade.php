@extends('layouts.master')
@section('title', 'My Collateral Analytics')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">My Collateral Analytics</h3>
        </div>
        <div class="box-body">
            <form method="get" action="{{ route('collateral.my') }}" class="form-inline" style="margin-bottom: 15px;">
                <div class="form-group" style="margin-right: 10px;">
                    <select name="collateral_type_id" class="form-control input-sm">
                        <option value="">All Types</option>
                        @foreach($collateralTypes as $type)
                            <option value="{{ $type->id }}"{{ request('collateral_type_id') == $type->id ? ' selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <select name="loan_status" class="form-control input-sm">
                        <option value="">All Loan Statuses</option>
                        @foreach($loanStatuses as $status)
                            <option value="{{ $status }}"{{ request('loan_status') == $status ? ' selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <input type="date" name="date_purchased_from" class="form-control input-sm" value="{{ request('date_purchased_from') }}">
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <input type="date" name="date_purchased_to" class="form-control input-sm" value="{{ request('date_purchased_to') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
            </form>

            <div class="row">
                @foreach(['active','sold','defaulted','repossessed'] as $status)
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon @if($status == 'active') bg-white @elseif($status == 'sold') bg-success @elseif($status == 'defaulted') bg-danger @elseif($status == 'repossessed') bg-warning @else bg-aqua @endif"><i class="fa fa-archive"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ ucfirst($status) }}</span>
                                <span class="info-box-number">{{ number_format(optional($statuses->get($status))->total ?? 0, 2) }}</span>
                                <span class="progress-description">Items: {{ optional($statuses->get($status))->count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Collateral Exposure by Type</h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr><th>Type</th><th>Current Worth</th><th>Count</th></tr>
                        </thead>
                        <tbody>
                        @forelse($typeExposure as $row)
                            <tr>
                                <td>{{ optional($row->type)->name }}</td>
                                <td>{{ number_format($row->total, 2) }}</td>
                                <td>{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">No exposure data available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Time Series (Purchased)</h3></div>
                <div class="box-body">
                    <canvas id="myCollateralChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('myCollateralChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels->toArray()) !!},
                datasets: [{
                    label: 'Collateral current worth',
                    backgroundColor: 'rgba(54,162,235,0.2)',
                    borderColor: 'rgba(54,162,235,1)',
                    data: {!! json_encode($chartValues->toArray()) !!},
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection