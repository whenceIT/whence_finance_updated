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
                               value="{{date('Y-m-d')}}"
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

    <?php

$remainingInterest = 0;

$lastInterest = App\Models\LoanTransaction::where('loan_id', $loan->id)
    ->whereIn('transaction_type', ['interest', 'interest_initial']) // adjust column if needed
    ->orderBy('id', 'desc')
    ->first();

if ($lastInterest) {

    $repaymentsAfterInterest = App\Models\LoanTransaction::where('loan_id', $loan->id)
        ->where('transaction_type', 'repayment') // adjust column if needed
        ->where('id', '>', $lastInterest->id)
        ->sum('credit');

    $remainingInterest = max(
        0,
        $lastInterest->debit - $repaymentsAfterInterest
    );
}

?>

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

                                                            <input type="hidden"
       id="remaining_interest"
       value="{{ $remainingInterest }}">
                                                            <div class="modal-body">

                                                            <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.date',1) }}
                                                                    </label>
                                                                    <input type="text" name="submitte_on_date"
                                                               class="form-control date-picker"
                                                               value="{{date('Y-m-d')}}"
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
       min="{{ $remainingInterest }}"
       required
       id="paid"
       onkeyup="sum();">

                                                                </div>

                                                                <small class="text-danger">
    Minimum payment required:
    {{ number_format($remainingInterest, 2) }}
</small>

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


@include('loan.repayment.recovery_modal')
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


function getMinimumInterestAmount() {
    var outstanding = parseFloat($('#outstanding').val()) || 0;
    var rate = parseFloat($('#interest_rate').val()) || 40;

    return outstanding * (rate / (100 + rate));
}

$('#paid').on('input', function () {
    var minInterest = getMinimumInterestAmount();

    $(this).attr('min', minInterest);

    if (parseFloat($(this).val()) < minInterest) {
        this.setCustomValidity(
            'Amount paid cannot be less than the interest amount (' +
            minInterest.toFixed(2) + ')'
        );
    } else {
        this.setCustomValidity('');
    }
});


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

            var paid = parseFloat($('#paid').val()) || 0;
            var minInterest = parseFloat($('#remaining_interest').val()) || 0;

            if (paid < minInterest) {

                swal(
                    "Invalid Amount",
                    "Amount paid cannot be less than the remaining interest of " +
                    minInterest.toFixed(2),
                    "error"
                );

                return false;
            }

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
