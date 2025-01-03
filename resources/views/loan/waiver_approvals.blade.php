@extends('layouts.master')
@section('title')
    Pending Waiver Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Waiver Approvals</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th >Waiver Id</th>
                        <th >Loan Id</th>
                        <th >Branch</th>
                        <th >Client</th>
                        <th >Amount</th>
                        <th >Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWaivers as $waiver)
                        <tr>
                            <td>{{ $waiver->id }}</td>
                            <td>{{ $waiver->loan_id }}</td>
                            <td>{{ $waiver->office->name }}</td>
                            <td>
                                @if($waiver->loan && $waiver->loan->client)
                                    @if($waiver->loan->client->client_type == "individual")
                                        {{ $waiver->loan->client->first_name }} {{ $waiver->loan->client->middle_name }} {{ $waiver->loan->client->last_name }}
                                    @else
                                        {{ $waiver->loan->client->full_name }}
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ number_format($waiver->credit, 2) }}</td>
                            <td>{{ $waiver->created_at ? $waiver->created_at->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                <form action="{{ route('waiver.approve', $waiver->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-xs">Approve</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('waiver.decline', $waiver->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs">Decline</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
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

