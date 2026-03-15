@extends('layouts.master')
@section('title')
    Open New Recovery Case
@endsection
@section('content')
<div class="row">
    <div class="col-md-10 col-lg-8">

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-folder-open"></i> New Recovery Case</h3>
            </div>

            <form method="POST" action="{{ url('recovery/case/store') }}">
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

                    {{-- ── LOAN SELECTION ──────────────────────────── --}}
                    <h4 class="text-primary" style="margin:0 0 14px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;font-weight:700">
                        <i class="fa fa-file-text-o"></i> Case Details
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('loan_id') ? 'has-error' : '' }}">
                                <label>Loan <span class="text-danger">*</span></label>
                                @php $loans = \App\Models\Loan::with('client')->orderBy('id','desc')->get(); @endphp
                                <select name="loan_id" class="form-control" required>
                                    <option value="">— Select Loan —</option>
                                    @foreach($loans as $loan)
                                    <option value="{{ $loan->id }}" {{ old('loan_id') == $loan->id ? 'selected' : '' }}>
                                        {{ $loan->loan_id ?? 'Loan #'.$loan->id }}
                                        &mdash;
                                        {{ ($loan->client->client_type ?? '') === 'business'
                                            ? ($loan->client->full_name ?? '')
                                            : trim(($loan->client->first_name ?? '') . ' ' . ($loan->client->last_name ?? '')) }}
                                    </option>
                                    @endforeach
                                </select>
                                <span class="help-block">Client will be linked automatically from the selected loan</span>
                                @if($errors->has('loan_id'))
                                    <span class="help-block text-danger">{{ $errors->first('loan_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                                <label>Recovery Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" id="category-select" required>
                                    <option value="">— Select Category —</option>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
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
                                    @php $offices = \App\Models\Office::orderBy('name')->get(); @endphp
                                    @foreach($offices as $branch)
                                    <option value="{{ $branch->id }}" {{ old('origin_branch_id') == $branch->id ? 'selected' : '' }}>
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
                                    @php $offices = \App\Models\Office::orderBy('name')->get(); @endphp
                                    @foreach($offices as $branch)
                                    <option value="{{ $branch->id }}" {{ old('supporting_branch_id') == $branch->id ? 'selected' : '' }}>
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
                                           value="{{ old('loan_outstanding_amount') }}">
                                </div>
                                @if($errors->has('loan_outstanding_amount'))
                                    <span class="help-block text-danger">{{ $errors->first('loan_outstanding_amount') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assign Specialist <small class="text-muted">(optional)</small></label>
                                <select name="assigned_specialist_id" class="form-control">
                                    <option value="">— Assign Later —</option>
                                    @php $specialists = \App\Models\User::orderBy('first_name')->get(); @endphp
                                    @foreach($specialists as $u)
                                    <option value="{{ $u->id }}" {{ old('assigned_specialist_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->first_name }} {{ $u->last_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Target Resolution Date <small class="text-muted">(optional)</small></label>
                                <input type="date" name="target_resolution_date" class="form-control"
                                       value="{{ old('target_resolution_date') }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 2: ESCALATION (shown when category = escalated) ──── --}}
                    <div id="section-escalated" style="display:none">
                        <div class="box box-warning box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <i class="fa fa-arrow-up"></i> Escalation Details
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Escalated By (Loan Consultant)</label>
                                            <select name="escalated_by_user_id" class="form-control">
                                                <option value="">— Select —</option>
                                                @php $allUsers = \App\Models\User::orderBy('first_name')->get(); @endphp
                                                @foreach($allUsers as $u)
                                                <option value="{{ $u->id }}" {{ old('escalated_by_user_id') == $u->id ? 'selected' : '' }}>
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
                                                   value="{{ old('escalation_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Days Past Due at Escalation</label>
                                            <input type="number" name="days_past_due_at_escalation"
                                                   class="form-control" min="0"
                                                   value="{{ old('days_past_due_at_escalation') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>LC Contact Attempts</label>
                                            <input type="number" name="lc_contact_attempts"
                                                   class="form-control" min="0"
                                                   value="{{ old('lc_contact_attempts') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 3: CROSS-BRANCH / RUNAWAY (shown when category = cross_branch) ── --}}
                    <div id="section-cross_branch" style="display:none">
                        <div class="box box-primary box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <i class="fa fa-random"></i> Runaway Client Details
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Known Location</label>
                                            <input type="text" name="client_last_known_location"
                                                   class="form-control"
                                                   value="{{ old('client_last_known_location') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>New / Suspected Location</label>
                                            <input type="text" name="client_new_location"
                                                   class="form-control"
                                                   value="{{ old('client_new_location') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 4: LEGAL (shown when category = legal) ── --}}
                    <div id="section-legal" style="display:none">
                        <div class="box box-danger box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <i class="fa fa-gavel"></i> Legal Details
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Legal Reference #</label>
                                            <input type="text" name="legal_reference_number"
                                                   class="form-control"
                                                   value="{{ old('legal_reference_number') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Lawyer / Firm</label>
                                            <input type="text" name="lawyer_firm"
                                                   class="form-control"
                                                   value="{{ old('lawyer_firm') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Enforcement Type</label>
                                            <select name="enforcement_type" class="form-control">
                                                <option value="none"                      {{ old('enforcement_type','none') == 'none'                      ? 'selected' : '' }}>None yet</option>
                                                <option value="garnishee_order"           {{ old('enforcement_type') == 'garnishee_order'           ? 'selected' : '' }}>Garnishee Order</option>
                                                <option value="warrant_of_distress"       {{ old('enforcement_type') == 'warrant_of_distress'       ? 'selected' : '' }}>Warrant of Distress</option>
                                                <option value="writ_of_execution"         {{ old('enforcement_type') == 'writ_of_execution'         ? 'selected' : '' }}>Writ of Execution</option>
                                                <option value="charging_order"            {{ old('enforcement_type') == 'charging_order'            ? 'selected' : '' }}>Charging Order</option>
                                                <option value="judgment_debtor_summons"   {{ old('enforcement_type') == 'judgment_debtor_summons'   ? 'selected' : '' }}>Judgment Debtor Summons</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 5: DORMANT (shown when category = dormant) ── --}}
                    <div id="section-dormant" style="display:none">
                        <div class="box box-default box-solid" style="margin:16px 0 0">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <i class="fa fa-moon-o"></i> Dormant Account Details
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Payment Date</label>
                                            <input type="date" name="last_payment_date"
                                                   class="form-control"
                                                   value="{{ old('last_payment_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dormant Days</label>
                                            <input type="number" name="dormant_days"
                                                   class="form-control" min="0"
                                                   value="{{ old('dormant_days') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Revival Method</label>
                                            <input type="text" name="revival_method"
                                                   class="form-control"
                                                   placeholder="e.g. sms_campaign, field_visit"
                                                   value="{{ old('revival_method') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── NOTES (always visible) ── --}}
                    <div class="form-group" style="margin-top:14px">
                        <label>Notes <small class="text-muted">(optional)</small></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Any additional context or observations...">{{ old('notes') }}</textarea>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-folder-open"></i> Open Case
                    </button>
                    <a href="{{ url('recovery/case/data') }}" class="btn btn-default" style="margin-left:6px">
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
        toggle(); // run on page load to restore old() state
    }());
</script>
@endpush
@endsection
