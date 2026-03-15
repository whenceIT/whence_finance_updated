<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Specialist Report — {{ ucfirst($period) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size:12px; color:#222; padding:30px; }
        .header { border-bottom:3px solid #00a04a; padding-bottom:16px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:flex-end; }
        .header h1 { font-size:20px; color:#00a04a; }
        .meta { text-align:right; color:#666; font-size:11px; }
        .kpi-row { display:flex; gap:12px; margin-bottom:20px; }
        .kpi-box { flex:1; border:1px solid #ddd; border-top:3px solid #00a04a; border-radius:4px; padding:12px 14px; }
        .kpi-box.blue { border-top-color:#00b5d8; }
        .kpi-box.yellow { border-top-color:#f39c12; }
        .kpi-box.red { border-top-color:#e74c3c; }
        .kpi-label { font-size:10px; color:#888; text-transform:uppercase; margin-bottom:4px; }
        .kpi-value { font-size:18px; font-weight:700; }
        h2 { font-size:13px; font-weight:700; color:#00a04a; border-bottom:1px solid #eee; padding-bottom:6px; margin:20px 0 10px; text-transform:uppercase; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        thead tr { background:#f5f5f5; }
        th { padding:8px 10px; text-align:left; font-size:11px; font-weight:700; color:#555; border-bottom:2px solid #ddd; }
        th.right, td.right { text-align:right; }
        th.center, td.center { text-align:center; }
        td { padding:7px 10px; border-bottom:1px solid #eee; font-size:11px; }
        tr:nth-child(even) { background:#fafafa; }
        tfoot tr { background:#f0f0f0; font-weight:700; }
        .badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; color:#fff; }
        .badge-primary { background:#337ab7; }
        .badge-warning { background:#f0ad4e; }
        .badge-default { background:#aaa; }
        .badge-danger  { background:#d9534f; }
        .badge-success { background:#5cb85c; }
        .badge-info    { background:#5bc0de; }
        .footer { margin-top:30px; border-top:1px solid #ddd; padding-top:10px; font-size:10px; color:#aaa; display:flex; justify-content:space-between; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body>

<div class="header">
    <div>
        <div style="font-size:11px;color:#888;margin-bottom:4px">WHENCE FINANCIAL SERVICES</div>
        <h1>Specialist Performance Report</h1>
        <div style="font-size:13px;color:#555;margin-top:2px">Period: {{ ucfirst($period) }}</div>
    </div>
    <div class="meta">
        <div>Generated: {{ now()->format('d M Y, H:i') }}</div>
        <div>Recoveries Department — Confidential</div>
    </div>
</div>

@php
$totalRecovered = $specialists->sum('total_recovered');
$totalActive    = $specialists->sum('active_cases');
$totalResolved  = $specialists->sum('resolved_cases');
$catBadges = ['cross_branch'=>'badge-primary','escalated'=>'badge-warning','dormant'=>'badge-default','legal'=>'badge-danger','skip_trace'=>'badge-success'];
@endphp

<div class="kpi-row">
    <div class="kpi-box">
        <div class="kpi-label">Total Recovered</div>
        <div class="kpi-value">{{ number_format($totalRecovered, 2) }}</div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-label">Active Specialists</div>
        <div class="kpi-value">{{ $specialists->count() }}</div>
    </div>
    <div class="kpi-box yellow">
        <div class="kpi-label">Active Cases</div>
        <div class="kpi-value">{{ $totalActive }}</div>
    </div>
    <div class="kpi-box red">
        <div class="kpi-label">Needing Attention</div>
        <div class="kpi-value">{{ $specialists->whereIn('status',['at_risk','behind'])->count() }}</div>
    </div>
</div>

<h2>Specialist Performance Detail</h2>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Specialist</th>
            <th>Stream</th>
            <th class="right">Recovered</th>
            <th class="right">Share</th>
            <th class="center">Active</th>
            <th class="center">Resolved</th>
            <th class="right">Target</th>
            <th class="right">vs Target</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($specialists->sortByDesc('total_recovered')->values() as $i => $row)
        @php
            $cat   = $row['category'] ?? 'escalated';
            $catName = \App\Models\RecoveryCase::CATEGORIES[$cat] ?? ucwords(str_replace('_',' ',$cat));
            $share = $totalRecovered > 0 ? round(($row['total_recovered']/$totalRecovered)*100,1) : 0;
            $statusBadge = match($row['status']) {
                'exceeding' => 'badge-success',
                'on_track'  => 'badge-info',
                'at_risk'   => 'badge-warning',
                default     => 'badge-danger',
            };
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ trim(($row['specialist']->first_name ?? '') . ' ' . ($row['specialist']->last_name ?? '')) ?: '—' }}</strong></td>
            <td><span class="badge {{ $catBadges[$cat] ?? 'badge-default' }}">{{ $catName }}</span></td>
            <td class="right"><strong>{{ number_format($row['total_recovered'], 2) }}</strong></td>
            <td class="right">{{ $share }}%</td>
            <td class="center">{{ $row['active_cases'] }}</td>
            <td class="center">{{ $row['resolved_cases'] }}</td>
            <td class="right">{{ $row['target_amount'] > 0 ? number_format($row['target_amount'],2) : '—' }}</td>
            <td class="right">{{ $row['target_pct'] }}%</td>
            <td><span class="badge {{ $statusBadge }}">{{ ucwords(str_replace('_',' ',$row['status'])) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;color:#aaa;padding:20px">No data</td></tr>
        @endforelse
    </tbody>
    @if($specialists->count())
    <tfoot>
        <tr>
            <td colspan="3">Totals</td>
            <td class="right">{{ number_format($totalRecovered, 2) }}</td>
            <td class="right">100%</td>
            <td class="center">{{ $totalActive }}</td>
            <td class="center">{{ $totalResolved }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="footer">
    <span>{{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }} — Recoveries Department</span>
    <span>Confidential — Internal Use Only</span>
    <span>{{ now()->format('d M Y') }}</span>
</div>

<div class="no-print" style="margin-top:20px;text-align:center">
    <button onclick="window.print()"
            style="padding:10px 28px;background:#00a04a;color:#fff;border:none;border-radius:4px;font-size:14px;cursor:pointer">
        <strong>Print / Save as PDF</strong>
    </button>
    <a href="{{ url('recovery/report/specialist_report') }}"
       style="margin-left:12px;padding:10px 20px;background:#eee;color:#555;border-radius:4px;text-decoration:none;font-size:14px">
        ← Back
    </a>
</div>

</body>
</html>
