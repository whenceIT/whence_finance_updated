@extends('layouts.master')
@section('title')
    {{trans_choice('general.trial_balance',1)}}
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
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #339933
        }

        .style-10 {
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

        .style-11 {
            color: black;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 1pt solid black
        }

        .style-12 {
            color: black;
            padding-right: 5pt;
            font-size: 10pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            border-top: 1pt solid black
        }

        .style-13 {
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

        .style-14 {
            width: 50px;
            height: 50px
        }

        .style-15 {
            color: black;
            padding-right: 5pt;
            font-size: 9pt;
            font-family: serif;
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
            text-align: right;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-3 {
            color: black;
            font-size: 12pt;
            font-family: "Roboto", Helvetica, Arial, sans-serif;
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
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

        }

        .style-5 {
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
            width: 35cm;

            background-color: #cccccc
        }

        .style-6 {
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

            background-color: #cccccc
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
            text-align: center;
            word-spacing: 0pt;
            letter-spacing: 0pt;

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
            font-style: italic;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;

            background-color: #cccccc
        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.trial_balance',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
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
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                    <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
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
                                    <a href="{{url('report/financial_report/trial_balance/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
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
        <div class="panel panel-white">
            <div class="panel-body table-responsive">

                <table  class="table table-striped">

                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="6" valign="middle"
                            class="style-3">  {{trans_choice('general.trial_balance',1)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td valign="middle"  class="style-3">{{trans_choice('general.office',1)}} :

                        </td>
                        <td valign="middle" class="style-3">
                            @if($office_id!=0)
                                {{\App\Models\Office::find($office_id)->name}}
                            @endif
                        </td>
                        <td colspan="2" valign="middle"
                            class="style-3">{{trans_choice('general.from',1)}} {{$start_date}} {{trans_choice('general.to',1)}} {{$end_date}}</td>
                        <td>
                    </tr>

                    <tr  align="right" style="text-align: right;"> 
            <th  align="right" style="text-align: right;" width="100"> </th>
            <th  align="right" style="text-align: right;" width="100"> </th>
            <th  align="right" style="text-align: right;" width="100"></th>
            <th class="hidden" align="right" style="text-align: right;" width="160">Opening Balances </th>
            <th  align="right" style="text-align: right;" width="100"> Month to date</th>
            <th  align="right" style="text-align: right;" width="100"></th>
            <th  align="right" style="text-align: right;" width="100">Year to date </th>
            <th  align="right" style="text-align: right;" width="100"> </th>
            
            </tr>


                    <tr>
               
               <th width="100">{{trans_choice('general.gl_code',1)}}</th>
               <th width="300">{{trans_choice('general.account',1)}}</th>
               <th class="hidden" width="100" style="text-align: right;">{{trans_choice('general.opening',1)}} {{trans_choice('general.debit',1)}}</th>
               <th class="hidden" width="100" style="text-align: right;">{{trans_choice('general.opening',1)}} {{trans_choice('general.credit',1)}}</th>
               <th align="right" style="text-align: right;" width="100">{{trans_choice('general.debit',1)}}</th>
               <th align="right" style="text-align: right;" width="100" style="text-align: right;">{{trans_choice('general.credit',1)}}</th>

               <th align="right" style="text-align: right;" width="100"> {{trans_choice('general.debit',1)}}</th>
               <th align="right" style="text-align: right;" width="100">{{trans_choice('general.credit',1)}}</th>
             </tr>
                    <?php
                    $office = \App\Models\Office::find($office_id);
                    $total_debit_balance = 0;
                    $total_credit_balance = 0;
                    $total_opening_balance = 0;
                    $total_closing_balance = 0;
                    $total_dr = 0;
                    $total_op_dr=0;
                    $total_cr = 0;
                    $total_op_cr=0;
                    $total_balance = 0;
                    $total_debits=0;
                    $total_credits=0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $dr = 0;
                        $cr = 0;
                        $op_balance_dr = 0;
                        $op_balance_cr = 0;
                        $balance=0;
                        $debits=0;
                        $credits=0;
                        
                        $journals = \App\Models\GlJournalEntry::where('gl_account_id' ,$key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                            if ($office_id != 0) {
                                $query->where('office_id', '=', $office_id);
                            }
                        })->whereBetween('date',
                        [$office->opening_date, $end_date])->get();

                        foreach ($journals as $journal) {
                            $cr = $cr + $journal->credit;
                            $dr = $dr + $journal->debit;
                            $op_balance_dr =  $op_balance_dr + $journal->op_balance_dr;
                            $op_balance_cr =  $op_balance_cr + $journal->op_balance_cr;

                            if($dr>$cr)
                        {
                            $dr = $dr - $cr;
                            $cr = 0;
                        }else{
    
                            $cr = $cr - $dr;
                            $dr = 0;
                        }

                           
                        }
                        $total_dr = $total_dr + $dr;
                        $total_cr = $total_cr + $cr;
                        $total_closing_balance = $total_closing_balance + $balance;
                        $total_op_cr = $total_op_cr + $op_balance_cr;
                        $total_op_dr = $total_op_dr + $op_balance_dr;
         
                        $fdebit = $op_balance_dr + $dr;
                        $fcredit = $op_balance_cr + $cr;
    
                        if($fdebit>$fcredit)
                        {
                            $debits = $fdebit - $fcredit;
                            $credits = 0;
                        }else{
    
                            $credits = $fcredit - $fdebit;
                            $debits = 0;
                        }
                      
                        $total_balance = $total_balance + $balance;
                        $total_debits = $total_debits + $debits + $dr;
                        $total_credits = $total_credits + $credits + $cr;


                        ?>
                       
                         
                            <tr>
                <th><b>{{ $key->gl_code }}</b></th>
                <th><b>{{$key->name}}</b></th>
                <td class="hidden"align="right">{{number_format($op_balance_dr ,2) }}</td>
                <td class="hidden"align="right">{{number_format($op_balance_cr ,2) }}</td>

                <td align="right">{{number_format($dr,2) }}</td>
                <td align="right">{{number_format($cr,2) }}</td>

                <td align="right">{{number_format($debits,2)}}</td>
                <td align="right">{{number_format($credits,2)}}</td>
              </tr>








                            
                    @endforeach
                    


                     <tr>
                <th></th>
                <th></th>
                <td class="hidden"align="right"><b> {{number_format($total_op_dr ,2)}}</b></td>
                <td class="hidden"align="right"><b>{{number_format ($total_op_cr ,2)}}</b></td>

                <td align="right"><b> {{number_format($total_dr ,2)}}</b></td>
                <td align="right"><b> {{number_format($total_cr ,2)}}</b></td>

                <td align="right"><b>{{number_format($total_debits,2)}}</b></td>
                <td align="right"><b>{{number_format($total_credits,2)}}</b></td>
              </tr>
                    
                    <tr style="height: 2pt">
                        <td class="style-8" colspan="6"></td>
                    </tr>
                    </tbody>

                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
