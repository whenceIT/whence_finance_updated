@php $rc = $ratingConfig[$branch['rating']] ?? $ratingConfig['pending']; @endphp
<div class="{{ request('card_view') ? 'card' : 'col-md-6 col-lg-4' }}" id="audit-card-{{ $branch['submission_id'] }}" style="margin-bottom:20px;">
    <div class="box box-solid" style="border-top:3px solid {{ $rc['color'] }};margin-bottom:0;">

        {{-- Card header --}}
        <div class="box-header box-toggle" style="background:{{ $rc['bg'] }};padding:10px 14px;cursor:pointer;">
            <div class="toggle-icon" style="float:right;margin-top:4px;transition:transform .2s;">
                <i class="fa fa-chevron-down" style="color:#999;"></i>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <h4 style="margin:0;font-size:15px;color:#333;">
                        <i class="fa fa-map-marker" style="color:{{ $rc['color'] }};"></i>
                        &nbsp;{{ $branch['name'] }}
                    </h4>
                    <small class="text-muted">{{ $branch['code'] }}</small>
                </div>
                <span class="label label-{{ $rc['badge'] }}" style="font-size:11px;padding:4px 8px;background:{{ $rc['color'] }};">
                    {{ $rc['label'] }}
                </span>
            </div>

            {{-- Audit metadata row --}}
            <div style="margin-top:8px;font-size:12px;color:#555;">
                {{-- Audit type badge --}}
                @if(!empty($branch['audit_type']))
                    @php
                        $typeIcons = [
                            'routine' => ['icon' => 'fa-calendar-check-o', 'color' => '#3498db'],
                            'special' => ['icon' => 'fa-star', 'color' => '#f39c12'],
                            'follow-up' => ['icon' => 'fa-repeat', 'color' => '#9b59b6'],
                            'investigation' => ['icon' => 'fa-search', 'color' => '#e74c3c'],
                        ];
                        $typeConfig = $typeIcons[strtolower($branch['audit_type'])] ?? ['icon' => 'fa-clipboard', 'color' => '#95a5a6'];
                    @endphp
                    <span class="label" style="background:{{ $typeConfig['color'] }};font-size:10px;padding:3px 6px;margin-right:6px;">
                        <i class="fa {{ $typeConfig['icon'] }}"></i> {{ ucfirst($branch['audit_type']) }}
                    </span>
                @endif

                {{-- Unannounced badge --}}
                @if(!empty($branch['unannounced']) && strtolower($branch['unannounced']) === 'yes')
                    <span class="label label-warning" style="font-size:10px;padding:3px 6px;margin-right:6px;">
                        <i class="fa fa-bolt"></i> Unannounced
                    </span>
                @endif

                {{-- Total checklist items --}}
                @php
                    $itemCounts = config('risk-audit.section_item_counts', []);
                    $totalChecklistItems = array_sum($itemCounts);
                @endphp
                <span class="label label-default" style="font-size:10px;padding:3px 6px;">
                    <i class="fa fa-list-ol"></i> {{ $totalChecklistItems }} items
                </span>
            </div>

            {{-- Time and auditor info --}}
            <div style="margin-top:6px;font-size:11px;color:#666;">
                <i class="fa fa-clock-o"></i> {{ $branch['created_at_human'] ?? $branch['audit_date_human'] }}
                &nbsp;&nbsp;
                <i class="fa fa-user-o"></i> {{ $branch['auditor'] }}
                &nbsp;&nbsp;
                <strong style="color:{{ $rc['color'] }};">
                    <i class="fa fa-times-circle"></i> {{ $branch['fail_count'] }} fail{{ $branch['fail_count'] !== 1 ? 's' : '' }}
                </strong>
            </div>

            {{-- Opening remarks preview --}}
            @if(!empty($branch['opening_remarks']))
                <div style="margin-top:6px;padding:6px 8px;background:rgba(255,255,255,0.5);border-radius:3px;font-size:11px;color:#555;font-style:italic;">
                    <i class="fa fa-comment-o" style="color:#95a5a6;"></i>
                    {{ $branch['opening_remarks'] }}
                </div>
            @endif
        </div>

        {{-- Section breakdown --}}
        <div class="box-body box-body-{{ $branch['submission_id'] }}" style="padding:12px 14px;">
            <table style="width:100%;font-size:11px;border-collapse:collapse;">
                <thead>
                    <tr style="color:#888;border-bottom:1px solid #eee;">
                        <th style="padding:3px 4px;text-align:left;font-weight:600;">Section</th>
                        <th style="padding:3px 4px;text-align:center;color:#27ae60;">✓</th>
                        <th style="padding:3px 4px;text-align:center;color:#c0392b;">✗</th>
                        <th style="padding:3px 4px;text-align:center;color:#aaa;">N/A</th>
                        <th style="padding:3px 4px;text-align:left;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectionShorts as $si => $sname)

                        {{-- ── Section 0 = Admin (metadata, not pass/fail/na) ─────────── ── --}}
                        @if($si === 0)
                            <tr style="border-bottom:1px solid #f5f5f5;background:#f9f9f9;">
                                <td style="padding:4px 4px;color:#555;font-style:italic;font-size:10px;" colspan="5">
                                    <i class="fa fa-info-circle" style="margin-right:4px;"></i>
                                    <strong>{{ $sname }}</strong>
                                    &mdash;
                                    <span style="color:#999;">{{ count(config('risk-audit.admin_metadata_fields', [])) }} metadata fields</span>
                                </td>
                            </tr>

                        @else
                            @php
                                $sec   = $branch['sections'][$si] ?? ['pass'=>0,'fail'=>0,'na'=>0];
                                $total = $sec['pass'] + $sec['fail'] + $sec['na'];
                            @endphp
                            @if($total > 0)
                                @php
                                    $pct = round(($sec['pass'] / $total) * 100);
                                    $failRatio = $sec['fail'] / $total;
                                    $barColor  = $failRatio == 0              ? '#27ae60'
                                               : ($failRatio <= 0.10        ? '#f39c12'
                                               : ($failRatio <= 0.25        ? '#e67e22'
                                               : '#c0392b'));
                                @endphp
                                <tr style="border-bottom:1px solid #f5f5f5;">
                                    <td style="padding:4px 4px;color:#444;cursor:pointer;" onclick="loadSectionDetails({{ $branch['submission_id'] }}, {{ $si }}, '{{ $sname }}')">{{ $sname }}</td>
                                <td style="padding:4px 4px;text-align:center;color:#27ae60;font-weight:bold;">{{ $sec['pass'] }}</td>
                                <td style="padding:4px 4px;text-align:center;{{ $sec['fail'] > 0 ? 'color:#c0392b;font-weight:bold;' : 'color:#ccc;' }}">
                                    {{ $sec['fail'] > 0 ? $sec['fail'] : '—' }}
                                </td>
                                <td style="padding:4px 4px;text-align:center;color:#aaa;">{{ $sec['na'] > 0 ? $sec['na'] : '—' }}</td>
                                <td style="padding:4px 4px;min-width:70px;">
                                    <div style="background:#eee;border-radius:3px;height:6px;overflow:hidden;">
                                        <div style="width:{{ $pct }}%;background:{{ $barColor }};height:6px;border-radius:3px;transition:width .3s;"></div>
                                    </div>
                                </td>
                            </tr>
                            @endif {{-- $total > 0 --}}
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Card footer --}}
        <div class="box-footer" style="padding:8px 14px;background:#fafafa;border-top:1px solid #f0f0f0;text-align:right;">
            <a href="#" class="btn btn-xs btn-default" id="viewReportBtn-{{ $branch['submission_id'] }}" data-submission-id="{{ $branch['submission_id'] }}">
                <i class="fa fa-eye"></i> View Full Report
            </a>
            <a href="#" class="btn btn-xs btn-danger" style="margin-left:4px;">
                <i class="fa fa-clipboard"></i> Re-Audit
            </a>
            @if(isset($showDelete) && $showDelete)
                <button type="button" class="btn btn-xs btn-danger" style="margin-left:4px;" onclick="confirmDeleteAudit({{ $branch['submission_id'] }}, '{{ $branch['name'] }}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            @endif
        </div>

    </div>
</div>

<script>
(function(){
    var el   = document.getElementById('audit-card-{{ $branch["submission_id"] }}');
    var head = document.querySelector('.audit-card-{{ $branch["submission_id"] }} .box-toggle');
    var body = head && head.closest('.box').querySelector('.box-body');
    var icon = head && head.querySelector('.toggle-icon');
    if (!head || !body) return;
    head.addEventListener('click', function(e){
        if (e.target.closest('a') || e.target.closest('button')) return;
        body.style.display = body.style.display === 'none' ? '' : 'none';
        if (icon) icon.style.transform = body.style.display === 'none' ? 'rotate(-90deg)' : '';
    });
})();
</script>
