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
                  

              
                </div>
            </div>
        </div>
    </div>
</section>
@endsection