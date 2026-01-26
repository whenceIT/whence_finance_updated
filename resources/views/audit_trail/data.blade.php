@extends('layouts.master')
@section('title')
    {{ trans_choice('general.audit_trail',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">  {{ trans_choice('general.audit_trail',2) }} </h3>
            <div class="box-tools pull-right">
            </div>
        </div>

        <div class="box-tools-section">
            <button id="quick-audit-btn" class="btn btn-primary">Quick Transactions Audit</button>
        </div>

        <!--<button id="manager-button" class="btn btn-primary">Managers Audit Trail</button>-->

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
                        <th>Actions</th>
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
                            <td>
                                <a href="{{ url('audit_trail/user/' . $key->user_id) }}" class="btn btn-sm btn-primary">View Details</a>
                            </td>
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

    <!-- Quick Audit Modal -->
    <div id="quick-audit-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Quick Audit</h4>
                </div>
                <form action="{{ url('audit_trail/quick_audit') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">Select Staff</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">-- Select Staff --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')

<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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

    $('#quick-audit-btn').click(function() {
        $('#quick-audit-modal').modal('show');
    });
</script>
@endsection

