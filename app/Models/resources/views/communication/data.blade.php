@extends('layouts.master')
@section('title')
    {{ trans_choice('general.campaign',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.campaign',2) }}</h3>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('communication.create'))
                    <a href="{{ url('communication/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.campaign',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.type',1) }}</th>
                    <th>{{ trans_choice('general.message',2) }}</th>
                    <th>{{ trans_choice('general.created_by',1) }}</th>
                    <th>{{ trans_choice('general.campaign',1) }} {{ trans_choice('general.type',1) }}</th>
                    <th>{{ trans_choice('general.status',1) }}</th>
                    <th>{{ trans_choice('general.date',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            @if($key->type=="sms")
                                {{ trans_choice('general.sms',1) }}
                            @endif
                            @if($key->type=="email")
                                {{ trans_choice('general.email',1) }}
                            @endif
                        </td>
                        <td>{{ \App\Helpers\GeneralHelper::limit_text(strip_tags($key->message),4) }}</td>
                        <td>
                            @if(!empty($key->created_by))
                                {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                            @endif
                        </td>
                        <td>
                            @if($key->recurrence_type=="none")
                                {{ trans_choice('general.direct',1) }}
                            @endif
                            @if($key->recurrence_type=="schedule")
                                {{ trans_choice('general.schedule',1) }}
                            @endif
                        </td>
                        <td>
                            @if($key->status=="pending")
                                {{ trans_choice('general.pending',1) }}
                            @endif
                            @if($key->status=="active")
                                {{ trans_choice('general.active',1) }}
                            @endif
                            @if($key->status=="declined")
                                {{ trans_choice('general.declined',1) }}
                            @endif
                            @if($key->status=="inactive")
                                {{ trans_choice('general.inactive',1) }}
                            @endif
                        </td>
                        <td>{{ $key->created_at }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('communication.view'))
                                        <li class="hidden">
                                            <a href="{{ url('communication/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('communication.update') && $key->recurrence_type!="none")
                                        <li class="hidden">
                                            <a href="{{ url('communication/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('communication.delete'))
                                        <li>
                                            <a href="{{ url('communication/'.$key->id.'/delete') }}"
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
