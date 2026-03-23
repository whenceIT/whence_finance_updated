@extends('layouts.master')

@section('title', $employee->full_name . ' - Employee Dashboard')

@section('content')
<section class="content-header">
    <h1>
        Employee Dashboard
        <small>{{ $employee->full_name }}</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    @if($employee->image)
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset($employee->image) }}" alt="{{ $employee->full_name }}">
                    @else
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/default-user.png') }}" alt="Default Image">
                    @endif

                    <h3 class="profile-username text-center">{{ $employee->full_name }}</h3>
                    <p class="text-muted text-center">{{ optional($employee->role)->name ?? 'No Role' }}</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Office</b> <span class="pull-right">{{ optional($employee->office)->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Gender</b> <span class="pull-right">{{ $employee->gender ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b> <span class="pull-right">{{ $employee->employment_status ?? 'N/A' }}</span>
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

        <!-- <div class="col-md-9">
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
                                <td>{{ $employee->full_name }}</td>
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
                                <td>{{ $employee->nrc ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ $employee->date_of_birth ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date Hired</th>
                                <td>{{ $employee->date_hired ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Office</th>
                                <td>{{ optional($employee->office)->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>{{ optional($employee->role)->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Employment Status</th>
                                <td>{{ $employee->employment_status ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Performance --}}
                    <div class="tab-pane" id="performance">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Target Score</th>
                                    <th>Actual Score</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->performances as $performance)
                                    <tr>
                                        <td>{{ $performance->period }}</td>
                                        <td>{{ $performance->target_score }}</td>
                                        <td>{{ $performance->actual_score }}</td>
                                        <td>{{ $performance->remarks }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No performance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Payroll --}}
                    <div class="tab-pane" id="payroll">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Basic Salary</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->payrolls as $payroll)
                                    <tr>
                                        <td>{{ $payroll->month }}</td>
                                        <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                                        <td>{{ number_format($payroll->allowances, 2) }}</td>
                                        <td>{{ number_format($payroll->deductions, 2) }}</td>
                                        <td>{{ number_format($payroll->net_pay, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No payroll records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Leave --}}
                    <div class="tab-pane" id="leave">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaves as $leave)
                                    <tr>
                                        <td>{{ $leave->leave_type }}</td>
                                        <td>{{ $leave->start_date }}</td>
                                        <td>{{ $leave->end_date }}</td>
                                        <td>{{ $leave->days }}</td>
                                        <td>{{ $leave->status }}</td>
                                        <td>{{ $leave->reason }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No leave records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Advances --}}
                    <div class="tab-pane" id="advances">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Date Issued</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->advances as $advance)
                                    <tr>
                                        <td>{{ number_format($advance->amount, 2) }}</td>
                                        <td>{{ $advance->date_issued }}</td>
                                        <td>{{ $advance->status }}</td>
                                        <td>{{ $advance->remarks }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No advance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</section>
@endsection