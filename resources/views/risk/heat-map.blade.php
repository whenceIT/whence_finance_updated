@extends('layouts.master')

@section('title')
    Enterprise Risk Heat Map
@endsection

@push('styles')
<style>
    /* ── Heat-map token palette ── */
    .hm-low      { background:#eafaf1; border-left:4px solid #07b851; }
    .hm-medium   { background:#fef9e7; border-left:4px solid #f39c12; }
    .hm-high     { background:#fdedec; border-left:4px solid #ff0000; }
    .hm-critical { background:#f9ebea; border-left:4px solid #770a00; }
    .hm-pending  { background:#f3f3f3; border-left:4px solid #95a5a6; }

    .hm-pill-low      { background:#27ae60; color:#fff; }
    .hm-pill-medium   { background:#f39c12; color:#fff; }
    .hm-pill-high     { background:#e74c3c; color:#fff; }
    .hm-pill-critical { background:#7b241c; color:#fff; }
    .hm-pill-pending  { background:#95a5a6; color:#fff; }

    /* Province panel */
    .hm-province {
        border: 1px solid #ddd; border-radius: 6px; overflow: hidden; margin-bottom: 24px;
    }
    .hm-province .hm-pv-header {
        padding: 11px 14px; color: #fff; font-size: 13.5pt; font-weight: 700;
        display: flex; align-items: center; justify-content: space-between;
    }
    .hm-province .hm-pv-body { padding: 12px 10px 6px; }
    .hm-province .hm-pv-stats {
        padding: 6px 14px 8px; background: #fafafa; border-top: 1px solid #eee;
        display: flex; flex-wrap: wrap; gap: 6px;
    }

    /* Office tile */
    .hm-office {
        border: 1px solid #ddd; border-radius: 5px; padding: 7px 9px 6px;
        margin-bottom: 7px; font-size: 10pt; cursor: default;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .hm-office:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,.12); }
    .hm-office-name { font-weight: 700; color: #333; font-size: 9.5pt; line-height: 1.3; }
    .hm-office-code { font-size: 8pt; color: #888; margin-top: 1px; }
    .hm-office-meta { font-size: 8pt; color: #666; margin-top: 3px; }
    .hm-badge {
        display: inline-block; padding: 1px 7px; border-radius: 3px;
        font-size: 8pt; font-weight: 700; margin-top: 3px;
    }
    .hm-pending-tag  { color: #95a5a6; font-style: italic; font-size: 8.5pt; margin-top: 3px; }

    /* Legend */
    .hm-legend-item { display:flex; align-items:center; gap:7px; font-size:9.5pt; }
    .hm-legend-swatch {
        width:22px; height:22px; border-radius:4px; border:1px solid #ccc; flex-shrink:0;
    }

    /* Responsive: fewer columns */
    @media(max-width:1100px) { .hm-office-grid { grid-template-columns: repeat(3, 1fr) !important; } }
    @media(max-width: 768px) { .hm-office-grid { grid-template-columns: repeat(2, 1fr) !important; } }
    @media(max-width: 500px) { .hm-office-grid { grid-template-columns: 1fr !important; } }

    /* Print page-break between provinces */
    @media print { .hm-province { page-break-inside: avoid; break-inside: avoid; } }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-fire"></i>&nbsp; Enterprise Risk Heat Map For Lateast Completed Audits</h3>
                <div class="box-tools pull-right">
                    <span class="label label-default" style="font-size:12px;">{{ $totalOffices }} offices</span>
                    &nbsp;
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body" style="padding:16px;">

                {{-- NATIONAL COUNTERS STRIP --}}
                <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:18px;">
                    @foreach($totals as $ratingKey => $count)
                        @php
                            $cfg  = $ratingConfig[$ratingKey] ?? ['label'=>'NO DATA','hex'=>'#95a5a6'];
                            $rk   = strtolower((string) $ratingKey);
                            $hex  = $cfg['hex'] ?? '#95a5a6';
                            $icon = ($rk === 'critical') ? '🚨' : (($rk === 'high') ? '🔴' : (($rk === 'medium') ? '🟡' : (($rk === 'low') ? '🟢' : '⬜')));
                            $lbl  = $cfg['label'];
                            $bg   = $rk === 'critical' ? '#f9ebea' : ($rk === 'high' ? '#fdedec' : ($rk === 'medium' ? '#fef9e7' : ($rk === 'low' ? '#eafaf1' : '#f3f3f3')));
                            $bc   = $rk === 'critical' ? '#610e06' : ($rk === 'high' ? '#ff1900' : ($rk === 'medium' ? '#f39c12' : ($rk === 'low' ? '#27ae60' : '#95a5a6')));
                        @endphp
                        <div style="position:relative;background:{{ $bg }};border:2px solid {{ $bc }};border-radius:8px;padding:16px 8px 14px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $bc }};border-radius:0;"></div>
                            <div style="font-size:20pt;line-height:1.1;margin-bottom:6px;">{{ $icon }}</div>
                            <div style="font-size:30pt;font-weight:800;line-height:1;color:#222;">{{ $count }}</div>
                            <div style="font-size:8pt;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-top:4px;color:rgba(0,0,0,.75);">{{ $lbl }}</div>
                        </div>
                    @endforeach
                </div>
                
                <hr>

                {{-- PROVINCE GRID – TILES CONTAINING OFFICE TILES --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:10px;">

                    @foreach($provincial as $provName => $pd)
                        @php
                            $worstHex = ($ratingConfig[$pd['worst']]['hex'] ?? '#95a5a6');
                            $ratingKeys = ['critical','high','medium','low','pending'];
                            $bgMapProv = [
                                'critical'=> 'rgb(255, 255, 255)',
                                'high'    => 'rgb(255, 255, 255)',
                                'medium'  => 'rgb(255, 255, 255)',
                                'low'     => 'rgb(255, 255, 255)',
                                'pending' => 'rgb(255, 255, 255)',
                            ];
                            $bgProv = $bgMapProv[$pd['worst']] ?? $bgMapProv['pending'];
                        @endphp

                        {{-- Province tile --}}
                        <div class="hm-province-tile"
                             style="background:{{ $bgProv }};border-radius:6px;overflow:hidden;">

                            {{-- Province header --}}
                            <div style="padding:10px 14px 8px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid {{ $worstHex }}22;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <i class="fa fa-map-marker" style="color:{{ $worstHex }};font-size:11pt;"></i>
                                    <span style="font-weight:700;font-size:11pt;color:#222;">{{ $provName }}</span>
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <!-- @foreach($ratingKeys as $rkIn)
                                        @php $rcIn = $pd['ratings'][$rkIn] ?? 0; @endphp
                                        @if($rcIn > 0)
                                            @php $rhIn = $ratingConfig[$rkIn]['hex'] ?? '#95a5a6'; @endphp
                                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $rhIn }};"
                                                  title="{{ $ratingConfig[$rkIn]['label'] ?? '' }}: {{ $rcIn }}"></span>
                                        @endif
                                    @endforeach -->
                                    <span style="font-size:8.5pt;font-weight:700;color:{{ $worstHex }};margin-left:4px;">
                                        {{ $pd['label'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- Summary pill strip --}}
                            <div style="padding:6px 10px 4px;display:flex;flex-wrap:wrap;gap:3px;border-bottom:1px solid #eee;">
                                @foreach($ratingKeys as $rk)
                                    @php $rc = $pd['ratings'][$rk] ?? 0; @endphp
                                    @if($rc > 0)
                                        @php $rkHex  = ($ratingConfig[$rk]['hex'] ?? '#95a5a6'); $rkLabel = ($ratingConfig[$rk]['label'] ?? 'N/A'); @endphp
                                        <span style="display:inline-block;padding:1px 7px;border-radius:10px;font-size:7.5pt;font-weight:700;color:#fff;background:{{ $rkHex }};">
                                            {{ $rkLabel }}: {{ $rc }}
                                        </span>
                                    @endif
                                @endforeach
                                <span style="font-size:7.5pt;color:#aaa;margin-left:auto;">{{ $pd['total'] }} branch{{ $pd['total'] !== 1 ? 'es' : '' }}</span>
                            </div>

                            {{-- Office tile sub-grid --}}
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(175px,1fr));gap:7px;padding:9px;">

                                @foreach($pd['offices'] as $office)
                                    @php
                                        $or   = $officeRatings[$office->id] ?? null;
                                        $rk   = $or['rating']    ?? 'pending';
                                        $hex  = $or['hex']       ?? ($ratingConfig[$rk]['hex'] ?? '#95a5a6');
                                        $label= $or['label']     ?? 'NO DATA';
                                        $fc   = $or['fail_count']?? 0;
                                        $al   = $or['audit_label']?? '';
                                        $mg   = $office->manager->name ?? '—';

                                        $bgMap = [
                                            'critical'=> 'rgba(188, 16, 0, 0.36)',
                                            'high'    => 'rgba(255, 25, 0, 0.12)',
                                            'medium'  => 'rgba(243,156,18,0.08)',
                                            'low'     => 'rgba(39,174,96,0.08)',
                                            'pending' => 'rgba(149,165,166,0.09)',
                                        ];
                                        $bg = $bgMap[$rk] ?? $bgMap['pending'];
                                    @endphp
                                    <div class="hm-office hm-{{ $rk }}"
                                         style="background:{{ $bg }};border:1px solid {{ $hex }}22;border-left:3px solid {{ $hex }};border-radius:4px;padding:7px 8px;cursor:default;"
                                         id="office-{{ $rk }}-{{ $office->id }}">
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:4px;">
                                            <div style="min-width:0;">
                                                <div style="font-weight:600;font-size:9pt;color:#222;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                    <i class="fa fa-map-marker"
                                                       style="color:{{ $hex }};margin-right:2px;font-size:8pt;"></i>{{ $office->name }}
                                                </div>
                                                <div style="font-size:7.5pt;color:#999;margin-top:1px;">
                                                    @if($office->external_id)
                                                        {{ $office->external_id }}
                                                    @elseif($or)
                                                        #{{ $office->id }}
                                                    @else
                                                        Not yet audited
                                                    @endif
                                                </div>
                                                @if($fc > 0)
                                                    <div style="font-size:7.5pt;color:#c0392b;font-weight:700;margin-top:2px;">
                                                        <i class="fa fa-times-circle"></i> {{ $fc }} fail{{ $fc !== 1 ? 's' : '' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="hm-badge hm-pill-{{ $rk }}"
                                                  id="badge-{{ $office->id }}"
                                                  style="display:inline-block;padding:1px 6px;border-radius:3px;font-size:7.5pt;font-weight:700;color:#fff;background:{{ $hex }};white-space:nowrap;">
                                                {{ $label }}
                                            </span>
                                        </div>
                                        @if($al !== '')
                                            <div style="font-size:7pt;color:#bbb;margin-top:3px;padding-top:3px;border-top:1px solid #eee;">
                                                <i class="fa fa-calendar"></i> {{ explode(' ', $al, 2)[0] ?? '' }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>{{-- /hm-office-tile-grid --}}
                        </div>{{-- /hm-province-tile --}}
                    @endforeach

                </div>{{-- /hm-province-grid --}}

                @if($totalOffices === 0)
                    <div class="alert alert-info" style="margin-top:20px;">
                        <i class="fa fa-info-circle"></i> No offices found. Please add branches in <strong>Settings → Office Management</strong>.
                    </div>
                @endif

                {{-- FOOTER --}}
                <div style="margin-top:18px;border-top:1px solid #ddd;padding-top:8px;font-size:8pt;color:#aaa;text-align:center;">
                    Enterprise Risk Heat Map &nbsp;·&nbsp;
                    Audit data source: Withinhere Branch Audit Checklist v3.0 &nbsp;·&nbsp;
                    {{ \Carbon\Carbon::now()->format('d M Y') }} &nbsp;·&nbsp;
                    Showing latest completed audit submission per office &nbsp;·&nbsp;
                    INTERNAL USE ONLY
                </div>

            </div>{{-- /box-body --}}
        </div>{{-- /box --}}
    </div>{{-- /col-md-12 --}}
</div>{{-- /row --}}
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Risk-level filter ─────────────────────────── */
    window.hmFilterRisk = function () {
        var filterVal  = (document.getElementById('hm-risk-filter') || {}).value || '';
        var offices    = document.querySelectorAll('.hm-office');
        var emptyMsg   = document.getElementById('hm-empty-msg');

        var visibleCount = 0;
        offices.forEach(function (el) {
            var id = el.id || '';
            var cls = el.className || '';
            var matches = !filterVal || cls.indexOf('hm-' + filterVal) !== -1;
            el.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        /* Show / hide empty-state placeholder */
        if (emptyMsg) {
            emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    };

    /* ── Highlight risk-level tiles on hover ────────── */
    document.addEventListener('mouseover', function (e) {
        var tile = e.target.closest('.hm-office');
        if (!tile) return;
        var cls   = tile.className || '';
        var match = cls.match(/hm-(low|medium|high|critical|pending)\b/);
        if (!match) return;
        var rk   = match[1];
        var badge= document.getElementById('badge-' + tile.id.replace('office-' + rk + '-', ''));
        if (badge) {
            badge.style.opacity   = badge.style.opacity === '.45' ? '1' : '1';
            badge.style.boxShadow = '0 0 4px rgba(0,0,0,.15)';
        }
    });
})();
</script>
@endpush