@extends('layouts.master')

@section('title', 'Employee Dashboard')

@section('content')
<style>
    /* Wizard Progress */
    .wizard-progress {
        margin-bottom: 30px;
    }

    .progress-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .progress-steps .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .progress-steps .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #ddd;
        z-index: 1;
    }

    .progress-steps .step.active:not(:last-child)::after {
        background: #337ab7;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ddd;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 5px;
        position: relative;
        z-index: 2;
    }

    .step.active .step-number {
        background: #337ab7;
        color: white;
    }

    .step-label {
        font-size: 12px;
        color: #666;
        text-align: center;
    }

    .step.active .step-label {
        color: #337ab7;
        font-weight: bold;
    }

    /* Record Type Options */
    .record-type-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .option-card {
        border: 2px solid #e7e7e7;
        border-radius: 8px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .option-card:hover {
        border-color: #337ab7;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .option-card.selected {
        border-color: #337ab7;
        background: #f8f9fa;
    }

    .option-icon {
        font-size: 24px;
        color: #337ab7;
        margin-bottom: 10px;
        text-align: center;
    }

    .option-content h6 {
        margin: 0 0 8px 0;
        color: #333;
        font-weight: bold;
    }

    .option-content p {
        margin: 0;
        color: #666;
        font-size: 14px;
    }

    /* Wizard Steps */
    .wizard-step {
        min-height: 300px;
    }

    .step-title {
        color: #337ab7;
        border-bottom: 2px solid #337ab7;
        padding-bottom: 8px;
        margin-bottom: 20px;
    }

    /* Summary Grid */
    .summary-grid {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        font-weight: bold;
        color: #495057;
    }

    .summary-value {
        color: #6c757d;
    }
</style>


<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    @if($employee->image)
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset($employee->image) }}" alt="{{ $employee->full_name }}">
                    @else
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/default-employee-icon.jpg') }}" alt="Default Image">
                    @endif

                    <h3 class="profile-username text-center">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                    <p class="text-muted text-center">{{ ($employee->position)->name ?? 'No Role' }}</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Office</b> <span class="pull-right">{{ optional($employee->office)->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Gender</b> <span class="pull-right">{{ $employee->gender ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b> <span class="pull-right">{{ $employee->status ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Employee No.</b> <span class="pull-right">{{ $employee->employee_number ?? 'N/A' }}</span>
                        </li>
                    </ul>

                    <button class="btn btn-primary btn-block" onclick="openAdminRecordModal()">
                        <b>Add Administrative Record</b>
                    </button>

                    <a href="{{ url('hr/employees') }}" class="btn btn-default btn-block">
                        <b>Back to Records</b>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#general" data-toggle="tab">General Information</a></li>
                    <li><a href="#performance" data-toggle="tab">Performance</a></li>
                     <li><a href="#payroll" data-toggle="tab">Payroll</a></li>
                     <li><a href="#leave" data-toggle="tab">Leave</a></li>
                     <li><a href="#advances" data-toggle="tab">Advances</a></li>
                     <li><a href="#disciplinary" data-toggle="tab">Disciplinary Actions</a></li>
                     <li><a href="#health-records" data-toggle="tab">Health Records</a></li>
                     <li><a href="#career-progression" data-toggle="tab">Career Progression</a></li>
                </ul>

                <div class="tab-content">
                    {{-- General Information --}}
                    <div class="active tab-pane" id="general">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Full Name</th>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $employee->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $employee->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>NRC</th>
                                <td>{{ $employee->nrc_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $employee->date_of_birth ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date Hired</th>
                                <td>{{ $employee->date_of_joining ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Office</th>
                                <td>{{ optional($employee->office)->name ?? 'N/A' }}</td>
                            </tr>
                              <tr>
                                <th>department</th>
                                <td>{{ $employee->department ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>{{ optional($employee->position)->name ?? 'N/A' }}</td>
                            </tr>

                             <tr>
                                <th>Employment Type</th>
                                <td>{{$employee->employment_type ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Employment Status</th>
                                <td>{{ $employee->employment_status ?? 'N/A' }}</td>
                            </tr>
                          <tr>
    <th>Emergency Contact</th>
    <td>
        <strong>Name:</strong> {{ $employee->emergency_contact_name ?? 'N/A' }}<br>
        <strong>Relationship:</strong> {{ $employee->relation_to_emergency ?? 'N/A' }}<br>
        <strong>Contact:</strong> {{ $employee->emergency_phone ?? 'N/A' }}
    </td>
</tr>
                        </table>
                    </div>


                    
            

                 {{-- Performance --}}
                    <div class="tab-pane" id="performance">

                    
@if(empty($data) || is_null($data))

    <div class="box-header with-border text-center">
        <h3 class="box-title">
            Performance Summary
        </h3>
    </div>

    <div class="text-center" style="padding:40px; background:#f9fafc; border-radius:16px;">
        <h4 style="margin-bottom:10px;">No performance information available</h4>
    </div>

@else

    <div class="box-header with-border text-center">
        <h3 class="box-title">
            Performance Summary
            {{ date("jS M, Y", strtotime($start)) }}
            to
            {{ date("jS M, Y", strtotime($end)) }}
        </h3>
    </div>

    <div style="background:#f9fafc; padding:25px; border-radius:16px; margin-bottom:25px;">
        <form method="GET" action="{{ url('user/manager_performance') }}" class="form-horizontal">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">

                    <div class="form-group text-center">
                        <label class="control-label">Cycle Start</label>
                        <input type="month" name="start_month" class="form-control"
                            value="{{ substr($start, 0, 7) }}">
                    </div>

                    <div class="form-group text-center">
                        <label class="control-label">Cycle End</label>
                        <input type="month" name="end_month" class="form-control"
                            value="{{ substr($end, 0, 7) }}">
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">
                            Load
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Cycle Opening Uncollected</th>
                <th>Total Collected</th>
                <th>Still Uncollected</th>
                <th>Given Out</th>
                <th>PDUA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($data['total_uncollected'] ?? 0) }}</td>
                <td>{{ number_format($data['total_collected'] ?? 0) }}</td>
                <td>{{ number_format(max(0, $data['still_uncollected'] ?? 0)) }}</td>
                <td>{{ number_format($data['given_out'] ?? 0) }}</td>
                <td>{{ number_format(($data['pdua'] ?? 0) * 100, 2) }}%</td>
            </tr>
        </tbody>
    </table>

@endif



                    </div>


                    {{-- Payroll --}}
<div class="tab-pane" id="payroll">

    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-striped table-hover">

            <thead>
                <tr>
                    <th>Staff</th>
                    @foreach($payroll_fields as $payroll_field)
                        <th>{{$payroll_field->name}}</th>
                    @endforeach
                    <th>Net Pay</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @php 
                use App\Models\PayrollMeta;
                use App\Models\PayrollTemplateMeta;

                $currentMonth = null;
            @endphp

            @foreach($employeePayrolls->sortByDesc('payroll_date') as $payroll)

                @php
                    $month = date("F Y", strtotime($payroll->payroll_date));
                @endphp

                {{-- ✅ Month Header --}}
                @if($currentMonth != $month)
                    <tr style="background:#f4f6f9;">
                        <td colspan="100%">
                            <strong>{{ $month }}</strong>
                        </td>
                    </tr>
                    @php $currentMonth = $month; @endphp
                @endif

                @php
                    $payroll_info = PayrollMeta::where('payroll_id',$payroll->id)->get();

                    $additions = 0;
                    $deductions = 0;

                    foreach($payroll_info as $info){
                        $payroll_field = PayrollTemplateMeta::where('id',$info->payroll_template_meta_id)->first();

                        if($payroll_field && $payroll_field->type == 'addition'){
                            $additions += $info->value;
                        } elseif($payroll_field && $payroll_field->type == 'deduction'){
                            $deductions += $info->value;
                        }
                    }

                    $net_pay = $additions - $deductions;
                @endphp

                <tr>
                    <td>{{ $payroll->employee_name }}</td>

                    @foreach($payroll_info as $info)
                        <td>{{ number_format($info->value ?? 0,2) }}</td>
                    @endforeach

                    <td>{{ number_format($net_pay,2) }}</td>
                    <td>{{ date("M, Y", strtotime($payroll->payroll_date)) }}</td>
                    <td>{{ $payroll->status }}</td>

                    <td>
                        <a href="{{ url('payroll/'.$payroll->id.'/payslip') }}" 
                           class="btn btn-success btn-xs">
                            Generate Payslip
                        </a>
                    </td>
                </tr>

            @endforeach

            </tbody>

        </table>
    </div>

</div>



               {{-- Leave --}}
<div class="tab-pane" id="leave">

    <div style="margin-bottom: 15px;">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-3">
                    <label for="leave_year">Filter by Year</label>
                    <select name="leave_year" id="leave_year" class="form-control" onchange="this.form.submit()">
                        @foreach($leaveYears as $year)
                            <option value="{{ $year }}" {{ (int)$selectedLeaveYear === (int)$year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Time Period Taken</th>
                <th>Days</th>
                <th>Status</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeLeaves as $leave)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($leave->commencement_date)->format('d M Y') }}
                        -
                        {{ \Carbon\Carbon::parse($leave->return_date)->format('d M Y') }}
                    </td>
                    <td>{{ $leave->days_taken }}</td>
                    <td>{{ ucfirst($leave->status) }}</td>
                    <td>{{ $leave->reason }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        No leave records found for {{ $selectedLeaveYear }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- Advances --}}
<div class="tab-pane" id="advances">

    <div style="margin-bottom: 15px;">
        <form method="GET" action="">
            <input type="hidden" name="tab" value="advances">

            @if(request('leave_year'))
                <input type="hidden" name="leave_year" value="{{ request('leave_year') }}">
            @endif

            <div class="row">
                <div class="col-md-3">
                    <label for="advance_year">Filter by Year</label>
                    <select name="advance_year" id="advance_year" class="form-control" onchange="this.form.submit()">
                        @foreach($advanceYears as $year)
                            <option value="{{ $year }}" {{ (int)$selectedAdvanceYear === (int)$year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Date Requested</th>
                <th>Date Approved</th>
                <th>Amount Paid</th>
                <th>Remaining Amount</th>
                <th>Repayment Status</th>
                <th>Status</th>
                <th>Purpose / Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeAdvances as $advance)
                <tr>
                    <td>{{ number_format($advance->amount, 2) }}</td>
                    <td>{{ $advance->date_requested ? \Carbon\Carbon::parse($advance->date_requested)->format('d M Y') : '-' }}</td>
                    <td>{{ $advance->date_approved ? \Carbon\Carbon::parse($advance->date_approved)->format('d M Y') : '-' }}</td>
                    <td>{{ number_format($advance->amount_paid ?? 0, 2) }}</td>
                    <td>{{ number_format($advance->remaining_amount ?? 0, 2) }}</td>
                    <td>{{ $advance->payment_status }}</td>
                    <td>{{ ucfirst($advance->status) }}</td>
                    <td>
                        {{ $advance->purpose ?: ($advance->notes ?: '-') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        No advance records found for {{ $selectedAdvanceYear }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Disciplinary Actions --}}
<div class="tab-pane" id="disciplinary">
    <div class="box-header with-border">
        <h3 class="box-title">Disciplinary Action Records</h3>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Date</th>
                <th>Action Type</th>
                <th>Details</th>
                <th>Level/Duration</th>
                <th>Comments</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
                            @forelse($employeeDisciplinaryRecords as $record)
                <tr>
                    <td>{{ $record->created_at->format('d M Y') }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $record->disciplinary_type)) }}</td>
                    <td>
                        @if($record->warning_type)
                            {{ ucfirst($record->warning_type) }}
                        @elseif($record->number_of_days)
                            {{ $record->number_of_days }} days<br>
                            @if($record->absence_dates)
                                <small>{{ collect($record->absence_dates)->map(function($date) { return \Carbon\Carbon::parse($date)->format('M j'); })->join(', ') }}</small>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $record->warning_level ? ucfirst($record->warning_level) : ($record->number_of_days ? 'N/A' : '-') }}</td>
                    <td>{{ $record->comments ?: '-' }}</td>
                    <td>{{ $record->creator->first_name ?? 'Unknown' }} {{ $record->creator->last_name ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        No disciplinary records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Health Records --}}
<div class="tab-pane" id="health-records">
    <div class="box-header with-border">
        <h3 class="box-title">Health Record Details</h3>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Date</th>
                <th>Record Type</th>
                <th>Incident Type</th>
                <th>Description</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeHealthRecords as $record)
                <tr>
                    <td>{{ $record->created_at->format('d M Y') }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $record->health_type)) }}</td>
                    <td>{{ $record->incident_type ? ucfirst(str_replace('-', ' ', $record->incident_type)) : '-' }}</td>
                    <td>{{ $record->description ?: '-' }}</td>
                    <td>{{ $record->creator->first_name ?? 'Unknown' }} {{ $record->creator->last_name ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        No health records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Career Progression --}}
<div class="tab-pane" id="career-progression">
    <div class="box-header with-border">
        <h3 class="box-title">Career Progression Details</h3>
    </div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Recording Date</th>
                <th>Progression Type</th>
                <th>Name/Title</th>
                <th>Description</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeCareerRecords as $record)
                <tr>
                    <td>{{ $record->recording_date ? $record->recording_date->format('d M Y') : $record->created_at->format('d M Y') }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $record->career_type)) }}</td>
                    <td>{{ $record->name ?: '-' }}</td>
                    <td>{{ $record->description ?: '-' }}</td>
                    <td>{{ $record->creator->first_name ?? 'Unknown' }} {{ $record->creator->last_name ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        No career progression records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



                </div>
            </div>
        </div>
    </div>
</section>

<!-- Administrative Record Modal -->
<div class="modal fade" id="adminRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Administrative Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Progress Indicator -->
                <div class="wizard-progress">
                    <div class="progress-steps">
                        <div class="step active" data-step="1">
                            <span class="step-number">1</span>
                            <span class="step-label">Record Type</span>
                        </div>
                        <div class="step" data-step="2">
                            <span class="step-number">2</span>
                            <span class="step-label">Details</span>
                        </div>
                        <div class="step" data-step="3">
                            <span class="step-number">3</span>
                            <span class="step-label">Summary</span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Record Type Selection -->
                <div class="wizard-step" id="step1">
                    <h5 class="step-title">Select Record Type</h5>
                    <div class="record-type-options">
                        <div class="option-card" data-type="disciplinary">
                            <div class="option-icon">
                                <i class="fa fa-gavel"></i>
                            </div>
                            <div class="option-content">
                                <h6>Disciplinary Action</h6>
                                <p>Record disciplinary actions including warnings, absenteeism, and misconduct</p>
                            </div>
                        </div>
                        <div class="option-card" data-type="health">
                            <div class="option-icon">
                                <i class="fa fa-medkit"></i>
                            </div>
                            <div class="option-content">
                                <h6>Health Records</h6>
                                <p>Record health-related incidents, sick letters, and workers compensation</p>
                            </div>
                        </div>
                        <div class="option-card" data-type="career">
                            <div class="option-icon">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <div class="option-content">
                                <h6>Career Progression</h6>
                                <p>Record awards, promotions, performance achievements, and additional responsibilities</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Details based on type -->
                <div class="wizard-step" id="step2" style="display: none;">
                    <!-- Disciplinary Action Options -->
                    <div class="detail-section" id="disciplinary-options">
                        <h5 class="step-title">Disciplinary Action Details</h5>
                        <div class="form-group">
                            <label>Select Action Type</label>
                            <select class="form-control" id="disciplinary-type" required>
                                <option value="">Choose action type</option>
                                <option value="absenteeism">Absenteeism Entry</option>
                                <option value="late-coming">Late Coming</option>
                                <option value="warning">Warning Letter</option>
                            </select>
                        </div>

                        <!-- Number of Days Field (for absenteeism and late-coming) -->
                        <div class="form-group" id="days-field" style="display: none;">
                            <label>Number of Days</label>
                            <input type="number" class="form-control" id="number-of-days" min="1" placeholder="Enter number of days">
                        </div>

                        <!-- Dynamic Date Fields Container -->
                        <div id="date-fields-container" style="display: none;">
                            <label>Specify Dates</label>
                            <div id="date-fields">
                                <!-- Date fields will be dynamically added here -->
                            </div>
                        </div>

                        <!-- Warning Letter Sub-options -->
                        <div id="warning-options" style="display: none;">
                            <div class="form-group">
                                <label>Warning Type</label>
                                <select class="form-control" id="warning-type">
                                    <option value="">Select warning type</option>
                                    <option value="performance">Performance</option>
                                    <option value="misconduct">Misconduct</option>
                                    <option value="negligence">Negligence</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Warning Level</label>
                                <select class="form-control" id="warning-level">
                                    <option value="">Select level</option>
                                    <option value="first">First Warning</option>
                                    <option value="second">Second Warning</option>
                                    <option value="third">Third Warning</option>
                                    <option value="final">Final Warning</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Comments</label>
                            <textarea class="form-control" id="disciplinary-comments" rows="3" placeholder="Add any additional comments..."></textarea>
                        </div>
                    </div>

                    <!-- Health Records Options -->
                    <div class="detail-section" id="health-options" style="display: none;">
                        <h5 class="step-title">Health Record Details</h5>
                        <div class="form-group">
                            <label>Record Type</label>
                            <select class="form-control" id="health-type" required>
                                <option value="">Choose record type</option>
                                <option value="sick-note">Sick Note</option>
                                <option value="workplace-injury">Workplace Injury</option>
                            </select>
                        </div>

                        <!-- Sick Note Sub-options -->
                        <div id="sick-note-options" style="display: none;">
                            <div class="form-group">
                                <label>Incident Type</label>
                                <select class="form-control" id="incident-type">
                                    <option value="">Select incident type</option>
                                    <option value="workers-compensation">Workers Compensation</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" id="health-description" rows="3" placeholder="Describe the health incident..."></textarea>
                        </div>
                    </div>

                    <!-- Career Progression Options -->
                    <div class="detail-section" id="career-options" style="display: none;">
                        <h5 class="step-title">Career Progression Details</h5>
                        <div class="form-group">
                            <label>Progression Type</label>
                            <select class="form-control" id="career-type" required>
                                <option value="">Choose progression type</option>
                                <option value="award">Award</option>
                                <option value="performance">Performance</option>
                                <option value="promotion">Promotion</option>
                                <option value="extra-responsibility">Extra Responsibility</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Name/Title</label>
                            <input type="text" class="form-control" id="career-name" placeholder="e.g., Employee of the Month, Team Lead">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" id="career-description" rows="3" placeholder="Describe the achievement or change..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Recording Date</label>
                            <input type="date" class="form-control" id="career-date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <!-- Step 3: Summary -->
                <div class="wizard-step" id="step3" style="display: none;">
                    <h5 class="step-title">Review & Submit</h5>
                    <div class="summary-grid" id="record-summary">
                        <!-- Summary will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevStep()" style="display: none;">Previous</button>
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">Next</button>
                <button type="button" class="btn btn-success" id="submitBtn" onclick="submitRecord()" style="display: none;">Submit Record</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let selectedRecordType = null;
    let formData = {};

    function openAdminRecordModal() {
        // Reset modal state
        currentStep = 1;
        selectedRecordType = null;
        formData = {};

        // Reset form fields
        $('#disciplinary-type').val('');
        $('#warning-type').val('');
        $('#warning-level').val('');
        $('#disciplinary-comments').val('');
        $('#number-of-days').val('');
        $('#date-fields').empty();
        $('#health-type').val('');
        $('#incident-type').val('');
        $('#health-description').val('');
        $('#career-type').val('');
        $('#career-name').val('');
        $('#career-description').val('');
        $('#career-date').val('{{ date('Y-m-d') }}');

        // Hide all dynamic sections
        $('#warning-options').hide();
        $('#days-field').hide();
        $('#date-fields-container').hide();
        $('#sick-letter-options').hide();

        // Show first step, hide others
        showStep(1);
        updateProgress();

        $('#adminRecordModal').modal('show');
    }

    function showStep(step) {
        // Hide all steps
        $('.wizard-step').hide();

        // Show current step
        $(`#step${step}`).show();

        // Update button visibility
        if (step === 1) {
            $('#prevBtn').hide();
            $('#nextBtn').show();
            $('#submitBtn').hide();
        } else if (step === 2) {
            $('#prevBtn').show();
            $('#nextBtn').show();
            $('#submitBtn').hide();
        } else if (step === 3) {
            $('#prevBtn').show();
            $('#nextBtn').hide();
            $('#submitBtn').show();
        }

        currentStep = step;
        updateProgress();
    }

    function updateProgress() {
        $('.step').removeClass('active');
        for (let i = 1; i <= currentStep; i++) {
            $(`.step[data-step="${i}"]`).addClass('active');
        }
    }

    function nextStep() {
        if (currentStep === 1) {
            if (!selectedRecordType) {
                alert('Please select a record type.');
                return;
            }
            showStep(2);
        } else if (currentStep === 2) {
            if (!validateStep2()) {
                return;
            }
            populateSummary();
            showStep(3);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    }

    // Step 1: Record Type Selection
    $('.option-card').click(function() {
        $('.option-card').removeClass('selected');
        $(this).addClass('selected');
        selectedRecordType = $(this).data('type');

        // Show/hide relevant detail sections
        $('.detail-section').hide();
        $(`#${selectedRecordType}-options`).show();
    });

    // Step 2: Dynamic form handling
    $('#disciplinary-type').change(function() {
        const selectedType = $(this).val();

        // Show/hide warning options
        if (selectedType === 'warning') {
            $('#warning-options').show();
            $('#days-field').hide();
            $('#date-fields-container').hide();
        } else if (selectedType === 'absenteeism' || selectedType === 'late-coming') {
            $('#warning-options').hide();
            $('#days-field').show();
            $('#number-of-days').val(''); // Reset
            $('#date-fields').empty(); // Clear previous dates
            $('#date-fields-container').hide();
        } else {
            $('#warning-options').hide();
            $('#days-field').hide();
            $('#date-fields-container').hide();
        }
    });

    // Handle number of days change to generate date fields
    $('#number-of-days').change(function() {
        const numDays = parseInt($(this).val());
        const dateFieldsContainer = $('#date-fields');

        if (numDays > 0 && numDays <= 30) { // Reasonable limit
            dateFieldsContainer.empty();

            for (let i = 1; i <= numDays; i++) {
                const dateFieldHtml = `
                    <div class="form-group">
                        <label>Day ${i} Date</label>
                        <input type="date" class="form-control absence-date" id="absence-date-${i}" name="absence_date_${i}" required>
                    </div>
                `;
                dateFieldsContainer.append(dateFieldHtml);
            }

            $('#date-fields-container').show();
        } else {
            dateFieldsContainer.empty();
            $('#date-fields-container').hide();
        }
    });

    $('#health-type').change(function() {
        if ($(this).val() === 'sick-letter') {
            $('#sick-letter-options').show();
        } else {
            $('#sick-letter-options').hide();
        }
    });

    function validateStep2() {
        if (selectedRecordType === 'disciplinary') {
            const type = $('#disciplinary-type').val();
            if (!type) {
                alert('Please select a disciplinary action type.');
                return false;
            }
            if (type === 'warning') {
                if (!$('#warning-type').val() || !$('#warning-level').val()) {
                    alert('Please fill in all warning details.');
                    return false;
                }
            } else if (type === 'absenteeism' || type === 'late-coming') {
                const numDays = $('#number-of-days').val();
                if (!numDays || numDays < 1) {
                    alert('Please enter the number of days.');
                    return false;
                }

                // Check if all date fields are filled
                const dateFields = $('.absence-date');
                let allDatesFilled = true;
                dateFields.each(function() {
                    if (!$(this).val()) {
                        allDatesFilled = false;
                        return false;
                    }
                });

                if (!allDatesFilled) {
                    alert('Please fill in all date fields.');
                    return false;
                }
            }
        } else if (selectedRecordType === 'health') {
            if (!$('#health-type').val()) {
                alert('Please select a health record type.');
                return false;
            }
        } else if (selectedRecordType === 'career') {
            if (!$('#career-type').val()) {
                alert('Please select a career progression type.');
                return false;
            }
        }
        return true;
    }

    function populateSummary() {
        formData = {
            record_type: selectedRecordType,
            employee_id: {{ $employee->id }},
            employee_name: '{{ $employee->first_name }} {{ $employee->last_name }}'
        };

        let summaryHtml = '<div class="summary-item"><span class="summary-label">Employee:</span><span class="summary-value">' + formData.employee_name + '</span></div>';
        summaryHtml += '<div class="summary-item"><span class="summary-label">Record Type:</span><span class="summary-value">' + selectedRecordType.charAt(0).toUpperCase() + selectedRecordType.slice(1) + '</span></div>';

        if (selectedRecordType === 'disciplinary') {
            formData.disciplinary_type = $('#disciplinary-type').val();
            formData.comments = $('#disciplinary-comments').val();

            let displayType = formData.disciplinary_type;
            if (formData.disciplinary_type === 'absenteeism') {
                displayType = 'Absenteeism Entry';
            } else if (formData.disciplinary_type === 'late-coming') {
                displayType = 'Late Coming';
            } else if (formData.disciplinary_type === 'warning') {
                displayType = 'Warning Letter';
            }

            summaryHtml += '<div class="summary-item"><span class="summary-label">Action Type:</span><span class="summary-value">' + displayType + '</span></div>';

            if (formData.disciplinary_type === 'warning') {
                formData.warning_type = $('#warning-type').val();
                formData.warning_level = $('#warning-level').val();

                summaryHtml += '<div class="summary-item"><span class="summary-label">Warning Type:</span><span class="summary-value">' + formData.warning_type + '</span></div>';
                summaryHtml += '<div class="summary-item"><span class="summary-label">Warning Level:</span><span class="summary-value">' + formData.warning_level + '</span></div>';
            } else if (formData.disciplinary_type === 'absenteeism' || formData.disciplinary_type === 'late-coming') {
                formData.number_of_days = $('#number-of-days').val();

                // Collect all absence dates
                const absenceDates = [];
                $('.absence-date').each(function() {
                    if ($(this).val()) {
                        absenceDates.push($(this).val());
                    }
                });
                formData.absence_dates = absenceDates;

                summaryHtml += '<div class="summary-item"><span class="summary-label">Number of Days:</span><span class="summary-value">' + formData.number_of_days + '</span></div>';
                summaryHtml += '<div class="summary-item"><span class="summary-label">Dates:</span><span class="summary-value">' + absenceDates.join(', ') + '</span></div>';
            }

            if (formData.comments) {
                summaryHtml += '<div class="summary-item"><span class="summary-label">Comments:</span><span class="summary-value">' + formData.comments + '</span></div>';
            }

        } else if (selectedRecordType === 'health') {
            formData.health_type = $('#health-type').val();
            formData.description = $('#health-description').val();

            let displayType = formData.health_type;
            if (formData.health_type === 'sick-letter') {
                displayType = 'Sick Letter';
            } else if (formData.health_type === 'workplace-injury') {
                displayType = 'Workplace Injury';
            }

            summaryHtml += '<div class="summary-item"><span class="summary-label">Record Type:</span><span class="summary-value">' + displayType + '</span></div>';

            if ($('#incident-type').val()) {
                formData.incident_type = $('#incident-type').val();
                summaryHtml += '<div class="summary-item"><span class="summary-label">Incident Type:</span><span class="summary-value">' + formData.incident_type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</span></div>';
            }

            summaryHtml += '<div class="summary-item"><span class="summary-label">Record Type:</span><span class="summary-value">' + displayType + '</span></div>';

            if ($('#incident-type').val()) {
                formData.incident_type = $('#incident-type').val();
                summaryHtml += '<div class="summary-item"><span class="summary-label">Incident Type:</span><span class="summary-value">' + formData.incident_type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</span></div>';
            }

            if (formData.description) {
                summaryHtml += '<div class="summary-item"><span class="summary-label">Description:</span><span class="summary-value">' + formData.description + '</span></div>';
            }

        } else if (selectedRecordType === 'career') {
            formData.career_type = $('#career-type').val();
            formData.career_name = $('#career-name').val();
            formData.description = $('#career-description').val();
            formData.recording_date = $('#career-date').val();

            summaryHtml += '<div class="summary-item"><span class="summary-label">Progression Type:</span><span class="summary-value">' + formData.career_type + '</span></div>';
            summaryHtml += '<div class="summary-item"><span class="summary-label">Name/Title:</span><span class="summary-value">' + (formData.career_name || 'N/A') + '</span></div>';
            summaryHtml += '<div class="summary-item"><span class="summary-label">Date:</span><span class="summary-value">' + formData.recording_date + '</span></div>';

            if (formData.description) {
                summaryHtml += '<div class="summary-item"><span class="summary-label">Description:</span><span class="summary-value">' + formData.description + '</span></div>';
            }
        }

        $('#record-summary').html(summaryHtml);
    }

    function submitRecord() {
        // Send the data to the backend
        $.ajax({
            url: '{{ url("hr/administrative-records") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Administrative record submitted successfully!');
                $('#adminRecordModal').modal('hide');
                // Optionally reload the page or update the UI to show the new record
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('Error submitting record: ' + xhr.responseJSON?.message || 'Unknown error');
            }
        });
    }
</script>

@endsection