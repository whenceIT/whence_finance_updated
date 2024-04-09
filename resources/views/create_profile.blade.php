@extends('layouts.auth')
@section('title')
    {{ trans_choice('general.create_profile',1) }}
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.create_profile',1) }} </h3>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>
    <span class="text-success"> 💪 Almost there 👏 </span>
</div>
            <div class="box-tools pull-right">
                <button onclick="{{url('login')}}" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
   

        <form method="post" action="{{url('createProfile')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}


                        <input type="hidden" name="id" class="form-control date-picker"
                               value="{{$id}}"
                               id="{{$id}}">
            <div class="box-body">



                <div class="form-group">
                    <label for="client_type"
                           class="control-label col-md-2">{{trans_choice('general.client',1)}} {{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="client_type" class="form-control " id="client_type" required>
                            <option value="individual">{{trans_choice('general.individual',1)}}</option>
                            <option value="business">{{trans_choice('general.business',1)}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">Branch</label>
                    <div class="col-md-3">
                    <select name="office_id" class="branch form-control" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="loan_officer_id"
                           class="control-label col-md-2">Loan Consultant</label>
                    <div class="col-md-3">
                    <select name="loan_officer_id" class="branch form-control" id="loan_officer_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>  


                <div id="business_name_div" style="display: none">
                    <div class="form-group">
                        <label for="full_name"
                               class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="full_name" class="form-control"
                                   value="{{old('full_name')}}"
                                   required id="full_name">
                        </div>
                        <label for="incorporation_number"
                               class="control-label col-md-2">{{trans_choice('general.incorporation_number',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="incorporation_number" class="form-control"
                                   value="{{old('incorporation_number')}}" id="incorporation_number">
                        </div>
                    </div>
                </div>
          

                <div id="individual_extra_details">
                    <div class="form-group">
                        <label for="dob"
                               class="control-label col-md-2">{{trans_choice('general.dob',1)}}</label>
                        <div class="col-md-3">

                  
                            <input type="date" name="dob" class="form-control date-picker"
                                   value="{{old('dob')}}"
                                   id="dob">
                        </div>
                        <label for="gender"
                               class="control-label col-md-2">{{trans_choice('general.gender',1)}}</label>
                        <div class="col-md-3">
                            <select name="gender" class="form-control" id="gender">
                                <option></option>
                                <option value="male">{{trans('general.male')}}</option>
                                <option value="female">{{trans('general.female')}}</option>
                                <option value="other">{{trans('general.other')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="marital_status"
                               class="control-label col-md-2">{{trans_choice('general.marital_status',1)}}</label>
                        <div class="col-md-3">
                            <select name="marital_status" class="form-control" id="marital_status">
                                <option></option>
                                <option value="married">{{trans('general.married')}}</option>
                                <option value="single">{{trans('general.single')}}</option>
                                <option value="divorced">{{trans('general.divorced')}}</option>
                                <option value="widowed">{{trans('general.widowed')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="street"
                           class="control-label col-md-2">{{trans_choice('general.street',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="street" class="form-control"
                               value="{{old('street')}}"
                               id="street">
                    </div>
                    <label for="address"
                           class="control-label col-md-2">{{trans_choice('general.address',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="address" class="form-control"
                               value=""
                               required
                               id="address">
                    </div>

                </div>

                <div class="form-group">
                
                    <label for="external_id"
                           class="control-label col-md-2">{{trans_choice('general.id_num',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="external_id" class="form-control"
                               value="{{old('external_id')}}"
                               id="external_id">
                    </div>
                
                        <input type="hidden" name="joined_date" class="form-control date-picker"
                               value="{{date("Y-m-d")}}"
                               id="joined_date">

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
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','clients')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif></textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
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
                                            <option>{{$v}}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}" value="{{$v}}"
                                                       @if($key->required==1) required @endif>
                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
                                    @foreach(explode(',',$key->checkbox_values) as $v)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]" id="{{$key->id}}"
                                                       value="{{$v}}"
                                                       @if($key->required==1) required @endif>
                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
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

$(document).ready(function() {
    $('.branch').select2();
});


// $(document).ready(function(){
//     $('.office_id').select2();
// });


// $(document).ready(function(){
//     $('.loan_officer_id').select2();
// });


        if ($("#client_type").val() == "individual") {
            $("#business_name_div").hide();
            $("#individual_name_div").show();
            $("#individual_extra_details").show();
            $("#first_name").attr("required", "required");
            $("#last_name").attr("required", "required");
            $("#dob").attr("required", "required");
            $("#gender").attr("required", "required");
            $("#marital_status").attr("required", "required");
            $("#full_name").removeAttr("required");


        } else {
            $("#business_name_div").show();
            $("#individual_name_div").hide();
            $("#individual_extra_details").hide();
            $("#first_name").removeAttr("required");
            $("#last_name").removeAttr("required");
            $("#dob").removeAttr("required");
            $("#gender").removeAttr("required");
            $("#marital_status").removeAttr("required");
            $("#full_name").attr("required", "required");
        }
        $("#client_type").change(function () {
            if ($("#client_type").val() == "individual") {
                $("#business_name_div").hide();
                $("#individual_name_div").show();
                $("#individual_extra_details").show();
                $("#first_name").attr("required", "required");
                $("#last_name").attr("required", "required");
                $("#dob").attr("required", "required");
                $("#gender").attr("required", "required");
                $("#marital_status").attr("required", "required");
                $("#full_name").removeAttr("required");


            } else {
                $("#business_name_div").show();
                $("#individual_name_div").hide();
                $("#individual_extra_details").hide();
                $("#first_name").removeAttr("required");
                $("#last_name").removeAttr("required");
                $("#dob").removeAttr("required");
                $("#gender").removeAttr("required");
                $("#marital_status").removeAttr("required");
                $("#full_name").attr("required", "required");
            }
        });
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