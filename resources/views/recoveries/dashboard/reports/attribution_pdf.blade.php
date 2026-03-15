<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attribution Report</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 30px; }
        .header { border-bottom: 3px solid #00a04a; padding-bottom: 16px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:flex-end; }
        .header h1 { font-size: 20px; color: #00a04a; }
        .meta { text-align:right; color: #666; font-size:11px; }
        .kpi-row { display:flex; gap:12px; margin-bottom:20px; }
        .kpi-box { flex:1; border:1px solid #ddd; border-top:3px solid #00a04a; border-radius:4px; padding:12px 14px; }
        .kpi-box.blue { border-top-color:#00b5d8; }
        .kpi-box.yellow { border-top-color:#f39c12; }
        .kpi-box.purple { border-top-color:#6f42c1; }
        .kpi-label { font-size:10px; color:#888; text-transform:uppercase; margin-bottom:4px; }
        .kpi-value { font-size:18px; font-weight:700; }
        h2 { font-size:13px; font-weight:700; color:#00a04a; border-bottom:1px solid #eee; padding-bottom:6px; margin:20px 0 10px; text-transform:uppercase; }
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
        .note { background:#f9f9f9; border-left:3px solid #00a04a; padding:10px 14px; margin-bottom:20px; font-size:11px; color:#555; }
        .footer { margin-top:30px; border-top:1px solid #ddd; padding-top:10px; font-size:10px; color:#aaa; display:flex; justify-content:space-between; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body>
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="header">
    <div>
        <div style="font-size:11px;color:#888;margin-bottom:4px">WHENCE FINANCIAL SERVICES</div>
        <h1>Recovery Attribution Report</h1>
        <div style="font-size:13px;color:#555;margin-top:2px">
            Period: {{ ucfirst($period) }}
        </div>
    </div>
    <div class="meta">
        <div>Generated: {{ now()->format('d M Y, H:i') }}</div>
        <div>Recoveries Department</div>
    </div>
</div>

<div class="note">
    This report shows how recovered amounts are attributed across the Recoveries Department,
    the originating branch, and the supporting branch as per the Recoveries Framework.
</div>

{{-- Summary KPIs --}}
<div class="kpi-row">
    <div class="kpi-box">
        <div class="kpi-label">Grand Total</div>
        <div class="kpi-value">{{ number_format($attributionData->sum('grand_total'), 2) }}</div>
    </div>
    <div class="kpi-box blue">
        <div class="kpi-label">Recoveries Dept</div>
        <div class="kpi-value">{{ number_format($attributionData->sum('dept_total'), 2) }}</div>
    </div>
    <div class="kpi-box yellow">
        <div class="kpi-label">Origin Branch</div>
        <div class="kpi-value">{{ number_format($attributionData->sum('origin_total'), 2) }}</div>
    </div>
    <div class="kpi-box purple">
        <div class="kpi-label">Supporting Branch</div>
        <div class="kpi-value">{{ number_format($attributionData->sum('supporting_total'), 2) }}</div>
    </div>
</div>

{{-- Attribution Table --}}
<h2>Attribution by Category</h2>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th class="right">Grand Total</th>
            <th class="right">Recoveries Dept</th>
            <th class="right">Dept %</th>
            <th class="right">Origin Branch</th>
            <th class="right">Origin %</th>
            <th class="right">Supporting Branch</th>
            <th class="right">Supporting %</th>
        </tr>
    </thead>
    <tbody>
        @php
        $catBadges = ['cross_branch'=>'badge-primary','escalated'=>'badge-warning','dormant'=>'badge-default','legal'=>'badge-danger','skip_trace'=>'badge-success'];
        @endphp
        @forelse($attributionData as $row)
        @php
            $gt   = $row->grand_total ?: 1;
            $dPct = round(($row->dept_total / $gt) * 100, 1);
            $oPct = round(($row->origin_total / $gt) * 100, 1);
            $sPct = round(($row->supporting_total / $gt) * 100, 1);
            $catName = $categories[$row->category] ?? ucwords(str_replace('_',' ',$row->category));
        @endphp
        <tr>
            <td><span class="badge {{ $catBadges[$row->category] ?? 'badge-default' }}">{{ $catName }}</span></td>
            <td class="right"><strong>{{ number_format($row->grand_total, 2) }}</strong></td>
            <td class="right">{{ number_format($row->dept_total, 2) }}</td>
            <td class="right">{{ $dPct }}%</td>
            <td class="right">{{ number_format($row->origin_total, 2) }}</td>
            <td class="right">{{ $oPct }}%</td>
            <td class="right">{{ number_format($row->supporting_total, 2) }}</td>
            <td class="right">{{ $sPct }}%</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px">No data available</td></tr>
        @endforelse
    </tbody>
    @if($attributionData->count())
    <tfoot>
        <tr>
            <td>Totals</td>
            <td class="right">{{ number_format($attributionData->sum('grand_total'), 2) }}</td>
            <td class="right">{{ number_format($attributionData->sum('dept_total'), 2) }}</td>
            <td></td>
            <td class="right">{{ number_format($attributionData->sum('origin_total'), 2) }}</td>
            <td></td>
            <td class="right">{{ number_format($attributionData->sum('supporting_total'), 2) }}</td>
            <td></td>
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
    <a href="{{ url('recovery/report/attribution') }}"
       style="margin-left:12px;padding:10px 20px;background:#eee;color:#555;border-radius:4px;text-decoration:none;font-size:14px">
        ← Back
    </a>
</div>

</body>
</html>
