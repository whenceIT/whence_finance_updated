/**
 * fraud-monitor.js
 * ---------------
 * Client-side supervisor that calls AlertService::runAll() **only** during
 * two supervised windows each day:
 *   • 06:00 – 10:30
 *   • 13:00 – 14:30
 *
 * ES8+ (const/let, arrow functions, template literals, Promise chains).
 */

(function () {
    'use strict';

    const WINDOWS = [
        { start: '06:00', end: '10:30' },
        { start: '13:00', end: '14:30' },
    ];

    const POLL_MS = 60_000;
    let   timer   = null;
    let   busy    = false;

    const url = '/risk/monitor/alert-service';

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
                'Content-Type' : 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
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
