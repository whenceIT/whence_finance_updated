@extends('layouts.master')

@section('title')
    Branch Deposit Audit
@endsection

@section('content')
<style>
    .da-type-card {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .da-type-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.14); }
    .da-type-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px;
        cursor: pointer;
        user-select: none;
        border-bottom: 1px solid transparent;
        transition: background .15s;
    }
    .da-type-header:hover { background: #f8f9ff; }
    .da-type-header .left { display: flex; align-items: center; gap: 12px; }
    .da-type-header .toggle-icon { font-size: 14px; color: #667eea; width: 18px; text-align: center; transition: transform .2s; }
    .da-type-card.open .da-type-header .toggle-icon { transform: rotate(90deg); }
    .da-type-header .type-name { font-weight: 700; font-size: 15px; color: #333; }
    .da-type-header .type-meta  { font-size: 13px; color: #888; margin-left: 8px; }
    .da-type-summary  { display: flex; gap: 20px; font-size: 13px; }
    .da-summary-item  { color: #555; }
    .da-summary-item strong { color: #667eea; }
    .right-group { display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
    .da-stats { display:flex; gap:12px; }
    .da-stat  { font-size:12px; color:#888; white-space:nowrap; }
    .da-stat strong { color:#667eea; font-size:13px; }
    .da-search-wrap { position:relative; }
    .da-search-wrap .fa-search { position:absolute; left:8px; top:50%; transform:translateY(-50%); color:#bbb; font-size:12px; pointer-events:none; }
    .da-office-search {
        padding:4px 10px 4px 26px; border:1px solid #ddd; border-radius:20px;
        font-size:12px; width:180px; outline:none; transition:border-color .2s, box-shadow .2s;
        background:#fff;
    }
    .da-office-search:focus { border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,0.15); }
    .da-body { display: none; padding: 16px 20px; border-top: 1px solid #f0f0f0; background: #fafbff; }
    .da-type-card.open .da-body { display: block; }
    .da-loading { color: #999; font-size: 14px; padding: 10px 20px; }
    .da-empty { color: #bbb; font-size: 13px; font-style: italic; padding: 8px 20px; }
    .da-office-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .da-office-table thead th {
        background: #667eea; color: #fff; padding: 7px 12px; font-size: 12px;
        font-weight: 600; text-transform: uppercase; letter-spacing: .5px; text-align: left;
    }
    .da-office-table tbody td {
        padding: 7px 12px; font-size: 13px; border-bottom: 1px solid #eef0f7; color: #444; vertical-align: middle;
    }
    .da-office-table tbody tr:last-child td { border-bottom: none; }
    .da-office-table tbody tr:hover td { background: #f0f4ff; }
    .da-office-table tbody td.da-amt { font-weight: 700; color: #333; }
    .da-office-table tbody tr.da-row-zero td {
        background: #fff5f5 !important;
        color: #c0392b;
    }
    .da-office-table tbody tr.da-row-zero td.da-amt { color: #e61700; }
    .da-office-table tbody tr.da-row-zero { animation: daPulse 2.5s ease-in-out infinite; }
    @keyframes daPulse {
        0%, 100% { box-shadow: inset 0 0 0 0 transparent; }
        50%       { box-shadow: inset 3px 0 0 0 #cf1a05; }
    }
    .da-office-table tbody tr.da-row-warn td {
        background: #fffbe6 !important;
        color: #b7950b;
    }
    .da-office-table tbody tr.da-row-warn { animation: daWarnPulse 2.5s ease-in-out infinite; }
    @keyframes daWarnPulse {
        0%, 100% { box-shadow: inset 0 0 0 0 transparent; }
        50%       { box-shadow: inset 3px 0 0 0 #f39c12; }
    }
    .da-month-grid { display: flex; gap: 3px; flex-wrap: nowrap; }
    .da-month-box {
        width: 22px; height: 22px; border-radius: 3px;
        font-size: 9px; font-weight: 700; line-height: 22px;
        text-align: center; display: inline-block;
        background: #f0f0f0; color: #bbb;
    }
    .da-month-box.has {
        background: #667eea; color: #fff;
    }
    .da-search-row {
        display: flex; align-items: center; gap: 6px;
        padding: 6px 8px; margin: 0 0 6px 0;
        background: #f7f8fc; border-radius: 5px; border: 1px solid #e0e4ed;
    }
    .da-search-row i { color: #aaa; font-size: 13px; }
    .da-search-row .da-office-search {
        border: none; background: transparent; outline: none;
        font-size: 13px; color: #444; width: 100%;
    }
    .da-search-row .da-office-search::placeholder { color: #bbb; }
    .da-filter-bar {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding: 10px 16px; margin-bottom: 18px;
        background: #f4f6fb; border-radius: 7px;
        border: 1px solid #dde3ef;
    }
    .da-filter-bar label { font-size: 13px; font-weight: 600; color: #555; white-space: nowrap; }
    .da-filter-bar select {
        font-size: 13px; padding: 5px 8px; border: 1px solid #c7cfdf;
        border-radius: 4px; background: #fff; color: #444; outline: none;
    }
    .da-filter-bar select:focus { border-color: #667eea; }
    .da-custom-row { display: flex; align-items: center; gap: 6px; }
    .page-chrome { background: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #667eea; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
    .page-chrome h1  { margin: 0; font-size: 20px; font-weight: 700; }
    .page-chrome p   { margin: 4px 0 0; font-size: 13px; color: #666; }
</style>

<div class="content-wrapper" style="margin: 20px;">

    <div class="page-chrome">
        <h1><i class="fa fa-history"></i> Branch Deposit Audit</h1>
        <p>Click a deposit type to expand and view all offices, including those with no deposits, for that type.</p>
    </div>

    <div class="da-filter-bar">
        <label><i class="fa fa-filter"></i> Period</label>

        <?php
            $currentPeriod = $period ?? 'month';
            $sel = fn($v) => $currentPeriod === $v ? ' selected' : '';
        ?>
        <select id="da-period">
            <option value="overall"       {{ $sel('overall') }}>Overall</option>
            <option value="month"         {{ $sel('month') }}>This Month</option>
            <option value="quarter"       {{ $sel('quarter') }}>This Quarter</option>
            <option value="year"          {{ $sel('year') }}>This Year</option>
            <option value="this_circle"   {{ $sel('this_circle') }}>This Circle</option>
            <option value="last_circle"   {{ $sel('last_circle') }}>Last Circle</option>
            <option value="last_quarter"  {{ $sel('last_quarter') }}>Last Quarter</option>
            <option value="last_month"    {{ $sel('last_month') }}>Last Month</option>
            <option value="last_year"     {{ $sel('last_year') }}>Last Year</option>
            <option value="custom"        {{ $sel('custom') }}>Custom…</option>
        </select>

        <div id="da-custom-row" class="da-custom-row" style="display:<?= $currentPeriod === 'custom' ? 'flex' : 'none' ?>">
            <select id="da-month">
                <?php
                    $curMonth = $customMonth ?? date('n');
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    foreach ($months as $i => $m) {
                        $v = $i + 1;
                        echo '<option value="' . $v . '"' . ($curMonth === $v ? ' selected' : '') . '>' . $m . '</option>';
                    }
                ?>
            </select>

            <select id="da-year">
                <?php
                    $curYear  = $customYear ?? date('Y');
                    $thisYear = date('Y');
                    for ($y = $thisYear + 1; $y >= $thisYear - 3; $y--) {
                        echo '<option value="' . $y . '"' . ($curYear === $y ? ' selected' : '') . '>' . $y . '</option>';
                    }
                ?>
            </select>
        </div>
    </div>

    <div id="daContainer">
        @foreach($types as $t)
        <div class="da-type-card" data-type-id="{{ $t['id'] }}">
            <div class="da-type-header">
                <div class="left">
                    <span class="toggle-icon"><i class="fa fa-caret-right"></i></span>
                    <span class="type-name">{{ $t['name'] }}</span>
                    <span class="type-meta">{{ $t['bank'] ?? '–' }} &nbsp;|&nbsp; GL: {{ $t['gl_account'] ?? '–' }}</span>
                </div>
                <div class="right-group">
                    <div class="da-stats">
                        <span class="da-stat" title="Total offices">
                            <i class="fa fa-building"></i> <strong>{{ $t['office_count'] }}</strong> offices
                        </span>
                        <span class="da-stat" title="Offices with deposits">
                            <i class="fa fa-check-circle" style="color:#27ae60"></i> <strong>{{ $t['offices_with_deposits'] }}</strong> with deposits
                        </span>
                        <span class="da-stat" title="Overall total amount across all offices">
                            <i class="fa fa-line-chart" style="color:#667eea"></i> <strong>${{ number_format((float)$t['total_amount'], 2) }}</strong> total
                        </span>
                    </div>
                </div>
            </div>
            <div class="da-body" id="da-body-{{ $t['id'] }}">
                <p class="da-loading"><i class="fa fa-spinner fa-spin"></i> Loading offices&hellip;</p>
            </div>
        </div>
        @endforeach
    </div>

</div>

<script>
(function(){

    var csrf = (function(){
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    })();

    function toCurrency(val) {
        if (!val) return '–';
        return parseFloat(val).toLocaleString('en-US', { style:'currency', currency:'USD' });
    }

    function fetchOffices(typeId, bodyEl) {
        bodyEl.innerHTML = '<p class="da-loading"><i class="fa fa-spinner fa-spin"></i> Loading offices&hellip;</p>';

        // Build query string from the URL (set by the da-period filter on every change)
        var qs = window.location.search;                   // e.g. "?period=custom&custom_month=2&custom_year=2026"
        var query = qs ? qs : '';

        // When period=overall the key is already in the URL; controller handles null=false
        fetch('/risk/branch-deposit-audit/type/' + typeId + query, {
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) { return r.json(); }).then(function(resp) {
            if (!resp.rows || resp.rows.length === 0) {
                bodyEl.innerHTML = '<p class="da-empty">No offices found.</p>';
                return;
            }

            resp.rows.sort(function(a, b) {
                var aNoDep  = a.deposit_count === 0;
                var bNoDep  = b.deposit_count === 0;
                if (aNoDep !== bNoDep) return aNoDep ? 1 : -1;
                var aZeroT = !a.total || a.total === 0;
                var bZeroT = !b.total || b.total === 0;
                if (aZeroT !== bZeroT) return aZeroT ? 1 : -1;
                return (b.total || 0) - (a.total || 0);
            });

            var html = '<div class="da-search-row"><i class="fa fa-search"></i><input type="text" class="da-office-search" placeholder="Filter offices&hellip;" autocomplete="off" spellcheck="false"></div>'
                     + '<table class="da-office-table"><thead>'
                     + '<tr><th>#</th><th>Office</th><th>Deposits</th><th class="da-amt">Total Amount</th><th>Months</th></tr>'
                     + '</thead><tbody>';

            resp.rows.forEach(function(row) {
                var cls = '';
                if (row.deposit_count === 0) {
                    cls = ' da-row-zero';
                } else if (!row.total || row.total === 0) {
                    cls = ' da-row-warn';
                }

                // Build 12 month boxes: Jan..Dec
                var mNames = ['J','F','M','A','M','J','J','A','S','O','N','D'];
                var mBoxes = '';
                for (var i = 0; i < 12; i++) {
                    var cnt = (row.months && row.months[i]) || 0;
                    mBoxes += '<span class="da-month-box' + (cnt > 0 ? ' has' : '') + '" title="' + mNames[i] + ': ' + cnt + ' deposit(s)">' + mNames[i] + '</span>';
                }

                html += '<tr class="' + cls + '">'
                      + '<td>' + (row.deposit_count > 0 ? '<span class="da-badge da-badge-success">' + row.deposit_count + '</span>' : '—') + '</td>'
                      + '<td>' + row.office_name + '</td>'
                      + '<td>' + (row.deposit_count > 0 ? row.deposit_count + ' deposit(s)' : '<em>No deposits</em>') + '</td>'
                      + '<td class="da-amt">' + (row.deposit_count > 0 ? toCurrency(row.total) : '—') + '</td>'
                      + '<td><div class="da-month-grid">' + mBoxes + '</div></td>'
                      + '</tr>';
            });

            html += '</tbody></table>';
            bodyEl.innerHTML = html;
            bodyEl.dataset.loaded = 'true';
        }).catch(function() {
            bodyEl.innerHTML = '<p class="da-empty">Error loading data. Try again.</p>';
        });
    }

    document.querySelectorAll('.da-type-header').forEach(function(header) {
        header.addEventListener('click', function() {
            var card   = header.closest('.da-type-card');
            if (!card) return;
            var typeId = card.getAttribute('data-type-id');
            var body   = document.getElementById('da-body-' + typeId);

            if (!card.classList.contains('open') && body && !body.dataset.loaded) {
                fetchOffices(typeId, body);
            }
            card.classList.toggle('open');
        });
    });

    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('da-office-search')) return;
        var input  = e.target;
        var body   = input.closest('.da-body');
        var table  = body ? body.querySelector('.da-office-table') : null;
        if (!table) return;

        var term = input.value.trim().toLowerCase();
        var rows = table.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var txt = row.textContent.toLowerCase();
            row.style.display = (term === '' || txt.indexOf(term) !== -1) ? '' : 'none';
        });
    });

    // Period filter — wired via addEventListener so the handler is in the same scope
    (function() {
        var period  = document.getElementById('da-period');
        if (!period) return;

        period.addEventListener('change', function() {
            var value   = period.value;
            var customR = document.getElementById('da-custom-row');

            // Invalidate every expanded card so they re-fetch for the new period
            document.querySelectorAll('.da-body[data-loaded]').forEach(function(body) {
                body.dataset.loaded = '';
            });

            if (value === 'custom') {
                var month = document.getElementById('da-month').value;
                var year  = document.getElementById('da-year').value;
                customR.style.display = 'flex';
                window.location.href = '?period=custom&custom_month=' + month + '&custom_year=' + year;
            } else {
                customR.style.display = 'none';
                window.location.href = '?period=' + value;
            }
        });

        // Init on page load — show/hide custom row based on current selection
        var customR = document.getElementById('da-custom-row');
        if (customR) {
            customR.style.display = period.value === 'custom' ? 'flex' : 'none';
        }
    })();

})();
</script>

@endsection