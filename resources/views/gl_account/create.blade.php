@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}}  {{trans_choice('general.chart_of_account',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h1>THIS IS THE PAGE</h1>
            <h4 class="box-title">{{trans_choice('general.add',1)}}  {{trans_choice('general.chart_of_account',1)}}</h4>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('accounting/gl_account/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="account_type"
                           class="control-label col-md-2">{{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="account_type" class="form-control select2" id="account_type" required>
                            <option></option>
                            <option value="asset">{{trans_choice('general.asset',1)}}</option>
                            <option value="liability">{{trans_choice('general.liability',1)}}</option>
                            <option value="equity">{{trans_choice('general.equity',1)}}</option>
                            <option value="income">{{trans_choice('general.income',1)}}</option>
                            <option value="expense">{{trans_choice('general.expense',1)}}</option>
                        </select>
                    </div>
                    <label for="parent_id"
                           class="control-label col-md-2">{{trans_choice('general.parent',1)}}</label>
                    <div class="col-md-3">
                        <select name="parent_id" class="form-control select2" id="parent_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gl_code"
                           class="control-label col-md-2">{{trans_choice('general.gl_code',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="gl_code" class="form-control"
                               value="{{old('gl_code')}}"
                               required id="gl_code">
                    </div>
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="manual_entries"
                           class="control-label col-md-2">{{trans_choice('general.manual',1)}} {{trans_choice('general.entry',2)}}</label>
                    <div class="col-md-3">
                        <select name="manual_entries" class="form-control " id="manual_entries" required>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                        </select>
                    </div>
                    <label for="active"
                           class="control-label col-md-2">{{trans_choice('general.active',1)}}</label>
                    <div class="col-md-3">
                        <select name="active" class="form-control " id="active" required>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3">{{old('notes')}}</textarea>
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
    <!-- /.box -->
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
                $(element).closest('.form-group div').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group div').removeClass('has-error');
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

