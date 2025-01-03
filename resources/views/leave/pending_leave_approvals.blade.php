@extends('layouts.master')

@section('title')
    Pending Leave Approvals
@endsection



@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Leave Approvals</h3>
        </div>
        <div class="box-body table-responsive">
            @if ($leave->isEmpty())
                <p>No pending approvals found.</p>
            @else
                <table class="table table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Reason</th>
                            <th>Notes</th>
                            <th>Commencement Date</th>
                            <th>Return Date</th>
                            <th>Requested Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leave as $leave)
                            <tr>
                                <td>{{ $leave->id }}</td>
                                <td>{{ $leave->first_name }} {{ $leave->last_name }}</td>
                                <td>{{ $leave->office->name }}</td>
                                <td>{{ $leave->department }}</td>
                                <td>{{ $leave->position }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>{{ $leave->notes }}</td>
                                <td>{{ $leave->commencement_date }}</td>
                                <td>{{ $leave->return_date }}</td>
                                <td>{{ $leave->date_requested }}</td>
                                <td>
                                <div style="display: inline-block;">
                                    <form method="POST" action="{{ route('leave.approve', $leave->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-xs" style="padding: 1px 2px; font-size: 10px;">Approve</button>
                                    </form>
                                </div>
                                <div style="display: inline-block;">    
                                    <form method="POST" action="{{ route('leave.decline', $leave->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs" style="padding: 1px 2px; font-size: 10px;">Decline</button>
                                    </form>
                                </div>
                                </td>
                            </tr>
                            <!----->
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true, 
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[4, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [0, 1, 2, 4, 5, 6, 7, 8, 9]}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    });
</script>
@endsection
