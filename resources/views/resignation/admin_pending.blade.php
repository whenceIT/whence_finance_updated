@extends('layouts.master')

@section('title', 'Pending Resignations - Final Approvals')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Pending Resignations For Final Approval</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Resignation Date</th>
                    <th>Manager Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $resignation)
                <tr>
                    <td>{{ $resignation->user->first_name }} {{ $resignation->user->last_name }}</td>
                    <td>{{ date('d M Y', strtotime($resignation->resignation_date)) }}</td>
                    <td>{{ $resignation->manager ? $resignation->manager->first_name . ' ' . $resignation->manager->last_name : 'N/A' }}</td>
                    <td>
                        <button class="btn btn-xs btn-info view-resignation" data-id="{{ $resignation->id }}">View</button>
                        @if($resignation->user_id != Sentinel::getUser()->id)
                        <button class="btn btn-xs btn-success confirm-approve-btn" data-id="{{ $resignation->id }}" data-action="approve">Approve</button>
                        <button class="btn btn-xs btn-danger confirm-decline-btn" data-id="{{ $resignation->id }}" data-action="decline">Decline</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No Pending Resignations</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> Confirm Action
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">Are you sure you want to proceed with this action?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
                <button type="button" class="btn btn-primary" id="proceedBtn">Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="approvalForm" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modalTitle"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="modalSubmitBtn"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var currentId, currentAction;

$('.confirm-approve-btn, .confirm-decline-btn').on('click', function() {
    currentId = $(this).data('id');
    currentAction = $(this).data('action');
    var message = currentAction === 'approve' ?
        'Are you sure you want to approve this resignation? The user account will be deactivated.' :
        'Are you sure you want to decline this resignation?';
    $('#confirmMessage').text(message);
    $('#confirmModal').modal('show');
});

$('#proceedBtn').on('click', function() {
    $('#confirmModal').modal('hide');
    var title = currentAction === 'approve' ? 'Approve Resignation' : 'Decline Resignation';
    var btnClass = currentAction === 'approve' ? 'btn-success' : 'btn-danger';
    var btnText = currentAction === 'approve' ? 'Approve' : 'Decline';

    $('#modalTitle').text(title);
    $('#modalSubmitBtn').removeClass('btn-success btn-danger').addClass(btnClass).text(btnText);
    $('#approvalForm').attr('action', '{{ url("resignation/admin/approve") }}/' + currentId + '?action=' + currentAction);
    $('#approvalModal').modal('show');
});
</script>

@include('resignation.partials.view_modal')

<script>
$(document).ready(function() {
    $('.view-resignation').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ url("resignation/show") }}/' + id,
            type: 'GET',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                $('#resignationModalBody').html(response.html);
                $('#viewResignationModal').modal('show');
            },
            error: function() {
                alert('Error loading resignation details.');
            }
        });
    });
});
</script>
@endsection