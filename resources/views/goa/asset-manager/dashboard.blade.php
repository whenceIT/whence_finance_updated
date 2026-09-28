@extends('layouts.master')
@section('title')
Overview - Statistics
@endsection
@section('content')
<div class="container-fluid">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <h2 style="font-weight:700;color:#1e293b;margin-bottom:4px;">
                <i class="fa fa-cubes" style="color:#2563eb;"></i> Asset Manager
            </h2>
            <p class="text-muted" style="margin:0;">Company-wide branch equipment inventory overview</p>
        </div>
    </div>

    {{-- Quick Nav --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('goa.asset-manager.inventory') }}" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Branch Inventory</a>
            <a href="{{ route('goa.asset-manager.damage-reports') }}" class="btn btn-default btn-sm"><i class="fa fa-exclamation-triangle"></i> Damage Reports</a>
            <a href="{{ route('goa.asset-manager.repairs') }}" class="btn btn-default btn-sm"><i class="fa fa-wrench"></i> Repair Tracking</a>
            <a href="{{ route('goa.asset-manager.verification') }}" class="btn btn-default btn-sm"><i class="fa fa-check-square-o"></i> Periodic Verification</a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <style>
        .asset-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }
        .asset-stat-card {
            display: flex;
            align-items: stretch;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0,0,0,.12);
            overflow: hidden;
            flex: 1 1 130px;
            min-width: 120px;
        }
        .asset-stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            min-width: 54px;
            font-size: 22px;
            color: #fff;
        }
        .asset-stat-body {
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .asset-stat-label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .4px;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        .asset-stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1;
        }
    </style>
    <div class="asset-stats">
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:#2563eb;"><i class="fa fa-cubes"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Total Items</span>
                <span class="asset-stat-value">{{ number_format($totalItems) }}</span>
            </div>
        </div>
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:#27ae60;"><i class="fa fa-check-circle"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Working</span>
                <span class="asset-stat-value">{{ number_format($totalWorking) }}</span>
            </div>
        </div>
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:#e74c3c;"><i class="fa fa-times-circle"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Damaged</span>
                <span class="asset-stat-value">{{ number_format($totalDamaged) }}</span>
            </div>
        </div>
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:#e67e22;"><i class="fa fa-wrench"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Under Repair</span>
                <span class="asset-stat-value">{{ number_format($totalUnderRepair) }}</span>
            </div>
        </div>
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:#f39c12;"><i class="fa fa-question-circle"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Missing</span>
                <span class="asset-stat-value">{{ number_format($totalMissing) }}</span>
            </div>
        </div>
        <div class="asset-stat-card">
            <div class="asset-stat-icon" style="background:{{ $overallCondition >= 90 ? '#27ae60' : ($overallCondition >= 70 ? '#f39c12' : '#e74c3c') }};"><i class="fa fa-bar-chart"></i></div>
            <div class="asset-stat-body">
                <span class="asset-stat-label">Overall Condition</span>
                <span class="asset-stat-value">{{ $overallCondition }}%</span>
            </div>
        </div>
    </div>

    {{-- Attention Required --}}
    @if($attentionItems->isNotEmpty())
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bell"></i> Attention Required</h3>
                    <span class="badge bg-red" style="float:right;">{{ $attentionItems->count() }} alert(s)</span>
                </div>
                <div class="box-body" style="padding:0;">
                    <ul class="list-group" style="margin:0;">
                        @foreach($attentionItems as $item)
                        <li class="list-group-item" style="border-left: 4px solid {{ $item['color'] }}; padding: 10px 15px;">
                            <span style="font-size:1.2em; margin-right:8px;">{{ $item['icon'] }}</span>
                            <strong>{{ $item['branch'] }}</strong> — {{ $item['message'] }}
                            @if(isset($item['link']))
                                <a href="{{ $item['link'] }}" class="btn btn-xs btn-default pull-right">View</a>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        {{-- Most Damaged Categories --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-exclamation-triangle text-red"></i> Most Damaged Asset Categories</h3>
                </div>
                <div class="box-body p-0">
                    <table class="table table-sm table-hover" style="margin:0;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Total Damaged</th>
                                <th class="text-center">Branches Affected</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mostDamagedCategories as $cat)
                            <tr>
                                <td><i class="fa fa-tag text-muted"></i> {{ $cat->name }}</td>
                                <td class="text-center"><span class="badge bg-red">{{ $cat->total_damaged }}</span></td>
                                <td class="text-center">{{ $cat->branches_affected }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No damage recorded</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Repair Spend Summary --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-wrench text-orange"></i> Repair Expenditure</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 text-center" style="border-right:1px solid #eee;">
                            <div style="font-size:2rem;font-weight:700;color:#e67e22;">K{{ number_format($totalRepairCost, 2) }}</div>
                            <div class="text-muted">Total Repair Spend</div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div style="font-size:2rem;font-weight:700;color:#3498db;">K{{ number_format($thisMonthRepairCost, 2) }}</div>
                            <div class="text-muted">This Month</div>
                        </div>
                    </div>
                    <hr style="margin:10px 0;">
                    <table class="table table-sm" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th class="text-right">Repair Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairCostByBranch->take(5) as $row)
                            <tr>
                                <td>{{ $row->office_name }}</td>
                                <td class="text-right">K{{ number_format($row->cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No repairs logged</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Top Branches with Open Damage Reports --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-map-marker text-yellow"></i> Branches — Open Damage Reports</h3>
                </div>
                <div class="box-body p-0">
                    <table class="table table-sm table-hover" style="margin:0;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th>Branch</th>
                                <th class="text-center">Open Reports</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchesWithMostDamage as $row)
                            <tr>
                                <td>{{ $row->office_name }}</td>
                                <td class="text-center"><span class="badge bg-orange">{{ $row->open_reports }}</span></td>
                                <td><a href="{{ route('goa.asset-manager.damage-reports', ['office_id' => $row->office_id]) }}" class="btn btn-xs btn-default">View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No open damage reports</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pending Verifications --}}
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-check-square-o text-blue"></i> Pending Verifications</h3>
                    <a href="{{ route('goa.asset-manager.verification') }}" class="btn btn-xs btn-default pull-right" style="margin-top:3px;">Manage</a>
                </div>
                <div class="box-body p-0">
                    <table class="table table-sm table-hover" style="margin:0;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th>Branch</th>
                                <th>Period</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingVerifications->take(8) as $v)
                            <tr>
                                <td>{{ optional($v->office)->name }}</td>
                                <td>{{ $v->period }}</td>
                                <td class="text-center">
                                    <span class="label label-warning">{{ $v->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No pending verifications</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
