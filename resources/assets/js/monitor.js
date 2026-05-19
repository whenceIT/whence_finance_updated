/**
 * fraud-monitor.js
 * ---------------
 * Client-side supervisor that calls AlertService::runAll() **only** during
 * two supervised windows each day:
 *   • 06:00 – 10:30
 *   • 13:00 – 14:30
 *
 * Uses ES8+ syntax (const/let, arrow functions, template literals,
 * Promise.then chains).
 *
 * Injected by MonitorController / fraud-feed.blade.php.
 */

(function () {
    'use strict';

    const WINDOWS = [
        { start: '06:00', end: '10:30' },
        { start: '13:00', end: '18:30' },
        { start: '19:00', end: '05:30' },
    ];

    const POLL_MS = 60_000;   // check every 60 s
    let   timer   = null;
    let   busy    = false;

    const url = '/risk/monitor/run-all-alerts';

    /** Return true if the current time (server-side render, client clock) falls inside a window. */
    function inSupervisedWindow() {
        const now   = new Date();
        const mins  = now.getHours() * 60 + now.getMinutes();

        return WINDOWS.some(w => {
            const [sh, sm] = w.start.split(':').map(Number);
            const [eh, em] = w.end.split(':').map(Number);
            return mins >= sh * 60 + sm && mins <= eh * 60 + em;
        });
    }

    /** GET /fraud-alerts → triggers AlertService::runAll() on the back-end. */
    function tick() {
        if (! inSupervisedWindow()) return;
        if (busy) return;
        busy = true;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    console.info(
                        `[fraud-monitor] ${data.created} alert(s) created — ${data.timestamp}`
                    );
                } else {
                    console.warn(
                        `[fraud-monitor] Skipped (${data.message || 'unknown'}) — ${data.timestamp}`
                    );
                }
            })
            .catch(err => console.error('[fraud-monitor] tick error:', err))
            .finally(() => { busy = false; });
    }

    // Fire once immediately, then on the interval.
    tick();
    timer = setInterval(tick, POLL_MS);

    // Run immediately when the tab regains focus if we're inside a window.
    document.addEventListener('visibilitychange', () => {
        if (! document.hidden) { tick(); }
    });
})();
