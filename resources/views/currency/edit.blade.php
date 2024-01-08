@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.currency',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.currency',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('currency/'.$currency->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">

                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{$currency->name}}"
                               required id="name">
                    </div>
                    <label for="code"
                           class="control-label col-md-2">{{trans_choice('general.code',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="code" class="form-control"
                               value="{{$currency->code}}"
                               required id="code">
                    </div>

                </div>
                <div class="form-group">
                    <label for="symbol"
                           class="control-label col-md-2">{{trans_choice('general.symbol',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="symbol" class="form-control"
                               value="{{$currency->symbol}}"
                               required id="symbol">
                    </div>
                    <label for="decimals"
                           class="control-label col-md-2">{{trans_choice('general.decimal',2)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="decimals" class="form-control"
                               value="{{$currency->decimals}}"
                               required id="decimals">
                    </div>

                </div>
                <div class="form-group">
                    <label for="active"
                           class="control-label col-md-2">{{trans_choice('general.active',1)}}</label>
                    <div class="col-md-3">
                        <select name="active" class="form-control " id="active" required>
                            <option value="1" @if($currency->active=="1") selected @endif>{{trans_choice('general.yes',1)}}</option>
                            <option value="0" @if($currency->active=="0") selected @endif>{{trans_choice('general.no',1)}}</option>
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