      <!-- AUDIT CHECKLIST WIZARD MODAL  (v3.0 — Cashless Operations)
      ============================================================  -->
<style>
.audit-radio-wrap {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    border: 1px solid #ccc;
    background: #f5f5f5;
    font-size: 16px;
    transition: all 0.2s ease;
}
.audit-radio-wrap:hover {
    opacity: 0.8;
}
.audit-radio-wrap.pass-wrap {
    background: #27ae60;
    color: white;
    border-color: #27ae60;
}
.audit-radio-wrap.fail-wrap {
    background: #e74c3c;
    color: white;
    border-color: #e74c3c;
}
.audit-radio-wrap input[type="radio"] {
    display: none;
}
.audit-radio-wrap.is-checked {
    border-width: 2px;
    box-shadow: 0 0 5px rgba(0,0,0,0.3);
}
</style>
<div class="modal fade" id="auditChecklistModal" tabindex="-1" role="dialog" aria-labelledby="auditChecklistModalLabel" aria-modal="true">
    <div class="modal-dialog" role="document" style="width:98%;margin:10px auto;">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header" style="background:#c0392b;color:#fff;border-radius:4px 4px 0 0;padding:14px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1;font-size:22px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="auditChecklistModalLabel" style="margin:0;">
                    <i class="fa fa-clipboard"></i>&nbsp; Branch Audit Checklist
                    <small style="font-size:12px;opacity:.8;margin-left:8px;">v3.0 &mdash; Cashless Operations Edition</small>
                </h4>
                <div style="margin-top:10px;">
                    <div class="progress" style="height:6px;margin-bottom:4px;background:rgba(255,255,255,.3);">
                        <div id="auditProgressBar" class="progress-bar" role="progressbar"
                             style="width:10%;background:#fff;transition:width .3s ease;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small id="auditStepLabel" style="color:#f5c6cb;">Step 1 of 10 &mdash; How to Use This Checklist</small>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body" style="padding:0;max-height:75vh;overflow-y:auto;">
                <form id="auditForm" method="POST" action="{{ route('risk.store-audit-submission') }}">
                    @csrf
                    <input type="hidden" name="audit_submission_id" id="audit_submission_id" value="">
                <div id="auditWizard">

                    {{-- =============================================
                         STEP 0 — HOW TO USE / RISK SCORING GUIDE
                         ============================================= --}}
                    <div class="audit-step" id="step-1">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-info-circle"></i>&nbsp; How to Use This Checklist</h4>
                        </div>
                        <div style="padding:24px;">
                            <div class="callout callout-info">
                                <p>
                                    This checklist enables any supervising officer, manager, auditor, or any authorised person — even without a finance background —
                                    to identify early warning signs of theft or fraud before losses grow large. It reflects the institution's
                                    <strong>cashless operations policy</strong>: all transactions are conducted exclusively through the
                                    <strong>Withinhere branch wallet</strong> or the <strong>Whence Financial Services app</strong>.
                                    No branch may hold or handle physical cash at any time.
                                </p>
                                <ul style="margin:8px 0 0;padding-left:20px;">
                                    <li>Work through each section <strong>in order</strong>. Do not skip items even if the branch looks fine.</li>
                                    <li>Tick <strong>✓ Pass</strong> or <strong>✗ Fail</strong> for each item.</li>
                                    <li>For every ✗, write what you observed in the <em>Finding / Notes</em> field.</li>
                                    <li>After completing all sections, your <strong>Risk Score</strong> is calculated automatically below.</li>
                                    <li>If you encounter anything you do not understand, stop and call: <em>Provincial Manager, Accountant, Risk Manager, Manager Administration, or Technical Director</em>.</li>
                                </ul>
                            </div>

                            <h5 style="color:#c0392b;margin-top:20px;"><i class="fa fa-tachometer"></i>&nbsp; Risk Scoring Guide</h5>
                            <table class="table table-bordered" style="margin-top:10px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th>Risk Level</th>
                                        <th>✗ Count</th>
                                        <th>What It Means</th>
                                        <th>Required Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="success">
                                        <td><strong>🟢 LOW</strong></td>
                                        <td>0–3 ✗ items</td>
                                        <td>Branch is compliant</td>
                                        <td>Standard monitoring; next audit in 3 months</td>
                                    </tr>
                                    <tr class="warning">
                                        <td><strong>🟡 MEDIUM</strong></td>
                                        <td>4–7 ✗ items</td>
                                        <td>Weaknesses present</td>
                                        <td>Action plan within 7 days; follow-up in 30 days</td>
                                    </tr>
                                    <tr class="danger">
                                        <td><strong>🔴 HIGH</strong></td>
                                        <td>8–12 ✗ items</td>
                                        <td>Significant control failures</td>
                                        <td>Escalate to Regional Manager; freeze lending if needed</td>
                                    </tr>
                                    <tr style="background:#7b241c;color:#fff;">
                                        <td><strong>🚨 CRITICAL</strong></td>
                                        <td>13+ ✗ OR any confirmed fraud</td>
                                        <td>Immediate risk to institution</td>
                                        <td>Suspend operations, summon Risk &amp; Finance teams within 24 hrs</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="alert alert-warning" style="margin-top:10px;">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Important:</strong> Even a single confirmed fraud finding immediately elevates the rating to CRITICAL, regardless of total score.
                                Always check the Fraud Risk Indicators section (Section 5) before finalising your rating.
                            </div>

                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Document Version</label>
                                        <input type="text" class="form-control" value="3.0 (Cashless Operations Edition)" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Prepared by</label>
                                        <input type="text" class="form-control" value="Dr. Henry Lukama Chikweti, Ph.D" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Classification</label>
                                        <input type="text" class="form-control" value="INTERNAL USE ONLY" readonly style="color:#c0392b;font-weight:bold;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 2 — AUDIT ADMINISTRATION
                         ============================================= --}}
                    <div class="audit-step" id="step-2" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-folder-open-o"></i>&nbsp; Section 1 &mdash; Audit Administration</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Complete this section first. It establishes the formal record of the audit. <strong>Every field is mandatory.</strong>
                            </p>
                        </div>
                        <div style="padding:24px;">
                            {{-- Branch search-select — populated from offices table --}}
                            <div class="form-group">
                                <label>Branch Name <span class="text-danger">*</span></label>
                                <select class="form-control select2-branch" id="s1OfficeSelect" name="s1_office_id" style="width:100%;">
                                    <option value="">— Search and select a branch —</option>
                                    @foreach(\App\Models\Office::with('province', 'district')->where('active', 1)->orderBy('name')->get() as $office)
                                    <option value="{{ $office->id }}" data-code="{{ $office->external_id }}">
                                        {{ $office->name }}{{ $office->external_id ? ' ('.$office->external_id.')' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                                <div style="margin-top:4px;font-size:12px;">
                                    <span class="text-muted"><i class="fa fa-check-circle text-success"></i> <strong>How to verify:</strong> Check against official branch register or system records.</span>
                                    &nbsp;&nbsp;
                                    <span class="text-danger"><i class="fa fa-flag"></i> <strong>Red flag:</strong> Name differs from system record — possible confusion or wrong location.</span>
                                </div>
                            </div>

                            {{-- Auto-filled branch details (read-only, populated by JS on selection) --}}
                            <div id="s1BranchDetails" style="display:none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Branch Code / ID</label>
                                            <input type="text" class="form-control" name="s1_branch_code" id="s1BranchCode" readonly
                                                   style="background:#f9f9f9;" placeholder="Auto-filled">
                                            <div style="margin-top:4px;font-size:12px;">
                                                <span class="text-muted"><i class="fa fa-check-circle text-success"></i> <strong>How to verify:</strong> Cross-check with the branch management system.</span>
                                                &nbsp;&nbsp;
                                                <span class="text-danger"><i class="fa fa-flag"></i> <strong>Red flag:</strong> No code recorded — audit may be attributed to wrong branch.</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                    
                            </div>

                            @php $s1items = [
                                [
                                    'id'     => 's1_audit_date',
                                    'label'  => 'Audit Date',
                                    'verify' => 'Use today\'s actual date — do not back-date.',
                                    'flag'   => 'Date left blank or inconsistent with supporting documents.',
                                    'type'   => 'date',
                                    'ph'     => '',
                                ],
                                [
                                    'id'     => 's1_auditor_name',
                                    'label'  => 'Auditor Name & Title',
                                    'verify' => 'Sign and print your name on the cover page.',
                                    'flag'   => 'No name recorded — audit has no accountable owner.',
                                    'type'   => 'text',
                                    'ph'     => 'Full legal name and official job title',
                                ],
                                [
                                    'id'     => 's1_audit_scope',
                                    'label'  => 'Audit Scope',
                                    'verify' => 'Confirm scope with Provincial Manager, Administration, or Risk Manager before starting.',
                                    'flag'   => 'Scope so broad it is unworkable, or so narrow key risks are missed.',
                                    'type'   => 'select',
                                    'multiple' => true,
                                    'class'  => 'form-control select2',
                                    'placeholder' => 'Select one or more applicable audit sections',
                                    'options' => [
                                        'Section 1 — Audit Administration',
                                        'Section 2 — Withinhere Wallet & Digital Payment Controls',
                                        'Section 3 — Loan Portfolio Integrity',
                                        'Section 4 — Collections & Recoveries',
                                        'Section 5 — Fraud Risk Indicators',
                                        'Section 6 — Staff & Process Compliance',
                                        'Section 7 — System & Control Environment',
                                        'Section 8 — Reporting & Governance',
                                        'Section 9 — Audit Conclusion & Sign-Off'
                                    ],
                                ],
                                [
                                    'id'     => 's1_period_start',
                                    'label'  => 'Period Under Review (Start Date)',
                                    'verify' => 'Match to the reporting period covered by the Withinhere wallet transaction history.',
                                    'flag'   => 'Period unclear — makes it impossible to match wallet transactions and records.',
                                    'type'   => 'date',
                                    'ph'     => '',
                                ],
                                [
                                    'id'     => 's1_period_end',
                                    'label'  => 'Period Under Review (End Date)',
                                    'verify' => 'Match to the reporting period covered by the Withinhere wallet transaction history.',
                                    'flag'   => 'Period unclear — makes it impossible to match wallet transactions and records.',
                                    'type'   => 'date',
                                    'ph'     => '',
                                ],
                            ]; @endphp

                            @foreach($s1items as $item)
                            <div class="form-group">
                                <label>{{ $item['label'] }} <span class="text-danger">*</span></label>
                                @if($item['type'] == 'select')
                                    <select class="{{ $item['class'] ?? 'form-control' }}" name="{{ $item['id'] }}{{ $item['multiple'] ? '[]' : '' }}" {{ $item['multiple'] ? 'multiple' : '' }}
                                            {{ !empty($item['placeholder']) ? 'data-placeholder="'.$item['placeholder'].'"' : '' }} style="width:100%;">
                                        @if(!empty($item['placeholder']))
                                            <option></option>
                                        @endif
                                        @foreach($item['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $item['type'] }}" class="form-control" name="{{ $item['id'] }}" placeholder="{{ $item['ph'] }}">
                                @endif
                                <div style="margin-top:4px;font-size:12px;">
                                    <span class="text-muted"><i class="fa fa-check-circle text-success"></i> <strong>How to verify:</strong> {{ $item['verify'] }}</span>
                                    &nbsp;&nbsp;
                                    <span class="text-danger"><i class="fa fa-flag"></i> <strong>Red flag:</strong> {{ $item['flag'] }}</span>
                                </div>
                            </div>
                            @endforeach

                            <div class="form-group">
                                <label>Audit Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="s1_audit_type">
                                    <option value="">-- Select --</option>
                                    <option value="routine">Routine</option>
                                    <option value="surprise">Surprise / Unannounced</option>
                                    <option value="follow_up">Follow-Up</option>
                                    <option value="special">Special Investigation</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Unannounced Audit Confirmed? <span class="text-danger">*</span></label>
                                <select class="form-control" name="s1_unannounced">
                                    <option value="">-- Select --</option>
                                    <option value="yes">Yes — branch was NOT notified in advance</option>
                                    <option value="no">No — branch was notified (state reason below)</option>
                                </select>
                                <div style="margin-top:4px;font-size:12px;">
                                    <span class="text-muted"><i class="fa fa-check-circle text-success"></i> <strong>How to verify:</strong> Record in your notes the time of arrival and who was present.</span>
                                    &nbsp;&nbsp;
                                    <span class="text-danger"><i class="fa fa-flag"></i> <strong>Red flag:</strong> Branch was given advance notice without authorisation from Risk Manager.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Branch Manager Present? <span class="text-danger">*</span></label>
                                <select class="form-control" name="s1_manager_present">
                                    <option value="">-- Select --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No — acting manager present</option>
                                    <option value="absent">No — no manager present</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Branch Manager Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="s1_manager_name" placeholder="Full name of Branch Manager (or acting)">
                            </div>
                            <div class="form-group">
                                <label>Opening Remarks / Context <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="s1_opening_remarks" rows="3" placeholder="Briefly describe the purpose and scope of this audit visit."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 3 — WITHINHERE WALLET & DIGITAL PAYMENT CONTROLS
                         ============================================= --}}
                    <div class="audit-step" id="step-3" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-mobile"></i>&nbsp; Section 2 &mdash; Withinhere Wallet &amp; Digital Payment Controls</h4>
                            <div class="callout callout-danger" style="margin:10px 0 0;">
                                <h5><i class="fa fa-ban"></i> Zero-Cash Policy</h5>
                                <p style="margin:0;">
                                    This institution does not permit any physical cash to be held or handled at branch level at any time.
                                    All client payments must be received through the <strong>Withinhere app</strong>, the <strong>Whence Financial Services app</strong>,
                                    or the <strong>company mobile money lines</strong>. All loan disbursements must be made exclusively through the
                                    <strong>Withinhere branch wallet</strong>.
                                    <span class="expand-toggle" style="cursor:pointer; color:#c0392b; font-weight:bold;" onclick="toggleZeroCashPolicy()"> See More</span>
                                </p>
                                <div id="zeroCashPolicyExpanded" style="display:none;">
                                    <p style="margin:8px 0 0;">
                                        A payment received via mobile money is <strong>NOT confirmed</strong> until it has been
                                        transferred into the Withinhere branch wallet. Only the Branch Manager is authorised to initiate this transfer,
                                        and it must occur in the same transaction — no delay is permitted.
                                    </p>
                                    <p style="margin:8px 0 0;" class="text-muted">
                                        This section verifies that the branch is operating in full compliance with the cashless policy and that the
                                        Withinhere wallet is being used correctly as the single authorised channel for all money movement.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div style="padding:24px;">
                            @php $s2items = [
                                [
                                    'id'     => 's2_1',
                                    'label'  => 'Zero physical cash confirmed at branch',
                                    'check'  => 'No cash of any kind — coins, notes, petty cash, or any float — should be present anywhere at the branch at any time.',
                                    'verify' => 'Physically inspect the branch: till drawers, desks, safe (if any), reception area, and personal bags/wallets of staff on duty. Record any cash found: amount, location, and name of person holding it.',
                                    'flag'   => 'Any physical cash found at the branch — regardless of amount or explanation — is an immediate red flag. Escalate to Provincial Manager and Risk Manager the same day.',
                                ],
                                [
                                    'id'     => 's2_2',
                                    'label'  => 'All client payments received via authorised channels only',
                                    'check'  => 'Clients must pay through one of three authorised channels only: (1) Withinhere app, (2) Whence Financial Services app, or (3) company mobile money lines.',
                                    'verify' => 'Review the Withinhere wallet transaction log and mobile money records for the period. Confirm all inflows came through one of the three authorised channels. Ask staff how they instruct clients to pay.',
                                    'flag'   => 'A client paid via a personal mobile money number belonging to a staff member, or by handing cash to a staff member — neither is an authorised channel.',
                                ],
                                [
                                    'id'     => 's2_3',
                                    'label'  => 'Mobile money payments transferred to Withinhere wallet immediately',
                                    'check'  => 'When a client pays through the company mobile money lines, the Branch Manager must transfer that money into the Withinhere branch wallet in the same transaction. No gap is permitted.',
                                    'verify' => 'Cross-reference mobile money transaction timestamps with Withinhere wallet inflow timestamps. Every mobile money receipt must have a corresponding wallet transfer with the same amount and an equal or earlier timestamp.',
                                    'flag'   => 'Mobile money line shows receipt of K2,500 at 10:14 AM; Withinhere wallet shows no corresponding inflow until 4:45 PM — a 6-hour gap is a policy breach.',
                                ],
                                [
                                    'id'     => 's2_4',
                                    'label'  => 'Only Branch Manager initiates mobile-money-to-wallet transfers',
                                    'check'  => 'No other staff member — not the loan officer, not the Recoveries Representative, not a relief manager (unless officially acting) — is authorised to transfer funds from the mobile money line to the Withinhere wallet.',
                                    'verify' => 'Review the Withinhere wallet and mobile money transfer logs. Check who initiated each transfer. Record any transfer initiated by a person other than the Branch Manager.',
                                    'flag'   => 'Transfer log shows 14 wallet inflow transfers in the period; 6 were initiated by the loan officer — those 6 are unauthorised transfers.',
                                ],
                                [
                                    'id'     => 's2_5',
                                    'label'  => 'Withinhere wallet balance reconciles with loan system records',
                                    'check'  => 'The balance shown in the Withinhere branch wallet must match the total funds shown in the loan management system for the same period. Any difference must have a documented explanation.',
                                    'verify' => 'Request the Withinhere wallet statement for the period. Compare total inflows and outflows to the loan system\'s disbursement and collection records. List and investigate every variance.',
                                    'flag'   => 'Loan system shows total collections of K85,000 for March; Withinhere wallet shows inflows of K61,000 — a K24,000 gap with no explanation.',
                                ],
                                [
                                    'id'     => 's2_6',
                                    'label'  => 'No loans disbursed via mobile money or any channel other than Withinhere',
                                    'check'  => 'Every loan, without exception, must be disbursed through the Withinhere branch wallet. A loan disbursed by sending mobile money directly from the branch mobile money line is a serious policy violation.',
                                    'verify' => 'Pull disbursement records for the period. For each disbursement, verify the outflow came from the Withinhere wallet. Cross-check with the Withinhere transaction log.',
                                    'flag'   => 'System shows K10,000 disbursed to Client #44 on 12 March; Withinhere wallet shows no outflow of that amount on that date — loan was disbursed via another channel.',
                                ],
                                [
                                    'id'     => 's2_7',
                                    'label'  => 'Client disbursement channel preference documented',
                                    'check'  => 'When a client receives a loan, their preference to receive it into their personal Withinhere account, mobile money number, or bank must be documented in the loan file and recorded in the system.',
                                    'verify' => 'Pull 5 loan files. For each, check that the disbursement channel preference is noted and that the actual disbursement matches the documented preference. Verify the Withinhere outflow shows the correct recipient.',
                                    'flag'   => 'Client file states disbursement to Withinhere account; Withinhere log shows outflow to a mobile number not linked to the client — funds may have gone to wrong person.',
                                ],
                                [
                                    'id'     => 's2_8',
                                    'label'  => 'No inter-branch transfers without Withinhere audit compliance and authorisation',
                                    'check'  => 'Funds may not be transferred from one branch\'s Withinhere wallet to another without a formal authorisation form reviewed and signed by a supervisor independent of the transfer.',
                                    'verify' => 'Check the Withinhere wallet transaction log for any outflows to other branch wallets. Every such transfer must have a corresponding signed authorisation form on file.',
                                    'flag'   => 'Branch transferred K50,000 to another branch wallet with no authorisation form — possible diversion of funds between branches.',
                                ],
                                [
                                    'id'     => 's2_9',
                                    'label'  => 'Withinhere wallet audit trail reviewed',
                                    'check'  => 'The Withinhere system records every transaction: who initiated it, the amount, the time, and the recipient. This log must be reviewed during every audit.',
                                    'verify' => 'Request the full Withinhere wallet transaction log for the period. Look for: transactions outside business hours, transfers to unknown recipients, reversed transactions, or entries with no matching loan record.',
                                    'flag'   => 'Withinhere log shows a K7,000 outflow at 11:52 PM initiated by the Branch Manager with no matching loan disbursement record — suspicious activity outside business hours.',
                                ],
                                [
                                    'id'     => 's2_10',
                                    'label'  => 'Exception or error transactions investigated and resolved',
                                    'check'  => 'Any transaction that failed, was reversed, or flagged as an exception in the Withinhere system must have a written explanation and resolution record.',
                                    'verify' => 'Request the exception/error log from the Withinhere system for the period. For each exception, ask for the written explanation and the outcome.',
                                    'flag'   => 'Three failed transfer attempts appear in the Withinhere log with no explanation or follow-up action recorded.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s2items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label>Section 2 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s2_notes" rows="3" placeholder="Record any additional findings, exceptions, or supporting observations for this section."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 4 — LOAN PORTFOLIO INTEGRITY
                         ============================================= --}}
                    <div class="audit-step" id="step-4" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-file-text-o"></i>&nbsp; Section 3 &mdash; Loan Portfolio Integrity</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Loan files are the paper trail for every kwacha disbursed. A missing document is not just an administrative lapse —
                                it is a potential fraud marker. <strong>Treat every incomplete file seriously.</strong>
                                All disbursements must trace back to the Withinhere wallet.
                            </p>
                        </div>
                        <div style="padding:24px;">
                            @php $s3items = [
                                [
                                    'id'     => 's3_1',
                                    'label'  => 'Client files complete & verified',
                                    'check'  => 'Every active loan file must contain: national ID copy, signed loan application, credit appraisal, approval signature at correct authority level, and a Withinhere disbursement receipt.',
                                    'verify' => 'Pull 10 random active loan files. Check each for the five required documents. Record any missing items.',
                                    'flag'   => 'File #401 missing client signature and national ID copy — loan should not have been disbursed.',
                                ],
                                [
                                    'id'     => 's3_2',
                                    'label'  => 'Loan approvals within authorised limits',
                                    'check'  => 'Each staff member has a maximum loan amount they are allowed to approve. No one may approve above their authorised limit.',
                                    'verify' => 'List approvals for the period. Compare each to the approver\'s signed authority letter on file.',
                                    'flag'   => 'Officer with K3,500 limit approved a K5,000 loan — a serious control breach.',
                                ],
                                [
                                    'id'     => 's3_3',
                                    'label'  => 'No ghost clients (verify via phone calls)',
                                    'check'  => 'A ghost client is a person who does not exist or who never received the loan but whose name is on the system.',
                                    'verify' => 'Randomly select 15 active loan clients. Call them on the phone number on file. Ask: (1) Are you a client? (2) Did you receive the loan via Withinhere? Record all responses.',
                                    'flag'   => '2 out of 15 called clients said they have never received a loan from this branch — possible ghost loans.',
                                ],
                                [
                                    'id'     => 's3_4',
                                    'label'  => 'Loan disbursements match Withinhere wallet outflows',
                                    'check'  => 'Every loan shown as disbursed in the system must correspond to a matching outflow in the Withinhere branch wallet transaction log on the same date.',
                                    'verify' => 'Compare system disbursement total for the period with Withinhere wallet outflows on those same days. Every disbursement must have a matching wallet transaction.',
                                    'flag'   => 'System shows K50,000 disbursed on 15 March; Withinhere wallet shows only K30,000 in outflows that day — K20,000 gap requires investigation.',
                                ],
                                [
                                    'id'     => 's3_5',
                                    'label'  => 'Interest rates applied correctly',
                                    'check'  => 'All clients must be charged the interest rate stated in their loan agreement and in company policy. No unauthorised rates.',
                                    'verify' => 'Select 5 files. Calculate expected interest. Compare to actual charges on client ledger.',
                                    'flag'   => 'Policy rate: 15%; client file shows 20% — either an error or an undisclosed fee arrangement.',
                                ],
                                [
                                    'id'     => 's3_6',
                                    'label'  => 'No expired or rolled-over loans without re-approval',
                                    'check'  => 'Loans that have reached maturity cannot simply be extended (rolled over) without a fresh credit appraisal and approval.',
                                    'verify' => 'Identify any loans past their maturity date. Check if they have a re-approval document.',
                                    'flag'   => 'Loan #207 matured January 2026 but is still listed as active with no re-approval on file.',
                                ],
                                [
                                    'id'     => 's3_7',
                                    'label'  => 'Loan purpose verification conducted',
                                    'check'  => 'The stated purpose of each loan should be plausible and verified.',
                                    'verify' => 'For loans taken to pay school fees, check if it is school paying period or if the amount is adequate for such fees.',
                                    'flag'   => 'Loan amount not adequate for stated school fees — risk of diversion.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s3items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Active Loans (System)</label>
                                        <input type="number" class="form-control" id="s3_total_active" name="s3_total_active" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Number of Files Sampled</label>
                                        <input type="number" class="form-control" name="s3_files_sampled" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Number of Incomplete Files Found</label>
                                        <input type="number" class="form-control" id="s3_incomplete_files" name="s3_incomplete_files" placeholder="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Section 3 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s3_notes" rows="3" placeholder="List file reference numbers with missing documents and describe the nature of each gap."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 5 — COLLECTIONS & RECOVERIES
                         ============================================= --}}
                    <div class="audit-step" id="step-5" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-money"></i>&nbsp; Section 4 &mdash; Collections &amp; Recoveries</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Under the cashless policy, all client repayments must be received through the Withinhere app, the Whence Financial Services app,
                                or company mobile money lines and <strong>immediately transferred to the Withinhere branch wallet</strong>.
                                <span class="expand-toggle" style="cursor:pointer; color:#c0392b; font-weight:bold;" onclick="toggleCollectionsPolicy()"> See More</span>
                            </p>
                            <div id="collectionsPolicyExpanded" style="display:none;">
                                <p class="text-muted" style="margin:8px 0 0;">
                                    This section verifies that every collection is fully accounted for in the wallet.
                                </p>
                            </div>
                        </div>
                        <div style="padding:24px;">
                            @php $s4items = [
                                [
                                    'id'     => 's4_1',
                                    'label'  => 'Collections recorded in Withinhere match total repayments due',
                                    'check'  => 'The total repayments received and recorded in the Withinhere wallet for the period must equal the total repayments that were scheduled and collected from clients.',
                                    'verify' => 'Pull the collections schedule from the loan system. Compare to total Withinhere wallet inflows from client repayments for the same period. Investigate every shortfall.',
                                    'flag'   => 'Collections schedule shows K40,000 due and reportedly collected for March; Withinhere wallet shows only K28,000 in repayment inflows — K12,000 is unaccounted for.',
                                ],
                                [
                                    'id'     => 's4_2',
                                    'label'  => 'No recycling of collections — all repayments go to Withinhere wallet before any disbursement',
                                    'check'  => 'Recycling means using funds received as repayments to fund new loan disbursements without first recording them in the Withinhere wallet. This is prohibited and hides the true financial position.',
                                    'verify' => 'Cross-check the timestamps of wallet inflows (repayments) and outflows (disbursements) on the same day. If a disbursement occurred before the repayment was recorded in the wallet, investigate.',
                                    'flag'   => 'Withinhere log shows K10,000 disbursement at 9:30 AM; the K10,000 repayment that funded it only appears in the wallet at 2:00 PM — disbursement funded outside proper channel.',
                                ],
                                [
                                    'id'     => 's4_3',
                                    'label'  => 'Collections logs signed by two staff and matched to Withinhere receipts',
                                    'check'  => 'Every daily collections record must be signed by at least two people: the collecting officer and a supervisor. Each entry must have a corresponding Withinhere wallet receipt or mobile money transfer record.',
                                    'verify' => 'Review each collections log entry. Count those with fewer than two signatures. For each entry, verify the corresponding Withinhere transaction reference number is recorded.',
                                    'flag'   => 'March 14 log has only the loan officer\'s signature; no Withinhere transaction reference is recorded for 3 of the entries.',
                                ],
                                [
                                    'id'     => 's4_4',
                                    'label'  => 'Delinquency managed per policy',
                                    'check'  => 'Overdue loans must be followed up within the timeframe set in company policy (e.g., call client after 7 days overdue). Delays allow small problems to grow.',
                                    'verify' => 'Pull the delinquency report. For each overdue loan, check if the required follow-up action was taken on time.',
                                    'flag'   => 'Policy: call after 7 days; branch waited 30 days for 12 loans — weak recovery culture.',
                                ],
                                [
                                    'id'     => 's4_5',
                                    'label'  => 'Timely handover to Recoveries Department',
                                    'check'  => 'Loans that reach a certain days-past-due threshold must be transferred to the Recoveries team within the policy timeframe.',
                                    'verify' => 'List all loans past the handover threshold. Check if handover forms exist and are dated.',
                                    'flag'   => 'Loan overdue 90 days still in branch file — no handover initiated.',
                                ],
                                [
                                    'id'     => 's4_6',
                                    'label'  => 'Write-offs approved at correct authority level',
                                    'check'  => 'No loan should be written off without documented approval from the appropriate authority level (typically Head Office).',
                                    'verify' => 'Pull any write-offs from the period. Verify each has a signed Head Office approval.',
                                    'flag'   => 'K5,000 loan written off with only Branch Manager signature — insufficient authority.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s4items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Collections per System (Period)</label>
                                        <input type="text" class="form-control" id="s4_system_collections" name="s4_system_collections" placeholder="e.g. K 450,000.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total Collections per Withinhere Wallet (Period)</label>
                                        <input type="text" class="form-control" id="s4_wallet_collections" name="s4_wallet_collections" placeholder="e.g. K 450,000.00">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Section 4 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s4_notes" rows="3" placeholder="Describe any variances between system records and wallet data, and note any unreconciled items."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 6 — FRAUD RISK INDICATORS
                         ============================================= --}}
                    <div class="audit-step" id="step-6" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-exclamation-triangle"></i>&nbsp; Section 5 &mdash; Fraud Risk Indicators</h4>
                            <div class="callout callout-warning" style="margin:10px 0 0;">
                                <p style="margin:0;">
                                    This section identifies patterns that, individually, might have an innocent explanation, but together signal a
                                    high likelihood of ongoing fraud.
                                    <span class="expand-toggle" style="cursor:pointer; color:#c0392b; font-weight:bold;" onclick="toggleFraudWarning()"> See More</span>
                                </p>
                                <div id="fraudWarningExpanded" style="display:none;">
                                    <p style="margin:8px 0 0;">
                                        <strong>If you tick ✗ on three or more items in this section, treat it as a Critical finding and contact the Risk Manager immediately before leaving the branch.</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div style="padding:24px;">
                            @php $s5items = [
                                [
                                    'id'     => 's5_1',
                                    'label'  => 'Pending disbursements investigated',
                                    'check'  => 'A disbursement is "pending" when a loan has been approved in the system but no corresponding outflow appears in the Withinhere wallet. Legitimate pending items resolve within 1–2 business days.',
                                    'verify' => 'List all disbursements pending for more than 7 days. Check whether a Withinhere wallet outflow exists for each. Ask the Branch Manager for a written explanation of any that do not.',
                                    'flag'   => '15 loans pending more than 7 days; Withinhere wallet shows no outflows for any of them — possible fraudulent approvals with no intention to disburse.',
                                ],
                                [
                                    'id'     => 's5_2',
                                    'label'  => 'Unusual loan volume spikes verified',
                                    'check'  => 'A sudden large increase in loans issued — especially near month-end or in target periods — is a known fraud pattern.',
                                    'verify' => 'Compare monthly loan counts for the last 6 months. Any month where volume is more than double the average requires 100% file review and Withinhere wallet verification.',
                                    'flag'   => 'Branch averaged 10 loans/month; last month shows 50 loans — all files and corresponding Withinhere outflows must be reviewed individually.',
                                ],
                                [
                                    'id'     => 's5_3',
                                    'label'  => 'Wallet flow inconsistencies investigated',
                                    'check'  => 'A branch reporting strong performance (high collections, target achievement) but showing low Withinhere wallet inflows is a contradiction that warrants immediate investigation.',
                                    'verify' => 'Cross-reference the performance report against the Withinhere wallet statement for the same period.',
                                    'flag'   => 'Performance report shows 85% target achieved; Withinhere wallet inflows for the period total only K3,200 — where did collections go?',
                                ],
                                [
                                    'id'     => 's5_4',
                                    'label'  => 'Staff performance anomalies reviewed',
                                    'check'  => 'If one staff member is consistently outperforming all others by a very large margin — especially in a branch with overall weak Withinhere wallet inflows — this may indicate ghost loans or manipulated records.',
                                    'verify' => 'Compare individual collection records against Withinhere wallet inflows. Identify any staff member attributed with collections not reflected in wallet deposits.',
                                    'flag'   => 'One loan officer claims K60,000 of K70,000 branch collections; Withinhere wallet shows only K9,000 in total inflows for the period.',
                                ],
                                [
                                    'id'     => 's5_5',
                                    'label'  => 'All early warning signs addressed per Playbook',
                                    'check'  => 'The Theft & Fraud Prevention Playbook lists specific early warning signs and the required intervention for each. Every sign that was triggered in the period must have a documented response.',
                                    'verify' => 'Ask for the Playbook intervention log. Every triggered sign must have a response date, action taken, and outcome recorded.',
                                    'flag'   => 'Sign #2 (missed targets for 2 consecutive months) triggered in February — no intervention documented.',
                                ],
                                [
                                    'id'     => 's5_6',
                                    'label'  => 'No client complaints about unrecorded payments',
                                    'check'  => 'If clients report making payments that do not appear on their Withinhere account or loan record, this strongly suggests staff collected money through an unauthorised channel.',
                                    'verify' => 'Review the complaints register. Call 5 clients whose loans show as delinquent and ask if they have made payments that did not reflect on their account. Check Withinhere wallet for corresponding inflows.',
                                    'flag'   => 'Client #82 reports paying K1,500 in March via mobile money; their Withinhere account shows no payment and the mobile money line shows no transfer to the wallet for that amount.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Fraud Indicator</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓ Not Present</th>
                                        <th style="width:5%;text-align:center;">✗ Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s5items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                        <td style="text-align:center;"><input type="radio" name="{{ $item['id'] }}" value="not_present"></td>
                                        <td style="text-align:center;"><input type="radio" name="{{ $item['id'] }}" value="present" class="fraud-indicator-radio fail-radio"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗ Present)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="fraudAlert" class="alert alert-danger" style="display:none;margin-top:10px;">
                                <i class="fa fa-exclamation-circle"></i>
                                <strong>Critical Alert:</strong> Three or more fraud indicators have been marked as Present.
                                Contact the Risk Manager <strong>immediately</strong> before leaving the branch.
                            </div>
                            <div class="form-group">
                                <label>Section 5 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s5_notes" rows="3" placeholder="Describe each indicator that is present, with supporting evidence or observations."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 7 — STAFF & PROCESS COMPLIANCE
                    ============================================= --}}
                    <div class="audit-step" id="step-7" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-users"></i>&nbsp; Section 6 &mdash; Staff &amp; Process Compliance</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Most fraud is enabled by weak process controls — one person having too much unsupervised access.
                                <span class="expand-toggle" style="cursor:pointer; color:#c0392b; font-weight:bold;" onclick="toggleStaffingPolicy()"> See More</span>
                            </p>
                            <div id="staffingPolicyExpanded" style="display:none;">
                                <p class="text-muted" style="margin:8px 0 0;">
                                    This section checks whether the staffing structure is designed to <strong>prevent, not enable</strong>, misconduct.
                                </p>
                            </div>
                        </div>
                        <div style="padding:24px;">
                            @php $s6items = [
                                [
                                    'id'     => 's6_1',
                                    'label'  => 'No staff receiving payments via unauthorised channels',
                                    'check'  => 'All salary and any staff advances must be paid through the official payroll system. No staff member should receive payment from the Withinhere wallet or mobile money lines except through authorised payroll.',
                                    'verify' => 'Review the payroll record and match it to official bank/wallet transfer records. Ask staff if they have ever received payment directly from the branch wallet or mobile money lines.',
                                    'flag'   => 'Branch manager made a K500 Withinhere transfer to a staff member\'s personal account — not on payroll records.',
                                ],
                                [
                                    'id'     => 's6_2',
                                    'label'  => 'Segregation of duties enforced',
                                    'check'  => 'The same person must not initiate Withinhere transfers, record transactions in the loan system, AND approve loans. These three functions must always be separated.',
                                    'verify' => 'List: who initiates wallet transfers? Who records in the loan system? Who approves loans? If any name appears in more than one column, that is a segregation failure.',
                                    'flag'   => 'The Branch Manager initiates all wallet transfers AND approves all loans — no independent check exists.',
                                ],
                                [
                                    'id'     => 's6_3',
                                    'label'  => 'All staff adhere to loan procedures',
                                    'check'  => 'Every loan must follow the full procedure: client interview, credit check, committee review (where required), and documented approval. Skipping steps is a process failure.',
                                    'verify' => 'Pull 5 loan files and trace each through the procedure checklist. Note any missing steps.',
                                    'flag'   => 'Loan #305 has no record of a client interview or credit check — disbursed based on manager\'s verbal instruction alone.',
                                ],
                                [
                                    'id'     => 's6_4',
                                    'label'  => 'No override of system controls',
                                    'check'  => 'The loan management and Withinhere systems have built-in controls. Overriding these controls requires formal documented authorisation.',
                                    'verify' => 'Request the system exception/override log and the Withinhere admin log for the period. Every override entry must have a supporting authorisation document.',
                                    'flag'   => 'Withinhere log shows a transfer limit override — no authorisation document found.',
                                ],
                                [
                                    'id'     => 's6_5',
                                    'label'  => 'Staff accountability documented',
                                    'check'  => 'Every staff member must have a signed accountability document on file stating their Withinhere wallet access level, approval authority, and acknowledgement of the cashless operations policy.',
                                    'verify' => 'Pull HR file for each branch staff member. Check for signed accountability form that references the cashless policy.',
                                    'flag'   => 'Loan officer has no signed accountability form and is unaware of the rule that only the Branch Manager initiates wallet transfers.',
                                ],
                                [
                                    'id'     => 's6_6',
                                    'label'  => 'Staff leave rotation policy followed',
                                    'check'  => 'Requiring all staff to take leave — and having another person cover their role while absent — is a key anti-fraud control. It forces exposure of any manipulation.',
                                    'verify' => 'Check leave records for the past year. Identify any staff member who has not taken at least one week of leave with a designated cover person.',
                                    'flag'   => 'Branch Manager has not taken leave in 18 months — the sole person authorised to initiate wallet transfers with no cover arrangement.',
                                ],
                                [
                                    'id'     => 's6_7',
                                    'label'  => 'No inappropriate relationships between staff and clients',
                                    'check'  => 'Staff members should not have personal financial relationships with clients (e.g., sharing in loan proceeds, acting as guarantors for multiple clients, receiving payments to personal mobile money numbers).',
                                    'verify' => 'Review guarantor records. Check mobile money transfer logs for transfers to staff personal numbers. Ask staff to declare any personal relationships with active clients.',
                                    'flag'   => 'Loan officer\'s personal mobile number received a K2,000 transfer from a client — possible unauthorised collection.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s6items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Branch Staff</label>
                                        <input type="number" class="form-control" id="s6_total_staff" name="s6_total_staff" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Staff Present During Audit</label>
                                        <input type="number" class="form-control" name="s6_staff_present" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Staff Absent / Unaccounted For</label>
                                        <input type="number" class="form-control" name="s6_staff_absent" placeholder="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Section 6 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s6_notes" rows="3" placeholder="Note any segregation of duties gaps, staffing concerns, or process violations observed."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 8 — SYSTEM & CONTROL ENVIRONMENT
                         ============================================= --}}
                    <div class="audit-step" id="step-8" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-laptop"></i>&nbsp; Section 7 &mdash; System &amp; Control Environment</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                The loan management system and the Withinhere platform are only as strong as the controls surrounding them.
                                This section checks whether technology is being used correctly and <strong>no one is working around it</strong>.
                            </p>
                        </div>
                        <div style="padding:24px;">
                            @php $s7items = [
                                [
                                    'id'     => 's7_1',
                                    'label'  => 'Withinhere and loan system prevent unauthorised transactions',
                                    'check'  => 'Both systems should block any transaction that exceeds a user\'s authorised level.',
                                    'verify' => 'Ask a junior officer to attempt a transfer or loan approval above their limit in both systems. Observe whether both block the action.',
                                    'flag'   => 'Withinhere allowed a loan officer to initiate a wallet transfer — only the Branch Manager should be able to do this.',
                                ],
                                [
                                    'id'     => 's7_2',
                                    'label'  => 'Audit trail enabled & reviewed in both systems',
                                    'check'  => 'Both the loan management system and Withinhere must record every action: who logged in, what they did, and when. These logs must be reviewed at least monthly.',
                                    'verify' => 'Request the audit log from both systems for the period. Look for actions outside business hours, deleted records, or bulk edits in either system.',
                                    'flag'   => 'Withinhere log shows a K7,000 transfer initiated at 11:52 PM — no staff should be operating the wallet outside business hours.',
                                ],
                                [
                                    'id'     => 's7_3',
                                    'label'  => 'Exception reports generated & reviewed',
                                    'check'  => 'Exception reports flag unusual activity automatically (e.g., many edits to one record, repeated failed transactions, reversed transfers). These must be reviewed and signed off.',
                                    'verify' => 'Ask to see the last three months of exception reports from both systems and the sign-off log.',
                                    'flag'   => 'Exception reports are generated by Withinhere but stored unread — no evidence of review.',
                                ],
                                [
                                    'id'     => 's7_4',
                                    'label'  => 'Access controls properly assigned in both systems',
                                    'check'  => 'Staff should only have system access to functions their job requires. A loan officer should not have wallet transfer initiation rights in Withinhere.',
                                    'verify' => 'Request a user access report from both the loan system and Withinhere. Compare each user\'s access level to their job description.',
                                    'flag'   => 'Three former employees still have active Withinhere or LMS logins — access was not revoked when they left.',
                                ],
                                [
                                    'id'     => 's7_5',
                                    'label'  => 'No manual workarounds bypassing controls',
                                    'check'  => 'If staff are keeping handwritten registers, spreadsheets, or notebooks to track payments or loans outside the system, controls are being bypassed.',
                                    'verify' => 'Walk the branch and look for notebooks, loose papers, or parallel spreadsheets tracking loans or collections.',
                                    'flag'   => 'Loan officer keeps a personal notebook of client payments — claims Withinhere is "too slow". This creates an uncontrolled parallel record.',
                                ],
                                [
                                    'id'     => 's7_6',
                                    'label'  => 'Passwords and login security maintained in both systems',
                                    'check'  => 'Each staff member must have their own unique login for both the loan system and Withinhere. Sharing passwords makes the audit trail unreliable.',
                                    'verify' => 'Ask each staff member to log into both systems while you watch. If any share a password or one person logs in for another, record it.',
                                    'flag'   => 'Manager logs into Withinhere using the loan officer\'s credentials "to save time" — audit trail is now compromised.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s7items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label>Section 7 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s7_notes" rows="3" placeholder="Describe any system access issues, workarounds, or technology control gaps observed."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 9 — REPORTING & GOVERNANCE
                         ============================================= --}}
                    <div class="audit-step" id="step-9" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-bar-chart"></i>&nbsp; Section 8 &mdash; Reporting &amp; Governance</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Accurate reporting is the foundation of institutional trust. This section checks whether the branch is reporting
                                honestly and whether management oversight is functioning.
                                <strong>All performance figures must be verifiable against Withinhere wallet data.</strong>
                            </p>
                        </div>
                        <div style="padding:24px;">
                            @php $s8items = [
                                [
                                    'id'     => 's8_1',
                                    'label'  => 'Accurate reporting of performance metrics',
                                    'check'  => 'All figures reported to Head Office [disbursements, collections, Portfolio At Risk (PAR), client numbers] must match the Withinhere wallet transaction records and loan system data you can verify at the branch.',
                                    'verify' => 'Request the last three monthly reports submitted to Head Office. Cross-check five key figures against the Withinhere wallet statement and loan system records.',
                                    'flag'   => 'Report to Head Office shows 90% collection rate; Withinhere wallet inflows for the period reflect only 45% of that figure — deliberate manipulation.',
                                ],
                                [
                                    'id'     => 's8_2',
                                    'label'  => 'No manipulation of KPIs via re-loans',
                                    'check'  => 'A common manipulation technique is to issue a new loan to a client on the same day they repay the old one, making the repayment look like genuine recovery.',
                                    'verify' => 'Identify any clients for whom a Withinhere inflow (repayment) and outflow (disbursement) occurred on the same day. Check if this was policy-compliant and approved.',
                                    'flag'   => 'Client repaid K4,900 and received a K4,900 new loan same day via Withinhere — repayment was not genuine income.',
                                ],
                                [
                                    'id'     => 's8_3',
                                    'label'  => 'All escalations documented',
                                    'check'  => 'Any time a staff member or supervisor identified a potential fraud sign, irregularity, or policy breach — including any detection of cash at the branch — they must have submitted a written escalation report.',
                                    'verify' => 'Review the escalation register. Cross-reference with the Playbook intervention log and any incident reports filed during the period.',
                                    'flag'   => 'Staff member found K200 at the branch in February — no escalation report was filed and no investigation was conducted.',
                                ],
                                [
                                    'id'     => 's8_4',
                                    'label'  => 'Previous audit findings addressed',
                                    'check'  => 'The findings from the most recent previous audit must have a written action plan and evidence of implementation.',
                                    'verify' => 'Obtain the previous audit report. For each finding, ask for the corrective action evidence.',
                                    'flag'   => 'Previous audit cited mobile money collections not being transferred to Withinhere wallet immediately — current review finds 4 instances of same-day delays this period.',
                                ],
                                [
                                    'id'     => 's8_5',
                                    'label'  => 'Compliance with governance framework',
                                    'check'  => 'The branch must be following all applicable company policies: lending policy, HR policy, cashless operations policy, and fraud response policy.',
                                    'verify' => 'Conduct a spot-check: pick one policy and ask three staff members a basic question about it. Specifically ask all staff: "What do you do if a client brings cash to the branch?"',
                                    'flag'   => 'Staff member says they would accept cash and "sort it out later" — demonstrates non-compliance with zero-cash policy.',
                                ],
                                [
                                    'id'     => 's8_6',
                                    'label'  => 'Board/management reports on file',
                                    'check'  => 'Minutes or summaries of any management visits, board oversight visits, or risk committee meetings relevant to the branch should be kept on file.',
                                    'verify' => 'Ask for the file of management visit reports. Each visit should have a dated record.',
                                    'flag'   => 'No record of any management visit to this branch in the past 6 months despite it being flagged in the previous audit.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Control Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s8items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label>Section 8 Overall Notes / Observations</label>
                                <textarea class="form-control" name="s8_notes" rows="3" placeholder="Note any reporting discrepancies, governance gaps, or unresolved prior findings."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                         STEP 10 — AUDIT CONCLUSION & SIGN-OFF
                         ============================================= --}}
                    <div class="audit-step" id="step-10" style="display:none;">
                        <div style="background:#f4f4f4;border-bottom:2px solid #c0392b;padding:14px 24px;">
                            <h4 style="margin:0;color:#c0392b;"><i class="fa fa-check-square-o"></i>&nbsp; Section 9 &mdash; Audit Conclusion &amp; Sign-Off</h4>
                            <p class="text-muted" style="margin:6px 0 0;">
                                Complete this section after reviewing all previous sections.
                                <strong>Do not rush this step</strong> — the conclusions you draw here directly determine what protective actions are taken.
                            </p>
                        </div>
                        <div style="padding:24px;">

                            {{-- Live risk score display --}}
                            <div class="row" style="margin-bottom:20px;">
                                <div class="col-md-12">
                                    <div class="box box-solid" id="riskScoreBox" style="border-color:#27ae60;">
                                        <div class="box-header" style="background:#27ae60;color:#fff;padding:10px 15px;">
                                            <h4 style="margin:0;"><i class="fa fa-tachometer"></i>&nbsp; Live Risk Score</h4>
                                        </div>
                                        <div class="box-body" style="text-align:center;padding:16px;">
                                            <h2 id="failCount" style="margin:0;font-size:48px;color:#27ae60;">0</h2>
                                            <p style="margin:4px 0 0;" class="text-muted">Total ✗ (Fail) items across all sections</p>
                                            <h4 id="riskRatingLabel" style="margin:10px 0 0;color:#27ae60;">🟢 LOW — Branch is compliant</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php $s9items = [
                                [
                                    'id'     => 's9_1',
                                    'label'  => 'Total ✗ Count & Risk Rating confirmed',
                                    'check'  => 'Count all items marked ✗ across all sections. Use the Risk Scoring Guide to assign a risk rating: Low / Medium / High / Critical.',
                                    'verify' => 'Tally ✗ marks from all sections. Record: Total items reviewed, Total ✗, Risk Rating.',
                                    'flag'   => 'Risk rating not completed — audit finding has no actionable classification.',
                                ],
                                [
                                    'id'     => 's9_2',
                                    'label'  => 'Key Findings documented (max 5, ranked by severity)',
                                    'check'  => 'Summarise the most significant problems discovered. Rank them by severity — most serious first. Use plain language anyone can understand.',
                                    'verify' => 'Write one sentence per finding. Example: "1. Withinhere wallet shows K12,000 less than loan system collections for March — unexplained."',
                                    'flag'   => 'Findings list contains 20 minor items and buries the one critical finding — key issues not distinguished from minor ones.',
                                ],
                                [
                                    'id'     => 's9_3',
                                    'label'  => 'Immediate Actions assigned (within 24–48 hours)',
                                    'check'  => 'Some findings require action before you leave or within the next two business days. List what must happen immediately, who is responsible, and the deadline.',
                                    'verify' => 'For each Critical or High finding, assign an immediate action with a named responsible person and a specific deadline.',
                                    'flag'   => 'No immediate actions assigned despite a confirmed wallet discrepancy — opportunity for cover-up increases.',
                                ],
                                [
                                    'id'     => 's9_4',
                                    'label'  => 'Recommendations for permanent fixes documented',
                                    'check'  => 'Beyond immediate actions, note what structural changes are needed to prevent recurrence. These go into the formal action plan reviewed by management.',
                                    'verify' => 'Write recommendations clearly and specifically, not vaguely.',
                                    'flag'   => 'Recommendations too vague to implement — "improve wallet usage" with no specifics.',
                                ],
                                [
                                    'id'     => 's9_5',
                                    'label'  => 'Follow-up Audit Date scheduled',
                                    'check'  => 'Every audit must end by scheduling the next one. The follow-up date should be proportionate to the risk rating found.',
                                    'verify' => 'Record the date, time, and names of auditors who will conduct the follow-up.',
                                    'flag'   => 'No follow-up date set — findings may go unresolved indefinitely.',
                                ],
                            ]; @endphp
                            <table class="table table-bordered table-striped" style="font-size:13px;">
                                <thead style="background:#c0392b;color:#fff;">
                                    <tr>
                                        <th style="width:28%;">Conclusion Item</th>
                                        <th style="width:22%;">What to Check</th>
                                        <th style="width:22%;">How to Verify</th>
                                        <th style="width:18%;">Red Flag Example</th>
                                        <th style="width:5%;text-align:center;">✓</th>
                                        <th style="width:5%;text-align:center;">✗</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($s9items as $item)
                                    <tr>
                                        <td><strong>{{ $item['label'] }}</strong></td>
                                        <td class="text-muted" style="font-size:12px;">{{ $item['check'] }}</td>
                                        <td style="font-size:12px;color:#1a5276;">{{ $item['verify'] }}</td>
                                        <td style="font-size:12px;color:#922b21;">{{ $item['flag'] }}</td>
                                         <td style="text-align:center;">
                                            <label class="audit-radio-wrap pass-wrap" title="Pass">
                                                <input type="radio" name="{{ $item['id'] }}" value="pass">
                                                <i class="fa fa-check-circle audit-icon"></i>
                                            </label>
                                        </td>
                                        <td style="text-align:center;">
                                            <label class="audit-radio-wrap fail-wrap" title="Fail">
                                                <input type="radio" name="{{ $item['id'] }}" value="fail" class="fail-radio">
                                                <i class="fa fa-times-circle audit-icon"></i>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <input type="text" class="form-control input-sm" name="{{ $item['id'] }}_notes" placeholder="Finding / Notes for this item (required if ✗)">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="form-group">
                                <label>Key Findings Summary (max 5, most serious first) <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="key_findings" rows="5"
                                    placeholder="1. [Most critical finding]&#10;2. [Second finding]&#10;3. [Third finding]&#10;4. [Fourth finding]&#10;5. [Fifth finding]"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Immediate Actions Required (within 24–48 hours) <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="immediate_actions" rows="4"
                                    placeholder="Action | Responsible Person | Deadline&#10;e.g. Freeze wallet access for Loan Officer X | Branch Manager | Today"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Recommendations (permanent structural fixes)</label>
                                <textarea class="form-control" name="recommendations" rows="4"
                                    placeholder="e.g. Enforce same-transaction mobile-money-to-wallet transfer policy via Withinhere system controls."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Follow-up Audit Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="followup_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Immediate Escalation Required? <span class="text-danger">*</span></label>
                                        <select class="form-control" name="escalation_required">
                                            <option value="">-- Select --</option>
                                            <option value="no">No</option>
                                            <option value="yes">Yes — Risk Manager notified</option>
                                            <option value="yes_pending">Yes — Notification pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 style="color:#c0392b;"><i class="fa fa-pencil"></i> Sign-Off</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Auditor Signature (Full Name) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="auditor_signature" placeholder="Type full name as signature">
                                        <p class="help-block" style="font-size:12px;">
                                            <i class="fa fa-flag text-danger"></i> Checklist submitted without signature — findings are unattributed and may be challenged.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date &amp; Time of Sign-Off <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" name="signoff_datetime">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Manager Acknowledgement (Full Name) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="manager_acknowledgement" placeholder="Branch Manager types full name to acknowledge receipt">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Manager Comments (Optional)</label>
                                        <input type="text" class="form-control" name="manager_comments" placeholder="Branch Manager may note any disagreement or context">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /#auditWizard --}}
                </form>
            </div>{{-- /.modal-body --}}

            {{-- Footer --}}
            <div class="modal-footer" style="background:#f9f9f9;border-top:1px solid #ddd;">
                <div class="pull-left">
                    <button type="button" class="btn btn-default" id="auditPrevBtn" onclick="auditWizardNav(-1)" style="display:none;">
                        <i class="fa fa-arrow-left"></i> Previous
                    </button>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="auditNextBtn" onclick="auditWizardNav(1)">
                        Next <i class="fa fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-success" id="auditSubmitBtn" style="display:none;" onclick="submitAuditChecklist()">
                        <i class="fa fa-check"></i> Submit Audit
                    </button>
                </div>
            </div>

        </div>{{-- /.modal-content --}}
    </div>{{-- /.modal-dialog --}}
</div>{{-- /#auditChecklistModal --}}
