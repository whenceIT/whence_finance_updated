@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Motor Vehicle Loan Lifecycle
        <small>Loan #{{ $loan->id }}</small>
    </h1>
</section>

<section class="content">

<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Loan Summary</h3>
            </div>
            <div class="box-body">
                <p><strong>Client:</strong> {{ $loan->client->display_name ?? 'N/A' }}</p>
                <p><strong>Vehicle:</strong> {{ $loan->vehicle->make ?? '' }} {{ $loan->vehicle->model ?? '' }}</p>
                <p><strong>Registration:</strong> {{ $loan->vehicle->registration_number ?? 'N/A' }}</p>
                <p><strong>Branch:</strong> {{ $loan->originatingBranch->name ?? 'N/A' }}</p>
                <p><strong>Consultant:</strong> {{ $loan->loanConsultant->first_name ?? '' }} {{ $loan->loanConsultant->last_name ?? '' }}</p>
                <p><strong>Status:</strong>
                    <span class="label label-{{ $loan->status == 'active_loan' ? 'success' : ($loan->status == 'default' ? 'danger' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                    </span>
                </p>
                <p><strong>Approved Amount:</strong> K{{ number_format($loan->approved_amount, 2) }}</p>
                <p><strong>LTV:</strong> {{ $loan->ltv_percent }}%</p>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Workflow Actions</h3>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('motor-vehicle-loans.transition-stage', $loan->id) }}">
                    @csrf
                    <div class="form-group">
                        <label>Transition Stage</label>
                        <select name="stage" class="form-control" required>
                            <option value="">Select Stage</option>
                            @foreach(['draft', 'assessment', 'approval', 'vehicle_intake', 'disbursement', 'active_loan', 'arrears', 'default', 'recovery', 'disposal_pending', 'sold', 'closed'] as $stage)
                                <option value="{{ $stage }}" {{ $loan->status == $stage ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $stage)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comments</label>
                        <textarea name="comments" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Approval Notes</label>
                        <textarea name="approval_notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Transition Stage</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_master" data-toggle="tab">Master Record</a></li>
                <li><a href="#tab_workflow" data-toggle="tab">Workflow Stages</a></li>
                <li><a href="#tab_status" data-toggle="tab">Status History</a></li>
                <li><a href="#tab_audit" data-toggle="tab">Audit Trail</a></li>
                <li><a href="#tab_compliance" data-toggle="tab">Compliance</a></li>
                <li><a href="#tab_vehicle" data-toggle="tab">Vehicle Details</a></li>
                <li><a href="#tab_valuations" data-toggle="tab">Valuations</a></li>
                <li><a href="#tab_inspections" data-toggle="tab">Inspections</a></li>
                <li><a href="#tab_insurance" data-toggle="tab">Insurance</a></li>
                <li><a href="#tab_custody" data-toggle="tab">Custody</a></li>
                <li><a href="#tab_movements" data-toggle="tab">Movements</a></li>
                <li><a href="#tab_roll_calls" data-toggle="tab">Roll Calls</a></li>
                <li><a href="#tab_documents" data-toggle="tab">Documents</a></li>
                <li><a href="#tab_photos" data-toggle="tab">Photos</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_master">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Master Record</h3>
                            <a href="{{ route('motor-vehicle-loans.edit-master-record', $loan->id) }}" class="btn btn-primary btn-xs pull-right">Edit</a>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Loan Consultant:</strong> {{ $loan->loanConsultant->first_name ?? '' }} {{ $loan->loanConsultant->last_name ?? '' }}</p>
                                    <p><strong>Originating Branch:</strong> {{ $loan->originatingBranch->name ?? 'N/A' }}</p>
                                    <p><strong>Branch Assessor:</strong> {{ $loan->branchAssessor->first_name ?? '' }} {{ $loan->branchAssessor->last_name ?? '' }}</p>
                                    <p><strong>District:</strong> {{ $loan->district->name ?? 'N/A' }}</p>
                                    <p><strong>Province:</strong> {{ $loan->province->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Vehicle Status:</strong> {{ $loan->vehicle_status ?? 'N/A' }}</p>
                                    <p><strong>Custody Status:</strong> {{ $loan->custody_status ?? 'N/A' }}</p>
                                    <p><strong>Current Location:</strong> {{ $loan->current_storage_location ?? 'N/A' }}</p>
                                    <p><strong>Custodian:</strong> {{ $loan->currentCustodian->first_name ?? 'N/A' }}</p>
                                    <p><strong>Custodian Phone:</strong> {{ $loan->current_custodian_phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if($loan->remarks)
                            <hr>
                            <p><strong>Remarks:</strong> {{ $loan->remarks }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_workflow">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Workflow Stages</h3>
                        </div>
                        <div class="box-body">
                            @forelse($workflowStages as $stage)
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $stage->transition_date->format('Y-m-d H:i') }}</span>
                                    <h3 class="timeline-title">{{ ucfirst(str_replace('_', ' ', $stage->stage)) }}</h3>
                                </div>
                                <div class="timeline-body">
                                    @if($stage->previous_stage)
                                    <p><strong>Previous:</strong> {{ ucfirst(str_replace('_', ' ', $stage->previous_stage)) }}</p>
                                    @endif
                                    @if($stage->comments)
                                    <p><strong>Comments:</strong> {{ $stage->comments }}</p>
                                    @endif
                                    @if($stage->approval_notes)
                                    <p><strong>Approval Notes:</strong> {{ $stage->approval_notes }}</p>
                                    @endif
                                    <p><strong>Officer:</strong> {{ $stage->officer->first_name ?? 'N/A' }} {{ $stage->officer->last_name ?? '' }}</p>
                                    <p><strong>Branch:</strong> {{ $stage->branch->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-center">No workflow stages recorded</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_status">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Status History</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Previous Status</th>
                                        <th>New Status</th>
                                        <th>User</th>
                                        <th>Branch</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($statusHistory as $history)
                                    <tr>
                                        <td>{{ $history->transition_date->format('Y-m-d H:i') }}</td>
                                        <td>{{ $history->previous_status ?? '-' }}</td>
                                        <td>{{ $history->new_status ?? '-' }}</td>
                                        <td>{{ $history->user->first_name ?? 'N/A' }} {{ $history->user->last_name ?? '' }}</td>
                                        <td>{{ $history->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $history->action_reason ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center">No status history</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_audit">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <a href="{{ route('motor-vehicle-loans.audit-trail', $loan->id) }}" class="btn btn-primary btn-xs">Full Audit Trail</a>
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                        <th>User</th>
                                        <th>Branch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($auditLogs->take(20) as $log)
                                    <tr>
                                        <td>{{ $log->actioned_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
                                        <td>{{ $log->old_value ?? '-' }}</td>
                                        <td>{{ $log->new_value ?? '-' }}</td>
                                        <td>{{ $log->user->first_name ?? 'N/A' }} {{ $log->user->last_name ?? '' }}</td>
                                        <td>{{ $log->branch->name ?? 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center">No audit logs</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_compliance">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Compliance Screening</h3>
                            <a href="{{ route('motor-vehicle-loans.compliance-screening', $loan->id) }}" class="btn btn-primary btn-xs pull-right">Add Screening</a>
                        </div>
                        <div class="box-body">
                            @if($compliance)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>PEP Result:</strong> {{ $compliance->pep_result ?? 'N/A' }}</p>
                                    <p><strong>Sanctions Result:</strong> {{ $compliance->sanctions_result ?? 'N/A' }}</p>
                                    <p><strong>Screening Date:</strong> {{ $compliance->screening_date ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Match Level:</strong> {{ $compliance->match_level ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> {{ $compliance->status ?? 'N/A' }}</p>
                                    <p><strong>Officer:</strong> {{ $compliance->screeningOfficer->first_name ?? 'N/A' }} {{ $compliance->screeningOfficer->last_name ?? '' }}</p>
                                </div>
                            </div>
                            @if($compliance->comments)
                            <hr>
                            <p><strong>Comments:</strong> {{ $compliance->comments }}</p>
                            @endif
                            @else
                            <p class="text-center">No compliance screening recorded</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_vehicle">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Vehicle Details</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id) }}" class="btn btn-primary btn-xs pull-right">Full Vehicle Details</a>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Make:</strong> {{ $loan->vehicle->make }}</p>
                                    <p><strong>Model:</strong> {{ $loan->vehicle->model }}</p>
                                    <p><strong>Year:</strong> {{ $loan->vehicle->year }}</p>
                                    <p><strong>Color:</strong> {{ $loan->vehicle->color }}</p>
                                    <p><strong>Registration:</strong> {{ $loan->vehicle->registration_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Engine Number:</strong> {{ $loan->vehicle->engine_number }}</p>
                                    <p><strong>Chassis Number:</strong> {{ $loan->vehicle->chassis_number }}</p>
                                    <p><strong>Fuel Type:</strong> {{ $loan->vehicle->fuel_type }}</p>
                                    <p><strong>Transmission:</strong> {{ $loan->vehicle->transmission }}</p>
                                    <p><strong>Mileage:</strong> {{ $loan->vehicle->mileage }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Market Value:</strong> K{{ number_format($loan->vehicle->market_value, 2) }}</p>
                                    <p><strong>Forced Sale Value:</strong> K{{ number_format($loan->vehicle->forced_sale_value, 2) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ownership Type:</strong> {{ ucfirst($loan->vehicle->ownership_type ?? 'individual') }}</p>
                                    <p><strong>Vehicle Status:</strong> {{ $loan->vehicle->status }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_valuations">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Valuations</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/valuations/create') }}" class="btn btn-primary btn-xs pull-right">Add Valuation</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Market Value</th>
                                        <th>Forced Sale Value</th>
                                        <th>Valuator</th>
                                        <th>Report</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($valuations as $valuation)
                                        <tr>
                                            <td>{{ $valuation->valuation_date }}</td>
                                            <td>K{{ number_format($valuation->market_value, 2) }}</td>
                                            <td>K{{ number_format($valuation->forced_sale_value, 2) }}</td>
                                            <td>{{ $valuation->valuator_name }}</td>
                                            <td>@if($valuation->report_file)<a href="{{ $valuation->report_file }}" target="_blank" class="btn btn-xs btn-primary">View</a>@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">No valuations recorded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_inspections">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Inspections</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/inspections/create') }}" class="btn btn-primary btn-xs pull-right">Add Inspection</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Inspector</th>
                                        <th>Mileage</th>
                                        <th>Fuel</th>
                                        <th>Result</th>
                                        <th>Report</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inspections as $inspection)
                                        <tr>
                                            <td>{{ $inspection->inspection_date }}</td>
                                            <td>{{ $inspection->inspection_type ?? '-' }}</td>
                                            <td>{{ $inspection->inspector }}</td>
                                            <td>{{ $inspection->mileage }}</td>
                                            <td>{{ $inspection->fuel }}</td>
                                            <td>{{ ucfirst($inspection->result) }}</td>
                                            <td>@if($inspection->report_url)<a href="{{ $inspection->report_url }}" target="_blank" class="btn btn-xs btn-primary">Report</a>@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No inspections recorded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_insurance">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Insurance Policies</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/insurance/create') }}" class="btn btn-primary btn-xs pull-right">Add Insurance</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Insurer</th>
                                        <th>Policy Number</th>
                                        <th>Start Date</th>
                                        <th>Expiry Date</th>
                                        <th>Insured Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($insurancePolicies as $policy)
                                        <tr>
                                            <td>{{ $policy->insurer_name }}</td>
                                            <td>{{ $policy->policy_number }}</td>
                                            <td>{{ $policy->start_date }}</td>
                                            <td>{{ $policy->expiry_date }}</td>
                                            <td>K{{ number_format($policy->insured_value, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">No insurance policies recorded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_custody">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Custody Details</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/custody/create') }}" class="btn btn-primary btn-xs pull-right">Record Intake</a>
                        </div>
                        <div class="box-body">
                            @if($custody)
                                <p><strong>Status:</strong> {{ ucfirst($custody->status) }}</p>
                                <p><strong>Received At:</strong> {{ $custody->received_at }}</p>
                                <p><strong>Received By:</strong> {{ $custody->receiver->first_name ?? 'N/A' }} {{ $custody->receiver->last_name ?? '' }}</p>
                                <p><strong>Garage:</strong> {{ $custody->garage_name }}</p>
                                <p><strong>Location:</strong> {{ $custody->garage_location }}</p>
                                <p><strong>Contact Person:</strong> {{ $custody->garage_contact_person }}</p>
                                <p><strong>Contact Phone:</strong> {{ $custody->garage_contact_phone }}</p>
                                <p><strong>Keys Received:</strong> {{ $custody->keys_received ? 'Yes' : 'No' }}</p>
                                <p><strong>Remarks:</strong> {{ $custody->remarks }}</p>
                            @else
                                <p class="text-center">No custody records found</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_movements">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Movement History</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/movements') }}" class="btn btn-primary btn-xs pull-right">Record Movement</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Previous Location</th>
                                        <th>New Location</th>
                                        <th>Authorized By</th>
                                        <th>Moved By</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $movement)
                                        <tr>
                                            <td>{{ $movement->movement_date }}</td>
                                            <td>{{ $movement->previous_location }}</td>
                                            <td>{{ $movement->new_location }}</td>
                                            <td>{{ $movement->authorizedBy->first_name ?? 'N/A' }}</td>
                                            <td>{{ $movement->movedBy->first_name ?? 'N/A' }}</td>
                                            <td>{{ $movement->reason }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center">No movements recorded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_roll_calls">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Weekly Roll Calls</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/roll-calls') }}" class="btn btn-primary btn-xs pull-right">Record Roll Call</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Officer</th>
                                        <th>Status</th>
                                        <th>Mileage</th>
                                        <th>Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rollCalls as $rollCall)
                                        <tr>
                                            <td>{{ $rollCall->verification_date }}</td>
                                            <td>{{ $rollCall->officer->first_name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($rollCall->status) }}</td>
                                            <td>{{ $rollCall->current_mileage }}</td>
                                            <td>{{ $rollCall->condition_notes }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">No roll calls recorded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_documents">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Documents</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/documents/create') }}" class="btn btn-primary btn-xs pull-right">Upload Document</a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $document)
                                        <tr>
                                            <td>{{ $document->document_type }}</td>
                                            <td>
                                                <a href="{{ $document->document_file }}" target="_blank" class="btn btn-xs btn-primary">View</a>
                                                <a href="{{ $document->document_file }}" target="_blank" class="btn btn-xs btn-success">Download</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center">No documents uploaded</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_photos">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Photos</h3>
                            <a href="{{ url('vehicles/'.$loan->vehicle->id.'/photos/create') }}" class="btn btn-primary btn-xs pull-right">Upload Photo</a>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @forelse($photos as $photo)
                                    <div class="col-md-3">
                                        <div class="thumbnail">
                                            <img src="{{ $photo->photo_url }}" class="img-responsive" style="height: 200px; object-fit: cover;">
                                            <div class="caption">
                                                <strong>{{ $photo->photo_type }}</strong>
                                                <br>{{ $photo->caption }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-md-12">
                                        <p class="text-center text-muted">No photos uploaded</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</section>

@endsection
