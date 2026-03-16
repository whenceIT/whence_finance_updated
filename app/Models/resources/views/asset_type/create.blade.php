@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.type',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.type',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('asset/type/store')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="gl_account_asset_id"
                           class="control-label col-md-3">{{trans_choice('general.cash',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Asset account that is credited when  purchase asset"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_asset_id" class="form-control select2"
                                id="gl_account_asset_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_fixed_asset_id"
                           class="control-label col-md-3">{{trans_choice('general.fixed',1)}} {{trans_choice('general.asset',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Asset account that is debited when  purchase asset"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_fixed_asset_id" class="form-control select2"
                                id="gl_account_fixed_asset_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_expense_id"
                           class="control-label col-md-3">{{trans_choice('general.expense',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an expense account that is debited when  an asset depreciates"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_expense_id" class="form-control select2"
                                id="gl_account_expense_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"expense")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_contra_asset_id"
                           class="control-label col-md-3">{{trans_choice('general.accumulated',1)}} {{trans_choice('general.depreciation',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Asset account that is credited when  asset depreciates"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_contra_asset_id" class="form-control select2"
                                id="gl_account_contra_asset_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_income_id"
                           class="control-label col-md-3"> {{trans_choice('general.income',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Asset account that is credited when  an asset is sold"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_income_id" class="form-control select2"
                                id="gl_account_income_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-9">
                        <textarea name="notes" class="form-control"

                                  id="notes">{{old('notes')}}</textarea>
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