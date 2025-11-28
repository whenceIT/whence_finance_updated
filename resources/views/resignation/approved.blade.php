@extends('layouts.master')

@section('title', 'Approved Resignations')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Approved Resignations</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Resignation Date</th>
                    <th>Manager</th>
                    <th>Admin</th>
                    <th>Approved Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approved as $resignation)
                <tr>
                    <td>{{ $resignation->user->first_name }} {{ $resignation->user->last_name }}</td>
                    <td>{{ date('d M Y', strtotime($resignation->resignation_date)) }}</td>
                    <td>{{ $resignation->manager ? $resignation->manager->first_name . ' ' . $resignation->manager->last_name : 'N/A' }}</td>
                    <td>{{ $resignation->admin ? $resignation->admin->first_name . ' ' . $resignation->admin->last_name : 'N/A' }}</td>
                    <td>{{ $resignation->admin_approved_at ? date('d M Y', strtotime($resignation->admin_approved_at)) : 'N/A' }}</td>
                    <td>
                        <button class="btn btn-xs btn-info view-resignation" data-id="{{ $resignation->id }}">View</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No Approved Resignations</td>
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