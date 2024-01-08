@extends('layouts.master')
@section('title'){{trans_choice('general.financial',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.financial',1)}} {{trans_choice('general.report',2)}}</h3>

            <div class="heading-elements">

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
                        <a href="{{url('report/financial_report/balance_sheet')}}">{{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
                    </td>
                    <td><a href="{{url('report/financial_report/balance_sheet')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/financial_report/trial_balance')}}">{{trans_choice('general.trial_balance',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.trial_balance',1)}}
                    </td>
                    <td><a href="{{url('report/financial_report/trial_balance')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                
    



                <tr class="hidden">
                    <td>
                        <a href="{{url('report/financial_report/cash_flow')}}">{{trans_choice('general.cash_flow',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.cash_flow',1)}}
                    </td>
                    <td><a href="{{url('report/financial_report/cash_flow')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/financial_report/income_statement')}}">{{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.income_statement_description',1)}}
                    </td>
                    <td><a href="{{url('report/financial_report/income_statement')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/financial_report/provisioning')}}">{{trans_choice('general.provisioning',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.provisioning_description',1)}}
                    </td>
                    <td><a href="{{url('report/financial_report/provisioning')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/financial_report/journals_report')}}">{{trans_choice('general.journal',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        Report shows all Journal bookings
                    </td>
                    <td><a href="{{url('report/financial_report/journals_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{url('report/financial_report/daily_transaction_report')}}">{{trans_choice('general.daily',2)}} {{trans_choice('general.transaction',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        Report shows all daily booking against staff
                    </td>
                    <td><a href="{{url('report/financial_report/daily_transaction_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>




                <tr>
                    <td>
                        <a href="{{url('report/financial_report/ledger_statement')}}">{{trans_choice('general.ledger',1)}} {{trans_choice('general.statement',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        Report shows Ledger
                    </td>
                    <td><a href="{{url('report/financial_report/ledger_statement')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
