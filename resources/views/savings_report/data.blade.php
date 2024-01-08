@extends('layouts.master')
@section('title'){{trans_choice('general.savings',2)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.savings',2)}} {{trans_choice('general.report',2)}}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body">
            <table id="" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{ trans_choice('general.description',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <td>
                        <a href="{{url('report/savings_report/savings_transactions')}}">{{trans_choice('general.savings',2)}} {{trans_choice('general.transaction',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.savings_transactions_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings_report/savings_transactions')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/savings_report/savings_accounts')}}">{{trans_choice('general.savings',2)}} {{trans_choice('general.account',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.savings_accounts_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings_report/savings_accounts')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/savings_report/savings_balance')}}">{{trans_choice('general.savings',2)}} {{trans_choice('general.balance',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.savings_balance_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings_report/savings_balance')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
