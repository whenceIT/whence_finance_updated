@extends('layouts.master')

@section('title')
    Edit Case — {{ $case->case_number }}
@endsection

@section('content')

@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="row">
    <div class="col-md-10 col-lg-8">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-pencil"></i> Edit Case
                    <span class="label label-default" style="margin-left:8px">{{ $case->case_number }}</span>
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('recovery/case/' . $case->id . '/show') }}" class="btn btn-xs btn-default">
                        <i class="fa fa-arrow-left"></i> Back to Case
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ url('recovery/case/' . $case->id . '/update') }}">
                @csrf
                <div class="box-body">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin:0;padding-left:18px">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- ── CORE DETAILS ─────────────────────────────── --}}
                    <h4 class="text-primary" style="margin:0 0 14px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;font-weight:700">
                        <i class="fa fa-file-text-o"></i> Case Details
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Loan</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ $case->loan->loan_id ?? ('Loan #'.$case->loan_id) }} — {{ ($case->client->client_type ?? '') === 'business' ? ($case->client->full_name ?? '') : trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) }}">
                                <span class="help-block">Loan cannot be changed after case is opened</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                                <label>Recovery Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" id="category-select" required>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ $case->category === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($errors->has('category'))
                                    <span class="help-block text-danger">{{ $errors->first('category') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('origin_branch_id') ? 'has-error' : '' }}">
                                <label>Origin Branch <span class="text-danger">*</span></label>
                                <select name="origin_branch_id" class="form-control" required>
                                    <option value="">— Select Branch —</option>
                                    @foreach($offices as $branch)
                                    <option value="{{ $branch->id }}" {{ $case->origin_branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($errors->has('origin_branch_id'))
                                    <span class="help-block text-danger">{{ $errors->first('origin_branch_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supporting Branch <small class="text-muted">(optional)</small></label>
                                <select name="supporting_branch_id" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach($offices as $branch)
                                    <option value="{{ $branch->id }}" {{ $case->supporting_branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('loan_outstanding_amount') ? 'has-error' : '' }}">
                                <label>Loan Outstanding Amount (K) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">K</span>
                                    <input type="number" name="loan_outstanding_amount"
                                           class="form-control" step="0.01" min="0" required
                                           value="{{ old('loan_outstanding_amount', $case->loan_outstanding_amount) }}">
                                </div>
                                @if($errors->has('loan_outstanding_amount'))
                                    <span class="help-block text-danger">{{ $errors->first('loan_outstanding_amount') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    @foreach(['runaway_pending_confirmation'    => 'Pending Confirmation',
                                        'runaway_active_recovery'         => 'Active Recovery',
                                        'recovered_runaway'     => 'Recovered (Runaway)',
                                        'escalated_handover'    => 'Handover',
                                        'escalated_in_review'   => 'In Review',
                                        'escalated_active_recovery'       => 'Active Recovery',
                                        'recovered_post_escalation'       => 'Recovered (Escalated)',
                                        'dormant_for_revival'   => 'For Revival',
                                        'recovery_revived'      => 'Revived',
                                        'pre_litigation_review' => 'Pre-Litigation Review',
                                        'legal_filed' => 'Legal Filed',
                                        'legal_active'=> 'Legal Active',
                                        'legal_judgment_won'    => 'Judgment Won',
                                        'recovered_legal'       => 'Recovered (Legal)',
                                        'skip_trace_required'   => 'Trace Required',
                                        'skip_trace_digital_review'       => 'Digital Review',
                                        'skip_trace_contact_reengagement' => 'Re-engagement',
                                        'skip_trace_field_intel_active'   => 'Field Intel Active',
                                        'located_for_recovery'  => 'Located',
                                        'closed'      => 'Closed',
                                        'written_off' => 'Written Off'] as $sVal => $sName)
                                    <option value="{{ $sVal }}" {{ $case->status === $sVal ? 'selected' : '' }}>
                                        {{ $sName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Assigned Specialist <small class="text-muted">(optional)</small></label>
                                @php $specialists = \App\Models\User::orderBy('first_name')->get(); @endphp
                                <select name="assigned_specialist_id" class="form-control">
                                    <option value="">— Unassigned —</option>
                                    @foreach($specialists as $u)
                                    <option value="{{ $u->id }}" {{ $case->assigned_specialist_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->first_name }} {{ $u->last_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Target Resolution Date <small class="text-muted">(optional)</small></label>
                                <input type="date" name="target_resolution_date" class="form-control"
                                       value="{{ old('target_resolution_date', $case->target_resolution_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── ATTRIBUTION ──────────────────────────────── --}}
                    <h4 class="text-primary" style="margin:20px 0 14px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;font-weight:700">
                        <i class="fa fa-pie-chart"></i> Attribution Percentages
                    </h4>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Recoveries Dept %</label>
                                <div class="input-group">
                                    <input type="number" name="recoveries_dept_attribution_pct"
                                           class="form-control" step="0.01" min="0" max="100"
                                           value="{{ old('recoveries_dept_attribution_pct', $case->recoveries_dept_attribution_pct) }}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Origin Branch %</label>
                                <div class="input-group">
                                    <input type="number" name="origin_branch_attribution_pct"
                                           class="form-control" step="0.01" min="0" max="100"
                                           value="{{ old('origin_branch_attribution_pct', $case->origin_branch_attribution_pct) }}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supporting Branch %</label>
                                <div class="input-group">
                                    <input type="number" name="supporting_branch_attribution_pct"
                                           class="form-control" step="0.01" min="0" max="100"
                                           value="{{ old('supporting_branch_attribution_pct', $case->supporting_branch_attribution_pct) }}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── ESCALATION (category = escalated) ───────── --}}
                    <div id="section-escalated" style="display:none">
                        <div class="box box-warning box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-arrow-up"></i> Escalation Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Escalated By (Loan Consultant)</label>
                                            @php $allUsers = \App\Models\User::orderBy('first_name')->get(); @endphp
                                            <select name="escalated_by_user_id" class="form-control">
                                                <option value="">— Select —</option>
                                                @foreach($allUsers as $u)
                                                <option value="{{ $u->id }}" {{ $case->escalated_by_user_id == $u->id ? 'selected' : '' }}>
                                                    {{ $u->first_name }} {{ $u->last_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Escalation Date</label>
                                            <input type="date" name="escalation_date" class="form-control"
                                                   value="{{ old('escalation_date', $case->escalation_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Days Past Due at Escalation</label>
                                            <input type="number" name="days_past_due_at_escalation"
                                                   class="form-control" min="0"
                                                   value="{{ old('days_past_due_at_escalation', $case->days_past_due_at_escalation) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>LC Contact Attempts</label>
                                            <input type="number" name="lc_contact_attempts"
                                                   class="form-control" min="0"
                                                   value="{{ old('lc_contact_attempts', $case->lc_contact_attempts) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── CROSS-BRANCH (category = cross_branch) ───── --}}
                    <div id="section-cross_branch" style="display:none">
                        <div class="box box-primary box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-random"></i> Runaway Client Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Known Location</label>
                                            <input type="text" name="client_last_known_location"
                                                   class="form-control"
                                                   value="{{ old('client_last_known_location', $case->client_last_known_location) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>New / Suspected Location</label>
                                            <input type="text" name="client_new_location"
                                                   class="form-control"
                                                   value="{{ old('client_new_location', $case->client_new_location) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── LEGAL (category = legal) ─────────────────── --}}
                    <div id="section-legal" style="display:none">
                        <div class="box box-danger box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-gavel"></i> Legal Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Legal Reference #</label>
                                            <input type="text" name="legal_reference_number"
                                                   class="form-control"
                                                   value="{{ old('legal_reference_number', $case->legal_reference_number) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Lawyer / Firm</label>
                                            <input type="text" name="lawyer_firm"
                                                   class="form-control"
                                                   value="{{ old('lawyer_firm', $case->lawyer_firm) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Enforcement Type</label>
                                            <select name="enforcement_type" class="form-control">
                                                @foreach(['none'=>'None yet','garnishee_order'=>'Garnishee Order','warrant_of_distress'=>'Warrant of Distress','writ_of_execution'=>'Writ of Execution','charging_order'=>'Charging Order','judgment_debtor_summons'=>'Judgment Debtor Summons'] as $val => $lbl)
                                                <option value="{{ $val }}" {{ ($case->enforcement_type ?? 'none') === $val ? 'selected' : '' }}>
                                                    {{ $lbl }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Legal Filed Date</label>
                                            <input type="date" name="legal_filed_date" class="form-control"
                                                   value="{{ old('legal_filed_date', $case->legal_filed_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Court Date</label>
                                            <input type="date" name="court_date" class="form-control"
                                                   value="{{ old('court_date', $case->court_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Legal Costs Incurred (K)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">K</span>
                                                <input type="number" name="legal_costs_incurred"
                                                       class="form-control" step="0.01" min="0"
                                                       value="{{ old('legal_costs_incurred', $case->legal_costs_incurred) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── DORMANT (category = dormant) ─────────────── --}}
                    <div id="section-dormant" style="display:none">
                        <div class="box box-default box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-moon-o"></i> Dormant Account Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Payment Date</label>
                                            <input type="date" name="last_payment_date" class="form-control"
                                                   value="{{ old('last_payment_date', $case->last_payment_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dormant Days</label>
                                            <input type="number" name="dormant_days" class="form-control" min="0"
                                                   value="{{ old('dormant_days', $case->dormant_days) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Revival Method</label>
                                            <input type="text" name="revival_method" class="form-control"
                                                   placeholder="e.g. sms_campaign, field_visit"
                                                   value="{{ old('revival_method', $case->revival_method) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SKIP TRACE (category = skip_trace) ──────── --}}
                    <div id="section-skip_trace" style="display:none">
                        <div class="box box-success box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-search"></i> Skip Trace Details</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tracking Code</label>
                                            <input type="text" name="skip_trace_tracking_code" class="form-control"
                                                   value="{{ old('skip_trace_tracking_code', $case->skip_trace_tracking_code) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Client Located?</label>
                                            <select name="client_located" class="form-control">
                                                <option value="0" {{ !$case->client_located ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ $case->client_located ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Located Date</label>
                                            <input type="date" name="located_date" class="form-control"
                                                   value="{{ old('located_date', $case->located_date?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Skip Trace Costs (K)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">K</span>
                                                <input type="number" name="skip_trace_costs"
                                                       class="form-control" step="0.01" min="0"
                                                       value="{{ old('skip_trace_costs', $case->skip_trace_costs) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Joint Field Visit Done?</label>
                                            <select name="joint_field_visit_done" class="form-control">
                                                <option value="0" {{ !$case->joint_field_visit_done ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ $case->joint_field_visit_done ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── NOTES ────────────────────────────────────── --}}
                    <div class="form-group" style="margin-top:14px">
                        <label>Notes <small class="text-muted">(optional)</small></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Any additional context or observations...">{{ old('notes', $case->notes) }}</textarea>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                    <a href="{{ url('recovery/case/' . $case->id . '/show') }}" class="btn btn-default" style="margin-left:6px">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
    (function () {
        var sections = ['escalated', 'cross_branch', 'legal', 'dormant', 'skip_trace'];
        var select   = document.getElementById('category-select');

        function toggle() {
            sections.forEach(function (s) {
                var el = document.getElementById('section-' + s);
                if (el) el.style.display = 'none';
            });
            var active = document.getElementById('section-' + select.value);
            if (active) active.style.display = 'block';
        }

        select.addEventListener('change', toggle);
        toggle();
    }());
</script>
@endpush

@endsection
