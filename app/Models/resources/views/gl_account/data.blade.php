@extends('layouts.master')
@section('title')
    {{trans_choice('general.chart_of_account',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{trans_choice('general.chart_of_account',2)}}</h3>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('accounting.gl_accounts.create'))
                    <a href="{{ url('accounting/gl_account/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.account',1)}}</a>
                @endif
            </div>
        </div>
        <div class="box-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.gl_code',1)}}</th>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.balance',1)}}</th>
                        <th>{{trans_choice('general.unreconciled',1)}} {{trans_choice('general.balance',1)}}</th>
                        <th>{{trans_choice('general.note',2)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>{{ $key->name }}</td>
                            <td>
                                @if($key->account_type=="expense")
                                    {{trans_choice('general.expense',1)}}
                                @endif
                                @if($key->account_type=="asset")
                                    {{trans_choice('general.asset',1)}}
                                @endif
                                @if($key->account_type=="equity")
                                    {{trans_choice('general.equity',1)}}
                                @endif
                                @if($key->account_type=="liability")
                                    {{trans_choice('general.liability',1)}}
                                @endif
                                @if($key->account_type=="income")
                                    {{trans_choice('general.income',1)}}
                                @endif
                            </td>
                            <td>
                                <?php
                                $transactions = \App\Helpers\GeneralHelper::gl_account_balance($key->id);
                                $unreconciled_transactions = \App\Helpers\GeneralHelper::gl_account_unreconciled_balance($key->id);
                                $balance = 0;
                                $unreconciled_balance = 0;
                                if (!empty($transactions)) {
                                    if ($key->account_type == "asset" || $key->account_type == "expense") {
                                        $balance = $transactions->debit - $transactions->credit;
                                    }
                                    if ($key->account_type == "liability" || $key->account_type == "income" || $key->account_type == "equity") {
                                        $balance = $transactions->credit - $transactions->debit;
                                    }
                                }
                                if (!empty($unreconciled_transactions)) {
                                    if ($key->account_type == "asset" || $key->account_type == "expense") {
                                        $unreconciled_balance = $unreconciled_transactions->debit - $unreconciled_transactions->credit;
                                    }
                                    if ($key->account_type == "liability" || $key->account_type == "income" || $key->account_type == "equity") {
                                        $unreconciled_balance = $unreconciled_transactions->credit - $unreconciled_transactions->debit;
                                    }
                                }
                                ?>
                                {{number_format($balance,2)}}
                            </td>
                            <td>{{number_format($unreconciled_balance,2)}}</td>
                            <td>{!!  $key->notes  !!}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button"
                                            data-toggle="dropdown"
                                            aria-expanded="false"><i
                                                class="fa fa-navicon"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('accounting.gl_accounts.update'))
                                            <li>
                                                <a href="{{ url('accounting/gl_account/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i>
                                                    {{ trans('general.edit') }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('accounting.gl_accounts.delete'))
                                            <li>
                                                <a href="{{ url('accounting/gl_account/'.$key->id.'/delete') }}"
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
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [4]}
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
