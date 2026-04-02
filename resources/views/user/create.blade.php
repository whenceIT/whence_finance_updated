a@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add', 1) }} {{ trans_choice('general.user', 1) }}
@endsection
@section('content')
    <style>
        .wizard-step {
            display: none;
        }

        .wizard-step.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .wizard-progress {
            margin-bottom: 20px;
        }

        .wizard-progress .progress-bar {
            transition: width 0.3s;
        }
    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add', 1) }} {{ trans_choice('general.user', 1) }}</h3>
            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="wizard-progress">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                        id="wizard-progress-bar" style="width: 33%;">
                        Step 1 of 3
                    </div>
                </div>
            </div>
            <form method="post" action="{{url('user/store')}}" class="form" id="wizard-form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="box-body">
                    <div class="wizard-step active" id="step-1">
                        <!-- Step 1 -->
                        <div class="form-group">
                            <label for="first_name" class="">{{trans_choice('general.first_name', 1)}}</label>
                            <input type="text" name="first_name" class="form-control"
                                placeholder="{{trans_choice('general.first_name', 1)}}" value="{{old('first_name')}}"
                                required id="first_name">
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="">{{trans_choice('general.last_name', 1)}}</label>
                            <input type="text" name="last_name" class="form-control"
                                placeholder="{{trans_choice('general.last_name', 1)}}" value="{{old('last_name')}}" required
                                id="last_name">
                        </div>
                        <div class="form-group">
                            <label for="gender" class="">{{trans_choice('general.gender', 1)}}</label>
                            <select name="gender" class="form-control" id="gender">
                                <option value="male">{{trans('general.male')}}</option>
                                <option value="female">{{trans('general.female')}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="">{{trans_choice('general.phone', 1)}}</label>
                            <input type="tel" name="phone" class="form-control"
                                placeholder="{{trans_choice('general.phone', 1)}}" value="{{old('phone')}}" id="phone"
                                maxlength="10" minlength="10" required autocomplete="off" readonly
                                onfocus="this.removeAttribute('readonly');" style="background-color: #ffffff;">
                        </div>
                        <div class="box-footer">
                            <button type="button" class="btn btn-info pull-right next-step">Next</button>
                        </div>
                    </div>

                    <div class="wizard-step" id="step-2">
                        <!-- Step 2 -->
                        <div class="form-group">
                            <label for="nrc_id">NRC ID</label>
                            <input type="text" name="nrc_id" class="form-control" placeholder="NRC ID"
                                value="{{old('nrc_id')}}" id="nrc_id">
                        </div>
                        <div class="form-group ">
                            <label for="email" class="">{{trans_choice('general.email', 1)}}</label>
                            <input type="email" name="email" class="form-control"
                                placeholder="{{trans_choice('general.email', 1)}}" value="{{old('email')}}" required
                                id="email">
                        </div>
                        <div class="form-group">
                            <label for="password" class="">{{trans_choice('general.password', 1)}}</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="{{trans_choice('general.password', 1)}}" value="" required id="password">
                        </div>
                        <div class="form-group">
                            <label for="repeat_password" class="">{{trans_choice('general.repeat_password', 1)}}</label>
                            <input type="password" name="repeat_password" class="form-control"
                                placeholder="{{trans_choice('general.repeat_password', 1)}}" value="" required
                                id="repeat_password">
                        </div>
                        <div class="box-footer">
                            <button type="button" class="btn btn-default pull-left previous-step">Previous</button>
                            <button type="button" class="btn btn-info pull-right next-step">Next</button>
                        </div>
                    </div>

                    <div class="wizard-step" id="step-3">
                        <!-- Step 3 content truncated for brevity in replacement, but I must include all fields -->
                        <!-- I will include them carefully -->
                        <!-- Step 3 -->
                        <div class="form-group">
                            <label for="role" class="">{{trans_choice('general.role', 1)}}</label>
                            <select name="role" class="form-control select2" id="role" required>
                                <option></option>
                                @foreach(DB::table('roles')->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="province_id">Province</label>
                            <select name="province_id" class="form-control select2" id="province_id" required>
                                <option></option>
                                @foreach($provinces as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="district_id">District</label>
                            <select name="district_id" class="form-control select2" id="district_id" required>
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="office_id">{{trans_choice('general.office', 1)}}</label>
                            <select name="office_id" class="form-control select2" id="office_id" required>
                                <option></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address" class="">{{trans_choice('general.address', 1)}}</label>
                            <textarea name="address" class="form-control"
                                placeholder="{{trans_choice('general.address', 1)}}" id="address"
                                rows="3">{{old('address')}}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes" class="">{{trans_choice('general.note', 2)}}</label>
                            <!-- <textarea name="notes" class="form-control wysihtml5" -->
                            <textarea name="notes" class="form-control" placeholder="{{trans_choice('general.note', 2)}}"
                                id="notes" rows="3">{{old('notes')}}</textarea>
                        </div>
                        @if(\App\Models\Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1)
                            @foreach(\App\Models\CustomField::where('category', 'users')->get() as $key)
                                <div class="form-group">
                                    <label for="notes" class="control-label col-md-2">{{$key->name}}</label>
                                    <div class="col-md-8">
                                        @if($key->field_type == "number")
                                            <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                                @if($key->required == 1) required @endif>
                                        @endif
                                        @if($key->field_type == "textfield")
                                            <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                                @if($key->required == 1) required @endif>
                                        @endif
                                        @if($key->field_type == "date")
                                            <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                                @if($key->required == 1) required @endif>
                                        @endif
                                        @if($key->field_type == "textarea")
                                            <textarea class="form-control" name="custom_field_{{$key->id}}" @if($key->required == 1)
                                            required @endif></textarea>
                                        @endif
                                        @if($key->field_type == "decimal")
                                            <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                                @if($key->required == 1) required @endif>
                                        @endif
                                        @if($key->field_type == "select")
                                            <select class="form-control touchspin" name="custom_field_{{$key->id}}"
                                                @if($key->required == 1) required @endif>
                                                @if($key->required != 1)
                                                    <option value=""></option>
                                                @else
                                                    <option value="" disabled selected>Select...</option>
                                                @endif
                                                @foreach(explode(',', $key->select_values) as $v)
                                                    <option>{{$v}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @if($key->field_type == "radiobox")
                                            @foreach(explode(',', $key->radio_box_values) as $v)
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}"
                                                            value="{{$v}}" @if($key->required == 1) required @endif>
                                                        <b>{{$v}}</b>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if($key->field_type == "checkbox")
                                            @foreach(explode(',', $key->checkbox_values) as $v)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]" id="{{$key->id}}"
                                                            value="{{$v}}" @if($key->required == 1) required @endif>
                                                        <b>{{$v}}</b>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="box-footer">
                            <button type="button" class="btn btn-default pull-left previous-step">Previous</button>
                            <button type="submit"
                                class="btn btn-primary pull-right">{{trans_choice('general.save', 1)}}</button>
                        </div>
                    </div>
            </form>
        </div>
@endsection
    @section('footer-scripts')
        <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
        <script>
            // Add custom password strength rule
            $.validator.addMethod("strongPassword", function (value, element) {
                return this.optional(element)
                    || /[A-Z]/.test(value)     // has uppercase
                    && /[a-z]/.test(value)     // has lowercase
                    && /[0-9]/.test(value)     // has number
                    && /[\W_]/.test(value)     // has special character
                    && value.length >= 8;      // minimum length
            }, "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");

            $(".form").validate({
                rules: {
                    password: {
                        required: true,
                        strongPassword: true
                    },
                    repeat_password: {
                        required: true,
                        equalTo: "#password"
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    }
                },
                highlight: function (element) {
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

            // Wizard Navigation Logic
            var currentStep = 1;
            var totalSteps = 3;

            function updateProgressBar() {
                var percentage = (currentStep / totalSteps) * 100;
                $('#wizard-progress-bar').css('width', percentage + '%').text('Step ' + currentStep + ' of ' + totalSteps);
            }

            $('.next-step').click(function () {
                var form = $(".form"); // The form has class "form"

                // Validate only fields in the current step
                var currentStepFields = $('#step-' + currentStep).find('input, select, textarea');
                var isValid = true;
                currentStepFields.each(function () {
                    if (!$(this).valid()) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    $('#step-' + currentStep).removeClass('active');
                    currentStep++;
                    $('#step-' + currentStep).addClass('active');
                    updateProgressBar();

                    // Fix for select2 width issues when showing hidden elements
                    if ($('.select2').length > 0) {
                        $('.select2').select2({
                            width: '100%'
                        });
                    }
                }
            });

            $('.previous-step').click(function () {
                $('#step-' + currentStep).removeClass('active');
                currentStep--;
                $('#step-' + currentStep).addClass('active');
                updateProgressBar();
            });



            function handleDistrictVisibility() {
                var roleId = $('#role').val();
                if (roleId == 12) {
                    $('#district_id').closest('.form-group').show();
                    $('#district_id').prop('required', true);
                } else {
                    $('#district_id').closest('.form-group').hide();
                    $('#district_id').val('').trigger('change');
                    $('#district_id').prop('required', false);
                }
            }

            handleDistrictVisibility();

            $('#role').change(function () {
                handleDistrictVisibility();
            });

            $('#province_id').change(function () {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        url: "{{ url('user/get_offices_by_province') }}/" + id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#office_id').empty();
                            $('#office_id').append('<option value=""></option>');
                            $.each(data, function (key, value) {
                                $('#office_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });

                        }
                    });
                    $.ajax({
                        url: "{{ url('user/get_districts_by_province') }}/" + id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#district_id').empty();
                            $('#district_id').append('<option value=""></option>');
                            $.each(data, function (key, value) {
                                $('#district_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#office_id').empty();
                    $('#office_id').append('<option value=""></option>');
                    $('#district_id').empty();
                    $('#district_id').append('<option value=""></option>');
                }
            });
        </script>
    @endsection