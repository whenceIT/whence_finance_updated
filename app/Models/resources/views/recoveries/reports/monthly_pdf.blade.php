<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recovery Report — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 30px; }
        .header { border-bottom: 3px solid #00a04a; padding-bottom: 16px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:flex-end; }
        .header h1 { font-size: 20px; color: #00a04a; }
        .header .meta { text-align:right; color: #666; font-size:11px; }
        .kpi-row { display:flex; gap:12px; margin-bottom:20px; }
        .kpi-box { flex:1; border:1px solid #ddd; border-top:3px solid #00a04a; border-radius:4px; padding:12px 14px; }
        .kpi-box.blue { border-top-color:#00b5d8; }
        .kpi-box.yellow { border-top-color:#f39c12; }
        .kpi-box.red { border-top-color:#e74c3c; }
        .kpi-label { font-size:10px; color:#888; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .kpi-value { font-size:18px; font-weight:700; color:#222; }
        h2 { font-size:13px; font-weight:700; color:#00a04a; border-bottom:1px solid #eee; padding-bottom:6px; margin:20px 0 10px; text-transform:uppercase; letter-spacing:0.5px; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        thead tr { background:#f5f5f5; }
        th { padding:8px 10px; text-align:left; font-size:11px; font-weight:700; color:#555; border-bottom:2px solid #ddd; }
        th.right, td.right { text-align:right; }
        td { padding:7px 10px; border-bottom:1px solid #eee; font-size:11px; }
        tr:nth-child(even) { background:#fafafa; }
        tfoot tr { background:#f0f0f0; font-weight:700; }
        .badge { display:inline-block; padding:2px 7px; border-radius:3px; font-size:10px; font-weight:700; }
        .badge-primary { background:#337ab7; color:#fff; }
        .badge-warning { background:#f0ad4e; color:#fff; }
        .badge-default { background:#aaa; color:#fff; }
        .badge-danger  { background:#d9534f; color:#fff; }
        .badge-success { background:#5cb85c; color:#fff; }
        .footer { margin-top:30px; border-top:1px solid #ddd; padding-top:10px; font-size:10px; color:#aaa; display:flex; justify-content:space-between; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="header">
    <div>
        <div style="font-size:11px;color:#888;margin-bottom:4px">WHENCE FINANCIAL SERVICES</div>
        <h1>Recovery Report</h1>
        <div style="font-size:13px;color:#555;margin-top:2px">
            {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </div>
    </div>
    <div class="meta">
        <div>Generated: {{ now()->format('d M Y, H:i') }}</div>
        <div>Recoveries Department</div>
    </div>
</div>

{{-- KPI Summary --}}
<div class="kpi-row">
    <div class="kpi-box">
        <div class="kpi-label">Total Recovered</div>
        <div class="kpi-value">{{ number_format($totalRecovered, 2) }}</div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-label">Cases Resolved</div>
        <div class="kpi-value">{{ $bySpecialist->sum('resolved_cases') }}</div>
    </div>
    <div class="kpi-box yellow">
        <div class="kpi-label">Active Specialists</div>
        <div class="kpi-value">{{ $bySpecialist->count() }}</div>
    </div>
    <div class="kpi-box red">
        <div class="kpi-label">Branches Active</div>
        <div class="kpi-value">{{ $branchBreakdown->count() }}</div>
    </div>
</div>

{{-- Category Breakdown --}}
<h2>Recovery by Category</h2>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th class="right">Amount Recovered</th>
            <th class="right">% of Total</th>
        </tr>
    </thead>
    <tbody>
        @php
        $catBadges = ['cross_branch'=>'badge-primary','escalated'=>'badge-warning','dormant'=>'badge-default','legal'=>'badge-danger','skip_trace'=>'badge-success'];
        @endphp
        @foreach($categories as $key => $label)
        @php $amt = $byCategory[$key]->total ?? 0; @endphp
        <tr>
            <td><span class="badge {{ $catBadges[$key] ?? 'badge-default' }}">{{ $label }}</span></td>
            <td class="right"><strong>{{ number_format($amt, 2) }}</strong></td>
            <td class="right">{{ $totalRecovered > 0 ? round(($amt/$totalRecovered)*100,1) : 0 }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td class="right">{{ number_format($totalRecovered, 2) }}</td>
            <td class="right">100%</td>
        </tr>
    </tfoot>
</table>

{{-- Specialist Performance --}}
<h2>Specialist Performance</h2>
<table>
    <thead>
        <tr>
            <th>Specialist</th>
            <th>Category</th>
            <th class="right">Recovered</th>
            <th class="right">Active Cases</th>
            <th class="right">Resolved</th>
            <th class="right">vs Target</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bySpecialist as $row)
        <tr>
            <td><strong>{{ trim(($row['specialist']->first_name ?? '') . ' ' . ($row['specialist']->last_name ?? '')) ?: '—' }}</strong></td>
            <td>
                <span class="badge {{ $catBadges[$row['category'] ?? 'escalated'] ?? 'badge-default' }}">
                    {{ $categories[$row['category']] ?? '—' }}
                </span>
            </td>
            <td class="right">{{ number_format($row['total_recovered'], 2) }}</td>
            <td class="right">{{ $row['active_cases'] }}</td>
            <td class="right">{{ $row['resolved_cases'] }}</td>
            <td class="right">{{ $row['target_pct'] }}%</td>
            <td>{{ ucwords(str_replace('_',' ',$row['status'])) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#aaa;padding:16px">No data</td></tr>
        @endforelse
    </tbody>
    @if($bySpecialist->count())
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="right">{{ number_format($bySpecialist->sum('total_recovered'), 2) }}</td>
            <td class="right">{{ $bySpecialist->sum('active_cases') }}</td>
            <td class="right">{{ $bySpecialist->sum('resolved_cases') }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Branch Breakdown --}}
<h2>Recovery by Branch</h2>
<table>
    <thead>
        <tr>
            <th>Branch</th>
            <th class="right">Cases</th>
            <th class="right">Amount Recovered</th>
        </tr>
    </thead>
    <tbody>
        @forelse($branchBreakdown as $b)
        <tr>
            <td>{{ $b->name }}</td>
            <td class="right">{{ $b->case_count }}</td>
            <td class="right">{{ number_format($b->total_recovered, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;color:#aaa;padding:16px">No data</td></tr>
        @endforelse
    </tbody>
    @if($branchBreakdown->count())
    <tfoot>
        <tr>
            <td>Total</td>
            <td class="right">{{ $branchBreakdown->sum('case_count') }}</td>
            <td class="right">{{ number_format($branchBreakdown->sum('total_recovered'), 2) }}</td>
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
    <a href="{{ url('recovery/report/overview') }}"
       style="margin-left:12px;padding:10px 20px;background:#eee;color:#555;border-radius:4px;text-decoration:none;font-size:14px">
        ← Back
    </a>
</div>

</body>
</html>
