@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.profile',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{ trans_choice('general.edit',1) }} {{ trans_choice('general.profile',1) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('user/update_profile')}}" class="form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
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

        @if(Sentinel::inRole('client'))
    <div class="form-group">
                    <label for="client_type"
                           class="">{{trans_choice('general.client',1)}} {{trans_choice('general.type',1)}}</label>
                        <select name="client_type" class="form-control " id="client_type" required>
                            <option value="individual">{{trans_choice('general.individual',1)}}</option>
                            <option value="business">{{trans_choice('general.business',1)}}</option>
                        </select>
                </div>
                @endif

                <div class="form-group">
                    <label for="phone"
                           class="">{{trans_choice('general.phone',1)}}</label>
                    <input type="text" name="phone" class="form-control"
                           placeholder="{{trans_choice('general.phone',1)}}"
                           value="{{$user->phone}}"
                           id="phone">
                </div>
                <div class="form-group ">
                    <label for="email"
                           class="">{{trans_choice('general.email',1)}}</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="{{trans_choice('general.email',1)}}"
                           value="{{$user->email}}" required
                           id="email">
                </div>

                <div class="form-group ">
        
                    <label for="external_id"
                           class="">{{trans_choice('general.id_num',1)}}</label>
                        <input type="text" name="external_id" class="form-control"
                               value="{{$user->external_id}}"
                               id="external_id">


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
                <div class="form-group">
                    <label for="address"
                           class="">{{trans_choice('general.address',1)}}</label>
                    <textarea name="address" class="form-control wysihtml5"
                              placeholder="{{trans_choice('general.address',1)}}"
                              id="address" rows="3">{!! $user->address !!}</textarea>

                </div>

            


                <div class="form-group">
                    <label for="notes"
                           class="">{{trans_choice('general.note',2)}}</label>
                    <textarea name="notes" class="form-control wysihtml5"
                              placeholder="{{trans_choice('general.note',2)}}"
                              id="notes" rows="3">{!! $user->notes !!}</textarea>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
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
    </script>
@endsection