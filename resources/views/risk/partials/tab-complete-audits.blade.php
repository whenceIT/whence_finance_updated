@php
    // $grouped is pre-built in the controller (key = 'F Y', value = [branches])
@endphp

{{-- ── Live search filter ──────────────────────────────────────── --}}
<div style="margin-bottom:16px;">
    <div style="position:relative;max-width:320px;">
        <i class="fa fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#aaa;"></i>
        <input type="text" id="auditSearch" placeholder="Search by office name or code&#8230;"
            style="width:100%;padding:7px 12px 7px 30px;border:1px solid #ddd;border-radius:4px;font-size:13px;"
            autocomplete="off">
    </div>
</div>

<div id="completeAuditsContainer">
@foreach($grouped as $monthYear => $branches)
    <div class="audit-month-group" data-month="{{ $monthYear }}">
        <h4 style="margin:0 0 12px 0;padding:6px 12px;background:#f4f4f4;border-left:4px solid #c0392b;border-radius:3px;font-size:14px;font-weight:600;color:#333;">
            <i class="fa fa-calendar" style="color:#c0392b;margin-right:6px;"></i>
            {{ $monthYear }}
            <span class="badge" style="background:#c0392b;margin-left:6px;font-size:11px;">{{ count($branches) }}</span>
        </h4>
        <div class="row" style="display:flex;flex-wrap:wrap;">
            @foreach($branches as $branch)
                @include('risk.partials.audit-branch-card', ['branch' => $branch, 'ratingConfig' => $ratingConfig, 'sectionShorts' => $sectionShorts])
            @endforeach
        </div>
    </div>
@endforeach
</div>

{{-- Full-Audit-Report shared modal --}}
<div class="modal fade" id="auditReportModal" tabindex="-1" role="dialog" aria-labelledby="auditReportModalLabel" aria-modal="true">
    <div class="modal-dialog" role="document" style="width:90%;max-width:1100px;">
        <div class="modal-content">
            <div class="modal-header" id="auditReportModalHeader" style="background:#555;color:#fff;border-radius:4px 4px 0 0;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1;font-size:22px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="auditReportModalLabel" style="margin:0;">
                    <i class="fa fa-file-text-o"></i>&nbsp; Full Audit Report
                </h4>
            </div>
            <div class="modal-body" id="auditReportModalContent" style="padding:20px;max-height:75vh;overflow-y:auto;">
                {{-- report body injected by JS --}}
            </div>
            <div class="modal-footer" style="background:#f9f9f9;border-top:1px solid #ddd;text-align:right;padding:10px 20px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- No-results message (hidden by default) --}}
<div id="noResultsMsg" class="alert alert-info" style="display:none;">
    <i class="fa fa-info-circle"></i> No audits match your search.
</div>

<script>
(function () {
    'use strict';

    var searchInput = document.getElementById('auditSearch');
    var container   = document.getElementById('completeAuditsContainer');
    var noResults   = document.getElementById('noResultsMsg');

    if (!searchInput || !container) return;

    searchInput.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();

        if (!q) {
            // Show everything, hide no-results
            container.querySelectorAll('.audit-month-group').forEach(function (g) { g.style.display = ''; });
            noResults.style.display = 'none';
            return;
        }

        var anyVisible = false;
        container.querySelectorAll('.audit-month-group').forEach(function (group) {
            var children = group.querySelectorAll('.col-md-6, .col-lg-4');
            var groupVisible = false;

            children.forEach(function (card) {
                var text = card.textContent.toLowerCase();
                var match = text.indexOf(q) !== -1;
                card.style.display = match ? '' : 'none';
                if (match) groupVisible = true;
            });

            group.style.display = groupVisible ? '' : 'none';
            if (groupVisible) anyVisible = true;
        });

        noResults.style.display = anyVisible ? 'none' : 'block';
    });

    /* ── Status icon helper ─────────────────────────────────────────── */
    function statusIcon(val) {
        if (!val || val === '' || val === 'na' || val === 'N/A') {
            return '<i class="fa fa-minus" style="color:#aaa;font-size:16px;"></i>';
        }
        var lo = String(val).toLowerCase();
        if (lo === 'pass') {
            return '<i class="fa fa-check" style="color:#27ae60;font-size:16px;"></i>';
        }
        if (lo === 'fail') {
            return '<i class="fa fa-times" style="color:#c0392b;font-size:16px;"></i>';
        }
        return '<i class="fa fa-minus" style="color:#aaa;font-size:16px;"></i>';
    }

    function statusLabelCls(val) {
        if (!val || String(val).toLowerCase() === 'pass') return 'color:#27ae60;font-weight:bold;';
        if (String(val).toLowerCase() === 'fail')  return 'color:#c0392b;font-weight:bold;';
        return 'color:#aaa;';
    }

    /* ── Build section table HTML ───────────────────────────────────── */
    function buildSectionTable(sname, items, sectionData) {
        var pass = (sectionData.pass || 0), fail = (sectionData.fail || 0), na = (sectionData.na || 0);
        var total = pass + fail + na;
        var failRatio = total > 0 ? fail / total : 0;
        var barColor  = failRatio === 0              ? '#27ae60'
                      : (failRatio <= 0.10           ? '#f39c12'
                      : (failRatio <= 0.25            ? '#e67e22'
                      : '#c0392b'));
        var pct       = total > 0 ? Math.round((pass / total) * 100) : 0;

        var rows = '';
        for (var i = 0; i < items.length; i++) {
            var field = items[i][0], label = items[i][1];
            var val   = sectionData[field] !== undefined ? sectionData[field] : '';
            var notes = sectionData[field + '_notes'] || '';
            rows += '<tr style="border-bottom:1px solid #f0f0f0;">'
                 + '<td style="padding:7px 10px;' + statusLabelCls(val) + '">' + label + '</td>'
                 + '<td style="padding:7px 10px;text-align:center;font-size:16px;min-width:40px;">' + statusIcon(val) + '</td>';
            if (notes) {
                rows += '<td colspan="3" style="padding:7px 10px;color:#666;font-style:italic;">'
                     + '<i class="fa fa-comment-o" style="color:#aaa;margin-right:4px;"></i>' + notes
                     + '</td>';
            } else {
                rows += '<td></td><td></td><td></td>';
            }
            rows += '</tr>';
        }

        return '<table style="width:100%;font-size:13px;border-collapse:collapse;margin-bottom:20px;border:1px solid #e0e0e0;border-radius:4px;overflow:hidden;">'
             + '<thead><tr style="background:#c0392b;color:#fff;">'
             + '<th style="padding:8px 12px;text-align:left;font-weight:600;font-size:14px;" colspan="2">'
             + '<i class="fa fa-list-ol"></i>&nbsp; Section ' + sname + '</th>'
             + '<th style="padding:8px 12px;text-align:center;font-size:14px;color:#2ecc71;">' + pass + '&nbsp;&#10003;</th>'
             + '<th style="padding:8px 12px;text-align:center;font-size:14px;color:#e74c3c;">' + fail  + '&nbsp;&#x2717;</th>'
             + '<th style="padding:8px 12px;text-align:center;font-size:14px;color:#aaa;">' + na + '&nbsp;N/A</th>'
             + '</tr></thead><tbody>' + rows + '</tbody></table>';
    }

    /* ── Helper: open full-audit modal (global so other scripts can call it) ── */
    window.openFullAuditReport = function (submissionId) {
        if (!submissionId) return;
        var modal   = document.getElementById('auditReportModal');
        var content = document.getElementById('auditReportModalContent');
        var header  = document.getElementById('auditReportModalHeader');
        var lbl     = document.getElementById('auditReportModalLabel');
        if (!modal || !content) return;

        lbl.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp; Loading…';
        content.innerHTML = '';
        if (header) header.style.background = '#555';

        fetch('/risk/audit-report/' + submissionId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (resp) { return resp.json(); })
        .then(function (data) {
            var headerStyle = 'background:' + (data.risk_color || '#333') + ';color:#fff;border-radius:4px 4px 0 0;padding:14px 20px;';

            var sectionTables = '';
            var sh            = data.section_shorts || {};
            var si            = data.section_items  || {};
            for (var s = 0; s < 9; s++) {
                if (!sh[s]) continue;
                var secData   = data.sections[s] || {};
                var sp        = secData.pass || 0;
                var sf        = secData.fail || 0;
                var sna       = secData.na   || 0;
                if ((sp + sf + sna) === 0 && s > 0 && s < 9) continue;
                sectionTables += buildSectionTable(sh[s], si[s] || [], secData);
            }

            var actionsHTML = '';
            if (data.key_findings || data.immediate_actions || data.recommendations) {
                actionsHTML = '<table style="width:100%;font-size:13px;border-collapse:collapse;margin-bottom:20px;border:1px solid #e0e0e0;border-radius:4px;overflow:hidden;">'
                    + '<thead><tr style="background:#333;color:#fff;"><th style="padding:8px 12px;text-align:left;font-weight:600;font-size:14px;">'
                    + '<i class="fa fa-lightbulb-o"></i>&nbsp; Key Findings &amp; Actions</th></tr></thead><tbody>';
                if (data.key_findings)       actionsHTML += '<tr style="border-bottom:1px solid #f0f0f0;"><td style="padding:10px 12px;"><strong style="color:#c0392b;">Key Findings</strong><br>' + data.key_findings + '</td></tr>';
                if (data.immediate_actions)   actionsHTML += '<tr style="border-bottom:1px solid #f0f0f0;"><td style="padding:10px 12px;"><strong style="color:#e67e22;">Immediate Actions</strong><br>' + data.immediate_actions + '</td></tr>';
                if (data.recommendations)     actionsHTML += '<tr style="border-bottom:1px solid #f0f0f0;"><td style="padding:10px 12px;"><strong style="color:#3498db;">Recommendations</strong><br>' + data.recommendations + '</td></tr>';
                actionsHTML += '</tbody></table>';
            }

            var remarksHTML = data.opening_remarks
                ? '<table style="width:100%;font-size:13px;border-collapse:collapse;margin-bottom:20px;border:1px solid #e0e0e0;border-radius:4px;overflow:hidden;">'
                  + '<thead><tr style="background:#7f8c8d;color:#fff;"><th style="padding:8px 12px;text-align:left;font-weight:600;font-size:14px;">'
                  + '<i class="fa fa-comment-o"></i>&nbsp; Opening Remarks</th></tr></thead><tbody>'
                  + '<tr><td style="padding:10px 12px;color:#555;font-style:italic;">' + data.opening_remarks + '</td></tr>'
                  + '</tbody></table>'
                : '';

            content.innerHTML =
                '<div style="margin-bottom:18px;display:flex;flex-wrap:wrap;gap:8px;align-items:center;">'
              + '<span class="label" style="font-size:13px;padding:5px 12px;background:' + (data.risk_color || '#666') + ';">Risk: ' + (data.risk_label || '') + '</span>'
              + '<span class="label label-default" style="font-size:13px;padding:5px 12px;"><i class="fa fa-times-circle" style="color:#c0392b;"></i>&nbsp; ' + data.fail_count + ' Fails</span>'
              + '<span class="label label-default" style="font-size:13px;padding:5px 12px;"><i class="fa fa-user-o"></i>&nbsp; ' + data.auditor + '</span>'
              + '<span class="label label-default" style="font-size:13px;padding:5px 12px;"><i class="fa fa-calendar"></i>&nbsp; ' + data.audit_date + '</span>'
              + '</div>'
              + sectionTables
              + actionsHTML
              + remarksHTML;

            lbl.innerHTML = '<i class="fa fa-file-text-o"></i>&nbsp; Full Audit Report';
            if (header) header.style.background = '';
            $(modal).modal('show');
        })
        .catch(function (err) {
            console.error('Failed to load report:', err);
            content.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Failed to load report. Please try again.</div>';
            lbl.innerHTML = 'Error';
            $(modal).modal('show');
        });
    };

    // Event delegation: catch clicks on any "View Full Report" button
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[id^="viewReportBtn-"]');
        if (!btn) return;
        e.preventDefault();
        var submissionId = btn.getAttribute('data-submission-id');
        if (window.openFullAuditReport) openFullAuditReport(submissionId);
    });
})();
</script>
