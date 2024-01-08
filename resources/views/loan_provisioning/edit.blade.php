@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.provisioning',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.provisioning',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan_provisioning/'.$loan_provisioning->id.'/update')}}"
              class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{$loan_provisioning->name}}"
                               required id="name">
                    </div>
                    <label for="percentage"
                           class="control-label col-md-2">{{trans_choice('general.percentage',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="percentage" class="form-control"
                               value="{{$loan_provisioning->percentage}}"
                               required id="percentage">
                    </div>
                </div>
                <div class="form-group">
                    <label for="min"
                           class="control-label col-md-2">{{trans_choice('general.min',1)}} {{trans_choice('general.age',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="min" class="form-control"
                               value="{{$loan_provisioning->min}}"
                               required id="min">
                    </div>
                    <label for="max"
                           class="control-label col-md-2">{{trans_choice('general.max',1)}} {{trans_choice('general.age',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="max" class="form-control"
                               value="{{$loan_provisioning->max}}"
                               required id="max">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_account_liability_id"
                           class="control-label col-md-2">{{trans_choice('general.liability',1)}} {{trans_choice('general.account',1)}}</label>
                    <div class="col-md-3">
                        <select name="gl_account_liability_id" class="form-control select2" id="gl_account_liability_id"
                                required>
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('account_type','liability')->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="gl_account_expense_id"
                           class="control-label col-md-2">{{trans_choice('general.expense',1)}} {{trans_choice('general.account',1)}}</label>
                    <div class="col-md-3">
                        <select name="gl_account_expense_id" class="form-control select2" id="gl_account_expense_id"
                                required>
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('account_type','expense')->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
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