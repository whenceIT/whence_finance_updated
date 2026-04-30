@extends('layouts.master')

@section('title', 'Administrative Records Management')

@section('content')
<section class="content-header">
    <h1>
        <small>Approve, review, and manage employee administrative records</small>
    </h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <ul class="nav nav-tabs">
                <li class="{{ request('tab', 'pending') === 'pending' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=pending') }}">
                        <i class="fa fa-clock-o"></i> Approvals
                        @if($tab === 'pending')
                            <span class="badge badge-warning">{{ $records->total() }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ $tab === 'active' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=active') }}">
                        <i class="fa fa-check"></i> Active
                        @if($tab === 'active')
                            <span class="badge badge-success">{{ $records->total() }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ $tab === 'declined' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=declined') }}">
                        <i class="fa fa-times"></i> Declined
                        @if($tab === 'declined')
                            <span class="badge badge-danger">{{ $records->total() }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="box-body">
            @if($records->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Record Type</th>
                                <th>Details</th>
                                <th>Created By</th>
                                <th>Date</th>
                                @if($tab === 'pending')
                                    <th>Actions</th>
                                @elseif($tab === 'declined')
                                    <th>Decline Reason</th>
                                @else
                                    <th>Approved By</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->employee->first_name }} {{ $record->employee->last_name }}</strong><br>
                                        <small class="text-muted">{{ $record->employee->employee_number ?? 'No ID' }}</small>
                                    </td>
                                    <td>
                                        <span class="label label-primary">{{ ucfirst($record->record_type) }}</span>
                                    </td>
                                    <td>
                                        @if($record->record_type === 'disciplinary')
                                            <strong>Type:</strong> {{ ucfirst(str_replace('-', ' ', $record->disciplinary_type)) }}<br>
                                            @if($record->warning_type)
                                                <strong>Warning:</strong> {{ ucfirst($record->warning_type) }} ({{ ucfirst($record->warning_level) }})<br>
                                            @endif
                                            @if($record->number_of_days)
                                                <strong>Number of Days:</strong> {{ $record->number_of_days }}<br>
                                                @if($record->absence_dates)
                                                    <strong>Dates:</strong> {{ collect($record->absence_dates)->map(function($date) { return \Carbon\Carbon::parse($date)->format('M j, Y'); })->join(', ') }}<br>
                                                @endif
                                            @endif
                                            @if($record->comments)
                                                <strong>Comments:</strong> {{ Str::limit($record->comments, 50) }}
                                            @endif
                                        @elseif($record->record_type === 'health')
                                            <strong>Type:</strong> {{ ucfirst(str_replace('-', ' ', $record->health_type)) }}<br>
                                            @if($record->incident_type)
                                                <strong>Incident:</strong> {{ ucfirst(str_replace('-', ' ', $record->incident_type)) }}<br>
                                            @endif
                                            @if($record->description)
                                                <strong>Description:</strong> {{ Str::limit($record->description, 50) }}
                                            @endif
                                        @elseif($record->record_type === 'career')
                                            <strong>Type:</strong> {{ ucfirst(str_replace('-', ' ', $record->career_type)) }}<br>
                                            @if($record->name)
                                                <strong>Name:</strong> {{ $record->name }}<br>
                                            @endif
                                            @if($record->description)
                                                <strong>Description:</strong> {{ Str::limit($record->description, 50) }}
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $record->creator->first_name }} {{ $record->creator->last_name }}</td>
                                    <td>{{ $record->created_at->format('d M Y') }}</td>
                                    @if($tab === 'pending')
                                        <td>
                                            <form action="{{ url('hr/administrative-records/'.$record->id.'/approve') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Are you sure you want to approve this record?')">
                                                    <i class="fa fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="declineRecord({{ $record->id }}, '{{ addslashes($record->employee->first_name.' '.$record->employee->last_name) }}')">
                                                <i class="fa fa-times"></i> Decline
                                            </button>
                                        </td>
                                    @elseif($tab === 'declined')
                                        <td>
                                            <span class="text-danger">{{ $record->decline_reason }}</span><br>
                                            <small class="text-muted">Declined by: {{ $record->approver->first_name ?? 'Unknown' }} {{ $record->approver->last_name ?? '' }}</small>
                                        </td>
                                    @else
                                        <td>
                                            {{ $record->approver->first_name ?? 'Unknown' }} {{ $record->approver->last_name ?? '' }}<br>
                                            <small class="text-muted">{{ $record->approved_at ? $record->approved_at->format('d M Y') : '' }}</small>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center">
                    {{ $records->appends(['tab' => $tab])->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    @if($tab === 'pending')
                        <i class="fa fa-clock-o fa-3x"></i>
                        <h4>No pending records</h4>
                        <p>All administrative records have been processed.</p>
                    @elseif($tab === 'active')
                        <i class="fa fa-check fa-3x"></i>
                        <h4>No active records</h4>
                        <p>No approved administrative records found.</p>
                    @else
                        <i class="fa fa-times fa-3x"></i>
                        <h4>No declined records</h4>
                        <p>No declined administrative records found.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Decline Administrative Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="declineForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="decline_reason">Reason for Decline <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="decline_reason" name="decline_reason" rows="4" placeholder="Please provide a reason for declining this record..." required></textarea>
                    </div>
                    <div id="employeeInfo" class="alert alert-info">
                        <!-- Employee info will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function declineRecord(recordId, employeeName) {
    $('#declineForm').attr('action', '{{ url("hr/administrative-records") }}/' + recordId + '/decline');
    $('#employeeInfo').html('<strong>Employee:</strong> ' + employeeName + '<br><strong>Record ID:</strong> ' + recordId);
    $('#decline_reason').val('');
    $('#declineModal').modal('show');
}
</script>
@endsection