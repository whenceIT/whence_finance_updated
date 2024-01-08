@extends('layouts.master')
@section('title')
    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.application',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.loan',1) }} {{ trans_choice('general.application',2) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.id',1) }}</th>
                    <th>{{ trans_choice('general.branch',1) }}</th>
                    <th>{{ trans_choice('general.client',1) }}</th>
                    <th>{{ trans_choice('general.product',1) }}</th>
                    <th>{{ trans_choice('general.amount',1) }}</th>
                    <th>{{ trans_choice('general.status',1) }}</th>
                    <th>{{ trans_choice('general.created_at',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach($data as $key)
                    <tr>
                        <td><a href="{{ url('portal/loan_application/'.$key->id.'/show') }}" data-toggle="tooltip"
                               title="Click to view">{{ $key->id }}</a></td>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                            @if($key->client_type=="client")
                                @if(!empty($key->client))
                                    @if($key->client->client_type=="individual")
                                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                    @else
                                        {{$key->client->full_name}}
                                    @endif
                                @endif
                            @endif
                            @if($key->client_type=="group")
                                {{$key->group->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->loan_product))
                                {{$key->loan_product->name}}
                            @endif
                        </td>
                        <td>{{ number_format($key->amount,2) }}</td>
                        <td>
                            @if($key->status=="pending")
                                {{ trans_choice('general.pending',1) }}
                            @endif
                            @if($key->status=="approved")
                                {{ trans_choice('general.approved',1) }}
                            @endif
                            @if($key->status=="disbursed")
                                {{ trans_choice('general.disbursed',1) }}
                            @endif
                            @if($key->status=="written_off")
                                {{ trans_choice('general.written_off',1) }}
                            @endif
                            @if($key->status=="rescheduled")
                                {{ trans_choice('general.rescheduled',1) }}
                            @endif
                            @if($key->status=="declined")
                                {{ trans_choice('general.declined',1) }}
                            @endif
                            @if($key->status=="withdrawn")
                                {{ trans_choice('general.withdrawn',1) }}
                            @endif
                        </td>
                        <td>{{ $key->created_at }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('loans.view'))
                                        <li>
                                            <a href="{{ url('loan/application/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
                                    @endif
                                    @if($key->status=="pending")
                                        @if(Sentinel::hasAccess('loans.create'))
                                            <li>
                                                <a href="{{ url('loan/application/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i>
                                                    {{ trans('general.delete') }}</a>
                                            </li>
                                        @endif
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
            "order": [[7, "desc"]],
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
