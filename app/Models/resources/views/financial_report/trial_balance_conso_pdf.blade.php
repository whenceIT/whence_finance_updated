@extends('layouts.master')
@section('title')
    {{trans_choice('general.trial_balance', 1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 976pt
        }

        .style-1 {
            color: white;
            padding-left: 10pt;
            font-size: 14pt;
            font-family: "Arial";
            font-weight: bold;
            background-color: #339933
        }

        .style-10 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            background-color: #cccccc
        }

        .style-11 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            border-top: 1pt solid black
        }

        .style-12 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            border-top: 1pt solid black
        }

        .style-13 {
            color: black;
            font-size: 10pt;
            font-family: serif;
        }

        .style-14 {
            width: 50px;
            height: 50px
        }

        .style-15 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: serif;
        }

        .style-16 {
            color: #2f2c35;
            font-size: 9pt;
            font-family: "Arial";
        }

        .style-2 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
        }

        .style-3 {
            color: black;
            font-size: 12pt;
            font-family: "Roboto", Helvetica, Arial, sans-serif;
        }

        .style-4 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
        }

        .style-5 {
            color: white;
            padding-left: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            background-color: #cccccc
        }

        .style-6 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            background-color: #cccccc
        }

        .style-7 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            text-align: center;
            background-color: #cccccc
        }

        .style-8 {
            border-top: 1pt solid black
        }

        .style-9 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            background-color: #cccccc
        }
    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                @if(!empty($end_date))
                    for period: <b> As at {{$end_date}}</b>
                @endif
            </h3>
            <div class="heading-elements"></div>
        </div>
        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
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
                    <label for=""
                           class="control-label col-md-2"></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!</button>
                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>
                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance_conso/pdf?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/excel?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/csv?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($end_date))
        <div class="panel panel-white">
            <div class="panel-body table-responsive">
                <section class="invoice">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                <i class=""></i> Trial Balance
                                <small class="pull-right"> <strong>As at {{$end_date}}</strong></small>
                            </h2>
                        </div>
                    </div>

                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Office:
                            <address>
                                <strong></strong><br>
                            </address>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr align="right">
                                        <th align="right" width="100"></th>
                                        <th align="right" width="100"></th>
                                        <th align="right" width="100"></th>
                                        <th class="hidden" align="right" width="160">Opening Balances</th>
                                        <th align="right" width="100"><i> Month to date</i></th>
                                        <th align="right" width="100"></th>
                                        <th align="right" width="100"><i>Year to date</i></th>
                                    </tr>
                                    <tr>
                                        <th width="100">{{trans_choice('general.gl_code', 1)}}</th>
                                        <th width="300">{{trans_choice('general.account', 1)}}</th>
                                        <th class="hidden" width="100" style="text-align: right;">{{trans_choice('general.opening', 1)}} {{trans_choice('general.debit', 1)}}</th>
                                        <th class="hidden" width="100" style="text-align: right;">{{trans_choice('general.opening', 1)}} {{trans_choice('general.credit', 1)}}</th>
                                        <th align="right" width="100">{{trans_choice('general.debit', 1)}}</th>
                                        <th align="right" width="100">{{trans_choice('general.credit', 1)}}</th>
                                        <th align="right" width="100">{{trans_choice('general.debit', 1)}}</th>
                                        <th align="right" width="100">{{trans_choice('general.credit', 1)}}</th>
                                    </tr>

                                    <?php
                                    $total_debit_balance = 0;
                                    $total_credit_balance = 0;
                                    $total_opening_balance = 0;
                                    $total_closing_balance = 0;
                                    $total_dr = 0;
                                    $total_xdr = 0;
                                    $total_op_dr = 0;
                                    $total_cr = 0;
                                    $total_xcr = 0;
                                    $total_op_cr = 0;
                                    $total_balance = 0;
                                    $total_debits = 0;
                                    $total_credits = 0;
                                    ?>

                                    @foreach($data as $key)
                                        <?php
                                        $dr = 0;
                                        $cr = 0;
                                        $xdr = 0;
                                        $xcr = 0;
                                        $op_balance_dr = 0;
                                        $op_balance_cr = 0;

                                        // Split end_date for year and month
                                        $d = explode('-', $end_date);

                                        // Fetch journal entries for the current account and the specified year and month
                                        $xjournals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)
                                            ->where('reversed', 0)
                                            ->where('year', $d[0])
                                            ->where('month', $d[1])
                                            ->where('date', '<=', $end_date) // Ensure only entries up to end_date are included
                                            ->get();

                                        foreach ($xjournals as $xjournal) {
                                            $xcr += $xjournal->credit;
                                            $xdr += $xjournal->debit;

                                            if ($xdr > $xcr) {
                                                $xdr -= $xcr;
                                                $xcr = 0;
                                            } else {
                                                $xcr -= $xdr;
                                                $xdr = 0;
                                            }
                                        }

                                        // Fetch all journal entries up to the end_date for the current account
                                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)
                                            ->where('reversed', 0)
                                            ->where('date', '<=', $end_date) // Ensure only entries before or on end_date are fetched
                                            ->get();

                                        foreach ($journals as $journal) {
                                            $cr += $journal->credit;
                                            $dr += $journal->debit;
                                            $op_balance_dr += $journal->op_balance_dr;
                                            $op_balance_cr += $journal->op_balance_cr;

                                            if ($dr > $cr) {
                                                $dr -= $cr;
                                                $cr = 0;
                                            } else {
                                                $cr -= $dr;
                                                $dr = 0;
                                            }
                                        }

                                        $total_dr += $dr;
                                        $total_cr += $cr;
                                        $total_xdr += $xdr;
                                        $total_xcr += $xcr;
                                        $total_op_cr += $op_balance_cr;
                                        $total_op_dr += $op_balance_dr;

                                        $fdebit = $op_balance_dr + $dr;
                                        $fcredit = $op_balance_cr + $cr;

                                        if ($fdebit > $fcredit) {
                                            $debits = $fdebit - $fcredit;
                                            $credits = 0;
                                        } else {
                                            $credits = $fcredit - $fdebit;
                                            $debits = 0;
                                        }

                                        $total_debits += $debits;
                                        $total_credits += $credits;
                                        ?>

                                        <tr>
                                            <td>{{$key->gl_code}}</td>
                                            <td>{{$key->name}}</td>
                                            <td class="hidden" align="right">{{number_format($op_balance_dr, 2)}}</td>
                                            <td class="hidden" align="right">{{number_format($op_balance_cr, 2)}}</td>
                                            <td align="right">{{number_format($xdr, 2)}}</td>
                                            <td align="right">{{number_format($xcr, 2)}}</td>
                                            <td align="right">{{number_format($debits, 2)}}</td>
                                            <td align="right">{{number_format($credits, 2)}}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <th>#</th>
                                        <th>ZMW</th>
                                        <td class="hidden" align="right"><b>{{number_format($total_op_dr, 2)}}</b></td>
                                        <td class="hidden" align="right"><b>{{number_format($total_op_cr, 2)}}</b></td>
                                        <td align="right"><b>{{number_format($total_xdr, 2)}}</b></td>
                                        <td align="right"><b>{{number_format($total_xcr, 2)}}</b></td>
                                        <td align="right"><b>{{number_format($total_debits, 2)}}</b></td>
                                        <td align="right"><b>{{number_format($total_credits, 2)}}</b></td>
                                    </tr>

                                    <tr style="height: 2pt">
                                        <td class="style-8" colspan="6"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')
@endsection
