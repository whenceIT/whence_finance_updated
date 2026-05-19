@extends('layouts.master')
@section('title')
    {{ trans_choice('general.user',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.user',2) }}</h3>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('users.create'))
                    <a href="{{ url('user/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans('general.gender') }}</th>
                    <th>Province</th>
                    <th>Branch</th>
                    <th>{{ trans('general.phone') }}</th>
                    <th>{{ trans_choice('general.email',1) }}</th>
                    <th>NRC ID</th>
                    <th>Role</th>
                    <th>Position</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                @if($key->role && $key->role->role_id != 2)
      
                    <tr>
                        <td><span class="badge badge-info">{{ $key->id }}</span></td>
                        <td>{{ $key->first_name }} {{ $key->last_name }}</td>
                        <td>
                            @if($key->gender=="male")
                                {{trans_choice('general.male',1)}}
                            @endif
                            @if($key->gender=="female")
                                {{trans_choice('general.female',1)}}
                            @endif
                            @if($key->gender=="other")
                                {{trans_choice('general.other',1)}}
                            @endif
                            @if($key->gender=="unspecified")
                                {{trans_choice('general.unspecified',1)}}
                            @endif
                        </td>
                        <td>{{ $key->province->name ?? '' }}</td>
                        <td>{{ $key->office->name ?? '' }}</td>
                        <td>{{ $key->phone }}</td>
                        <td>{{ $key->email }}</td>
                        <td>{{ $key->nrc_id }}</td>
                        <td>{{ $key->role->role->name ?? '' }}</td>
                        <td>{{ $key->position_name }}</td>

                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('users.view'))
                                        <li>
                                            <a href="{{ url('user/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('users.update'))
                                        <li>
                                            <a href="{{ url('user/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>
                                    @endif
                                        @if(Sentinel::hasAccess('users.view'))
                                        <li>
                                            <a href="{{ url('user/'.$key->id.'/qr-download') }}"><i
                                                        class="fa fa-search"></i>
                                                Download QRCode</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('users.delete'))
                                        <li>
                                            <a href="{{ url('user/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i>
                                                {{ trans('general.delete') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
           @endif
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
    "order": [[0, "asc"]],
     "columnDefs": [
         {"orderable": false, "targets": [9]} // fixed index
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
