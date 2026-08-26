@extends('layouts.master')
@section('title', 'Collateral Approval Queue')
@section('content')
    @php
        $isValuator = Sentinel::getUser()->isCollateralValuator();
        $showWriteOff = in_array($roleId, [1]);
        $showRelease = in_array($roleId, [4]);
        $activeTab = $isValuator ? 'seizure-pending-tab' : ($showWriteOff ? 'pending-written-off-tab' : ($showRelease ? 'release-pending-tab' : ''));
    @endphp
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral Approval Queue</h3>
        </div>
        <div class="box-body">
            <ul class="nav nav-tabs" role="tablist">
                @if($isValuator)
                <li role="presentation" class="@if($activeTab === 'seizure-pending-tab') active @endif">
                    <a href="#seizure-pending-tab" aria-controls="seizure-pending-tab" role="tab" data-toggle="tab">
                        Seizure Pending <span class="badge">{{ $seizurePending->count() }}</span>
                    </a>
                </li>
                @endif
                @hasRole('role.exec')
                <li role="presentation" class="@if($activeTab === 'pending-written-off-tab') active @endif">
                    <a href="#pending-written-off-tab" aria-controls="pending-written-off-tab" role="tab" data-toggle="tab">
                        Pending Write Off <span class="badge">{{ $pendingWrittenOff->count() }}</span>
                    </a>
                </li>
                @endif
                @if($showRelease)
                <li role="presentation" class="@if($activeTab === 'release-pending-tab') active @endif">
                    <a href="#release-pending-tab" aria-controls="release-pending-tab" role="tab" data-toggle="tab">
                        Release Pending <span class="badge">{{ $releasePending->count() }}</span>
                    </a>
                </li>
                @endif
            </ul>

            <div class="tab-content" style="margin-top: 20px;">

                <!-- Seizure Pending Tab -->
                @if($isValuator)
                <div role="tabpanel" class="tab-pane @if($activeTab === 'seizure-pending-tab') active @endif" id="seizure-pending-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Client</th>
                                <th>Current Worth</th>
                                <th>Condition</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($seizurePending as $collateral)
                                <tr>
                                    <td>{{ $collateral->name }}</td>
                                    <td>{{ optional($collateral->loan)->id }}</td>
                                    <td>{{ optional(optional($collateral->loan)->client)->first_name ?? 'N/A' }} {{ optional(optional($collateral->loan)->client)->last_name ?? '' }}</td>
                                    <td>{{ number_format($collateral->current_worth, 2) }}</td>
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
                                <tr><td colspan="8" class="text-center">No seizure pending approvals.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Pending Write Off Tab -->
                @if($showWriteOff)
                <div role="tabpanel" class="tab-pane @if($activeTab === 'pending-written-off-tab') active @endif" id="pending-written-off-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Client</th>
                                <th>Current Worth</th>
                                <th>Condition</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($pendingWrittenOff as $collateral)
                                <tr>
                                    <td>{{ $collateral->name }}</td>
                                    <td>{{ optional($collateral->loan)->id }}</td>
                                    <td>{{ optional(optional($collateral->loan)->client)->first_name ?? 'N/A' }} {{ optional(optional($collateral->loan)->client)->last_name ?? '' }}</td>
                                    <td>{{ number_format($collateral->current_worth, 2) }}</td>
                                    <td>{{ ucfirst($collateral->condition) }}</td>
                                    <td>{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</td>
                                    <td>{{ $collateral->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('collateral.show', $collateral) }}" class="btn btn-xs btn-primary">View</a>
                                        <form method="post" action="{{ route('collateral.workflow.next', $collateral) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-xs">Write Off</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No pending write-off approvals.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Release Pending Tab -->
                @if($showRelease)
                <div role="tabpanel" class="tab-pane @if($activeTab === 'release-pending-tab') active @endif" id="release-pending-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Loan</th>
                                <th>Client</th>
                                <th>Current Worth</th>
                                <th>Condition</th>
                                <th>Created By</th>
                                <th>Release Requested At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($releasePending as $collateral)
                                <tr>
                                    <td>{{ $collateral->name }}</td>
                                    <td>{{ optional($collateral->loan)->id }}</td>
                                    <td>{{ optional(optional($collateral->loan)->client)->first_name ?? 'N/A' }} {{ optional(optional($collateral->loan)->client)->last_name ?? '' }}</td>
                                    <td>{{ number_format($collateral->current_worth, 2) }}</td>
                                    <td>{{ ucfirst($collateral->condition) }}</td>
                                    <td>{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</td>
                                    <td>{{ optional($collateral->release_requested_at?->format('Y-m-d H:i')) }}</td>
                                    <td>
                                        <a href="{{ route('collateral.show', $collateral) }}" class="btn btn-xs btn-primary">View</a>
                                        <form method="post" action="{{ route('collateral.approvals.approve_release', $collateral) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-xs">Approve Release</button>
                                        </form>
                                        <form method="post" action="{{ route('collateral.approvals.decline_release', $collateral) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-xs">Decline</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No release pending approvals.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
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