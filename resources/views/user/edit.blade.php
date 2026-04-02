@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.user',1) }}
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
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
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
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.user',1) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body">
            <div class="wizard-progress">
                <div class="progress">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" id="wizard-progress-bar" style="width: 33%;">
                        Step 1 of 3
                    </div>
                </div>
            </div>
            <form method="post" action="{{url('user/'.$user->id.'/update')}}" class="form" id="wizard-form" enctype="multipart/form-data">
                {{csrf_field()}}
            <div class="box-body">
                <input type="hidden" name="previous_role" value="{{$selected}}"/>

                <div class="wizard-step active" id="step-1">
                    <!-- step 1 -->
                    <div class="form-group">
                        <label for="first_name"
                               class="">{{trans_choice('general.first_name',1)}}</label>
                        <input type="text" name="first_name" class="form-control"
                               placeholder="{{trans_choice('general.first_name',1)}}"
                               value="{{$user->first_name}}"
                               required id="first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name"
                               class="">{{trans_choice('general.last_name',1)}}</label>
                        <input type="text" name="last_name" class="form-control"
                               placeholder="{{trans_choice('general.last_name',1)}}"
                               value="{{$user->last_name}}"
                               required id="last_name">
                    </div>
                    <div class="form-group">
                        <label for="gender"
                               class="">{{trans_choice('general.gender',1)}}</label>
                        <select name="gender" class="form-control" id="gender">
                            <option value="male"
                                    @if($user->gender=="male") selected @endif>{{trans('general.male')}}</option>
                            <option value="female"
                                    @if($user->gender=="female") selected @endif>{{trans('general.female')}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone"
                               class="">{{trans_choice('general.phone',1)}}</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="{{trans_choice('general.phone',1)}}"
                               value="{{$user->phone}}"
                               id="phone">
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-info pull-right next-step">Next</button>
                    </div>
                </div>

                <div class="wizard-step" id="step-2">
                    <!-- Step 2 -->
                    <div class="form-group">
                        <label for="nrc_id">NRC ID</label>
                        <input type="text" name="nrc_id" class="form-control"
                               placeholder="NRC ID"
                               value="{{$user->nrc_id}}"
                               id="nrc_id">
                    </div>
                    <div class="form-group ">
                        <label for="email"
                               class="">{{trans_choice('general.email',1)}}</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="{{trans_choice('general.email',1)}}"
                               value="{{$user->email}}" required
                               id="email">
                    </div>
                    <div class="form-group">
                        <label for="password"
                               class="">{{trans_choice('general.password',1)}}</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="{{trans_choice('general.password',1)}}"
                               value=""
                               id="password">
                    </div>
                    <div class="form-group">
                        <label for="repeat_password"
                               class="">{{trans_choice('general.repeat_password',1)}}</label>
                        <input type="password" name="rpassword" class="form-control"
                               placeholder="{{trans_choice('general.repeat_password',1)}}"
                               value=""
                               id="repeat_password">
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-default pull-left previous-step">Previous</button>
                        <button type="button" class="btn btn-info pull-right next-step">Next</button>
                    </div>
                </div>

                <div class="wizard-step" id="step-3">
                    <!-- Step 3 content -->
                    <!-- Step 3 -->
                     
                    <div class="form-group">
                        <label for="role"
                               class="">{{trans_choice('general.role',1)}}</label>
                        <select name="role" class="form-control select2" id="role" required>
                            <option></option>
                            @foreach(DB::table('roles')->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($selected==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="province_id">Province</label>
                        <select name="province_id" class="form-control select2" id="province_id" required>
                            <option></option>
                            @foreach($provinces as $key)
                                <option value="{{$key->id}}"
                                        @if($user->province_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="district_id">District</label>
                        <select name="district_id" class="form-control select2" id="district_id" required>
                            <option></option>
                            @if($user->province_id)
                                @foreach(\App\Models\District::where('province_id', $user->province_id)->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($user->district_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="office_id">{{trans_choice('general.office',1)}}</label>
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @if($user->province_id)
                                @foreach(\App\Models\Office::where('province_id',$user->province_id)->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($user->office_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address"
                               class="">{{trans_choice('general.address',1)}}</label>
                        <textarea name="address" class="form-control"
                                  placeholder="{{trans_choice('general.address',1)}}"
                                  id="address" rows="3">{!! $user->address !!}</textarea>

                    </div>
                    <div class="form-group">
                        <label for="notes"
                               class="">{{trans_choice('general.note',2)}}</label>
                        <textarea name="notes" class="form-control"
                                  placeholder="{{trans_choice('general.note',2)}}"
                                  id="notes" rows="3">{!! $user->notes !!}</textarea>
                    </div>
                    @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                        @foreach(\App\Models\CustomField::where('category','users')->get() as $key)
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label col-md-2">{{$key->name}}</label>
                                <div class="col-md-8">
                                    @if($key->field_type=="number")
                                        <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required
                                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name}} @endif">
                                    @endif
                                    @if($key->field_type=="textfield")
                                        <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required
                                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name}} @endif">
                                    @endif
                                    @if($key->field_type=="date")
                                        <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required
                                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name}} @endif">
                                    @endif
                                    @if($key->field_type=="textarea")
                                        <textarea class="form-control" name="custom_field_{{$key->id}}"
                                                  @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name}} @endif</textarea>
                                    @endif
                                    @if($key->field_type=="decimal")
                                        <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                               @if($key->required==1) required
                                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name}} @endif">
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
                                                @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()))
                                                    @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name==$v)
                                                        <option selected>{{$v}}</option>
                                                    @else
                                                        <option>{{$v}}</option>
                                                    @endif
                                                @else
                                                    <option>{{$v}}</option>
                                                @endif

                                            @endforeach
                                        </select>
                                    @endif
                                    @if($key->field_type=="radiobox")
                                        @foreach(explode(',',$key->radio_box_values) as $v)
                                            <div class="radio">
                                                <label>
                                                    @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()))
                                                        @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()->name==$v)
                                                            <input type="radio" name="custom_field_{{$key->id}}"
                                                                   id="{{$key->id}}" value="{{$v}}"
                                                                   @if($key->required==1) required @endif checked>
                                                        @else
                                                            <input type="radio" name="custom_field_{{$key->id}}"
                                                                   id="{{$key->id}}" value="{{$v}}"
                                                                   @if($key->required==1) required @endif>
                                                        @endif
                                                    @else
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif

                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($key->field_type=="checkbox")
                                        @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$user->id)->where('category','users')->first()))
                                            <?php $c = unserialize(\App\Models\CustomFieldMeta::where('custom_field_id',
                                                $key->id)->where('parent_id', $user->id)->where('category',
                                                'users')->first()->name); ?>

                                            @foreach(explode(',',$key->checkbox_values) as $v)
                                                <div class="checkbox">
                                                    <label>
                                                        @if(array_key_exists($v,$c))
                                                            @if($c[$v]==$v)
                                                                <input type="checkbox"
                                                                       name="custom_field_{{$key->id}}[{{$v}}]"
                                                                       id="{{$key->id}}"
                                                                       value="{{$v}}"
                                                                       @if($key->required==1) required @endif checked>
                                                            @else
                                                                <input type="checkbox"
                                                                       name="custom_field_{{$key->id}}[{{$v}}]"
                                                                       id="{{$key->id}}"
                                                                       value="{{$v}}"
                                                                       @if($key->required==1) required @endif>
                                                            @endif
                                                        @else
                                                            <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif>
                                                        @endif
                                                        <b>{{$v}}</b>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach(explode(',',$key->checkbox_values) as $v)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                               id="{{$key->id}}"
                                                               value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                        <b>{{$v}}</b>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="box-footer">
                        <button type="button" class="btn btn-default pull-left previous-step">Previous</button>
                        <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                    </div>
                </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
    $(".form").validate({
        rules: {
            field: {
                required: true,
                step: 10
            }, password: {
                minlength: 6,
            },
            repeat_password: {
                minlength: 6,
                equalTo: "#password"
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

    // Wizard Navigation Logic
    var currentStep = 1;
    var totalSteps = 3;

    function updateProgressBar() {
        var percentage = (currentStep / totalSteps) * 100;
        $('#wizard-progress-bar').css('width', percentage + '%').text('Step ' + currentStep + ' of ' + totalSteps);
    }

    $('.next-step').click(function() {
        var form = $(".form");
        
        // Validate only fields in the current step
        var currentStepFields = $('#step-' + currentStep).find('input, select, textarea');
        var isValid = true;
        currentStepFields.each(function() {
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

    $('.previous-step').click(function() {
        $('#step-' + currentStep).removeClass('active');
        currentStep--;
        $('#step-' + currentStep).addClass('active');
        updateProgressBar();
    });

    function handleOfficeVisibility() {
        var roleId = $('#role').val();
        var provinceId = $('#province_id').val();
        
        // Provincial Manager role ID is 6
        if (roleId == 6 && provinceId) {
            $('#office_id').closest('.form-group').hide();
            $('#office_id').val('').trigger('change');
            $('#office_id').prop('required', false);
        } else {
            $('#office_id').closest('.form-group').show();
            $('#office_id').prop('required', true);
        }

        if (roleId == 12) {
            $('#district_id').closest('.form-group').show();
            $('#district_id').prop('required', true);
        } else {
            $('#district_id').closest('.form-group').hide();
            $('#district_id').val('').trigger('change');
            $('#district_id').prop('required', false);
        }
    }

    // Run on page load for initial state
    handleOfficeVisibility();

    $('#role, #province_id').change(function() {
        handleOfficeVisibility();
    });

    $('#province_id').change(function() {
        var id = $(this).val();
        if (id) {
            $.ajax({
                url: "{{ url('user/get_offices_by_province') }}/" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#office_id').empty();
                    $('#office_id').append('<option value=""></option>');
                    $.each(data, function(key, value) {
                        $('#office_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    // Re-run visibility check after AJAX to ensure correct state
                    handleOfficeVisibility();
                }
            });
            $.ajax({
                url: "{{ url('user/get_districts_by_province') }}/" + id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#district_id').empty();
                    $('#district_id').append('<option value=""></option>');
                    $.each(data, function(key, value) {
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