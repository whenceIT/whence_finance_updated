@extends('layouts.master')
@section('title')
    Pending Top Up Approvals
@endsection
@section('content')
 <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Top Up Approvals </h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan</th>
                    <th>Branch</th>
                    <th>Loan Officer</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $key)
                    <tr>
                        <td>
                            @if($key->loan_id)
                            <a href="{{ url('loan/'.$key->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">{{ $key->loan_id }}</a>
                            @endif
                        </td>
                        <td>
                            @if($key->office)
                                {{ $key->office->name }}
                            @endif
                        </td>
                        <td>
                            {{ $key->createdBy ? $key->createdBy->first_name.' '.$key->createdBy->last_name : '' }}
                        </td>
                        <td>
                            @if($key->loan && $key->loan->client)
                                {{ $key->loan->client->first_name.' '.$key->loan->client->middle_name.' '.$key->loan->client->last_name }}
                            @endif
                        </td>
                        <td>{{ number_format($key->amount, 2) }}</td>
                        <td>{{ $key->status }}</td>
                        <td>{{ $key->date }}</td>
                        <td>
                            @if($key->loan_id)
                            <a href="{{ url('loan/'.$key->loan_id.'/'.$key->id.'/approve_top_up') }}" onclick="return confirm('Are you sure you want to approve this top-up?')">
                                <span class="label label-success">Approve</span>
                            </a>
                            @endif
                            <a href="{{ url('loan/'.$key->id.'/decline_top_up')}}" onclick="return confirm('Are you sure you want to decline this top-up?')">
                                <span class="label label-danger" style="color:red">Decline</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No pending top-up approvals found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
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
