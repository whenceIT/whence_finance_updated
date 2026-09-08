@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.product_configuration',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.product_configuration',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{ route('motor-vehicle.product-configurations.store') }}" class="form-horizontal">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>
                    <label for="short_name"
                           class="control-label col-md-2">{{trans_choice('general.short_name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="short_name" class="form-control"
                               value="{{old('short_name')}}"
                               id="short_name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="minimum_principal"
                           class="control-label col-md-2">{{trans_choice('general.minimum',1)}} {{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="minimum_principal" class="form-control"
                               value="{{old('minimum_principal')}}"
                               id="minimum_principal">
                    </div>
                    <label for="maximum_principal"
                           class="control-label col-md-2">{{trans_choice('general.maximum',1)}} {{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="maximum_principal" class="form-control"
                               value="{{old('maximum_principal')}}"
                               id="maximum_principal">
                    </div>

                </div>
                <div class="form-group">
                    <label for="default_interest_rate"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="default_interest_rate" class="form-control"
                               value="{{old('default_interest_rate')}}"
                               id="default_interest_rate">
                    </div>
                    <label for="interest_rate_type"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="interest_rate_type" class="form-control" id="interest_rate_type">
                            <option value=""></option>
                            <option value="day">{{trans_choice('general.day',1)}}</option>
                            <option value="week">{{trans_choice('general.week',1)}}</option>
                            <option value="month">{{trans_choice('general.month',1)}}</option>
                            <option value="year">{{trans_choice('general.year',1)}}</option>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label for="default_loan_term"
                           class="control-label col-md-2">{{trans_choice('general.tenure',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="default_loan_term" class="form-control"
                               value="{{old('default_loan_term')}}"
                               id="default_loan_term">
                    </div>
                    <label for="effective_date"
                           class="control-label col-md-2">{{trans_choice('general.effective_date',1)}}</label>
                    <div class="col-md-3">
                        <input type="date" name="effective_date" class="form-control"
                               value="{{old('effective_date')}}"
                               id="effective_date">
                    </div>

                </div>
                <div class="form-group">
                    <label for="service_fee"
                           class="control-label col-md-2">{{trans_choice('general.service',1)}} {{trans_choice('general.fee',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="service_fee" class="form-control"
                               value="{{old('service_fee')}}"
                               id="service_fee">
                    </div>
                    <label for="processing_fee"
                           class="control-label col-md-2">{{trans_choice('general.processing',1)}} {{trans_choice('general.fee',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="processing_fee" class="form-control"
                               value="{{old('processing_fee')}}"
                               id="processing_fee">
                    </div>

                </div>
                <div class="form-group">
                    <label for="insurance_fee"
                           class="control-label col-md-2">{{trans_choice('general.insurance',1)}} {{trans_choice('general.fee',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="insurance_fee" class="form-control"
                               value="{{old('insurance_fee')}}"
                               id="insurance_fee">
                    </div>
                    <label for="valuation_fee"
                           class="control-label col-md-2">{{trans_choice('general.valuation',1)}} {{trans_choice('general.fee',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="valuation_fee" class="form-control"
                               value="{{old('valuation_fee')}}"
                               id="valuation_fee">
                    </div>

                </div>
                <div class="form-group">
                    <label for="inspection_fee"
                           class="control-label col-md-2">{{trans_choice('general.inspection',1)}} {{trans_choice('general.fee',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="inspection_fee" class="form-control"
                               value="{{old('inspection_fee')}}"
                               id="inspection_fee">
                    </div>
                    <label for="penalty_rate"
                           class="control-label col-md-2">{{trans_choice('general.penalty',1)}} {{trans_choice('general.rate',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="penalty_rate" class="form-control"
                               value="{{old('penalty_rate')}}"
                               id="penalty_rate">
                    </div>

                </div>
                <div class="form-group">
                    <label for="recovery_charges"
                           class="control-label col-md-2">{{trans_choice('general.recovery',1)}} {{trans_choice('general.charge',2)}}</label>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="recovery_charges" class="form-control"
                               value="{{old('recovery_charges')}}"
                               id="recovery_charges">
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
