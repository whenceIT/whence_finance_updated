@extends('layouts.master')
@section('content')
<?php
    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
?>

<style>
    .cd-wrap { max-width: 1100px; margin: 0 auto; }

    .cd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .cd-header h1 {
        font-size: 22px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    .cd-header .subtitle {
        font-size: 13px;
        color: #8a94a6;
        margin-top: 2px;
    }

    .cd-panel {
        background: #fff;
        border: 1px solid #e6e9ef;
        border-radius: 6px;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .cd-panel-header {
        padding: 14px 20px;
        border-bottom: 1px solid #eef0f4;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cd-panel-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .cd-panel-body { padding: 20px; }

    .cd-fieldgrid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0 32px;
    }
    @media (max-width: 767px) {
        .cd-fieldgrid { grid-template-columns: 1fr; }
    }
    .cd-field {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f2f3f6;
        font-size: 13.5px;
    }
    .cd-field:last-child { border-bottom: none; }
    .cd-field .cd-label {
        color: #8a94a6;
        font-weight: 500;
        flex-shrink: 0;
    }
    .cd-field .cd-value {
        color: #2c3e50;
        font-weight: 500;
        text-align: right;
    }
    .cd-value-lg {
        font-size: 15px;
        font-weight: 800;
    }

    .cd-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    .cd-badge-active   { background: #e6f7ee; color: #1e8e5a; }
    .cd-badge-sold      { background: #e8f0fe; color: #1a56c4; }
    .cd-badge-defaulted { background: #fdeceb; color: #c0392b; }
    .cd-badge-repossessed { background: #fff4e0; color: #b7791f; }
    .cd-badge-default   { background: #eef0f4; color: #5a6472; }

    .cd-finance {
        background: #fff;
        border: 1px solid #e6e9ef;
        border-radius: 6px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .cd-finance-header {
        padding: 14px 20px;
        border-bottom: 1px solid #eef0f4;
    }
    .cd-finance-header h3 {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .cd-finance-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
    }
    @media (max-width: 900px) {
        .cd-finance-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .cd-finance-cell {
        padding: 18px 20px;
        border-right: 1px solid #eef0f4;
        border-top: 1px solid #eef0f4;
    }
    .cd-finance-cell:nth-child(4n) { border-right: none; }
    .cd-finance-cell .cd-fin-label {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #8a94a6;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .cd-finance-cell .cd-fin-value {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
    }
    .cd-finance-cell.cd-fin-balance {
        background: #fff9e8;
    }
    .cd-finance-cell.cd-fin-balance .cd-fin-label { color: #a06c00; }
    .cd-finance-cell.cd-fin-balance .cd-fin-value { color: #856404; }

    .cd-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .cd-table thead th {
        text-align: left;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #8a94a6;
        font-weight: 600;
        padding: 10px 14px;
        border-bottom: 2px solid #eef0f4;
        background: #fafbfc;
    }
    .cd-table tbody td {
        padding: 10px 14px;
        border-bottom: 1px solid #f2f3f6;
        color: #2c3e50;
        vertical-align: middle;
    }
    .cd-table tbody tr:last-child td { border-bottom: none; }
    .cd-table tbody tr:hover { background: #fafbfc; }
    .cd-empty-row td {
        text-align: center;
        color: #b3b9c4;
        padding: 22px;
        font-style: italic;
    }

    .cd-form .form-group { margin-bottom: 16px; }
    .cd-form label {
        font-size: 12.5px;
        font-weight: 600;
        color: #5a6472;
        text-transform: uppercase;
        letter-spacing: .02em;
        margin-bottom: 6px;
        display: block;
    }
    .cd-form .form-control {
        border-radius: 4px;
        border: 1px solid #dde1e8;
        font-size: 13.5px;
        box-shadow: none;
    }
    .cd-form .form-control:focus {
        border-color: #7aa7ff;
        box-shadow: 0 0 0 3px rgba(122,167,255,0.15);
    }

    .cd-panel-footer {
        padding: 14px 20px;
        border-top: 1px solid #eef0f4;
        text-align: right;
    }

    .cd-warning-panel .cd-panel-header { background: #fffaf0; }
    .cd-warning-panel .cd-panel-footer { background: #fffaf0; }

    #sold-price-alerts .alert { font-size: 13px; margin-bottom: 10px; }
</style>

<div class="cd-wrap">

    <div class="cd-header">
        <div>
            <h1>Collateral Details</h1>
            <div class="subtitle">{{ $collateral->name }}</div>
            @php
                $statusMeaning = match($collateral->status) {
                    'pledged' => 'Collateral attached to an active loan.',
                    'seizure_pending' => 'Initiated by Branch Manager, awaiting approval and handover.',
                    'seized_inventory' => 'Physically taken and in central inventory, awaiting evaluation.',
                    'valuation_completed' => 'Independent valuation recorded, not yet sold.',
                    'listed_for_sale' => 'Asset is being marketed.',
                    'sold' => 'Asset sold and proceeds received.',
                    'written_off' => 'Asset unsaleable and removed from inventory.',
                    'released' => 'Asset returned to borrower.',
                    default => '',
                };
            @endphp
            @if($statusMeaning)
            <div class="current-status-summary" style="margin-top: 0; padding: 5px 18px; border: 0px solid #e2e8f0; font-size: 13.5px; color: #4a4b4c; line-height: 1.1; display: flex; align-items: flex-start; gap: 12px;">
                <i class="fa fa-lightbulb-o" style="color: #000000; font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
                <span class="current-status-summary-text">{{ $statusMeaning }}</span>
            </div>
            @endif
        </div>
        <button onclick="window.history.back()" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Back
        </button>
 
    </div>

    <div class="cd-header">
        <div></div>
        @if($collateral->status == 'listed_for_sale')
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#sellCollateralModal">
            <i class="fa fa-tag" aria-hidden="true"></i> Sell Collateral
        </button>
        @endif
    </div>
    <!-- Overview -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Overview</h3>
        </div>
        <div class="cd-panel-body">
            <x-collateral-timeline :currentStatus="$collateral->status" :showHeader="false" />

            <div class="cd-fieldgrid">
                <div>
                    <div class="cd-field">
                        <span class="cd-label">Name</span>
                        <span class="cd-value cd-value-lg">{{ $collateral->name }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Serial Number</span>
                        <span class="cd-value">{{ $collateral->serial_num ?? 'N/A' }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Description</span>
                        <span class="cd-value">{{ $collateral->description }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Type</span>
                        <span class="cd-value">{{ optional($collateral->type)->name }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Loan</span>
                        <span class="cd-value">
                            @if($collateral->loan)
                                {{ $collateral->loan->id }}
                            @else
                                <span style="color: #c0392b; font-weight: 600;">Unassigned</span>
                            @endif
                        </span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Client</span>
                        <span class="cd-value">
                            @if($collateral->loan && $collateral->loan->client)
                                <b>{{ $collateral->loan->client->first_name }} {{ $collateral->loan->client->last_name }}</b>
                            @else
                                <span style="color: #b3b9c4;">No client</span>
                            @endif
                        </span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Office</span>
                        <span class="cd-value">
                            @if($collateral->loan && $collateral->loan->office)
                                {{ $collateral->loan->office->name }}
                            @else
                                <span style="color: #b3b9c4;">No office</span>
                            @endif
                        </span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Status</span>
                        <span class="cd-value">
                            @php $statusClass = match($collateral->status) {
                                'pledged' => 'cd-badge-active',
                                'seizure_pending' => 'cd-badge-defaulted',
                                'seized_inventory' => 'cd-badge-repossessed',
                                'valuation_completed' => 'cd-badge-sold',
                                'listed_for_sale' => 'cd-badge-sold',
                                'sold' => 'cd-badge-sold',
                                'written_off' => 'cd-badge-default',
                                'released' => 'cd-badge-active',
                                default => 'cd-badge-default',
                            }; @endphp
                            <span class="cd-badge {{ $statusClass }}">{{ match($collateral->status) {
                                'pledged' => 'Pledged',
                                'seizure_pending' => 'Seizure Pending',
                                'seized_inventory' => 'Seized/Inventory',
                                'valuation_completed' => 'Valuation Completed',
                                'listed_for_sale' => 'Listed for Sale',
                                'sold' => 'Sold',
                                'written_off' => 'Written Off',
                                'released' => 'Released',
                                default => ucfirst($collateral->status)
                            } }}</span>
                        </span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Condition</span>
                        <span class="cd-value">{{ ucfirst($collateral->condition) }}</span>
                    </div>
                </div>
                <div>
                    <div class="cd-field">
                        <span class="cd-label">Initial Price</br><small class="cd-label">(How much client bought)</small></span>
                        <span class="cd-value">{{ number_format($collateral->initial_price, 2) }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Current Worth</br><small class="cd-label">(Approved Value)</small></span>
                        
                        <span class="cd-value cd-value-lg">{{ number_format($collateral->current_worth, 2) }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Date Purchased</span>
                        <span class="cd-value">{{ optional($collateral->date_purchased)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Date Resold</span>
                        <span class="cd-value">{{ optional($collateral->date_resold)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Pledged At</span>
                        <span class="cd-value">{{ optional($collateral->pledged_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Seized At</span>
                        <span class="cd-value">{{ optional($collateral->seized_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Valuated At</span>
                        <span class="cd-value">{{ optional($collateral->valuated_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Listed At</span>
                        <span class="cd-value">{{ optional($collateral->listed_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Sold At</span>
                        <span class="cd-value">{{ optional($collateral->sold_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Written Off At</span>
                        <span class="cd-value">{{ optional($collateral->written_off_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Released At</span>
                        <span class="cd-value">{{ optional($collateral->released_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Created By</span>
                        <span class="cd-value">{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</span>
                    </div>

                    @if($collateral->status == 'sold')
                        @php
                            $soldPrice = $collateral->sold_price ?? 0;
                            $approvedValue = $collateral->approved_value ?? $collateral->current_worth ?? 0;
                            $balance = $loanBalance ?? 0;
                            $disposalCostsTotal = 0;
                            if ($collateral->disposal_costs && is_array($collateral->disposal_costs)) {
                                foreach ($collateral->disposal_costs as $cost) {
                                    $disposalCostsTotal += (float) ($cost['amount'] ?? 0);
                                }
                            }
                            $netProceeds = $soldPrice - $disposalCostsTotal;
                        @endphp

                        <div class="cd-panel cd-analysis-panel" style="margin-top: 10px;">
                            <div class="cd-panel-header">
                                <h3>Sell Analysis <button type="button" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="top" title="Net Proceeds = Sold Price - Disposal Costs. Must cover Loan Balance."><i class="fa fa-question-circle"></i></button></h3>
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
                                            <span class="cd-value">{{ number_format($disposalCostsTotal, 2) }}</span>
                                        </div>
                                        <div class="cd-field">
                                            <span class="cd-label">Net Proceeds</span>
                                            <span class="cd-value">{{ number_format($netProceeds, 2) }}</span>
                                        </div>
                                        <div class="cd-field">
                                            <span class="cd-label">Loan Balance</span>
                                            <span class="cd-value">{{ number_format($balance, 2) }}</span>
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

                                    @if($netProceeds >= $balance)
                                        @php $excess2 = $netProceeds - $balance; @endphp
                                        <span class="label label-success" style="margin-left: 8px;">Covers Loan Balance</span>
                                        @if($excess2 > 0)
                                            <div class="alert alert-success" style="margin-top: 10px;">Net proceeds exceed the loan balance by <strong>{{ number_format($excess2, 2) }}</strong>. This amount is owed to the client.</div>
                                        @endif
                                    @else
                                        @php $shortfall2 = $balance - $netProceeds; @endphp
                                        <span class="label label-danger" style="margin-left: 8px;">Below Loan Balance</span>
                                        <div class="alert alert-danger" style="margin-top: 10px;">Net proceeds are <strong>{{ number_format($netProceeds, 2) }}</strong>, but the loan balance is <strong>{{ number_format($balance, 2) }}</strong>. You need <strong>{{ number_format($shortfall2, 2) }}</strong> more to cover the loan balance.</div>
                                    @endif

                                    @if($collateral->disposal_costs && is_array($collateral->disposal_costs) && count($collateral->disposal_costs) > 0)
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
                                                    @foreach($collateral->disposal_costs as $cost)
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(is_null($collateral->loan_id) && isset($loans) && $loans->count() > 0)
    <div class="cd-panel" style="margin-top: 20px; border-left: 4px solid #f39c12;">
        <div class="cd-panel-header" style="background: #fffaf0;">
            <h3><i class="fa fa-exclamation-triangle" style="color: #f39c12;"></i> Assign Loan</h3>
        </div>
        <div class="cd-panel-body">
            <form method="post" action="{{ route('collateral.assign.loan', $collateral) }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Select Loan</label>
                    <select name="loan_id" class="form-control select2" required>
                        <option value="">Select loan</option>
                        @foreach($loans as $loan)
                            @php $client = optional($loan->client); @endphp
                            <option value="{{ $loan->id }}">
                                #{{ $loan->id }} | K{{ number_format($loan->principal, 2) }} - {{ $client->first_name ?? 'Unknown' }} {{ $client->last_name ?? '' }} ({{ ucfirst($loan->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fa fa-link"></i> Assign Loan
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Loan Financials -->
    @if(!in_array($collateral->status, ['sold', 'written_off', 'released']))
    <div class="cd-finance">
        <div class="cd-finance-header">
            <h3>Loan Financials</h3>
        </div>
        <div class="cd-finance-grid">
            <div class="cd-finance-cell">
                <div class="cd-fin-label">Loan Principal</div>
                <div class="cd-fin-value">ZMW {{ number_format($loanPrincipal ?? 0, 2) }}</div>
            </div>
            <div class="cd-finance-cell">
                <div class="cd-fin-label">Loan Interest</div>
                <div class="cd-fin-value">ZMW {{ number_format($loanInterest ?? 0, 2) }}</div>
            </div>
            <div class="cd-finance-cell">
                <div class="cd-fin-label">Loan Full Amount</div>
                <div class="cd-fin-value">ZMW {{ number_format(($loanPrincipal ?? 0) + ($loanInterest ?? 0), 2) }}</div>
            </div>
            <div class="cd-finance-cell">
                <div class="cd-fin-label">Loan Penalty</div>
                <div class="cd-fin-value">ZMW {{ number_format($loanPenalty ?? 0, 2) }}</div>
            </div>
            <div class="cd-finance-cell cd-fin-balance" style="grid-column: 1 / -1; border-right: none;">
                <div class="cd-fin-label">Loan Balance</div>
                <div class="cd-fin-value">ZMW {{ number_format($loanBalance ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    @else
    <div>
        <p>SOLD & CLOSED</p>
    </div>
    @endif
    
    @if(!in_array($collateral->status, ['sold', 'written_off', 'released']))
    <!-- Request Status Change -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Request Status Change</h3>
        </div>
        <div class="current-status-summary" style="margin-top: 0; padding: 14px 18px; background: linear-gradient(135deg, #000c3c 0%, #95aaee 100%); border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; color: #ffffff; line-height: 1.6; display: flex; align-items: flex-start; gap: 12px;">
            <i class="fa fa-lightbulb-o" style="color: #fefefe; font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
            <span class="current-status-summary-text"></span>
        </div>
        @php
            $workflow = [
                'pledged' => [
                    'next' => 'seizure_pending',
                    'label' => 'Request Seizure',
                    'roles' => [3, 4],
                ],
                'seizure_pending' => [
                    'next' => 'seized_inventory',
                    'label' => 'Approve Seizure',
                    'roles' => [1],
                ],
                'seized_inventory' => [
                    'next' => 'valuation_completed',
                    'label' => 'Mark as Valuation Completed',
                    'roles' => [1],
                ],
                'valuation_completed' => [
                    'next' => 'listed_for_sale',
                    'label' => 'List for Sale',
                    'roles' => [1],
                ],
                'listed_for_sale' => [
                    'next' => 'written_off',
                    'label' => 'Write Off',
                    'roles' => [1],
                ],
            ];
            $currentWorkflow = $workflow[$collateral->status] ?? null;
            $canAdvance = $currentWorkflow && in_array($role, $currentWorkflow['roles']);
        @endphp
        @if($canAdvance)
        <form method="post" action="{{ route('collateral.workflow.next', $collateral) }}" style="display: inline;">
            {{ csrf_field() }}
            <div class="cd-panel-body cd-form">
                <div class="form-group">
                    <label>Next Step</label>
                    <p class="form-control-static" style="margin-top: 7px;">
                        Current: <strong>{{ match($collateral->status) {
                            'pledged' => 'Pledged',
                            'seizure_pending' => 'Seizure Pending',
                            'seized_inventory' => 'Seized/Inventory',
                            'valuation_completed' => 'Valuation Completed',
                            'listed_for_sale' => 'Listed for Sale',
                            'sold' => 'Sold',
                            'written_off' => 'Written Off',
                            'released' => 'Released',
                            default => ucfirst($collateral->status),
                        } }}</strong>
                        → <strong>{{ match($currentWorkflow['next']) {
                            'seizure_pending' => 'Seizure Pending',
                            'seized_inventory' => 'Seized/Inventory',
                            'valuation_completed' => 'Valuation Completed',
                            'listed_for_sale' => 'Listed for Sale',
                            'written_off' => 'Written Off',
                            default => ucfirst($currentWorkflow['next']),
                        } }}</strong>
                    </p>
                </div>
                <div class="form-group">
                    <label>Reason <span style="color: #c0392b;">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
            </div>
            <div class="cd-panel-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-arrow-right"></i> {{ $currentWorkflow['label'] }}
                </button>
            </div>
        </form>
        @else
        <div class="cd-panel-body">
            <p style="color: #8a94a6; font-style: italic;">No workflow action available for your role at this stage.</p>
        </div>
        @endif
    </div>

    @if($role == 20)
    <!-- Change Status Directly -->
    <div class="cd-panel cd-warning-panel">
        <div class="cd-panel-header">
            <h3>Change Status Directly</h3>
        </div>
        <form method="post" action="{{ route('collateral.direct_change', $collateral) }}">
            {{ csrf_field() }}
            <div class="cd-panel-body cd-form">
                <div class="form-group">
                    <label>New Status</label>
                    <select name="new_status" class="form-control" required>
                        <option value="pledged">Pledged</option>
                        <option value="seizure_pending">Seizure Pending</option>
                        <option value="seized_inventory">Seized/Inventory</option>
                        <option value="valuation_completed">Valuation Completed</option>
                        <option value="listed_for_sale">Listed for Sale</option>
                        <option value="sold">Sold</option>
                        <option value="written_off">Written Off</option>
                        <option value="released">Released</option>
                    </select>
                </div>
            </div>
            <div class="cd-panel-footer">
                <button type="submit" class="btn btn-warning">Change Status</button>
            </div>
        </form>
    </div>
    @endif

    @if($role == 1)
    <!-- Audit History -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Audit History</h3>
        </div>
        <div class="cd-panel-body" style="padding: 0; overflow-x: auto;">
            <table class="cd-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>IP</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collateral->auditTrail as $audit)
                        <tr>
                            <td>{{ $audit->action }}</td>
                            <td>{{ optional($audit->user)->first_name }} {{ optional($audit->user)->last_name }}</td>
                            <td>{{ $audit->ip_address }}</td>
                            <td>{{ optional($audit->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr class="cd-empty-row"><td colspan="4">No audit records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Status Change History -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Status Change History</h3>
        </div>
        <div class="cd-panel-body" style="padding: 0; overflow-x: auto;">
            <table class="cd-table">
                <thead>
                    <tr>
                        <th>Old Status</th>
                        <th>New Status</th>
                        <th>Reason</th>
                        <th>Requested By</th>
                        <th>Approved By</th>
                        <th>Request Date</th>
                        <th>Approval Date</th>
                        <th>Approval Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collateral->statusChanges as $change)
                        <tr>
                            <td>{{ ucfirst($change->old_status) }}</td>
                            <td>{{ ucfirst($change->new_status) }}</td>
                            <td>{{ $change->reason }}</td>
                            <td>{{ optional($change->requested_by)->first_name }} {{ optional($change->requested_by)->last_name }}</td>
                            <td>{{ optional($change->approved_by)->first_name }} {{ optional($change->approved_by)->last_name }}</td>
                            <td>{{ optional($change->request_date)->format('Y-m-d') }}</td>
                            <td>{{ optional($change->approval_date)->format('Y-m-d') }}</td>
                            <td>{{ ucfirst($change->approval_status) }}</td>
                        </tr>
                    @empty
                        <tr class="cd-empty-row"><td colspan="8">No status change records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '[data-toggle="tooltip"]', function(e) {
        e.preventDefault();
        $(this).tooltip('toggle');
    });

    var approvedValue = {{ $collateral->approved_value ?? $collateral->current_worth ?? 0 }};
    var loanBalance = {{ $loanBalance ?? 0 }};

    function getDisposalCostsTotal() {
        var total = 0;
        $('.disposal-cost-item').each(function() {
            var amount = parseFloat($(this).find('.disposal-cost-amount').val()) || 0;
            total += amount;
        });
        return total;
    }

    function updateSellAnalysis() {
        var $modal = $('#sellCollateralModal');
        var soldPrice = parseFloat($modal.find('input[name="sold_price"]').val()) || 0;
        var disposalCosts = 0;
        $modal.find('.disposal-cost-item').each(function() {
            var amount = parseFloat($(this).find('.disposal-cost-amount').val()) || 0;
            disposalCosts += amount;
        });
        var netProceeds = soldPrice - disposalCosts;

        $modal.find('#sell-analysis-approved').text(approvedValue.toFixed(2));
        $modal.find('#sell-analysis-sold').text(soldPrice.toFixed(2));
        $modal.find('#sell-analysis-disposal').text(disposalCosts.toFixed(2));
        $modal.find('#sell-analysis-net').text(netProceeds.toFixed(2));
        $modal.find('#sell-analysis-loan-balance').text(loanBalance.toFixed(2));

        // Verdict 1: Sold Price vs Approved Value
        var verdict1Badge = '';
        var verdict1Message = '';
        if (soldPrice >= approvedValue) {
            var excess = soldPrice - approvedValue;
            verdict1Badge = '<span class="label label-success">Meets Minimum Value</span>';
            if (excess > 0) {
                verdict1Message = '<div class="alert alert-success" style="margin-top: 10px;">Sold price exceeds approved value by <strong>' + excess.toFixed(2) + '</strong>.</div>';
            }
            $modal.find('#sell-confirm-btn').prop('disabled', false);
        } else {
            var shortfall1 = approvedValue - soldPrice;
            verdict1Badge = '<span class="label label-danger">Below Approved Value</span>';
            verdict1Message = '<div class="alert alert-danger" style="margin-top: 10px;">The disposal value is <strong>' + soldPrice.toFixed(2) + '</strong>. It must be at least <strong>' + approvedValue.toFixed(2) + '</strong>. You need <strong>' + shortfall1.toFixed(2) + '</strong> more to proceed.</div>';
            $modal.find('#sell-confirm-btn').prop('disabled', true);
        }
        $modal.find('#sell-analysis-verdict1').html(verdict1Badge + verdict1Message);

        // Verdict 2: Net Proceeds vs Loan Balance (always shown)
        var verdict2Badge = '';
        var verdict2Message = '';
        if (netProceeds >= loanBalance) {
            var excess2 = netProceeds - loanBalance;
            verdict2Badge = '<span class="label label-success">Covers Loan Balance</span>';
            if (excess2 > 0) {
                verdict2Message = '<div class="alert alert-success" style="margin-top: 10px;">Net proceeds exceed loan balance by <strong>' + excess2.toFixed(2) + '</strong>.</div>';
            }
        } else {
            var shortfall2 = loanBalance - netProceeds;
            verdict2Badge = '<span class="label label-danger">Below Loan Balance</span>';
            verdict2Message = '<div class="alert alert-danger" style="margin-top: 10px;">Net proceeds are <strong>' + netProceeds.toFixed(2) + '</strong>, but the loan balance is <strong>' + loanBalance.toFixed(2) + '</strong>. You need <strong>' + shortfall2.toFixed(2) + '</strong> more to cover the loan balance.</div>';
        }
        $modal.find('#sell-analysis-verdict2').html(verdict2Badge + verdict2Message);
    }

    $('input[name="sold_price"], input[name="disposal_costs_amount"], input[name*="disposal_costs"][name*="amount"]').on('input', function() {
        updateSellAnalysis();
    });

    $('#sell-add-cost-btn').on('click', function() {
        var index = $('.disposal-cost-item').length;
        var html = '<div class="disposal-cost-item" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">' +
            '<input type="text" name="disposal_costs[' + index + '][name]" class="form-control disposal-cost-name" placeholder="Cost item (e.g. Security)" style="flex: 2;">' +
            '<input type="number" name="disposal_costs[' + index + '][amount]" class="form-control disposal-cost-amount" placeholder="Amount" step="0.01" min="0" style="flex: 1;">' +
            '<button type="button" class="btn btn-danger btn-sm remove-cost-btn">&times;</button>' +
            '</div>';
        $('#disposal-costs-container').append(html);
    });

    $(document).on('click', '.remove-cost-btn', function() {
        $(this).closest('.disposal-cost-item').remove();
        updateSellAnalysis();
    });

    $('#sell-preview-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $section = $('#sell-analysis-section');

        $btn.prop('disabled', true).text('Analyzing...');
        $section.hide();
        $('#sell-analysis-verdict1').empty();
        $('#sell-analysis-verdict2').empty();
        $('#sell-confirm-btn').prop('disabled', true);

        setTimeout(function() {
            updateSellAnalysis();
            $section.slideDown();
            $btn.prop('disabled', false).text('Analyze');
        }, 600);
    });

    $('#sell-confirm-btn').on('click', function() {
        var $modal = $('#sellCollateralModal');
        var soldPrice = parseFloat($modal.find('input[name="sold_price"]').val()) || 0;

        if (soldPrice < approvedValue) {
            alert('The disposal value must be equal to or greater than the recorded collateral value of ' + approvedValue.toFixed(2) + '.');
            return;
        }

        $modal.find('form').off('submit').submit();
    });
});
</script>

<div class="modal fade" id="sellCollateralModal" tabindex="-1" role="dialog" aria-labelledby="sellCollateralModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="sellCollateralModalLabel">Sell Collateral</h4>
            </div>
            <form method="post" action="{{ route('collateral.sell', $collateral) }}">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Sold Price</label>
                        <input type="number" name="sold_price" class="form-control" step="0.01" min="{{ $collateral->approved_value ?? $collateral->current_worth ?? 0 }}" required>
                        <small class="form-help-text">Approved collateral value (must be ≥ {{ number_format($collateral->approved_value ?? $collateral->current_worth ?? 0, 2) }})</small>
                    </div>

                    <div class="form-group">
                        <label>Disposal Costs</label>
                        <div id="disposal-costs-container">
                            <div class="disposal-cost-item" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                <input type="text" name="disposal_costs[0][name]" class="form-control disposal-cost-name" placeholder="Cost item (e.g. Security)" style="flex: 2;">
                                <input type="number" name="disposal_costs[0][amount]" class="form-control disposal-cost-amount" placeholder="Amount" step="0.01" min="0" style="flex: 1;">
                            </div>
                        </div>
                        <button type="button" class="btn btn-default btn-sm" id="sell-add-cost-btn" style="margin-top: 5px;">
                            <i class="fa fa-plus"></i> Add Cost Item
                        </button>
                        <small class="form-help-text">Expenses incurred during collateral disposal</small>
                    </div>

                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>

                    <div id="sell-analysis-section" class="cd-panel cd-analysis-panel" style="margin-top: 20px; display: none;">
                        <div class="cd-panel-header">
                            <h3>Sell Analysis <button type="button" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="top" title="Net Proceeds = Sold Price - Disposal Costs. Must cover Loan Balance."><i class="fa fa-question-circle"></i></button></h3>
                        </div>
                        <div class="cd-panel-body">
                            <div class="cd-fieldgrid">
                                <div>
                                    <div class="cd-field">
                                        <span class="cd-label">Approved Value</span>
                                        <span class="cd-value" id="sell-analysis-approved">{{ number_format($collateral->approved_value ?? $collateral->current_worth ?? 0, 2) }}</span>
                                    </div>
                                    <div class="cd-field">
                                        <span class="cd-label">Sold Price</span>
                                        <span class="cd-value" id="sell-analysis-sold">0.00</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="cd-field">
                                        <span class="cd-label">Disposal Costs</span>
                                        <span class="cd-value" id="sell-analysis-disposal">0.00</span>
                                    </div>
                                    <div class="cd-field">
                                        <span class="cd-label">Net Proceeds</span>
                                        <span class="cd-value" id="sell-analysis-net">0.00</span>
                                    </div>
                                    <div class="cd-field">
                                        <span class="cd-label">Loan Balance</span>
                                        <span class="cd-value" id="sell-analysis-loan-balance">{{ number_format($loanBalance ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 12px;">
                                <div id="sell-analysis-verdict1"></div>
                                <div id="sell-analysis-verdict2" style="margin-top: 8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sell-preview-btn">Analyze</button>
                    <button type="button" class="btn btn-success" id="sell-confirm-btn" disabled>Confirm Sell Request</button>
                </div>
            </form>
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
    .form-help-text {
        display: block;
        margin-top: 4px;
        color: #8a94a6;
        font-size: 12px;
    }
</style>

<script>
$(document).ready(function() {
    function updateStatusMeaning() {
        var status = $('select[name="new_status"]').val();
        var $summaries = $('.current-status-summary-text');
        var meanings = {
            'pledged': 'Collateral attached to an active loan.',
            'seizure_pending': 'Initiated by Branch Manager, awaiting approval and handover.',
            'seized_inventory': 'Physically taken and in central inventory, awaiting evaluation.',
            'valuation_completed': 'Independent valuation recorded, not yet sold.',
            'listed_for_sale': 'Asset is being marketed.',
            'sold': 'Asset sold and proceeds received.',
            'written_off': 'Asset unsaleable and removed from inventory.',
            'released': 'Asset returned to borrower.'
        };

        var text = meanings[status] || '';
        $summaries.text(text);
    }

    $('select[name="new_status"]').on('change', updateStatusMeaning);
    updateStatusMeaning();
});
</script>
@endsection