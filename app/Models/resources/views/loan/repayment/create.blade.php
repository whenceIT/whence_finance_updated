@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.repayment',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/'.$loan->id.'/repayment/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
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
                               value="{{$payment_amount}}"
                               required id="amount">
                    </div>
                </div>
                <div class="form-group">
                        <label for="gl_account_fund_source_id"
                        class="control-label col-md-2">Account to Credit
                     <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-title="an Asset account(typically Bank or cash) that is debited during repayments/payments an credited using disbursals.."></i>
                 </label>
                 <div class="col-md-3">
                     <select name="gl_account_fund_source_id" class="form-control select2"
                             id="gl_account_fund_source_id" required>
                         <option></option>
                         @foreach(\App\Models\GlAccount::where('active',1)->get() as $key)
                             <option value="{{$key->id}}">{{$key->name}}</option>
                         @endforeach
                     </select>
                 </div>





                    <label for="payment_apply_to"
                           class="control-label col-md-2">{{trans_choice('general.payment',1)}} {{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="payment_apply_to" class="form-control select2"
                                id="payment_apply_to" required>
                            <option value="regular">{{trans_choice('general.regular',1)}}</option>
                            <option value="interest">{{trans_choice('general.interest',1)}}</option>
                            <option value="fees">{{trans_choice('general.fee',2)}}</option>
                            <option value="penalty">{{trans_choice('general.penalty',1)}}</option>
                            <option value="principal">{{trans_choice('general.principal',1)}}</option>
                        </select>
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
                    <label for="approved_amount"
                           class="control-label col-md-2">{{trans_choice('general.show',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</label>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary" data-toggle="collapse"
                                data-target="#show_payment_details">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                    <div class="form-group">
                        <label for="cheque_number"
                               class="control-label col-md-2 hidden">{{trans_choice('general.cheque',1)}}
                            #</label>
                        <div class="col-md-3 hidden">
                            <input type="text" name="cheque_number"
                                   class="form-control"
                                   value=""
                                   id="cheque_number">
                        </div>
                    </div>
                   
                    <div class="form-group">
                    <label for="receipt_number" class="control-label col-md-2">
                        {{ trans_choice('general.receipt', 1) }} #
                    </label>
                    <div class="col-md-3">
                    <input type="text" name="receipt_number"
                    class="form-control"
                    value="{{ old('receipt_number', $nextReceipt ?? '') }}"
                    id="receipt_number"
                    style="font-weight: bold; color: #b22222;" readonly> {{-- Dark red like a receipt --}}

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
                    <button type="submit" class="btn btn-primary pull-right">Post Reciept</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(".form-horizontal").validate();
    </script>
@endsection