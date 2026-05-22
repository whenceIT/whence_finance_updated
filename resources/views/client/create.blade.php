@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}
@endsection
@section('content')
  <div
class="box"

style="
border:none;
border-radius:20px;
overflow:hidden;
background:white;
box-shadow:
0 20px 40px rgba(
0,
0,
0,
.08
);
">
        <div

class="box-header"

style="

background:

linear-gradient(

135deg,

#3c8dbc,

#00c0ef

);

padding:

25px;

border:none;

color:white;

">
            <h3

class="box-title"

style="

font-size:

24px;

font-weight:

700;

">
{{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('client/store')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div

class="box-body"

style="

padding:

35px;

background:

#fafafa;

">

                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @php
                                $offices = \App\Helpers\GeneralHelper::get_filtered_offices();
                            @endphp
                            @foreach($offices as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @php
                        $staffs = \App\Helpers\GeneralHelper::get_filtered_staffs();
                    @endphp
                    <label for="staff_id"
                           class="control-label col-md-2">{{trans_choice('general.staff',1)}}</label>
                    <div class="col-md-3">
                        <select name="staff_id" class="form-control select2" id="staff_id" required>
                            <option>Please select a branch first</option>                        
                        </select>
                        <div id="staff_loading" style="display: none; margin-top: 5px;">
                            <i class="fa fa-spinner fa-spin"></i> Checking staff...
                        </div>
                    </div>
                </div>
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
                <div id="individual_name_div">
                    <div class="form-group">
                        <label for="first_name"
                               class="control-label col-md-2">{{trans_choice('general.first_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="first_name" class="form-control"
                                   value="{{old('first_name')}}"
                                   required id="first_name">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="middle_name"
                               class="control-label col-md-2">{{trans_choice('general.middle_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="middle_name" class="form-control"
                                   value="{{old('middle_name')}}" id="middle_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="last_name"
                               class="control-label col-md-2">{{trans_choice('general.last_name',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="last_name" class="form-control"
                                   value="{{old('last_name')}}"
                                   required id="last_name">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mobile"
                           class="control-label col-md-2">{{trans_choice('general.mobile',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="mobile" class="form-control"
                               value="{{old('mobile')}}"
                               required id="mobile">
                               <small

style="

display:

block;

margin-top:

8px;

font-size:

12px;

color:

#888;

">

🔒 Identity verification powered by

<b>

Withinhere

</b>

</small>
                    </div>
                    <label for="phone"
                           class="control-label col-md-2">{{trans_choice('general.phone',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control"
                               value="{{old('phone')}}"
                               id="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}}</label>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control"
                               value="{{old('email')}}"
                               id="email">
                    </div>
                </div>
                <div id="individual_extra_details">
                    <div class="form-group">

                        <label for="dob"
                               class="control-label col-md-2">{{trans_choice('general.dob',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="dob" class="form-control date-picker"
                               placeholder=""
                               id="joined_date"">
                              
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
                               id="address">
                    </div>

                </div>

                <div class="form-group">
                    <label for="nrc_number"
                           class="control-label col-md-2">NRC Number</label>
                    <div class="col-md-3">
                        <input type="text" name="nrc_number" class="form-control"
                               value="{{old('nrc_number')}}"
                               id="nrc_number">
                    </div>
                  

                </div>


                <div class="form-group">
                    <label for="working_place"
                           class="control-label col-md-2">Working Place</label>
                    <div class="col-md-3">
                        <input type="text" name="working_place" class="form-control"
                               value="{{old('working_place')}}"
                               id="working_place">
                    </div>
                    
                    <label for="salary"
                           class="control-label col-md-2">{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="salary" class="form-control"
                               value="{{old('salary')}}"
                               id="salary">
                    </div>

                </div>
                <div class="form-group">
                    <label for="p"
                           class="control-label col-md-2">Position</label>
                    <div class="col-md-3">
                        <input type="text" name="working_position" class="form-control"
                               value="{{old('working_position')}}"
                               id="working_position">
                    </div>
                    
                    <label for="joined_date"
                           class="control-label col-md-2">{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="joined_date" class="form-control date-picker"
                               value="{{date('Y-m-d')}}"
                               id="joined_date">
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
                    <button

type="submit"

class="btn btn-primary pull-right"

style="

padding:

14px 35px;

border-radius:

12px;

font-weight:

700;

font-size:

15px;

box-shadow:

0 10px 20px rgba(

60,
141,
188,

.2

);

">
{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
<div
class="modal fade"

id="verificationModal"

tabindex="-1">

<div

class="modal-dialog modal-sm"

style="

margin-top:

8%;

">

<div

class="modal-content"

style="

border:none;

border-radius:

20px;

overflow:

hidden;

box-shadow:

0 20px 50px rgba(

0,
0,
0,

.15

);

">

<div

class="modal-header"

style="

background:

linear-gradient(

135deg,

#3c8dbc,

#00c0ef

);

padding:

22px;

border:none;

color:white;

">

<button

type="button"

class="close"

data-dismiss="modal"

style="

color:

white;

opacity:

1;

">

×

</button>


<h4

class="modal-title"

style="

font-weight:

700;

">

Identity Verification

</h4>

</div>



<div

id="verificationBody"

class="modal-body"

style="

padding:

35px;

text-align:

center;

font-size:

16px;

">

</div>



<div

class="modal-footer"

style="

border:none;

padding:

20px;

">

<button

type="button"

class="btn btn-primary"

data-dismiss="modal"

style="

width:

100%;

padding:

14px;

border-radius:

12px;

font-weight:

700;

">

Continue

</button>

</div>



</div>

</div>

</div>

    </div>
@endsection
@section('footer-scripts')
    <script>
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

        $("#office_id").change(function () {
            var officeId = $(this).val();
            if (officeId) {
                $("#staff_loading").show();
                $.ajax({
                    url: '{{ url("client/get-staffs") }}',
                    type: 'GET',
                    data: { office_id: officeId },
                    success: function (data) {
                        $("#staff_id").empty();
                        $("#staff_id").append('<option></option>');
                        $.each(data, function (key, value) {
                            $("#staff_id").append('<option value="' + key + '">' + value + '</option>');
                        });
                        $("#staff_loading").hide();
                    },
                    error: function () {
                        $("#staff_loading").hide();
                        $("#staff_id").empty();
                        $("#staff_id").append('<option>Please select a branch first</option>');
                    }
                });
            } else {
                $("#staff_id").empty();
                $("#staff_id").append('<option>Please select a branch first</option>');
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

        var date = $('#dob').datepicker({ dateFormat: 'yy-mm-dd' }).val();
// =========================
// PHONE FRAUD VERIFICATION
// =========================

function normalizeName(name) {

    return name
        .toLowerCase()
        .trim()
        .replace(/\s+/g,' ');

}


// Determine operator
function getOperator(phoneNumber){

    let thirdDigit =
        phoneNumber.charAt(2);


    switch(thirdDigit){

        case "7":
            return "airtel";

        case "6":
            return "mtn";

        case "5":
            return "zamtel";

        default:
            return null;

    }

}




function verifyPhone(phoneNumber){


    let firstName =

        $("#first_name")
        .val()

        ||

        "";



    let lastName =

        $("#last_name")
        .val()

        ||

        "";



    let enteredName =

        normalizeName(

            firstName +

            " " +

            lastName

        );




    let operator =

        getOperator(

            phoneNumber

        );




    if(

        !firstName ||

        !lastName ||

        !operator

    ){

        return;

    }



    // prettier loading state

    $("#verificationBody")
    .html(`

        <div
        style="
        font-size:
        55px;
        margin-bottom:
        20px;
        ">

        ⏳

        </div>


        <h3>

        Verifying Identity

        </h3>


        <p>

        Checking registered mobile owner...

        </p>

    `);



    $("#verificationModal")
    .modal(
        "show"
    );




    $.ajax({


        url:

        "https://withinheremobileapi.com/api/v1/payment/resolve/mobile",



        type:

        "POST",



        contentType:

        "application/json",



        data:

        JSON.stringify({

            phone:

            phoneNumber,

            operator:

            operator

        }),




        success:function(response){



            let apiName =


                normalizeName(

                    response.data.accountName

                    ||

                    ""

                );




            let match =


                apiName.includes(

                    enteredName

                )

                ||

                enteredName.includes(

                    apiName

                );





            if(match){


                $("#verificationBody")

                .html(`


                    <div

                    style="

                    font-size:

                    65px;

                    margin-bottom:

                    20px;

                    ">

                    ✅

                    </div>




                    <h3>

                    Identity Verified

                    </h3>




                    <p>

                    Registered Name:

                    <br>

                    <b>

                    ${response.data.accountName}

                    </b>

                    </p>




                    <p>

                    Network:

                    <b>

                    ${response.data.operator.toUpperCase()}

                    </b>

                    </p>




                    <div

                    style="

                    background:

                    #eaf8ee;

                    padding:

                    12px;

                    border-radius:

                    10px;

                    color:

                    #1e7e34;

                    font-weight:

                    600;

                    margin-top:

                    20px;

                    ">

                    ✓ Names Match

                    </div>

                    <div

style="

margin-top:

18px;

font-size:

12px;

color:

#888;

">

Powered by

<b>

Withinhere

</b>

</div>

                `);



            }else{



                $("#verificationBody")

                .html(`


                    <div

                    style="

                    font-size:

                    65px;

                    margin-bottom:

                    20px;

                    ">

                    ⚠️

                    </div>




                    <h3>

                    Verification Warning

                    </h3>




                    <p>

                    Name Entered:

                    <br>

                    <b>

                    ${firstName}

                    ${lastName}

                    </b>

                    </p>




                    <p>

                    Registered Name:

                    <br>

                    <b>

                    ${response.data.accountName}

                    </b>

                    </p>




                    <p>

                    Network:

                    <b>

                    ${response.data.operator.toUpperCase()}

                    </b>

                    </p>




                    <div

                    style="

                    background:

                    #fff4e5;

                    padding:

                    12px;

                    border-radius:

                    10px;

                    color:

                    #b76e00;

                    font-weight:

                    600;

                    margin-top:

                    20px;

                    ">

                    ⚠ Names Do Not Match

                    </div>

                    <div

style="

margin-top:

18px;

font-size:

12px;

color:

#888;

">

Powered by

<b>

Withinhere

</b>

</div>

                `);


            }



        },




        error:function(){



            $("#verificationBody")

            .html(`


                <div

                style="

                font-size:

                65px;

                margin-bottom:

                20px;

                ">

                ❌

                </div>




                <h3>

                Verification Failed

                </h3>




                <div

                style="

                background:

                #fdecec;

                padding:

                12px;

                border-radius:

                10px;

                color:

                #c53030;

                font-weight:

                600;

                ">

                Unable to verify this number

                </div>

                <div

style="

margin-top:

18px;

font-size:

12px;

color:

#888;

">

Powered by

<b>

Withinhere

</b>

</div>

            `);



        }



    });



}





// ======================================
// Trigger verification
// ======================================


let verificationTimeout;



function triggerVerification(){


    clearTimeout(

        verificationTimeout

    );




    verificationTimeout =


    setTimeout(()=>{



        let phone =


            $("#mobile")

            .val()

            ||

            $("#phone")

            .val()

            ||

            "";



        phone =


            phone.replace(

                /\D/g,

                ""

            );





        let firstName =


            $("#first_name")

            .val()

            ?.trim()

            ||

            "";




        let lastName =


            $("#last_name")

            .val()

            ?.trim()

            ||

            "";




        if(

            phone.length !== 10

            ||

            !firstName

            ||

            !lastName

        ){

            return;

        }




        verifyPhone(

            phone

        );



    },300);



}




// Verify when anything changes


$("#mobile")

.on(

    "input",

    triggerVerification

);



$("#first_name,#last_name")

.on(

    "input",

    triggerVerification

);
    </script>
@endsection






























   
