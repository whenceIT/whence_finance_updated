@extends('layouts.master')
@section('title'){{trans_choice('general.charge',2)}}
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{trans_choice('general.charge',2)}}</h6>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('products.charges.create'))
                    <a href="{{ url('charge/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}</a>
                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <table id="data-table" class="table table-striped table-condensed table-hover ">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{trans_choice('general.product',1)}}</th>
                    <th>{{trans_choice('general.type',1)}}</th>
                    <th>{{trans_choice('general.active',1)}}</th>
                    <th>{{trans_choice('general.amount',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            @if($key->product=='loan')
                                {{trans_choice('general.loan',1)}}
                            @endif
                            @if($key->product=='savings')
                                {{trans_choice('general.savings',1)}}
                            @endif
                            @if($key->product=='client')
                                {{trans_choice('general.client',1)}}
                            @endif
                            @if($key->product=='shares')
                                {{trans_choice('general.shares',1)}}
                            @endif
                        </td>
                        <td>
                            @if($key->charge_type=='disbursement')
                                {{trans_choice('general.disbursement',1)}}
                            @endif
                            @if($key->charge_type=='specified_due_date')
                                {{trans_choice('general.specified_due_date',2)}}
                            @endif
                            @if($key->charge_type=='installment_fee')
                                {{trans_choice('general.installment_fee',2)}}
                            @endif
                            @if($key->charge_type=='overdue_installment_fee')
                                {{trans_choice('general.overdue_installment_fee',2)}}
                            @endif
                            @if($key->charge_type=='loan_rescheduling_fee')
                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                            @endif
                            @if($key->charge_type=='overdue_maturity')
                                {{trans_choice('general.overdue_maturity',2)}}
                            @endif
                            @if($key->charge_type=='savings_activation')
                                {{trans_choice('general.savings_activation',2)}}
                            @endif
                            @if($key->charge_type=='withdrawal_fee')
                                {{trans_choice('general.withdrawal_fee',2)}}
                            @endif
                            @if($key->charge_type=='monthly_fee')
                                {{trans_choice('general.monthly_fee',2)}}
                            @endif
                            @if($key->charge_type=='annual_fee')
                                {{trans_choice('general.annual_fee',2)}}
                            @endif
                        </td>
                        <td>
                            @if($key->active==1)
                                {{trans_choice('general.yes',1)}}
                            @else
                                {{trans_choice('general.no',1)}}
                            @endif
                        </td>
                        <td>
                            {{$key->amount}}
                            @if($key->charge_option=="flat")
                                {{trans_choice('general.flat',1)}}
                            @endif
                            @if($key->charge_option=="installment_principal_due")
                                % {{trans_choice('general.installment_principal_due',1)}}
                            @endif
                            @if($key->charge_option=="installment_principal_interest_due")
                                % {{trans_choice('general.installment_principal_interest_due',1)}}
                            @endif
                            @if($key->charge_option=="installment_interest_due")
                                % {{trans_choice('general.installment_interest_due',1)}}
                            @endif
                            @if($key->charge_option=="total_due")
                                % {{trans_choice('general.total_due',1)}}
                            @endif
                            @if($key->charge_option=="original_principal")
                                % {{trans_choice('general.original_principal',1)}}
                            @endif
                            @if($key->charge_option=="percentage")
                                % {{trans_choice('general.percentage',1)}} {{trans_choice('general.of',1)}} {{trans_choice('general.amount',1)}}
                            @endif

                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('products.charges.update'))
                                        <li>
                                            <a href="{{ url('charge/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('products.charges.delete'))
                                        <li>
                                            <a href="{{ url('charge/'.$key->id.'/delete') }}"
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
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
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
