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
    .page-chrome #openOfficeDebtModal { margin-top: 10px; }

    /* Modal-fullscreen override */
    .od-dialog {
        width: 96vw;
        height: 92vh;
        margin: 3vh auto;
        padding: 0;
    }
    .od-dialog .modal-content {
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    .od-dialog .modal-header { background: #2c3e50; color: #fff; padding: 16px 24px; }
    .od-dialog .modal-header h4 { margin: 0; font-size: 16px; font-weight: 700; }
    .od-dialog .modal-header .close { color: #fff; opacity: .8; font-size: 26px; margin-top: -4px; }

    /* Debt table */
    #odTable { font-size: 13px; }
    #odTable thead th { background: #667eea; color: #fff; }
    #odTable tbody td { vertical-align: middle; }
    .od-debt-row td { border-top: 1px solid #eee; }

    /* Status pill badge styling */
    .od-status-pill {
        display: inline-block; padding: 2px 10px; border-radius: 12px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    }
    .od-status-pill.owing   { background: #fdecea; color: #c0392b; }
    .od-status-pill.partial { background: #fff8e1; color: #f39c12; }
    .od-status-pill.paid    { background: #eafaf1; color: #27ae60; }

    /* Form inside modal.body */
    .od-form-row { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; padding: 14px 16px; background: #f7f9fc; border: 1px solid #dde3ef; border-radius: 6px; }
    .od-form-group { display: flex; flex-direction: column; gap: 4px; flex: 1 1 180px; }
    .od-form-group label { font-size: 12px; font-weight: 600; color: #555; }
    .od-form-group input, .od-form-group select, .od-form-group textarea { padding: 7px 10px; border: 1px solid #c7cfdf; border-radius: 4px; font-size: 13px; outline: none; }
    .od-form-group input:focus, .od-form-group select:focus, .od-form-group textarea:focus { border-color: #667eea; }

    /* Action cell */
    .od-actions { display: flex; gap: 4px; }
    .od-btn { padding: 3px 8px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; }
    .od-btn-edit  { background: #fff3cd; color: #856404; }
    .od-btn-del   { background: #fdecea; color: #c0392b; }
    .od-btn-save  { background: #667eea; color: #fff; }
    .od-btn-cancel{ background: #eee; color: #555; }

    /* Shimmer */
    #odShimmer { display: none; padding: 20px 24px; }
    @keyframes shimmer-anim {
        0%   { background-position: -400px 0; }
        100% { background-position:  400px 0; }
    }

</style>

<div class="content-wrapper" style="margin: 20px;">

    <div class="page-chrome">
        <h1><i class="fa fa-history"></i> Branch Deposit Audit</h1>
        <p>Click a deposit type to expand and view all offices, including those with no deposits, for that type.</p>
        <a href="#odModal" class="btn btn-primary btn-sm" style="border-radius:6px;text-decoration:none;color:#fff;">
            <i class="fa fa-balance-scale"></i> Office Debt Management
        </a>
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
        return parseFloat(val).toLocaleString('en-US', { style:'currency', currency:'ZMW' });
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

    // ── OfficeDebt Management ──────────────────────────────────────────────────
    (function() {
        // Markers
        var $modal    = $('#odModal');
        var $shimmer  = $('#odShimmer');
        var $tableBody= $('#odTableBody');
        var $formBar  = $('#odFormBar');
        var $empty    = $('#odEmpty');
        var editId    = function() { return $('#odEditId').val(); };

        // Open modal → load data (delegated: survives second jQuery load in master layout)
        $(document).on('click', '#openOfficeDebtModal', function() {
            odResetForm();
            odLoadTable();
            $modal.modal('show');
        });

        function odResetForm() {
            $('#odInputOffice').val('');
            $('#odInputStatus').val('owing');
            $('#odInputOriginal').val('');
            $('#odInputOutstanding').val('');
            $('#odInputNotes').val('');
            $('#odEditId').val('');
            $formBar.hide();
        }

        function odShowForm() {
            $formBar[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            $formBar.show();
        }

        // ── Load table ──
        function odLoadTable() {
            $tableBody.empty();
            $empty.hide();
            $shimmer.show();

            $.get('{{ route("risk.office-debts.list") }}', function(resp) {
                $shimmer.hide();

                if (!resp || resp.length === 0) {
                    $empty.show();
                    return;
                }

                resp.forEach(function(row) {
                    $tableBody.append(odRowHtml(row));
                });
            }).fail(function() {
                $shimmer.hide();
                $tableBody.html('<tr><td colspan="6" style="padding:20px;text-align:center;color:#c0392b;">Error loading debt records. Try again.</td></tr>');
            });
        }

        // ── Row HTML ──
        function odRowHtml(row) {
            var amountClass = function() {
                if (row.outstanding_amount <= 0) return 'paid';
                if (row.outstanding_amount < row.original_amount) return 'partial';
                return 'owing';
            };
            var cls = amountClass();
            var balance = row.outstanding_amount <= 0
                ? '—'
                : (function() {
                    try { return parseFloat(row.outstanding_amount).toLocaleString('en-US', { style: 'currency', currency: 'USD' }); }
                    catch(e) { return row.outstanding_amount; }
                  })();
            var original = (function() {
                try { return parseFloat(row.original_amount).toLocaleString('en-US', { style: 'currency', currency: 'USD' }); }
                catch(e) { return row.original_amount; }
            })();

            return '<tr class="od-debt-row" data-id="' + row.id + '">'
                 + '<td>' + (row.office_name || '—') + '</td>'
                 + '<td><span class="od-status-pill ' + cls + '">' + row.debt_status + '</span></td>'
                 + '<td>' + original + '</td>'
                 + '<td style="font-weight:700;color:' + (cls === 'owing' ? '#c0392b' : (cls === 'partial' ? '#f39c12' : '#27ae60')) + ';">' + balance + '</td>'
                 + '<td style="color:#777;font-size:12px;">' + (row.notes || '') + '</td>'
                 + '<td class="od-actions">'
                 + '<button class="od-btn od-btn-edit"  title="Edit"   onclick="odEdit(' + row.id + ')"><i class="fa fa-pencil"></i></button> '
                 + '<button class="od-btn od-btn-del"   title="Delete" onclick="odDel(' + row.id + ')"><i class="fa fa-trash"></i></button>'
                 + '</td>'
                 + '</tr>';
        }

        // ── New Record ──
        $(document).on('click', '#odBtnNewRow', function() {
            odResetForm();
            odShowForm();
        });

        // ── Cancel new/edit ──
        $(document).on('click', '#odBtnCancelForm', odResetForm);

        // ── Edit row ──
        window.odEdit = function(id) {
            $.get('{{ route("risk.office-debts.list") }}', function(resp) {
                var row = resp.find(function(r) { return r.id === id; });
                if (!row) return;

                $('#odEditId').val(id);
                $('#odInputOffice').val(row.office_id);
                $('#odInputStatus').val(row.debt_status);
                $('#odInputOriginal').val(row.original_amount);
                $('#odInputOutstanding').val(row.outstanding_amount);
                $('#odInputNotes').val(row.notes);
                odShowForm();

                // Scroll form into view
                $tableBody.find('tr[data-id="' + id + '"]')[0] && $tableBody.find('tr[data-id="' + id + '"]')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        };

        // ── Delete row (soft-clear → hard-delete after double-confirm) ──
        window.odDel = function(id) {
            if (!confirm('Remove this debt record? The branch will no longer appear as carrying debt.')) return;
            $.ajax({
                url: '{{ route("risk.office-debts.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
            }).done(function(r) {
                if (r.success) odLoadTable();
                else alert(r.message || 'Unable to delete record.');
            }).fail(function() {
                alert('Network error. Try again.');
            });
        };

        // ── Save (create / update) ──
        $(document).on('click', '#odBtnSaveForm', function() {
            var officeId    = $('#odInputOffice').val();
            var status      = $('#odInputStatus').val();
            var original    = $('#odInputOriginal').val();
            var outstanding = $('#odInputOutstanding').val();
            var notes       = $('#odInputNotes').val();

            if (!officeId || !original || outstanding === '') {
                alert('Please fill in Branch, Original Amount and Outstanding Amount.');
                return;
            }

            var id      = editId();
            var url     = id
                ? '{{ route("risk.office-debts.update", ["id" => "__ID__"]) }}'.replace('__ID__', id)
                : '{{ route("risk.office-debts.store") }}';
            var type    = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: type,
                data: {
                    _token:            '{{ csrf_token() }}',
                    office_id:         officeId,
                    debt_status:       status,
                    original_amount:   original,
                    outstanding_amount:outstanding,
                    notes:             notes,
                },
            }).done(function(r) {
                if (r.success) {
                    odResetForm();
                    odLoadTable();
                } else {
                    alert(r.message || 'Save failed.');
                }
            }).fail(function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Save failed.';
                alert(msg);
            });
        });

    })();

})();
</script>

<!-- ── Office Debt Management Modal ─────────────────────────────────────────── -->
<div class="modal fade" id="odModal" tabindex="-1" role="dialog" aria-labelledby="odModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog od-dialog modal-fullscreen" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="odModalLabel">
                    <i class="fa fa-balance-scale"></i> Office Debt Management
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="overflow-y:auto;padding:20px 24px;">

                <!-- Shimmer loading state -->
                <div id="odShimmer">
                    <div style="animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;height:14px;margin-bottom:14px;border-radius:4px;"></div>
                    @php $shimmer = 8; @endphp
                    @for($i = 0; $i < $shimmer; $i++)
                    <div style="display:flex;gap:10px;margin-bottom:8px;">
                        <div style="width:22%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                        <div style="width:18%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                        <div style="width:14%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                        <div style="width:10%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                        <div style="width:10%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                        <div style="width:16%;height:36px;animation:shimmer 1.5s infinite;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;border-radius:4px;"></div>
                    </div>
                    @endfor
                </div>

                <!-- Add / Edit form bar -->
                <div id="odFormBar" style="display:none;margin-bottom:16px;">
                    <div class="od-form-row">
                        <div class="od-form-group">
                            <label>Branch</label>
                            <select id="odInputOffice">
                                <option value="">Select a branch…</option>
                                <?php
                                    $offices = \App\Models\Office::orderBy('name')->get();
                                    foreach ($offices as $o) {
                                        echo '<option value="' . $o->id . '">' . htmlspecialchars($o->name) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="od-form-group">
                            <label>Status</label>
                            <select id="odInputStatus">
                                <option value="owing">Owing</option>
                                <option value="partial">Partially Paid</option>
                                <option value="paid">Cleared</option>
                            </select>
                        </div>
                        <div class="od-form-group">
                            <label>Original Debt</label>
                            <input type="number" id="odInputOriginal" min="0" step="0.01">
                        </div>
                        <div class="od-form-group">
                            <label>Outstanding</label>
                            <input type="number" id="odInputOutstanding" min="0" step="0.01">
                        </div>
                        <div class="od-form-group" style="flex:1 1 100%;">
                            <label>Notes</label>
                            <input type="text" id="odInputNotes" placeholder="Optional notes…">
                        </div>
                        <div class="od-form-group" style="justify-content:flex-end;flex-direction:row;gap:6px;">
                            <button class="od-btn od-btn-save"       id="odBtnSaveForm"    >Save</button>
                            <button class="od-btn od-btn-cancel"     id="odBtnCancelForm"  >Cancel</button>
                        </div>
                    </div>
                    <input type="hidden" id="odEditId" value="">
                </div>

                <!-- Header row -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <h4 style="margin:0;font-size:14px;font-weight:700;color:#333;">Branch Debt Records</h4>
                    <button class="od-btn od-btn-save" id="odBtnNewRow" style="padding:6px 14px;font-size:13px;font-weight:600;">
                        <i class="fa fa-plus"></i> New Record
                    </button>
                </div>

                <!-- Data table -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="odTable">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Original (ZMW)</th>
                                <th>Outstanding (ZMW)</th>
                                <th>Notes</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="odTableBody"></tbody>
                    </table>
                </div>

                <p id="odEmpty" style="display:none;text-align:center;padding:30px;color:#bbb;">
                    <i class="fa fa-check-circle" style="font-size:28px;color:#27ae60;margin-bottom:10px;"></i><br>
                    No branches currently carry an outstanding debt.
                </p>

            </div><!-- /.modal-body -->

        </div>
    </div>
</div>

@endsection