@extends('layouts.master')

@section('title')
    Real-Time Fraud Alert Feed
@endsection

@push('styles')
<style>
    .ff-severity-critical { border-left: 4px solid #c0392b; }
    .ff-severity-warning  { border-left: 4px solid #f39c12; }
    .ff-severity-info     { border-left: 4px solid #3498db; }
    .ff-badge-critical { background:#c0392b; }
    .ff-badge-warning  { background:#f39c12; }
    .ff-badge-info     { background:#3498db; }

    .ff-alert-card {
        border-radius:6px;
        padding:12px 14px;
        margin-bottom:10px;
        background:#fff;
        box-shadow:0 1px 3px rgba(0,0,0,.08);
        transition:background .2s, box-shadow .2s;
        animation: ffSlideIn .3s ease-out;
    }
    .ff-alert-card.is-read {
        opacity:.72;
    }
    .ff-alert-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.14); }

    .ff-alert-card.unread {
        background:#fffdfd;
    }

    @keyframes ffSlideIn {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .ff-pulse {
        display:inline-block;
        width:8px;height:8px;
        border-radius:50%;
        margin-right:6px;
        animation: ffPulse 1.4s ease-in-out infinite;
    }
    @keyframes ffPulse {
        0%,100% { opacity:1; }
        50%      { opacity:.3; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">

        <!-- Header card -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-shield"></i>&nbsp; Real-Time Fraud Alert Feed</h3>
                <div class="box-tools pull-right">
                    <button id="ff-refresh-btn" class="btn btn-success btn-sm" onclick="ffRefreshNow()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="box-body">

                <!-- Toolbar -->
                <div class="row" style="margin-bottom:16px;">
                    <div class="col-md-12">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">

                            <!-- Severity filter -->
                            <select id="ff-severity" class="form-control input-sm" style="width:auto;min-width:140px;"
                                    onchange="ffApplyFilters()">
                                <option value="">All Severities</option>
                                <option value="critical">🔴 Critical</option>
                                <option value="warning">🟡 Warning</option>
                                <option value="info">🔵 Info</option>
                            </select>

                            <!-- Unread toggle -->
                            <label style="font-size:13px;display:flex;align-items:center;gap:4px;cursor:pointer;">
                                <input type="checkbox" id="ff-unread-only" onchange="ffApplyFilters()"> Unread only
                            </label>

                            <!-- Hours window -->
                            <select id="ff-hours" class="form-control input-sm" style="width:auto;min-width:130px;"
                                    onchange="ffApplyFilters()">
                                <option value="24">Last 24 hours</option>
                                <option value="48">Last 48 hours</option>
                                <option value="168" selected>Last 7 days</option>
                                <option value="720">Last 30 days</option>
                            </select>

                            <span class="text-muted" style="font-size:12px;margin-left:auto;">
                                <span id="ff-tick-lbl">● Live</span>
                                &nbsp;|&nbsp;
                                <span id="ff-count">—</span> alerts
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Alert list container -->
                <div id="ff-alert-list" style="max-height:520px;overflow-y:auto;">
                    <div style="text-align:center;padding:40px;color:#aaa;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top:10px;">Loading alerts…</p>
                    </div>
                </div>

                <!-- Empty state -->
                <div id="ff-empty" style="text-align:center;padding:30px;color:#999;display:none;">
                    <i class="fa fa-check-circle fa-2x" style="color:#27ae60;"></i>
                    <p style="margin-top:10px;">No alerts match the current filters.</p>
                </div>

            </div>

            <!-- Live footer -->
            <div class="box-footer" style="background:#f9f9f9;font-size:11px;color:#999;display:flex;justify-content:space-between;align-items:center;">
                <span>
                    <span class="ff-pulse" style="background:#27ae60;"></span>
                    Supervisor polling every 15 s · Automated rule engine · Last scan: <span id="ff-last-scan">—</span>
                </span>
                <span>
                    <i class="fa fa-info-circle"></i> Critical &amp; Warning alerts require supervisor review
                </span>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    const WINDOWS = [
        { start: '06:00', end: '10:30' },
        { start: '13:00', end: '16:50' },
    ];

    const POLL_MS = 60_000;
    let   timer   = null;
    let   busy    = false;

    const url = '{{ route("risk.alert-service") }}';

    function inSupervisedWindow() {
        const now  = new Date();
        const mins = now.getHours() * 60 + now.getMinutes();

        return WINDOWS.some(w => {
            const [sh, sm] = w.start.split(':').map(Number);
            const [eh, em] = w.end.split(':').map(Number);
            return mins >= sh * 60 + sm && mins <= eh * 60 + em;
        });
    }

    function tick() {
        if (! inSupervisedWindow()) return;
        if (busy) return;
        busy = true;

        const now = new Date();
        console.info(
            `[monitor] → fetch  url=${url}  method=POST  hour=${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`
        );

        fetch(url, {
            method : 'POST',
            headers: {
                'Content-Type'     : 'application/json',
                'X-Requested-With' : 'XMLHttpRequest',
                'X-CSRF-TOKEN'     : '{{ csrf_token() }}',
            },
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    console.info(
                        `[monitor] ← ${data.created} alert(s) created  window=${data.inWindow}  serverHour=${data.serverHour}  ts=${data.timestamp}`
                    );
                } else {
                    console.warn(
                        `[monitor] ← blocked: ${data.message || 'unknown'}  serverHour=${data.serverHour}`
                    );
                }
            })
            .catch(err => console.error('[monitor] fetch error:', err))
            .finally(() => { busy = false; });
    }

    tick();
    timer = setInterval(tick, POLL_MS);

    document.addEventListener('visibilitychange', () => {
        if (! document.hidden) tick();
    });
})();
</script>



<script>
(function () {
    'use strict';

    var POLL_MS   = 15_000;   // 15-second supervisor cycle
    var lastHash  = '';
    var timer     = null;
    var busy      = false;

    // ── Render alert HTML ───────────────────────────────────────────────────
    function renderCard(a) {
        return '<div class="ff-alert-card ff-severity-' + a.severity + (a.is_read ? ' is-read' : ' unread') + '"' +
               ' data-id="' + a.id + '">' +
               '  <div style="display:flex;align-items:flex-start;gap:8px;">' +
               '    <div style="font-size:18pt;flex-shrink:0;line-height:1;">' +
                           ffIcon(a.severity) + '</div>' +
               '    <div style="flex:1;min-width:0;">' +
               '      <div style="font-weight:700;color:#333;font-size:13px;margin-bottom:2px;">' + escHtml(a.title) + '</div>' +
               '      <div style="font-size:12px;color:#555;line-height:1.5;">' + escHtml(a.description) + '</div>' +
               '    </div>' +
               '    <div style="flex-shrink:0;text-align:right;">' +
               '      <span class="label ff-badge-' + a.severity + '" style="color:#fff;font-size:10px;padding:2px 7px;border-radius:3px;">' +
                           a.severity.toUpperCase() + '</span>' +
               '      <div style="font-size:10px;color:#aaa;margin-top:2px;">' + a.created_at + '</div>' +
               '    </div>' +
               '  </div>' +
               '</div>';
    }

    function ffIcon(sev) {
        if (sev === 'critical') return '<span style="color:#c0392b;">🚨</span>';
        if (sev === 'warning')  return '<span style="color:#f39c12;">⚠️</span>';
        return '<span style="color:#3498db;">ℹ️</span>';
    }

    function escHtml(s) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
    }

    // ── Fetch alerts from backend ───────────────────────────────────────────
    function fetchAlerts(cb) {
        var sevEl    = document.getElementById('ff-severity');
        var unreadEl = document.getElementById('ff-unread-only');
        var hoursEl  = document.getElementById('ff-hours');
        if (!sevEl || !unreadEl || !hoursEl) { cb(new Error('DOM not ready')); return; }

        var sev    = sevEl.value || '';
        var unread = !!unreadEl.checked;
        var hours  = hoursEl.value || '168';

        var url = '{{ route("risk.fraud-alerts") }}' +
                  '?severity='  + encodeURIComponent(sev) +
                  '&unread='    + (unread ? 1 : 0) +
                  '&hours='     + encodeURIComponent(hours);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) { cb(null, data); })
            .catch(function (e) { cb(e); });
    }

    // ── Build alert list ─────────────────────────────────────────────────────
    function buildList(data) {
        if (!data.alerts || data.alerts.length === 0) {
            return '<div style="text-align:center;padding:30px;color:#999;">' +
                   '<i class="fa fa-check-circle" style="color:#27ae60;font-size:24px;"></i>' +
                   '<p style="margin-top:8px;">No alerts found.</p></div>';
        }

        var html = '';
        for (var i = 0; i < data.alerts.length; i++) {
            html += renderCard(data.alerts[i]);
        }
        return html;
    }

    // ── Supervisor tick ──────────────────────────────────────────────────────
    function supervisorTick() {
        // Guard: stop if any filter element is missing (e.g. before DOM ready)
        var sevEl    = document.getElementById('ff-severity');
        var unreadEl = document.getElementById('ff-unread-only');
        var hoursEl  = document.getElementById('ff-hours');
        if (!sevEl || !unreadEl || !hoursEl) return;

        if (busy) return;
        busy = true;

        var sev    = sevEl.value || '';
        var unread = !!unreadEl.checked;
        var hours  = hoursEl.value || '168';
        var url   = '{{ route("risk.fraud-alerts") }}' +
                    '?severity=' + encodeURIComponent(sev) +
                    '&unread='   + (unread ? 1 : 0) +
                    '&hours='    + encodeURIComponent(hours);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var hash = JSON.stringify(data.alerts);
                if (hash !== lastHash) {
                    lastHash = hash;
                    var listEl = document.getElementById('ff-alert-list');
                    listEl.innerHTML = buildList(data);
                }
                document.getElementById('ff-count').textContent    = data.total || 0;
                document.getElementById('ff-last-scan').textContent = new Date().toLocaleTimeString();
                document.getElementById('ff-empty').style.display   = (!data.total) ? '' : 'none';
                document.getElementById('ff-alert-list').style.display = data.total ? '' : 'none';
            })
            .catch(function (e) { console.error('Fraud feed error:', e); })
            .finally(function () { busy = false; });
    }

    // ── Manual refresh ───────────────────────────────────────────────────────
    window.ffRefreshNow = function () {
        var btn = document.getElementById('ff-refresh-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Refreshing…';

        supervisorTick();

        setTimeout(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-refresh"></i> Refresh';
        }, 1_200);
    };

    // ── Apply filters and reset hash ─────────────────────────────────────────
    window.ffApplyFilters = function () {
        lastHash = '';
        supervisorTick();
    };

    // ── Start supervisor on DOM ready ───────────────────────────────────────
    supervisorTick();
    timer = setInterval(supervisorTick, POLL_MS);
})();
</script>
@endsection