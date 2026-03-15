@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.sms',1)}} {{trans_choice('general.gateway',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.sms',1)}} {{trans_choice('general.gateway',1)}}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('sms_gateway/'.$sms_gateway->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="col-sm-2 control-label">{{trans_choice('general.name',1)}}</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control"
                               placeholder=""
                               value="{{$sms_gateway->name}}"
                               required id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="to_name"
                           class="col-sm-2 control-label">{{trans_choice('general.to',1).' '.trans_choice('general.name',1)}}</label>
                    <div class="col-sm-10">
                        <input type="text" name="to_name" class="form-control"
                               placeholder=""
                               value="{{$sms_gateway->to_name}}"
                               required id="to_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="msg_name"
                           class="col-sm-2 control-label">{{trans_choice('general.message',1).' '.trans_choice('general.name',1)}}</label>
                    <div class="col-sm-10">
                        <input type="text" name="msg_name" class="form-control"
                               placeholder=""
                               value="{{$sms_gateway->msg_name}}"
                               required id="msg_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="url"
                           class="col-sm-2 control-label">{{trans_choice('general.url',1)}}</label>
                    <div class="col-sm-10">
                        <input type="text" name="url" class="form-control"
                               placeholder=""
                               value="{{$sms_gateway->url}}"
                               required id="url">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="col-sm-2 control-label">{{trans_choice('general.note',2)}}</label>
                    <div class="col-sm-10">
                                    <textarea name="notes" class="form-control "
                                              placeholder="{{trans_choice('general.note',2)}}"
                                              id="notes" rows="3">{{$sms_gateway->notes}}</textarea>
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

