@extends('layouts.master')
@section('title')
    {{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 940pt
        }

        .style-1 {
            color: white;
            padding-left: 10pt;
            font-size: 14pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #339933
        }

        .style-10 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-11 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-left: 1pt solid black
        }

        .style-12 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-13 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-left: 1pt solid black
        }

        .style-14 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-15 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black
        }

        .style-16 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-17 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-left: 1pt solid black
        }

        .style-18 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-bottom: 1pt solid black
        }

        .style-19 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-bottom: 1pt solid black
        }

        .style-2 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-20 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-left: 1pt solid black;
            border-bottom: 1pt solid black
        }

        .style-21 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-bottom: 2pt solid black
        }

        .style-22 {
            color: black;
            padding-right: 4pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 2pt solid black;
            border-left: 1pt solid black
        }

        .style-23 {
            border-bottom: 2pt solid black
        }

        .style-24 {
            color: black;
            font-size: 10pt;
            font-family: serif;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-25 {
            width: 50px;
            height: 50px
        }

        .style-3 {
            color: black;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-4 {
            color: #2f2c35;
            padding-left: 5pt;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-5 {
            color: #2f2c35;
            padding-right: 1pt;
            font-size: 8pt;
            font-family: "Arial Narrow";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-6 {
            color: black;
            padding-top: 5pt;
            padding-right: 5pt;
            font-size: 12pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-7 {
            color: black;
            padding-top: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
        }

        .style-8 {
            border-top: 2pt solid black
        }

        .style-9 {
            border-top: 2pt solid black;
            border-left: 1pt solid black
        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="start_date"
                           class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="start_date" class="form-control date-picker"
                               value="{{$start_date}}"
                               required id="start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date"
                           class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="end_date" class="form-control date-picker"
                               value="{{$end_date}}"
                               required id="end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="officer_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="officer_id" class="form-control select2" id="officer_id" required>
                            <option value="0"
                                    @if($officer_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\User::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for=""
                           class="control-label col-md-2"></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>

                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/pdf?start_date='.$start_date.'&end_date='.$end_date.'&officer_id='.$officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/excel?start_date='.$start_date.'&end_date='.$end_date.'&officer_id='.$officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/csv?start_date='.$start_date.'&end_date='.$end_date.'&officer_id='.$officer_id)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-primary">
            <div class="panel-body table-responsive ">
                <table cellspacing="0" cellpadding="0" class="style-0">
                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="28" valign="middle"
                            class="style-1">{{trans_choice('general.expected',1)}} {{trans_choice('general.vs',1)}} {{trans_choice('general.actual',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.repayment',2)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td colspan="2" valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
                        <td valign="middle" class="style-3" colspan="2">{{$start_date}}</td>
                        <td colspan="6" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="4" valign="middle" class="style-5">{{date("Y-m-d H:i:s")}}</td>
                        <td colspan="15"></td>

                    </tr>
                    <tr style="height: 15pt">
                        <td colspan="2" valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
                        <td valign="middle" colspan="2" class="style-3">{{$end_date}}</td>
                        <td colspan="24"></td>
                    </tr>

                    <tr style="height: 7pt">
                        <td colspan="4" valign="middle"
                            class="style-6">{{trans_choice('general.cash_flow',1)}} {{trans_choice('general.per',1)}}  {{trans_choice('general.officer',1)}}</td>
                        <td colspan="3" valign="middle" class="style-7">{{trans_choice('general.actual',1)}}</td>
                        <td colspan="11"></td>

                        <td colspan="2" valign="middle" class="style-7">{{trans_choice('general.expected',1)}}</td>
                        <td colspan="9"></td>

                    </tr>
                    <tr style="height: 11pt">
                        <td colspan="28"></td>
                    </tr>
                    <tr style="height: 4pt">
                        <td class="style-8" colspan="28"></td>
                    </tr>
                    <tr style="height: 15pt">
                        <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.name',1)}} :</td>
                        <td colspan="3" valign="middle" class="style-10">{{trans_choice('general.principal',1)}}</td>
                        <td colspan="4" valign="middle" class="style-10">{{trans_choice('general.interest',1)}}</td>
                        <td colspan="3" valign="middle" class="style-10">{{trans_choice('general.fee',2)}}</td>
                        <td colspan="3" valign="middle" class="style-10">{{trans_choice('general.penalty',1)}}</td>
                        <td colspan="2" valign="middle" class="style-10">{{trans_choice('general.repayment',2)}}</td>
                        <td colspan="2" valign="middle" class="style-11">{{trans_choice('general.principal',1)}}</td>
                        <td colspan="2" valign="middle" class="style-10">{{trans_choice('general.interest',1)}}</td>
                        <td colspan="2" valign="middle" class="style-10">{{trans_choice('general.fee',2)}}</td>
                        <td colspan="2" valign="middle" class="style-10">{{trans_choice('general.penalty',1)}}</td>
                        <td colspan="2" valign="middle" class="style-10">{{trans_choice('general.repayment',2)}}</td>
                    </tr>
                    <?php
                    $total_loans = 0;
                    $total_expected_principal = 0;
                    $total_expected_interest = 0;
                    $total_expected_penalty = 0;
                    $total_expected_fees = 0;
                    $total_expected_repayments = 0;
                    $total_actual_principal = 0;
                    $total_actual_interest = 0;
                    $total_actual_penalty = 0;
                    $total_actual_fees = 0;
                    $total_actual_repayments = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $loans = \App\Models\Loan::where('status', 'disbursed')->where('loan_officer_id', $key->id)->with(['repayment_schedules' => function ($query) use ($start_date, $end_date) {
                            $query->whereBetween('due_date', [$start_date, $end_date]);
                        }])->get();
                        $expected_principal = 0;
                        $expected_interest = 0;
                        $expected_penalty = 0;
                        $expected_fees = 0;
                        $expected_repayments = 0;
                        $actual_principal = 0;
                        $actual_interest = 0;
                        $actual_penalty = 0;
                        $actual_fees = 0;
                        $actual_repayments = 0;

                        foreach ($loans as $loan) {
                            foreach ($loan->repayment_schedules as $schedule) {
                                $expected_principal = $expected_principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
                                $actual_principal = $actual_principal + $schedule->principal_paid;
                                $expected_interest = $expected_interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
                                $actual_fees = $actual_fees + $schedule->fees_paid;
                                $expected_fees = $expected_fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
                                $actual_interest = $actual_interest + $schedule->interest_paid;
                                $expected_penalty = $expected_penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;
                                $actual_penalty = $actual_penalty + $schedule->penalty_paid;
                            }
                        }
                        $actual_repayments = $actual_fees + $actual_interest + $actual_penalty + $actual_principal;
                        $expected_repayments = $expected_fees + $expected_interest + $expected_penalty + $expected_principal;
                        $total_expected_principal = $total_expected_principal + $expected_principal;
                        $total_expected_interest = $total_expected_interest + $expected_interest;
                        $total_expected_penalty = $total_expected_penalty + $expected_penalty;
                        $total_expected_fees = $total_expected_fees + $expected_fees;
                        $total_expected_repayments = $total_expected_repayments + $expected_repayments;
                        $total_actual_principal = $total_actual_principal + $actual_principal;
                        $total_actual_interest = $total_actual_interest + $actual_interest;
                        $total_actual_penalty = $total_actual_penalty + $actual_penalty;
                        $total_actual_fees = $total_actual_fees + $actual_fees;
                        $total_actual_repayments = $total_actual_repayments + $actual_repayments;
                        ?>
                        @if($total_expected_repayments>0 || $total_actual_repayments>0)
                            <tr style="height: 15pt">
                                <td colspan="3" valign="middle" class="style-12">{{$key->first_name}} {{$key->last_name}}</td>
                                <td colspan="3" valign="middle"
                                    class="style-14">{{number_format($actual_principal,2)}}</td>
                                <td colspan="4" valign="middle"
                                    class="style-14">{{number_format($actual_interest,2)}}</td>
                                <td colspan="3" valign="middle" class="style-14">{{number_format($actual_fees,2)}}</td>
                                <td colspan="3" valign="middle"
                                    class="style-14">{{number_format($actual_penalty,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-14">{{number_format($actual_repayments,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-13">{{number_format($expected_principal,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-14">{{number_format($expected_interest,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-14">{{number_format($expected_fees,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-14">{{number_format($expected_penalty,2)}}</td>
                                <td colspan="2" valign="middle"
                                    class="style-14">{{number_format($expected_repayments,2)}}</td>

                            </tr>
                        @endif
                    @endforeach

                    <tr style="height: 1pt">
                        <td colspan="3"></td>
                        <td class="style-8" colspan="17"></td>
                        <td colspan="2" valign="middle" class="style-15"></td>
                        <td colspan="2" valign="middle" class="style-15"></td>
                        <td colspan="2" valign="middle" class="style-15"></td>
                        <td colspan="2" valign="middle" class="style-15"></td>

                    </tr>
                    <tr style="height: 17pt">
                        <td colspan="3"></td>
                        <td colspan="3" valign="middle" class="style-16">{{number_format($total_actual_principal,2)}}</td>
                        <td colspan="4" valign="middle" class="style-16">{{number_format($total_actual_interest,2)}}</td>
                        <td colspan="3" valign="middle" class="style-16">{{number_format($total_actual_fees,2)}}</td>
                        <td colspan="3" valign="middle" class="style-16">{{number_format($total_actual_penalty,2)}}</td>
                        <td colspan="2" valign="middle" class="style-16">{{number_format($total_actual_repayments,2)}}</td>
                        <td colspan="2" valign="middle" class="style-17">{{number_format($total_expected_principal,2)}}</td>
                        <td colspan="2" valign="middle" class="style-17">{{number_format($total_expected_interest,2)}}</td>
                        <td colspan="2" valign="middle" class="style-17">{{number_format($total_expected_fees,2)}}</td>
                        <td colspan="2" valign="middle" class="style-17">{{number_format($total_expected_penalty,2)}}</td>
                        <td colspan="2" valign="middle" class="style-17">{{number_format($total_expected_repayments,2)}}</td>
                    </tr>
                    <tr style="height: 0pt">
                        <td colspan="3"></td>
                        <td colspan="25" class="style-8"></td>

                    </tr>
                    </tbody>
                </table>


            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("body").addClass('sidebar-xs sidebar-collapse');
            });
        </script>
    @endif

@endsection
@section('footer-scripts')

@endsection
