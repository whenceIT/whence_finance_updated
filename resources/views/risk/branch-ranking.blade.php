@extends('layouts.master')

@section('title')
    Branch Risk Ranking Index
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Branch Risk Ranking Index</h3>
            </div>
            <div class="box-body" style="padding:16px;">
                <p>This page ranks all branches using weighted institutional risk scoring based on factors such as fraud incidents, delayed deposits, cash discrepancies, portfolio deterioration, ghost client findings, LMS overrides, policy breaches, audit findings, staff turnover, and concentration risks.</p>

                <!-- Filters -->
                <div class="row" style="margin-bottom:16px;">
                    <div class="col-md-4">
                        <select id="br-province" class="form-control input-sm">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p }}" @if(($filters['province'] ?? '') === $p) selected @endif>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="br-search" class="form-control input-sm" placeholder="Search Branch..."
                               value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-sm btn-primary" onclick="window.brApplyFilters()">Filter</button>
                        <a href="{{ route('risk.branch-ranking') }}" class="btn btn-sm btn-default">Reset</a>
                    </div>
                </div>

                <!-- Ranking Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table" id="br-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Branch</th>
                                <th style="width:80px;">Score</th>
                                <th style="width:110px;">Risk Level</th>
                                <th>Key Risk Factors</th>
                                <th style="width:60px;">Fails</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            @php
                                $rLabel = $ratingConfig[$row['rating']]['label'] ?? 'N/A';
                                $rHex   = $ratingConfig[$row['rating']]['hex']   ?? '#95a5a6';
                                // Row row-colour tint
                                $rowBg  = $row['rating'] === 'critical' ? '#f9ebea'
                                    : ($row['rating'] === 'high'     ? '#fdedec'
                                    : ($row['rating'] === 'medium'   ? '#fef9e7'
                                    : ($row['rating'] === 'low'      ? '#eafaf1'
                                    : '#f3f3f3')));
                            @endphp
                            <tr data-province="" style="background:{{ $rowBg }};">
                                <td style="font-weight:700;color:#555;">{{ $row['rank'] }}</td>
                                <td>
                                    <strong>{{ $row['office']->name }}</strong>
                                    @if($row['office']->external_id)
                                        <br><small class="text-muted">{{ $row['office']->external_id }}</small>
                                    @else
                                        <br><small class="text-muted">#{{ $row['office']->id }}</small>
                                    @endif
                                    <br><small>{{ $row['province'] }} &nbsp;/&nbsp; {{ $row['district'] }}</small>
                                </td>
                                <td style="font-weight:800;font-size:13pt;color:#222;">{{ $row['score'] }}</td>
                                <td>
                                    <span style="display:inline-block;padding:2px 10px;border-radius:4px;font-size:8pt;font-weight:700;color:#fff;background:{{ $rHex }};">
                                        {{ $rLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if($row['factors'])
                                        {{ implode(', ', $row['factors']) }}
                                    @else
                                        <em class="text-muted">—</em>
                                    @endif
                                </td>
                                <td style="font-weight:700;color:{{ $row['fails'] > 0 ? '#c0392b' : '#27ae60' }};">{{ $row['fails'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center" style="padding:24px;color:#888;">No branches found matching the current filters.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="row" style="margin-top:18px;">
                    <div class="col-md-12">
                        <div class="box box-solid" style="box-shadow:none;border:1px solid #ddd;">
                            <div class="box-header" style="padding:8px 14px;background:#f9f9f9;border-bottom:1px solid #eee;">
                                <h4 style="font-size:9.5pt;margin:0;">Summary Statistics</h4>
                            </div>
                            <div class="box-body" style="padding:10px 12px;">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6">
                                        <div style="background:#f9ebea;border-left:4px solid #7b241c;border-radius:4px;padding:12px 10px;margin-bottom:6px;">
                                            <div style="font-size:22pt;font-weight:800;color:#7b241c;line-height:1;">{{ $summary['critical'] }}</div>
                                            <div style="font-size:7.5pt;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;">Critical</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div style="background:#fdedec;border-left:4px solid #e74c3c;border-radius:4px;padding:12px 10px;margin-bottom:6px;">
                                            <div style="font-size:22pt;font-weight:800;color:#e74c3c;line-height:1;">{{ $summary['high'] }}</div>
                                            <div style="font-size:7.5pt;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;">High</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div style="background:#fef9e7;border-left:4px solid #f39c12;border-radius:4px;padding:12px 10px;margin-bottom:6px;">
                                            <div style="font-size:22pt;font-weight:800;color:#b7770d;line-height:1;">{{ $summary['medium'] }}</div>
                                            <div style="font-size:7.5pt;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;">Medium</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div style="background:#eafaf1;border-left:4px solid #27ae60;border-radius:4px;padding:12px 10px;margin-bottom:6px;">
                                            <div style="font-size:22pt;font-weight:800;color:#1e8449;line-height:1;">{{ $summary['low'] }}</div>
                                            <div style="font-size:7.5pt;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;">Low</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /box-body -->
        </div><!-- /box -->
    </div><!-- /col -->
</div><!-- /row -->
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    window.brApplyFilters = function () {
        var province = document.getElementById('br-province').value;
        var search   = document.getElementById('br-search').value.trim();
        var params   = [];
        if (province) params.push('province=' + encodeURIComponent(province));
        if (search)   params.push('search='   + encodeURIComponent(search));
        window.location.href = '{{ route("risk.branch-ranking") }}' + (params.length ? '?' + params.join('&') : '');
    };

    document.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && e.target.id === 'br-search') brApplyFilters();
    });
})();
</script>
@endpush