@extends('layouts.master')
@section('title')
    Pending Recovery Case Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Recovery Case Approvals</h3>
            <div class="box-tools pull-right">
                <span class="label label-info">{{ count($data) }} Pending</span>
            </div>
        </div>
        <div class="box-body table-responsive">
            @if(count($data) > 0)
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Case Number</th>
                    <th>Client</th>
                    <th>Loan ID</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Assigned Specialist</th>
                    <th>Outstanding Amount</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $case)
                    <tr>
                        <td>
                            <a href="{{ url('recovery/case/'.$case->id.'/show') }}" data-toggle="tooltip" title="Click to view">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td>
                            @if($case->client)
                                {{ $case->client->first_name }} {{ $case->client->last_name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($case->loan)
                                <a href="{{ url('loan/'.$case->loan->id.'/show') }}">
                                    {{ $case->loan->id }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="label label-primary">{{ ucfirst(str_replace('_', ' ', $case->category)) }}</span>
                        </td>
                        <td>
                            <span class="label label-{{ $case->status_color }}">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                        </td>
                        <td>
                            @if($case->assignedSpecialist)
                                {{ $case->assignedSpecialist->first_name }} {{ $case->assignedSpecialist->last_name }}
                            @else
                                Unassigned
                            @endif
                        </td>
                        <td>{{ number_format($case->loan_outstanding_amount, 2) }}</td>
                        <td>
                            <a href="{{ url('loan/recovery_case_approve/'.$case->id) }}"
                               onclick="return confirm('Are you sure you want to approve this recovery case?')"
                               class="btn btn-xs btn-success">
                                Approve
                            </a>
                            <a href="{{ url('loan/recovery_case_decline/'.$case->id) }}"
                               onclick="return confirm('Are you sure you want to decline and delete this recovery case?')"
                               class="btn btn-xs btn-danger">
                                Decline
                            </a>
                            <a href="{{ url('recovery/case/'.$case->id.'/show') }}"
                               class="btn btn-xs btn-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No pending recovery cases to approve.
            </div>
            @endif
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [7]}
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
    </script>
@endsection