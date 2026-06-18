@extends('layouts.master')

@section('title')
    Recovery Dashboard 4
@endsection

@section('content')

{{-- Period Selector --}}
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Period</h3>
    </div>
    <div class="box-body">
        <div class="btn-group">
            @foreach(['month' => 'This Month', 'quarter' => 'This Quarter', 'year' => 'This Year'] as $p => $label)
                <a href="{{ url('recovery/overview') }}?period={{ $p }}"
                   class="btn btn-sm {{ $period === $p ? 'btn-primary' : 'btn-default' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Top KPI Boxes --}}
<div class="row">

    @if(Sentinel::hasAccess('recoveries.view'))
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Recovered</span>
                <span class="info-box-number">{{ number_format($kpis['totalRecovered'], 2) }}</span>
                @if($kpis['recoveredChange'] !== null)
                    <div class="progress">
                        <div class="progress-bar {{ $kpis['recoveredChange'] >= 0 ? 'progress-bar-success' : 'progress-bar-danger' }}"
                             style="width:{{ min(abs($kpis['recoveredChange']), 100) }}%"></div>
                    </div>
                    <span class="progress-description">
                        <i class="fa fa-arrow-{{ $kpis['recoveredChange'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($kpis['recoveredChange']) }}% vs last {{ $period }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(Sentinel::hasAccess('recoveries.view'))
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-university"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dept. Share</span>
                <span class="info-box-number">{{ number_format($kpis['deptRecovered'], 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ $kpis['totalRecovered'] > 0 ? round(($kpis['deptRecovered']/$kpis['totalRecovered'])*100) : 0 }}%"></div>
                </div>
                <span class="progress-description">
                    {{ $kpis['totalRecovered'] > 0 ? round(($kpis['deptRecovered']/$kpis['totalRecovered'])*100) : 0 }}% of total recovered
                </span>
            </div>
        </div>
    </div>
    @endif

    @if(Sentinel::hasAccess('recoveries.view'))
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-folder-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Cases</span>
                <span class="info-box-number">{{ $kpis['activeCases'] }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ $kpis['activeCases'] > 0 ? min(round(($kpis['resolvedCases']/($kpis['activeCases']+$kpis['resolvedCases']))*100), 100) : 0 }}%"></div>
                </div>
                <span class="progress-description">{{ $kpis['resolvedCases'] }} resolved this {{ $period }}</span>
            </div>
        </div>
    </div>
    @endif

    @if(Sentinel::hasAccess('recoveries.view'))
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Portfolio at Risk</span>
                <span class="info-box-number">{{ number_format($kpis['portfolioAtRisk'], 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:{{ $kpis['resolutionRate'] }}%"></div>
                </div>
                <span class="progress-description">{{ $kpis['resolutionRate'] }}% resolution rate</span>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- Secondary KPI row --}}
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-line-chart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Net Recovered</span>
                <span class="info-box-number">{{ number_format($kpis['netRecovered'], 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-minus-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Costs</span>
                <span class="info-box-number">{{ number_format($kpis['totalCosts'], 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Resolved This {{ ucfirst($period) }}</span>
                <span class="info-box-number">{{ $kpis['resolvedCases'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Resolution Rate</span>
                <span class="info-box-number">{{ $kpis['resolutionRate'] }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- Pipeline + Recovery Mix --}}
<div class="row">

    {{-- Pipeline by Category --}}
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pipeline by Category</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/case/data') }}" class="btn btn-xs btn-default">View All →</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Active Cases</th>
                            <th>Outstanding</th>
                            <th>Recovered</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pipeline as $slug => $pipe)
                        <tr>
                            <td><strong>{{ $pipe['label'] }}</strong></td>
                            <td><span class="badge bg-blue">{{ $pipe['count'] }}</span></td>
                            <td>{{ number_format($pipe['total_value'], 2) }}</td>
                            <td>{{ number_format($pipe['amount_recovered'], 2) }}</td>
                            <td>
                                <a href="{{ url('recovery/case/' . $slug) }}" class="btn btn-xs btn-default">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted" style="padding:20px">No active cases</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recovery Mix --}}
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recovery Mix</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @forelse($recoveryMix as $slug => $mix)
                <div class="progress-group">
                    <span class="progress-text">{{ $mix['label'] }}</span>
                    <span class="progress-number"><b>{{ $mix['percentage'] }}%</b> ({{ $mix['count'] }})</span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-primary" style="width:{{ $mix['percentage'] }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No active cases this {{ $period }}</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Specialist Performance + Monthly Trend --}}
<div class="row">

    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Specialist Performance bbb</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/specialist/data') }}" class="btn btn-xs btn-default">Full Report →</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Specialist</th>
                            <th>Category</th>
                            <th>Recovered</th>
                            <th>Active</th>
                            <th>Resolved</th>
                            <th>vs Target</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialists as $row)
                        @php
                            $barColor = match($row['status']) {
                                'exceeding' => 'progress-bar-success',
                                'on_track'  => 'progress-bar-info',
                                'at_risk'   => 'progress-bar-warning',
                                default     => 'progress-bar-danger',
                            };
                            $labelColor = match($row['status']) {
                                'exceeding' => 'success',
                                'on_track'  => 'info',
                                'at_risk'   => 'warning',
                                default     => 'danger',
                            };
                        @endphp
                        <tr>
                            <td>{{ $row['specialist']->first_name }} {{ $row['specialist']->last_name }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $row['category'] ?? '—')) }}</td>
                            <td>{{ number_format($row['total_recovered'], 2) }}</td>
                            <td>{{ $row['active_cases'] }}</td>
                            <td>{{ $row['resolved_cases'] }}</td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar {{ $barColor }}"
                                         style="width:{{ min($row['target_pct'], 100) }}%"></div>
                                </div>
                                <small>{{ $row['target_pct'] }}%</small>
                            </td>
                            <td>
                                <span class="label label-{{ $labelColor }}">
                                    {{ ucwords(str_replace('_', ' ', $row['status'])) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:20px">
                                No specialists assigned yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Monthly Trend --}}
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Monthly Trend (6 months)</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @php $maxTrend = collect($monthlyTrend)->max('amount') ?: 1; @endphp
                @foreach($monthlyTrend as $month)
                <div class="progress-group">
                    <span class="progress-text">{{ $month['label'] }}</span>
                    <span class="progress-number"><b>{{ number_format($month['amount'], 2) }}</b></span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-aqua"
                             style="width:{{ round(($month['amount']/$maxTrend)*100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Branch Breakdown + Recent Activity --}}
<div class="row">

    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recovery by Branch</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @php $bMax = $branchBreakdown->max('total_recovered') ?: 1; @endphp
                @forelse($branchBreakdown as $b)
                <div class="progress-group">
                    <span class="progress-text">{{ $b->name }}</span>
                    <span class="progress-number">
                        <b>{{ number_format($b->total_recovered, 2) }}</b>
                        <small class="text-muted">({{ $b->case_count }} cases)</small>
                    </span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-green"
                             style="width:{{ round(($b->total_recovered/$bMax)*100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No branch data for this period</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recent Activity</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/report/overview') }}" class="btn btn-xs btn-default">Full Report →</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Case</th>
                            <th>Activity</th>
                            <th>By</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $activity)
                        <tr>
                            <td>
                                @if($activity->recoveryCase)
                                    <a href="{{ url('recovery/case/' . $activity->recoveryCase->id . '/show') }}">
                                        {{ $activity->recoveryCase->case_number }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ ucwords(str_replace('_', ' ', $activity->activity_type)) }}</td>
                            <td>
                                {{ $activity->performedBy
                                    ? trim(($activity->performedBy->first_name ?? '') . ' ' . ($activity->performedBy->last_name ?? ''))
                                    : '—' }}
                            </td>
                            <td>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding:20px">No recent activity</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
