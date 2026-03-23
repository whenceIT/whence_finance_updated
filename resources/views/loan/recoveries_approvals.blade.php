@extends('layouts.master')
@section('title')
    Pending Debt Recovery Transaction Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Debt Recovery Transaction Approvals</h3>
            <div class="box-tools pull-right">
                <span class="label label-info">{{ count($data) }} Pending</span>
            </div>
        </div>
        <div class="box-body table-responsive">
            @if(count($data) > 0)
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Branch</th>
                    <th>Loan Officer</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Receipt #</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                <?php
                    $client_identification = $key->loan->client_id;
                    $client = \App\Models\Client::find($client_identification);
                ?>
                    <tr>
                        <td>
                            <a href="{{ url('loan/'.$key->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">
                                {{ $key->loan_id }}
                            </a>
                        </td>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->created_by))
                                {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                            @endif
                        </td>
                        @if(!empty($client))
                        <td>{{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}</td>
                        @else
                        <td>-</td>
                        @endif
                        <td>{{number_format($key->credit,2)}}</td>
                        <td>{{$key->date}}</td>
                        <td>
                            @if(!empty($key->payment_type_id))
                                <span class="label label-primary">
                                    {{ ucfirst(str_replace('_', ' ', $key->payment_type_id)) }}
                                </span>
                            @else
                                <span class="label label-default">Not Set</span>
                            @endif
                        </td>
                        <td>{{ $key->receipt_number ?? '-' }}</td>
                        <td>
                            <a href="{{ url('loan/recoveries_approve/'.$key->id) }}"
                               onclick="return confirm('Are you sure you want to approve this recovery payment?')">
                                <span class="label label-success">Approve</span>
                            </a>
                            <a href="{{ url('loan/recoveries_decline/'.$key->id) }}"
                               onclick="return confirm('Are you sure you want to decline this recovery payment?')">
                                <span class="label label-danger">Decline</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No pending debt recovery transactions to approve.
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
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [8]}
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
