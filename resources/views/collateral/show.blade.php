@extends('layouts.master')
@section('title', 'Collateral Details')
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

    .cd-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .02em;
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
        </div>
        <button onclick="window.history.back()" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Back
        </button>
    </div>

    <!-- Overview -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Overview</h3>
        </div>
        <div class="cd-panel-body">
            <div class="cd-fieldgrid">
                <div>
                    <div class="cd-field">
                        <span class="cd-label">Name</span>
                        <span class="cd-value">{{ $collateral->name }}</span>
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
                        <span class="cd-value">{{ optional($collateral->loan)->id }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Client</span>
                        <span class="cd-value">{{ optional($collateral->loan->client)->first_name }} {{ optional($collateral->loan->client)->last_name }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Office</span>
                        <span class="cd-value">{{ optional($collateral->loan->office)->name }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Status</span>
                        <span class="cd-value">
                            @php $statusClass = 'cd-badge-' . strtolower($collateral->status ?? 'default'); @endphp
                            <span class="cd-badge {{ $statusClass }}">{{ ucfirst($collateral->status) }}</span>
                        </span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Condition</span>
                        <span class="cd-value">{{ ucfirst($collateral->condition) }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Stage</span>
                        <span class="cd-value">
                            @if($collateral->stage_icon)
                                {!! $collateral->stage_icon !!}
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $collateral->stage)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="cd-field">
                        <span class="cd-label">Initial Price</span>
                        <span class="cd-value">{{ number_format($collateral->initial_price, 2) }}</span>
                    </div>
                    <div class="cd-field">
                        <span class="cd-label">Current Worth</span>
                        <span class="cd-value">{{ number_format($collateral->current_worth, 2) }}</span>
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
                        <span class="cd-label">Created By</span>
                        <span class="cd-value">{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Financials -->
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

    <!-- Request Status Change -->
    <div class="cd-panel">
        <div class="cd-panel-header">
            <h3>Request Status Change</h3>
        </div>
        <form method="post" action="{{ route('collateral.request_change', $collateral) }}">
            {{ csrf_field() }}
            <div class="cd-panel-body cd-form">
                <div class="form-group">
                    <label>New Status</label>
                    <select name="new_status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="sold">Sold</option>
                        <option value="defaulted">Defaulted</option>
                        <option value="repossessed">Repossessed</option>
                    </select>
                </div>

                <div id="sold-fields" style="display: none;">
                    <div class="form-group">
                        <label>Sold Price</label>
                        <input type="number" value="{{ old('sold_price') ?: old('current_worth') }}" name="sold_price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Penalty</label>
                        <input type="number" value="{{ old('penalty') }}" name="penalty" class="form-control" step="0.01">
                    </div>
                </div>

                <div id="sold-price-alerts"></div>

                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
            </div>
            <div class="cd-panel-footer">
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>

    @if($role == 1 || $role == 4 || $role == 6)
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
                        <option value="active">Active</option>
                        <option value="sold">Sold</option>
                        <option value="defaulted">Defaulted</option>
                        <option value="repossessed">Repossessed</option>
                    </select>
                </div>
            </div>
            <div class="cd-panel-footer">
                <button type="submit" class="btn btn-warning">Change Status</button>
            </div>
        </form>
    </div>
    @endif

    @if($role == 1 || $role == 4 || $role == 6)
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

</div>

<script>
$(document).ready(function() {
    function toggleSoldFields() {
        var selectedStatus = $('select[name="new_status"]').val();
        if (selectedStatus === 'sold') {
            $('#sold-fields').show();
        } else {
            $('#sold-fields').hide();
        }
    }

    $('select[name="new_status"]').on('change', function() {
        toggleSoldFields();
    });

    function validateSoldPrice() {
        var selectedStatus = $('select[name="new_status"]').val();
        var alertsContainer = $('#sold-price-alerts');
        alertsContainer.empty();

        if (selectedStatus !== 'sold') {
            return true;
        }

        var soldPrice = parseFloat($('input[name="sold_price"]').val()) || 0;
        var principal = {{ $loanPrincipal ?? 0 }};
        var interest = {{ $loanInterest ?? 0 }};
        var balance = {{ $loanBalance ?? 0 }};
        var fullAmount = principal + interest;
        var excessThreshold = balance;

        var alerts = [];

        if (soldPrice < principal) {
            alerts.push('<div class="alert alert-danger"><strong>Red Flag:</strong> Sold price is below Principal (' + principal.toFixed(2) + ').</div>');
        }

        if (soldPrice < balance) {
            alerts.push('<div class="alert alert-warning"><strong>Undervalued:</strong> Sold amount (' + soldPrice.toFixed(2) + ') is below the Loan Balance (' + balance.toFixed(2) + ').</div>');
        }

        if (soldPrice > excessThreshold) {
            alerts.push('<div class="alert alert-info"><strong>Excess Collateral:</strong> Sold amount (' + soldPrice.toFixed(2) + ') is above Loan Balance.</div>');
        }

        if (alerts.length > 0) {
            alertsContainer.html(alerts.join(''));

            if (soldPrice < balance) {
                return false;
            }
        }

        return true;
    }

    $('form[action="{{ route('collateral.request_change', $collateral) }}"]').on('submit', function(e) {
        if (!validateSoldPrice()) {
            e.preventDefault();
        }
    });

    // Check on page load in case of old input
    toggleSoldFields();
});
</script>
@endsection