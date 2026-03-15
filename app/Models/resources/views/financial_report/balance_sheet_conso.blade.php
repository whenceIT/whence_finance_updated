@extends('layouts.master')
@section('title')
    {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width:  876pt
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
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: italic;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #cccccc
        }

        .style-11 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: italic;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #cccccc
        }

        .style-12 {
            border-top: 1pt solid black
        }

        .style-13 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-14 {
            color: black;
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

        .style-15 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-16 {
            color: #2f2c35;
            font-size: 9pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-2 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-3 {
            color: black;
            font-size: 10pt;
            font-family: "Roboto", Helvetica, Arial, sans-serif;;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-4 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-5 {
            color: #2f2c35;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-6 {
            color: white;
            padding-left: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #999999
        }

        .style-7 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #999999
        }

        .style-8 {
            color: white;
            padding-left: 5pt;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #999999
        }

        .style-9 {
            color: black;
            font-size: 13pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
                @if(!empty($start_date))
                    as at: <b>{{$start_date}} </b>
                @endif
            </h3>

            <div class="heading-elements">

            </div>
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
                                    <a href="{{url('report/financial_report/balance_sheet/pdf?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/balance_sheet/excel?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/balance_sheet/csv?end_date='.$end_date)}}"
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
    @if(!empty($end_date))
        <div class="box box-primary">
            <div class="box-body table-responsive">
                
            </div>
               <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            Balance Sheet.
            <small class="pull-right">Date: 2/10/2014</small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
       
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
        <table  class="table table-striped">
                    <tbody>
                   
                   
                    <tr style="height: 16pt">
                        <td colspan="4" valign="middle" class="style-2">{{trans_choice('general.office',1)}}</td>
                        <td valign="middle" class="style-3">
                            
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td colspan="9" valign="middle"
                            class="style-4">{{trans_choice('general.as',1)}} {{trans_choice('general.at',1)}} {{$end_date}}</td>
                    </tr>
                    <tr style="height: 12pt">
                        <td colspan="7"></td>
                        <td colspan="2" valign="middle" class="style-4">{{trans_choice('general.on',1)}}:</td>
                        <td colspan="5" valign="middle" class="style-5">{{date("Y-m-d H:i:s")}}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr style="height: 20pt">
                        <td colspan="2" valign="middle" class="style-6">{{trans_choice('general.gl_code',1)}}</td>
                        <td colspan="3" valign="middle" class="style-7">{{trans_choice('general.account',1)}}</td>
                        <td colspan="6" valign="middle" class="style-7">{{trans_choice('general.office',1)}}</td>
                        <td colspan="5" valign="middle" class="style-8">MTD {{trans_choice('general.balance',1)}}</td>
                        <td colspan="9" valign="middle" class="style-8">YTD {{trans_choice('general.balance',1)}}</td>

                    </tr>
                    <tr style="height: 20pt">
                        <td></td>
                        <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.asset',2)}}</td>
                    </tr>
                    <?php
                    $total_assets = 0;
                    $ototal_assets = 0;
                    $total_liabilities = 0;
                    $ototal_liabilities = 0;
                    $total_equity = 0;
                    $ototal_equity = 0;
                    ?>
                    @foreach(\App\Models\GlAccount::where('account_type','asset')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $opbalance=0;
                        $xbalance = 0;
                        $balance = 0;
                        $dr = 0;
                        $cr = 0;
                        $opdr = 0;
                        $opcr = 0;
                        $xdr = 0;
                        $xcr = 0;
                        $d = explode('-', $end_date);
                        
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->where('date', '<=', $end_date)->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $opdr = $opdr + $journal->op_balance_dr;
                            $opcr = $opcr + $journal->op_balance_cr;
                        }
                        
                        $xjournals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->where('year',
                        $d[0])->where('month',
                        $d[1])->get();

                        foreach ($xjournals as $xjournal) {
                            $xcr = $xcr + $xjournal->credit;
                            $xdr = $xdr + $xjournal->debit;   
                        }
                        $xbalance = $xdr - $xcr;
                        $balance = $dr - $cr;
                        $opbalance = $opdr - $opcr + $balance;
                        $total_assets = $total_assets + $xbalance;
                        $ototal_assets = $ototal_assets + $opbalance;
                        ?>
                        <tr style="height: 15pt">

                            <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                            <td width = "25%" colspan="3" valign="middle" class="style-3">
                                {{$key->name}}
                            </td>
                            <td colspan="6"></td>
                            <td colspan="5" valign="middle" class="style-4">{{ number_format($xbalance,2) }}</td>
                            <td colspan="9" valign="middle" class="style-4">{{ number_format($opbalance,2) }}</td>
                        </tr>
                        
                    @endforeach
                    <tr style="height: 2pt">

                        <td class="style-12" colspan="29"></td>
                    </tr>
                    <tr style="height: 4pt">

                        <td colspan="11" valign="middle" class="style-13">
                            {{trans_choice('general.total',1)}} {{trans_choice('general.asset',2)}}</td>
                        <td colspan="5" valign="middle"
                            class="style-14">{{ number_format($total_assets,2) }}</td>
                            <td colspan="9" valign="middle"
                            class="style-14">{{ number_format($ototal_assets,2) }}</td>
                    </tr>

                    <tr style="height: 0pt">

                        <td class="style-12" colspan="16">
                        </td>
                    </tr>
                    <tr style="height: 20pt">
                        <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.liability',2)}}</td>
                    </tr>

                    @foreach(\App\Models\GlAccount::where('account_type','liability')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $opbalance=0;
                        $balance = 0;
                        $opdr = 0;
                        $opcr = 0;
                        $dr = 0;
                        $cr = 0;
                        $xdr = 0;
                        $xcr = 0;
                        $d = explode('-', $end_date);
                        
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->where('date', '<=', $end_date)->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $opdr = $opdr + $journal->op_balance_dr;
                            $opcr = $opcr + $journal->op_balance_cr;
                        }

                        $xjournals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->where('year',
                        $d[0])->where('month',
                        $d[1])->get();

                        foreach ($xjournals as $xjournal) {
                            $xcr = $xcr + $xjournal->credit;
                            $xdr = $xdr + $xjournal->debit;   
                        }
                        $xbalance = $xcr - $xdr;
                        $balance = $cr - $dr;
                        $opbalance = $opcr - $opdr + $balance;
                        $total_liabilities = $total_liabilities + $xbalance;
                        $ototal_liabilities = $ototal_liabilities + $opbalance;
                        ?>
                        <tr style="height: 15pt">
                            <td colspan="2" valign="middle" class="style-3">{{$key->gl_code}}</td>
                            <td colspan="3" valign="middle" class="style-3">
                                {{$key->name}}
                            </td>
                            <td colspan="6"></td>
                            <td colspan="5" valign="middle" class="style-4"> {{ number_format($xbalance,2) }}</td>
                            <td colspan="9" valign="middle" class="style-4"> {{ number_format($opbalance,2) }}</td>
                        </tr>
                      
                    @endforeach
                    <tr style="height: 1pt">

                        <td class="style-12" colspan="16">
                        </td>

                    </tr>
                    <tr style="height: 1pt">

                        <td class="style-12" colspan="16">
                        </td>

                    </tr>
                    <tr style="height: 20pt">

                        <td colspan="13" valign="middle"
                            class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}}
                            :
                        </td>
                        <td colspan="5" valign="middle" class="style-14">{{ number_format($total_liabilities,2) }}</td>
                        <td colspan="9" valign="middle" class="style-14">{{ number_format($ototal_liabilities,2) }}</td>
                    </tr>
                   
                                    

                    @foreach(\App\Models\GlAccount::where('account_type','equity')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $balance = 0;
                        $obalance = 0;
                        $dr = 0;
                        $cr = 0;
                        $opdr = 0;
                        $opcr = 0;
                        $xdr = 0;
                        $xcr = 0;
                        $d = explode('-', $end_date);
                        
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->where('date', '<=', $end_date)->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $opdr = $opdr + $journal->op_balance_dr;
                            $opcr = $opcr + $journal->op_balance_cr;
                        }
                        $xjournals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->where('year',
                        $d[0])->where('month',
                        $d[1])->get();

                        foreach ($xjournals as $xjournal) {
                            $xcr = $xcr + $xjournal->credit;
                            $xdr = $xdr + $xjournal->debit;   
                        }
                        $balance = $cr - $dr;
                        $xbalance = $xcr - $xdr;
                        $obalance = $opcr - $opdr + $balance;
                        $total_equity = $total_equity + $balance;
                        $ototal_equity = $ototal_equity + $obalance;
                        ?>
                        <tr style="height: 15pt">
                            <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                            <td colspan="3" valign="middle" class="style-3">
                                {{$key->name}}
                            </td>
                            <td colspan="6"></td>
                            <td colspan="5" valign="middle" class="style-4">{{ number_format($xbalance,2) }}</td>
                            <td colspan="5" valign="middle" class="style-4">{{ number_format($obalance,2) }}</td>
                        </tr>
                        <tr style="height: 4pt">
                            <td colspan="16">
                            </td>
                        </tr>
                        <tr style="height: 1pt">
                            <td class="style-12" colspan="16">
                            </td>
                        </tr>
                    @endforeach
                    <tr style="height: 15pt">
                        <td colspan="2" valign="middle" class="style-3"></td>
                       
                        
                        <td colspan="6"></td>
                        <?php
                        $current_year_profits = 0;
                        $ocurrent_year_profits = 0;
                        $total_expenses = 0;
                        $ototal_expenses = 0;
                        $total_income = 0;
                        $ototal_income = 0;
                        
                        foreach (\App\Models\GlAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                            $balance = 0;
                            $xbalance = 0;
                            $obalance = 0;
                            $dr = 0;
                            $cr = 0;
                            $opdr = 0;
                            $opcr = 0;
                            $xdr = 0;
                            $xcr = 0;
                            $d = explode('-', $end_date);
                            $inidate = \App\Models\Setting::where('setting_key','initial_date')->first();
                            $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->whereBetween('date',
                                [$inidate, $end_date])->get();

                            foreach ($journals as $journal) {
                                $cr = $cr + $journal->credit;
                                $dr = $dr + $journal->debit;
                                $opdr = $opdr + $journal->op_balance_dr;
                                $opcr = $opcr + $journal->op_balance_cr;
                            }

                            $xjournals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->where('year',
                            $d[0])->where('month',
                            $d[1])->get();
    
                            foreach ($xjournals as $xjournal) {
                                $xcr = $xcr + $xjournal->credit;
                                $xdr = $xdr + $xjournal->debit;   
                            }
                            $balance = $dr - $cr;
                            $xbalance = $xdr - $xcr;
                            $obalance = $opdr - $opcr + $balance;
                            $total_expenses = $total_expenses + $xbalance;
                            $ototal_expenses = $ototal_expenses + $obalance;
                        }
                        foreach (\App\Models\GlAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                            $balance = 0;
                            $obalance = 0;
                            $dr = 0;
                            $cr = 0;
                            $opdr = 0;
                            $opcr = 0;
                            $xdr = 0;
                            $xcr = 0;
                            $d = explode('-', $end_date);
                            $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->whereBetween('date',
                                [$inidate, $end_date])->get();

                            foreach ($journals as $journal) {
                                $cr = $cr + $journal->credit;
                                $dr = $dr + $journal->debit;
                                $opdr = $opdr + $journal->op_balance_dr;
                                $opcr = $opcr + $journal->op_balance_cr;
                            }

                            $xjournals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->where('year',
                            $d[0])->where('month',
                            $d[1])->get();
    
                            foreach ($xjournals as $xjournal) {
                                $xcr = $xcr + $xjournal->credit;
                                $xdr = $xdr + $xjournal->debit;   
                            }
                            $balance = $cr - $dr;
                            $xbalance = $xcr - $xdr;
                            $obalance = $opcr - $opdr + $balance;
                            $total_income = $total_income + $xbalance;
                            $ototal_income = $ototal_income + $obalance;
                        }
                        $current_year_profits = $total_income - $total_expenses;
                        $ocurrent_year_profits = $ototal_income - $ototal_expenses;
                        $total_equity=$total_equity+$current_year_profits;
                        $ototal_equity=$ototal_equity+$ocurrent_year_profits;
                        ?>
                      
                    </tr>
                  
                    <tr style="height: 18pt">
                        <td align="right" colspan="11" valign="middle"
                            class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}} {{trans_choice('general.and',2)}} {{trans_choice('general.equity',2)}}
                            :
                        </td>
                        <td align="right" colspan="5" valign="middle"
                            class="style-14">{{ number_format($total_equity+$total_liabilities,2) }}</td>
                            <td align="right" colspan="9" valign="middle"
                            class="style-14">{{ number_format($ototal_equity+$ototal_liabilities,2) }}</td>
                    </tr>
                    </tr>
                    </tbody>
                </table>
          
                    </div>
    </section>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
