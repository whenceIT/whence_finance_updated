@php
    // Group audits by Month-Year (derived from audit_date 'd M Y')
    $grouped = [];
    foreach ($completeAudits as $branch) {
        // Reconstruct a parseable date from 'd M Y' (e.g. "15 May 2026")
        $dt = \Carbon\Carbon::createFromFormat('d M Y', $branch['audit_date']);
        $key = $dt ? $dt->format('F Y') : 'Unknown';
        if (!isset($grouped[$key])) $grouped[$key] = [];
        $grouped[$key][] = $branch;
    }
    // Sort groups newest first
    krsort($grouped);
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
        <div class="row">
            @foreach($branches as $branch)
                @include('risk.partials.audit-branch-card', ['branch' => $branch, 'ratingConfig' => $ratingConfig, 'sectionShorts' => $sectionShorts])
            @endforeach
        </div>
    </div>
@endforeach
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
})();
</script>
