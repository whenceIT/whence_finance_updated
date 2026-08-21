@extends('layouts.master')
@section('title', 'Collateral Executive Analytics')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Executive Analytics</h3>
        </div>
        <div class="box-body">
            <form method="get" action="{{ route('collateral.analytics.executive') }}" class="form-inline" style="margin-bottom: 15px;">
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

            <ul class="nav nav-tabs" role="tablist">
                @foreach(['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released'] as $status)
                    <li role="presentation" {{ $loop->first ? 'class="active"' : '' }}>
                        <a href="#tab-{{ $status }}" aria-controls="tab-{{ $status }}" role="tab" data-toggle="tab">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                            <span class="badge">{{ optional($statuses->get($status))->count ?? 0 }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" style="margin-top: 20px;">
                @foreach(['pledged','seizure_pending','seized_inventory','valuation_completed','listed_for_sale','sold','written_off','released'] as $status)
                    <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab-{{ $status }}">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Loan</th>
                                    <th>Type</th>
                                    <th>Current Worth</th>
                                    <th>Sold Price</th>
                                    <th>Condition</th>
                                    <th>Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($statusData[$status] as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ optional($item->loan)->id }}</td>
                                        <td>{{ optional($item->type)->name }}</td>
                                        <td>{{ number_format($item->current_worth, 2) }}</td>
                                        <td>{{ number_format($item->sold_price ?? 0, 2) }}</td>
                                        <td>{{ ucfirst($item->condition) }}</td>
                                        <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center">No collateral found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            {{ $statusData[$status]->links() }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="box box-default" style="margin-top: 20px;">
                <div class="box-header with-border"><h3 class="box-title">Collateral Exposure by Type</h3></div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr><th>Type</th><th>Current Worth</th><th>Sold Price</th><th>Penalty</th><th>Count</th></tr>
                        </thead>
                        <tbody>
                        @forelse($typeExposure as $row)
                            <tr>
                                <td>{{ optional($row->type)->name }}</td>
                                <td>{{ number_format($row->total, 2) }}</td>
                                <td>{{ number_format($row->total_sold ?? 0, 2) }}</td>
                                <td>{{ number_format($row->total_penalty ?? 0, 2) }}</td>
                                <td>{{ $row->count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No exposure data available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center" style="margin-bottom: 20px;">
                <a href="{{ route('collateral.index') }}" class="btn btn-primary">View All Collateral</a>
            </div>

            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Time Series (Purchased)</h3></div>
                <div class="box-body">
                    <canvas id="executiveChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('executiveChart').getContext('2d');
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
