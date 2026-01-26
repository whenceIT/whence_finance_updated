@extends('layouts.master')
@section('title')
    {{ trans_choice('general.audit_trail',2) }} - User Details
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">  {{ trans_choice('general.audit_trail',2) }} - User Details </h3>
            <div class="box-tools pull-right">
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th> {{ trans_choice('general.action',1) }}</th>
                        <th> {{ trans_choice('general.module',1) }}</th>
                        <th> {{ trans_choice('general.user',1) }}</th>
                        <th> {{ trans_choice('general.note',2) }}</th>
                        <th> {{ trans_choice('general.date',2) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->action }}</td>
                            <td>{{ $key->module }}</td>
                            <td>
                                <a href="{{url('user/'.$key->user_id.'/show')}}"> {{$key->user ? $key->user->first_name . ' ' . $key->user->last_name : ''}}</a>
                            </td>
                            <td>{{ $key->notes }}</td>
                            <td>{{ $key->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            {!! $data->links() !!}
        </div>
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            dom: 'frti',
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": true,
            "order": [[4, "desc"]],
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
    });
</script>
@endsection