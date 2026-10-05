@php
    /**
     * Cash Audit Wizard Component
     *
     * Shown automatically to Branch Managers (role_id = 4) whose office matches
     * the target_office_id set by the Risk Manager via the dashboard toggle.
     *
     * Included in layouts/master.blade.php — guards are checked here so the
     * component is a complete no-op for every other user / office combination.
     *
     * Variables available from master.blade.php scope:
     *   $user   – Sentinel user object (or null)
     *   $role   – role_id string, e.g. "4"
     *   $office – office id integer
     *
     * $isPreview – optional boolean injected by the risk dashboard preview route
     *              to render the form in a preview modal without auto-showing.
     */
    $isPreview = $isPreview ?? false;

    // Only render anything for BMs (role 4) or when previewing from risk dashboard
    $currentUser   = isset($user)   ? $user   : \Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser();
    $currentRole   = isset($role)   ? $role   : null;
    $currentOffice = isset($office) ? $office : ($currentUser ? $currentUser->office_id : null);

    $isRiskManager = $currentUser && in_array((string) $currentUser->id, array_map('strval', config('role.risk', [])));

    // For preview mode (risk manager sees the form)
    if ($isPreview && $isRiskManager) {
        $shouldShow = true;
    } elseif ((string) $currentRole !== '4') {
        $shouldShow = false;
    } else {
        // Check if wizard is active and this office is the target
        $auditConfig = \App\Models\CashAuditWizardConfig::find(1);
        $shouldShow  = $auditConfig
            && $auditConfig->is_active
            && (int) $auditConfig->target_office_id === (int) $currentOffice;
    }
@endphp

@if($shouldShow)
{{-- ==========================================================================
     Cash Balance & Mobile Money Audit Wizard
     Two-step form modal shown to the targeted Branch Manager.
     Uses Bootstrap 3 modal + pure jQuery — no Alpine.js / Livewire.
========================================================================== --}}

{{-- Overlay backdrop (separate from Bootstrap modal so we can control z-index) --}}
<div id="cashAuditOverlay" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(10,14,30,.82);
    backdrop-filter:blur(6px);
    -webkit-backdrop-filter:blur(6px);
    z-index:999990;
"></div>

{{-- Wizard modal --}}
<div id="cashAuditWizardModal"
     style="
        display:none;
        position:fixed;
        inset:0;
        z-index:999991;
        align-items:center;
        justify-content:center;
        padding:16px;
    "
     role="dialog"
     aria-labelledby="cashAuditWizardTitle"
     aria-modal="true">

    <div style="
        background:#fff;
        border-radius:18px;
        width:100%;
        max-width:620px;
        max-height:90vh;
        overflow-y:auto;
        box-shadow:0 24px 64px rgba(0,0,0,.4);
        display:flex;
        flex-direction:column;
        animation:cawSlideUp .4s cubic-bezier(.16,1,.3,1) both;
    ">

        {{-- ── Header ──────────────────────────────────────────────── --}}
        <div style="
            background:linear-gradient(135deg,#1a3a6b 0%,#0d2247 100%);
            padding:20px 24px 18px;
            border-radius:18px 18px 0 0;
            flex-shrink:0;
        ">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="
                    width:42px;height:42px;
                    background:rgba(255,255,255,.15);
                    border-radius:50%;
                    display:flex;align-items:center;justify-content:center;
                    flex-shrink:0;
                ">
                    <i class="fa fa-money" style="color:#fff;font-size:1.2rem;"></i>
                </div>
                <div>
                    <h4 id="cashAuditWizardTitle" style="color:#fff;margin:0;font-size:1.3rem;font-weight:700;line-height:1.3;">
                        Cash Balance &amp; Mobile Money Audit
                    </h4>
                    <p style="color:rgba(255,255,255,.75);margin:0;font-size:.95rem;">
                        Immediate cash count required — submit all sections accurately
                    </p>
                </div>
            </div>

            {{-- Step indicator --}}
            <div style="display:flex;gap:8px;margin-top:16px;">
                <div class="caw-step-indicator" id="cawStepDot1" style="
                    flex:1;height:4px;border-radius:2px;
                    background:rgba(255,255,255,.9);
                    transition:background .3s;
                "></div>
                <div class="caw-step-indicator" id="cawStepDot2" style="
                    flex:1;height:4px;border-radius:2px;
                    background:rgba(255,255,255,.3);
                    transition:background .3s;
                "></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:4px;">
                <span id="cawStepLabel1" style="font-size:.85rem;color:rgba(255,255,255,.9);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Step 1 of 2 — Cash Balance
                </span>
                <span id="cawStepLabel2" style="font-size:.85rem;color:rgba(255,255,255,.4);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                    Step 2 — Mobile Money
                </span>
            </div>
        </div>

        {{-- ── Body (steps) ──────────────────────────────────────────── --}}
        <div style="padding:24px;flex:1;">

            {{-- ════════════════════════════════════════════════════════
                 STEP 1 — Cash Balance Confirmation
            ════════════════════════════════════════════════════════ --}}
            <div id="cawStep1">

                {{-- Instruction banner --}}
                <div style="
                    background:#fff8e1;border-left:4px solid #f59e0b;
                    border-radius:0 8px 8px 0;padding:10px 14px;
                    margin-bottom:18px;font-size:.98rem;color:#78450a;line-height:1.5;
                ">
                    <i class="fa fa-exclamation-circle" style="margin-right:6px;"></i>
                    <strong>Action required:</strong>
                    Conduct a physical cash count immediately and record the figures below.
                </div>

                {{-- A. Branch Identification --}}
                <div style="margin-bottom:20px;">
                    <h6 style="font-size:.92rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;margin-bottom:10px;border-bottom:1px solid #f0f0f0;padding-bottom:6px;">
                        A. Branch Identification
                    </h6>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label class="caw-label" for="cawBranchName">Branch Name <span style="color:#e53e3e;">*</span></label>
                            <input type="text" id="cawBranchName" class="caw-input" placeholder="e.g. Lusaka Main Branch" required>
                        </div>
                        <div>
                            <label class="caw-label" for="cawDMName">District Manager Name <span style="color:#e53e3e;">*</span></label>
                            <input type="text" id="cawDMName" class="caw-input" placeholder="Full name" required>
                        </div>
                    </div>
                </div>

                {{-- B. Cash Count Breakdown --}}
                <div style="margin-bottom:20px;">
                    <h6 style="font-size:.92rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;margin-bottom:10px;border-bottom:1px solid #f0f0f0;padding-bottom:6px;">
                        B. Cash Count Breakdown — Physical Cash (enter number of notes/coins)
                    </h6>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                        @foreach([5, 10, 20, 50, 100, 200, 500] as $denom)
                        <div>
                            <label class="caw-label" for="cawCash{{ $denom }}">K{{ $denom }}</label>
                            <input type="number" id="cawCash{{ $denom }}"
                                   class="caw-input caw-denom-input"
                                   data-denom="{{ $denom }}"
                                   data-target="cash"
                                   min="0" step="1" placeholder="0">
                        </div>
                        @endforeach
                    </div>
                    <div style="
                        background:#f0fdf4;border:1px solid #bbf7d0;
                        border-radius:8px;padding:10px 14px;margin-top:10px;
                        display:flex;justify-content:space-between;align-items:center;
                    ">
                        <span style="font-size:.98rem;color:#166534;font-weight:600;">Total Cash Balance</span>
                        <span id="cawCashTotal" style="font-size:1.2rem;font-weight:700;color:#166534;">K 0.00</span>
                    </div>
                    <div style="margin-top:10px;">
                        <label class="caw-label" for="cawCashDatetime">Date &amp; Time of Cash Count <span style="color:#e53e3e;">*</span></label>
                        <input type="datetime-local" id="cawCashDatetime" class="caw-input">
                    </div>
                </div>

                {{-- C. Petty Cash --}}
                <div>
                    <h6 style="font-size:.92rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;margin-bottom:10px;border-bottom:1px solid #f0f0f0;padding-bottom:6px;">
                        C. Petty Cash
                    </h6>
                    <div style="margin-bottom:10px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:.98rem;color:#374151;cursor:pointer;">
                            <input type="checkbox" id="cawPettyViaMobile" style="width:16px;height:16px;cursor:pointer;">
                            Petty cash is held via mobile wallet (skip denomination breakdown)
                        </label>
                    </div>
                    <div id="cawPettyDenomSection">
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                            @foreach([5, 10, 20, 50, 100, 200, 500] as $denom)
                            <div>
                                <label class="caw-label" for="cawPetty{{ $denom }}">K{{ $denom }}</label>
                                <input type="number" id="cawPetty{{ $denom }}"
                                       class="caw-input caw-denom-input"
                                       data-denom="{{ $denom }}"
                                       data-target="petty"
                                       min="0" step="1" placeholder="0">
                            </div>
                            @endforeach
                        </div>
                        <div style="
                            background:#f0fdf4;border:1px solid #bbf7d0;
                            border-radius:8px;padding:10px 14px;margin-top:10px;
                            display:flex;justify-content:space-between;align-items:center;
                        ">
                            <span style="font-size:.98rem;color:#166534;font-weight:600;">Total Petty Cash</span>
                            <span id="cawPettyTotal" style="font-size:1.2rem;font-weight:700;color:#166534;">K 0.00</span>
                        </div>
                    </div>
                    <div id="cawPettyMobileNote" style="display:none;">
                        <div style="
                            background:#eff6ff;border-left:3px solid #3b82f6;
                            border-radius:0 8px 8px 0;padding:10px 14px;
                            font-size:.95rem;color:#1e40af;
                        ">
                            <i class="fa fa-info-circle"></i>
                            Petty cash via mobile wallet — use the Mobile Money section (Step 2) to confirm the balance.
                        </div>
                    </div>
                </div>

            </div>{{-- /cawStep1 --}}

            {{-- ════════════════════════════════════════════════════════
                 STEP 2 — Mobile Money Balance Confirmation
            ════════════════════════════════════════════════════════ --}}
            <div id="cawStep2" style="display:none;">

                <div style="
                    background:#eff6ff;border-left:4px solid #3b82f6;
                    border-radius:0 8px 8px 0;padding:10px 14px;
                    margin-bottom:18px;font-size:.98rem;color:#1e40af;line-height:1.5;
                ">
                    <i class="fa fa-mobile" style="margin-right:6px;font-size:1rem;"></i>
                    Verify your mobile money balance via <strong>*115#</strong> (Check Balance option),
                    then enter the details below.
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

                    <div style="grid-column:1/-1;">
                        <label class="caw-label" for="cawUssdRef">
                            Transaction Reference Number <span style="color:#e53e3e;">*</span>
                            <span style="font-weight:400;color:#9ca3af;">(from USSD balance enquiry)</span>
                        </label>
                        <input type="text" id="cawUssdRef" class="caw-input" placeholder="e.g. TXN20261001123456" required>
                    </div>

                    <div>
                        <label class="caw-label" for="cawMobileNumber">
                            Airtel / MTN Mobile Number <span style="color:#e53e3e;">*</span>
                        </label>
                        <input type="tel" id="cawMobileNumber" class="caw-input" placeholder="e.g. 0971 234 567" required>
                    </div>

                    <div>
                        <label class="caw-label" for="cawSimName">
                            Registered Name of SIM Card Holder <span style="color:#e53e3e;">*</span>
                        </label>
                        <input type="text" id="cawSimName" class="caw-input" placeholder="Full name on SIM registration" required>
                    </div>

                    <div style="grid-column:1/-1;">
                        <label class="caw-label" for="cawDMUsingSim">
                            Name of District Manager Using this SIM Card <span style="color:#e53e3e;">*</span>
                        </label>
                        <input type="text" id="cawDMUsingSim" class="caw-input" placeholder="Full name" required>
                    </div>

                </div>

                {{-- Summary card shown before final submit --}}
                <div id="cawSummaryCard" style="
                    margin-top:20px;
                    background:#f8fafc;border:1px solid #e2e8f0;
                    border-radius:10px;padding:14px;
                    display:none;
                ">
                    <div style="font-size:.92rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;margin-bottom:10px;">
                        Submission Summary
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:.97rem;">
                        <div style="color:#6b7280;">Branch:</div>           <div id="cawSumBranch"     style="font-weight:600;color:#1e293b;"></div>
                        <div style="color:#6b7280;">District Manager:</div> <div id="cawSumDM"         style="font-weight:600;color:#1e293b;"></div>
                        <div style="color:#6b7280;">Cash Total:</div>       <div id="cawSumCash"       style="font-weight:600;color:#166534;"></div>
                        <div style="color:#6b7280;">Petty Cash Total:</div> <div id="cawSumPetty"      style="font-weight:600;color:#166534;"></div>
                        <div style="color:#6b7280;">USSD Reference:</div>   <div id="cawSumUssd"       style="font-weight:600;color:#1e293b;"></div>
                        <div style="color:#6b7280;">Mobile Number:</div>    <div id="cawSumMobile"     style="font-weight:600;color:#1e293b;"></div>
                    </div>
                </div>

            </div>{{-- /cawStep2 --}}

            {{-- Error alert --}}
            <div id="cawError" style="
                display:none;
                background:#fef2f2;border:1px solid #fecaca;
                border-radius:8px;padding:10px 14px;
                color:#dc2626;font-size:.98rem;margin-top:12px;
            ">
                <i class="fa fa-times-circle"></i>
                <span id="cawErrorMsg"></span>
            </div>

        </div>{{-- /body --}}

        {{-- ── Footer ──────────────────────────────────────────────── --}}
        <div style="
            padding:16px 24px;
            border-top:1px solid #f0f0f0;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-shrink:0;
            background:#fafafa;
            border-radius:0 0 18px 18px;
        ">
            {{-- Back / Cancel --}}
            <div>
                <button type="button" id="cawBtnBack" style="display:none;" onclick="cawGoBack()" class="caw-btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
                @if($isPreview)
                <button type="button" onclick="cawClosePreview()" class="caw-btn-secondary">
                    <i class="fa fa-times"></i> Close Preview
                </button>
                @endif
            </div>

            {{-- Next / Submit --}}
            <div style="display:flex;gap:8px;align-items:center;">
                <span id="cawSpinner" style="display:none;">
                    <i class="fa fa-spinner fa-spin" style="color:#1a3a6b;"></i>
                </span>
                <button type="button" id="cawBtnNext" onclick="cawGoNext()" class="caw-btn-primary">
                    Next Step <i class="fa fa-arrow-right"></i>
                </button>
                <button type="button" id="cawBtnSubmit" style="display:none;" onclick="cawSubmit()" class="caw-btn-primary" style="background:#16a34a;border-color:#16a34a;">
                    <i class="fa fa-check"></i> Submit Audit
                </button>
            </div>
        </div>

    </div>{{-- /modal panel --}}
</div>{{-- /cashAuditWizardModal --}}

{{-- ── Success screen (replaces modal on submit) ────────────────────────── --}}
<div id="cashAuditSuccess" style="
    display:none;
    position:fixed;
    inset:0;
    z-index:999992;
    align-items:center;
    justify-content:center;
    padding:16px;
">
    <div style="
        background:#fff;border-radius:18px;
        max-width:400px;width:100%;
        padding:40px 32px;text-align:center;
        box-shadow:0 24px 64px rgba(0,0,0,.4);
        animation:cawSlideUp .4s cubic-bezier(.16,1,.3,1) both;
    ">
        <div style="
            width:64px;height:64px;
            background:linear-gradient(135deg,#22c55e,#16a34a);
            border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 16px;
        ">
            <i class="fa fa-check" style="color:#fff;font-size:1.8rem;"></i>
        </div>
        <h4 style="margin:0 0 8px;color:#1e293b;font-weight:700;">Audit Submitted</h4>
        <p style="color:#6b7280;font-size:1.05rem;margin:0;">
            Your cash balance and mobile money details have been recorded successfully.
            Thank you.
        </p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════════════════════ --}}
<style>
    @keyframes cawSlideUp {
        from { opacity:0; transform:translateY(32px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .caw-label {
        display:block;
        font-size:.92rem;
        font-weight:600;
        color:#374151;
        margin-bottom:4px;
        text-transform:uppercase;
        letter-spacing:.04em;
    }

    .caw-input {
        width:100%;
        padding:8px 10px;
        border:1px solid #d1d5db;
        border-radius:8px;
        font-size:1rem;
        color:#1e293b;
        background:#fff;
        transition:border-color .2s,box-shadow .2s;
        box-sizing:border-box;
    }
    .caw-input:focus {
        outline:none;
        border-color:#1a3a6b;
        box-shadow:0 0 0 3px rgba(26,58,107,.12);
    }
    .caw-input.caw-invalid {
        border-color:#dc2626;
        box-shadow:0 0 0 3px rgba(220,38,38,.1);
    }

    .caw-btn-primary {
        background:linear-gradient(135deg,#1a3a6b 0%,#0d2247 100%);
        color:#fff;
        border:none;
        border-radius:8px;
        padding:10px 22px;
        font-size:1rem;
        font-weight:600;
        cursor:pointer;
        transition:opacity .2s;
    }
    .caw-btn-primary:hover { opacity:.88; }
    .caw-btn-primary:disabled { opacity:.5; cursor:not-allowed; }

    .caw-btn-primary.caw-btn-green {
        background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%);
    }

    .caw-btn-secondary {
        background:#f3f4f6;
        color:#374151;
        border:1px solid #d1d5db;
        border-radius:8px;
        padding:10px 18px;
        font-size:1rem;
        font-weight:600;
        cursor:pointer;
        transition:background .2s;
    }
    .caw-btn-secondary:hover { background:#e5e7eb; }
</style>

{{-- ══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    var CAW_STEP       = 1;
    var CAW_CSRF       = '{{ csrf_token() }}';
    var CAW_SUBMIT_URL = '{{ route("risk.cash-audit.store") }}';
    var CAW_PREVIEW    = {{ $isPreview ? 'true' : 'false' }};
    var DENOMS         = [5, 10, 20, 50, 100, 200, 500];

    // ── Open on load (non-preview only) ─────────────────────────────────────
    $(document).ready(function () {
        if (!CAW_PREVIEW) {
            // Small delay so the page settles before the modal appears
            setTimeout(function () {
                cawOpen();
            }, 800);
        }

        // Pre-fill datetime to now
        var now  = new Date();
        var pad  = function (n) { return n < 10 ? '0' + n : n; };
        var dt   = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) +
                   'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        document.getElementById('cawCashDatetime').value = dt;

        // Denomination live totals
        $(document).on('input', '.caw-denom-input', function () {
            var target = $(this).data('target'); // 'cash' or 'petty'
            cawRecalcTotal(target);
        });

        // Petty-via-mobile toggle
        $('#cawPettyViaMobile').on('change', function () {
            if (this.checked) {
                $('#cawPettyDenomSection').hide();
                $('#cawPettyMobileNote').show();
            } else {
                $('#cawPettyDenomSection').show();
                $('#cawPettyMobileNote').hide();
            }
        });
    });

    // ── Expose to window for inline onclick handlers ─────────────────────────
    window.cawOpen = function () {
        $('#cashAuditOverlay').fadeIn(300);
        $('#cashAuditWizardModal').css('display', 'flex').hide().fadeIn(300);
    };

    window.cawClosePreview = function () {
        $('#cashAuditOverlay').fadeOut(200);
        $('#cashAuditWizardModal').fadeOut(200);
    };

    window.cawGoNext = function () {
        cawHideError();
        if (CAW_STEP === 1) {
            if (!cawValidateStep1()) return;
            cawPopulateSummary();
            cawSetStep(2);
        }
    };

    window.cawGoBack = function () {
        cawHideError();
        if (CAW_STEP === 2) {
            cawSetStep(1);
        }
    };

    window.cawSubmit = function () {
        if (CAW_PREVIEW) {
            alert('Preview mode — no data will be saved.');
            return;
        }
        if (!cawValidateStep2()) return;

        $('#cawSpinner').show();
        $('#cawBtnSubmit').prop('disabled', true);
        $('#cawBtnBack').prop('disabled', true);

        var payload = cawBuildPayload();

        $.ajax({
            url:  CAW_SUBMIT_URL,
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': CAW_CSRF },
            success: function (res) {
                if (res.success) {
                    $('#cashAuditOverlay').css('background', 'rgba(10,14,30,.82)');
                    $('#cashAuditWizardModal').fadeOut(300);
                    $('#cashAuditSuccess').css('display', 'flex').hide().fadeIn(400);
                    // Reload the page after 3 seconds so the wizard no longer appears
                    setTimeout(function () { location.reload(); }, 3000);
                } else {
                    cawShowError(res.message || 'Submission failed. Please try again.');
                }
            },
            error: function (xhr) {
                var msg = 'An error occurred. Please try again.';
                try {
                    var errs = xhr.responseJSON;
                    if (errs && errs.errors) {
                        var first = Object.values(errs.errors)[0];
                        msg = Array.isArray(first) ? first[0] : first;
                    } else if (errs && errs.message) {
                        msg = errs.message;
                    }
                } catch (e) {}
                cawShowError(msg);
            },
            complete: function () {
                $('#cawSpinner').hide();
                $('#cawBtnSubmit').prop('disabled', false);
                $('#cawBtnBack').prop('disabled', false);
            }
        });
    };

    // ── Internals ────────────────────────────────────────────────────────────
    function cawSetStep(n) {
        CAW_STEP = n;
        if (n === 1) {
            $('#cawStep1').show();
            $('#cawStep2').hide();
            $('#cawBtnBack').hide();
            $('#cawBtnNext').show();
            $('#cawBtnSubmit').hide();
            // Step indicators
            $('#cawStepDot1').css('background', 'rgba(255,255,255,.9)');
            $('#cawStepDot2').css('background', 'rgba(255,255,255,.3)');
            $('#cawStepLabel1').css('color', 'rgba(255,255,255,.9)');
            $('#cawStepLabel2').css('color', 'rgba(255,255,255,.4)');
        } else {
            $('#cawStep1').hide();
            $('#cawStep2').show();
            $('#cawBtnBack').show();
            $('#cawBtnNext').hide();
            $('#cawBtnSubmit').show().addClass('caw-btn-green');
            $('#cawSummaryCard').show();
            // Step indicators
            $('#cawStepDot1').css('background', 'rgba(255,255,255,.4)');
            $('#cawStepDot2').css('background', 'rgba(255,255,255,.9)');
            $('#cawStepLabel1').css('color', 'rgba(255,255,255,.4)');
            $('#cawStepLabel2').css('color', 'rgba(255,255,255,.9)');
        }
    }

    function cawRecalcTotal(target) {
        var total = 0;
        $('.caw-denom-input[data-target="' + target + '"]').each(function () {
            var qty   = parseFloat($(this).val()) || 0;
            var denom = parseFloat($(this).data('denom')) || 0;
            total += qty * denom;
        });
        var formatted = 'K ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (target === 'cash') {
            $('#cawCashTotal').text(formatted);
        } else {
            $('#cawPettyTotal').text(formatted);
        }
        return total;
    }

    function cawGetCashTotal()  { return cawRecalcTotal('cash'); }
    function cawGetPettyTotal() { return cawRecalcTotal('petty'); }

    function cawValidateStep1() {
        var ok = true;
        var required = ['cawBranchName', 'cawDMName', 'cawCashDatetime'];
        required.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el.value.trim()) {
                $(el).addClass('caw-invalid');
                ok = false;
            } else {
                $(el).removeClass('caw-invalid');
            }
        });
        if (!ok) {
            cawShowError('Please fill in all required fields before proceeding.');
        }
        return ok;
    }

    function cawValidateStep2() {
        var ok = true;
        var required = ['cawUssdRef', 'cawMobileNumber', 'cawSimName', 'cawDMUsingSim'];
        required.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el.value.trim()) {
                $(el).addClass('caw-invalid');
                ok = false;
            } else {
                $(el).removeClass('caw-invalid');
            }
        });
        if (!ok) {
            cawShowError('Please complete all mobile money fields before submitting.');
        }
        return ok;
    }

    function cawPopulateSummary() {
        $('#cawSumBranch').text($('#cawBranchName').val());
        $('#cawSumDM').text($('#cawDMName').val());
        $('#cawSumCash').text($('#cawCashTotal').text());
        $('#cawSumPetty').text($('#cawPettyViaMobile').is(':checked') ? 'Via mobile wallet' : $('#cawPettyTotal').text());
        $('#cawSumUssd').text($('#cawUssdRef').val() || '—');
        $('#cawSumMobile').text($('#cawMobileNumber').val() || '—');
    }

    function cawBuildPayload() {
        var payload = {
            branch_name:           $('#cawBranchName').val(),
            district_manager_name: $('#cawDMName').val(),
            cash_count_datetime:   $('#cawCashDatetime').val(),
            petty_via_mobile_wallet: $('#cawPettyViaMobile').is(':checked') ? 1 : 0,
            mobile_ussd_reference: $('#cawUssdRef').val(),
            mobile_number:         $('#cawMobileNumber').val(),
            sim_registered_name:   $('#cawSimName').val(),
            dm_using_sim:          $('#cawDMUsingSim').val(),
        };
        DENOMS.forEach(function (d) {
            payload['cash_'  + d] = parseFloat($('#cawCash'  + d).val()) || 0;
            payload['petty_' + d] = parseFloat($('#cawPetty' + d).val()) || 0;
        });
        return payload;
    }

    function cawShowError(msg) {
        $('#cawErrorMsg').text(msg);
        $('#cawError').show();
    }
    function cawHideError() {
        $('#cawError').hide();
        $('.caw-invalid').removeClass('caw-invalid');
    }

}());
</script>
@endif
