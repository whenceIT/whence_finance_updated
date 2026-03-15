@extends('layouts.master')
@section('title')
    {{ trans_choice('general.application',1) }} {{ trans_choice('general.detail',2) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> {{ trans_choice('general.application',1) }} {{ trans_choice('general.detail',2) }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{ trans('general.id') }}</td>
                            <td>{{ $loan_application->id }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.branch',1) }}</td>
                            <td>
                                @if(!empty($loan_application->office))
                                    {{$loan_application->office->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.client',1) }}</td>
                            <td>
                                @if($loan_application->client_type=="client")
                                    @if(!empty($loan_application->client))
                                        @if($loan_application->client->client_type=="individual")
                                            {{$loan_application->client->first_name}} {{$loan_application->client->middle_name}} {{$loan_application->client->last_name}}
                                        @else
                                            {{$loan_application->client->full_name}}
                                        @endif
                                    @endif
                                @endif
                                @if($loan_application->client_type=="group")
                                    {{$loan_application->group->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.product',1) }}</td>
                            <td>
                                @if(!empty($loan_application->loan_product))
                                    {{$loan_application->loan_product->name}}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>{{ trans_choice('general.amount',1) }}</td>
                            <td>{{ number_format($loan_application->amount,2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.status',1) }}</td>
                            <td>
                                @if($loan_application->status=="pending")
                                    {{ trans_choice('general.pending',1) }}
                                @endif
                                @if($loan_application->status=="approved")
                                    {{ trans_choice('general.approved',1) }}
                                @endif
                                @if($loan_application->status=="disbursed")
                                    {{ trans_choice('general.disbursed',1) }}
                                @endif
                                @if($loan_application->status=="written_off")
                                    {{ trans_choice('general.written_off',1) }}
                                @endif
                                @if($loan_application->status=="rescheduled")
                                    {{ trans_choice('general.rescheduled',1) }}
                                @endif
                                @if($loan_application->status=="declined")
                                    {{ trans_choice('general.declined',1) }}
                                @endif
                                @if($loan_application->status=="withdrawn")
                                    {{ trans_choice('general.withdrawn',1) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.created_at',1) }}</td>
                            <td>{{ $loan_application->created_at }}</td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans_choice('general.note',2) }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    {!!   $loan_application->notes !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">

    </div>

@endsection
@section('footer-scripts')

    <script>
        $('.data-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
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
                },
                "columnDefs": [
                    {"orderable": false, "targets": 0}
                ]
            },
            responsive: true,
        });
    </script>
@endsection
