@extends('layouts.master')
@section('title')
    {{ trans_choice('general.client',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.client',2) }}</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('clients.create'))
                    <a href="{{ url('client/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}
                    </a>
                @endif
            </div>
            <form id="search-form" action="{{ route('client.data') }}" method="GET" style="display: flex; justify-content: center;">
                <div class="input-group" style="width: 350px; margin-top: 15px;">
                    <input id="search-input" type="text" name="query" class="form-control" placeholder="Search by First Name or Last Name" value="{{ $query ?? '' }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(isset($query))
                            <button type="button" class="btn btn-default" id="clear-search">Clear</button>
                        @endif
                    </span>
                </div>
            </form>
        </div> 
        <div class="box-body table-responsive">
            @if(isset($query))
                <p>Showing results for: {{ $query }}</p>
            @endif
            @if(isset($data))
                <table class="table table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('general.name',1) }}</th>
                            <th>{{ trans_choice('general.client',1) }} {{ trans('general.id') }}</th>
                            <th>{{ trans('general.nrc_number') }}</th>
                            <th>{{ trans_choice('general.branch',1) }}</th>
                            <th>{{ trans_choice('general.staff',1) }}</th>
                            <th>{{ trans_choice('general.action',1) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key)
                            <tr>
                                <td>
                                    @if($key->client_type=="individual")
                                        {{ $key->first_name }}    {{ $key->last_name }}
                                    @else
                                        {{ $key->full_name }}
                                    @endif
                                </td>
                                <td>{{ $key->account_no }}</td>
                                <td>{{ $key->nrc_number }}</td>
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
                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-navicon"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            @if(Sentinel::hasAccess('clients.view'))
                                                <li>
                                                    <a href="{{ url('client/'.$key->id.'/show') }}">
                                                        <i class="fa fa-search"></i>
                                                        {{ trans_choice('general.detail',2) }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('clients.update'))
                                                <li>
                                                    <a href="{{ url('client/'.$key->id.'/edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                        {{ trans('general.edit') }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('clients.delete'))
                                                <li>
                                                    <a href="{{ url('client/'.$key->id.'/delete') }}" class="delete">
                                                        <i class="fa fa-trash"></i>
                                                        {{ trans('general.delete') }}
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
                <p>No clients found for the search query.</p>
            @endif
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#data-tableM').DataTable({
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
        $('#clear-search').click(function() {
            $('#search-input').val('');
            $('#search-form').submit();
        });
    </script>
@endsection
