<script>
(function () {
    'use strict';

    var TOTAL_STEPS = 10;
    var currentStep = 1;

    var stepLabels = [
        'How to Use This Checklist',
        'Section 1 — Audit Administration',
        'Section 2 — Withinhere Wallet & Digital Payment Controls',
        'Section 3 — Loan Portfolio Integrity',
        'Section 4 — Collections & Recoveries',
        'Section 5 — Fraud Risk Indicators',
        'Section 6 — Staff & Process Compliance',
        'Section 7 — System & Control Environment',
        'Section 8 — Reporting & Governance',
        'Section 9 — Audit Conclusion & Sign-Off'
    ];

    /* ── Risk scoring ─────────────────────────────────────────── */
    function getRiskRating(count) {
        if (count <= 3)  return { label: '🟢 LOW — Branch is compliant',                color: '#27ae60' };
        if (count <= 7)  return { label: '🟡 MEDIUM — Weaknesses present',              color: '#f39c12' };
        if (count <= 12) return { label: '🔴 HIGH — Significant control failures',       color: '#e74c3c' };
        return               { label: '🚨 CRITICAL — Immediate escalation required',    color: '#7b241c' };
    }

    function updateRiskScore() {
        var fails = document.querySelectorAll('.fail-radio:checked').length;
        var rating = getRiskRating(fails);

        var countEl  = document.getElementById('failCount');
        var labelEl  = document.getElementById('riskRatingLabel');
        var boxEl    = document.getElementById('riskScoreBox');

        if (countEl)  { countEl.textContent  = fails; countEl.style.color = rating.color; }
        if (labelEl)  { labelEl.textContent  = rating.label; labelEl.style.color = rating.color; }
        if (boxEl)    { boxEl.style.borderColor = rating.color; }

        var header = boxEl ? boxEl.querySelector('.box-header') : null;
        if (header) header.style.background = rating.color;
    }

    /* ── Fraud indicator alert ────────────────────────────────── */
    function updateFraudAlert() {
        var presentCount = document.querySelectorAll('.fraud-indicator-radio:checked').length;
        var alertBox = document.getElementById('fraudAlert');
        if (alertBox) alertBox.style.display = (presentCount >= 3) ? 'block' : 'none';
    }

    /* ── Step navigation ──────────────────────────────────────── */
    function showStep(n) {
        document.querySelectorAll('.audit-step').forEach(function (el) {
            el.style.display = 'none';
        });

        var target = document.getElementById('step-' + n);
        if (target) target.style.display = 'block';

        var pct = Math.round((n / TOTAL_STEPS) * 100);
        var bar = document.getElementById('auditProgressBar');
        if (bar) { bar.style.width = pct + '%'; bar.setAttribute('aria-valuenow', pct); }

        var lbl = document.getElementById('auditStepLabel');
        if (lbl) lbl.textContent = 'Step ' + n + ' of ' + TOTAL_STEPS + ' \u2014 ' + stepLabels[n - 1];

        var prevBtn   = document.getElementById('auditPrevBtn');
        var nextBtn   = document.getElementById('auditNextBtn');
        var submitBtn = document.getElementById('auditSubmitBtn');

        if (prevBtn)   prevBtn.style.display   = (n === 1) ? 'none' : 'inline-block';
        if (n === TOTAL_STEPS) {
            if (nextBtn)   nextBtn.style.display   = 'none';
            if (submitBtn) submitBtn.style.display = 'inline-block';
            updateRiskScore();
        } else {
            if (nextBtn)   nextBtn.style.display   = 'inline-block';
            if (submitBtn) submitBtn.style.display = 'none';
        }
    }

    window.auditWizardNav = function (direction) {
        var next = currentStep + direction;
        if (next < 1 || next > TOTAL_STEPS) return;
        currentStep = next;
        showStep(currentStep);
    };

    /* ── Reset on open ────────────────────────────────────────── */
    var modal = document.getElementById('auditChecklistModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function () {
            currentStep = 1;
            showStep(1);
        });
    }

    /* ── Live listeners ───────────────────────────────────────── */
    document.addEventListener('change', function (e) {
        if (!e.target) return;

        var radio = e.target;

        /* Handle any audit radio wrap toggle */
        if (radio.closest && radio.closest('.audit-radio-wrap')) {
            var wrap = radio.closest('.audit-radio-wrap');
            var name = radio.name;

            /* Reset all wraps sharing this radio name */
            document.querySelectorAll('.audit-radio-wrap input[name="' + name + '"]').forEach(function (inp) {
                var w = inp.closest('.audit-radio-wrap');
                var td = inp.closest('td');
                w.classList.remove('is-checked');
                if (td) td.classList.remove('fail-cell-active');
            });

            /* Mark the selected wrap */
            wrap.classList.add('is-checked');

            /* If it's a fail radio, highlight the cell and the data row */
            var isFail = radio.classList.contains('fail-radio');
            var td = radio.closest('td');
            var tr = radio.closest('tr');

            if (isFail) {
                if (td) td.classList.add('fail-cell-active');
                /* Find the data row (the one before the notes row) and mark it */
                if (tr) tr.classList.add('row-failed');
                /* Also mark the notes row below */
                var notesRow = tr ? tr.nextElementSibling : null;
                if (notesRow) notesRow.classList.add('row-failed');
            } else {
                /* Clearing fail state when pass is selected */
                if (tr) {
                    tr.classList.remove('row-failed');
                    var notesRow = tr.nextElementSibling;
                    if (notesRow) notesRow.classList.remove('row-failed');
                }
            }
        }

        if (radio.classList.contains('fraud-indicator-radio')) updateFraudAlert();
        if (radio.classList.contains('fail-radio'))            updateRiskScore();
    });

    /* ── Submit ───────────────────────────────────────────────── */
    window.submitAuditChecklist = function () {
        alert('Audit checklist submitted successfully. A copy will be saved to the Risk Management records.');
        $('#auditChecklistModal').modal('hide');
    };

})();
</script>

