<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
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
        white-space: pre-wrap;
        background-color: #339933;
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
        white-space: pre-wrap;
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
        white-space: pre-wrap;
    }
</style>
<div>
<div class="col-md-12">
	                        <div class="white-box">
	                            <h3><b> @if(!empty($key->office)){{$key->office->name}} @endif - Reloans Statement <span class="pull-right"> </span></h3>
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
                                                 <b>@if(!empty($key->office)){{$key->office->name}} @endif  <br> 
                                                 from: <b>{{$start_date}} to {{$end_date}} <br> 
                                                 <br>
												</p>
											</address>
										</div>
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
                            <b>{{number_format($total_paid,2)}}</b>
                        </td>
                      
                        <td>
                            <b>{{number_format($new_bf - $credit_amount,2)}}</b>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
</div>