@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.journal',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.journal',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('accounting/journal/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">

                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="currency_id"
                           class="control-label col-md-2">{{trans_choice('general.currency',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="currency_id" class="form-control select2" id="currency_id" required>
                            <option></option>
                            @foreach(\App\Models\Currency::where('active',1)->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="amount" class="form-control"
                               value="{{old('amount')}}"
                               required id="amount">
                    </div>
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date("Y-m-d")}}"
                               required id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="debit"
                           class="control-label col-md-2">{{trans_choice('general.debit',1)}}</label>
                    <div class="col-md-3">
                        <select name="debit" class="form-control select2" id="debit" required>
                            <option></option>
                            @foreach(\App\Models\GlAccount::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="credit"
                           class="control-label col-md-2">{{trans_choice('general.credit',1)}}</label>
                    <div class="col-md-3">
                        <select name="credit" class="form-control select2" id="credit" required>
                            <option></option>
                            @foreach(\App\Models\GlAccount::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for=""
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
                        <label for="payment_type_id"
                               class="control-label col-md-2">{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}
                        </label>
                        <div class="col-md-3">
                            <select name="payment_type_id" class="form-control select2"
                                    id="payment_type_id">
                                <option></option>
                                @foreach(\App\Models\PaymentType::all() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
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
                               class="control-label col-md-2">{{trans_choice('general.receipt',1)}}
                            #</label>
                        <div class="col-md-3">
                            <input type="text" name="receipt_number"
                                   class="form-control"
                                   value=""
                                   id="receipt_number">
                        </div>
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
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection