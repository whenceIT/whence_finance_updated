@extends('layouts.master')
@section('title')
    {{ trans_choice('general.client', 2) }} - Recovered Clients
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-users"></i> Recovered Clients
            </h3>
            <div class="box-tools pull-right">
                <ul class="nav nav-tabs">
                    <li><a href="{{ url('client/dormant_clients') }}"><i class="fa fa-users"></i> Dormant Clients</a></li>
                    <li class="active"><a href="{{ url('client/recovered_clients') }}"><i class="fa fa-check-circle"></i> Recovered Clients</a></li>
                </ul>
            </div>
        </div>
        <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> 
                <strong>Recovered clients</strong> are active clients who have successfully completed their loan repayment and have a closed loan status.
            </div>

            @if($data->count() > 0)
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Account #</th>
                    <th>Client Name</th>
                    <th>Mobile</th>
                    <th>Office</th>
                    <th>Loan Officer</th>
                    <th>Last Closed Loan Date</th>
                    <th>Total Closed Loans</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $client)
                    <?php
                        $lastClosedLoan = $client->loans->first();
                    ?>
                    <tr>
                        <td>{{ $client->account_no ?? '-' }}</td>
                        <td>
                            <strong>{{ $client->first_name }} {{ $client->last_name }}</strong>
                        </td>
                        <td>{{ $client->mobile ?? '-' }}</td>
                        <td>{{ $client->office->name ?? '-' }}</td>
                        <td>
                            @if($client->staff)
                                {{ $client->staff->first_name }} {{ $client->staff->last_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($lastClosedLoan)
                                {{ \Carbon\Carbon::parse($lastClosedLoan->closed_at ?? $lastClosedLoan->created_at)->format('d M Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-gray">{{ $client->loans->count() }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-cog"></i> Actions
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    @if(Sentinel::hasAccess('clients.view'))
                                        <li>
                                            <a href="{{ url('client/' . $client->id . '/show') }}" target="_blank">
                                                <i class="fa fa-eye"></i> View Client
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-success text-center" style="padding: 40px;">
                <i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 15px;"></i>
                <h4>No Recovered Clients Found</h4>
                <p class="text-muted">No active clients with closed loans found at this time.</p>
            </div>
            @endif
        </div>
    </div>
@endsection

@include('components.kilo-alert')

@section('footer-scripts')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'Bfrtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [7]}
            ],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "Showing _START_ to _END_ of _TOTAL_ recovered clients",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "(filtered from _MAX_ total clients)",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: true
        });
    });
</script>
@endsection