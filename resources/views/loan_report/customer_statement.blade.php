@extends('layouts.master')
@section('title')
    Customer {{trans_choice('general.statement',1)}}
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
            font-size: 10pt;
            font-family: "Tahoma", Helvetica, Arial, sans-serif;
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
                {{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}
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
     
                  <!-- Date and time range -->
                  <div class="form-group">
                                        
                                    </div><!-- /.form group--->
           
                
                <div class="form-group">
                    <label for="start_date"
                           class="control-label col-md-2">{{trans_choice('general.period',1)}}</label>
                    <div class="col-md-3">
                    <input type="text" class="daterangepicker-field form-control" value="{{$start_date}} To {{$end_date}}" required />
                                <input type="hidden" name="start_date" value="{{$start_date}}" />
                                <input type="hidden" name="end_date" value="{{$end_date}}" />
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
                <?php
                        $office_id = Sentinel::getUser()->office_id;
                        ?>
                <div class="form-group">
                    <label for="loan_id"
                           class="control-label col-md-2">{{trans_choice('general.account',1)}}</label>
                    <div class="col-md-3">
                    <select name="loan[]" class="form-control select2" style="width:250px">
                    <option value="">--- Select Loan ---</option>
                    @foreach(\App\Models\Loan::where('office_id',$office_id)->with('loan_product')->get() as $key)
                                <option value= "{{$key->id}}"> Loan # {{$key->id}}---{{$key->client->external_id}}--- {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}--- {{$key->loan_product->name}} </option>
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
                                    <a href="{{url('report/financial_report/journals_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_id='.$loan_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/journals_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_id='.$loan_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/journals_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&loan_id='.$loan_id)}}"
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
    <?php
     $stroffice = \App\Models\Office::find($office_id);
    

     ?>
   


     
        <div class="panel panel-white">

       
     
            <div class="panel-body table-responsive">
            <div class="col-md-12">
	                        <div class="white-box">
	                            <h3><b>{{\App\Models\Loan::find($loan_id)->client->first_name}}  {{\App\Models\Loan::find($loan_id)->client->last_name}}   - Account Statement   <b>{{$start_date}} to {{$end_date}} <br> </b> <span class="pull-right"> </span></h3>
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
                                                 <b>{{\App\Models\Loan::find($loan_id)->office->name}}  <br> 
                                                 from: <b>{{$start_date}} to {{$end_date}} <br> 
                                                 <br>
												</p>
											</address>
										</div>
										<div class="pull-right text-right">
                                        <div class="alert alert-info" role="alert">
											
                                            {{\App\Models\Loan::find($loan_id)->status}}
											
										</div>
    </div>
									</div>
	                               
                                    <table id="repayments-data-table"
                                                   class="table  table-condensed table-hover">
                                                <thead>
                                                    
                                                <tr>
                                                    <th>
                                                        {{trans_choice('general.id',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.date',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                                                    </th>
                                                    <th>
                                                    {{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}
                                                    </th>

                                                    <th>
                                                        {{trans_choice('general.debit',1)}} [ZMK]
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.credit',1)}} [ZMK]
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.balance',1)}}  [ZMK]
                                                    </th>
                                                   
                                                </tr>
                                                </thead>
                                                <tbody>
                                           


                                                <?php
                                                $total_dr = 0;
                                                $total_cr = 0;
                                                $balance = 0;
                                                $dr = 0;
                                                $cr = 0;
                                                ?>
                                                @foreach(\App\Models\LoanTransaction::where('loan_id',$loan_id)->whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->get() as $key)
                                                    <?php
                                                   
                                                    $cr = $cr + $key->credit;
                                                    $dr = $dr + $key->debit;
                                                    $total_dr = $total_dr + $key->debit;
                                                    $total_cr = $total_cr + $key->credit;
                                                    $balance = $balance + ($key->debit - $key->credit);
                                                    
                                                    ?>
                                                    <tr>
                                                        <td>{{$key->id}}</td>
                                                        <td>{{$key->date}}</td>
                                                        <td>{{$key->created_at}}</td>
                                                        <td>
                                                            @if($key->transaction_type=='disbursement')
                                                                {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='specified_due_date_fee')
                                                                {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='installment_fee')
                                                                {{trans_choice('general.installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_installment_fee')
                                                                {{trans_choice('general.overdue_installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='loan_rescheduling_fee')
                                                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_maturity')
                                                                {{trans_choice('general.overdue_maturity',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='disbursement_fee')
                                                                {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment')
                                                                {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='penalty')
                                                                {{trans_choice('general.penalty',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest_waiver')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='waiver')
                                                                {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='')
                                                                {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='charge_waiver')
                                                                {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='write_off')
                                                                {{trans_choice('general.write_off',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment_disbursement')
                                                                {{trans_choice('general.repayment',1)}} {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='write_off_recovery')
                                                                {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->reversed==1)
                                                                @if($key->reversal_type=="user")
                                                                    <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif
                                                                @if($key->reversal_type=="system")
                                                                    <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif

                                                            @endif
                                                         
                                                        </td>
                                                        <td>{{number_format($key->debit,2)}}</td>
                                                        <td>{{number_format($key->credit,2)}}</td>
                                                        <td>{{number_format($balance,2)}}</td>
                                                        
                                                       
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tr >
                    
                    <th></th>
                    <th align="left" class="style-3"><b>TOTALS</b></th>
                    <th align="left"></th>
                    <th align="left"></th>
                    <th align="right" style="text-align: left" class="style-3"><b>{{ number_format($total_dr,2) }}</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b>{{ number_format($total_cr,2) }}</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b></b></th>
                  </tr>
                    <tr style="height: 2pt">
                        <td class="style-8" colspan="8"></td>
                    </tr>
                                            </table>





            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')
<script type="text/javascript">
           

   $(".daterangepicker-field").daterangepicker({
  callback: function(startDate, endDate, period){
    var start_date = startDate.format('YYYY-MM-DD');
    var end_date = endDate.format('YYYY-MM-DD');
    var title = start_date + ' To ' + end_date;
    $(this).val(title);
    $('input[name="start_date"]').val(start_date);
    $('input[name="end_date"]').val(end_date);
  }
});
        </script>
@endsection
