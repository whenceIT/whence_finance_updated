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
        <form method="post" action="{{url('office/'.$office->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="name" class="form-control"
                               value="{{$office->name}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="external_id"
                           class="control-label col-md-3">{{trans_choice('general.external_id',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="external_id" class="form-control"
                               value="{{$office->external_id}}"
                               required id="external_id">
                    </div>

                </div>
                <div class="form-group">
                    <label for="opening_date"
                           class="control-label col-md-3">{{trans_choice('general.opening',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="opening_date" class="form-control date-picker"
                               value="{{$office->opening_date}}"
                               required id="opening_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="parent_id"
                           class="control-label col-md-3">{{trans_choice('general.parent',1)}} {{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-9">
                        <select name="parent_id" class="form-control select2" id="parent_id">
                            <option></option>
                            @foreach(\App\Models\Office::where('id','!=',$office->id)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($office->parent_id==$key->id) selected @endif>{{$key->name}}</option>
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