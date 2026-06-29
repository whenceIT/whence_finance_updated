@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}
@endsection

@section('content')

@if($cashBalance < 20000)

<div class="box box-danger">
    <div class="box-body text-center" style="padding: 30px 20px;">
        <i class="fa fa-exclamation-triangle text-danger"
           style="font-size: 48px; margin-bottom: 15px;"></i>

        <h4 style="font-weight: bold; margin-bottom: 10px;">
            Insufficient Cash Balance
        </h4>

        <p style="font-size: 16px; color: #555; margin-bottom: 0;">
            Your cash balance is below the required minimum of
            <strong>K20,000</strong>. You can only add essential expenses.
        </p>
    </div>
</div>

 @include('components.ledger_blocker')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}</h3>
            {{-- Warning for Deposits --}}

        </div>

        <div style="
    margin: 15px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 10px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
">
    <div>
        <div style="font-size: 13px; opacity: 0.9;">
            Current Wallet Balance
        </div>
        <div style="font-size: 28px; font-weight: bold; margin-top: 5px;">
            K{{ number_format($cashBalance, 2) }}
        </div>
    </div>

    <div style="
        background: rgba(255,255,255,0.2);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
    ">
        💰
    </div>
</div>


        <form method="post" action="{{url('expense/store')}}" class="form-horizontal" enctype="multipart/form-data" id="expenseForm">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach($offices as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                 <input type="hidden" name="total_deducted" id="hidden_total_deducted">
                    <input type="hidden" name="user_id" id="hidden_user_id">
                    <input type="hidden" name="hidden_operator" id="hidden_operator">
                <div class="form-group">
                    <label for="expense_type_id"
                           class="control-label col-md-2">{{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="expense_type_id" class="form-control select2" id="expense_type_id"
                                required>
                            <option></option>
                            @foreach(\App\Models\NonEssentialExpenseTypes::all() as $key)
                                <option value="{{$key->id}}">
                                    {{$key->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_id"
                           class="control-label col-md-2">GL Account</label>
                    <div class="col-md-3">
                        <select name="gl_account_id" class="form-control select2" id="gl_account_id" 
                                >
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date('Y-m-d')}}"
                               id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="amount" class="form-control"
                               value="{{old('amount')}}" required
                               id="amount">
                    </div>
		</div>
        <div class="form-group">
    <label class="control-label col-md-2">Payment Type</label>
    <div class="col-md-3">
        <select name="payment_type"
                id="payment_type"
                class="form-control">
            <option value="mobile_money" selected>
                Mobile Money
            </option>
            <option value="bank">
                Bank Account
            </option>
        </select>
    </div>
</div>


<div id="bankSection" style="display:none;">

    <div class="form-group">
        <label class="control-label col-md-2">
            Bank
        </label>

        <div class="col-md-3">
            <select id="bank_id"
                    name="bank_id"
                    class="form-control">
                <option value="">
                    Loading Banks...
                </option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-2">
            Account Number
        </label>

        <div class="col-md-3">
            <input type="text"
                   id="account_number"
                   name="account_number"
                   class="form-control">
        </div>
    </div>

</div>

<div id="mobileMoneySection">
  <div class="form-group">
          <label for="phone"
                           class="control-label col-md-2">Recipient Phone</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control"
                               value="{{old('phone')}}"
                               id="phone" required>
                    </div>
                    </div>

</div>

        <div class="form-group">
    <div class="col-md-offset-2 col-md-8">
        <div id="duplicate-warning" class="alert alert-danger" style="display:none;">
        </div>
    </div>
</div>


                <div class="form-group">
                    <label for="recurring"
                           class="control-label col-md-2">{{trans_choice('general.recurring',1)}}</label>
                    <div class="col-md-3">
                        <select name="recurring" class="form-control select2" id="recurring"
                                required>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="recur">
                    <div class="form-group">
                        <label for="recur_frequency"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.frequency',1)}}</label>
                        <div class="col-md-3">
                            <input type="number" name="recur_frequency" class="form-control"
                                   value="{{old('recur_frequency')}}"
                                   id="recur_frequency">
                        </div>
                        <label for="recur_type"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.type',1)}}</label>
                        <div class="col-md-3">
                            <select name="recur_type" class="form-control select2" id="recur_type">
                                <option value="day">{{trans_choice('general.day',1)}}</option>
                                <option value="week">{{trans_choice('general.week',1)}}</option>
                                <option value="month">{{trans_choice('general.month',1)}}</option>
                                <option value="year">{{trans_choice('general.year',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recur_start_date"
                               class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_start_date" class="form-control date-picker"
                                   value=""
                                   id="recur_start_date">
                        </div>
                        <label for="recur_end_date"
                               class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_end_date" class="form-control date-picker"
                                   value=""
                                   id="recur_end_date">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">Narration</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3" required></textarea>
                    </div>
		</div>
<!-- <div class="form-group">
    <label for="proof_of_payment" class="control-label col-md-2">
        {{ __('Proof of Payment') }}
        <span class="text-danger">*</span>
    </label>
    <div class="col-md-3">
        <input type="file"
               name="proof_of_payment"
               class="form-control-file"
               required>
    </div>
</div> -->
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','expenses')->get() as $key)
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
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}"
                                                       value="{{$v}}"
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
                                                <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                       id="{{$key->id}}"
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

            <div class="box-footer">
               <button type="button"
         id="previewChargesBtn"
        class="btn btn-primary pull-right">
    {{trans_choice('general.save',1)}}
</button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="expensePreviewModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">
                    Confirm Expense Information
                </h4>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <tbody>

                    <tr>
    <th>Payment Type</th>
    <td id="previewPaymentType"></td>
</tr>

<tr id="bankNameRow" style="display:none;">
    <th>Bank</th>
    <td id="previewBankName"></td>
</tr>

<tr id="accountNumberRow" style="display:none;">
    <th>Account Number</th>
    <td id="previewAccountNumber"></td>
</tr>

                    <tr>
                        <th>Account Name</th>
                        <td id="accountName"></td>
                    </tr>

                    <tr>
                        <th>Phone Number</th>
                        <td id="resolvedPhone"></td>
                    </tr>

                    <tr>
                        <th>Operator</th>
                        <td id="resolvedOperator"></td>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <td id="withdrawalAmount"></td>
                    </tr>

                    <tr>
                        <th>Gateway Fee</th>
                        <td id="gatewayFee"></td>
                    </tr>

                    <tr>
                        <th>Withinhere Fee</th>
                        <td id="withinhereFee"></td>
                    </tr>

                    <tr>
                        <th>Total Charge</th>
                        <td id="totalCharge"></td>
                    </tr>

                    <tr>
                        <th>Total Deducted</th>
                        <td id="totalDeducted"></td>
                    </tr>

                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="confirmExpenseBtn"
                        class="btn btn-success">
                    Confirm & Save
                </button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="passwordConfirmModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">
                    Confirm Password
                </h4>
            </div>

            <div class="modal-body">

                <div class="alert alert-danger"
                     id="passwordError"
                     style="display:none;">
                    Incorrect password.
                </div>

                <div class="form-group">
                    <label>Enter your password to continue</label>

                    <input
                        type="password"
                        id="confirmPassword"
                        class="form-control"
                        autocomplete="current-password">
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        class="btn btn-success"
                        id="verifyPasswordBtn">
                    Verify & Save
                </button>

            </div>

        </div>
    </div>
</div>


@else



    @include('components.ledger_blocker')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}</h3>
            {{-- Warning for Deposits --}}

        </div>

                <div style="
    margin: 15px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 10px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
">
    <div>
        <div style="font-size: 13px; opacity: 0.9;">
            Current Wallet Balance
        </div>
        <div style="font-size: 28px; font-weight: bold; margin-top: 5px;">
            K{{ number_format($cashBalance, 2) }}
        </div>
    </div>

    <div style="
        background: rgba(255,255,255,0.2);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
    ">
        💰
    </div>
</div>
        <form method="post" action="{{url('expense/store')}}" class="form-horizontal" enctype="multipart/form-data" id="expenseForm">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach($offices as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                    <input type="hidden" name="total_deducted" id="hidden_total_deducted">
                    <input type="hidden" name="user_id" id="hidden_user_id">
                    <input type="hidden" name="hidden_operator" id="hidden_operator">
                <div class="form-group">
                    <label for="expense_type_id"
                           class="control-label col-md-2">{{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="expense_type_id" class="form-control select2" id="expense_type_id"
                                required>
                            <option></option>
                            @foreach(\App\Models\ExpenseType::all() as $key)
                                <option value="{{$key->id}}">
                                    {{$key->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_id"
                           class="control-label col-md-2">GL Account</label>
                    <div class="col-md-3">
                        <select name="gl_account_id" class="form-control select2" id="gl_account_id" 
                                >
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date('Y-m-d')}}"
                               id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="amount" class="form-control"
                               value="{{old('amount')}}" required
                               id="amount">
                    </div>
		</div>

        <div class="form-group">
    <label class="control-label col-md-2">Payment Type</label>
    <div class="col-md-3">
        <select name="payment_type"
                id="payment_type"
                class="form-control">
            <option value="mobile_money" selected>
                Mobile Money
            </option>
            <option value="bank">
                Bank Account
            </option>
        </select>
    </div>
</div>

<div id="bankSection" style="display:none;">

    <div class="form-group">
        <label class="control-label col-md-2">
            Bank
        </label>

    <div class="col-md-3">
        <select name="reference_type"
                id="reference_type"
                class="form-control">
            <option value="">Select Reference Type</option>
            <option value="airtel">Airtel Money</option>
            <option value="mtn">MTN Money</option>
            <option value="zanaco_express">Zanaco Xpress</option>
            <option value="zanaco_cash">Zanaco Cash Deposit</option>
            <option value="access">Access Bank</option>
            <option value="withinhere">Within Here</option>
        </select>
    </div>
</div>

    <div class="form-group">
        <label class="control-label col-md-2">
            Account Number
        </label>

        <div class="col-md-3">
            <input type="text"
                   id="account_number"
                   name="account_number"
                   class="form-control">
        </div>
    </div>

</div>

<div id="mobileMoneySection">
          <div class="form-group">
          <label for="phone"
                           class="control-label col-md-2">Recipient Phone</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control"
                               value="{{old('phone')}}"
                               id="phone" required>
                    </div>
                    </div>
</div>

        <div class="form-group">
    <div class="col-md-offset-2 col-md-8">
        <div id="duplicate-warning" class="alert alert-danger" style="display:none;">
        </div>
    </div>
</div>


                <div class="form-group">
                    <label for="recurring"
                           class="control-label col-md-2">{{trans_choice('general.recurring',1)}}</label>
                    <div class="col-md-3">
                        <select name="recurring" class="form-control select2" id="recurring"
                                required>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="recur">
                    <div class="form-group">
                        <label for="recur_frequency"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.frequency',1)}}</label>
                        <div class="col-md-3">
                            <input type="number" name="recur_frequency" class="form-control"
                                   value="{{old('recur_frequency')}}"
                                   id="recur_frequency">
                        </div>
                        <label for="recur_type"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.type',1)}}</label>
                        <div class="col-md-3">
                            <select name="recur_type" class="form-control select2" id="recur_type">
                                <option value="day">{{trans_choice('general.day',1)}}</option>
                                <option value="week">{{trans_choice('general.week',1)}}</option>
                                <option value="month">{{trans_choice('general.month',1)}}</option>
                                <option value="year">{{trans_choice('general.year',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recur_start_date"
                               class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_start_date" class="form-control date-picker"
                                   value=""
                                   id="recur_start_date">
                        </div>
                        <label for="recur_end_date"
                               class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_end_date" class="form-control date-picker"
                                   value=""
                                   id="recur_end_date">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">Narration</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3" required></textarea>
                    </div>
		</div>
<!-- <div class="form-group">
    <label for="proof_of_payment" class="control-label col-md-2">
        {{ __('Proof of Payment') }}
        <span class="text-danger">*</span>
    </label>
    <div class="col-md-3">
        <input type="file"
               name="proof_of_payment"
               class="form-control-file"
               required>
    </div>
</div> -->
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','expenses')->get() as $key)
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
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}"
                                                       value="{{$v}}"
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
                                                <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                       id="{{$key->id}}"
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

            <div class="box-footer">
                             <button type="button"
         id="previewChargesBtn"
        class="btn btn-primary pull-right">
    {{trans_choice('general.save',1)}}
</button>
            </div>
        </form>
    </div>


        <div class="modal fade" id="expensePreviewModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">
                    Confirm Expense Information
                </h4>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <tbody>


                    <tr>
    <th>Payment Type</th>
    <td id="previewPaymentType"></td>
</tr>

<tr id="bankNameRow" style="display:none;">
    <th>Bank</th>
    <td id="previewBankName"></td>
</tr>

<tr id="accountNumberRow" style="display:none;">
    <th>Account Number</th>
    <td id="previewAccountNumber"></td>
</tr>

                    <tr>
                        <th>Account Name</th>
                        <td id="accountName"></td>
                    </tr>

                    <tr>
                        <th>Phone Number</th>
                        <td id="resolvedPhone"></td>
                    </tr>

                    <tr>
                        <th>Operator</th>
                        <td id="resolvedOperator"></td>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <td id="withdrawalAmount"></td>
                    </tr>

                    <tr>
                        <th>Gateway Fee</th>
                        <td id="gatewayFee"></td>
                    </tr>

                    <tr>
                        <th>Withinhere Fee</th>
                        <td id="withinhereFee"></td>
                    </tr>

                    <tr>
                        <th>Total Charge</th>
                        <td id="totalCharge"></td>
                    </tr>

                    <tr>
                        <th>Total Deducted</th>
                        <td id="totalDeducted"></td>
                    </tr>

                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="confirmExpenseBtn"
                        class="btn btn-success">
                    Confirm & Save
                </button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="passwordConfirmModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">
                    Confirm Password
                </h4>
            </div>

            <div class="modal-body">

                <div class="alert alert-danger"
                     id="passwordError"
                     style="display:none;">
                    Incorrect password.
                </div>

                <div class="form-group">
                    <label>Enter your password to continue</label>

                    <input
                        type="password"
                        id="confirmPassword"
                        class="form-control"
                        autocomplete="current-password">
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        class="btn btn-success"
                        id="verifyPasswordBtn">
                    Verify & Save
                </button>

            </div>

        </div>
    </div>
</div>


    <!-- /.box -->

@endif    
@endsection
@section('footer-scripts')
    <script>


function loadBanks() {

    $.ajax({
        url: 'https://withinheremobileapi.com/api/v1/payment/banks',
        type: 'GET',

        success: function(response) {

            if(response.status){

                let options =
                    '<option value="">Select Bank</option>';

                response.data.forEach(function(bank){

                    options +=
                        '<option value="' + bank.id + '">' +
                        bank.name +
                        '</option>';
                });

                $('#bank_id').html(options);
            }
        }
    });
}


function getOperator(phoneNumber){

    let digit = phoneNumber.charAt(2);

    switch(digit){

        case "7":
            return "airtel";

        case "6":
            return "mtn";

        case "5":
            return "zamtel";

        default:
            return null;
    }
}

$(document).ready(function () {

    loadBanks();

    $('#payment_type').change(function(){

        if($(this).val() === 'bank'){

            $('#mobileMoneySection').hide();
            $('#bankSection').show();

        }else{

            $('#bankSection').hide();
            $('#mobileMoneySection').show();

        }

    });

    $('#previewChargesBtn').on('click', function () {

        let amount = $('#amount').val();
        let paymentType = $('#payment_type').val();

        // Reset bank rows
        $('#bankNameRow').hide();
        $('#accountNumberRow').hide();

        // ==========================
        // MOBILE MONEY FLOW
        // ==========================

        if(paymentType === 'mobile_money') {

            let phone = $('#phone').val();

            let operator = getOperator(phone);

            if(!operator){
                alert('Unable to determine operator from phone number.');
                return;
            }

            $.ajax({
                url: 'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    amount: amount,
                    payout_type: 'withinhere_to_mno'
                }),
                success: function(chargeResponse) {

                    if(!chargeResponse.success){
                        alert('Unable to retrieve withdrawal charges.');
                        return;
                    }

                    $.ajax({
                        url: 'https://withinheremobileapi.com/api/v1/payment/resolve/mobile',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            phone: phone,
                            operator: operator
                        }),
                        success: function(resolveResponse){


                              let user_id = "{{ $user_id }}";

                        $('#hidden_total_deducted').val(chargeResponse.data.totalDeducted);
                        $('#hidden_user_id').val(user_id);
                        $('#hidden_operator').val(operator);

                            $('#previewPaymentType').text('Mobile Money');

                            if(resolveResponse.success && resolveResponse.data){

                                $('#accountName').text(
                                    resolveResponse.data.accountName
                                );

                                $('#resolvedPhone').text(
                                    resolveResponse.data.phone
                                );

                                $('#resolvedOperator').text(
                                    resolveResponse.data.operator.toUpperCase()
                                );

                            } else {

                                $('#accountName').text('Not Found');
                                $('#resolvedPhone').text(phone);
                                $('#resolvedOperator').text(operator.toUpperCase());
                            }

                            $('#withdrawalAmount').text(
                                Number(chargeResponse.data.withdrawalAmount).toFixed(2)
                            );

                            $('#gatewayFee').text(
                                Number(chargeResponse.data.gatewayFee).toFixed(2)
                            );

                            $('#withinhereFee').text(
                                Number(chargeResponse.data.withinhereFee).toFixed(2)
                            );

                            $('#totalCharge').text(
                                Number(chargeResponse.data.totalCharge).toFixed(2)
                            );

                            $('#totalDeducted').text(
                                Number(chargeResponse.data.totalDeducted).toFixed(2)
                            );

                           $('#expensePreviewModal').modal('show');
                        },
                        error: function(){

                            $('#accountName').text('Lookup Failed');
                            $('#resolvedPhone').text(phone);
                            $('#resolvedOperator').text(operator.toUpperCase());

                            $('#expensePreviewModal').modal('show');
                        }
                    });
                },
                error: function(xhr) {
                    alert('Failed to retrieve withdrawal charges.');
                    console.log(xhr.responseText);
                }
            });

        }

        // ==========================
        // BANK FLOW
        // ==========================

        else {

            let bankId = $('#bank_id').val();
            let accountNumber = $('#account_number').val();

            if(!bankId){
                alert('Please select a bank.');
                return;
            }

            if(!accountNumber){
                alert('Please enter an account number.');
                return;
            }

            $.ajax({

                url: 'https://withinheremobileapi.com/api/v1/payment/resolve/bank',

                type: 'POST',

                contentType: 'application/json',

                data: JSON.stringify({

                    accountNumber: accountNumber,

                    bankId: bankId

                }),

                success: function(bankResponse){

                    if(!bankResponse.success){
                        alert('Unable to verify bank account.');
                        return;
                    }

                    $.ajax({

                        url: 'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',

                        type: 'POST',

                        contentType: 'application/json',

                        data: JSON.stringify({

                            amount: amount,

                            payout_type: 'withinhere_to_bank'

                        }),

                        success: function(chargeResponse){

                            $('#previewPaymentType').text(
                                'Bank Transfer'
                            );

                            $('#bankNameRow').show();
                            $('#accountNumberRow').show();

                            $('#previewBankName').text(
                                $('#bank_id option:selected').text()
                            );

                            $('#previewAccountNumber').text(
                                accountNumber
                            );

                            $('#accountName').text(
                                bankResponse.data.accountName
                            );

                            $('#resolvedPhone').text('-');
                            $('#resolvedOperator').text('-');

                            $('#withdrawalAmount').text(
                                Number(chargeResponse.data.withdrawalAmount).toFixed(2)
                            );

                            $('#gatewayFee').text(
                                Number(chargeResponse.data.gatewayFee).toFixed(2)
                            );

                            $('#withinhereFee').text(
                                Number(chargeResponse.data.withinhereFee).toFixed(2)
                            );

                            $('#totalCharge').text(
                                Number(chargeResponse.data.totalCharge).toFixed(2)
                            );

                            $('#totalDeducted').text(
                                Number(chargeResponse.data.totalDeducted).toFixed(2)
                            );

                            $('#expensePreviewModal').modal('show');
                        },

                        error: function(){
                            alert('Unable to retrieve bank transfer charges.');
                        }
                    });

                },

                error: function(){
                    alert('Unable to verify bank account.');
                }
            });

        }

    });

$('#confirmExpenseBtn').on('click', function () {

    $('#expensePreviewModal').modal('hide');

    $('#confirmPassword').val('');
    $('#passwordError').hide();

    $('#passwordConfirmModal').modal('show');

});

$('#verifyPasswordBtn').on('click', function () {

    let password = $('#confirmPassword').val();

    if(password === ''){
        alert('Please enter your password.');
        return;
    }

    // Disable button immediately
    $(this)
        .prop('disabled', true)
        .text('Verifying...');

    $.ajax({

        url: "{{ url('loan/verify-password') }}",

        type: "POST",

        data: {
            password: password,
            _token: "{{ csrf_token() }}"
        },

        success: function(response){

            if(response.success){

                $('#passwordConfirmModal').modal('hide');

                $('#expenseForm').submit();

            }else{

                $('#passwordError').show();

                // Re-enable button
                $('#verifyPasswordBtn')
                    .prop('disabled', false)
                    .text('Verify & Save');

            }

        },

        error: function(){

            $('#passwordError').show();

            // Re-enable button
            $('#verifyPasswordBtn')
                .prop('disabled', false)
                .text('Verify & Save');

        }

    });

});

$('#passwordConfirmModal').on('shown.bs.modal', function () {

    $('#verifyPasswordBtn')
        .prop('disabled', false)
        .text('Verify & Save');

    $('#confirmPassword').val('');
    $('#passwordError').hide();

});

});



        $(document).ready(function (e) {
            $(".form-horizontal").validate();

            if ($('#recurring').val() == '1') {
                $('#recur').show();
                $('#recur_frequency').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recur_type').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recur_frequency').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recur_type').removeAttr('required');
            }

            $('#recurring').change(function () {
                if ($('#recurring').val() == '1') {
                    $('#recur').show();
                    $('#recur_frequency').attr('required', 'required');
                    $('#recur_type').attr('required', 'required');
                    $('#recur_start_date').attr('required', 'required');
                } else {
                    $('#recur').hide();
                    $('#recur_frequency').removeAttr('required');
                    $('#recur_start_date').removeAttr('required');
                    $('#recur_type').removeAttr('required');
                }
            });

            // Dynamic GL accounts
            $('#expense_type_id').change(function () {
                var id = $(this).val();
                $('#gl_account_id').empty();
                $('#gl_account_id').append('<option>Loading...</option>');
                $('#gl_account_id').prop('disabled', true);
                if (id) {
                    $.ajax({
                        url: "{{ url('expense/type') }}/" + id + "/get_gl_accounts",
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#gl_account_id').empty();
                            $('#gl_account_id').append('<option></option>');
                            $.each(data, function (key, value) {
                                $('#gl_account_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                            $('#gl_account_id').prop('disabled', false);
                        },
                        error: function() {
                            $('#gl_account_id').empty();
                            $('#gl_account_id').append('<option></option>');
                            $('#gl_account_id').prop('disabled', false);
                        }
                    });
                } else {
                    $('#gl_account_id').empty();
                    $('#gl_account_id').append('<option></option>');
                    $('#gl_account_id').prop('disabled', false);
                }
            });

        $('form').on('submit', function(e) {

        let paymentMethod = $('#reference_type').val();
        let currentReferenceNumber = $('#reference_number').val();
        let valid = true;

switch (paymentMethod) {

    case 'airtel':
        valid = /^[A-Za-z]{2}\d{6}\.\d{4}\.[A-Za-z]\d{5}$/.test(currentReferenceNumber);
        break;

    case 'airtel_app':
        valid = /^[A-Za-z]{5}\d{15}$/.test(currentReferenceNumber);
        break;

    case 'zanaco_express':
        valid = /^\d{12}$/.test(currentReferenceNumber);
        break;

    case 'mtn':
        valid = /^\d{10}$/.test(currentReferenceNumber);
        break;

    case 'zanaco_cash':
        valid = /^\d{16}$/.test(currentReferenceNumber);
        break;

    case 'access':
        valid = /^[A-Za-z]{3}\d{13}$/.test(currentReferenceNumber);
        break;

    case 'withinhere':
        valid = /^\d+$/.test(currentReferenceNumber);
        break;
}

if (paymentMethod && !valid) {
    alert('Invalid Reference Number format.');
    e.preventDefault();
    return false;
}

    if ($('#duplicate-warning').is(':visible')) {

        let proceed = confirm(
            'A similar expense already exists. Are you sure you want to save this expense?'
        );

        if (!proceed) {
            e.preventDefault();
            return false;
        }
    }

   // Only disable button if form is valid
if ($(this).valid()) {

    const $btn = $(this).find('button[type="submit"]');

    $btn.prop('disabled', true)
        .text('Saving...');

} else {
    e.preventDefault();
    return false;
}
});
        });

        function checkDuplicateExpense()
{
    let office_id = $('#office_id').val();
    let expense_type_id = $('#expense_type_id').val();
    let amount = $('#amount').val();
    let date = $('#date').val();

    if (!office_id || !expense_type_id || !amount || !date) {
        return;
    }

    $.ajax({
        url: "{{ url('expense/check-duplicate') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            office_id: office_id,
            expense_type_id: expense_type_id,
            amount: amount,
            date: date
        },
        success: function(response) {

            if (response.length > 0) {

                let html =
                    '<strong>⚠ Possible Duplicate Expense Found</strong><br><br>';

                response.forEach(function(exp) {
                    html +=
                        'Expense #' + exp.id +
                        ' | Amount: ' + exp.amount +
                        ' | Date: ' + exp.date +
                        '<br>';
                });

                $('#duplicate-warning')
                    .html(html)
                    .show();

            } else {

                $('#duplicate-warning')
                    .hide()
                    .html('');
            }
        }
    });
}

$('#office_id,#expense_type_id,#amount,#date').on(
    'change keyup',
    checkDuplicateExpense
);


$('#reference_type').change(function () {

    let hint = $('#reference-format-hint');
    let referenceInput = $('#reference_number');

    referenceInput.val('');

    switch ($(this).val()) {

        case 'airtel':
            hint.text('Format: MP260223.0953.J76581');
            referenceInput.attr('placeholder', 'MP260223.0953.J76581');
            break;

        case 'airtel_app':
            hint.text('Format: APCZM194947529952000');
            referenceInput.attr('placeholder', 'APCZM194947529952000');
            break;

        case 'zanaco_express':
            hint.text('Format: 12 digit number (002504072516)');
            referenceInput.attr('placeholder', '002504072516');
            break;

        case 'mtn':
            hint.text('Format: 10 digit number (8704564481)');
            referenceInput.attr('placeholder', '8704564481');
            break;

        case 'zanaco_cash':
            hint.text('Format: 16 digit number (0502605703255600)');
            referenceInput.attr('placeholder', '0502605703255600');
            break;

        case 'access':
            hint.text('Format: FJB2606341708208');
            referenceInput.attr('placeholder', 'FJB2606341708208');
            break;

        case 'withinhere':
            hint.text('Format: 1777356230718931');
            referenceInput.attr('placeholder', '1777356230718931');
            break;

        default:
            hint.text('Enter Payment Reference Number');
            referenceInput.attr('placeholder', '');
    }
});

    </script>
@endsection

