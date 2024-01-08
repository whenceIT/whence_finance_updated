@extends('layouts.auth')
@section('title')
    {{ trans_choice('general.login',1) }}
@endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                <img src="{{asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                     class="" height="100"/>
            @else
                {{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}
            @endif
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            @if(Session::has('flash_notification.message'))
                <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
            @endif
            @if (isset($msg))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ $msg }}
                </div>
            @endif
            @if (isset($error))
                <div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ $error }}
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{url('confirm_password_reset/'.$id.'/'.$code)}}" method="post" class="login-form">
                {{csrf_field()}}
                <p class="login-box-msg">{{ trans_choice('general.forgot_password_msg',1) }}</p>
                <p class="login-box-msg">{{ trans_choice('general.enter_new_password',1) }}</p>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control"
                           placeholder="{{ trans_choice('general.password',1) }}"
                           required id="password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="repeat_password" class="form-control"
                           placeholder="{{ trans_choice('general.repeat_password',1) }}"
                           required id="repeat_password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <a href="{{url('login')}}"
                           class="btn btn-success btn-block btn-flat">{{ trans_choice('general.login',1) }}</a>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-8">
                        <button type="submit"
                                class="btn btn-primary btn-block btn-flat">{{ trans_choice('general.reset',1) }}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>

    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
            $.validator.addMethod('mypassword', function (value, element) {
                    return this.optional(element) || (value.match(/[a-zA-Z]/) && value.match(/[0-9]/));
                },
                'Password must contain at least one numeric and one alphabetic character.');
            $(".login-form").validate({
                rules: {
                    field: {
                        required: true,
                        step: 10
                    }, password: {
                        required: true,
                        minlength: 6,
                        mypassword: true
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
        });
    </script>
@endsection
