@extends('layouts.master')
@section('title', 'Collateral Approval Queue')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral Approval Queue</h3>
        </div>
        <div class="box-body">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#pending-status-tab" aria-controls="pending-status-tab" role="tab" data-toggle="tab">
                        Pending Collateral Status <span class="badge">{{ $requests->count() }}</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#pending-new-tab" aria-controls="pending-new-tab" role="tab" data-toggle="tab">
                        Pending New Collateral <span class="badge">{{ $newCollaterals->count() }}</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content" style="margin-top: 20px;">
                <!-- Pending Collateral Status Tab -->
                <div role="tabpanel" class="tab-pane active" id="pending-status-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Collateral</th>
                                <th>Loan</th>
                                <th>Requested By</th>
                                <th>Old Status</th>
                                <th>New Pending Status</th>
                                <th>Sold Price</th>
                                <th>Disposal Costs</th>
                                <th>Net Proceeds</th>
                                <th>Reason</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($requests as $request)
                                @php
                                    $soldPrice = $request->sold_price ?? 0;
                                    $disposalTotal = 0;
                                    if ($request->disposal_costs && is_array($request->disposal_costs)) {
                                        foreach ($request->disposal_costs as $cost) {
                                            $disposalTotal += (float) ($cost['amount'] ?? 0);
                                        }
                                    }
                                    $netProceeds = $soldPrice - $disposalTotal;
                                    $loanBalance = 0;
                                    if ($request->collateral && $request->collateral->loan) {
                                        $loanBalance = $loanBalances[$request->collateral->loan->id] ?? 0;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('collateral.show', $request->collateral) }}">
                                            {{ $request->collateral->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($request->collateral && $request->collateral->loan)
                                            <a href="#">
                                                Loan #{{ $request->collateral->loan->id }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $request->requested_by->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($request->old_status) }}</td>
                                    <td>{{ ucfirst($request->new_status) }}</td>
                                    <td>{{ number_format($soldPrice, 2) }}</td>
                                    <td>{{ number_format($disposalTotal, 2) }}</td>
                                    <td>{{ number_format($netProceeds, 2) }}</td>
                                    <td>{{ $request->reason }}</td>
                                    <td>{{ $request->request_date }}</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#sellAnalysisModal{{ $request->id }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <form method="post" action="{{ route('collateral.approvals.approve', $request) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                        </form>
                                        <form method="post" action="{{ route('collateral.approvals.reject', $request) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-xs">Reject</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Sell Analysis Modal -->
                                <div class="modal fade" id="sellAnalysisModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="sellAnalysisModalLabel{{ $request->id }}">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title" id="sellAnalysisModalLabel{{ $request->id }}">Sell Analysis - {{ $request->collateral->name ?? 'Collateral' }}</h4>
                                            </div>
                                            <div class="modal-body">
                                                @php
                                                    $approvedValue = $request->collateral->approved_value ?? $request->collateral->current_worth ?? 0;
                                                @endphp
                                                <div class="cd-panel cd-analysis-panel">
                                                    <div class="cd-panel-header">
                                                        <h3>Sell Analysis</h3>
                                                    </div>
                                                    <div class="cd-panel-body">
                                                        <div class="cd-fieldgrid">
                                                            <div>
                                                                <div class="cd-field">
                                                                    <span class="cd-label">Approved Value</span>
                                                                    <span class="cd-value">{{ number_format($approvedValue, 2) }}</span>
                                                                </div>
                                                                <div class="cd-field">
                                                                    <span class="cd-label">Sold Price</span>
                                                                    <span class="cd-value">{{ number_format($soldPrice, 2) }}</span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="cd-field">
                                                                    <span class="cd-label">Disposal Costs</span>
                                                                    <span class="cd-value">{{ number_format($disposalTotal, 2) }}</span>
                                                                </div>
                                                                <div class="cd-field">
                                                                    <span class="cd-label">Net Proceeds</span>
                                                                    <span class="cd-value">{{ number_format($netProceeds, 2) }}</span>
                                                                </div>
                                                                <div class="cd-field">
                                                                    <span class="cd-label">Loan Balance</span>
                                                                    <span class="cd-value">{{ number_format($loanBalance, 2) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="margin-top: 12px;">
                                                            @if($soldPrice >= $approvedValue)
                                                                <span class="label label-success">Meets Minimum Value</span>
                                                            @else
                                                                @php $shortfall1 = $approvedValue - $soldPrice; @endphp
                                                                <span class="label label-danger">Below Approved Value</span>
                                                                <div class="alert alert-danger" style="margin-top: 10px;">The disposal value is <strong>{{ number_format($soldPrice, 2) }}</strong>. It must be at least <strong>{{ number_format($approvedValue, 2) }}</strong>. You need <strong>{{ number_format($shortfall1, 2) }}</strong> more to proceed.</div>
                                                            @endif

                                                            @if($netProceeds >= $loanBalance)
                                                                <span class="label label-success" style="margin-left: 8px;">Covers Loan Balance</span>
                                                                @if($netProceeds > $loanBalance)
                                                                    @php $excess2 = $netProceeds - $loanBalance; @endphp
                                                                    <div class="alert alert-success" style="margin-top: 10px;">Net proceeds exceed the loan balance by <strong>{{ number_format($excess2, 2) }}</strong>. This amount is owed to the client.</div>
                                                                @endif
                                                            @else
                                                                @php $shortfall2 = $loanBalance - $netProceeds; @endphp
                                                                <span class="label label-danger" style="margin-left: 8px;">Below Loan Balance</span>
                                                                <div class="alert alert-danger" style="margin-top: 10px;">Net proceeds are <strong>{{ number_format($netProceeds, 2) }}</strong>, but the loan balance is <strong>{{ number_format($loanBalance, 2) }}</strong>. You need <strong>{{ number_format($shortfall2, 2) }}</strong> more to cover the loan balance.</div>
                                                            @endif

                                                            @if($request->disposal_costs && is_array($request->disposal_costs) && count($request->disposal_costs) > 0)
                                                                <div style="margin-top: 15px;">
                                                                    <strong>Disposal Costs Breakdown:</strong>
                                                                    <table class="table table-bordered table-striped" style="margin-top: 5px;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Cost Item</th>
                                                                            <th>Amount</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach($request->disposal_costs as $cost)
                                                                            <tr>
                                                                                <td>{{ $cost['name'] ?? 'Unnamed' }}</td>
                                                                                <td>{{ number_format($cost['amount'] ?? 0, 2) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="11" class="text-center">No pending approval requests.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pending New Collateral Tab -->
                <div role="tabpanel" class="tab-pane" id="pending-new-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Client</th>
                                <th>Current Worth</th>
                                <th>Approved Value</th>
                                <th>Stage</th>
                                <th>Condition</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($newCollaterals as $collateral)
                                <tr>
                                    <td>{{ $collateral->name }}</td>
                                    <td>{{ optional($collateral->loan)->id }}</td>
                                    <td>{{ optional($collateral->loan->client)->first_name }} {{ optional($collateral->loan->client)->last_name }}</td>
                                    <td>{{ number_format($collateral->current_worth, 2) }}</td>
                                    <td>{{ number_format($collateral->approved_value ?? $collateral->current_worth, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $collateral->stage)) }}</td>
                                    <td>{{ ucfirst($collateral->condition) }}</td>
                                    <td>{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</td>
                                    <td>{{ $collateral->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('collateral.show', $collateral) }}" class="btn btn-xs btn-primary">View</a>
                                        <form method="post" action="{{ route('collateral.approvals.new.approve', $collateral) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                        </form>
                                        <form method="post" action="{{ route('collateral.approvals.new.decline', $collateral) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-xs">Decline</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center">No pending new collateral approvals.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cd-analysis-panel {
            background: #fff;
            border: 1px solid #e6e9ef;
            border-radius: 6px;
            overflow: hidden;
        }
        .cd-analysis-panel .cd-panel-header {
            padding: 14px 20px;
            border-bottom: 1px solid #eef0f4;
        }
        .cd-analysis-panel .cd-panel-header h3 {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .cd-analysis-panel .cd-panel-body {
            padding: 20px;
        }
        .cd-fieldgrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .cd-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f2f5;
        }
        .cd-field:last-child {
            border-bottom: none;
        }
        .cd-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        .cd-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
        }
    </style>
@endsection