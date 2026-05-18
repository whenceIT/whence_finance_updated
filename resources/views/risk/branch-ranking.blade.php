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
                            <tr style="background:{{ $rowBg }};" data-office-id="{{ $row['office']->id }}" data-office-name="{{ $row['office']->name }}" class="br-branch-row">
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
                                <td style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                    <span style="font-weight:700;color:{{ $row['fails'] > 0 ? '#c0392b' : '#27ae60' }};">{{ $row['fails'] }}</span>
                                    <span style="padding:3px 7px;border:1px solid #ddd;border-radius:4px;font-size:10px;color:#999;background:#fff;cursor:pointer;" title="View Audit History" onclick="window.brShowAuditHistory(event, {{ $row['office']->id }}, '{{ addslashes($row['office']->name) }}')"><i class="fa fa-search"></i></span>
                                </td>
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

                <!-- Branch Audit History Modal -->
                <div class="modal fade" id="brAuditHistoryModal" tabindex="-1" role="dialog" aria-labelledby="brAuditHistoryModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg" style="max-width:900px;width:90%;">
                        <div class="modal-content" style="border-radius:6px;overflow:hidden;">

                            <!-- Modal Header -->
                            <div class="modal-header" style="background:#34495e;color:#fff;padding:14px 20px;border-bottom:none;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.8;font-size:22px;margin-top:-6px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="brAuditHistoryModalLabel" style="margin:0;font-size:15px;">
                                    <i class="fa fa-history" style="margin-right:6px;"></i>
                                    <span id="brModalOfficeName">—</span>
                                    <small id="brModalOfficeCode" style="font-weight:400;color:#bdc3c7;"></small>
                                </h4>
                            </div>

                            <!-- Shimmer Loading State -->
                            <div id="brModalShimmer" style="display:none;padding:30px 30px 20px;">
                                @php
                                    $shimmerRows = 5;
                                @endphp
                                @for($i = 0; $i < $shimmerRows; $i++)
                                <div style="margin-bottom:14px;border:1px solid #eee;border-radius:6px;overflow:hidden;">
                                    {{-- Shimmer header --}}
                                    <div style="padding:12px 16px;background:#f9f9f9;border-bottom:1px solid #eee;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <div style="width:55%;height:14px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                            <div style="width:80px;height:22px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                        </div>
                                        <div style="margin-top:8px;display:flex;gap:8px;width:100%;">
                                            <div style="width:18%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div>
                                            <div style="width:14%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div>
                                            <div style="width:25%;height:12px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div>
                                        </div>
                                    </div>
                                    {{-- Shimmer body --}}
                                    <div style="padding:14px 16px;">
                                        @for($r = 0; $r < 8; $r++)
                                        <div style="display:flex;align-items:center;margin-bottom:6px;">
                                            <div style="width:28%;height:11px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div>
                                            <div style="width:8%;height:11px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;margin-left:auto;"></div>
                                            <div style="width:8%;height:11px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;margin-left:8px;"></div>
                                            <div style="width:8%;height:11px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;margin-left:8px;"></div>
                                            <div style="flex:1;margin-left:12px;">
                                                <div style="width:100%;height:8px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:3px;"></div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                                @endfor

                                {{-- Load-more shimmer button --}}
                                <div style="text-align:center;margin-top:10px;">
                                    <div style="display:inline-block;width:160px;height:32px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:4px;"></div>
                                </div>
                            </div>

                            <!-- Modal Body (injected) -->
                            <div id="brModalBody" style="padding:30px 30px 20px;max-height:70vh;overflow-y:auto;">
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer" id="brModalFooter" style="display:none;background:#fafafa;border-top:1px solid #eee;padding:10px 20px;">
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Shimmer Keyframes -->
                <style>
                    @keyframes shimmer {
                        0% { background-position: -200% 0; }
                        100% { background-position: 200% 0; }
                    }
                    #brModalBody .br-audit-card { margin-bottom: 0; }
                    #brModalBody .br-audit-card + .br-audit-card { margin-top: 16px; }
                    #brModalBody .br-expand-btn {
                        width:100%;text-align:left;background:none;border:none;padding:8px 12px;
                        cursor:pointer;font-size:12px;color:#555;border-top:1px solid #eee;
                    }
                    #brModalBody .br-expand-btn:hover { background:#f5f5f5; }
                    #brModalBody .br-expand-btn i { transition:transform .2s; }
                    #brModalBody .br-expand-btn.expanded i { transform:rotate(180deg); }
                    #brModalBody .br-section-table { width:100%;border-collapse:collapse;font-size:11px;display:none; }
                    #brModalBody .br-section-table.visible { display:table; }
                    #brModalBody .br-section-table th, #brModalBody .br-section-table td {
                        padding:5px 6px;border-bottom:1px solid #f5f5f5;
                    }
                    #brModalBody .br-section-table th { background:#f9f9f9;color:#888;font-weight:600; }
                    #brModalBody .br-stat-badge {
                        display:inline-block;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:700;margin-bottom:4px;margin-right:4px;
                    }
                    #brModalBody .br-section-card {
                        margin:8px 12px 14px;background:#fff;border-radius:4px;padding:10px 14px;
                        border:1px solid #eee;
                    }
                </style>

            </div><!-- /box-body -->
        </div><!-- /box -->
    </div><!-- /col -->
</div><!-- /row -->
@endsection


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

    // ── Branch-row click → show audit-history modal ──────────────────────────
    var ratingCfg = {
        low:     { label:'LOW',        hex:'#27ae60', bg:'#eafaf1', color:'#1e8449', badge:'success' },
        medium:  { label:'MODERATE',   hex:'#f39c12', bg:'#fef9e7', color:'#b7770d', badge:'warning' },
        high:    { label:'HIGH',       hex:'#e74c3c', bg:'#fdedec', color:'#c0392b', badge:'danger'  },
        critical:{ label:'CRITICAL',   hex:'#7b241c', bg:'#f9ebea', color:'#7b241c', badge:'danger'  },
        pending: { label:'NO DATA',    hex:'#95a5a6', bg:'#f3f3f3', color:'#7f8c8d', badge:'default' },
    };

    var auditHistoryUrl = '{{ route("risk.audit-history", ["officeId" => "__OFFICE_ID__"]) }}';

    window.brShowAuditHistory = function (e, officeId, officeName) {
        if (e) e.stopPropagation();

        var $modal    = $('#brAuditHistoryModal');
        var $shimmer  = $('#brModalShimmer');
        var $body     = $('#brModalBody');
        var $footer   = $('#brModalFooter');

        $('#brModalOfficeName').text(officeName);
        $('#brModalOfficeCode').text('');
        $body.empty();
        $body.hide();
        $footer.hide();
        $shimmer.show();

        $modal.modal('show');

        var url = auditHistoryUrl.replace('__OFFICE_ID__', officeId);
        $.get(url, function (resp) {
            $('#brModalOfficeCode').text('(' + (resp.code || '#') + ')');

            var audits = resp.audits || [];
            if (audits.length === 0) {
                $body.html('<p style="color:#999;text-align:center;padding:30px;">No audit submissions found for this branch.</p>');
                $body.show();
                $footer.show();
                $shimmer.hide();
                return;
            }

            var html = '';
            audits.forEach(function (br, idx) {
                var rc     = ratingCfg[br.risk_rating] || ratingCfg.pending;
                var date   = br.audit_date || '—';
                var auditor = br.auditor || '—';
                var typeBadge = br.audit_type
                    ? '<span style="font-size:10px;padding:2px 6px;border-radius:3px;margin-right:4px;">' + br.audit_type.charAt(0).toUpperCase() + br.audit_type.slice(1) + '</span>'
                    : '';

                html += '<div class="br-audit-card" style="border:1px solid #eee;border-top:3px solid ' + rc.hex + ';border-radius:6px;overflow:hidden;margin-bottom:0;">';

                // Card header
                html += '<div style="padding:10px 14px;background:' + rc.bg + ';cursor:pointer;" class="br-audit-card-head" data-index="' + idx + '">';
                html += '  <div style="display:flex;justify-content:space-between;align-items:flex-start;">';
                html += '    <div>';
                html += '      <span style="font-size:13px;font-weight:700;color:#333;">' + date + '</span>';
                html += '<br><small class="text-muted">' + typeBadge + 'Auditor: ' + auditor + '</small>';
                html += '    </div>';
                html += '    <div style="display:flex;align-items:center;gap:8px;">';
                html += '      <span style="display:inline-block;padding:2px 10px;border-radius:3px;font-size:10px;font-weight:700;color:#fff;background:' + rc.hex + ';">' + rc.label + '</span>';
                html += '      <span style="font-weight:700;color:' + (br.fail_count > 0 ? '#c0392b' : '#27ae60') + ';">' + br.fail_count + ' fail' + (br.fail_count !== 1 ? 's' : '') + '</span>';
                html += '      <span class="br-toggle-icon" style="transition:transform .2s;"><i class="fa fa-chevron-down" style="color:#999;"></i></span>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';

                // Collapsible sections
                html += '<div class="br-audit-sections" id="br-sec-' + idx + '" style="display:none;padding:4px 0;">';
                html += br.sections.map(function (sec, si) {
                    var total  = sec.pass + sec.fail + sec.na;
                    if (total === 0) return '';
                    var pct    = Math.round((sec.pass / total) * 100);
                    var failR  = total > 0 ? (sec.fail / total) : 0;
                    var barClr = failR === 0 ? '#27ae60' : (failR <= 0.10 ? '#f39c12' : (failR <= 0.25 ? '#e67e22' : '#c0392b'));
                    return '<div style="padding:6px 16px;">' +
                        '  <div style="display:flex;align-items:center;gap:8px;">' +
                        '    <span style="font-size:10px;color:#555;min-width:100px;font-weight:600;">Section ' + (si + 2) + '</span>' +
                        '    <span style="font-size:10px;color:#27ae60;font-weight:700;min-width:20px;text-align:center;">' + sec.pass + '</span>' +
                        '    <span style="font-size:10px;color:#c0392b;font-weight:700;min-width:20px;text-align:center;">' + (sec.fail > 0 ? sec.fail : '—') + '</span>' +
                        '    <span style="font-size:10px;color:#aaa;min-width:20px;text-align:center;">' + (sec.na > 0 ? sec.na : '—') + '</span>' +
                        '    <div style="flex:1;background:#eee;border-radius:3px;height:6px;overflow:hidden;">' +
                        '      <div style="width:' + pct + '%;background:' + barClr + ';height:6px;border-radius:3px;"></div>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>';
                }).join('');
                html += '</div>';

                html += '</div>';
            });

            $body.html(html);
            $body.show();
            $footer.show();
            $shimmer.hide();

            // Collapse / expand handlers
            $body.find('.br-audit-card-head').off('click').on('click', function () {
                var idx     = parseInt($(this).data('index'));
                var $panel  = $('#br-sec-' + idx);
                var $icon   = $(this).find('.br-toggle-icon');
                var visible = $panel.is(':visible');
                $panel.slideToggle(200);
                $icon.toggleClass('expanded', !visible);
            });
        })
        .fail(function () {
            $body.html('<p style="color:#c0392b;text-align:center;padding:30px;">Failed to load audits. Please try again.</p>');
            $body.show();
            $footer.show();
            $shimmer.hide();
        });
    };

    // Row click: open modal for that office
    document.addEventListener('click', function (e) {
        var row = e.target.closest('.br-branch-row');
        if (!row) return;
        if (e.target.closest('a') || e.target.closest('button')) return;
        var oid   = row.getAttribute('data-office-id');
        var oname = row.getAttribute('data-office-name');
        if (oid) window.brShowAuditHistory(null, parseInt(oid), oname || ('Office #' + oid));
    });

})();
</script>
