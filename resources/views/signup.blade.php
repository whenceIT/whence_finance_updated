@extends('layouts.auth')
@section('title')
    {{ trans_choice('general.login',1) }}
@endsection

@section('content')

    <div class="signup-page ">
</div>
    <div class="login-box ">
        <div class="login-logo">
            @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                <img src="{{asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                     class="" height="100"/>
            @else
                {{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}
            @endif
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body ">
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
            <form action="{{url('register')}}" method="post" class="login-form ">
                {{csrf_field()}}
                <p class="login-box-msg">Sign up</p>

               <div class="form-group has-feedback">
                    <input type="text" name="first_name" class="form-control"
                           placeholder="First name"
                           required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>


               <div class="form-group has-feedback">
                    <input type="text" name="last_name" class="form-control"
                           placeholder="Last name"
                           required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                <select name="role" class="form-control" id="role" required>
                                <option disabled selected>Apply as</option>
                                <option value="2">Client</option>
                                <option value="3">Loan Consultant</option>
                            </select>
                </div>

                <div class="form-group has-feedback">
                <select name="office" class="form-control" id="office" required>
                                <option disabled selected>Office</option>
                                @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                </div>


                   <div class="form-group has-feedback">
                    <input type="text" name="phone" class="form-control"
                           placeholder="{{ trans_choice('general.phone',1) }}"
                           required>
                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                </div>

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

                   <div class="form-group has-feedback">
                    <input type="password" name="rpassword" class="form-control"
                           placeholder="Confirm Password"
                           required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

</div>

                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                      
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit"
                                class="btn btn-primary btn-block btn-flat">Sign up</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <a href="{{'login'}}">{{ trans_choice('general.login',1) }}</a><br>
        </div>

    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {

            $('#org-type li').on('click', function(){
            $('#org_type').val($(this).text());
});



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
