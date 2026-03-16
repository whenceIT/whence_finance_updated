@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.repayment',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.repayment',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/'.$loan->id.'/repayment/store')}}" class="form-horizontal"
              enctype="multipart/form-data" id='form1'>
            {{csrf_field()}}
            <div class="box-body">
                <?php
                $schedule = DB::table('loan_repayment_schedules')->select(DB::raw('due_date,(COALESCE(principal,0)+COALESCE(interest,0)+COALESCE(fees,0)+COALESCE(penalty,0)-COALESCE(principal_waived,0)-COALESCE(principal_written_off,0)-COALESCE(principal_paid,0)-COALESCE(interest_waived,0)-COALESCE(interest_written_off,0)-COALESCE(interest_paid,0)-COALESCE(fees_waived,0)-COALESCE(fees_written_off,0)-COALESCE(fees_paid,0)-COALESCE(penalty_written_off,0)-COALESCE(penalty_paid,0)) as due'))->where('loan_id', $loan->id)->orderBy('due_date', 'asc')->havingRaw("due>0")->first();
                if (!empty($schedule)) {
                    $payment_amount = $schedule->due;
                    $payment_date = $schedule->due_date;
                }else{
                    $payment_amount = "";
                    $payment_date ="";
                }
                
                $balance=0;
                
                foreach (App\Models\LoanTransaction::where('loan_id',$loan->id)->whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->get() as $key) {
                    $balance = $balance + ($key->debit - $key->credit);   
		}

		$repayment_date = date('Y-m-d', strtotime($loan->first_repayment_date. ' + 1 months'));
                
                
                
                ?>
                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.transaction',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date"
                               class="form-control date-picker"
                               value="{{date("Y-m-d")}}"
                               required id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="amount"
                               class="form-control"
                               value="{{$balance}}"
                               required id="amount">
                    </div>
                </div>
                <div class="form-group">
                    <label for="payment_type_id"
                           class="control-label col-md-2">{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="payment_type_id" class="form-control select2"
                                id="payment_type_id" required>
                            <option></option>
                            @foreach(\App\Models\PaymentType::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="payment_apply_to"
                           class="control-label col-md-2">{{trans_choice('general.payment',1)}} {{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="payment_apply_to" class="form-control select2"
                                id="payment_apply_to" required>
                            <option value="">--select--</option>
                            <option value="full_payment">Full Payment</option>
                            <option value="part_payment">Part Payment</option>
                            <option value="#reschedule_loan_modal">Reloan Payment</option>
                
                            <option value="#recovery_modal">Debt Recovery Payment</option>
                         
                        </select>
                    </div>
                </div>
 
                <div class="form-group">
                    <label for="approved_amount"
                           class="control-label col-md-2">{{trans_choice('general.show',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</label>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" data-toggle="collapse"
                                data-target="#show_payment_details">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div id="show_payment_details" class="collapse">
                    <div class="form-group">
                        <label for="account_number"
                               class="control-label col-md-2">{{trans_choice('general.account',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="account_number"
                                   class="form-control"
                                   value=""
                                   id="account_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cheque_number"
                               class="control-label col-md-2">{{trans_choice('general.cheque',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="cheque_number"
                                   class="form-control"
                                   value=""
                                   id="cheque_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="routing_code"
                               class="control-label col-md-2">{{trans_choice('general.routing_code',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="routing_code"
                                   class="form-control"
                                   value=""
                                   id="routing_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receipt_number"
                               class="control-label col-md-2">Voucher
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="receipt_number"
                                   class="form-control"
                                   value=""
                                   id="receipt_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank"
                               class="control-label col-md-2">{{trans_choice('general.bank',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="bank"
                                   class="form-control"
                                   value=""
                                   id="bank">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-3">
                                                     <textarea name="notes" class="form-control"
                                                               id="notes"
                                                               rows="3">{{old('notes')}}</textarea>
                    </div>
                </div>







              
                    @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                        @foreach(\App\Models\CustomField::where('category','repayments')->get() as $key)
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label col-md-2">{{$key->name}}</label>
                                <div class="col-md-8">
                                    @if($key->field_type=="number")
                                        <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required @endif>
                                    @endif
                                    @if($key->field_type=="textfield")
                                        <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required @endif>
                                    @endif
                                    @if($key->field_type=="date")
                                        <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required @endif>
                                    @endif
                                    @if($key->field_type=="textarea")
                                        <textarea class="form-control" name="custom_field_{{$key->id}}"
                                                  @if($key->required==1) required @endif></textarea>
                                    @endif
                                    @if($key->field_type=="decimal")
                                        <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required @endif>
                                    @endif
                                    @if($key->field_type=="select")
                                        <select class="form-control touchspin" name="custom_field_{{$key->id}}"
                                                @if($key->required==1) required @endif>
                                            @if($key->required!=1)
                                                <option value=""></option>
                                            @else
                                                <option value="" disabled selected>Select...</option>
                                            @endif
                                            @foreach(explode(',',$key->select_values) as $v)
                                                <option>{{$v}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @if($key->field_type=="radiobox")
                                        @foreach(explode(',',$key->radio_box_values) as $v)
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}" value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($key->field_type=="checkbox")
                                        @foreach(explode(',',$key->checkbox_values) as $v)
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]" id="{{$key->id}}"
                                                           value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
            
                    <button type="submit" class="btn btn-primary pull-right"
                    id='paymentForm'>{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
    <div id="reschedule_loan_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title">Reloan Options</h4>
      </div>
      <div class="modal-body info">
      <form method="post" id="log"
                                                              action="{{ url('loan/'.$loan->id.'/reschedule_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">

                                                            <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.date',1) }}
                                                                    </label>
                                                                    <input type="text" name="submitte_on_date"
                                                               class="form-control date-picker"
                                                               value="{{date("Y-m-d")}}"
                                                               required id="rescheduled_on_date">

                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.total',1) }} {{ trans_choice('general.outstanding',1) }}  
                                                                    </label>
                                                                    <input type="text" name="outstanding"
                                                               class="form-control "
                                                               value="{{$balance}}"
                                                               required id="outstanding" >

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{trans_choice('general.amount',1) }} {{ trans_choice('general.paid',1) }}  
                                                                    </label>
                                                                    <input type="NUMBER" name="paid"
                                                               class="form-control"
                                                               value="" 
                                                               max="{{$payment_amount}}"
                                                               required id="paid" onkeyup="sum();">

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                         {{ trans_choice('general.balance',1) }} 
                                                                    </label>
                                                                    <input type="text" name="balance"
                                                               class="form-control "
                                                               value="{{$balance}}" readonly
                                                               required id="balance"  onkeyup="sum();">

                                                                </div>




                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                         {{ trans_choice('general.interest',1) }}  {{ trans_choice('general.rate',1) }} %
                                                                    </label>
                                                                    <input type="text" name="interest_rate"
                                                               class="form-control "
                                                               value="40"
                                                               required id="interest_rate" onkeyup="sum();" readonly>

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        Adjusted {{ trans_choice('general.interest',1) }} 
                                                                    </label>
                                                                    <input type="text" name="interest"
                                                               class="form-control "
                                                               value=""
                                                               required id="interest" readonly>

                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.next',1) }} {{ trans_choice('general.repayment',1) }} 
                                                                    </label>
                                                                    <input type="text" name="next_repayment"
                                                               class="form-control"
                                                               value="{{$repayment_date}}"
                                                               required id="rescheduled_on_date" readonly>

                                                                </div>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('general.close',1) }} 
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary"  id='paymentForm1'>{{ trans_choice('general.save',1) }}</button>
                                                            </div>
       </form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<!-- Recovery Case Modal -->
<div id="recovery_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><i class="fa fa-money"></i> Debt Recovery Payment</h4>
      </div>
      <div class="modal-body info">
        <form method="post" id="recoveryForm"
              action="{{ url('loan/'.$loan->id.'/repayment/store') }}">
            {{csrf_field()}}
            <input type="hidden" name="payment_apply_to" value="debt_recovery">
            
            <!-- Recovery Case Selection -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Recovery Case <span class="text-danger">*</span></label>
                        <select name="recovery_case_id" id="recovery_case_id" class="form-control select2" required>
                            <option selected value="">— Select Recovery Case —</option>
                            
                            @if(isset($recoveryCases))
                                @foreach($recoveryCases as $index => $case)
                                    <option value="{{ $case->id }}" 
                                        {{ $index === 0 ? 'selected' : '' }}
                                        data-case-number="{{ $case->case_number }}"
                                        data-outstanding="{{ $case->loan_outstanding_amount }}"
                                        data-amount-recovered="{{ $case->amount_recovered }}"
                                        data-recoveries-dept-pct="{{ $case->recoveries_dept_attribution_pct }}"
                                        data-origin-branch-pct="{{ $case->origin_branch_attribution_pct }}"
                                        data-supporting-branch-pct="{{ $case->supporting_branch_attribution_pct }}"
                                        data-origin-branch-id="{{ $case->origin_branch_id }}"
                                        data-supporting-branch-id="{{ $case->supporting_branch_id }}"
                                        data-specialist-id="{{ $case->assigned_specialist_id }}"
                                        data-client-name="{{ $case->client->first_name ?? '' }} {{ $case->client->last_name ?? '' }}">
                                        {{ $case->case_number }} - {{ $case->client->first_name ?? '' }} {{ $case->client->last_name ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Case Info Panel -->
            <div id="caseInfoPanel" class="alert alert-info" style="display:none;">
                <div class="row">
                    <div class="col-md-4">
                        <strong><i class="fa fa-user"></i> Client:</strong> <span id="displayClientName">-</span>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fa fa-money"></i> Outstanding Amount:</strong> <span id="displayOutstanding" class="text-danger">-</span>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fa fa-check-circle"></i> Amount Recovered:</strong> <span id="displayAmountRecovered" class="text-success">-</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Receipt Number</label>
                        <input type="text" name="receipt_number" id="recovery_receipt_number" class="form-control" placeholder="Auto-generated if empty">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Payment Date</label>
                        <input type="text" name="date" id="payment_date" class="form-control date-picker" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="recovery_amount" class="form-control" step="0.01" required placeholder="Enter recovery amount">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Payment Method</label>
                        <select name="payment_type_id" id="payment_method" class="form-control select2">
                            <option value="">— Select Payment Method —</option>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="payroll_deduction">Payroll Deduction</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row" id="referenceRow">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" id="paymentReferenceLabel">Payment Reference</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Cheque number, transaction ID, etc.">
                    </div>
                </div>
                <div class="col-md-6" id="bankRow" style="display:none;">
                    <div class="form-group">
                        <label class="control-label">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter bank name">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Is Settlement?</label>
                        <select name="is_settlement" id="is_settlement" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Yes - Full Settlement</option>
                        </select>
                        <small class="text-muted" id="settlementHint">Select "Yes" if this payment fully settles the debt</small>
                    </div>
                </div>
            </div>
            
            <!-- Attribution Summary Panel -->
            <div id="attributionPanel" class="panel panel-default" style="display:none;">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-calculator"></i> Attribution Breakdown</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Outstanding Before</label>
                                <input type="hidden" name="outstanding_before" id="outstanding_before" value="0">
                                <p class="form-control-static text-danger" id="displayOutstandingBefore">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Outstanding After</label>
                                <input type="hidden" name="outstanding_after" id="outstanding_after" value="0">
                                <p class="form-control-static text-success" id="displayOutstandingAfter">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-building"></i> Recoveries Dept <span id="recoveriesDeptPctLabel">(0%)</span></label>
                                <input type="hidden" name="recoveries_dept_amount" id="recoveries_dept_amount" value="0">
                                <p class="form-control-static text-primary" id="displayRecoveriesDeptAmount">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-home"></i> Origin Branch <span id="originBranchPctLabel">(0%)</span></label>
                                <input type="hidden" name="origin_branch_amount" id="origin_branch_amount" value="0">
                                <p class="form-control-static text-info" id="displayOriginBranchAmount">0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-9">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-handshake-o"></i> Supporting Branch <span id="supportingBranchPctLabel">(0%)</span></label>
                                <input type="hidden" name="supporting_branch_amount" id="supporting_branch_amount" value="0">
                                <p class="form-control-static text-warning" id="displaySupportingBranchAmount">0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Notes</label>
                        <textarea name="notes" id="recovery_notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default pull-left"
                        data-dismiss="modal">
                    {{ trans_choice('general.close',1) }} 
                </button>
                <button type="submit"
                        class="btn btn-primary"  id='recoverySubmitBtn'>{{ trans_choice('general.save',1) }}</button>
            </div>
        </form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
@endsection
@section('footer-scripts')

<script>


    $(".form-horizontal").validate();
		function sum() {
			  var txtFirstNumberValue = document.getElementById('outstanding').value;
        var txtSecondNumberValue = document.getElementById('paid').value;
    var inputFirstNumberValue = document.getElementById('balance').value;
    var inputSecondNumberValue = document.getElementById('interest_rate').value;                   
    var outputs = (txtFirstNumberValue -  txtSecondNumberValue) * 0.4;
    if (!isNaN(outputs)) {
        document.getElementById('interest').value = outputs;
    }
    
    var result = parseInt(txtFirstNumberValue) - parseInt(txtSecondNumberValue);
            if (!isNaN(result)) {
                document.getElementById('balance').value = result;
            }
    
}


$('#paymentForm').click(function(event){
        event.preventDefault();
        swal({
            title: "Are you sure you want to add this transaction?",
            text: "Double check the transaction to make sure it's correct.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            buttons: ["Cancel","Yes!"],
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes I'm sure!"
        }).then((willDelete) => {
            if (willDelete) {
                $('#form1').submit();
            }
        });
    });


    $('#paymentForm1').click(function(event){
        event.preventDefault();
        swal({
            title: "Are you sure you want to add this transaction?",
            text: "Double check the transaction to make sure it's correct.",
            icon: "warning",
            type: "warning",
            showCancelButton: true,
            buttons: ["Cancel","Yes!"],
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes I'm sure!"
        }).then((willDelete) => {
            if (willDelete) {
                $('#log').submit();
            }
        });
    });






$("#payment_apply_to").on("change", function() {
   var sOptionVal = $(this).val();
   if (/modal/i.test(sOptionVal)) {
     var $selectedOption = $(sOptionVal);
     $selectedOption.modal('show');
   }
 });

// Recovery Case Auto-fill Logic
$('#recovery_case_id').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var outstanding = parseFloat(selectedOption.data('outstanding')) || 0;
    var amountRecovered = parseFloat(selectedOption.data('amount-recovered')) || 0;
    var clientName = selectedOption.data('client-name') || '-';
    var recoveriesDeptPct = parseFloat(selectedOption.data('recoveries-dept-pct')) || 0;
    var originBranchPct = parseFloat(selectedOption.data('origin-branch-pct')) || 0;
    var supportingBranchPct = parseFloat(selectedOption.data('supporting-branch-pct')) || 0;
    
    // Show/hide panels
    if ($(this).val()) {
        $('#caseInfoPanel').show();
        $('#attributionPanel').show();
    } else {
        $('#caseInfoPanel').hide();
        $('#attributionPanel').hide();
    }
    
    // Update case info display
    $('#displayClientName').text(clientName);
    $('#displayOutstanding').text(outstanding.toFixed(2));
    $('#displayAmountRecovered').text(amountRecovered.toFixed(2));
    $('#displayOutstandingBefore').text(outstanding.toFixed(2));
    
    // Update percentage labels
    $('#recoveriesDeptPctLabel').text('(' + recoveriesDeptPct + '%)');
    $('#originBranchPctLabel').text('(' + originBranchPct + '%)');
    $('#supportingBranchPctLabel').text('(' + supportingBranchPct + '%)');
    
    // Set outstanding before
    $('#outstanding_before').val(outstanding);
    
    // Reset amount and calculate outstanding after when amount changes
    $('#recovery_amount').val('');
    $('#outstanding_after').val(outstanding);
    $('#displayOutstandingAfter').text(outstanding.toFixed(2));
    
    // Reset attribution amounts
    $('#recoveries_dept_amount').val(0);
    $('#origin_branch_amount').val(0);
    $('#supporting_branch_amount').val(0);
    $('#displayRecoveriesDeptAmount').text('0.00');
    $('#displayOriginBranchAmount').text('0.00');
    $('#displaySupportingBranchAmount').text('0.00');
    
    // Reset settlement dropdown
    $('#is_settlement').val('0');
    $('#settlementHint').text('Select "Yes" if this payment fully settles the debt');
});

// Calculate attribution amounts when amount changes
$('#recovery_amount').on('input', function() {
    var amount = parseFloat($(this).val()) || 0;
    var outstanding = parseFloat($('#outstanding_before').val()) || 0;
    var recoveriesDeptPct = parseFloat($('#recovery_case_id option:selected').data('recoveries-dept-pct')) || 0;
    var originBranchPct = parseFloat($('#recovery_case_id option:selected').data('origin-branch-pct')) || 0;
    var supportingBranchPct = parseFloat($('#recovery_case_id option:selected').data('supporting-branch-pct')) || 0;
    
    // Calculate outstanding after
    var outstandingAfter = Math.max(0, outstanding - amount);
    $('#outstanding_after').val(outstandingAfter);
    $('#displayOutstandingAfter').text(outstandingAfter.toFixed(2));
    
    // Auto-detect settlement: if outstanding after is 0 or amount >= outstanding, set settlement to Yes
    if (outstandingAfter <= 0 && amount > 0) {
        $('#is_settlement').val('1');
        $('#settlementHint').text('<span class="text-success">✓ Auto-detected: This payment will fully settle the debt</span>');
    } else if (amount >= outstanding && outstanding > 0) {
        $('#is_settlement').val('1');
        $('#settlementHint').text('<span class="text-success">✓ Auto-detected: This payment will fully settle the debt</span>');
    } else {
        $('#is_settlement').val('0');
        $('#settlementHint').text('Select "Yes" if this payment fully settles the debt');
    }
    
    // Calculate attribution amounts
    var recoveriesDeptAmount = (amount * recoveriesDeptPct / 100);
    var originBranchAmount = (amount * originBranchPct / 100);
    var supportingBranchAmount = (amount * supportingBranchPct / 100);
    
    $('#recoveries_dept_amount').val(recoveriesDeptAmount);
    $('#origin_branch_amount').val(originBranchAmount);
    $('#supporting_branch_amount').val(supportingBranchAmount);
    
    $('#displayRecoveriesDeptAmount').text(recoveriesDeptAmount.toFixed(2));
    $('#displayOriginBranchAmount').text(originBranchAmount.toFixed(2));
    $('#displaySupportingBranchAmount').text(supportingBranchAmount.toFixed(2));
});

// Handle Payment Method change - update reference label and show/hide bank field
$('#payment_method').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var paymentType = selectedOption.data('type') || '';
    paymentType = paymentType.toLowerCase();
    
    // Reset bank field
    $('#bankRow').hide();
    $('#bank_name').val('');
    
    // Update reference label based on payment method
    if (paymentType.indexOf('cash') !== -1) {
        $('#paymentReferenceLabel').text('Receiver Name');
        $('#payment_reference').attr('placeholder', 'Enter name of person receiving the payment');
    } else if (paymentType.indexOf('bank') !== -1) {
        $('#paymentReferenceLabel').text('Bank Payment Reference');
        $('#payment_reference').attr('placeholder', 'Enter cheque number, bank transaction ID, etc.');
        $('#bankRow').show();
    } else if (paymentType.indexOf('mobile') !== -1 || paymentType.indexOf('money') !== -1) {
        $('#paymentReferenceLabel').text('Mobile Money Transaction (Txn#) Reference');
        $('#payment_reference').attr('placeholder', 'Enter mobile money transaction number');
    } else {
        $('#paymentReferenceLabel').text('Payment Reference');
        $('#payment_reference').attr('placeholder', 'Cheque number, transaction ID, etc.');
    }
    
    // Also populate main form payment type
    $('#payment_type_id').val($(this).val()).trigger('change');
});

// When recovery amount changes, also update main form amount
$('#recovery_amount').on('input', function() {
    var amount = $(this).val();
    $('#amount').val(amount);
});

// When recovery notes changes, also update main form notes
$('#recovery_notes').on('input', function() {
    var notes = $(this).val();
    $('#notes').val(notes);
});

// When recovery payment date changes, also update main form date
$('#payment_date').on('change', function() {
    var paymentDate = $(this).val();
    $('#date').val(paymentDate);
});

// Handle Is Settlement change (manual override)
$('#is_settlement').on('change', function() {
    if ($(this).val() == '1') {
        // If settlement, set outstanding after to 0
        var outstanding = parseFloat($('#outstanding_before').val()) || 0;
        var amount = parseFloat($('#recovery_amount').val()) || 0;
        if (amount >= outstanding) {
            $('#outstanding_after').val(0);
            $('#displayOutstandingAfter').text('0.00');
        }
    }
});

// Recovery form submission
$('#recoverySubmitBtn').click(function(event){
    event.preventDefault();
    swal({
        title: "Are you sure you want to record this recovery payment?",
        text: "This will update the recovery case and loan balance.",
        icon: "warning",
        type: "warning",
        showCancelButton: true,
        buttons: ["Cancel","Yes!"],
        confirmButtonColor: 'green',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes I'm sure!"
    }).then((willDelete) => {
        if (willDelete) {
            $('#recoveryForm').submit();
        }
    });
});
    </script>
@endsection
