@extends('layouts.master')

@section('title')
    Recovery Dashboard
@endsection

@section('content')

{{-- Period Selector --}}
<div class="box box-default">
    <div class="box-body" style="padding:10px 15px">
        <form method="GET" action="{{ url('recovery/overview') }}" id="period-form" style="display:inline">

            {{-- Quick period buttons (plain links, bypass form) --}}
            <div class="btn-group" style="margin-right:10px">
                @foreach(['month' => 'This Month', 'quarter' => 'This Quarter', 'year' => 'This Year'] as $p => $label)
                    <a href="{{ url('recovery/overview') }}?period={{ $p }}"
                       class="btn btn-sm {{ $period === $p ? 'btn-primary' : 'btn-default' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Custom date range --}}
            <span class="text-muted" style="margin-right:6px;font-size:12px">Custom:</span>
            <input type="date" name="date_from"
                   value="{{ $dateFrom ?? '' }}"
                   max="{{ now()->toDateString() }}"
                   class="form-control input-sm"
                   style="display:inline-block;width:130px;vertical-align:middle">
            <span style="margin:0 4px;vertical-align:middle;color:#999">to</span>
            <input type="date" name="date_to"
                   value="{{ $dateTo ?? '' }}"
                   max="{{ now()->toDateString() }}"
                   class="form-control input-sm"
                   style="display:inline-block;width:130px;vertical-align:middle">
            <input type="hidden" name="period" value="custom">
            <button type="submit" class="btn btn-sm {{ $period === 'custom' ? 'btn-primary' : 'btn-default' }}"
                    style="margin-left:6px;vertical-align:middle">
                <i class="fa fa-filter"></i> Apply
            </button>

        </form>

        <span class="pull-right text-muted" style="line-height:30px;font-size:12px">
            @if($period === 'custom' && $dateFrom && $dateTo)
                <i class="fa fa-calendar"></i>
                {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                &ndash;
                {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            @else
                <i class="fa fa-clock-o"></i> {{ now()->format('d M Y') }}
            @endif
        </span>
    </div>
</div>


<!-- Dynamic Headline based on current filters -->
<div style="margin: 20px 0 25px 0; padding: 20px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="margin: 0 0 8px 0; color: #ffffff; font-size: 1.8rem; font-weight: 700; letter-spacing: -0.5px;">
                <i class="fa fa-line-chart" style="margin-right: 10px;"></i>
                Recovery Performance Overview
            </h2>
            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 1.1rem; font-weight: 500;">
                @if($period === 'month')
                    <i class="fa fa-calendar-o"></i> Viewing data for <strong>This Month</strong>
                    <span style="opacity: 0.8; margin-left: 10px;">({{ now()->format('F Y') }})</span>
                @elseif($period === 'quarter')
                    <i class="fa fa-calendar"></i> Viewing data for <strong>This Quarter</strong>
                    <span style="opacity: 0.8; margin-left: 10px;">(Q{{ now()->quarter }} {{ now()->year }})</span>
                @elseif($period === 'year')
                    <i class="fa fa-calendar-check-o"></i> Viewing data for <strong>This Year</strong>
                    <span style="opacity: 0.8; margin-left: 10px;">({{ now()->year }})</span>
                @elseif($period === 'custom' && $dateFrom && $dateTo)
                    <i class="fa fa-calendar-plus-o"></i> Custom Period: <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</strong>
                    <span style="opacity: 0.8; margin-left: 10px;">({{ \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1 }} days)</span>
                @else
                    <i class="fa fa-calendar"></i> Recovery Dashboard
                @endif
            </p>
        </div>
        <div style="text-align: right;">
            <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3);">
                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.8); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Last Updated</div>
                <div style="font-size: 1.1rem; color: #ffffff; font-weight: 600;">
                    <i class="fa fa-clock-o"></i> {{ now()->format('h:i A') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     ROW 1 — Primary KPI info-boxes
═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Recovered</span>
                <span class="info-box-number">{{ number_format($kpis['totalRecovered'], 2) }}</span>
                <div class="progress">
                    <div class="progress-bar"
                         style="width:{{ $kpis['recoveredChange'] !== null ? min(abs($kpis['recoveredChange']),100) : 0 }}%">
                    </div>
                </div>
                <span class="progress-description">
                    @if($kpis['recoveredChange'] !== null)
                        <i class="fa fa-arrow-{{ $kpis['recoveredChange'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($kpis['recoveredChange']) }}% vs last {{ $period }}
                    @else
                        No prior period data
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-university"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dept. Attribution</span>
                <span class="info-box-number">{{ number_format($kpis['deptRecovered'], 2) }}</span>
                <div class="progress">
                    <div class="progress-bar"
                         style="width:{{ $kpis['totalRecovered'] > 0 ? round(($kpis['deptRecovered']/$kpis['totalRecovered'])*100) : 0 }}%">
                    </div>
                </div>
                <span class="progress-description">
                    {{ $kpis['totalRecovered'] > 0 ? round(($kpis['deptRecovered']/$kpis['totalRecovered'])*100) : 0 }}% of gross
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-folder-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Cases</span>
                <span class="info-box-number">{{ $kpis['activeCases'] }}</span>
                <div class="progress">
                    <div class="progress-bar"
                         style="width:{{ ($kpis['activeCases'] + $kpis['resolvedCases']) > 0 ? round(($kpis['resolvedCases']/($kpis['activeCases']+$kpis['resolvedCases']))*100) : 0 }}%">
                    </div>
                </div>
                <span class="progress-description">
                    {{ $kpis['resolvedCases'] }} resolved &mdash; {{ $kpis['resolutionRate'] }}% rate
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Portfolio at Risk</span>
                <span class="info-box-number">{{ number_format($kpis['portfolioAtRisk'], 2) }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width:100%"></div>
                </div>
                <span class="progress-description">Outstanding in active cases</span>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 2 — Net Recovery strip (secondary info-boxes)
═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-line-chart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Net Recovered</span>
                <span class="info-box-number">{{ number_format($kpis['netRecovered'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-minus-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Costs</span>
                <span class="info-box-number">{{ number_format($kpis['totalCosts'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Resolved This {{ ucfirst($period) }}</span>
                <span class="info-box-number">{{ $kpis['resolvedCases'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Resolution Rate</span>
                <span class="info-box-number">{{ $kpis['resolutionRate'] }}%</span>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 3 — Pipeline table + Recovery Mix
═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-th-list"></i> Recovery Pipeline by Category</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/case/data') }}" class="btn btn-xs btn-default">View All →</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Active Cases</th>
                            <th>Outstanding</th>
                            <th>Recovered</th>
                            <th>Recent</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $catLabels = [
                            'cross_branch' => ['label-primary',   'fa-random'],
                            'escalated'    => ['label-warning',   'fa-arrow-up'],
                            'dormant'      => ['label-default',   'fa-moon-o'],
                            'legal'        => ['label-danger',    'fa-gavel'],
                            'skip_trace'   => ['label-success',   'fa-search'],
                        ];
                        @endphp
                        @forelse($pipeline as $slug => $pipe)
                        @php [$lClass, $icon] = $catLabels[$slug] ?? ['label-default','fa-circle']; @endphp
                        <tr>
                            <td>
                                <span class="label {{ $lClass }}">
                                    <i class="fa {{ $icon }}"></i>
                                    {{ $pipe['label'] }}
                                </span>
                            </td>
                            <td><span class="badge bg-blue">{{ $pipe['count'] }}</span></td>
                            <td><strong>{{ number_format($pipe['total_value'], 2) }}</strong></td>
                            <td>{{ number_format($pipe['amount_recovered'], 2) }}</td>
                            <td>
                                @foreach($pipe['recent_cases']->take(2) as $rc)
                                    <div style="font-size:11px;line-height:1.6">
                                        <a href="{{ url('recovery/case/' . $rc->id . '/show') }}">
                                            {{ ($rc->client->client_type ?? '') === 'business' ? ($rc->client->full_name ?? $rc->case_number) : (trim(($rc->client->first_name ?? '') . ' ' . ($rc->client->last_name ?? '')) ?: $rc->case_number) }}
                                        </a>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ url('recovery/case/' . $slug) }}"
                                   class="btn btn-xs btn-default">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding:24px">
                                No active cases
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pie-chart"></i> Recovery Mix</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                @forelse($recoveryMix as $slug => $mix)
                @php [$lClass] = $catLabels[$slug] ?? ['label-default']; @endphp
                <div class="progress-group">
                    <span class="progress-text">
                        <span class="label {{ $lClass }}">{{ $mix['label'] }}</span>
                    </span>
                    <span class="progress-number">
                        <b>{{ $mix['percentage'] }}%</b> ({{ $mix['count'] }})
                    </span>
                    <div class="progress sm">
                        <div class="progress-bar {{ str_replace('label-','progress-bar-',$lClass) }}"
                             style="width:{{ $mix['percentage'] }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center" style="padding:20px">
                    No active cases this {{ $period }}
                </p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 4 — Specialist Performance + Monthly Trend
═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-users"></i> Specialist Performance xxx</h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/specialist/data') }}"
                       class="btn btn-xs btn-default">Full Report →</a>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
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
                            $cat = $row['category'] ?? 'escalated';
                            [$barColor, $labelColor] = match($row['status']) {
                                'exceeding' => ['progress-bar-success', 'label-success'],
                                'on_track'  => ['progress-bar-info',    'label-info'],
                                'at_risk'   => ['progress-bar-warning', 'label-warning'],
                                'no_target' => ['progress-bar-default', 'label-default'],
                                default     => ['progress-bar-danger',  'label-danger'],
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>
                                    {{ $row['specialist']->first_name }}
                                    {{ $row['specialist']->last_name }}
                                </strong>
                            </td>
                            <td>
                                <span class="label {{ $catLabels[$cat][0] ?? 'label-default' }}">
                                    {{ $categories[$cat] ?? ucwords(str_replace('_', ' ', $cat)) }}
                                </span>
                            </td>
                            <td><strong>{{ number_format($row['total_recovered'], 2) }}</strong></td>
                            <td>{{ $row['active_cases'] }}</td>
                            <td>{{ $row['resolved_cases'] }}</td>
                            <td style="min-width:120px">
                                @if($row['has_target'] ?? false)
                                <div class="progress progress-xs" style="margin-bottom:2px">
                                    <div class="progress-bar {{ $barColor }}"
                                         style="width:{{ min($row['target_pct'],100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $row['target_pct'] }}% of K{{ number_format($row['target_amount'],2) }}</small>
                                @else
                                <small class="text-muted">No target set</small>
                                @endif
                            </td>
                            <td>
                                <span class="label {{ $labelColor }}">
                                    {{ $row['status'] === 'no_target' ? 'No Target' : ucwords(str_replace('_',' ',$row['status'])) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:24px">
                                No specialists assigned yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Monthly Trend</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                @php $maxTrend = collect($monthlyTrend)->max('amount') ?: 1; @endphp
                @foreach($monthlyTrend as $month)
                <div class="progress-group">
                    <span class="progress-text">{{ $month['label'] }}</span>
                    <span class="progress-number">
                        <b>{{ number_format($month['amount'], 2) }}</b>
                    </span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-aqua"
                             style="width:{{ $maxTrend > 0 ? round(($month['amount']/$maxTrend)*100) : 0 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
     ROW 5 — Branch Breakdown + Recent Activity
═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-map-marker"></i> Recovery by Branch</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                @php $bMax = $branchBreakdown->max('total_recovered') ?: 1; @endphp
                @forelse($branchBreakdown as $b)
                <div class="progress-group">
                    <span class="progress-text">{{ $b->name }}</span>
                    <span class="progress-number">
                        <b>{{ number_format($b->total_recovered, 2) }}</b>
                        <small class="text-muted"> / {{ $b->case_count }} cases</small>
                    </span>
                    <div class="progress sm">
                        <div class="progress-bar progress-bar-green"
                             style="width:{{ round(($b->total_recovered/$bMax)*100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center" style="padding:20px">
                    No branch data for this period
                </p>
                @endforelse
            </div>
            <div class="box-footer">
                <a href="{{ url('recovery/report/overview') }}" class="btn btn-sm btn-default">
                    Full Report →
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-history"></i> Recent Activity</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
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
                                    <div style="font-size:11px;color:#999">
                                        {{ ($activity->recoveryCase->client->client_type ?? '') === 'business' ? ($activity->recoveryCase->client->full_name ?? '') : trim(($activity->recoveryCase->client->first_name ?? '') . ' ' . ($activity->recoveryCase->client->last_name ?? '')) }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                {{ ucwords(str_replace('_', ' ', $activity->activity_type)) }}
                                @if(!empty($activity->description))
                                    <div style="font-size:11px;color:#999">
                                        {{ strlen($activity->description) > 50 ? substr($activity->description, 0, 50) . '…' : $activity->description }}
                                    </div>
                                @endif
                            </td>
                            <td style="white-space:nowrap">
                                {{ $activity->performedBy
                                    ? trim(($activity->performedBy->first_name ?? '') . ' ' . ($activity->performedBy->last_name ?? ''))
                                    : '—' }}
                            </td>
                            <td style="white-space:nowrap">
                                <small class="text-muted">
                                    {{ $activity->created_at->diffForHumans() }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding:24px">
                                No recent activity
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
