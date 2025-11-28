@extends('layouts.master')

@section('title', 'Pending Resignations - Admin')

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
                        <button class="btn btn-xs btn-success approve-btn" data-id="{{ $resignation->id }}" data-action="approve">Approve</button>
                        <button class="btn btn-xs btn-danger decline-btn" data-id="{{ $resignation->id }}" data-action="decline">Decline</button>
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
$('.approve-btn, .decline-btn').on('click', function() {
    var id = $(this).data('id');
    var action = $(this).data('action');
    var title = action === 'approve' ? 'Approve Resignation' : 'Decline Resignation';
    var btnClass = action === 'approve' ? 'btn-success' : 'btn-danger';
    var btnText = action === 'approve' ? 'Approve' : 'Decline';

    $('#modalTitle').text(title);
    $('#modalSubmitBtn').removeClass('btn-success btn-danger').addClass(btnClass).text(btnText);
    $('#approvalForm').attr('action', '{{ url("resignation/admin/approve") }}/' + id + '?action=' + action);
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