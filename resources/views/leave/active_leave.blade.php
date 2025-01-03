@extends('layouts.master')
@section('title')
    Active Leave
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Active Leave</h3>
        </div>
        <div class="box-body table-responsive">
            @if ($leave->isEmpty())
                <p>No active leave found.</p>
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
                            <th>Commencement Date</th>
                            <th>Return Date</th>
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
                                <td>{{ $leave->commencement_date }}</td>
                                <td>{{ $leave->return_date }}</td>
                                <td>
                                    <a href="{{ route('leave.show', $leave->id) }}" class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <!----->
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
                {"orderable": false, "targets": [0, 2, 6, 7, 9, 10]},
                
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

