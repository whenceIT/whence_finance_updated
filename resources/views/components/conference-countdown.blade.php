{{-- =====================================================================
     Annual Management Conference — Modal + Countdown Script
     Target: 15 October 2026

     The nav trigger buttons are placed inline in master.blade.php
     (desktop navbar and mobile header). This component provides:
       • The modal
       • The shared JS countdown that drives all timer spans
===================================================================== --}}

{{-- ── CONFERENCE MODAL ─────────────────────────────────────────────── --}}
<div class="modal fade" id="conferenceModal" tabindex="-1" role="dialog"
     aria-labelledby="conferenceModalTitle" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"
         style="max-width:680px;">
        <div class="modal-content"
             style="border-radius:16px; overflow:hidden; border:none;
                    box-shadow:0 24px 80px rgba(0,0,0,.35);">

            {{-- Header --}}
            <div class="modal-header"
                 style="background:linear-gradient(135deg,#0a0a2e 0%,#003d1f 60%,#1a6b00 100%);
                         border:none; padding:2rem 1.75rem 1.5rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;
                            border-radius:50%;background:rgba(255,200,0,.08);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-20px;left:10px;width:100px;height:100px;
                            border-radius:50%;background:rgba(0,160,74,.12);pointer-events:none;"></div>

                <div style="position:relative;z-index:1;width:100%;">
                    <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem;">
                        <span style="font-size:1.5rem;">🏆</span>
                        <span style="color:rgba(255,224,102,.9);font-size:.72rem;font-weight:700;
                                     letter-spacing:.14em;text-transform:uppercase;">
                            Annual Management Conference 2026
                        </span>
                    </div>
                    <h4 id="conferenceModalTitle"
                        style="color:#ffffff;font-size:1.3rem;font-weight:800;margin:0 0 .35rem;
                               line-height:1.3;text-shadow:0 1px 4px rgba(0,0,0,.4);">
                        LEADERSHIP BEYOND POSITION
                    </h4>
                    <p style="color:rgba(255,224,102,.85);font-size:.82rem;font-weight:600;
                               margin:0;letter-spacing:.04em;font-style:italic;">
                        "Accountability. Ownership. Performance."
                    </p>

                    {{-- Flip-tile countdown --}}
                    <div style="display:flex;gap:.55rem;margin-top:1.1rem;flex-wrap:wrap;">
                        @foreach(['confDays'=>'Days','confHours'=>'Hours','confMins'=>'Mins','confSecs'=>'Secs'] as $id=>$label)
                        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);
                                    border-radius:10px;padding:.5rem .85rem;text-align:center;min-width:56px;">
                            <div id="{{ $id }}"
                                 style="font-size:1.55rem;font-weight:800;color:#ffe066;line-height:1;">--</div>
                            <div style="font-size:.65rem;color:rgba(255,255,255,.7);font-weight:600;
                                        text-transform:uppercase;letter-spacing:.08em;">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="position:absolute;top:1rem;right:1.25rem;color:#fff;
                               opacity:.7;font-size:1.5rem;line-height:1;background:none;border:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body" style="padding:1.75rem;background:#fafafa;max-height:62vh;overflow-y:auto;">

                <div style="background:linear-gradient(135deg,#f0fdf4,#fefce8);border-left:4px solid #00a04a;
                             border-radius:0 10px 10px 0;padding:1rem 1.25rem;margin-bottom:1.25rem;">
                    <p style="margin:0;font-size:.9rem;color:#1a3a1a;font-weight:700;font-style:italic;">
                        "LEADERSHIP BEYOND POSITION: ACCOUNTABILITY, OWNERSHIP AND PERFORMANCE."
                    </p>
                </div>

                <p style="color:#374151;font-size:.875rem;line-height:1.7;margin-bottom:1rem;">
                    This theme isn't just a title — it's a call to action. It invites each of us to reflect on
                    <strong>how we lead, how we own our roles and how we hold ourselves accountable</strong>,
                    ultimately translating our positions into <strong>measurable performance</strong>.
                </p>
                <p style="color:#374151;font-size:.875rem;line-height:1.7;margin-bottom:1.25rem;">
                    Prepare mentally and professionally. Come ready not just to <em>listen</em>, but to
                    <strong>participate, challenge ideas, share experiences and drive solutions</strong>.
                </p>

                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;
                             padding:1rem 1.25rem;margin-bottom:1.1rem;">
                    <p style="margin:0 0 .5rem;font-weight:700;color:#1e293b;font-size:.88rem;">
                        🎤 A Different Kind of Conference
                    </p>
                    <p style="margin:0;color:#4b5563;font-size:.845rem;line-height:1.65;">
                        Expect <strong>open discussions, interactive sessions, round-table conversations,
                        brainstorming and practical management dialogues</strong>.
                        Your voice matters — this is your platform, not just a seat in the audience.
                    </p>
                    <ul style="margin:.75rem 0 0;padding-left:1.25rem;color:#374151;font-size:.845rem;line-height:1.9;">
                        <li>Bring the issues you believe we need to address.</li>
                        <li>Bring ideas that can make us better.</li>
                        <li>Come ready to hear perspectives different from your own.</li>
                    </ul>
                </div>

                <div style="background:#fff3cd;border:1px solid #fbbf24;border-radius:10px;
                             padding:.85rem 1.1rem;margin-bottom:1.1rem;">
                    <p style="margin:0;font-size:.845rem;color:#78350f;line-height:1.65;">
                        🎥 <strong>Live arrival interviews kick things off!</strong> From the moment you arrive,
                        be ready to share your thoughts, expectations and perspectives as we open the conference.
                    </p>
                </div>

                <div style="background:linear-gradient(135deg,#eff6ff,#ecfdf5);border-radius:10px;
                             padding:.85rem 1.1rem;border:1px solid #bfdbfe;">
                    <p style="margin:0;font-size:.845rem;color:#1e40af;line-height:1.65;font-weight:700;">
                        🎉 AND YES… WE WILL HAVE FUN!
                    </p>
                    <p style="margin:.4rem 0 0;font-size:.83rem;color:#374151;line-height:1.65;">
                        A water-packed day with all roads leading to <strong>Kafue! 🌊🔥</strong>
                        Bring your <em>competitive spirit, your energy and your sense of adventure</em>. 😄
                    </p>
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer"
                 style="border-top:1px solid #e5e7eb;padding:1rem 1.5rem;background:#f8fafc;
                         display:flex;justify-content:space-between;align-items:center;">
                <p style="margin:0;font-size:.78rem;color:#6b7280;font-style:italic;">
                    — Annual Management Conference Secretariat &nbsp;·&nbsp;
                    <strong style="color:#00a04a;">LEAD. OWN. DELIVER.</strong>
                </p>
                <button type="button" class="btn btn-success btn-sm" data-dismiss="modal"
                        style="border-radius:8px;font-weight:600;background:#00a04a;border-color:#00a04a;">
                    Got it! 🚀
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ── COUNTDOWN SCRIPT (shared by desktop + mobile buttons) ─────────── --}}
<script>
(function () {
    var target = new Date('2026-10-15T00:00:00').getTime();
    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        var diff = target - Date.now();

        if (diff <= 0) {
            ['confTimerDesktop','confTimerMobile'].forEach(function(id){
                var el = document.getElementById(id);
                if (el) el.textContent = '🎉 TODAY!';
            });
            ['confDays','confHours','confMins','confSecs'].forEach(function(id){
                var el = document.getElementById(id);
                if (el) el.textContent = '00';
            });
            return;
        }

        var days  = Math.floor(diff / 86400000);
        var hours = Math.floor((diff % 86400000) / 3600000);
        var mins  = Math.floor((diff % 3600000)  / 60000);
        var secs  = Math.floor((diff % 60000)    / 1000);

        var dEl = document.getElementById('confTimerDesktop');
        var mEl = document.getElementById('confTimerMobile');
        if (dEl) dEl.textContent = days + 'd ' + pad(hours) + 'h ' + pad(mins) + 'm ' + pad(secs) + 's';
        if (mEl) mEl.textContent = days + 'd ' + pad(hours) + 'h ' + pad(mins) + 'm';

        var tiles = {confDays: days, confHours: hours, confMins: mins, confSecs: secs};
        Object.keys(tiles).forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.textContent = pad(tiles[id]);
        });

        setTimeout(tick, 1000);
    }

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', tick)
        : tick();
})();
</script>
