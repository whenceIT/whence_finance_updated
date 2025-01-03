@extends('layouts.master')

@section('title', 'Active Leave Details')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Current Leave Details</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    
                    <tr>
                        <th>User</th>
                        <td>{{ $leave->first_name . ' ' . $leave->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Branch</th>
                        <td>{{ $leave->office->name }}</td> <!-- Assuming office relation -->
                    </tr>
                    <tr>
                        <th>Whence Department</th>
                        <td>{{ $leave->department }}</td>
                    </tr>
                    <tr>
                        <th>Employee Position</th>
                        <td>{{ $leave->position }}</td>
                    </tr>
                    <tr>
                        <th>Reason for Leave</th>
                        <td>{{ $leave->reason }}</td>
                    </tr>
                    <tr>
                        <th>Date Approved</th>
                        <td>{{ $leave->date_approved }}</td>
                    </tr>
                    <tr>
                        <th>Leave commencement date</th>
                        <td>{{ $leave->commencement_date }}</td>
                    </tr>
                    <tr>
                        <th>Leave return date</th>
                        <td>{{ $leave->return_date }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Leave Statistics</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Days Taken</th>
                            <!-- Add more headers as needed -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveReasons as $reason => $days)
                        <tr>
                            <td>{{ $reason }}</td>
                            <td>{{ $days }}</td>
                            <!-- Add more columns as needed -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">User's Leave Records</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userLeaveRecords as $record)
                        <tr>
                            <td>{{ $record->user->first_name }} {{ $record->user->last_name }}</td> <!-- Assuming user relation -->
                            <td>{{ $record->reason }}</td>
                            <td>{{ $record->commencement_date }} - {{ $record->return_date }}</td>
                            <!-- Add more columns as needed -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 50px;">
    <div class="col-md-12">
        <div class="calendar-container">
            {!! $calendarHtml !!}
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('.table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
        });
    });
</script>
@endsection

