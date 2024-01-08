@extends('layouts.master')
@section('title')
Daily Loan Activities Breakdown Report
@endsection
<style>

.table > tbody > tr > .thick-line {
    border-top: 2px solid;
}   
</style>
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
            Daily Loan Activities Breakdown Report
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
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}! bfuhs9djh9
                        </button>

                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/repayments_report_details/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/repayments_report_details/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/repayments_report_details/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->

    </div>

    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-primary">
            <div class="box-body table-responsive ">
                
            <div class="box-group" id="accordion">
                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                <div class="panel box box-success">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                       Full Payments
                      </a>
                    </h4><br>
                    <!-- <a href="{{url('report/loan_report/full_repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                               class="btn btn-info btn-sm pull-right"
                               data-toggle="tooltip" title="statement"><b><i
                                            class="fa fa-file"></i>
                                </b></a> -->
                                <div class="btn-group  pull-right">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/full_repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/full_repayments_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/full_repayments_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                    
                  </div>
                  <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="box-body">
                    <table class="table  table-condensed table-hover">
                    <tbody>
               
                    <tr class="">
                        <td><strong>{{trans_choice('general.id',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.client',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}}#</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</strong> </td>
                        <td><strong>{{trans_choice('general.amount',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.date',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.channel',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.office',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}</strong></td>
                    </tr>
                    <?php
                    $total_principal = 0;
                    $total_balance = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    $decimals = 0;
                    $balance = 0;
                    $total = 0;
                    $new_amount = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        if (!empty($key->loan)) {
                            $decimals = $key->loan->decimals;
                        } else {
                            $decimals = 0;
                        }
                        $principal = $key->principal_derived;
                        $interest = $key->interest_derived;
                        $fees = $key->fees_derived;
                        $new_amount = $key -> credit;
                        $total = $total + $new_amount;
                        $penalty = $key->penalty_derived;
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_penalty = $total_penalty + $penalty;
                        $total_balance = $total_balance + $balance;
                        ?>
                        <tr>
                            <td>{{$key->loan->client->external_id ?? 'None'}}</td>
                            <td>
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
                            </td>
                            <td>{{$key->loan->id ?? 'None'}}</td>
                            <td>
                                @if(!empty($key->loan))
                                    @if(!empty($key->loan->loan_officer))
                                        {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                                    @endif
                                @endif
                            </td>
                            
                            <!-- <td>{{number_format($principal+$interest+$fees+$penalty,$decimals)}}</td> -->
                             <td>{{$key->credit}}</td>
                            <td>{{$key->date}}</td>
                            <td>
                                @if(!empty($key->payment_detail))
                                    @if(!empty($key->payment_detail->type))
                                        {{$key->payment_detail->type->name}}
                                    @endif
                                @endif
                            </td>
                            <td>  @if(!empty($key->office))
                                    {{$key->office->name}}
                                @endif</td>
                            <td>
                            @if($key->payment_apply_to=="full_payment")
                            <span class="label label-success">Full Payment</span>
                            @endif       
                                
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tr>
    								<td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line text-center"><strong></strong></td>
    								<td class="thick-line text-right"></td>
    							</tr>
                    <tfoot>
                   
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Total</b></td>
                        
                        <td>
                            <!-- <b>{{number_format($total_principal+$total_interest+$total_fees+$total_penalty,$decimals)}}</b> -->
                            <b>{{number_format($total,2)}}</b>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                    </div>
                  </div>
                </div>
      </div>


      <div class="panel box box-info">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapsefour">
                        Reloan Payments
                      </a>
                    </h4>
                    <!-- <a href="{{url('report/loan_report/reloans_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                               class="btn btn-info btn-sm pull-right"
                               data-toggle="tooltip" title="statement"><b><i
                                            class="fa fa-file"></i>
                                </b></a> -->

                                <div class="btn-group  pull-right">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/reloans_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/reloans_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/reloans_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                  </div>
                  <div id="collapsefour" class="panel-collapse collapse">
                    <div class="box-body">
                    <table class="table  table-condensed table-hover">
                    <tbody>
               
                    <tr class="">
                        <td><strong>{{trans_choice('general.id',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.client',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}}#</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</strong> </td>
                        <td><strong>{{trans_choice('general.balance',1)}} B/F</strong></td>
                        <!-- <td><strong>{{trans_choice('general.interest',1)}} {{trans_choice('general.paid',1)}}</strong></td> -->
                        <td><strong>Paid</strong></td>
                        <td><strong>{{trans_choice('general.outstanding',1)}}</strong></td>
 
                        <td><strong>{{trans_choice('general.date',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.status',1)}}</strong></td>
                    </tr>
                    <?php
                    $bf=0;
                    $amount_in_arrears = 0;
                    $total_principal = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    $decimals = 0;
                    $total_outstanding=0;
                    $interest_sch=0;
                    $interest_paid=0;
                    $total_interest_paid=0;
                    $prev_balance = 0;
                    $total_prev_balance = 0;
                    $paid_amount = 0;
                    $new = 0;
                    $new_amount = 0;
                    $total_paid = 0;
                    $outstanding_total = 0;
                    $new_balance = 0;
                    $outstanding_new = 0;
                    $new_bf = 0;
                    $credit_amount = 0;
                    ?>
                    @foreach($reloans_data as $key)
                        <?php
                        $new_balance = $new_balance + ($key->debit - $key->credit);
                        if (!empty($key->loan)) {
                            $decimals = $key->loan->decimals;
                        } else {
                            $decimals = 0;
                        }
                   
                        // $interest = \App\Models\LoanRepaymentSchedule::where('loan_id', $key->loan->id)->first();
                        
                        $bdr=0;
                        $bcr=0;
                        $badrcr=0;
                        //sum of credit amounts
                        $paid_amount = \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('payment_apply_to','reloan_payment')->sum('credit');
                        $new = \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('payment_apply_to','reloan_payment')->sum('debit');
                        $interest_paid =  \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('payment_apply_to','reloan_payment')->sum('interest_derived');
                        $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->loan_id);
                        $interest_new = $key->interest_derived;
                        $new_amount = $key -> credit;
                        $bf_balance = $key ->balance_bf;
                        $total_interest_paid = $total_interest_paid + $interest_new;
                        $total_paid = $total_paid + $new_amount;
                        $prev_balance = $balance - $interest_paid;
                        $total_bf = 0; 
                        $total_bf = $total_bf + $bf;
                        $total_prev_balance  = $total_prev_balance  + $prev_balance;
                        $test =  \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('date','<',$key->date)->sum('debit');
                        $bf =  \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('date','<',$key->date)->sum('debit');
                        $new_bf = $new_bf + $bf_balance; //- $new_amount;
                        $credit_amount = $credit_amount + $new_amount;
                        $bdr =  \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('date','<',$key->date)->sum('debit');
                        $bcr =  \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('date','<',$key->date)->sum('credit');
                        $badrcr=$bdr-$bcr;
                        $total_outstanding = $total_outstanding + $balance;
                        $outstanding_amount = $bf - $key->credit;


                        $outstanding_total = $outstanding_total + $outstanding_amount;

                       
                       
                        ?>
                             @if(!empty($key->loan->status))
                  
                        <tr>

                     <td>{{$key->loan->client->external_id ?? 'None'}}</td>
                            <td>
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
                            </td>

                            <td>{{$key->loan->id ?? 'None'}}</td>

                            <td>
                                uugu
                                @if(!empty($key->loan))
                                    @if(!empty($key->loan->loan_officer))
                                        {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                                    @endif
                                @endif
                            </td>
                            
                            <td>{{number_format($key->balance_bf, $decimals)}}</td>
                            <!-- <td>{{number_format($bf, $decimals)}}</td> -->
                            <!-- <td>{{number_format($key->interest_derived, $decimals)}}</td> -->
                            <td>{{number_format($key->credit, $decimals)}}</td>
                            <!-- <td>{{number_format($balance,$decimals)}}</td> -->
                            <!-- <td>{{number_format($bf - $key->credit ,$decimals)}}</td> -->
                            <td>{{number_format($key->balance_bf-$key->credit,$decimals)}}</td>
                            <!-- <td>{{number_format($new_balance ,$decimals)}}</td> -->
                            <td>{{$key->date}}</td>
                            
                            <td>
                        @if(!empty($key->loan->status))
                          @if($key->loan->status=="disbursed")
                            <span class="label label-primary">{{trans_choice('general.active',1)}}</span>
                            @endif
                            @if($key->loan->status=="closed")
                            <!-- <span class="label label-danger">{{trans_choice('general.closed',1)}}</span> -->
                            @endif
                        @endif
                            </td>


                        </tr>

                         @endif
                
                    @endforeach
                  
                    </tbody>
                    <tr>
    								<td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line text-center"><strong></strong></td>
    								<td class="thick-line text-right"></td>
    							</tr>
                    <tfoot>
                   
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Total</b></td>
                        
                        <td>
                            <b></b>
                        </td>
                        <td>
                        <!-- <b>{{number_format($new_amount,2)}}</b> -->
                            <b>{{number_format($total_paid,2)}} </b>
                            <!-- <b>{{number_format($paid_amount,2)}}</b> -->
                              <!-- <b>{{number_format($interest_paid,2)}}</b> -->
                        </td>
                      
                        <td>
                            <!-- <b>{{number_format($outstanding_new,2)}}</b> -->
                             <b>{{number_format($new_bf - $credit_amount,2)}}</b>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                    </div>
                  </div>
                </div>


                <div class="panel box box-warning">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                        Part Payments
                      </a>
                    </h4>
                                <div class="btn-group  pull-right">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/part_repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/part_repayments_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/part_repayments_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                  </div>
                  <div id="collapseThree" class="panel-collapse collapse">
                    <div class="box-body">
                     
                      <table class="table  table-condensed table-hover">
                    <tbody>
               
                    <tr class="">
                        <td><strong>{{trans_choice('general.id',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.client',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}}#</strong></td>
                        <td><strong>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</strong> </td>
                        <td><strong>{{trans_choice('general.amount',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.balance',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.date',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.channel',1)}}</strong></td>
                        <td><strong>{{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}</strong></td>
                    </tr>
                    <?php
                    $total_principal = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    $decimals = 0;
                    $total_outstanding = 0;
                    $paid_amount = 0;
                    $total = 0
                    ?>
                    @foreach($part_data as $key)
                        <?php
                        if (!empty($key->loan)) {
                            $decimals = $key->loan->decimals;
                        } else {
                            $decimals = 0;
                        }
                       
                        $paid_amount = \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('payment_apply_to','part_payment')->sum('credit');
                        $principal = $key->principal_derived;
                        $interest = $key->interest_derived;
                        $fees = $key->fees_derived;
                        $penalty = $key->penalty_derived;
                        $new_amount = $key -> credit;
                        $total = $total + $new_amount;
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_penalty = $total_penalty + $penalty;
                        $amount_in_arrears = 0;
                        $expected = 0;
                        $balancep = 0;
                        $total_balance=0;
                        $total_balancep=0;
                        $total_expected=0;
                        $principal_outstanding = 0;
                        $total_principal_outstanding = 0;
                        $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->loan_id);
                      
                        $bf_balance=0;
                        $total_balance = $total_balance + $balance;
                        $total_balancep = $total_balancep + $balancep;
                        $bf_balance = $principal + $interest + $fees + $penalty;
                        $total_outstanding = $total_outstanding + $balance;

                       ?>
                       
                        <tr>
                            <td>{{$key->loan->client->external_id}}</td>
                            <td>
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
                            </td>
                            <td>{{$key->loan->id}}</td>
                            <td>
                                @if(!empty($key->loan))
                                    @if(!empty($key->loan->loan_officer))
                                        {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                                    @endif
                                @endif
                            </td>
                            
                            <!-- <td>{{number_format($bf_balance,$decimals)}}jbj</td> -->
                            <!-- <td>{{number_format($paid_amount,$decimals)}}</td> -->
                            <td>{{number_format($key->credit,$decimals)}}</td>
                            <td>{{number_format($balance,$decimals)}}</td>
                            <td>{{$key->date}}</td>
                            <td>
                                @if(!empty($key->payment_detail))
                                    @if(!empty($key->payment_detail->type))
                                        {{$key->payment_detail->type->name}}
                                    @endif
                                @endif
                            </td>
                            <td>
                            @if($key->payment_apply_to=="part_payment")
                            <span class="label label-warning">Part Payment</span>
                            @endif     
                                
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tr>
    								<td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
    								<td class="thick-line text-center"><strong></strong></td>
    								<td class="thick-line text-right"></td>
    							</tr>
                    <tfoot>
                   
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Total</b></td>
                        
                        <td>
                            <b>{{number_format($total,2)}}</b>
                        </td>
                        <td>
                            <b>{{number_format($total_outstanding,2)}}</b>     <!-- ERROR HERE -->
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                    </div>
                  </div>
                </div>



                <div class="panel box box-success">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapsefive">
                        New Loans
                      </a>
                    </h4>
                                <div class="btn-group  pull-right">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/new_loans_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/new_loans_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/new_loans_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div>
                  </div>
                  <div id="collapsefive" class="panel-collapse collapse">
                    <div class="box-body">
        <table class="table">
  <thead>
    <tr>
      <th>NRC</th>
      <th >{{trans_choice('general.client',1)}} {{trans_choice('general.name',1)}}</th>
      <th >{{trans_choice('general.product',1)}}</th>
      <th>{{trans_choice('general.amount',1)}}</th>
      <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}</th>
      <th>Loan Officer</td>
      <th>{{trans_choice('general.office',1)}}</td>
    </tr>
  </thead>
  <tbody>
  <?php

$total_due = 0;
$total_principal = 0;
$total_principal_paid = 0;
$total_principal_outstanding = 0;
$total_interest = 0;
$total_interest_paid = 0;
$total_interest_outstanding = 0;
$total_fees = 0;
$total_fees_paid = 0;
$total_fees_outstanding = 0;
$total_penalty = 0;
$total_penalty_paid = 0;
$total_penalty_outstanding = 0;
$total_outstanding = 0;
$total_amount = 0;
$total_arrears = 0;
$total_loans = 0;
?>
  @foreach($new_loans as $key)
  <?php
    $amount = 0;
    $outstanding = 0;
    $principal = 0;
    $principal_paid = 0;
    $interest = 0;
    $interest_paid = 0;
    $fees = 0;
    $fees_paid = 0;
    $penalty = 0;
    $penalty_paid = 0;
    $amount_in_arrears = 0;
    $days_in_arrears = 0;
    $balance = 0;
    $percentage = 0;
    $late_count = 0;
    $timely_repayments = 0;
    $installments = 0;
    $total_repayments_due = 0;
    foreach ($key->repayment_schedules as $schedule) {
        $installments++;
        if (strtotime($schedule->due_date) < strtotime($end_date)) {
            $total_repayments_due++;
            $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
        }
        if (!empty($schedule->from_date)) {
            if (strtotime($schedule->due_date) > strtotime($schedule->from_date) && strtotime($schedule->due_date) < strtotime($end_date)) {
                $timely_repayments = $timely_repayments + 1;
            }
        }
        $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
        $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
        $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
        $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;

        $principal_paid = $principal_paid + $schedule->principal_paid;
        $interest_paid = $interest_paid + $schedule->interest_paid;
        $fees_paid = $fees_paid + $schedule->fees_paid;
        $penalty_paid = $penalty_paid + $schedule->penalty_paid;
        $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
        if ($amount_in_arrears > 0) {
            $late_count++;
            if ($late_count == 1) {
                $overdue_date = $schedule->due_date;
            }
        }
    }

    if ($amount_in_arrears > 0) {
        $date1 = new DateTime($overdue_date);
        $date2 = new DateTime($end_date);
        $days_in_arrears = $date2->diff($date1)->format("%a");
    }
    if ($total_repayments_due > 0) {
        $percentage = round($timely_repayments * 100 / $total_repayments_due, 2);
    }
    $amount = $principal + $penalty + $interest + $fees;
    $total_amount = $total_amount + $amount;
    $total_outstanding = $total_outstanding + $balance;
    $total_due = $total_due + $amount_in_arrears;
    $total_principal = $total_principal + $principal;
    $total_interest = $total_interest + $interest;
    $total_fees = $total_fees + $fees;

    $total_penalty = $total_penalty + $penalty;
    $total_principal_paid = $total_principal_paid + $principal_paid;
    $total_interest_paid = $total_interest_paid + $interest_paid;
    $total_fees_paid = $total_fees_paid + $fees_paid;
    $total_penalty_paid = $total_penalty_paid + $penalty_paid;

    $total_principal_outstanding = $total_principal_outstanding + $principal - $principal_paid;
    $total_interest_outstanding = $total_interest_outstanding + $interest - $interest_paid;
    $total_fees_outstanding = $total_fees_outstanding + $fees - $fees_paid;
    $total_penalty_outstanding = $total_penalty_outstanding + $penalty - $penalty_paid;
    $total_amount = $total_amount + $key->principal;
    $total_arrears = $total_arrears + $amount_in_arrears;
    //select appropriate schedules


    ?>
  <tr>
      
      <td>{{$key->external_id}}</td>
      <td>@if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                        @endif
                                        @if($key->client->client_type=="business")
                                            {{$key->client->full_name}}
                                        @endif
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->name}}
                                    @endif
                                @endif</td>
                               
                                <td>@if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif</td>
                                <td class="primary">{{number_format($key->principal, 2) }}</td>
                                <td>@if(!empty($key->loan_purpose))
                                    {{$key->loan_purpose->name}}
                                @endif</td>
                                <td>   
                                @if(!empty($key))
                                    @if(!empty($key->loan_officer))
                                        {{$key->loan_officer->first_name}}  {{$key->loan_officer->last_name}}
                                    @endif
                                @endif
                                    <?php
                                // $disbursement_detail = \App\Models\LoanTransaction::where('transaction_type', 'disbursement')->where('reversed', 0)->orderBy('date', 'asc')->first();
                                // if (!empty($disbursement_detail)) {
                                //     if (!empty($disbursement_detail->payment_detail)) {
                                //         if (!empty($disbursement_detail->payment_detail->type)) {
                                //             echo $disbursement_detail->payment_detail->type->name;
                                //         }
                                //     }
                                // }
                                ?>
                                
                            </td>
                                <td>{{$key->office->name ?? 'None'}}</td>
    </tr>
    
    @endforeach
                    </tbody>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_principal,2)}}</th>
                        <th></th>
                        <th></th>
                        <th> </td>
                        <th></th>
                        <th></th>
                        <th> </td>
                        
                    </tr>
                    </table>
                    </div>
                  </div>
                </div>

                <div class="panel box box-warning">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                        Top Ups
                      </a>
                    </h4>
                                <!-- <div class="btn-group  pull-right">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/part_repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/part_repayments_report/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/part_repayments_report/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a>
                                </li>
                              
                            </ul>
                        </div> -->
                  </div>
                  <div id="collapseSix" class="panel-collapse collapse">
                    <div class="box-body">
                     
                      <table class="table  table-condensed table-hover">
                    <tbody>
                    <tr class="">
                        <td><strong>ID</strong></td>
                        <td><strong>Date</strong></td>
                        <td><strong>Loan#</strong></td>
                        <td><strong>Loan officer</strong> </td>
                        <td><strong>Client</strong></td>
                        <td><strong>Office</strong></td>
                        <td><strong>Balance B/F</strong></td>
                        <td><strong>Top-up amount</strong></td>
                        <td><strong>Outstanding</strong></td>
                    </tr>
                    <?php
                    $total_principal = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    $decimals = 0;
                    $total_outstanding = 0;
                    $paid_amount = 0;
                    $total = 0
                    ?>
                    @foreach($top_up as $key)
                        <?php
                        // if (!empty($key->loan)) {
                        //     $decimals = $key->loan->decimals;
                        // } else {
                        //     $decimals = 0;
                        // }
                        $decimals = 0;
                       
                        // $paid_amount = \App\Models\LoanTransaction::where('loan_id',$key->loan_id)->where('payment_apply_to','part_payment')->sum('credit');
                        // $principal = $key->principal_derived;
                        // $interest = $key->interest_derived;
                        // $fees = $key->fees_derived;
                        // $penalty = $key->penalty_derived;
                        // $new_amount = $key -> credit;
                        // $total = $total + $new_amount;
                        // $total_principal = $total_principal + $principal;
                        // $total_interest = $total_interest + $interest;
                        // $total_fees = $total_fees + $fees;
                        // $total_penalty = $total_penalty + $penalty;
                        // $amount_in_arrears = 0;
                        // $expected = 0;
                        // $balancep = 0;
                        // $total_balance=0;
                        // $total_balancep=0;
                        // $total_expected=0;
                        // $principal_outstanding = 0;
                        // $total_principal_outstanding = 0;
                        // $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->loan_id);
                      
                        // $bf_balance=0;
                        // $total_balance = $total_balance + $balance;
                        // $total_balancep = $total_balancep + $balancep;
                        // $bf_balance = $principal + $interest + $fees + $penalty;
                        // $total_outstanding = $total_outstanding + $balance;

                       ?>
                       
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                {{$key->date}}
                            </td>
                            <td>{{$key->loan_id}}</td>
                            <td>  
                             {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                            </td>
                            <td>{{$key->loan->client->first_name}} {{$key->loan->client->last_name}}</td>
                            <td> {{$key->office->name}}</td>
                            <td>{{number_format($key->balance_bf,2)}}</td>
                            <td>{{number_format($key->amount,2)}}</td>
                            <td>{{number_format($key->balance_new,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tr>
    								<td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
                                    <td class="thick-line"></td>
    								<td class="thick-line"></td>
    								<td class="thick-line text-center"><strong></strong></td>
    								<td class="thick-line text-right"></td>
    							</tr>
                    <tfoot>
                   
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Total</b></td>
                        
                        <td>
                            <b></b>
                        </td>
                        <td>
                            <b></b>     <!-- ERROR HERE -->
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                    </div>
                  </div>
                </div>



            </div>
        </div>











   

      

 



























                           
               
              
    @endif
@endsection
@section('footer-scripts')

@endsection
