@extends('layouts.master')
@section('title')
    {{trans_choice('general.account',2)}} {{trans_choice('general.statement',1)}}
@endsection
@section('content')
    <style type="text/css">
        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 676pt
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
                {{trans_choice('general.journal',2)}} {{trans_choice('general.report',1)}}
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
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="gl_account_id"
                           class="control-label col-md-2">{{trans_choice('general.account',1)}}</label>
                    <div class="col-md-3">
                        <select name="gl_account_id" class="form-control select2" id="gl_account_id" required>
                            <option>@if($office_id==0) selected @endif{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\GlAccount::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($gl_account_id==$key->id) selected @endif>{{$key->name}}</option>
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
                                    <a href="{{url('report/financial_report/journals_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&gl_account_id='.$gl_account_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/journals_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&gl_account_id='.$gl_account_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/journals_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&gl_account_id='.$gl_account_id)}}"
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
        <div class="panel panel-white">

       
     
            <div class="panel-body table-responsive">
            <div class="col-md-12">
	                        <div class="white-box">
	                            <h3><b> {{\App\Models\GlAccount::find($gl_account_id)->name}} - Ledger Statement   <b>{{$start_date}} to {{$end_date}} <br> </b> <span class="pull-right"> </span></h3>
	                            <hr>
	                            <div class="row">
	                                <div class="col-md-12">
										<div class="pull-left">
											<address>
                                            @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
            <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                 class="img-responsive" width="90"/><br> 

        @endif
												<p class="text-muted m-l-6">
                                                 <b>{{\App\Models\Office::find($office_id)->name}}  <br> 
                                                 from: <b>{{$start_date}} to {{$end_date}} <br> 
                                                 <br>
												</p>
											</address>
										</div>
										<div class="pull-right text-right">
											<address>
												
												</p>
											</address>
										</div>
									</div>
	                               
                <table class="table table-hover">

                    <tbody>
                   
                    

                   
                 
                    <tr style="height: 20pt">
                    
                
                            <th width="150" class="style-3"><b>{{trans_choice('general.date',1)}}</b></th>
                            <th width="100" style="text-align: left;" class="style-3"><b>{{trans_choice('general.reference',1)}}</b></th>
                            <th width="100" style="text-align: left;" class="style-3"><b>Entry</b></th>
                            <th width="150" style="text-align: left;" class="style-3"><b>{{trans_choice('general.debit',1)}}</b></th>
                            <th width="150" style="text-align: left;" class="style-3"><b>{{trans_choice('general.credit',1)}}</b></th>
                           
                            <th width="150" style="text-align: left;" class="style-3"><b>{{trans_choice('general.balance',1)}}</b></th>
                            
                    </tr>
<!-- get initial balance -->

                    <?php
                      $opening_balance = 0;
                      $close_balance = 0;
                      $ocr = 0;
                      $odr = 0;
                      $b_dr = 0;
                      $b_cr = 0;
                      $total_dr = 0;
                      $total_cr = 0;
                      $op_bal_dr = 0;
                      $op_bal_cr = 0;
                      $toffice = \App\Models\Office::find($office_id);
                      $journals = \App\Models\GlJournalEntry::where('gl_account_id', $gl_account_id)->where('office_id', $office_id)->where('date','>=',$toffice->opening_date)->first();
                      $op_bal_dr = $op_bal_dr + $journals->op_balance_dr;
                      $op_bal_cr = $op_bal_cr + $journals->op_balance_cr;
                      $opening_balance = $op_bal_dr - $op_bal_cr;
                      $balancebf = $opening_balance + $current_balance;
                      
                      
                      
                    ?>
         
                    
       
            <tr>
                  
                  <th width="200" style="text-align: left;"></th>
                  <th width="200" style="text-align: left;"></th>
                  <th width="250"  class="style-3" style="text-align: left;"><b>Balance Brought Forward</b></th>
                  <th width="150" align="right" class="style-3" style="text-align: left;" width="100"></th>
                  <th width="150" align="right" class="style-3" style="text-align: left;" width="100" style="text-align: right;"></th>
                  <th width="150" align="right" class="style-3" style="text-align: left;" width="100" style="text-align: right;">{{ number_format ($balancebf,2) }}</th>
                </tr>

   
                   
              
                    <?php




  
                     $total_op_dr = 0;
                     $total_op_cr = 0;
                     $op_balance_dr = 0;
                     $op_balance_cr = 0;
                     $op_balance_dr =  $op_balance_dr + $key->op_balance_dr;
                     $op_balance_cr =  $op_balance_cr + $key->op_balance_cr;
                     $total_op_dr = $total_op_dr + $op_balance_dr;
                     $total_op_cr = $total_op_cr + $op_balance_cr;
                    
                    ?>

                     <?php
                    $total_debit_balance = 0;
                    $total_credit_balance = 0;
                    $total_opening_balance = 0;
                    $total_closing_balance = 0;
                    $total_op_dr = 0;
                    $total_op_cr = 0;
                    $total_dr = 0;
                    $total_cr = 0;
                    $total_balance = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $op_bal_dr = 0;
                        $op_bal_cr = 0;
                        $op_bal_dr = $op_bal_dr + $key->op_balance_dr;
                        $op_bal_cr = $op_bal_cr +$key->op_balance_cr;
                        $dr = 0;
                        $cr = 0;
                        $balance = 0;
                        $cr = $cr + $key->credit;
                        $dr = $dr + $key->debit;
                        $total_op_dr = $total_op_dr + $op_bal_dr;
                        $total_op_cr = $total_op_cr + $op_bal_cr;
                        $total_dr = $total_dr + $dr;
                        $total_cr = $total_cr + $cr;
                        if ($key->account_type == "asset" || $key->account_type == "expense") {
                            //debit balance
                            $balance = $dr - $cr;
                            
                        }
                        if ($key->account_type == "liability" || $key->account_type == "equity" || $key->account_type == "income") {
                            //debit balance
                            $balance = $cr - $dr;
                            
                        }
                        $total_balance = $total_balance + $balance;
                        ?>
                                      <?php
                                     
      
                                      $prev_balance = 0;
                                      $prev_balance = $balancebf + $total_op_dr - $total_op_cr;
                                      $closing_balance = 0;
                                      $closing_balance =  $closing_balance + $prev_balance + $total_dr - $total_cr;
                                      $prev_balance = $closing_balance;
                                      $debits=0;
                                      $credits=0;
      
                                      $fdebit = $op_balance_dr + $dr;
                                      $fcredit = $op_balance_cr + $cr;
      
          
                              if($fdebit>$fcredit)
                              {
                                  $debits = $prev_balance - $debits;
                                  $credits = "";
                              }else{
          
                                  $credits = $prev_balance - $credits;
                                  $debits = "";
                              }

                              $income = \App\Models\OtherIncome::where('id', $key->reference)->get();  
                              $expense = \App\Models\Expense::where('id', $key->reference)->get();  
                                          
                                      ?>
                  
                   


                  
 

                        <tr style="height: 15pt">
                            <td valign="middle" class="style-3">{{$key->date }}</td>
                            <td valign="middle" class="style-3">
                                    @if(!empty($key->loan))
                                    @if($key->loan->client_type=="client")
                                        @if(!empty($key->loan->client))
                                            @if($key->loan->client->client_type=="individual")
                                                {{$key->loan->client->first_name}} {{$key->loan->client->middle_name}} {{$key->loan->client->last_name}}
                                            @endif
                                            @if($key->loan->client->client_type=="business")
                                                {{$key->loan->client->full_name}}
                                            @endif
                                        @endif
                                    @endif
                                    @if($key->loan->client_type=="group")
                                        @if(!empty($key->loan->group))
                                            {{$key->loan->group->name}}
                                        @endif
                                    @endif
                                @endif
                            @foreach($income as $inkey)
                               @if($key->transaction_type=='income')
                               {{$inkey->name}}
                             @endif
                              @endforeach
                       @foreach($expense as $exkey)
                               @if($key->transaction_type=='expense')
                               {{$exkey->name}}
                                   @endif
                              @endforeach
                              @if($key->transaction_type=='manual_entry')
                               {{$key->notes}}
                             @endif
                            </td>
                            
                            <td valign="middle" class="style-3"> {{ $key->transaction_type}}</td>
                        
                           
                        
                           
                                         
                            <td valign="middle" class="style-3">{{ number_format($dr,2) }}</td>
                            <td valign="middle" class="style-3">{{ number_format($cr,2) }}</td>
                            <td valign="right" class="style-3">
                                <?php
                                
                                 ?>
                                
                            <b class="style-3">{{ number_format($closing_balance,2) }}</b></td>
                        </tr>
                        
                    @endforeach
                    <tr >
                    
                    <th></th>
                    <th align="left"></th>
                    <th align="left"></th>
                    <th align="right" style="text-align: left" class="style-3"><b>Total DR</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b>Total CR</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b>Balance</b></th>
                  </tr>
                    <tr style="height: 2pt">
                        <td class="style-8" colspan="8"></td>
                    </tr>
                   
                    </tbody>

                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
