@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('user/store')}}" class="form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="first_name"
                           class="">{{trans_choice('general.first_name',1)}}</label>
                    <input type="text" name="first_name" class="form-control"
                           placeholder="{{trans_choice('general.first_name',1)}}"
                           value="{{old('first_name')}}"
                           required id="first_name">
                </div>
                <div class="form-group">
                    <label for="last_name"
                           class="">{{trans_choice('general.last_name',1)}}</label>
                    <input type="text" name="last_name" class="form-control"
                           placeholder="{{trans_choice('general.last_name',1)}}"
                           value="{{old('last_name')}}"
                           required id="last_name">
                </div>
                <div class="form-group">
                    <label for="gender"
                           class="">{{trans_choice('general.gender',1)}}</label>
                    <select name="gender" class="form-control" id="gender">
                        <option value="male">{{trans('general.male')}}</option>
                        <option value="female">{{trans('general.female')}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone"
                           class="">{{trans_choice('general.phone',1)}}</label>
                    <input type="text" name="phone" class="form-control"
                           placeholder="{{trans_choice('general.phone',1)}}"
                           value="{{old('phone')}}"
                           id="phone">
                </div>
                <div class="form-group ">
                    <label for="email"
                           class="">{{trans_choice('general.email',1)}}</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="{{trans_choice('general.email',1)}}"
                           value="{{old('email')}}" required
                           id="email">
                </div>
                <div class="form-group">
                    <label for="password"
                           class="">{{trans_choice('general.password',1)}}</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="{{trans_choice('general.password',1)}}"
                           value="" required
                           id="password">
                </div>
                <div class="form-group">
                    <label for="repeat_password"
                           class="">{{trans_choice('general.repeat_password',1)}}</label>
                    <input type="password" name="repeat_password" class="form-control"
                           placeholder="{{trans_choice('general.repeat_password',1)}}"
                           value="" required
                           id="repeat_password">
                </div>
                <div class="form-group">
                    <label for="office_id">{{trans_choice('general.office',1)}}</label>
                           <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                <div class="form-group">
                    <label for="role"
                           class="">{{trans_choice('general.role',1)}}</label>
                    <select name="role" class="form-control select2" id="role" required>
                        @foreach(DB::table('roles')->get() as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="address"
                           class="">{{trans_choice('general.address',1)}}</label>
                    <textarea name="address" class="form-control wysihtml5"
                              placeholder="{{trans_choice('general.address',1)}}"
                              id="address" rows="3">{{old('address')}}</textarea>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="">{{trans_choice('general.note',2)}}</label>
                    <textarea name="notes" class="form-control wysihtml5"
                              placeholder="{{trans_choice('general.note',2)}}"
                              id="notes" rows="3">{{old('notes')}}</textarea>
                </div>
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','users')->get() as $key)
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
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(".form").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }, password: {
                    required: true,
                    minlength: 6,
                },
                repeat_password: {
                    required: true,
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
    </script>
@endsection