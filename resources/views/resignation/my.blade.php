@extends('layouts.master')

@section('title', 'My Resignations')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">My Resignations</h3>
        <div class="box-tools pull-right">
            @if($existing)
                <a href="{{ route('resignation.create') }}" class="btn btn-warning btn-sm">Update Current</a>
            @else
                <a href="{{ route('resignation.create') }}" class="btn btn-primary btn-sm">Submit New</a>
            @endif
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Resignation Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resignations as $resignation)
                <tr>
                    <td>{{ date('d M Y', strtotime($resignation->created_at)) }}</td>
                    <td>{{ date('d M Y', strtotime($resignation->resignation_date)) }}</td>
                    <td>
                        @if($resignation->status == 'pending')
                            <span class="label label-warning">{{ ucfirst($resignation->status) }}</span>
                        @elseif($resignation->status == 'manager_approved')
                            <span class="label label-info">Manager Approved</span>
                        @elseif($resignation->status == 'admin_approved')
                            <span class="label label-success">Approved</span>
                        @elseif($resignation->status == 'declined')
                            <span class="label label-danger">Declined</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-xs btn-info view-resignation" data-id="{{ $resignation->id }}">View</button>
                        @if(in_array($resignation->status, ['pending', 'manager_approved']))
                            <a href="{{ route('resignation.cancel', $resignation->id) }}" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to cancel this resignation?')">Cancel</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No resignations found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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