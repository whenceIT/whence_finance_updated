@extends('layouts.master')
@section('title')
    {{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 875pt
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
            font-family: "Arial";
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
            <i class=""></i> {{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}.
            <small class="pull-right">AS at {{$end_date}}</small>
          </h2>
          <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          Office:
          <address>
            <strong> </strong><br>
            
          </address>
        </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
     
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
        <table cellspacing="0" cellpadding="0" class="table table-striped">
                    <tbody>
             
                    <tr style="height: 20pt">
                        <td colspan="2" valign="middle" class="style-6">{{trans_choice('general.gl_code',1)}}</td>
                        <td colspan="3" valign="middle" class="style-7">{{trans_choice('general.account',1)}}</td>
                        <td colspan="6" valign="middle" class="style-7">{{trans_choice('general.office',1)}}</td>
                        <td colspan="5" valign="middle" class="style-8">Month to Date</td>
                        <td colspan="5" valign="middle" class="style-8">Year to Date</td>

                    </tr>
                    <tr style="height: 20pt">
                        <td></td>
                        <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.income',1)}}</td>
                    </tr>
                    <?php
                   
                    $total_income = 0;
                    $total_expenses = 0;
                    $op_total_income = 0;
                    $op_total_expenses = 0;
                    ?>
                    @foreach(\App\Models\GlAccount::where('account_type','income')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $balance = 0;
                        $xbalance = 0;$xbalance = 0;
                        $op_balance = 0;
                        $op_dr = 0;
                        $op_cr = 0;
                        $dr = 0;
                        $cr = 0;
                        $xdr = 0;
                        $xcr = 0;
                        $d = explode('-', $end_date);
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->where('date','<=',$end_date)->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $op_dr = $op_dr + $journal->op_balance_dr;
                            $op_cr = $op_cr + $journal->op_balance_cr;
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
                        $op_balance = $op_cr - $op_dr + $balance;
                        $total_income = $total_income + $xbalance;
                        $op_total_income = $op_total_income + $op_balance;
                        ?>
                        <tr style="height: 15pt">

                            <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                            <td colspan="3" valign="middle" class="style-3">
                                {{$key->name}}
                            </td>
                            <td colspan="6"></td>
                            <td colspan="2" valign="middle" class="style-4">{{ number_format($xbalance,2) }}</td>
                            <td colspan="4" valign="middle" class="style-4">{{ number_format($op_balance,2) }}</td>
                        </tr>
                       
                    @endforeach
                    <tr style="height: 1pt">

                        <td class="style-12" colspan="16"></td>
                    </tr>
                    <tr style="height: 1pt">

                        <td colspan="11" valign="middle" class="style-13">
                            {{trans_choice('general.total',1)}} {{trans_choice('general.income',1)}}</td>
                        <td colspan="2" valign="middle"
                            class="style-14">{{ number_format($total_income,2) }}</td>
                            <td colspan="5" valign="middle"
                            class="style-14">{{ number_format($op_total_income,2) }}</td>
                    </tr>

                    <tr style="height: 0pt">

                        <td class="style-12" colspan="16">
                        </td>
                    </tr>
                    <tr style="height: 20pt">
                        <td colspan="17" valign="middle" class="style-9">{{trans_choice('general.expense',2)}}</td>
                    </tr>

                    @foreach(\App\Models\GlAccount::where('account_type','expense')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $xbalance = 0;
                        $balance = 0;
                        $op_balance = 0;
                        $op_dr = 0;
                        $op_cr = 0;
                        $dr = 0;
                        $cr = 0;
                        $xdr = 0;
                        $xcr = 0;
                        
                        $d = explode('-', $end_date);
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->where('date','<=',$end_date)->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $op_dr = $op_dr + $journal->op_balance_dr;
                            $op_cr = $op_cr + $journal->op_balance_cr;
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
                        $op_balance = $op_dr - $op_cr + $balance;
                        $total_expenses = $total_expenses + $xbalance;
                        $op_total_expenses = $op_total_expenses + $op_balance;
                        ?>
                        <tr style="height: 15pt">
                            <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                            <td colspan="3" valign="middle" class="style-3">
                                {{$key->name}}
                            </td>
                            <td colspan="6"></td>
                            <td colspan="2" valign="middle" class="style-4">{{ number_format($xbalance,2) }}</td>
                            <td colspan="4" valign="middle" class="style-4">{{ number_format($op_balance,2) }}</td>
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

                        <td colspan="11" valign="middle"
                            class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.expense',2)}} :
                        </td>
                        <td colspan="2" valign="middle" class="style-14">{{ number_format($total_expenses,2) }}</td>
                        <td colspan="5" valign="middle" class="style-14">{{ number_format($op_total_expenses,2) }}</td>
                    </tr>
                    <tr style="height: 1pt">
                        <td class="style-12" colspan="16">
                        </td>

                    </tr>
                    <tr style="height: 18pt">
                        <td  colspan="11" valign="middle"
                            class="style-13">Profit/Loss :
                        </td>
                        <td colspan="2" valign="middle"
                            class="style-14">{{ number_format($total_income-$total_expenses,2) }}</td>
                            <td colspan="5" valign="middle"
                            class="style-14">{{ number_format($op_total_income-$op_total_expenses,2) }}</td>
                    </tr>
                    </tbody>
                </table>
       
        <!-- /.col -->
      </div>
      <!-- /.row -->

      @endif
@endsection
@section('footer-scripts')

@endsection
