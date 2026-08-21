@extends('layouts.master')
@section('title', 'Collateral Branch Analytics')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Branch Analytics</h3>
        </div>
        <div class="box-body">

            <ul class="nav nav-tabs" role="tablist">
                @foreach($statusOptions as $status)
                    <li role="presentation" {{ $loop->first ? 'class="active"' : '' }}>
                        <a href="#tab-{{ $status }}" aria-controls="tab-{{ $status }}" role="tab" data-toggle="tab">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                            <span class="badge">{{ $statusTotals->where('status', $status)->first()->count ?? 0 }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" style="margin-top: 20px;">
                @foreach($statusOptions as $status)
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
