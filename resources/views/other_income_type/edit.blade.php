@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.branch',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.branch',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('other_income/type/'.$other_income_type->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control"
                               value="{{$other_income_type->name}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="gl_account_asset_id"
                           class="control-label col-md-3">{{trans_choice('general.asset',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Asset account that is debited when  you record an asset"></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_asset_id" class="form-control select2"
                                id="gl_account_asset_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($other_income_type->gl_account_asset_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_income_id"
                           class="control-label col-md-3">{{trans_choice('general.income',1)}} {{trans_choice('general.account',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="an Income account "></i>
                    </label>
                    <div class="col-md-9">
                        <select name="gl_account_income_id" class="form-control select2"
                                id="gl_account_income_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($other_income_type->gl_account_income_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="notes">{{$other_income_type->notes}}</textarea>
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