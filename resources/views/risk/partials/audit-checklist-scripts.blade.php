<script>
(function () {
    'use strict';

    var TOTAL_STEPS = 10;
    var currentStep = 1;
    var activeSteps = [1,2,3,4,5,6,7,8,9,10];

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

    /* ── Initialize Select2 for branch selector ───────────────── */
    function initBranchSelect() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-branch').select2({
                placeholder: '— Search and select a branch —',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#auditChecklistModal') // Fix for modal focus issue
            });
        }
    }

    /* ── Handle audit scope change ───────────────── */
    function handleScopeChange() {
        var scopeSelect = document.getElementById('s1_audit_scope');
        if (scopeSelect) {
            scopeSelect.addEventListener('change', function() {
                var selectedSections = Array.from(scopeSelect.selectedOptions).map(o => parseInt(o.value));
                activeSteps = [1]; // intro always
                for (let s of selectedSections) {
                    activeSteps.push(s + 1); // section 1 -> step 2
                }
                TOTAL_STEPS = activeSteps.length;
                // Reset to step 1 if changed
                currentStep = 1;
                showStep(1);
            });
        }
    }

    /* ── Handle branch selection change ───────────────── */
    function handleBranchChange() {
        var select = document.getElementById('s1OfficeSelect');
        if (select) {
            select.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var branchDetailsDiv = document.getElementById('s1BranchDetails');
                var officeId = selectedOption.value;

                if (officeId) {
                    console.log('Office selected:', officeId);
                    // Populate branch details from data attributes
                    document.getElementById('s1BranchCode').value = selectedOption.getAttribute('data-code') || '';

                    // Fetch additional audit data
                    fetch(window.location.origin + '/risk/office-audit-data/' + officeId)
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            console.log('Data received:', data);
                            document.getElementById('s3_total_active').value = data.s3_total_active || 0;
                            document.getElementById('s3_incomplete_files').value = data.s3_incomplete_files || 0;
                            document.getElementById('s4_system_collections').value = data.s4_system_collections || 0;
                            document.getElementById('s4_wallet_collections').value = data.s4_wallet_collections || 0;
                            document.getElementById('s6_total_staff').value = data.s6_total_staff || 0;
                        })
                        .catch(function(error) {
                            console.error('Failed to fetch office audit data:', error);
                        });

                    // Show the branch details section
                    if (branchDetailsDiv) branchDetailsDiv.style.display = 'block';
                } else {
                    // Hide and clear branch details if no branch selected
                    if (branchDetailsDiv) branchDetailsDiv.style.display = 'none';
                    document.getElementById('s1BranchCode').value = '';
                    document.getElementById('s1BranchProvince').value = '';
                    document.getElementById('s1BranchDistrict').value = '';
                    document.getElementById('s1BranchAddress').value = '';
                    document.getElementById('s1BranchPhone').value = '';
                    document.getElementById('s1BranchEmail').value = '';
                    // Clear audit data fields
                    document.getElementById('s3_total_active').value = '';
                    document.getElementById('s3_incomplete_files').value = '';
                    document.getElementById('s4_system_collections').value = '';
                    document.getElementById('s4_wallet_collections').value = '';
                    document.getElementById('s6_total_staff').value = '';
                }
            });
        }
    }

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

        var stepId = activeSteps[n - 1];
        var target = document.getElementById('step-' + stepId);
        if (target) target.style.display = 'block';

        var pct = Math.round((n / TOTAL_STEPS) * 100);
        var bar = document.getElementById('auditProgressBar');
        if (bar) { bar.style.width = pct + '%'; bar.setAttribute('aria-valuenow', pct); }

        var lbl = document.getElementById('auditStepLabel');
        if (lbl) lbl.textContent = 'Step ' + n + ' of ' + TOTAL_STEPS + ' \u2014 ' + stepLabels[stepId - 1];

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
            
            // Initialize Select2 when modal opens
            initBranchSelect();
            // Attach change handlers
            handleBranchChange();
            handleScopeChange();
        });

        // Clean up Select2 when modal closes
        modal.addEventListener('hidden.bs.modal', function () {
            activeSteps = [1,2,3,4,5,6,7,8,9,10];
            TOTAL_STEPS = 10;
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-branch').select2('destroy');
            }
            document.getElementById('s1OfficeSelect').value = '';
            var scopeSelect = document.getElementById('s1_audit_scope');
            if (scopeSelect) {
                for (let option of scopeSelect.options) {
                    option.selected = false;
                }
            }
            var branchDetailsDiv = document.getElementById('s1BranchDetails');
            if (branchDetailsDiv) branchDetailsDiv.style.display = 'none';
            document.getElementById('s1BranchCode').value = '';
            document.getElementById('s1BranchProvince').value = '';
            document.getElementById('s1BranchDistrict').value = '';
            document.getElementById('s1BranchAddress').value = '';
            document.getElementById('s1BranchPhone').value = '';
            document.getElementById('s1BranchEmail').value = '';
            document.getElementById('s3_total_active').value = '';
            document.getElementById('s3_incomplete_files').value = '';
            document.getElementById('s4_system_collections').value = '';
            document.getElementById('s4_wallet_collections').value = '';
            document.getElementById('s6_total_staff').value = '';
        });
    }

    // Attach change handlers immediately in case modal is already shown or for testing
    handleBranchChange();
    handleScopeChange();

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

    /* ── Toggle functions ─────────────────────────────────────── */
    window.toggleZeroCashPolicy = function () {
        var expanded = document.getElementById('zeroCashPolicyExpanded');
        var toggle = event.target;
        if (expanded.style.display === 'none') {
            expanded.style.display = 'block';
            toggle.textContent = ' See Less';
        } else {
            expanded.style.display = 'none';
            toggle.textContent = ' See More';
        }
    };

    window.toggleCollectionsPolicy = function () {
        var expanded = document.getElementById('collectionsPolicyExpanded');
        var toggle = event.target;
        if (expanded.style.display === 'none') {
            expanded.style.display = 'block';
            toggle.textContent = ' See Less';
        } else {
            expanded.style.display = 'none';
            toggle.textContent = ' See More';
        }
    };

    window.toggleFraudWarning = function () {
        var expanded = document.getElementById('fraudWarningExpanded');
        var toggle = event.target;
        if (expanded.style.display === 'none') {
            expanded.style.display = 'block';
            toggle.textContent = ' See Less';
        } else {
            expanded.style.display = 'none';
            toggle.textContent = ' See More';
        }
    };

    window.toggleStaffingPolicy = function () {
        var expanded = document.getElementById('staffingPolicyExpanded');
        var toggle = event.target;
        if (expanded.style.display === 'none') {
            expanded.style.display = 'block';
            toggle.textContent = ' See Less';
        } else {
            expanded.style.display = 'none';
            toggle.textContent = ' See More';
        }
    };

    /* ── Submit ───────────────────────────────────────────────── */
    window.submitAuditChecklist = function () {
        // Submit the form
        document.getElementById('auditForm').submit();
    };

})();
</script>

