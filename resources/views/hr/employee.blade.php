@extends('layouts.master')

@section('title', 'Employee Dashboard')

@section('content')


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
                  

              
                </div>
            </div>
        </div>
    </div>
</section>
@endsection