@extends('layouts.master')

@section('title')
    Closed Advances
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Closed Advances</h3>
        </div>
        <div class="box-body table-responsive">
            @if ($closedAdvances->isEmpty())
                <p>No closed advances found.</p>
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
                            <th>Date Approved</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($closedAdvances as $advance)
                            <tr>
                                <td>{{ $advance->id }}</td>
                                <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                                <td>{{ $advance->office->name }}</td>
                                <td>{{ $advance->amount }}</td>
                                <td>{{ $advance->installments }}</td>
                                <td>{{ $advance->installment_amount }}</td>
                                <td>{{ $advance->date_approved }}</td>
                                
                                <td>{{ $advance->remaining_amount }}</td>
                                <td>
                        <a href="{{ route('advances.show', $advance->id) }}" class="btn btn-info btn-xs">
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
                {"orderable": false, "targets": [6, 7, 9]},
                
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
