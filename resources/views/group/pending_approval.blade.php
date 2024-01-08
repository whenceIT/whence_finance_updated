@extends('layouts.master')
@section('title')
    {{ trans_choice('general.group',2) }} {{ trans_choice('general.pending',1) }} {{ trans_choice('general.approval',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.group',2) }} {{ trans_choice('general.pending',1) }} {{ trans_choice('general.approval',1) }}</h3>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('groups.create'))
                    <a href="{{ url('group/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.group',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.account',1) }} {{ trans('general.id') }}</th>
                    <th>{{ trans('general.external_id') }}</th>
                    <th>{{ trans_choice('general.branch',1) }}</th>
                    <th>{{ trans_choice('general.staff',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>
                            {{ $key->name }}
                        </td>
                        <td>{{ $key->account_no }}</td>
                        <td>{{ $key->external_id }}</td>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->staff))
                                {{$key->staff->first_name}}   {{$key->staff->last_name}}
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('groups.view'))
                                        <li>
                                            <a href="{{ url('group/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('groups.update'))
                                        <li>
                                            <a href="{{ url('group/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('groups.delete'))
                                        <li>
                                            <a href="{{ url('group/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i>
                                                {{ trans('general.delete') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
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
            "order": [[0, "asc"]],
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
