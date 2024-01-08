@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}</h3>

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
                        <a href="{{url('report/loan_report/collection_sheet')}}">{{trans_choice('general.collection',2)}} {{trans_choice('general.sheet',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.collection_sheet_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/collection_sheet')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/repayments_report')}}">{{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.repayments_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/repayments_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>


                <tr>
                    <td>
                        <a href="{{url('report/loan_report/repayments_report_detail')}}">Daily Loan Activities Breakdown Report</a>
                    </td>
                    <td>
                        Get a break down of your Full Payments, Part Payments and Reloans
                    </td>
                    <td><a href="{{url('report/loan_report/repayments_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>



                <tr>
                    <td>
                        <a href="{{url('report/loan_report/expected_repayments')}}">{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.expected_repayments_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/expected_repayments')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/expected_repayments_cro')}}">{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',1)}} by CRO</a>
                    </td>
                    <td>
                        {{trans_choice('general.expected_repayments_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/expected_repayments_cro')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>



                <tr>
                    <td>
                        <a href="{{url('report/loan_report/loan_book')}}">{{trans_choice('general.loan',1)}} {{trans_choice('general.book',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.age_analysis_report_description',1)}} by (Balance)
                    </td>
                    <td><a href="{{url('report/loan_report/loan_book')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                




     <tr>
                    <td>
                        <a href="{{url('report/loan_report/customer_statement')}}">Customer {{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}</a>
                    </td>
                    <td>
                       Get Customer Transactions report
                    </td>
                    <td><a href="{{url('report/loan_report/customer_statement')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>



                <tr>
                    <td>
                        <a href="{{url('report/loan_report/age_analysis_principle')}}">{{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} Principle Balances</a>
                    </td>
                    <td>
                        {{trans_choice('general.age_analysis_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/age_analysis_principle')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>


                <tr>
                    <td>
                        <a href="{{url('report/loan_report/age_analysis')}}">{{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} Outstanding Balances</a>
                    </td>
                    <td>
                        {{trans_choice('general.age_analysis_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/age_analysis')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>












                <tr>
                    <td>
                        <a href="{{url('report/loan_report/arrears_report')}}">{{trans_choice('general.arrears',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.arrears_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/arrears_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/disbursed_loans')}}">{{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.disbursed_loans_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/disbursed_loans')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{url('report/loan_report/disbursed_loans_pmec')}}">PMEC {{trans_choice('general.loan',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.disbursed_loans_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/disbursed_loans')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/loan_portfolio')}}">{{trans_choice('general.loan',1)}} {{trans_choice('general.portfolio',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.loan_portfolio_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/loan_portfolio')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{url('report/loan_report/loan_portfolio_cro')}}">{{trans_choice('general.loan',1)}} {{trans_choice('general.portfolio',1)}} by CRO </a>
                    </td>
                    <td>
                        {{trans_choice('general.loan_portfolio_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/loan_portfolio_cro')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/loan_report/written_off_loans')}}">{{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.written_off_loans_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/written_off_loans')}}"><i class="icon-search4"></i> </a>
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
