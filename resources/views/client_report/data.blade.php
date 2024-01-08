@extends('layouts.master')
@section('title'){{trans_choice('general.client',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.client',1)}} {{trans_choice('general.report',2)}}</h3>

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

                <tr class="hidden">
                    <td>
                        <a href="{{url('report/client_report/borrowers_overview')}}">{{trans_choice('general.borrower',2)}} {{trans_choice('general.overview',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.borrowers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/borrower_report/borrowers_overview')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/client_report/client_numbers')}}">{{trans_choice('general.client',1)}} {{trans_choice('general.number',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.client_numbers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/client_report/client_numbers')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr class="hidden">
                    <td>
                        <a href="{{url('report/borrower_report/top_borrowers')}}">{{trans_choice('general.top',1)}} {{trans_choice('general.borrower',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.top_borrowers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/borrower_report/top_borrowers')}}"><i class="icon-search4"></i> </a>
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
