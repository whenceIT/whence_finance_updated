@extends('layouts.auth')
@section('title')
    {{ trans_choice('general.login',1) }}
@endsection

@section('content')


{{-- <div class="row"> --}}
    <div class="col-lg-8">
        {{-- <div  class="landing-img" style="background-image: url('{{ asset('assets/landing_page/img/login.jpeg') }}');background-position: center;
            background-repeat: no-repeat; background-size: cover;">
            <div class="align-middle padding-landing" style="color:#ffffff;"><h2 style="font-size: 60px;font-weight: bold;font-family: apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';letter-spacing: 0.0312rem;">
            Welcome to <br>Bedford Microfinance Ltd <br>Digital Banking</h2></div>

        </div> --}}
            
        {{-- <div class="landing-img" style="background-image: url('{{ asset('assets/landing_page/img/whence.jpg') }}');background-position: center;
        background-repeat: no-repeat; background-size: cover;"></div> --}}
        <img class="landing-img"src="{{asset('assets/landing_page/img/whence.jpg') }}"/>
    </div>
    <div class="col-lg-4">
        
        <div class="login-box">
            <div class="login-logo">
            <img src="{{asset('assets/landing_page/img/whence-logo-2.png') }}" style="width: 100%;height: 200px;margin-bottom: 10px;"/>
            <br>
            <br>
                <!-- @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                    <img src="{{asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                        class="" height="100"/>
                @else
                    {{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}0
                @endif -->
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
                    <div class="alert alert-danger" style="margin-bottom: 100px;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <h3 class="login-box-msg"style="
        margin-top: -90px;
        text-align: center;
        font-size: 22px;
        color: #000;
        font-family: apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';letter-spacing: 0.0312rem;
        font-weight: 700;"><strong>Log into your account</strong></h3>
                <form action="{{url('login')}}" method="post" class="login-form">
                    {{csrf_field()}}
                    <!-- <p class="login-box-msg">{{ trans_choice('general.sign_in',1) }}</p> -->
                    <div class="form-group has-feedback">
                        <input type="email" name="email" class="form-control"
                            placeholder="{{ trans_choice('general.email',1) }}"
                            required>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ trans_choice('general.password',1) }}"
                            required>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <!-- <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox" name="remember" value="1"> {{ trans('general.remember_me') }}
                                </label>
                            </div>
                        </div> -->
                        <!-- /.col -->
                        <div class="col-xs-8">
                            <button type="submit"
                                    class="btn btn-block btn-flat" style="width:80%; color:#ffffff; background-color:#00a04a;">{{ trans_choice('general.sign_in',1) }}</button>
                                    <div>

                                    </div>
                                    <!-- <a href="{{ url('loan/my_collections') }}" style="margin-top: 50px;">
                                          fugnun
                                    </a> -->
                        </div>
                    
                    
                        <!-- /.col -->
                    </div>
                </form>
                <!-- <a href="{{'password_reset'}}">{{ trans_choice('general.forgot_password_msg',1) }}</a><br> -->
            </div>

            <div class="col-xs-8 padding-bottom:10px;">
            <a href="{{ url('signup') }}" class="btn" style="width:100%; color:#ffffff; background-color:#00a04a;">
                                          Create a new account
                                    </a> 
                                    <div style="padding-top: 20px;"></div>
                                    <div></div>
                                    <div></div>
            </div>
         
        
                      

        </div>

    {{-- </div> --}}
        
    </div>
</div>

    {{-- 
        {-- <div class="align-middle" style="margin-top: 150px;color:#ffffff;"><h2 style="font-size: 60px;font-weight: bold;font-family: apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';letter-spacing: 0.0312rem;">
        Welcome to Whence</h2></div> --}

    </div> --}}
    {{-- <div class="col-md-4" style="background-color:#ffffff;min-height: 100%; min-width: 100%;">

        
    </div> --}}
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
            $(".login-form").validate({
                rules: {
                    field: {
                        required: true,
                        step: 10
                    },
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
