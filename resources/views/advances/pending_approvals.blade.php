@extends('layouts.master')

@section('title')
    Pending Approvals
@endsection



@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Approvals</h3>
        </div>
        <div class="box-body table-responsive">
            @if ($advances->isEmpty())
                <p>No pending approvals found.</p>
            @else
                <table class="table table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Amount</th>
                            <th>Installments</th>
                            <th>Installment Amount</th>
                            <th>Purpose</th>
                            <th>Payment Mode</th>
                            <th>Date Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advances as $advance)
                            <tr>
                                <td>{{ $advance->id }}</td>
                                <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                                <td>{{ $advance->office->name }}</td>
                                <td>{{ $advance->amount }}</td>
                                <td>{{ $advance->installments }}</td>
                                <td>{{ $advance->installment_amount }}</td>
                                <td>{{ $advance->purpose }}</td>
                                <td>{{ $advance->mode_of_payment }}</td>
                                <td>{{ $advance->date_requested }}</td>
                                <td>
                                <div style="display: inline-block;">
                                    <form method="POST" action="{{ route('advances.approve', $advance->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-xs" style="padding: 1px 2px; font-size: 10px;">Approve</button>
                                    </form>
                                </div>
                                <div style="display: inline-block;">    
                                    <form method="POST" action="{{ route('advances.decline', $advance->id) }}" style="display: inline;">
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