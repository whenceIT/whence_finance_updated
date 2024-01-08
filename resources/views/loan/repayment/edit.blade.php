@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.transaction',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.transaction',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()"  class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/repayment/'.$loan_transaction->id.'/update')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.transaction',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date"
                               class="form-control date-picker"
                               value="{{$loan_transaction->date}}"
                               required id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="amount"
                               class="form-control"
                               value="{{$loan_transaction->credit}}"
                               required id="amount">
                    </div>
                </div>
                <div class="form-group">
                    <label for="payment_apply_to"
                           class="control-label col-md-2">{{trans_choice('general.payment',1)}} {{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="payment_apply_to" class="form-control select2"
                                id="payment_apply_to" required>
                            <option value="regular" @if($loan_transaction->payment_apply_to=="regular") selected @endif>{{trans_choice('general.regular',1)}}</option>
                            <option value="interest" @if($loan_transaction->payment_apply_to=="interest") selected @endif>{{trans_choice('general.interest',1)}}</option>
                            <option value="fees" @if($loan_transaction->payment_apply_to=="fees") selected @endif>{{trans_choice('general.fee',2)}}</option>
                            <option value="penalty" @if($loan_transaction->payment_apply_to=="penalty") selected @endif>{{trans_choice('general.penalty',1)}}</option>
                            <option value="principal" @if($loan_transaction->payment_apply_to=="principal") selected @endif>{{trans_choice('general.principal',1)}}</option>
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
                                <option value="{{$key->id}}" @if($loan_transaction->payment_detail->payment_type_id==$key->id) selected @endif>{{$key->name}}</option>
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
                <div id="show_payment_details" class="collapse">
                    <div class="form-group">
                        <label for="account_number"
                               class="control-label col-md-2">{{trans_choice('general.account',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="account_number"
                                   class="form-control"
                                   value="{{$loan_transaction->payment_detail->account_number}}"
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
                                   value="{{$loan_transaction->payment_detail->cheque_number}}"
                                   id="cheque_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="routing_code"
                               class="control-label col-md-2">{{trans_choice('general.routing_code',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="routing_code"
                                   class="form-control"
                                   value="{{$loan_transaction->payment_detail->routing_code}}"
                                   id="routing_code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receipt_number"
                               class="control-label col-md-2">{{trans_choice('general.receipt',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="receipt_number"
                                   class="form-control"
                                   value="{{$loan_transaction->payment_detail->receipt_number}}"
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
                                   value="{{$loan_transaction->payment_detail->bank}}"
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
                                                               rows="3">{{$loan_transaction->notes}}</textarea>
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
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif</textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
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
                                            @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()))
                                                @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name==$v)
                                                    <option selected>{{$v}}</option>
                                                @else
                                                    <option>{{$v}}</option>
                                                @endif
                                            @else
                                                <option>{{$v}}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()))
                                                    @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name==$v)
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif checked>
                                                    @else
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                @else
                                                    <input type="radio" name="custom_field_{{$key->id}}"
                                                           id="{{$key->id}}" value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                @endif

                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
                                    @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()))
                                        <?php $c = unserialize(\App\Models\CustomFieldMeta::where('custom_field_id',
                                            $key->id)->where('parent_id', $loan_transaction->id)->where('category',
                                            'repayments')->first()->name); ?>

                                        @foreach(explode(',',$key->checkbox_values) as $v)
                                            <div class="checkbox">
                                                <label>
                                                    @if(array_key_exists($v,$c))
                                                        @if($c[$v]==$v)
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif checked>
                                                        @else
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif>
                                                        @endif
                                                    @else
                                                        <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                               id="{{$key->id}}"
                                                               value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
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
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
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