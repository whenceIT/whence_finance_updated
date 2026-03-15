@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('client/store')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="office_id" class="control-label col-md-2">{{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="staff_id" class="control-label col-md-2">{{trans_choice('general.staff',1)}}</label>
                    <div class="col-md-3">
                        <select name="staff_id" class="form-control select2" id="staff_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="client_type" class="control-label col-md-2">{{trans_choice('general.client',1)}} {{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="client_type" class="form-control" id="client_type" required>
                            <option value="individual">{{trans_choice('general.individual',1)}}</option>
                            <option value="business">{{trans_choice('general.business',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="business_name_div" style="display: none">
                    <div class="form-group">
                        <label for="full_name" class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="full_name" class="form-control" value="{{old('full_name')}}" id="full_name">
                        </div>
                        <label for="incorporation_number" class="control-label col-md-2">{{trans_choice('general.incorporation_number',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="incorporation_number" class="form-control" value="{{old('incorporation_number')}}" id="incorporation_number">
                        </div>
                    </div>
                </div>
                <div id="individual_name_div">
                    <div class="form-group">
                        <label for="first_name" class="control-label col-md-2">{{trans_choice('general.first_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="first_name" class="form-control" value="{{old('first_name')}}" id="first_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="middle_name" class="control-label col-md-2">{{trans_choice('general.middle_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="middle_name" class="form-control" value="{{old('middle_name')}}" id="middle_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="control-label col-md-2">{{trans_choice('general.last_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="last_name" class="form-control" value="{{old('last_name')}}" id="last_name">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mobile" class="control-label col-md-2">{{trans_choice('general.mobile',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="mobile" class="form-control" value="{{old('mobile')}}" required id="mobile">
                    </div>
                    <label for="phone" class="control-label col-md-2">{{trans_choice('general.phone',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control" value="{{old('phone')}}" id="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label col-md-2">{{trans_choice('general.email',1)}}</label>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control" value="{{old('email')}}" id="email">
                    </div>
                </div>
                <div class="form-group" id="contact_person_div" style="display: none;">
                    <label for="contact_person" class="control-label col-md-2">Contact Person</label>
                    <div class="col-md-3">
                        <input type="text" name="contact_person" class="form-control" id="contact_person">
                    </div>
                </div>
                <div id="individual_extra_details">
                    <div class="form-group">
                        <label for="dob" class="control-label col-md-2">{{trans_choice('general.dob',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="dob" class="form-control date-picker" value="{{old('dob')}}" id="dob">
                        </div>
                        <label for="gender" class="control-label col-md-2">{{trans_choice('general.gender',1)}}</label>
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
                        <label for="marital_status" class="control-label col-md-2">{{trans_choice('general.marital_status',1)}}</label>
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
                    <label for="street" class="control-label col-md-2" id="label_street">{{trans_choice('general.street',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="street" class="form-control" value="{{old('street')}}" id="street">
                    </div>
                    <label for="address" class="control-label col-md-2" id="label_address">{{trans_choice('general.address',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="address" class="form-control" value="" id="address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="external_id" class="control-label col-md-2" id="label_nrc">NRC Number</label>
                    <div class="col-md-3">
                    <input type="text" name="external_id" class="form-control"
    value="{{ old('external_id', isset($client) ? ($client->external_id ?? $client->key_contact_person_nrc_number) : '') }}"
    id="external_id">

                    </div>
                </div>
                
                <div class="form-group">
                    <label for="working_place" class="control-label col-md-2">Working Place</label>
                    <div class="col-md-3">
                        <input type="text" name="working_place" class="form-control" value="{{old('working_place')}}" id="working_place">
                    </div>
                    <label for="salary" class="control-label col-md-2" id="label_salary">{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="salary" class="form-control" value="{{old('salary')}}" id="salary">
                    </div>
                </div>
             
                <div class="form-group">
                    <label for="joined_date" class="control-label col-md-2">{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="joined_date" class="form-control date-picker" value="{{date("Y-m-d")}}" id="joined_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control" id="notes" rows="3">{{old('notes')}}</textarea>
                    </div>
                </div>
            </div>
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
    function adjustFormByClientType(type) {
        if (type === "individual") {
            $("#business_name_div").hide();
            $("#individual_name_div").show();
            $("#individual_extra_details").show();
            $("#position_div").show();
            $("#contact_person_div").hide();

            $("#first_name").attr("required", "required");
            $("#last_name").attr("required", "required");
            $("#dob").attr("required", "required");
            $("#gender").attr("required", "required");
            $("#marital_status").attr("required", "required");
            $("#contact_person").removeAttr("required");
            $("#full_name").removeAttr("required");

            $("#label_street").text("Street");
            $("#label_address").text("Address");
            $("#label_nrc").text("NRC Number");
            $("#label_salary").text("Net Pay");
        } else {
            $("#business_name_div").show();
            $("#individual_name_div").hide();
            $("#individual_extra_details").hide();
            $("#position_div").hide();
            $("#contact_person_div").show();

            $("#first_name").removeAttr("required");
            $("#last_name").removeAttr("required");
            $("#dob").removeAttr("required");
            $("#gender").removeAttr("required");
            $("#marital_status").removeAttr("required");
            $("#contact_person").attr("required", "required");
            $("#full_name").attr("required", "required");

            $("#label_street").text("Address of Registration");
            $("#label_address").text("Physical Address of Company");
            $("#label_nrc").text("NRC Number of Director");
            $("#label_salary").text("Monthly Turnover");
        }
    }

    $(document).ready(function () {
        adjustFormByClientType($("#client_type").val());
        $("#client_type").change(function () {
            adjustFormByClientType($(this).val());
        });
    });
</script>
@endsection
