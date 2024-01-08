@extends('layouts.master')
@section('title')
    {{trans_choice('general.daily',1)}}  {{trans_choice('general.transaction',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
<style type="text/css">
.doubleUnderline {
    text-decoration:underline;
    border-bottom: 1px solid #000;
}
</style>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.daily',2)}}{{trans_choice('general.transaction',1)}} {{trans_choice('general.report',1)}}
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
                    <label for="loan_officer_id"
                           class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="loan_officer_id" class="form-control select2" id="loan_officer_id" required>
                            <option value="0">{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
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
                                    <a href="{{url('report/financial_report/daily_transaction/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&gl_account_id='.$gl_account_id)}}"
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
        <div class="panel panel-white">
            <div class="panel-body table-responsive">
        <div id="example_wrapper" class="dataTables_wrapper">
<table id="data_table" class="table table-striped ">
<thead > 
    @foreach ($data as $day => $users_list)
        <tr>
            <th colspan="3"
            class="text-center" scope="col" colspan="4">{{ $day }}: {{ $users_list->count() }} Entries  </th>
                
                <th colspan="5"
                style="background-color: #F7F7F7">
               
                 
                 
             </th>
            
         

                
        </tr>
        
        <tr role="row" style="height: 20pt">
                       
        <b> <td valign="middle" class="style-5"><b>{{trans_choice('general.reference',1)}}</b></td>
                        <td valign="middle"
                            class="style-5"><b>{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</b></td>
                        <td valign="middle" class="style-5"><b>{{trans_choice('general.date',1)}}</b></td>
                        <td valign="middle" class="style-5"><b>{{trans_choice('general.gl_code',1)}}</b></td>
                        <td valign="middle" class="style-5"><b>{{trans_choice('general.account',1)}}</b></td>
                        <td valign="middle" class="style-5"><b>{{trans_choice('general.debit',1)}}</b></td>
                        <td valign="middle" class="style-5"><b>{{trans_choice('general.credit',1)}}</b></td>
                    </tr>
                    <?php
                    $total_debit_balance = 0;
                    $total_credit_balance = 0;
                    $total_opening_balance = 0;
                    $total_closing_balance = 0;
                    $total_dr = 0;
                    $total_cr = 0;
                    $total_balance = 0;
                    ?>
        @foreach ($users_list as $key)
        <?php

        if(is_object($key)):

                // dd($key);
                        $dr = 0;
                        $cr = 0;
                        $balance = 0;
                        $cr = $cr + $key->credit ? $key->credit : null;
                        $dr = $dr + $key->debit ? $key->debit : null;
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
            <tr>
            <?php
                              $income = \App\Models\OtherIncome::where('id', $key->reference)->get();  
                              $expense = \App\Models\Expense::where('id', $key->reference)->get();  
                                  ?>
           
            <td>             @if($key->name=='income')
                                       
                                       @endif
                                       @if(!empty($key->payment_detail)) 
                                       {{ $key->payment_detail->notes }}
                                       @endif    
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
                               @if($key->name=='Other income')
                               {{$inkey->name}}
                             @endif
                              @endforeach
                       @foreach($expense as $exkey)
                               @if($key->transaction_type=='expense')
                               {{$exkey->name}}
                                   @endif
                              @endforeach
                              @if(empty($key->loan))
                                     
                                {{$key->notes}}
                                @endif
                                                                           
                              
                              </td>
            <td>  @if($key->transaction_type=='disbursement')
                                    {{trans_choice('general.disbursement',1)}}
                                @endif
                                @if($key->transaction_type=='accrual')
                                    {{trans_choice('general.accrual',1)}}
                                @endif
                                @if($key->transaction_type=='deposit')
                                    {{trans_choice('general.deposit',1)}}
                                @endif
                                @if($key->transaction_type=='withdrawal')
                                    {{trans_choice('general.withdrawal',1)}}
                                @endif
                                @if($key->transaction_type=='manual_entry')
                                    {{trans_choice('general.manual_entry',2)}}
                                @endif
                                @if($key->transaction_type=='pay_charge')
                                    {{trans_choice('general.pay',1)}}    {{trans_choice('general.charge',1)}}
                                @endif
                                @if($key->transaction_type=='transfer_fund')
                                    {{trans_choice('general.transfer_fund',1)}} {{trans_choice('general.charge',2)}}
                                @endif
                                @if($key->transaction_type=='expense')
                                    {{trans_choice('general.expense',1)}}
                                @endif
                                @if($key->transaction_type=='payroll')
                                    {{trans_choice('general.payroll',1)}}
                                @endif
                                @if($key->transaction_type=='income')
                                    {{trans_choice('general.income',1)}}
                                @endif
                                @if($key->transaction_type=='penalty')
                                    {{trans_choice('general.penalty',1)}}
                                @endif
                                @if($key->transaction_type=='fee')
                                    {{trans_choice('general.fee',1)}}
                                @endif
                                @if($key->transaction_type=='close_write_off')
                                    {{trans_choice('general.write',1)}}  {{trans_choice('general.waiver',2)}}
                                @endif
                                @if($key->transaction_type=='repayment_recovery')
                                    {{trans_choice('general.repayment',1)}}
                                @endif
                                @if($key->transaction_type=='repayment')
                                    {{trans_choice('general.repayment',1)}}
                                @endif
                                @if($key->transaction_type=='interest_accrual')
                                    {{trans_choice('general.interest',1)}} {{trans_choice('general.accrual',1)}}
                                @endif
                                @if($key->transaction_type=='fee_accrual')
                                    {{trans_choice('general.fee',1)}} {{trans_choice('general.accrual',1)}}
                                @endif
                                @if($key->transaction_type=='batch')
                                    Batch {{trans_choice('general.entry',2)}}
                                @endif
                                </td>
                            <td valign="middle" class="style-3">
                                {{$key->date}}
                            </td>
                            @if(!empty($key->gl_account))
                                <td> {{ $key->gl_account->gl_code }}</td>
                                <td> {{ $key->gl_account->name }}</td>
                            @else
                            <td></td>
                            <td></td>
                            @endif
                            <td valign="middle" class="style-4">{{ number_format($dr,2) }}</td>
                            <td valign="middle" class="style-4">{{ number_format($cr,2) }}</td>
            </tr>
              <?php endif ;?>
        @endforeach
        <tr style="height: 20pt">
                        <td colspan="5"scope="row" class="bg-dark"><b>{{trans_choice('general.total',1)}}</b></td>
                        <td valign="middle" class="style-12">
                        <span class="doubleUnderline"><b>{{number_format($total_dr,2)}}</b></td></span>
                        <td valign="middle" class="style-12">
                        <span class="doubleUnderline"><b>{{number_format($total_cr,2)}}</b></td></span>
                    </tr>
    @endforeach
    </thead> 
</table>
</div>
        </div>
        </div>
        </div>
        </div>
@endsection
@section('footer-scripts')

<script>
 $('#start_date').datepicker({
                format: 'yyyy-mm-dd',
                todayBtn: 'linked',
                weekStart: 1,
                autoclose: true,
                
            });

            $('#end_date').datepicker({
                format: 'yyyy-mm-dd',
                todayBtn: 'linked',
                weekStart: 1,
                autoclose: true,
               
            });


</script>
@endsection