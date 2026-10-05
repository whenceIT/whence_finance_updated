{{-- ============================================================================
     Deposit Deadline Widget
     Shows the nearest upcoming deadline. When it expires the widget
     automatically advances to the next one in the sorted queue.

     "Debt Setup Cost" is only shown to offices that still owe a balance.

     Passed variable: $deadlines — Collection of Deadline models ordered
     by countdown_date asc (done by the parent view).
============================================================================ --}}

@php
    $currentUser        = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
    $currentUserOffice  = $currentUser ? (int) $currentUser->office_id : null;

    // Resolve once — avoid calling officesWithBalance() inside the loop
    $debtBalanceOffices = \App\Models\SetupDebtCost::officesWithBalance();

    // Deposit payment status for the current month — used to hide deadlines
    // the user has already fully paid. Called once, not per-deadline.
    $ddlDepositStatus = null;
    if ($currentUserOffice) {
        try {
            $ddlDepositStatus = \App\Models\Deposit::depositStatusByMonth(
                $currentUserOffice,
                now()->format('n-Y')   // e.g. "10-2026"
            );
        } catch (\Throwable $e) {
            $ddlDepositStatus = null;
        }
    }

    // Map deadline name → status array index
    // Index 0 = Building (type 3), 1 = Administration (type 1), 2 = Statutory (type 5)
    $ddlNameToStatusIndex = [
        'Building & Infrastructure fee deposits' => 0,
        'Administration Department fee deposit'  => 1,
        'Statutory payments deposits'            => 2,
    ];

    $upcoming = isset($deadlines)
        ? $deadlines->filter(function($d) use (
            $currentUserOffice,
            $debtBalanceOffices,
            $ddlDepositStatus,
            $ddlNameToStatusIndex
        ) {
            if (!$d->countdown_date || !$d->countdown_date->isFuture()) {
                return false;
            }

            // "Debt Setup Cost" — only show to offices that still owe a balance
            if ($d->name === 'Debt Setup Cost') {
                return $currentUserOffice && in_array($currentUserOffice, $debtBalanceOffices);
            }

            // Building / Administration / Statutory — hide if the office has
            // already fully paid for the current month
            if (isset($ddlNameToStatusIndex[$d->name]) && $ddlDepositStatus !== null) {
                $idx = $ddlNameToStatusIndex[$d->name];
                $entry = $ddlDepositStatus[$idx] ?? null;
                if ($entry && ($entry['status'] ?? '') === 'fully paid') {
                    return false;
                }
            }

            return true;
        })->sortBy('created_at')->values()
        : collect();
@endphp

@if($upcoming->isNotEmpty())

{{-- Single widget — JS drives which deadline is displayed --}}
<div id="depositDeadlineWidget" style="display:none;">
    <div class="deadline-widget" id="deadlineWidgetInner">

        <div class="widget-header">
            <i class="fa fa-clock-o"></i>
            <span id="ddl-name" style="flex:1;margin:0 6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <button class="widget-close" id="ddlCloseBtn" aria-label="Dismiss">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="widget-body">
            <div class="countdown-display">
                <div class="countdown-item">
                    <span class="countdown-number" id="ddl-days">--</span>
                    <span class="countdown-text">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="ddl-hours">--</span>
                    <span class="countdown-text">Hrs</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="ddl-mins">--</span>
                    <span class="countdown-text">Min</span>
                </div>
            </div>

            <p class="widget-message">
                Due <strong id="ddl-due-date"></strong>
                <span id="ddl-queue-badge" style="
                    display:none;
                    float:right;
                    background:rgba(0,0,0,.25);
                    border-radius:10px;
                    padding:1px 7px;
                    font-size:10px;
                    font-weight:700;
                "></span>
            </p>
        </div>

    </div>
</div>

<style>
    #depositDeadlineWidget {
        position: fixed;
        bottom: 15px;
        left: 15px;
        z-index: 9999;
        animation: ddlSlideIn 0.5s ease-out;
    }

    @keyframes ddlSlideIn {
        from { transform: translateX(-100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }

    .deadline-widget {
        background: linear-gradient(135deg, #eab366 0%, #ff9d00 100%);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,.3);
        width: 240px;
        overflow: hidden;
        transition: background .4s;
    }

    .deadline-widget.urgent {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        box-shadow: 0 4px 20px rgba(220,38,38,.45);
    }

    .widget-header {
        background: rgba(0,0,0,.2);
        padding: 8px 12px;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .widget-close {
        background: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background .2s;
        flex-shrink: 0;
    }
    .widget-close:hover { background: rgba(255,255,255,.2); }

    .widget-body { padding: 10px; }

    .countdown-display {
        display: flex;
        justify-content: space-around;
        gap: 8px;
        margin-bottom: 8px;
    }

    .countdown-item { text-align: center; flex: 1; }

    .countdown-number {
        display: block;
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .countdown-text {
        display: block;
        font-size: 9px;
        color: rgba(255,255,255,.8);
        text-transform: uppercase;
        margin-top: 2px;
    }

    .widget-message {
        color: #fff;
        font-size: 11px;
        text-align: center;
        margin: 0;
        padding: 5px 8px;
        background: rgba(0,0,0,.15);
        border-radius: 4px;
        overflow: hidden;
    }
</style>

<script>
(function () {
    'use strict';

    @php
        $ddlJson = $upcoming->map(function($d) {
            return [
                'id'             => $d->id,
                'name'           => $d->name,
                'countdown_date' => $d->countdown_date->toIso8601String(),
                'due_label'      => $d->countdown_date->format('j M Y'),
            ];
        });
    @endphp

    var QUEUE        = @json($ddlJson);   // sorted asc by countdown_date
    var currentIndex = 0;
    var tickInterval = null;

    // ── Advance to the next deadline in the queue ──────────────────────
    function showDeadline(index) {
        if (index >= QUEUE.length) {
            $('#depositDeadlineWidget').fadeOut(300);
            return;
        }

        var item   = QUEUE[index];
        var target = new Date(item.countdown_date).getTime();

        // Bail immediately if this one has already passed (edge-case where
        // the page was open across midnight)
        if (target - Date.now() <= 0) {
            showDeadline(index + 1);
            return;
        }

        // Update static labels
        $('#ddl-name').text(item.name);
        $('#ddl-due-date').text(item.due_label);

        // Queue badge — shows "2 more" etc. when there are subsequent deadlines
        var remaining = QUEUE.length - index - 1;
        if (remaining > 0) {
            $('#ddl-queue-badge').text('+' + remaining + ' more').show();
        } else {
            $('#ddl-queue-badge').hide();
        }

        // Reset urgency
        $('#deadlineWidgetInner').removeClass('urgent');

        // Show widget with slide-in animation
        $('#depositDeadlineWidget')
            .css('animation', 'none')
            .hide()
            .fadeIn(350)
            .css('animation', '');

        // Clear any previous ticker
        if (tickInterval) { clearInterval(tickInterval); }

        function tick() {
            var distance = new Date(item.countdown_date).getTime() - Date.now();

            if (distance <= 0) {
                clearInterval(tickInterval);
                // Small pause then advance
                setTimeout(function () {
                    currentIndex++;
                    showDeadline(currentIndex);
                }, 800);
                return;
            }

            var days  = Math.floor(distance / 86400000);
            var hours = Math.floor((distance % 86400000) / 3600000);
            var mins  = Math.floor((distance % 3600000)  / 60000);

            $('#ddl-days').text(days);
            $('#ddl-hours').text(hours);
            $('#ddl-mins').text(mins);

            if (days === 0) {
                $('#deadlineWidgetInner').addClass('urgent');
            }
        }

        tick();
        tickInterval = setInterval(tick, 1000);
    }

    // ── Close / dismiss (hides widget; does not affect the queue) ─────
    $('#ddlCloseBtn').on('click', function () {
        $('#depositDeadlineWidget').fadeOut(300);
    });

    // ── Boot ──────────────────────────────────────────────────────────
    $(document).ready(function () {
        if (QUEUE.length > 0) {
            showDeadline(0);
        }
    });

}());
</script>

@endif
